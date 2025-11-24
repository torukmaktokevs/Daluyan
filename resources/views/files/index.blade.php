{{-- resources/views/files/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('File Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">All Files</h3>
                        <div class="space-x-2">
                            <a href="{{ route('files.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Upload File
                            </a>
                            <a href="{{ route('document-types.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Document Types
                            </a>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                        <form action="{{ route('files.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Document Type</label>
                                <select name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">All Types</option>
                                    @foreach(\App\Models\DocumentType::all() as $type)
                                        <option value="{{ $type->name }}" {{ request('type') == $type->name ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Entity Type</label>
                                <select name="entity_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">All Entities</option>
                                    <option value="App\Models\Apartment" {{ request('entity_type') == 'App\Models\Apartment' ? 'selected' : '' }}>Apartment</option>
                                    <option value="App\Models\Tenant" {{ request('entity_type') == 'App\Models\Tenant' ? 'selected' : '' }}>Tenant</option>
                                    <option value="App\Models\Lease" {{ request('entity_type') == 'App\Models\Lease' ? 'selected' : '' }}>Lease</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Filter
                                </button>
                                <a href="{{ route('files.index') }}" class="ml-2 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Clear
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Files Table -->
                    @if($files->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($files as $file)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($file->isImage())
                                                <i class="fas fa-image text-blue-500 mr-2"></i>
                                            @elseif($file->isPdf())
                                                <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                                            @else
                                                <i class="fas fa-file text-gray-500 mr-2"></i>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $file->original_name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $file->description }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $file->documentType->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($file->fileable_type === 'App\Models\Apartment')
                                            Apartment: {{ $file->fileable->name }}
                                        @elseif($file->fileable_type === 'App\Models\Tenant')
                                            Tenant: {{ $file->fileable->user->name }}
                                        @elseif($file->fileable_type === 'App\Models\Lease')
                                            Lease: {{ $file->fileable->apartment->name }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $file->formatted_size }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $file->created_at->format('M d, Y') }}
                                        <div class="text-xs text-gray-400">
                                            by {{ $file->uploadedBy->name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="{{ route('files.download', $file) }}" class="text-blue-600 hover:text-blue-900" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <a href="{{ route('files.preview', $file) }}" class="text-green-600 hover:text-green-900" title="Preview" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('files.show', $file) }}" class="text-gray-600 hover:text-gray-900" title="Details">
                                            <i class="fas fa-info-circle"></i>
                                        </a>
                                        <form action="{{ route('files.destroy', $file) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Delete" onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $files->links() }}
                    </div>
                    @else
                    <div class="text-center py-8">
                        <i class="fas fa-folder-open text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">No files found.</p>
                        <a href="{{ route('files.create') }}" class="text-blue-500 hover:text-blue-700 mt-2 inline-block">
                            Upload your first file
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>