<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Host Dashboard</h2>
            <div class="flex gap-2">
                <a href="{{ route('host.properties.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm">Manage Properties</a>
                <a href="{{ route('host.tenants.index') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm">Tenants</a>
                <a href="{{ route('host.applications.index') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-md text-sm">Applications</a>
                <a href="{{ route('host.maintenance.index') }}" class="px-4 py-2 bg-amber-600 text-white rounded-md text-sm">Maintenance</a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg p-4 shadow">
                <div class="text-sm text-gray-500">Properties</div>
                <div class="text-3xl font-semibold">{{ $stats['properties'] }}</div>
            </div>
            <div class="bg-white rounded-lg p-4 shadow">
                <div class="text-sm text-gray-500">Tenants</div>
                <div class="text-3xl font-semibold">{{ $stats['tenants'] }}</div>
            </div>
            <div class="bg-white rounded-lg p-4 shadow">
                <div class="text-sm text-gray-500">Applications (Pending)</div>
                <div class="text-3xl font-semibold">{{ $stats['applications_pending'] }}</div>
            </div>
            <div class="bg-white rounded-lg p-4 shadow">
                <div class="text-sm text-gray-500">Maintenance (Open)</div>
                <div class="text-3xl font-semibold">{{ $stats['maintenance_open'] }}</div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold">Quick Actions</h3>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('host.properties.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md">Add New Property</a>
                    <a href="{{ route('host.applications.index') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-md">Review Applications</a>
                    <a href="{{ route('host.maintenance.index') }}" class="px-4 py-2 bg-amber-600 text-white rounded-md">Open Maintenance</a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-5">
                <h3 class="text-lg font-semibold mb-3">Recent Activity</h3>
                <ul class="text-sm text-gray-600 list-disc pl-5">
                    <li>No recent activity yet.</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
