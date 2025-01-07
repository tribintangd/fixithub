<!-- Place the first <script> tag in your HTML's <head> -->
<script src="https://cdn.tiny.cloud/1/v8ys1t6tqub2h9uaz7osyblyceriapagz7i8j5hlj3f0732g/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<!-- Place the following <script> and <textarea> tags your HTML's <body> -->
<script>
    tinymce.init({
        selector: '#description',
        menubar: false,
        // plugins: [
        //     // Core editing features
        //     'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
        //     // Your account includes a free trial of TinyMCE premium features
        //     // Try the most popular premium features until Jan 21, 2025:
        //     'checklist', 'mediaembed', 'casechange', 'export', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage', 'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown', 'importword', 'exportword', 'exportpdf'
        // ],
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        tinycomments_mode: 'embedded',
        tinycomments_author: 'Author name',
        mergetags_list: [{
                value: 'First.Name',
                title: 'First Name'
            },
            {
                value: 'Email',
                title: 'Email'
            },
        ],
        // ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
    });
</script>
<textarea>
  Welcome to TinyMCE!
</textarea>
<div class="space-y-5">
    <h1 class="text-4xl text-center text-gray-900"><span class="text-gray-400 font-light">Selamat Datang
            di</span>
        <span>FixIt<span class="font-bold">Hub</span></span>
    </h1>

    <div class="max-w-xl m-auto p-6 bg-gray-800 border-gray-700 rounded-lg shadow-md">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-white">Sampaikan Laporan Anda</h5>
        @if (session('success'))
        <div class="p-4 mb-4 text-green-700 bg-green-100 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="p-4 mb-4 text-red-700 bg-red-100 rounded-lg">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form action="{{ route('report.submit') }}" method="POST" enctype="multipart/form-data" class="mx-auto">
            @csrf
            <x-form-input type="text" label="Judul" name="title" placeholder="Ketik Judul Laporan" required />
            <div class="mb-5">
                <label for="description" class="block mb-2 text-sm font-medium text-white">Deskripsi</label>
                <textarea id="description" name="description" rows="4"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Jelaskan laporan anda...."></textarea>
            </div>
            <div class="flex justify-between mb-5 gap-5">
                <div class="flex-1">
                    <label for="location" class="block mb-2 text-sm font-medium text-white">Lokasi</label>
                    <input type="text" id="location" name="location"
                        class="rounded-md bg-gray-700 border-gray-600 placeholder-gray-400 text-white focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        placeholder="Info lokasi" required />
                </div>
                <div class="flex-1">
                    <label for="category" class="block mb-2 text-sm font-medium text-white">Kategori</label>
                    <select id="category" name="category"
                        class="rounded-md bg-gray-700 border-gray-600 placeholder-gray-400 text-white focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        required>
                        <option selected disabled>Pilih kategori laporan</option>
                        <option value="Kenpendudukan">Kenpendudukan</option>
                        <option value="Kesehatan">Kesehatan</option>
                        <option value="Transportasi">Transportasi</option>
                    </select>
                </div>
            </div>
            <div class="mb-5">
                <div class="flex items-center justify-center w-full">
                    <label for="mediafile"
                        class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                            </svg>
                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click
                                    to upload</span> or drag and drop</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF (MAX. 800x400px)</p>
                        </div>
                        <input id="mediafile" type="file" name="mediafile" class="hidden" />
                    </label>
                </div>
            </div>
            <div class="text-right">
                <button type="submit"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>