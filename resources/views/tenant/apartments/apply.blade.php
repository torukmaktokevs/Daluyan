<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Apply for {{ $apartment->title }} - Daluyan</title>
  @vite(['resources/css/tenant/app.css'])
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
      background: linear-gradient(135deg, #9ECFD4 0%, #E5E9C5 100%);
      min-height: 100vh;
      font-family: 'Poppins', sans-serif;
    }
    
    .apply-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }
    
    .apply-header {
      text-align: center;
      margin-bottom: 30px;
    }
    
    .apply-title {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--dark);
      margin-bottom: 8px;
    }
    
    .apply-subtitle {
      color: var(--gray);
      font-size: 1.1rem;
    }
    
    .apply-grid {
      display: grid;
      grid-template-columns: 1.2fr 0.8fr;
      gap: 30px;
      align-items: start;
    }
    
    .step-card {
      background: white;
      border-radius: 20px;
      padding: 30px;
      margin-bottom: 25px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.05);
      border: 1px solid var(--border);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .step-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }
    
    .step-header {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-bottom: 20px;
    }
    
    .step-number {
      background: var(--primary);
      color: white;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 1.2rem;
    }
    
    .step-title {
      font-size: 1.4rem;
      font-weight: 600;
      color: var(--dark);
      margin: 0;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: var(--dark);
    }
    
    .form-input, .form-textarea {
      width: 100%;
      padding: 15px;
      border: 2px solid var(--border);
      border-radius: 12px;
      font-size: 1rem;
      transition: all 0.3s ease;
      font-family: 'Poppins', sans-serif;
    }
    
    .form-input:focus, .form-textarea:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(1, 107, 97, 0.1);
    }
    
    .form-textarea {
      resize: vertical;
      min-height: 120px;
    }
    
    .summary-card {
      background: white;
      border-radius: 20px;
      padding: 25px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.08);
      border: 1px solid var(--border);
      position: sticky;
      top: 100px;
    }
    
    .property-card {
      display: flex;
      gap: 15px;
      margin-bottom: 20px;
    }
    
    .property-image {
      width: 100px;
      height: 100px;
      border-radius: 12px;
      object-fit: cover;
      flex-shrink: 0;
    }
    
    .property-info h3 {
      margin: 0 0 8px 0;
      font-size: 1.2rem;
      font-weight: 600;
    }
    
    .property-location {
      color: var(--gray);
      font-size: 0.9rem;
      margin-bottom: 5px;
    }
    
    .property-price {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--primary);
    }
    
    .features-list {
      list-style: none;
      padding: 0;
      margin: 20px 0;
    }
    
    .features-list li {
      padding: 8px 0;
      display: flex;
      align-items: center;
      gap: 10px;
      color: var(--gray);
    }
    
    .features-list li i {
      color: var(--primary);
      width: 20px;
    }
    
    .benefit-card {
      background: linear-gradient(135deg, var(--primary-light) 0%, #E5E9C5 100%);
      padding: 20px;
      border-radius: 12px;
      margin-top: 20px;
    }
    
    .benefit-card h4 {
      margin: 0 0 10px 0;
      color: var(--dark);
    }
    
    .benefit-card p {
      margin: 0;
      color: var(--gray);
      font-size: 0.9rem;
    }
    
    .btn-primary {
      background: linear-gradient(135deg, var(--primary) 0%, #028476 100%);
      color: white;
      border: none;
      border-radius: 12px;
      padding: 18px 30px;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(1, 107, 97, 0.3);
    }
    
    .error-message {
      color: #DC2626;
      font-size: 0.9rem;
      margin-top: 5px;
      display: flex;
      align-items: center;
      gap: 5px;
    }
    
    .rental-details {
      background: #f8fafc;
      border-radius: 12px;
      padding: 20px;
      margin: 20px 0;
    }
    
    .detail-item {
      display: flex;
      justify-content: between;
      align-items: center;
      padding: 10px 0;
      border-bottom: 1px solid var(--border);
    }
    
    .detail-item:last-child {
      border-bottom: none;
    }
    
    .detail-label {
      color: var(--gray);
      font-weight: 500;
    }
    
    .detail-value {
      font-weight: 600;
      color: var(--dark);
    }
    
    @media (max-width: 768px) {
      .apply-grid {
        grid-template-columns: 1fr;
      }
      
      .apply-title {
        font-size: 2rem;
      }
      
      .step-card {
        padding: 20px;
      }
    }
  </style>
</head>
<body>
  <header style="background: #70B2B2; backdrop-filter: blur(10px); border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 100;">
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
    <div class="apply-container">
      <div class="apply-header">
        <h1 class="apply-title">Apartment Rental Application</h1>
        <p class="apply-subtitle">Complete your rental application in 2 simple steps</p>
      </div>

      <div class="apply-grid">
        <form method="POST" action="{{ route('tenant.apartments.apply.store', $apartment) }}">
          @csrf

          <!-- Step 1: Message -->
          <div class="step-card">
            <div class="step-header">
              <div class="step-number">1</div>
              <h2 class="step-title">Tell Us About Yourself</h2>
            </div>
            <div class="form-group">
              <label for="message" class="form-label">Application Message</label>
              <textarea id="message" name="message" class="form-textarea" required minlength="5" maxlength="2000" placeholder="Hello, I'm interested in renting this apartment. Here's a bit about myself and my rental history...">{{ old('message') }}</textarea>
              @error('message')
                <div class="error-message">
                  <i class="fas fa-exclamation-circle"></i>
                  {{ $message }}
                </div>
              @enderror
            </div>
            
            <div class="rental-details">
              <h4 style="margin: 0 0 15px 0; color: var(--dark);">Rental Preferences</h4>
              <div class="detail-item">
                <span class="detail-label">Lease Duration</span>
                <span class="detail-value">12 Months</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Move-in Date</span>
                <span class="detail-value">Flexible</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Rental Type</span>
                <span class="detail-value">Long Term</span>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Preferred Dates (optional)</label>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                  <label for="visit_date" class="form-label">Preferred Visit Date</label>
                  <input type="date" id="visit_date" name="visit_date" value="{{ old('visit_date') }}" class="form-input" />
                  @error('visit_date')
                    <div class="error-message"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>
                  @enderror
                </div>
                <div>
                  <label for="movein_date" class="form-label">Estimated Move-in Date</label>
                  <input type="date" id="movein_date" name="movein_date" value="{{ old('movein_date') }}" class="form-input" />
                  @error('movein_date')
                    <div class="error-message"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>
          </div>

          <!-- Step 2: Submit -->
          <div class="step-card">
            <div class="step-header">
              <div class="step-number">2</div>
              <h2 class="step-title">Review & Submit Application</h2>
            </div>
            <p style="color: var(--gray); margin-bottom: 20px; line-height: 1.6;">
              We'll send your rental application directly to the property manager. You'll be notified once they review your application and schedule a viewing if interested.
            </p>
            <button type="submit" class="btn-primary">
              <i class="fas fa-paper-plane"></i>
              Submit Rental Application
            </button>
          </div>
        </form>

        <!-- Summary Sidebar -->
        <aside>
          <div class="summary-card">
            <div class="property-card">
              <img class="property-image" src="{{ optional($apartment->files->first())->path ? asset('storage/'.optional($apartment->files->first())->path) : 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=400&q=60' }}" alt="{{ $apartment->title }}" />
              <div>
                <h3>{{ $apartment->title }}</h3>
                <p class="property-location">{{ $apartment->address }}</p>
                <div class="property-price">₱{{ number_format((float)$apartment->price, 2) }} <span style="font-size: 0.9rem; color: var(--gray);">/ month</span></div>
              </div>
            </div>
            
            <div class="features-list">
              <li><i class="fas fa-bed"></i> {{ $apartment->bedrooms }} Bedrooms</li>
              <li><i class="fas fa-bath"></i> {{ $apartment->bathrooms }} Bathrooms</li>
              @if($apartment->area)
                <li><i class="fas fa-ruler-combined"></i> {{ $apartment->area }} sqm</li>
              @endif
              <li><i class="fas fa-home"></i> Apartment Unit</li>
            </div>
            
            <div class="benefit-card">
              <h4><i class="fas fa-shield-alt"></i> Secure Application</h4>
              <p>Your information is protected and only shared with the property manager after you apply.</p>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </main>
</body>
</html>
