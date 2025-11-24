<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" type="image/png" href="img/favicon-96x96.png" sizes="96x96" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Rubik:wght@600&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/293abf4b96.js" crossorigin="anonymous"></script>
  @vite(['resources/css/tenant/app.css', 'resources/js/tenant/app.js'])
  <title>Daluyan - Affordable Apartments</title>
</head>

<body>
  @php
    $approvedHost = false;
    if (Auth::check()) {
        $approvedHost = \App\Models\HostRequest::where('user_id', Auth::id())
            ->where('status', \App\Models\HostRequest::STATUS_APPROVED)
            ->exists();
    }
  @endphp
  <!-- Mobile Sticky Menu Start-->
  <div class="mobile-sticky-menu-container">
    <div class="mobile-sticky-menu-search">
      <i class="fa-solid fa-magnifying-glass fa-sm"></i>
      <p class="mobile-search-p">Start your search</p>
    </div>
    <ul class="mobile-nav-menu">
      <li class="mobile-nav-li">
        <a class="mobile-nav-link active">
          Homes
        </a>
      </li>
      <li class="mobile-nav-li">
        <a class="mobile-nav-link">
          Experiences
        </a>
      </li>
      <li class="mobile-nav-li">
        <a class="mobile-nav-link">
          Services
        </a>
      </li>
    </ul>
  </div>
  <!-- Mobile Sticky Menu End -->
  <header>
    <div class="container">
      <nav class="main-nav">
        <div class="logo-container">
          <a href="{{ route('tenant.browsing') }}" aria-label="Daluyan Home" style="display:inline-block;">
            <img class="nav-logo" src="{{ asset('images/common/logo1.png') }}" alt="Daluyan">
          </a>
          <a href="{{ route('tenant.browsing') }}" aria-label="Daluyan Home" style="display:inline-block;">
            <img class="mobile-logo" src="{{ asset('images/common/logo1.png') }}" alt="Daluyan">
          </a>
        </div>
        <div class="trip-planner-container">
          <input type="text" class="search-input" placeholder="Search destinations, dates, guests...">
          <div class="search-icon-container" aria-label="Search" title="Search">
            <i style="color: #E5E9C5;" class="fa-solid fa-magnifying-glass fa-sm"></i>
          </div>
        </div>
        <ul class="nav-menu-links">
          
          <li class="nav-icon-container">
            <a class="nav-link">
              <i class="fa-solid fa-bars"></i>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </header>
  <!-- Mobile Menu Fixed -->
  <div class="container">
    <div class="mobile-menu-fixed">
      <div class="mobile-menu-container">
        <ul class="mobile-menu">
          <li class="mobile-menu-li menu-flexbox">
            <a class="mobile-menu-link">
              <div class="circle">
                <i class="fa-solid fa-question fa-xs"></i>
              </div>
            </a>
            <a class="mobile-menu-link" style="font-weight: 500; font-family: 'Poppins', sans-serif;color: #000;" href="#">Help Center</a>
          </li>
          <li class="divider"></li>
          @unless($approvedHost)
            <li class="mobile-menu-li">
              <a class="mobile-menu-link menu-flexbox" href="{{ route('host.personal') }}">
                <div class="mobile-menu-text-container">
                  <span class="bold-txt"style="font-weight: 500; font-family: 'Poppins', sans-serif;color: #000;">Become a host</span><br style="font-weight: 500; font-family: 'Poppins', sans-serif;color: #000;">
                  <span style="font-weight: 400; font-family: 'Poppins', sans-serif;color: #000;">It's easy to start hosting and earn extra income</span> 
                </div>
                <div class="mobile-menu-img-container">
                </div>
              </a>
            </li>
          @endunless
          <li class="divider"></li>
          @auth
            @if(Auth::user()->is_admin)
              <li class="mobile-menu-li">
                <a class="mobile-menu-link" href="#">Refer a Host</a>
              </li>
              <li class="mobile-menu-li">
                <a class="mobile-menu-link" href="#">Find a co-host</a>
              </li>
            @endif
          @endauth
          <li class="divider"></li>
          @guest
            <li class="mobile-menu-li">
              <a class="mobile-menu-link" href="{{ route('login') }}">Login or signup</a>
            </li>
          @else
            <li class="mobile-menu-li">
              <a class="mobile-menu-link" style="font-weight: 500; font-family: 'Poppins', sans-serif;color: #000;" href="{{ route('profile.dashboard') }}">My Profile</a>
            </li>
            <li class="mobile-menu-li">
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="mobile-menu-link" style="font-weight: 500; font-family: 'Poppins', sans-serif; width:100%; text-align:left; background:none; border:none; color: #000;">Logout</button>
              </form>
            </li>
          @endguest
        </ul>
      </div>
    </div>
  </div>
  <!-- Mobile Menu Fixed End -->
  <!-- Mobile Bottom Menu Start -->
  <div class="mobile-bottom-menu-container">
    <ul class="mobile-bottom-menu">
      <li class="mobile-bottom-menu-li">
        <a class="mobile-bottom-menu-link active-bottom">
          <i class="fa-solid fa-magnifying-glass fa-lg"></i>
          <p class="bottom-menu-p">Explore</p>
        </a>
      </li>
      <li class="mobile-bottom-menu-li">
        <a class="mobile-bottom-menu-link">
          <i class="fa-regular fa-heart"></i>
          <p class="bottom-menu-p">Wishlists</p>
        </a>
      </li>
      <li class="mobile-bottom-menu-li">
        <a class="mobile-bottom-menu-link">
          <i class="fa-regular fa-user fa-lg"></i>
          <p class="bottom-menu-p">Log in</p>
        </a>
      </li>
    </ul>
  </div>
  <!-- Mobile Bottom Menu End -->
  <main>
    <div class="container">
      <section class="destinations">
        <div class="destinations-row">
          <div class="destinations-grid-container">
            @forelse($apartments as $apartment)
              @php
                $cover = $apartment->files->firstWhere('mime_type', 'like', 'image/%') ?? $apartment->files->first();
                $coverUrl = $cover ? asset('storage/'.$cover->path) : 'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=800&q=60';
              @endphp
              <a href="{{ route('tenant.apartments.show', $apartment) }}" class="destination-card" style="text-decoration:none;color:inherit;">
                <div class="destination-img-container">
                  <img class="destination-img" src="{{ $coverUrl }}" alt="{{ $apartment->title }}">
                  <p class="guest-favorite">Available</p>
                  <i class="fa-solid fa-heart fa-lg"></i>
                </div>
                <div class="desintation-text-container">
                  <h4 class="destination-text-header">{{ $apartment->title }}</h4>
                  <p class="desination-p">₱{{ number_format((float) $apartment->price, 2) }} • {{ $apartment->bedrooms }} bd • {{ $apartment->bathrooms }} ba @if($apartment->area) • {{ $apartment->area }} sqm @endif</p>
                </div>
              </a>
            @empty
              <div class="destination-card" style="grid-column: 1/-1; text-align:center; padding:2rem; background:#fff; border-radius:12px;">
                No apartments available yet.
              </div>
            @endforelse
          </div>
          @if(method_exists($apartments, 'links'))
            <div style="margin-top: 16px;">
              {{ $apartments->links() }}
            </div>
          @endif
        </div>
      </section>
    </div>
  </main>
  <footer>
  

</body>

</html>