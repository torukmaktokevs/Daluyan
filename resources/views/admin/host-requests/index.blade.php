<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Host Requests') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('status') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <th class="px-4 py-3">Applicant</th>
                                    <th class="px-4 py-3">Email</th>
                                    <th class="px-4 py-3">Phone</th>
                                    <th class="px-4 py-3">ID</th>
                                    <th class="px-4 py-3">Photo</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($requests as $r)
                                    <tr class="text-sm">
                                        <td class="px-4 py-3">
                                            <div class="font-semibold text-gray-900">{{ $r->full_name }}</div>
                                            <div class="text-gray-500">User #{{ $r->user_id }}</div>
                                        </td>
                                        <td class="px-4 py-3">{{ $r->email }}</td>
                                        <td class="px-4 py-3">{{ $r->phone }}</td>
                                        <td class="px-4 py-3">
                                            @if($r->id_path)
                                                <a class="text-sky-600" href="{{ asset('storage/'.$r->id_path) }}" target="_blank">View</a>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($r->photo_path)
                                                <a class="text-sky-600" href="{{ asset('storage/'.$r->photo_path) }}" target="_blank">View</a>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs {{ $r->status === 'approved' ? 'bg-green-100 text-green-800' : ($r->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">{{ ucfirst($r->status) }}</span>
                                        </td>
                                        <td class="px-4 py-3 space-x-2">
                                            <form method="POST" action="{{ route('admin.host-requests.approve', $r) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700" @disabled($r->status==='approved')>Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.host-requests.reject', $r) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700" @disabled($r->status==='rejected')>Reject</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">No host requests yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $requests->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
