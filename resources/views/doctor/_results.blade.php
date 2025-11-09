<div id="grid" class="grid">
  @forelse($doctors as $doctor)
    @php $p = $doctor->doctorProfile; @endphp
    <article class="card" aria-label="{{ $doctor->name }}, {{ $p->specialization ?? '' }}">
      <img class="card-img" src="{{ $p && $p->profile_image ? asset('storage/'.$p->profile_image) : asset('images/default.jpg') }}" alt="{{ $doctor->name }}" loading="lazy" />
      <div class="card-body">
        <h3 class="doc-name">{{ $doctor->first_name ? ($doctor->first_name . ' ' . ($doctor->last_name ?? '')) : $doctor->name }}</h3>
        <p class="doc-spec">{{ $p->specialization ?? 'General Physician' }}</p>
        <p class="doc-exp">{{ $p && $p->experience ? $p->experience . ' years' : '' }}</p>
      </div>
      <div class="card-actions">
        <span class="badge">{{ $p->specialization ?? 'General' }}</span>
        <div style="display:flex;gap:8px">
          <a href="{{ route('doctor.show', $doctor->id) }}" class="btn secondary">View Profile</a>
          <a href="{{ route('doctor.show', $doctor->id) }}#book" class="btn">Book Now</a>
        </div>
      </div>
    </article>
  @empty
    <div class="empty">No doctors found. Try a different search or category.</div>
  @endforelse
</div>

{{-- Pagination --}}
<div class="pagination" aria-label="Pagination">
  @if($doctors->previousPageUrl())
    <a href="{{ $doctors->previousPageUrl() }}">Previous</a>
  @endif
  <span>Page {{ $doctors->currentPage() }} of {{ $doctors->lastPage() }}</span>
  @if($doctors->nextPageUrl())
    <a href="{{ $doctors->nextPageUrl() }}">Next</a>
  @endif
</div>
