<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <!-- Responsive layout for all devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Page Title -->
    <title>Customer Login</title>

    <!-- TailwindCSS CDN for modern styling -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<!-- Full screen center alignment -->
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <!-- Main Login Card -->
    <div class="bg-white shadow-lg rounded-lg w-full max-w-md p-8">

        <!-- Page Heading -->
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">
            Customer Login
        </h2>

        <!-- ===============================
             DISPLAY VALIDATION ERRORS
             (Invalid email/password)
        ================================== -->
        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- ===============================
             DISPLAY SUCCESS MESSAGE
             (After successful registration)
        ================================== -->
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- ===============================
             CUSTOMER LOGIN FORM
        ================================== -->
        <form action="{{ route('customer.login') }}" method="POST" class="space-y-5">

            <!-- CSRF protection (required for Laravel forms) -->
            @csrf

            <!-- Email Input -->
            <div>
                <label class="block text-gray-700 mb-1" for="email">
                    Email
                </label>

                <!-- Customer Email Field -->
                <input
                    type="email"
                    name="email"
                    id="email"
                    placeholder="Enter your email"
                    class="w-full border border-gray-300 rounded px-4 py-2
                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                >
            </div>

            <!-- Password Input -->
            <div>
                <label class="block text-gray-700 mb-1" for="password">
                    Password
                </label>

                <!-- Customer Password Field -->
                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Enter your password"
                    class="w-full border border-gray-300 rounded px-4 py-2
                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                >
            </div>

            <!-- Login Button -->
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold
                       py-2 px-4 rounded transition duration-200"
            >
                Login
            </button>
        </form>

        <!-- Redirect Link to Registration Page -->
        <div class="text-center mt-5">
            <p class="text-gray-600">
                Don't have an account?
                <a href="{{ route('customer.register') }}"
                   class="text-blue-600 hover:underline">
                   Register here
                </a>
            </p>
        </div>

    </div>
</body>
</html>
