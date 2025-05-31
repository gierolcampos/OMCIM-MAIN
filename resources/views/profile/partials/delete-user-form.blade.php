<section class="bg-white p-6 rounded-lg shadow-sm mt-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Request Account Deletion') }}
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            {{ __('You can request to have your account deleted. This request will be reviewed by an administrator. Once your account is deleted, all of its resources and data will be permanently deleted.') }}
        </p>
    </header>

    <div class="mt-6">
        <button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
        >{{ __('Request Account Deletion') }}</button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-white">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Are you sure you want to request account deletion?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                {{ __('Your request will be reviewed by an administrator. Once approved, your account and all associated data will be permanently deleted. Please enter your password to confirm this request.') }}
            </p>

            <div class="mt-6">
                <label for="password" class="block text-sm font-medium text-gray-700 required">
                    {{ __('Password') }}
                </label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="{{ __('Enter your current password') }}"
                    required
                />

                @error('password', 'userDeletion')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" x-on:click="$dispatch('close')"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded transition-colors mr-3">
                    {{ __('Cancel') }}
                </button>

                <button type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    {{ __('Submit Deletion Request') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
