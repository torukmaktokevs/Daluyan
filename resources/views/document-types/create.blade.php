{{-- resources/views/document-types/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Document Type') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Back Button -->
                    <div class="mb-6">
                        <a href="{{ route('document-types.index') }}" class="text-blue-500 hover:text-blue-700">
                            &larr; Back to Document Types
                        </a>
                    </div>

                    <h3 class="text-lg font-semibold mb-6">Create New Document Type</h3>

                    <form action="{{ route('document-types.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                            <input type="text" name="name" id="name" 
                                   value="{{ old('name') }}"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                                   required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3"
                                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                                      placeholder="Describe what this document type is for">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Options Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Required -->
                            <div class="flex items-start space-x-3">
                                <input type="checkbox" name="is_required" id="is_required" value="1" 
                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded mt-1"
                                       {{ old('is_required') ? 'checked' : '' }}>
                                <div>
                                    <label for="is_required" class="block text-sm font-medium text-gray-700">
                                        Required Document
                                    </label>
                                    <p class="text-sm text-gray-500">Files of this type must be provided</p>
                                </div>
                            </div>

                            <!-- Max Size -->
                            <div>
                                <label for="max_size" class="block text-sm font-medium text-gray-700">Maximum File Size (KB)</label>
                                <input type="number" name="max_size" id="max_size" 
                                       value="{{ old('max_size') }}"
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                                       placeholder="2048 (2MB)"
                                       min="1" max="10240">
                                @error('max_size')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-500 text-sm mt-1">Leave empty for no limit (max 10MB)</p>
                            </div>
                        </div>

                        <!-- Allowed Extensions -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Allowed File Extensions</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                @php
                                    $commonExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx', 'txt'];
                                    $oldExtensions = old('allowed_extensions', []);
                                @endphp
                                
                                @foreach($commonExtensions as $ext)
                                <div class="flex items-center">
                                    <input type="checkbox" name="allowed_extensions[]" id="ext_{{ $ext }}" 
                                           value="{{ $ext }}"
                                           class="h-4 w-4 text-blue-600 border-gray-300 rounded"
                                           {{ in_array($ext, $oldExtensions) ? 'checked' : '' }}>
                                    <label for="ext_{{ $ext }}" class="ml-2 text-sm text-gray-700">
                                        .{{ $ext }}
                                    </label>
                                </div>
                                @endforeach
                                
                                <!-- Custom Extension -->
                                <div class="md:col-span-4 mt-2">
                                    <label for="custom_extension" class="block text-sm font-medium text-gray-700">Custom Extension</label>
                                    <div class="flex space-x-2">
                                        <input type="text" name="custom_extension" id="custom_extension" 
                                               class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                                               placeholder="Enter custom extension (without dot)">
                                        <button type="button" onclick="addCustomExtension()" 
                                                class="mt-1 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                            Add
                                        </button>
                                    </div>
                                    <div id="custom-extensions-list" class="mt-2 flex flex-wrap gap-2">
                                        <!-- Custom extensions will be added here -->
                                    </div>
                                </div>
                            </div>
                            @error('allowed_extensions')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-sm mt-1">Select allowed file extensions. Leave all unchecked to allow any type.</p>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-3 pt-6 border-t">
                            <a href="{{ route('document-types.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create Document Type
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
    <script>
        function addCustomExtension() {
            const input = document.getElementById('custom_extension');
            const extension = input.value.trim().toLowerCase();
            
            if (!extension) return;
            
            // Remove dot if present
            const cleanExtension = extension.replace(/^\./, '');
            
            // Check if already exists
            const existingCheckbox = document.getElementById('ext_' + cleanExtension);
            if (existingCheckbox) {
                existingCheckbox.checked = true;
                input.value = '';
                return;
            }
            
            // Add to custom extensions list
            const list = document.getElementById('custom-extensions-list');
            const badge = document.createElement('div');
            badge.className = 'bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded flex items-center';
            badge.innerHTML = `
                .${cleanExtension}
                <input type="hidden" name="allowed_extensions[]" value="${cleanExtension}">
                <button type="button" onclick="this.parentElement.remove()" class="ml-1 text-blue-600 hover:text-blue-800">
                    ×
                </button>
            `;
            list.appendChild(badge);
            
            input.value = '';
        }
        
        // Allow pressing Enter to add custom extension
        document.getElementById('custom_extension').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addCustomExtension();
            }
        });
    </script>
    @endsection
</x-app-layout>