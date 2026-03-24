@extends('layouts.auth')

@section('title', 'Admin Login')

@section('content')
<div class="flex flex-col lg:flex-row min-h-screen">
    <!-- Branding Section -->
    <div class="bg-white w-full lg:w-7/12 xl:w-3/5 flex flex-col justify-center items-center p-6 md:p-8 lg:p-12 order-1 lg:order-1">
        <div class="w-full max-w-md xl:max-w-lg">
            <div class="mb-6 md:mb-8 lg:mb-12 flex justify-center">
                <img src="{{ asset('images/logo sekolah.png') }}" alt="logo sekolah" class="w-48 md:w-56 lg:w-64 xl:w-72">
            </div>
            
            <h1 class="text-xl md:text-2xl lg:text-3xl font-bold text-slate-800 mb-3 md:mb-4 text-center">
                i-PAJSK SMK Dato' Haji Talib Karim
            </h1>
        </div>
    </div>

    <!-- Login Form Section -->
    <div class="bg-white-900 w-full lg:w-5/12 xl:w-2/5 flex flex-col justify-center items-center p-6 md:p-8 lg:p-12 order-2 lg:order-2">
        <div class="w-full max-w-md">
            <div class="mb-6 md:mb-8">
                <h2 class="text-2xl md:text-3xl font-bold text-black mb-2">
                    Admin Login
                </h2>
            </div>

            <form method="POST" action="{{ route('login.submit') }}" class="space-y-4 md:space-y-6">
                @csrf
                <input type="hidden" name="role" value="admin">
                
                @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- IC Number Input -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-black">
                        IDENTITY CARD NUMBER (IC)
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-id-card absolute left-3 md:left-4 top-1/2 transform -translate-y-1/2 text-slate-400 text-sm md:text-base"></i>
                        <input 
                            type="text" 
                            name="ic_number"
                            class="w-full pl-10 md:pl-12 pr-4 py-3 md:py-3.5 bg-white border border-slate-300 rounded-lg md:rounded-xl text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm md:text-base"
                            placeholder="123456-78-9012"
                            value="{{ old('ic_number') }}"
                            required
                        >
                    </div>
                </div>

                <!-- Password Input -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-black">
                        PASSWORD
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3 md:left-4 top-1/2 transform -translate-y-1/2 text-slate-400 text-sm md:text-base"></i>
                        <input 
                            type="password" 
                            name="password"
                            class="w-full pl-10 md:pl-12 pr-10 md:pr-12 py-3 md:py-3.5 bg-white border border-slate-300 rounded-lg md:rounded-xl text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm md:text-base"
                            placeholder="••••••••"
                            required
                        >
                        <i class="fa-regular fa-eye absolute right-3 md:right-4 top-1/2 transform -translate-y-1/2 text-slate-400 cursor-pointer toggle-password text-sm md:text-base"></i>
                    </div>
                </div>
                    
                <!-- Login Button -->
                <button type="submit" class="w-full py-3 md:py-3.5 bg-indigo-600 text-white font-semibold rounded-lg md:rounded-xl shadow-lg hover:bg-indigo-700 transition text-sm md:text-base flex items-center justify-center">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Log In
                </button>
            </form>
        </div>
    </div>
</div>
@endsection