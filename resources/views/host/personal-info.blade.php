<x-guest-layout>
    @push('styles')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
        @vite(['resources/css/host.css'])
    @endpush

    <div class="host-page">
        <div class="page-back">
            <a href="{{ route('tenant.browsing') }}" class="back-btn">⬅ Back to browsing</a>
        </div>

        <div class="host-form">
            @if (session('status'))
                <div class="auth-status">{{ session('status') }}</div>
            @endif
            <x-validation-errors class="auth-errors" />
            <div class="form-header">
                <h2>Host Personal Information</h2>
                <p>Please provide your personal details to start your hosting journey.</p>
            </div>

            <form method="POST" action="{{ route('host.requests.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" value="{{ old('fullname', auth()->user()->name ?? '') }}" required />
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email address" value="{{ old('email', auth()->user()->email) }}" required />
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="+63 900 000 0000" value="{{ old('phone') }}" required />
                </div>

                <div class="form-group">
                    <label for="idUpload">Government ID</label>
                    <div class="upload-field">
                        <input type="file" id="idUpload" name="idUpload" accept="image/*" />
                        <span>📎 Upload a valid government ID</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="photoUpload">Profile Photo</label>
                    <div class="upload-field">
                        <input type="file" id="photoUpload" name="photoUpload" accept="image/*" />
                        <span>📷 Upload a profile photo (optional)</span>
                    </div>
                </div>

                <button type="submit">Submit Request →</button>
                <p class="mt-3" style="font-size: 12px; color:#6b7280;">By submitting, your application will be reviewed by an administrator. You will be notified upon approval.</p>
            </form>
        </div>
    </div>
</x-guest-layout>
