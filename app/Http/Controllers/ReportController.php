<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ReportController extends Controller
{

    public function getReports()
    {
        $response = Http::get(env('BASE_URL_API') . '/api/data/reports?loadRelations=ownerData&&sortBy=%60created%60%20desc');

        // Periksa apakah permintaan berhasil
        if ($response->successful()) {
            $reports = $response->json();
            return view('reports.index', compact('reports')); // Tampilkan di view
        } else {
            return back()->withErrors(['error' => 'Gagal mengambil data laporan']);
        }
    }

    public function show($slug)
    {
        // Fetch data dari API menggunakan slug
        $apiUrl = env('BASE_URL_API') . "/api/data/reports/{$slug}?loadRelations=ownerData%2CsolutionReportList.ownerData%2CdiscussionMessages%2CreportFor%2cfeedbackRatingComment.ownerData";

        try {
            $response = Http::get($apiUrl);

            if ($response->successful()) {
                $reportData = $response->json();

                // Urutkan discussionMessages berdasarkan 'created'
                if (isset($reportData['discussionMessages'])) {
                    usort($reportData['discussionMessages'], function ($a, $b) {
                        return $a['created'] <=> $b['created'];
                    });
                }
                // Return data ke view atau JSON
                return view('reports.show', ['report' => $reportData]);
            }

            return response()->json(['error' => 'Data not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }

    public function createReport(Request $request, $slug = null)
    {
        // Validasi input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string',
            'category' => 'required|string',
            'mediafile' => 'nullable|file|mimes:pdf,jpg,png,gif,svg|max:2048',
        ]);

        // Upload file ke Cloudinary jika ada
        $mediaUrl = null;
        if ($request->hasFile('mediafile')) {
            // Dapatkan tipe MIME file
            $fileMimeType = $request->file('mediafile')->getMimeType();

            // Konfigurasi upload berdasarkan tipe file
            if (str_contains($fileMimeType, 'image')) {
                // Jika file adalah gambar
                $uploadedFileUrl = Cloudinary::upload($request->file('mediafile')->getRealPath(), [
                    'folder' => 'fixithub/reports', // Folder khusus gambar
                    'transformation' => [
                        'width' => 800,
                        'height' => 400,
                        'crop' => 'limit',
                        'quality' => 'auto',
                    ],
                ])->getSecurePath();
            } elseif ($fileMimeType === 'application/pdf') {
                // Jika file adalah PDF
                $uploadedFileUrl = Cloudinary::upload($request->file('mediafile')->getRealPath(), [
                    'folder' => 'fixithub/reports', // Folder khusus PDF
                    'resource_type' => 'raw', // Resource type untuk PDF
                ])->getSecurePath();
            } else {
                // Tipe file tidak sesuai, return error atau handle sesuai kebutuhan
                return back()->withErrors(['mediafile' => 'Tipe file tidak didukung.']);
            }

            $mediaUrl = $uploadedFileUrl; // URL file di Cloudinary
        }

        $accountId = session('user') ? session('user')['objectId'] : null;

        // Periksa apakah data user ada
        if (!$accountId) {
            return back()->withErrors(['error' => 'Data pengguna tidak ditemukan dalam sesi']);
        }
        Log::info('Response from Backendless:', [
            'response_data_account' => $accountId,
        ]);

        // Gabungkan data validasi dengan email
        $payload = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'category' => $validated['category'],
            'mediafile' => $mediaUrl
        ];


        // Kirim data ke API
        $createReportResponse = Http::post(env('BASE_URL_API') . '/api/data/reports', $payload);

        if (!$createReportResponse->successful()) {
            Log::error('Error creating report:', ['response' => $createReportResponse->body()]);
            return back()->withErrors(['error' => 'Gagal membuat laporan di Backendless']);
        }

        // Ambil ID laporan yang baru dibuat
        $reportObjectId = $createReportResponse->json()['objectId'];
        Log::info('Response from Backendless:', [
            'response_data_reportId' => $reportObjectId,
        ]);

        // Cek apakah laporan benar-benar ada
        $reportExists = Http::get(env('BASE_URL_API') . "/api/data/reports/{$reportObjectId}");
        if (!$reportExists->successful()) {
            Log::error('Report not found in Backendless:', ['reportId' => $reportObjectId]);
            return back()->withErrors(['error' => 'Laporan tidak ditemukan di Backendless']);
        }

        // Cek apakah akun benar-benar ada
        $accountExists = Http::get(env('BASE_URL_API') . "/api/data/accounts/{$accountId}");
        if (!$accountExists->successful()) {
            Log::error('Account not found in Backendless:', ['accountId' => $accountId]);
            return back()->withErrors(['error' => 'Akun tidak ditemukan di Backendless']);
        }

        // Buat relasi antara laporan dan akun di Backendless
        $relationAccountResponse = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->put(env('BASE_URL_API') . "/api/data/accounts/{$accountId}/reportList", [
            'objectIds' => $reportObjectId
        ]);

        $relationReportResponse = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->put(env('BASE_URL_API') . "/api/data/reports/{$reportObjectId}/ownerData", [
            'objectIds' => $accountId
        ]);

        if ($slug) {
            $relationReportForResponse = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->put(env('BASE_URL_API') . "/api/data/reports/{$reportObjectId}/reportFor", [
                'objectIds' => $slug
            ]);
        }


        // Tangani respons
        if ($createReportResponse->successful() && $relationAccountResponse->successful() && $relationReportResponse->successful()) {
            return back()->with('success', 'Laporan berhasil dibuat');
        } else {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat membuat laporan']);
        }
    }

    public function verifyReport(Request $request, $slug)
    {
        $verifyReport = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->put(env('BASE_URL_API') . "/api/data/reports/{$slug}", [
            'status' => 'Verified'
        ]);

        if ($verifyReport->successful()) {
            return back()->with('success', 'Laporan berhasil diverifikasi');
        } else {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memverifikasi laporan']);
        }
    }

    public function solvedReport(Request $request, $slug)
    {
        $solvedReport = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->put(env('BASE_URL_API') . "/api/data/reports/{$slug}", [
            'status' => 'Solved'
        ]);

        if ($solvedReport->successful()) {
            return back()->with('success', 'Laporan berhasil diselesaikan');
        } else {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyelesaikan laporan']);
        }
    }

    public function createFeedbackRating(Request $request, $slug)
    {
        // Validasi input
        $validated = $request->validate([
            'rating' => 'required',
            'comment' => 'required|string',
        ]);

        // Get user id from login session
        $accountId = session('user') ? session('user')['objectId'] : null;

        // Periksa apakah data user ada
        if (!$accountId) {
            return back()->withErrors(['error' => 'Data pengguna tidak ditemukan dalam sesi']);
        }

        // Gabungkan data validasi
        $payload = [
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ];

        $feedbackRatingResponse = Http::post(env('BASE_URL_API') . '/api/data/feedbacks', $payload);

        // Periksa ID rating yang baru dibuat
        $feedbackRatingObjectId = $feedbackRatingResponse->json()['objectId'];
        Log::info('Response from Backendless:', [
            'response_data_rating' => $feedbackRatingObjectId,
        ]);

        $relationfeedbackAccountResponse = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->put(env('BASE_URL_API') . "/api/data/feedbacks/{$feedbackRatingObjectId}/ownerData", [
            'objectIds' => $accountId
        ]);

        $relationfeedbackReportResponse = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->put(env('BASE_URL_API') . "/api/data/reports/{$slug}/feedbackRatingComment", [
            'objectIds' => $feedbackRatingObjectId
        ]);

        if ($feedbackRatingResponse->successful() && $relationfeedbackAccountResponse->successful() && $relationfeedbackReportResponse->successful()) {
            return back()->with('success', 'Berhasil mengirimkan rating');
        } else {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memberikan rating']);
        }
    }
}
