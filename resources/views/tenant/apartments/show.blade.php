<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $apartment->title }} - Daluyan</title>
  @vite(['resources/css/tenant/app.css', 'resources/js/tenant/app.js'])
  <style>
    :root {
      --primary: #016B61;
      --primary-light: #9ECFD4;
      --accent: #FF6B6B;
      --dark: #1A1A1A;
      --light: #F8FAFC;
      --gray: #64748B;
      --border: #E2E8F0;
    }
    
    body {
      background: #9ECFD4;
      font-family: 'Poppins', sans-serif;
    }
    
    .gallery-grid {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 10px;
      border-radius: 20px;
      overflow: hidden;
    }
    
    .gallery-right {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
    }
    
    .gallery-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 12px;
      transition: transform 0.3s ease;
    }
    
    .gallery-img:hover {
      transform: scale(1.02);
    }
    
    .sticky-book {
      position: sticky;
      top: 100px;
      background: white;
      border-radius: 20px;
      padding: 25px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.08);
      border: 1px solid var(--border);
    }
    
    .badge {
      display: inline-block;
      padding: 6px 12px;
      border-radius: 9999px;
      background: #dcfce7;
      color: #166534;
      font-size: 12px;
      font-weight: 600;
    }
    
    .amenities-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 12px;
    }
    
    .amenity-chip {
      background: #f3f4f6;
      border-radius: 9999px;
      padding: 10px 16px;
      font-size: 14px;
      color: #374151;
      display: inline-block;
      transition: all 0.3s ease;
    }
    
    .amenity-chip:hover {
      background: var(--primary-light);
      color: var(--dark);
    }
    
    .modal {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,.9);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 50;
      padding: 20px;
    }
    
    .modal.open {
      display: flex;
    }
    
    .modal-content {
      max-width: 90vw;
      max-height: 90vh;
      border-radius: 12px;
      overflow: hidden;
    }
    
    .property-highlights {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin: 30px 0;
      padding: 25px;
      background: white;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    
    .highlight-item {
      text-align: center;
      padding: 15px;
    }
    
    .highlight-icon {
      font-size: 2rem;
      color: var(--primary);
      margin-bottom: 10px;
    }
    
    .highlight-value {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--dark);
      display: block;
    }
    
    .highlight-label {
      color: var(--gray);
      font-size: 0.9rem;
    }
    
    .btn-primary {
      background: linear-gradient(135deg, var(--primary) 0%, #028476 100%);
      color: white;
      border: none;
      border-radius: 12px;
      padding: 16px 24px;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      width: 100%;
      text-align: center;
      text-decoration: none;
      display: block;
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(1, 107, 97, 0.3);
    }
    
    .rental-terms {
      background: white;
      border-radius: 16px;
      padding: 25px;
      margin: 25px 0;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    
    .terms-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-top: 15px;
    }
    
    .term-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 0;
    }
    
    .term-icon {
      color: var(--primary);
      font-size: 1.2rem;
      width: 24px;
    }
  </style>
</head>
<body>
  <header>
    <div class="container">
      <nav class="main-nav">
        <div class="logo-container">
          <a href="{{ route('tenant.browsing') }}" aria-label="Daluyan Home" style="display:inline-block;">
            <img class="nav-logo" src="{{ asset('images/common/logo1.png') }}" alt="Daluyan">
          </a>
        </div>
      </nav>
    </div>
  </header>
  
  <main>
    <div class="container" style="margin-top:16px;">
      <h1 style="font-size:32px;font-weight:700;color:#111827;margin-bottom:8px;line-height:1.2;">{{ $apartment->title }}</h1>
      <div style="display:flex;align-items:center;gap:10px;color:#6b7280;margin-bottom:16px;flex-wrap:wrap;">
        <span><i class="fas fa-map-marker-alt"></i> {{ $apartment->address }}</span>
        <span>•</span>
        <span><i class="fas fa-bed"></i> {{ $apartment->bedrooms }} beds • <i class="fas fa-bath"></i> {{ $apartment->bathrooms }} baths</span>
        @if($apartment->area)
          <span>•</span>
          <span><i class="fas fa-ruler-combined"></i> {{ $apartment->area }} sqm</span>
        @endif
        <span>•</span>
        <span class="badge">{{ ucfirst($apartment->status) }}</span>
      </div>

      <!-- Gallery -->
      @php
        $cover = $images->first();
        $others = $images->slice(1)->take(4);
      @endphp
      <div class="gallery-grid">
        <div>
          <img src="{{ $cover ? asset('storage/'.$cover->path) : 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?q=80&w=1600&auto=format&fit=crop' }}" class="gallery-img" alt="Cover" style="height: 400px;">
        </div>
        <div class="gallery-right">
          @foreach($others as $img)
            <img src="{{ asset('storage/'.$img->path) }}" class="gallery-img" alt="Photo" style="height: 195px;">
          @endforeach
          @for($i=$others->count(); $i<4; $i++)
            <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?q=80&w=1200&auto=format&fit=crop" class="gallery-img" alt="Placeholder" style="height: 195px;">
          @endfor
        </div>
      </div>
      <div style="margin-top:15px;text-align:center;">
        <button id="openGallery" class="btn-primary" style="width:auto;display:inline-flex;align-items:center;gap:8px;padding:12px 24px;">
          <i class="fas fa-images"></i>
          View All Photos
        </button>
      </div>

      <!-- Property Highlights -->
      <div class="property-highlights">
        <div class="highlight-item">
          <div class="highlight-icon">
            <i class="fas fa-money-bill-wave"></i>
          </div>
          <span class="highlight-value">₱{{ number_format((float) $apartment->price, 2) }}</span>
          <span class="highlight-label">Monthly Rent</span>
        </div>
        <div class="highlight-item">
          <div class="highlight-icon">
            <i class="fas fa-bed"></i>
          </div>
          <span class="highlight-value">{{ $apartment->bedrooms }}</span>
          <span class="highlight-label">Bedrooms</span>
        </div>
        <div class="highlight-item">
          <div class="highlight-icon">
            <i class="fas fa-bath"></i>
          </div>
          <span class="highlight-value">{{ $apartment->bathrooms }}</span>
          <span class="highlight-label">Bathrooms</span>
        </div>
        @if($apartment->area)
        <div class="highlight-item">
          <div class="highlight-icon">
            <i class="fas fa-vector-square"></i>
          </div>
          <span class="highlight-value">{{ $apartment->area }}</span>
          <span class="highlight-label">Square Meters</span>
        </div>
        @endif
      </div>

      <div style="display:grid;grid-template-columns:2fr 1fr;gap:30px;margin-top:24px;">
        <section>
          <div style="background:white;border-radius:16px;padding:30px;box-shadow:0 4px 12px rgba(0,0,0,0.05);margin-bottom:25px;">
            <h2 style="font-size:24px;font-weight:700;margin-bottom:16px;color:#111827;">About This Apartment</h2>
            <p style="color:#374151;line-height:1.7;font-size:16px;">{{ $apartment->description ?? 'A comfortable and well-maintained apartment available for long-term rental. Perfect for individuals, couples, or small families looking for a quality living space in a great location.' }}</p>
          </div>

          @if(!empty($apartment->amenities))
          <div style="background:white;border-radius:16px;padding:30px;box-shadow:0 4px 12px rgba(0,0,0,0.05);margin-bottom:25px;">
            <h3 style="font-size:20px;font-weight:700;margin-bottom:20px;color:#111827;">Apartment Features</h3>
            <div class="amenities-grid">
              @foreach($apartment->amenities as $amenity)
                <div class="amenity-chip">
                  <i class="fas fa-check" style="margin-right:8px;"></i>
                  {{ $amenity }}
                </div>
              @endforeach
            </div>
          </div>
          @endif

          <!-- Rental Terms -->
          <div class="rental-terms">
            <h3 style="font-size:20px;font-weight:700;margin-bottom:20px;color:#111827;">Rental Terms</h3>
            <div class="terms-grid">
              <div class="term-item">
                <i class="fas fa-file-contract term-icon"></i>
                <div>
                  <div style="font-weight:600;">Lease Duration</div>
                  <div style="color:var(--gray);font-size:14px;">12 months minimum</div>
                </div>
              </div>
              <div class="term-item">
                <i class="fas fa-hand-holding-usd term-icon"></i>
                <div>
                  <div style="font-weight:600;">Security Deposit</div>
                  <div style="color:var(--gray);font-size:14px;">2 months rent</div>
                </div>
              </div>
              <div class="term-item">
                <i class="fas fa-calendar-alt term-icon"></i>
                <div>
                  <div style="font-weight:600;">Availability</div>
                  <div style="color:var(--gray);font-size:14px;">Immediate</div>
                </div>
              </div>
              <div class="term-item">
                <i class="fas fa-users term-icon"></i>
                <div>
                  <div style="font-weight:600;">Occupancy</div>
                  <div style="color:var(--gray);font-size:14px;">Max {{ $apartment->bedrooms * 2 }} persons</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Location -->
          <div style="background:white;border-radius:16px;padding:30px;box-shadow:0 4px 12px rgba(0,0,0,0.05);margin-bottom:25px;">
            <h3 style="font-size:20px;font-weight:700;margin-bottom:16px;color:#111827;">Location</h3>
            <div style="border-radius:12px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
              <iframe
                width="100%" height="300" style="border:0" loading="lazy" allowfullscreen
                referrerpolicy="no-referrer-when-downgrade"
                src="https://www.google.com/maps?q={{ urlencode($apartment->address) }}&output=embed">
              </iframe>
            </div>
          </div>
            <!-- Reviews (read-only) -->
            @php
              $reviews = collect();
              $hasReviewsTable = false;
              try {
                  $hasReviewsTable = \Illuminate\Support\Facades\Schema::hasTable('apartment_reviews');
                  if ($hasReviewsTable) {
                      $reviews = \Illuminate\Support\Facades\DB::table('apartment_reviews')
                          ->where('apartment_id', $apartment->id)
                          ->orderBy('created_at', 'desc')
                          ->get();
                  }
              } catch (\Exception $e) {
                  $hasReviewsTable = false;
              }
            @endphp

            <div style="background:white;border-radius:16px;padding:30px;box-shadow:0 4px 12px rgba(0,0,0,0.05);margin-bottom:25px;">
              <h3 style="font-size:20px;font-weight:700;margin-bottom:16px;color:#111827;">Reviews</h3>
              @if(!$hasReviewsTable)
                <p style="color:#6b7280;margin:0 0 8px 0;">Reviews are not available. The reviews table hasn't been created on this environment. Run <code>php artisan migrate</code> to enable reviews.</p>
              @else
                @if($reviews->isEmpty())
                  <p style="color:#6b7280;margin:0;">No reviews yet for this apartment.</p>
                @else
                  @foreach($reviews as $r)
                    <div style="border-top:1px solid var(--border);padding-top:12px;margin-top:12px;">
                      <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
                        <div style="font-weight:600;color:#111827;">
                          @php
                            $name = 'Tenant #' . ($r->tenant_user_id ?? '');
                          @endphp
                          {{ $name }}
                        </div>
                        <div style="color:#f59e0b;font-size:18px;">
                          {!! str_repeat('★', (int)($r->rating ?? 0)) !!}{!! str_repeat('☆', 5 - ((int)($r->rating ?? 0))) !!}
                        </div>
                      </div>
                      <div style="color:#374151;margin-top:8px;">{{ $r->comment }}</div>
                      <div style="color:var(--gray);font-size:12px;margin-top:6px;">{{ \Carbon\Carbon::parse($r->created_at)->diffForHumans() }}</div>
                    </div>
                  @endforeach
                @endif
              @endif
            </div>
        </section>
        
        <aside>
          <div class="sticky-book">
            <div style="text-align:center;margin-bottom:20px;">
              <div style="font-size:28px;font-weight:700;color:#111827;">₱{{ number_format((float) $apartment->price, 2) }}</div>
              <div style="color:#6b7280;font-size:14px;">monthly rent</div>
            </div>
            
            <div style="background:#f8fafc;border-radius:12px;padding:20px;margin-bottom:20px;">
              <h4 style="margin:0 0 12px 0;font-size:16px;font-weight:600;">Quick Apply</h4>
              <p style="margin:0;color:#6b7280;font-size:14px;line-height:1.5;">
                Interested in this apartment? Submit your rental application today.
              </p>
            </div>
            
            <a href="{{ route('tenant.apartments.apply.create', $apartment) }}" class="btn-primary">
              <i class="fas fa-edit"></i>
              Apply for Rental
            </a>
            
              <div style="margin-top:20px;text-align:center;">
                <div style="display:flex;align-items:center;justify-content:center;gap:8px;color:#6b7280;font-size:12px;">
                  <i class="fas fa-shield-alt"></i>
                  <span>Secure application process</span>
                </div>
              </div>
          </div>
        </aside>
      </div>
    </div>
  </main>

  <!-- Reviews are shown in the main content area (read-only). Rating submission UI removed from this view. -->

  <!-- Simple gallery modal -->
  <div id="galleryModal" class="modal">
    <div class="modal-content">
      @foreach($images as $img)
        <img src="{{ asset('storage/'.$img->path) }}" style="max-width:100%;max-height:90vh;margin-bottom:8px;display:block;" alt="Photo" />
      @endforeach
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const galleryModal = document.getElementById('galleryModal');
      document.getElementById('openGallery')?.addEventListener('click', () => galleryModal.classList.add('open'));
      galleryModal?.addEventListener('click', () => galleryModal.classList.remove('open'));
    });
  </script>
</body>
</html>
