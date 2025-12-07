<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <!-- Ensure proper scaling on all devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Page Title -->
    <title>Customer Dashboard</title>

    <!-- TailwindCSS CDN for modern UI styling -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<!-- Full height + light gray background + column layout -->
<body class="bg-gray-100 min-h-screen flex flex-col">

    <!-- ===============================
         TOP NAVBAR
    ================================== -->
    <nav class="bg-white shadow px-6 py-4 flex justify-between items-center">

        <!-- Dashboard Title -->
        <h1 class="text-xl font-bold text-gray-800">Customer Dashboard</h1>

        <!-- Logout Button -->
        <a href="{{ route('customer.logout') }}"
           class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition duration-200">
            Logout
        </a>
    </nav>


    <!-- ===============================
         MAIN DASHBOARD CONTENT
    ================================== -->
    <div class="flex-grow flex items-center justify-center">

        <!-- Dashboard Card -->
        <div class="bg-white shadow-lg rounded-lg w-full max-w-lg p-8 text-center">

            <!-- Logged-in Customer Name -->
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                Welcome, {{ Auth::guard('customer')->user()->name }}!
            </h2>

            <!-- Simple dashboard description -->
            <p class="text-gray-600 mb-6">
                You are successfully logged in to your customer account.
            </p>

        </div>
    </div>


    <!-- ===============================
         FOOTER SECTION
    ================================== -->
    <footer class="bg-white shadow py-4 text-center text-gray-500">
        &copy; {{ date('Y') }} Your Company. All rights reserved.
    </footer>

</body>
</html>