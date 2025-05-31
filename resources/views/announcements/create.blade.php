<x-app-layout>

@section('content')
<div class="min-h-screen bg-gradient-to-b from-white to-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl font-semibold text-gray-800">Create Announcement</h1>
                <p class="text-sm text-gray-500 mt-1">Share important updates with organization members</p>
            </div>
            <a href="{{ route('admin.announcements.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2.5 rounded-md text-sm font-medium transition duration-200 hover:bg-gray-300 flex items-center shadow-sm w-full sm:w-auto justify-center sm:justify-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Back to Announcements</span>
            </a>
        </div>

        <div x-data="announcementCreator()" class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="flex flex-col md:flex-row">
                <!-- Left Panel: Composition -->
                <div class="w-full md:w-1/2 p-6 md:p-8 border-r border-gray-100">
                    <h2 class="text-xl font-semibold text-gray-800 mb-5">Compose Announcement</h2>

                    <form action="{{ route('admin.announcements.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5" @submit="isSubmitting = true">
                        @csrf

                        <div class="space-y-5">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-1.5">Announcement Title</label>
                                <input type="text" id="title" name="title" required x-model="announcementTitle" placeholder="Enter a clear, concise title"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 transition">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="content" class="block text-sm font-medium text-gray-700 mb-1.5">Content</label>
                                <textarea id="content" name="content" required x-model="announcementContent" rows="7" placeholder="Write your announcement content here..."
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 transition"
                                        :style="{ color: selectedColor }"></textarea>
                                @error('content')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Text Color</label>
                                <div class="flex flex-wrap gap-2.5">
                                    <template x-for="color in availableColors" :key="color">
                                        <div class="color-swatch"
                                            :style="{ backgroundColor: color }"
                                            :class="{ 'active': selectedColor === color }"
                                            @click="selectedColor = color">
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700 mb-1.5">Priority Level</label>
                                <select id="priority" name="priority" x-model="announcementPriority"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 transition bg-white">
                                    <option value="normal">Normal Priority</option>
                                    <option value="high">High Priority</option>
                                </select>
                                @error('priority')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="image" class="block text-sm font-medium text-gray-700 mb-1.5">Announcement Image (Optional)</label>
                                <div class="border border-gray-300 rounded-md p-3 bg-gray-50">
                                    <input type="file" id="image" name="image" accept="image/*" @change="previewImage($event)"
                                        class="w-full">
                                    <p class="mt-1.5 text-xs text-gray-500">Supported formats: JPG, PNG, GIF (max 2MB)</p>
                                </div>
                                @error('image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="media" class="block text-sm font-medium text-gray-700 mb-1.5">Media Attachment (Optional)</label>
                                <div class="border border-gray-300 rounded-md p-3 bg-gray-50">
                                    <input type="file" id="media" name="media" accept="image/*,video/*" @change="previewMedia($event)"
                                        class="w-full">
                                    <p class="mt-1.5 text-xs text-gray-500">Supported formats: JPG, PNG, GIF, MP4, MOV (max 20MB)</p>
                                </div>
                                @error('media')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Hidden form fields for backend processing -->
                        <input type="hidden" name="text_color" x-bind:value="selectedColor">
                        <input type="hidden" name="status" value="published">

                        <div class="flex justify-end space-x-3 pt-3 mt-5 border-t border-gray-100">
                            <a href="{{ route('admin.announcements.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md text-sm font-medium transition duration-200 hover:bg-gray-300">
                                Cancel
                            </a>
                            <button type="submit" class="bg-red-600 text-white px-5 py-2 rounded-md text-sm font-medium transition duration-200 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                    :disabled="isSubmitting"
                                    :class="{ 'opacity-75 cursor-not-allowed': isSubmitting }">
                                <span x-show="!isSubmitting">Publish Announcement</span>
                                <span x-show="isSubmitting" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Publishing...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right Panel: Live Preview -->
                <div class="w-full md:w-1/2 p-6 md:p-8 bg-gray-50">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Preview</h2>

                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <div class="flex flex-wrap justify-between items-start mb-4 gap-2">
                            <h3 class="text-xl font-semibold text-gray-900" x-text="announcementTitle || 'Announcement Title'"></h3>
                            <span x-show="announcementPriority === 'high'" class="text-xs font-bold uppercase text-red-600 bg-red-50 px-2.5 py-1 rounded-full border border-red-100">Important</span>
                        </div>

                        <div class="flex flex-wrap items-center text-sm text-gray-500 mb-5 border-b border-gray-100 pb-4">
                            <div class="flex items-center mr-4 mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span x-text="currentDateTime()"></span>
                            </div>

                            <div class="flex items-center mb-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="font-medium">{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</span>
                                <span class="ml-1.5 bg-blue-100 text-blue-800 text-xs px-1.5 py-0.5 rounded">Admin</span>
                            </div>
                        </div>

                        <!-- Image preview -->
                        <div x-show="imagePreviewUrl" class="mb-6 rounded-lg overflow-hidden shadow-sm">
                            <img :src="imagePreviewUrl" alt="Image Preview" class="w-full max-h-[40vh] object-contain rounded-lg">
                        </div>

                        <!-- Media preview -->
                        <div x-show="mediaPreviewUrl" class="mb-6 rounded-lg overflow-hidden shadow-sm">
                            <img x-show="mediaPreviewUrl && mediaPreviewUrl.startsWith('data:image')" :src="mediaPreviewUrl" alt="Media Preview" class="w-full max-h-[40vh] object-contain rounded-lg">
                            <div x-show="mediaFileName && !mediaPreviewUrl.startsWith('data:image')" class="p-4 bg-gray-100 rounded-lg text-sm text-gray-700 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <span x-text="mediaFileName"></span> <span class="ml-1.5 italic">(Video will be shown after publishing)</span>
                            </div>
                        </div>

                        <div class="prose max-w-none text-gray-700 whitespace-pre-wrap overflow-auto p-4 bg-gray-50 rounded-lg border border-gray-100 min-h-[100px]" x-text="announcementContent || 'Your announcement content will appear here.'" :style="{ color: selectedColor }"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .color-swatch {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .color-swatch:hover, .color-swatch.active {
        border-color: #3b82f6; /* Blue-500 */
        transform: scale(1.1);
        box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    }
</style>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    function announcementCreator() {
        return {
            announcementTitle: '',
            announcementContent: '',
            announcementPriority: 'normal',
            availableColors: ['#1f2937', '#ef4444', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6'], // Gray, Red, Blue, Green, Amber, Purple
            selectedColor: '#1f2937', // Default to Gray-800
            mediaFileName: '',
            mediaPreviewUrl: null,
            imageFileName: '',
            imagePreviewUrl: null,
            isSubmitting: false,

            previewImage(event) {
                const file = event.target.files[0];
                if (file) {
                    this.imageFileName = file.name;
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.imagePreviewUrl = e.target.result;
                        }
                        reader.readAsDataURL(file);
                    }
                } else {
                    this.imageFileName = '';
                    this.imagePreviewUrl = null;
                }
            },

            previewMedia(event) {
                const file = event.target.files[0];
                if (file) {
                    this.mediaFileName = file.name;
                    // Only create previews for images
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.mediaPreviewUrl = e.target.result;
                        }
                        reader.readAsDataURL(file);
                    } else {
                        // For videos, just show the filename
                        this.mediaPreviewUrl = 'video';
                    }
                } else {
                    this.mediaFileName = '';
                    this.mediaPreviewUrl = null;
                }
            },

            currentDateTime() {
                const now = new Date();
                return now.toLocaleDateString('en-US', {
                    month: 'long',
                    day: 'numeric',
                    year: 'numeric'
                });
            }
        }
    }
</script>
@endsection

</x-app-layout> 