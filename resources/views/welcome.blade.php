<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Find your dream property with PropertyPulse. Browse thousands of properties for sale and rent across India. Connect with top real estate agents.">
    <meta name="author" content="Dev Cube Tech">
    <title>Daluyan - Find Your Dream Home</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="nav-container">
                <div class="logo">
                    <img src="{{asset('images/common/logo1.png')}}" alt="Daluyan Logo">
                   
                </div>
                <nav>
                    <ul id="navMenu">
                        <li><a href="#" class="active">Home</a></li>
                        <li><a href="#listings">Properties</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                    </ul>
                </nav>
                <div class="auth-buttons">
                    @guest
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="btn"><i class="fas fa-sign-in-alt"></i> Login</a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn" style="margin-left:8px;"><i class="fas fa-user-plus"></i> Register</a>
                        @endif
                    @else
                        <span style="margin-right:8px;">Hello, {{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
                        </form>
                    @endguest
                </div>
                <div class="mobile-menu" id="mobileMenu">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Find Your Apartment Here at Daluyan</h1>
                <p>Discover Affordable Apartments Here in the Philippines</p>
                
            </div>
        </div>
    </section>

    <!-- Property Listings -->
    <section id="listings" class="listings">
        <div class="container">
            <div class="section-title">
                <h2>Featured Apartments</h2>
            </div>
            <div class="filters">
                <button class="filter-btn active" data-filter="all">All</button>
                <button class="filter-btn" data-filter="sale">For Sale</button>
                <button class="filter-btn" data-filter="rent">For Rent</button>
                
            </div>
            <div class="property-grid" id="propertyGrid">
                @php $items = $featuredApartments ?? collect(); @endphp
                @forelse($items as $apt)
                    @php
                        $thumb = optional(optional($apt->files)->first())->path ?? null;
                        $img = $thumb ? asset('storage/'.$thumb) : 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=800&q=60';
                        $showUrl = route('tenant.apartments.show', $apt);
                    @endphp
                    @auth
                        <a href="{{ $showUrl }}" class="property-card" style="text-decoration:none;color:inherit;" aria-label="View apartment: {{ $apt->title }}">
                    @else
                        <a href="javascript:void(0)" class="property-card requires-login" data-target-login="true" style="text-decoration:none;color:inherit;" aria-haspopup="dialog" aria-label="Login required to view apartment: {{ $apt->title }}">
                    @endauth
                        <div class="property-image">
                            <img src="{{ $img }}" alt="{{ $apt->title }}" loading="lazy"/>
                            <span class="property-badge">{{ ucfirst($apt->status ?? 'available') }}</span>
                        </div>
                        <div class="property-content">
                            <h3 class="property-title">{{ $apt->title }}</h3>
                            <p class="property-location"><i class="fas fa-map-marker-alt"></i> {{ $apt->address }}</p>
                            <div class="property-details">
                                <span><i class="fas fa-bed"></i> {{ $apt->bedrooms }} Beds</span>
                                <span><i class="fas fa-bath"></i> {{ $apt->bathrooms }} Baths</span>
                                @if(!empty($apt->area))
                                    <span><i class="fas fa-vector-square"></i> {{ $apt->area }} sqm</span>
                                @endif
                            </div>
                            <div class="property-price">₱{{ number_format((float) $apt->price, 2) }} <small style="color:#6b7280">/ month</small></div>
                        </div>
                    </a>
                @empty
                    <div class="empty-state" style="text-align:center;color:#6b7280;width:100%;grid-column:1/-1;padding:24px;border:1px dashed #e5e7eb;border-radius:12px;">No apartments found yet. Please check back later.</div>
                @endforelse
            </div>
            @guest
            <!-- Login Required Modal -->
            <div id="loginModal" class="login-modal" aria-hidden="true" role="dialog" aria-labelledby="loginModalTitle" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:80;align-items:center;justify-content:center;padding:20px;">
                <div class="login-modal-content" style="background:#fff;border-radius:16px;max-width:420px;width:100%;padding:30px;box-shadow:0 10px 30px rgba(0,0,0,.15);position:relative;">
                    <button type="button" id="loginModalClose" aria-label="Close" style="position:absolute;top:12px;right:12px;background:none;border:none;font-size:20px;cursor:pointer;color:#64748B;">&times;</button>
                    <h2 id="loginModalTitle" style="margin:0 0 10px;font-size:24px;font-weight:600;color:#1A1A1A;">Please Login</h2>
                    <p style="margin:0 0 20px;color:#64748B;line-height:1.5;font-size:14px;">You need to be logged in to view full apartment details and apply. Create an account or login below.</p>
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <a href="{{ route('login') }}" class="btn" style="background:#016B61;color:#fff;padding:14px 18px;border-radius:10px;text-align:center;font-weight:600;text-decoration:none;">Login</a>
                        @if(Route::has('register'))
                            <a href="{{ route('register') }}" class="btn" style="background:#0d9488;color:#fff;padding:14px 18px;border-radius:10px;text-align:center;font-weight:600;text-decoration:none;">Register</a>
                        @endif
                    </div>
                </div>
            </div>
            @endguest
        </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="how-it-works">
        <div class="container">
            <div class="section-title">
                <h2>How It Works</h2>
            </div>
            <div class="steps">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h3>Find an Apartment</h3>
                    <p>Browse apartments and filter by your preferences to find your perfect match.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h3>Contact the Owner</h3>
                    <p>Connect with your landlord to discuss details and arrange a visit.</p>
                </div>
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h3>Make It Yours</h3>
                    <p>Complete the deal with the owner and move to your new apartment.</p>
                </div>
            </div>
        </div>
    </section>

    



    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-container">
                <div class="footer-col">
                    <h3>PropertyPulse</h3>
                    <p>Your trusted partner in finding the perfect property. We connect buyers, sellers, and renters with the best real estate opportunities across India.</p>
                    <div class="social-links-footer">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#">Home</a></li>
                        <li><a href="#listings">Properties</a></li>
                        <li><a href="#agents">Agents</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Property Types</h3>
                    <ul class="footer-links">
                        <li><a href="#">Apartments</a></li>
                        <li><a href="#">Houses</a></li>
                        <li><a href="#">Villas</a></li>
                        <li><a href="#">Commercial</a></li>
                        <li><a href="#">Land</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Newsletter</h3>
                    <p>Subscribe to our newsletter for property updates and market insights.</p>
                    <div class="form-group">
                        <input type="email" placeholder="Your Email Address">
                        <button class="btn" style="width:100%; margin-top:10px;">Subscribe</button>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2025 <a href="https://www.devcubetech.com/" target="_blank">Dev Cube Tech</a>. All rights reserved.
            </div>
        </div>
    </footer>
    <script src="script.js"></script>
</body>
</html>