@extends('layouts.app')

@section('title', 'Sign Up')

@section('content')
<div class="py-24">
    <!-- Notifikasi -->
    @include('components.notification')
    <!-- Form -->
    <form action="{{ route('register.submit') }}" method="POST" class="max-w-sm px-10 py-6 bg-gray-800 border-gray-700 rounded-lg shadow-md mx-auto">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-white">Daftarkan Akun</h5>
        @csrf
        <x-form-input type="email" label="Email" name="email" placeholder="email@example.com" classLabel="text-white" required />
        <x-form-input type="password" label="Password" name="password" classLabel="text-white" required />
        <x-form-input type="password" label="Konfirmasi Password" name="confirm-password" classLabel="text-white" required />
        <button type="submit"
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center ">Daftar
            Sekarang</button>
    </form>
</div>
@endsection