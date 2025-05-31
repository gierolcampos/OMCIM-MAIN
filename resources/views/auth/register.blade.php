<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICS Organization Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
        .logo-container {
            position: relative;
            z-index: 10;
        }
        .required::after {
            content: " *";
            color: #c21313;
            font-weight: bold;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center py-24" style="background: url('{{ asset('img/bg.png') }}') no-repeat center center; background-size: cover;">

    <!-- Glassmorphism Card -->
    <div class="relative w-full max-w-4xl mx-4 bg-white rounded-2xl shadow-2xl border border-white/40">
        <!-- Logo Container -->
        <div class="logo-container flex justify-center -mt-20">
            <img src="{{ asset('img/ics-logo.png') }}" alt="Logo" class="w-36 h-36 rounded-full bg-white shadow-xl border-4 border-white object-contain">
        </div>

        <div class="p-8">
            <div class="mb-8">
                <h2 class="text-center text-3xl font-extrabold text-gray-900 tracking-tight drop-shadow">
                    ICS ORGANIZATION
                </h2>
                <p class="mt-2 text-center text-base text-gray-700 font-medium">
                    Navotas Polytechnic College - Integrated Computer Society
                </p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="mt-8">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="studentnumber" class="block text-sm font-semibold text-gray-700 required">Student Number</label>
                        <input id="studentnumber" name="studentnumber" type="text" pattern="[0-9]{1,6}"  tabindex="1" maxlength="6"
                            oninput="this.value = this.value.replace(/[^0-9]/g, ''); generateEmail();" placeholder="Eg: 123456"
                            class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-500 transition px-4 py-2 bg-white placeholder-gray-400"
                            value="{{ old('studentnumber') }}">
                    </div>
                </div>


                <!-- Row 2: First Name | Middle Name | Last Name -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="firstname" class="block text-sm font-semibold text-gray-700 required">First Name</label>
                        <input id="firstname" name="firstname" type="text" required autocomplete="firstname" tabindex="2" oninput="generateEmail()" placeholder="Enter First Name"
                            class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-500 transition px-4 py-2 bg-white placeholder-gray-400"
                            value="{{ old('firstname') }}">
                    </div>
                    <div>
                        <label for="middlename" class="block text-sm font-semibold text-gray-700">Middle Name</label>
                        <input id="middlename" name="middlename" type="text" autocomplete="middlename" tabindex="3" oninput="generateEmail()" placeholder="Enter Middle Name"
                            class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-500 transition px-4 py-2 bg-white placeholder-gray-400"
                            value="{{ old('middlename') }}">
                    </div>
                    <div>
                        <label for="lastname" class="block text-sm font-semibold text-gray-700 required">Last Name</label>
                        <input id="lastname" name="lastname" type="text" required autocomplete="lastname" tabindex="4" oninput="generateEmail()" placeholder="Enter Last Name"
                            class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-500 transition px-4 py-2 bg-white placeholder-gray-400"
                            value="{{ old('lastname') }}">
                    </div>
                </div>

                <!-- Row 3: Suffix | Contact Number | NPC Email -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div>
                        <label for="suffix" class="block text-sm font-semibold text-gray-700">Suffix</label>
                        <input id="suffix" name="suffix" type="text" autocomplete="suffix" tabindex="5" placeholder="Eg: Jr., Sr., II"
                            class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-500 transition px-4 py-2 bg-white placeholder-gray-400"
                            value="{{ old('suffix') }}">
                    </div>
                    <div>
                        <label for="mobile_no" class="block text-sm font-semibold text-gray-700 required">Contact Number</label>
                        <input id="mobile_no" name="mobile_no" type="text" maxlength="11" pattern="[0-9]{11}" required autocomplete="mobilenumber" tabindex="6" inputmode="numeric" placeholder="Eg: 09123456789"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                            class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-500 transition px-4 py-2 bg-white placeholder-gray-400"
                            value="{{ old('mobile_no') }}">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 required">NPC Email</label>
                        <input id="email" name="email" type="email" required autocomplete="username" tabindex="7"
                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-500 transition px-4 py-2 placeholder-gray-400"
                            value="{{ old('email') }}" placeholder="example@navotaspolytechniccollege.edu.ph">
                        <p class="mt-1 text-xs text-gray-500">Only @navotaspolytechniccollege.edu.ph email addresses are allowed</p>
                    </div>
                </div>

                <!-- Row 4: Course | Year | Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div>
                        <label for="course" class="block text-sm font-semibold text-gray-700 required">Course</label>
                        <select id="course" name="course" required tabindex="8"
                            class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-500 transition px-4 py-2 bg-white placeholder-gray-400">
                            <option value="" disabled selected>Select your course</option>
                            <option value="AIS" {{ old('course') == 'AIS' ? 'selected' : '' }}>Associate in Information System</option>
                            <option value="BSIS" {{ old('course') == 'BSIS' ? 'selected' : '' }}>Bachelor of Science in Information System</option>
                        </select>
                    </div>
                    <div>
                        <label for="year" class="block text-sm font-semibold text-gray-700 required">Year</label>
                        <select id="year" name="year" required tabindex="9"
                            class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-500 transition px-4 py-2 bg-white placeholder-gray-400">
                            <option value="" disabled selected>Select your year</option>
                            <option value="1" {{ old('year') == '1' ? 'selected' : '' }}>1st Year</option>
                            <option value="2" {{ old('year') == '2' ? 'selected' : '' }}>2nd Year</option>
                            <option value="3" {{ old('year') == '3' ? 'selected' : '' }}>3rd Year</option>
                            <option value="4" {{ old('year') == '4' ? 'selected' : '' }}>4th Year</option>
                        </select>
                    </div>
                    <div>
                        <label for="section" class="block text-sm font-semibold text-gray-700 required">Section</label>
                        <select id="section" name="section" required tabindex="10"
                            class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-500 transition px-4 py-2 bg-white placeholder-gray-400">
                            <option value="" disabled selected>Select your section</option>
                            <option value="A" {{ old('section') == 'A' ? 'selected' : '' }}>A</option>
                            <option value="B" {{ old('section') == 'B' ? 'selected' : '' }}>B</option>
                            <option value="C" {{ old('section') == 'C' ? 'selected' : '' }}>C</option>
                            <option value="D" {{ old('section') == 'D' ? 'selected' : '' }}>D</option>
                        </select>
                    </div>
                </div>

                <!-- Row 5: Password | Confirm Password -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 required">Password</label>
                        <div class="relative">
                            <input id="password" name="password" type="password" required autocomplete="new-password" tabindex="11"
                                class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-500 transition px-4 py-2 bg-white placeholder-gray-400">
                            <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-3 flex items-center mt-1">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 required">Confirm Password</label>
                        <div class="relative">
                            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" tabindex="12"
                                class="mt-1 block w-full rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-red-400 focus:border-red-500 transition px-4 py-2 bg-white placeholder-gray-400">
                            <button type="button" onclick="togglePassword('password_confirmation')" class="absolute inset-y-0 right-0 pr-3 flex items-center mt-1">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end mt-6 space-x-5">
                    <a class="underline text-sm text-gray-600 hover:text-red-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" href="{{ route('login') }}">
                        Already registered?
                    </a>
                    <button type="submit" class="py-2 px-6 bg-red-600 hover:bg-red-700 text-sm font-semibold rounded-lg text-white transition shadow">
                        Register
                    </button>
                </div>
            </form>

            @if ($errors->any())
    <div class=" mt-7 mb-4 text-sm text-red-600">
        <ul>
            @foreach ($errors->all() as $error)
                <li>• {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        </div>
        <script>
            function generateEmail() {
                const firstname = document.getElementById('firstname').value.trim();
                const lastname = document.getElementById('lastname').value.trim().toLowerCase();
                const studentnumber = document.getElementById('studentnumber').value.trim();
                if (firstname && lastname && studentnumber) {
                    const firstLetters = firstname.split(' ')
                        .filter(word => word.length > 0)
                        .map(word => word[0].toLowerCase())
                        .join('');
                    // Ensure the domain is always correct
                    const domain = 'navotaspolytechniccollege.edu.ph';
                    const email = firstLetters + lastname + studentnumber + '@' + domain;
                    document.getElementById('email').value = email;
                }
            }

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
            window.onload = generateEmail;
        </script>
    </div>
</body>
</html>
