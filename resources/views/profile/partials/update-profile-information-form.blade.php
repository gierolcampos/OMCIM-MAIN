<section class="bg-white p-6 rounded-lg shadow-sm">
    <style>
        .required::after {
            content: " *";
            color: #c21313;
            font-weight: bold;
        }
    </style>

    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <!-- Profile Picture Section -->
    <div class="mt-6 flex flex-col items-center">
        <div class="relative group">
            <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-700 flex items-center justify-center">
                @if(Auth::user()->profile_picture)
                    <img src="{{ Auth::user()->profile_picture }}" alt="{{ Auth::user()->firstname }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-white text-2xl font-semibold">
                        {{ strtoupper(substr(Auth::user()->firstname, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="absolute inset-0 rounded-full bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer"
                 onclick="document.getElementById('profile_picture_upload').click()">
                <span class="text-white text-xs font-medium">Change</span>
            </div>
        </div>
        <p class="mt-2 text-xs text-gray-500">Click on the profile picture to change it</p>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Hidden file input for profile picture -->
        <input type="file" id="profile_picture_upload" name="profile_picture" class="hidden" accept="image/*" onchange="previewImage(event)">
        <!-- Hidden input for base64 image data -->
        <input type="hidden" id="profile_picture_base64" name="profile_picture_base64" value="">
        <input type="hidden" name="using_base64" value="1">
        @error('profile_picture')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

        <div class="mt-6">
            <label for="name" class="block text-sm font-medium text-gray-700 required">
                {{ __('Name') }}
            </label>
            <input id="name" name="name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4">
            <label for="email" class="block text-sm font-medium text-gray-700 required">
                {{ __('Email') }}
            </label>
            <input id="email" name="email" type="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-600">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="mt-6">
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'profile-updated')
                <span x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="ml-3 text-sm text-gray-600">
                    {{ __('Saved.') }}
                </span>
            @endif
        </div>
    </form>

    <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">

    <!-- Cropper Modal -->
    <div id="cropperModal" class="fixed inset-0 bg-black bg-opacity-75 items-center justify-center z-50 hidden" style="display: none;">
        <div class="bg-gray-900 rounded-lg shadow-xl max-w-xl w-full mx-4 overflow-hidden">
            <div class="flex justify-between items-center p-4 border-b border-gray-700">
                <h3 class="text-xl font-semibold text-white">Choose profile picture</h3>
                <button type="button" onclick="closeCropperModal()" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-4">
                <div class="relative">
                    <div class="mb-4 overflow-hidden" style="max-height: 400px;">
                        <img id="cropperImage" src="" alt="Image to crop" class="max-w-full">
                    </div>

                    <div class="flex items-center justify-center mb-4">
                        <button type="button" onclick="zoomOut()" class="text-gray-400 hover:text-white mr-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </button>

                        <input type="range" id="zoomRange" min="0" max="100" value="0" class="w-full mx-2 accent-blue-600">

                        <button type="button" onclick="zoomIn()" class="text-gray-400 hover:text-white ml-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="flex justify-center mb-4">
                        <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md flex items-center justify-center" onclick="cropImage()">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"></path>
                            </svg>
                            Crop photo
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex justify-end p-4 border-t border-gray-700">
                <button type="button" onclick="closeCropperModal()" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-md mr-3">
                    Cancel
                </button>
                <button type="button" onclick="saveCroppedImage()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md">
                    Save
                </button>
            </div>
        </div>
    </div>

    <!-- Cropper.js Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <script>
        let cropper;
        let croppedImageData;

        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Show the cropper modal
                    const cropperModal = document.getElementById('cropperModal');
                    cropperModal.classList.remove('hidden');
                    cropperModal.style.display = 'flex';

                    // Set the image source
                    const cropperImage = document.getElementById('cropperImage');
                    cropperImage.src = e.target.result;

                    // Initialize cropper after image is loaded
                    cropperImage.onload = function() {
                        if (cropper) {
                            cropper.destroy();
                        }

                        cropper = new Cropper(cropperImage, {
                            aspectRatio: 1,
                            viewMode: 1,
                            dragMode: 'move',
                            autoCropArea: 0.8,
                            cropBoxResizable: false,
                            cropBoxMovable: true,
                            guides: true,
                            center: true,
                            highlight: false,
                            background: false,
                            zoomOnWheel: false,
                            minContainerWidth: 300,
                            minContainerHeight: 300,
                        });

                        // Initialize zoom slider
                        const zoomRange = document.getElementById('zoomRange');
                        zoomRange.addEventListener('input', function() {
                            const value = parseFloat(this.value) / 100;
                            cropper.zoomTo(0.5 + value);
                        });
                    };
                };
                reader.readAsDataURL(file);
            }
        }

        function closeCropperModal() {
            const cropperModal = document.getElementById('cropperModal');
            cropperModal.classList.add('hidden');
            cropperModal.style.display = 'none';

            // Reset file input
            document.getElementById('profile_picture_upload').value = '';

            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        }

        function zoomIn() {
            if (cropper) {
                cropper.zoom(0.1);
                updateZoomSlider();
            }
        }

        function zoomOut() {
            if (cropper) {
                cropper.zoom(-0.1);
                updateZoomSlider();
            }
        }

        function updateZoomSlider() {
            const zoomRange = document.getElementById('zoomRange');
            const data = cropper.getData();
            const value = Math.max(0, Math.min(100, (data.scaleX - 0.5) * 100));
            zoomRange.value = value;
        }

        function cropImage() {
            if (cropper) {
                croppedImageData = cropper.getCroppedCanvas({
                    width: 300,
                    height: 300,
                    fillColor: '#fff',
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                }).toDataURL('image/jpeg', 0.7); // Reduced quality to make file smaller

                // Preview the cropped image
                const container = document.querySelector('.w-32.h-32.rounded-full.overflow-hidden');
                if (container) {
                    container.innerHTML = '';
                    const img = document.createElement('img');
                    img.src = croppedImageData;
                    img.alt = 'Profile Preview';
                    img.className = 'w-full h-full object-cover';
                    container.appendChild(img);
                }

                // Store the base64 data in the hidden input
                document.getElementById('profile_picture_base64').value = croppedImageData;
            }
        }

        function saveCroppedImage() {
            if (cropper) {
                // Get the cropped canvas
                const canvas = cropper.getCroppedCanvas({
                    width: 300,
                    height: 300,
                    fillColor: '#fff',
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                });

                // Convert canvas to data URL
                croppedImageData = canvas.toDataURL('image/jpeg', 0.7); // Reduced quality to make file smaller

                // Preview the cropped image
                const container = document.querySelector('.w-32.h-32.rounded-full.overflow-hidden');
                if (container) {
                    container.innerHTML = '';
                    const img = document.createElement('img');
                    img.src = croppedImageData;
                    img.alt = 'Profile Preview';
                    img.className = 'w-full h-full object-cover';
                    container.appendChild(img);
                }

                // Store the base64 data in the hidden input
                document.getElementById('profile_picture_base64').value = croppedImageData;

                // Close the modal
                closeCropperModal();
            }
        }
    </script>
</section>
