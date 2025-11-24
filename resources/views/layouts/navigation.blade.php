<!-- Add this with your other navigation links -->
<x-nav-link href="{{ route('files.index') }}" :active="request()->routeIs('files.*')">
    {{ __('Files') }}
</x-nav-link>
<x-nav-link href="{{ route('document-types.index') }}" :active="request()->routeIs('document-types.*')">
    {{ __('Document Types') }}
</x-nav-link>