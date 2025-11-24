{{-- resources/views/files/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upload File') }}
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

                    <h3 class="text-lg font-semibold mb-6">Upload New File</h3>

                    <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- File Upload -->
                        <div>
                            <label for="file" class="block text-sm font-medium text-gray-700">Select File *</label>
                            <input type="file" name="file" id="file" 
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                                   required>
                            @error('file')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-sm mt-1">Maximum file size: 10MB</p>
                        </div>

                        <!-- Document Type -->
                        <div>
                            <label for="document_type_id" class="block text-sm font-medium text-gray-700">Document Type *</label>
                            <select name="document_type_id" id="document_type_id" 
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                                <option value="">Select Document Type</option>
                                @foreach(\App\Models\DocumentType::all() as $type)
                                    <option value="{{ $type->id }}" {{ old('document_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                        @if($type->is_required)
                                            (Required)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('document_type_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Entity Selection -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Entity Type -->
                            <div>
                                <label for="fileable_type" class="block text-sm font-medium text-gray-700">Attach To *</label>
                                <select name="fileable_type" id="fileable_type" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                                    <option value="">Select Entity Type</option>
                                    <option value="App\Models\Apartment" {{ old('fileable_type') == 'App\Models\Apartment' ? 'selected' : '' }}>Apartment</option>
                                    <option value="App\Models\Tenant" {{ old('fileable_type') == 'App\Models\Tenant' ? 'selected' : '' }}>Tenant</option>
                                    <option value="App\Models\Lease" {{ old('fileable_type') == 'App\Models\Lease' ? 'selected' : '' }}>Lease</option>
                                </select>
                                @error('fileable_type')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Entity ID (will be populated dynamically) -->
                            <div>
                                <label for="fileable_id" class="block text-sm font-medium text-gray-700">Select Entity *</label>
                                <select name="fileable_id" id="fileable_id" 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                                    <option value="">Select an entity first</option>
                                </select>
                                @error('fileable_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3"
                                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                                      placeholder="Optional file description">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Options -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Public Access -->
                            <div class="flex items-center">
                                <input type="checkbox" name="is_public" id="is_public" value="1" 
                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded"
                                       {{ old('is_public') ? 'checked' : '' }}>
                                <label for="is_public" class="ml-2 block text-sm text-gray-700">
                                    Make file publicly accessible
                                </label>
                            </div>

                            <!-- Expiration Date -->
                            <div>
                                <label for="expires_at" class="block text-sm font-medium text-gray-700">Expiration Date</label>
                                <input type="date" name="expires_at" id="expires_at" 
                                       class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"
                                       value="{{ old('expires_at') }}"
                                       min="{{ date('Y-m-d') }}">
                                <p class="text-gray-500 text-sm mt-1">Optional - file will expire after this date</p>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end space-x-3 pt-6">
                            <a href="{{ route('files.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Upload File
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for dynamic entity loading -->
    @section('scripts')
    <script>
        document.getElementById('fileable_type').addEventListener('change', function() {
            const entityType = this.value;
            const entitySelect = document.getElementById('fileable_id');
            
            // Clear existing options
            entitySelect.innerHTML = '<option value="">Loading...</option>';
            
            if (!entityType) {
                entitySelect.innerHTML = '<option value="">Select an entity first</option>';
                return;
            }
            
            // Map entity types to API endpoints (we'll create these later)
            const endpoints = {
                'App\Models\Apartment': '/api/apartments', // We'll create this API later
                'App\Models\Tenant': '/api/tenants',
                'App\Models\Lease': '/api/leases'
            };
            
            // For now, we'll use a simple approach - you can enhance this later
            fetchOptions(entityType);
        });
        
        function fetchOptions(entityType) {
            const entitySelect = document.getElementById('fileable_id');
            
            // Simple mapping for demo - replace with actual API calls later
            const options = {
                'App\Models\Apartment': [
                    { id: 1, name: 'Apartment 101' },
                    { id: 2, name: 'Apartment 102' }
                ],
                'App\Models\Tenant': [
                    { id: 1, name: 'John Doe' },
                    { id: 2, name: 'Jane Smith' }
                ],
                'App\Models\Lease': [
                    { id: 1, name: 'Lease #001' },
                    { id: 2, name: 'Lease #002' }
                ]
            };
            
            const entityOptions = options[entityType] || [];
            
            entitySelect.innerHTML = '<option value="">Select ' + entityType.split('\\').pop() + '</option>';
            entityOptions.forEach(option => {
                entitySelect.innerHTML += `<option value="${option.id}">${option.name}</option>`;
            });
        }
        
        // Initialize if there's a previously selected value
        @if(old('fileable_type'))
            document.addEventListener('DOMContentLoaded', function() {
                fetchOptions('{{ old('fileable_type') }}');
                document.getElementById('fileable_id').value = '{{ old('fileable_id') }}';
            });
        @endif
    </script>
    @endsection
</x-app-layout>