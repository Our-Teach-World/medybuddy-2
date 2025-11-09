<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Find Doctors — MeddyBuddy+</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('css/doctor.css') }}">

</head>
<body>
  <div class="container">
    <header>
      <h1 style="margin:0">Find Doctors</h1>
      <p style="margin:0;color:#6b7280">Search doctors by name, specialty or hospital — book appointments easily.</p>

      <!-- Search Form (GET): keeps things simple -->
      <form method="GET" action="{{ route('doctor.index') }}" class="searchbar" role="search" id="searchForm">
        <label for="q" class="sr-only">Search doctors</label>
        <input id="q" name="q" type="text" placeholder="Search doctor or specialty (e.g., 'Dr. Kumar' or 'Cardiologist')" value="{{ request('q', '') }}" />
        <span class="search-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" width="20" height="20"><path d="M11 19a8 8 0 1 1 6.32-3.16l4.42 4.42-1.41 1.41-4.42-4.42A7.97 7.97 0 0 1 11 19z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
        </span>

        <!-- Hidden specialization preserved -->
        <input type="hidden" name="specialization" id="specializationInput" value="{{ request('specialization', $specialization ?? 'All') }}" />

        <button type="button" class="clear-icon" id="clearSearch" aria-label="Clear search">✕</button>
      </form>
    </header>

    <h2 class="section-title">Browse by Category</h2>
    <nav aria-label="Doctor categories">
      <div class="swiper" id="categorySwiper">
        <div class="swiper-wrapper" id="categoryWrapper">
          {{-- render categories via blade --}}
          <div class="swiper-slide">
            <button type="button" class="category-card {{ (request('specialization') === null || request('specialization') === 'All') ? 'active' : '' }}" data-category="All" aria-pressed="{{ (request('specialization') === null || request('specialization') === 'All') ? 'true' : 'false' }}">
              <span class="category-icon">+</span>
              <span class="category-label">All</span>
            </button>
          </div>
          @foreach($specializations as $spec)
            <div class="swiper-slide">
              <button type="button" class="category-card {{ (request('specialization') === $spec) ? 'active' : '' }}" data-category="{{ $spec }}" aria-pressed="{{ (request('specialization') === $spec) ? 'true' : 'false' }}">
                <span class="category-icon">+</span>
                <span class="category-label">{{ $spec }}</span>
              </button>
            </div>
          @endforeach
        </div>
      </div>
    </nav>

    <main>
      <section aria-live="polite" aria-busy="false">
        <div id="resultsWrapper">
          @include('doctor._results', ['doctors' => $doctors])
       </div>
      </section>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script src="{{ asset('js/doctor.js') }}"></script>

</body>
</html>
