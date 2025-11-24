{{-- resources/views/files/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('File Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Back Button -->
                    <div class="mb-6">
                        <a href="{{ route('files.index') }}" class="text-blue-500 hover:text-blue-700">
                            &larr; Back to Files
                        </a>
                    </div>

                    <!-- File Header -->
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center space-x-4">
                            @if($file->isImage())
                                <i class="fas fa-image text-blue-500 text-3xl"></i>
                            @elseif($file->isPdf())
                                <i class="fas fa-file-pdf text-red-500 text-3xl"></i>
                            @else
                                <i class="fas fa-file text-gray-500 text-3xl"></i>
                            @endif
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $file->original_name }}</h1>
                                <p class="text-gray-500">{{ $file->description }}</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('files.download', $file) }}" 
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-download mr-2"></i>Download
                            </a>
                            @if($file->isImage() || $file->isPdf())
                            <a href="{{ route('files.preview', $file) }}" 
                               class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" target="_blank">
                                <i class="fas fa-eye mr-2"></i>Preview
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- File Information Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Basic Information -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold mb-4">File Information</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Original Name</dt>
                                    <dd class="text-sm text-gray-900">{{ $file->original_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Stored Name</dt>
                                    <dd class="text-sm text-gray-900">{{ $file->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">File Size</dt>
                                    <dd class="text-sm text-gray-900">{{ $file->formatted_size }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">File Type</dt>
                                    <dd class="text-sm text-gray-900">{{ $file->mime_type }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Extension</dt>
                                    <dd class="text-sm text-gray-900">.{{ $file->extension }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Document & Entity Information -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold mb-4">Document & Entity</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Document Type</dt>
                                    <dd class="text-sm text-gray-900">{{ $file->documentType->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Attached To</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if($file->fileable_type === 'App\Models\Apartment')
                                            Apartment: {{ $file->fileable->name }}
                                        @elseif($file->fileable_type === 'App\Models\Tenant')
                                            Tenant: {{ $file->fileable->user->name }}
                                        @elseif($file->fileable_type === 'App\Models\Lease')
                                            Lease: {{ $file->fileable->apartment->name }}
                                        @else
                                            {{ $file->fileable_type }}
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Uploaded By</dt>
                                    <dd class="text-sm text-gray-900">{{ $file->uploadedBy->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Upload Date</dt>
                                    <dd class="text-sm text-gray-900">{{ $file->created_at->format('M d, Y \a\t h:i A') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Status & Settings -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- File Status -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold mb-4">File Status</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-500">Public Access</span>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                              {{ $file->is_public ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $file->is_public ? 'Public' : 'Private' }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-500">Expiration</span>
                                    <span class="text-sm text-gray-900">
                                        @if($file->expires_at)
                                            {{ $file->expires_at->format('M d, Y') }}
                                            @if($file->isExpired())
                                                <span class="text-red-500 ml-2">(Expired)</span>
                                            @else
                                                <span class="text-green-500 ml-2">(Active)</span>
                                            @endif
                                        @else
                                            Never
                                        @endif
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-500">Storage Path</span>
                                    <span class="text-sm text-gray-900 font-mono text-xs">{{ $file->path }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <!-- Share File -->
                                <form action="{{ route('files.share', $file) }}" method="POST" class="flex space-x-2">
                                    @csrf
                                    <button type="submit" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded text-sm">
                                        <i class="fas fa-share-alt mr-2"></i>Create Share Link
                                    </button>
                                </form>

                                <!-- Delete File -->
                                <form action="{{ route('files.destroy', $file) }}" method="POST" class="flex space-x-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm"
                                            onclick="return confirm('Are you sure you want to delete this file? This action cannot be undone.')">
                                        <i class="fas fa-trash mr-2"></i>Delete File
                                    </button>
                                </form>

                                <!-- Share Links (if any) -->
                                @if($file->shares->count() > 0)
                                <div class="mt-4">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Active Share Links</h4>
                                    <div class="space-y-2">
                                        @foreach($file->shares->where('is_active', true) as $share)
                                        <div class="flex items-center justify-between bg-white p-2 rounded border">
                                            <div class="text-xs text-gray-600 truncate">
                                                {{ url("/shared/{$share->token}") }}
                                            </div>
                                            <form action="{{ route('files.shares.destroy', $share) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs">
                                                    Revoke
                                                </button>
                                            </form>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- File Preview (if image or PDF) -->
                    @if($file->isImage() || $file->isPdf())
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold mb-4">File Preview</h3>
                        <div class="flex justify-center">
                            @if($file->isImage())
                                <img src="{{ route('files.preview', $file) }}" 
                                     alt="{{ $file->original_name }}"
                                     class="max-w-full h-auto rounded-lg shadow-md max-h-96">
                            @elseif($file->isPdf())
                                <div class="text-center">
                                    <i class="fas fa-file-pdf text-red-500 text-6xl mb-4"></i>
                                    <p class="text-gray-600">PDF files can be previewed in a new window</p>
                                    <a href="{{ route('files.preview', $file) }}" 
                                       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-2 inline-block"
                                       target="_blank">
                                        Open PDF Preview
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>