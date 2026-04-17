<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    <nav class="bg-white shadow px-6 py-4 flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-800">Customer Dashboard</h1>

        <div class="flex space-x-3">
            <a href="{{ route('customer.profile.edit') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition duration-200">
                Edit Profile
            </a>
            <a href="{{ route('customer.logout') }}"
               class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition duration-200">
                Logout
            </a>
        </div>
    </nav>

    <div class="flex-grow flex items-center justify-center">
        <div class="bg-white shadow-lg rounded-lg w-full max-w-lg p-8 text-center">
            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                Welcome, {{ Auth::guard('customer')->user()->name }}!
            </h2>

            <p class="text-gray-600 mb-6">
                You are successfully logged in to your customer account.
            </p>
            
            <div class="border-t pt-4">
                <p class="text-sm text-gray-500 italic">Account Status: <span class="text-green-600 font-bold uppercase">{{ Auth::guard('customer')->user()->status }}</span></p>
            </div>
        </div>
    </div>

    <footer class="bg-white shadow py-4 text-center text-gray-500">
        &copy; {{ date('Y') }} Your Company. All rights reserved.
    </footer>

</body>
</html>