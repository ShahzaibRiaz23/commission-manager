<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Staff Account (VA / Sales)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 max-w-2xl mx-auto">

                <!-- Success Message Alert -->
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf

                    <!-- Name -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Full Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus
                            class="w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-4" x-data="{ showPassword: false }">
                        <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                            Password
                        </label>

                        <div style="position: relative; width: 100%; margin-top: 4px;">

                            <input :type="showPassword ? 'text' : 'password'" name="password" required
                                autocomplete="new-password"
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"
                                style="padding-right: 45px; height: 42px;">

                            <button type="button" @click="showPassword = !showPassword" style="
                position: absolute;
                right: 12px;
                top: 50%;
                transform: translateY(-50%);
                z-index: 10;
                background: transparent;
                border: none;
                cursor: pointer;
                padding: 4px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #9ca3af;
            ">
                                <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7s-8.268-2.943-9.542-7z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>

                                <svg x-show="showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" width="20"
                                    height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 3l18 18M10.58 10.59a2 2 0 002.83 2.83M9.88 5.09A10.6 10.6 0 0112 5c4.48 0 8.27 2.94 9.54 7a10.5 10.5 0 01-4.05 5.3M6.61 6.61A10.5 10.5 0 002.46 12a10.5 10.5 0 005.37 6.4A10.5 10.5 0 0012 19" />
                                </svg>
                            </button>

                        </div>

                        @error('password')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Select Role -->
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Assign Role</label>
                        <select name="role" required
                            class="w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                            <option value="">-- Select Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ ucwords(str_replace('-', ' ', $role->name)) }}</option>
                            @endforeach
                        </select>
                        @error('role') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end mt-4">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            Create Account
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>