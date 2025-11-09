<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>{{ $doctor->first_name ? $doctor->first_name.' '.$doctor->last_name : $doctor->name }} — Profile</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/show.css') }}">

   
</head>
<body>
  <div class="container">
    <div class="main-content">
      <!-- Left Column -->
      <div class="left-column">
        <!-- Doctor Profile card -->
        <div class="card doctor-profile">
          <div class="profile-photo">
            @if($doctor->doctorProfile && $doctor->doctorProfile->profile_image)
              <img src="{{ asset('storage/'.$doctor->doctorProfile->profile_image) }}" alt="{{ $doctor->name }}" style="width:150px;height:150px;border-radius:50%;object-fit:cover;display:block" />
            @else
              <i class="fas fa-user-md"></i>
            @endif
          </div>

          <h1 class="doctor-name">{{ $doctor->first_name ? ($doctor->first_name . ' ' . ($doctor->last_name ?? '')) : $doctor->name }}</h1>
          <p class="specialization">{{ $doctor->doctorProfile->specialization ?? 'General Physician' }}</p>

          <div class="doctor-info">
            <div class="info-item">
              <div class="info-label">Qualifications</div>
              <div class="info-value">{{ $doctor->doctorProfile->qualifications ?? '—' }}</div>
            </div>

            <div class="info-item">
              <div class="info-label">Experience</div>
              <div class="info-value">{{ $doctor->doctorProfile->experience ? $doctor->doctorProfile->experience . ' Years' : '—' }}</div>
            </div>

            <div class="info-item">
              <div class="info-label">Registration No.</div>
              <div class="info-value">{{ $doctor->license ?? ($doctor->doctorProfile->license ?? '—') }}</div>
            </div>

            <div class="info-item">
              <div class="info-label">Languages</div>
              <div class="languages">
                @foreach($doctor->doctorProfile->languages ?? [] as $lang)
                  <span class="language-tag">{{ $lang }}</span>
                @endforeach
              </div>
            </div>
          </div>
        </div>

        <!-- Professional details -->
        <div class="card">
          <h2>About {{ $doctor->first_name ?? $doctor->name }}</h2>
          <p>{!! nl2br(e($doctor->doctorProfile->bio ?? '—')) !!}</p>

          <h3 style="margin-top:1rem">Specialized Treatments</h3>
          <div class="treatments-list">
            @foreach($doctor->doctorProfile->treatments ?? [] as $t)
              <div class="treatment-item">{{ $t }}</div>
            @endforeach
          </div>

          <h3 style="margin-top:1rem">Areas of Expertise</h3>
          <div class="expertise-list">
            @foreach($doctor->doctorProfile->expertise ?? [] as $e)
              <div class="expertise-item">{{ $e }}</div>
            @endforeach
          </div>

          <h3 style="margin-top:1rem">Awards & Memberships</h3>
          <ul style="margin-top:1rem;padding-left:1.5rem">
            @foreach(array_filter(explode("\n", $doctor->doctorProfile->awards ?? '')) as $award)
              <li>{{ $award }}</li>
            @endforeach
          </ul>
        </div>

        <!-- Reviews -->
        <div class="card">
          <h2>Patient Reviews</h2>
          @if($doctor->reviews && $doctor->reviews->count())
            @foreach($doctor->reviews as $review)
              <div class="review" style="border-bottom:1px solid #e2e8f0;padding-bottom:1rem;margin-bottom:1rem">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem">
                  <strong>{{ $review->patient ? ($review->patient->first_name ?? $review->patient->name) : 'Anonymous' }}</strong>
                  <div style="color:#fbbf24">
                    @for($i=0;$i<floor($review->rating);$i++) <i class="fas fa-star"></i> @endfor
                    @for($i=floor($review->rating);$i<5;$i++) <i class="far fa-star"></i> @endfor
                  </div>
                </div>
                <p>{{ $review->comment }}</p>
              </div>
            @endforeach
          @else
            <div class="empty">No reviews yet.</div>
          @endif
        </div>
      </div>

      <!-- Right Column -->
      <div class="right-column">
        <!-- Contact & Location -->
        <div class="card">
          <h2>Contact & Location</h2>
          <div class="contact-item">
            <div class="contact-icon"><i class="fas fa-hospital"></i></div>
            <div>
              <strong>{{ $doctor->doctorProfile->hospital_name ?? '—' }}</strong><br>
              <small>{{ $doctor->doctorProfile->hospital_address ?? '—' }}</small>
            </div>
          </div>

          @if($doctor->phone)
          <div class="contact-item">
            <div class="contact-icon"><i class="fas fa-phone"></i></div>
            <div><strong>{{ $doctor->phone }}</strong><br><small>Call for appointments</small></div>
          </div>
          @endif

          @if($doctor->email)
          <div class="contact-item">
            <div class="contact-icon"><i class="fas fa-envelope"></i></div>
            <div><strong>{{ $doctor->email }}</strong><br><small>Email for inquiries</small></div>
          </div>
          @endif

        </div>

        <!-- Booking Card -->
        <div class="card" id="book">
          <h2><i class="fas fa-calendar-alt"></i> Book Appointment</h2>

          <h3>Consultation Fees</h3>
          <div style="display:flex;justify-content:space-between;margin-bottom:1rem">
            <span>In-person: <strong>{{ $doctor->doctorProfile->in_person_fee ?? '—' }}</strong></span>
            <span>Video call: <strong>{{ $doctor->doctorProfile->video_fee ?? '—' }}</strong></span>
          </div>

          <h3>Select Date</h3>
          <div class="date-picker">
            <input type="date" id="appointmentDate" min="{{ \Carbon\Carbon::today()->toDateString() }}" />
          </div>

          <h3>Available Time Slots</h3>
          <div class="time-slots" id="timeSlots">
            <div class="empty">Select a date to view slots</div>
          </div>

          <h3>Consultation Mode</h3>
          <div class="consultation-modes" id="modes">
            @foreach($doctor->doctorProfile->consultation_modes ?? [] as $mode)
              <div class="mode-option" data-mode="{{ $mode }}">{{ $mode }}</div>
            @endforeach
          </div>
          <div id="errorBox" style="color:red; margin-bottom:10px;"></div>
          <button class="book-btn" id="bookNowBtn">Book Appointment</button>
        </div>

        <!-- Social -->
        <div class="card">
          <h2>Connect & Share</h2>
          <h3>Follow {{ $doctor->first_name ?? $doctor->name }}</h3>
          <div class="social-links">
            @if($doctor->doctorProfile->linkedin ?? false)
              <a href="{{ $doctor->doctorProfile->linkedin }}" class="social-link" target="_blank"><i class="fab fa-linkedin"></i></a>
            @endif
            @if($doctor->doctorProfile->twitter ?? false)
              <a href="{{ $doctor->doctorProfile->twitter }}" class="social-link" target="_blank"><i class="fab fa-twitter"></i></a>
            @endif
            @if($doctor->doctorProfile->facebook ?? false)
              <a href="{{ $doctor->doctorProfile->facebook }}" class="social-link" target="_blank"><i class="fab fa-facebook"></i></a>
            @endif
            @if($doctor->doctorProfile->instagram ?? false)
              <a href="{{ $doctor->doctorProfile->instagram }}" class="social-link" target="_blank"><i class="fab fa-instagram"></i></a>
            @endif
          </div>

          <button class="share-btn" onclick="shareProfile()" style="margin-top:1rem;padding:.75rem 1.5rem;border-radius:6px;border:2px solid #e2e8f0;background:#f1f5f9">Share Profile</button>
        </div>
      </div>
    </div>
  </div>

  <!-- JS: fetch schedules & booking -->
   <script>
  window.doctorConfig = {
    doctorId: {{ $doctor->id }},
    scheduleGetUrl: "{{ route('doctor.schedule.get') }}",
    appointmentStoreUrl: "{{ route('appointments.store') }}",
    csrf: "{{ csrf_token() }}"
  };
  </script>
  <script src="{{ asset('js/show.js') }}"></script>


</body>
</html>
