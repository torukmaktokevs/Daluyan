<x-guest-layout>
    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-left">
                <div class="auth-head">
                    <h2>Create account</h2>
                    <p>Join us and start your journey.</p>
                </div>

                <x-validation-errors class="auth-errors" />

                <form method="POST" action="{{ route('register') }}" class="auth-form">
                    @csrf

                    <label for="name">Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />

                    <label for="email" class="mt-3">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />

                    <label for="password" class="mt-3">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password" />

                    <label for="password_confirmation" class="mt-3">Confirm password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />

                    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                        <label class="terms mt-3">
                            <input type="checkbox" name="terms" id="terms" required />
                            <span>
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </span>
                        </label>
                    @endif

                    <button type="submit" class="auth-btn">Create account</button>

                    <p class="auth-meta">Already have an account?
                        <a href="{{ route('login') }}">Log in</a>
                    </p>
                </form>
            </div>

            <div class="auth-right">
                <img src="{{ asset('images/common/loginimg.png') }}" alt="Apartments" />
            </div>
        </div>
    </div>
</x-guest-layout>
