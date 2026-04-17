<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Customer Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if(session('success'))
                    <div class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
                        {{ session('success') }}
                    </div>
                @endif

                <table class="w-full text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3 text-center">Status</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $customer->name }}</td>
                            <td class="px-6 py-4">{{ $customer->email }}</td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('customers.toggle', $customer->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="relative inline-flex items-center cursor-pointer">
                                        <div class="w-11 h-6 rounded-full transition-colors {{ $customer->status === 'active' ? 'bg-green-500' : 'bg-gray-400' }}">
                                            <div class="absolute top-1 left-1 bg-white w-4 h-4 rounded-full transition-transform {{ $customer->status === 'active' ? 'translate-x-5' : '' }}"></div>
                                        </div>
                                        <span class="ml-3 text-xs font-medium">{{ ucfirst($customer->status) }}</span>
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500 hover:text-red-700 text-sm font-bold">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>