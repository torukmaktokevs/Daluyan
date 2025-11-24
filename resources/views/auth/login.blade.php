<x-guest-layout>
    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-left">
                <div class="auth-head">
                    <h2>Log in</h2>
                    <p>Welcome back! Sign in to continue.</p>
                </div>

                <x-validation-errors class="auth-errors" />

                @if (session('status'))
                    <div class="auth-status">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="auth-form">
                    @csrf

                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />

                    <label for="password" class="mt-3">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password" />

                    <div class="auth-aux">
                        <label class="remember">
                            <input type="checkbox" name="remember" id="remember_me" />
                            <span>Remember me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot">Forgot password?</a>
                        @endif
                    </div>

                    <button type="submit" class="auth-btn">Log in</button>

                    <p class="auth-meta">Don't have an account?
                        <a href="{{ route('register') }}">Create one</a>
                    </p>
                </form>
            </div>

            <div class="auth-right">
                <img src="{{ asset('images/common/loginimg.png') }}" alt="Apartments" />
            </div>
        </div>
    </div>
</x-guest-layout>
