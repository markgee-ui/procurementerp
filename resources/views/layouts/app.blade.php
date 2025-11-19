<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Procurement ERP')</title>
    {{-- NOTE: Assuming @vite includes Tailwind --}}
    @vite(['resources/css/app.css', 'resources/js/app.js']) 
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Simple style for the active sidebar link */
        .sidebar-link.active {
            background-color: #d34006ff; /* A distinct active color */
            color: white;
            font-weight: 500;
        }
    </style>
    <!-- Page-specific styles -->
    @stack('styles')
</head>
<body class="bg-gray-100 h-screen flex">

    <!-- Mobile Sidebar (Hidden by default) -->
    <div id="mobile-sidebar" class="fixed inset-0 z-50 bg-gray-800 text-white w-64 transform -translate-x-full transition-transform md:hidden">
        <div class="p-4 border-b border-gray-700">
            <h1 class="text-2xl font-bold text-white">ERP</h1>
        </div>
        <nav class="mt-4">
            @include('layouts.navigation') {{-- Re-use navigation links --}}
        </nav>
    </div>

    <!-- Desktop Sidebar -->
    <aside class="hidden md:block w-64 bg-gray-800 text-white h-full flex-shrink-0">
        <div class="p-4 border-b border-gray-700">
            <div class="flex items-center space-x-2">
                {{-- Logo Image --}}
                <img src="{{ asset('hms-logo.png') }}" alt="Procurement ERP Logo" class="w-8 h-8">
                <h1 class="text-1xl font-extrabold text-white tracking-wide">
                    Procurement ERP
                </h1>
            </div>
        </div>
        <nav class="mt-4">
            @include('layouts.navigation') {{-- Re-use navigation links --}}
        </nav>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        
        <!-- Top Bar / Header -->
        <header class="bg-white shadow-md p-4 flex justify-between items-center">
            
            <!-- Mobile Menu Button -->
            <button id="mobile-menu-button" class="md:hidden text-gray-700 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            
            <!-- Search Input (Personalized) -->
            <div class="hidden md:block">
                @auth
                    <input type="text" placeholder="Search data, {{ Auth::user()->name }}..." class="border rounded-lg p-2 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                @else
                    <input type="text" placeholder="Search data..." class="border rounded-lg p-2 text-sm">
                @endauth
            </div>
            
            <!-- Notifications, User Profile & Logout -->
            <div class="flex items-center space-x-4">
                
                {{-- Notification Icon --}}
                <button class="p-1 text-gray-400 hover:text-gray-700 focus:outline-none relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.406L4 17h5m6 0a2 2 0 100 4 2 2 0 000-4z"></path>
                    </svg>
                    {{-- Optional: Notification Badge --}}
                    {{-- <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">3</span> --}}
                </button>

                @auth
                    {{-- User Profile Display --}}
                    <div class="flex items-center space-x-2">
                        {{-- User Initials Circle --}}
                        <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                            {{-- Displays the first initial of the user's name --}}
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        {{-- User Name --}}
                        <span class="hidden md:block text-sm font-medium text-gray-700">
                            {{ Auth::user()->name }}
                        </span>
                    </div>
                    
                    {{-- Logout Form/Button --}}
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="p-1 text-gray-400 hover:text-red-600 focus:outline-none" title="Log Out">
                            {{-- Logout Icon (Door) --}}
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                @else
                    {{-- Fallback for Guest User --}}
                    <span class="text-sm text-gray-500">Guest</span>
                @endauth
            </div>
        </header>

        <!-- Main Content Area (Scrollable) -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-8">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
        
        <!-- Footer -->
        <footer class="bg-white py-3 px-6 text-center text-sm text-gray-600 border-t">
            &copy; {{ date('Y') }} Taison Group Limited. All rights reserved.
        </footer>
    </div>

    <!-- Overlay for mobile menu -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden md:hidden"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileSidebar = document.getElementById('mobile-sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            function toggleSidebar() {
                mobileSidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }

            mobileMenuButton.addEventListener('click', toggleSidebar);
            overlay.addEventListener('click', toggleSidebar);
        });
    </script>

    <!-- Page-specific scripts -->
    @stack('scripts')

</body>
</html>