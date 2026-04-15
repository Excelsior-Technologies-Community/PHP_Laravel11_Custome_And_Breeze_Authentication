<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Edit - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles & Scripts (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">

    <!-- Navigation -->
    @include('layouts.navigation')

    <!-- Page Heading -->
    <header class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Profile</h1>
        </div>
    </header>

    <!-- Page Content -->
    <main class="max-w-3xl mx-auto p-4">

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-2 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 shadow p-6 rounded">
            @csrf
            @method('PATCH')

            <div class="mb-4">
                <label class="block mb-1 text-gray-700 dark:text-gray-200">Name:</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="border p-2 w-full rounded">
                @error('name') <p class="text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1 text-gray-700 dark:text-gray-200">Avatar:</label>
                <input type="file" name="avatar" class="border p-2 w-full rounded">
                @error('avatar') <p class="text-red-500 mt-1">{{ $message }}</p> @enderror

                @if($user->avatar)
                    <img src="{{ asset('storage/avatars/'.$user->avatar) }}" width="100" class="mt-2 rounded-full">
                @endif
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Update Profile</button>
        </form>

        <form action="{{ route('profile.destroy') }}" method="POST" class="mt-4">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Delete Account</button>
        </form>

    </main>

</body>
</html>