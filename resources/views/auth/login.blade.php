<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICS Organization Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .required::after {
            content: " *";
            color: #c21313;
            font-weight: bold;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center" style="background: url('{{ asset('img/bg.png') }}') no-repeat center center; background-size: cover;">

    <!-- Glassmorphism Card -->
    <div class="relative w-full max-w-md p-8 rounded-2xl shadow-2xl bg-white border border-white/40">

        <!-- Logo overlapping -->
        <div class="absolute left-1/2 -top-16 transform -translate-x-1/2">
            <img src="{{ asset('img/ics-logo.png') }}" alt="Logo" class="w-28 h-28 rounded-full bg-white shadow-xl border-4 border-white object-contain">
        </div>

        <div class="mt-12">
            <h2 class="text-center text-3xl font-extrabold text-gray-900 tracking-tight drop-shadow">
                ICS ORGANIZATION
            </h2>
            <p class="mt-2 text-center text-base text-gray-700 font-medium">
                Navotas Polytechnic College - Integrated Computer Society
            </p>
        </div>

        @if (session('success'))
            <div class="mb-4 mt-7 p-7 bg-green-100 border border-green-400 text-green-700 rounded-lg text-center">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 mt-7 p-7 bg-red-100 border border-red-400 text-red-700 rounded-lg text-center">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-6 mt-8">
            @csrf
            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 required">Email</label>
                <input id="email" name="email" type="email" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-500 transition px-4 py-2 bg-white/80 backdrop-blur placeholder-gray-400"
                    value="{{ old('email') }}" placeholder="example@navotaspolytechniccollege.edu.ph">
                <p class="mt-1 text-xs text-gray-500">Only @navotaspolytechniccollege.edu.ph email addresses are allowed</p>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 required">Password</label>
                <div class="relative">
                    <input id="password" name="password" type="password" required
                        class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-500 transition px-4 py-2 bg-white/80 backdrop-blur placeholder-gray-400">
                    <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-3 flex items-center mt-1">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded transition">
                <label for="remember" class="ml-2 block text-sm text-gray-900">
                    Remember me
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-2 mt-4">
                <a href="{{ route('register') }}">
                    <button type="button"
                        class="py-2 px-4 bg-gray-400 hover:bg-gray-500 text-sm font-semibold rounded-lg text-white transition shadow">
                        Register
                    </button>
                </a>
                <button type="submit"
                    class="py-2 px-4 bg-red-600 hover:bg-red-700 text-sm font-semibold rounded-lg text-white transition shadow">
                    Log in
                </button>
            </div>
        </form>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = input.nextElementSibling;
            const svg = button.querySelector('svg');

            if (input.type === 'password') {
                input.type = 'text';
                svg.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                `;
            } else {
                input.type = 'password';
                svg.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }
    </script>
</body>
</html>
