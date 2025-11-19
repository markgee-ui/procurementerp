<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP Login</title>
     @vite(['resources/css/app.css', 'resources/js/app.js'])
         <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6; /* Gray-100 */
        }
        .login-card {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .btn-login {
            background-color: #3b82f6; /* blue-500 */
            transition: background-color 0.3s;
        }
        .btn-login:hover {
            background-color: #2563eb; /* blue-600 */
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md">
        
        <!-- Login Card -->
        <div class="bg-white p-8 rounded-2xl login-card border border-gray-200">
            <header class="text-center mb-6">
                <h1 class="text-3xl font-extrabold text-gray-900">Sign In to Procurement ERP</h1>
                <p class="text-sm text-gray-500 mt-2">Use your official credentials to access the system.</p>
            </header>

            <!-- Status/Error Message Display -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm" role="alert">
                    Please check your input and try again.
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-4 py-3 border rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                         >
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input id="password" type="password" name="password" required
                           class="w-full px-4 py-3 border rounded-lg focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                           >
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                               class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="remember" class="ml-2 block text-sm text-gray-900">
                            Remember me
                        </label>
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="btn-login w-full flex justify-center py-3 px-4 border border-transparent rounded-lg text-lg font-medium text-white shadow-sm hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Sign In
                    </button>
                </div>
            </form>
            
        </div>
    </div>

</body>
</html>