<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <form action="{{ route('sessions.logout_all') }}" method="POST" onsubmit="return confirm('Logout from all other devices?')">
                @csrf
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded text-sm font-bold">
                    Logout All Other Devices
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Active Login Sessions</h3>
                
                @if(session('success'))
                    <div class="mb-4 text-green-500 font-bold">{{ session('success') }}</div>
                @endif

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-gray-500 dark:text-gray-400">
                        <thead class="bg-gray-50 dark:bg-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-2">IP Address</th>
                                <th class="px-4 py-2">Device / Browser</th>
                                <th class="px-4 py-2">Last Activity</th>
                                <th class="px-4 py-2 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $session)
                            <tr class="border-b dark:border-gray-700">
                                <td class="px-4 py-3">{{ $session->ip_address }}</td>
                                <td class="px-4 py-3 text-sm truncate max-w-xs">{{ $session->user_agent }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}</td>
                                <td class="px-4 py-3 text-right">
                                    @if($session->id == session()->getId())
                                        <span class="text-green-500 font-bold text-xs uppercase">Current Device</span>
                                    @else
                                        <form action="{{ route('sessions.destroy', $session->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:underline text-xs">Logout Session</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>