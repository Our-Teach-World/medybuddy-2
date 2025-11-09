<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $user->first_name . ' ' . $user->last_name ?? 'Doctor Dashboard' }}</title>

    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Your existing CSS -->
    <link href="{{ asset('css/doctordashboard.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="main-content">
            <!-- Left Column -->
            <div class="left-column">

                <!-- Doctor Profile -->
                <div class="card doctor-profile">
                    <button class="edit-btn" onclick="toggleEdit('profile')" id="editProfileBtn">
                        <i class="fas fa-edit"></i> Edit Profile
                    </button>

                    <!-- Display Mode -->
                    <div id="profileDisplay">
                        <div class="profile-photo">
                            @if($doctorProfile->profile_image)
                                <img src="{{ asset('storage/'.$doctorProfile->profile_image) }}" alt="Doctor Photo">
                            @else
                                <i class="fas fa-user-md"></i>
                            @endif
                
                        </div>
                        <h1 class="doctor-name" id="doctorName">Dr. {{ $user->first_name }} {{ $user->last_name }}</h1>
                        <p class="specialization" id="doctorSpecialization">{{ $doctorProfile->specialization }}</p>

                        <div class="doctor-info">
                            <div class="info-item">
                                <div class="info-label">Qualifications</div>
                                <div class="info-value" id="doctorQualifications">{{ $doctorProfile->qualifications ?? 'N/A' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Experience</div>
                                <div class="info-value" id="doctorExperience">{{ $doctorProfile->experience }} Years</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">License No.</div>
                                <div class="info-value" id="doctorRegistration">{{ $doctorProfile->license ?? 'N/A' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Languages</div>
                                <div class="languages" id="doctorLanguages">
                                    @foreach($doctorProfile->languages ?? [] as $lang)
                                        <span class="language-tag">{{ $lang }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Mode -->
                    <div class="edit-form" id="profileEdit">
                        <div class="photo-upload">
                            <div class="profile-photo">
                                @if($doctorProfile->profile_image)
                                    <img src="{{ asset('storage/'.$doctorProfile->profile_image) }}" alt="Doctor Photo">
                                @else
                                    <i class="fas fa-user-md"></i>
                                @endif
                            </div>
                            <input type="file" id="photoUpload" name="profile_image" accept="image/*" style="display: none;">
                            <button class="upload-btn" onclick="document.getElementById('photoUpload').click()">
                                <i class="fas fa-camera"></i> Change Photo
                            </button>
                        </div>

                        <div class="form-group">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-input" id="editFirstName" name="first_name" value="{{ $user->first_name }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-input" id="editLastName"  name="last_name" value="{{ $user->last_name }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Specialization</label>
                            <select class="form-select" id="editSpecialization"  name="specialization">
                                @php
                                     $specializations = [
                                        'cardiology' => 'Cardiology',
                                        'dermatology' => 'Dermatology',
                                        'neurology' => 'Neurology',
                                        'pediatrics' => 'Pediatrics',
                                        'psychiatry' => 'Psychiatry',
                                        'general' => 'General Medicine'
                                         ];
                                @endphp
                                @foreach($specializations as $key => $label)
                                    <option value="{{ $key }}" 
                                        @if(old('specialization', $doctorProfile->specialization ?? $user->specialization) == $key) selected @endif>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Qualifications</label>
                            <input type="text" class="form-input" id="editQualifications" value="{{ $doctorProfile->qualifications ?? '' }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Years of Experience</label>
                            <select class="form-select" id="editExperience" name="experience">
                                @php
                                    $experiences = ['0-2' => '0-2 years','3-5' => '3-5 years','6-10' => '6-10 years','11-15' => '11-15 years','15+' => '15+ years'];
                                @endphp
                                @foreach($experiences as $value => $label)
                                    <option value="{{ $value }}" 
                                        @if(old('experience', $doctorProfile->experience ?? $user->experience) == $value) selected @endif>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <div class="form-group">
                            <label class="form-label">License Number</label>
                            <input type="text" class="form-input" id="editLicense" value="{{ $doctorProfile->license ?? '' }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Languages Spoken</label>
                            <div class="language-selector">
                                @php
                                    $allLanguages = ['English','Spanish','French','German','Italian','Portuguese','Hindi','Mandarin'];
                                    $selectedLanguages = $doctorProfile->languages ?? [];
                                @endphp
                                @foreach($allLanguages as $lang)
                                    <div class="language-option @if(in_array($lang, $selectedLanguages)) selected @endif" onclick="toggleLanguage(this)">{{ $lang }}</div>
                                @endforeach
                            </div>
                        </div>

                        <div style="margin-top: 1.5rem;">
                            <button class="save-btn" onclick="saveProfile({{ $user->id }})">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <button class="cancel-btn" onclick="cancelEdit('profile')">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Professional Details -->
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h2><i class="fas fa-user-graduate"></i> About {{ $user->first_name }}</h2>
                        <button class="edit-btn" onclick="toggleEdit('professional')" id="editProfessionalBtn">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>

                    <!-- Display Mode -->
                    <div id="professionalDisplay">
                        <p id="doctorBio">{{ $doctorProfile->bio ?? 'No bio available.' }}</p>

                        <h3>Specialized Treatments</h3>
                        <div class="treatments-list" id="treatmentsList">
                            @foreach($doctorProfile->treatments ?? [] as $treatment)
                                <div class="treatment-item">{{ $treatment }}</div>
                            @endforeach
                        </div>

                        <h3>Areas of Expertise</h3>
                        <div class="expertise-list" id="expertiseList">
                            @foreach($doctorProfile->expertise ?? [] as $exp)
                                <div class="expertise-item">{{ $exp }}</div>
                            @endforeach
                        </div>

                        <h3>Awards & Memberships</h3>
                        <ul id="awardsList" style="margin-top: 1rem; padding-left: 1.5rem;">
                            @foreach(explode("\n", $doctorProfile->awards ?? '') as $award)
                                <li>{{ $award }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Edit Mode -->
                    <div class="edit-form" id="professionalEdit">
                        <div class="form-group">
                            <label class="form-label">Biography</label>
                            <textarea class="form-textarea" id="editBio">{{ $doctorProfile->bio ?? '' }}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Specialized Treatments (comma separated)</label>
                            <input type="text" class="form-input" id="editTreatments" value="{{ implode(',', $doctorProfile->treatments ?? []) }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Areas of Expertise (comma separated)</label>
                            <input type="text" class="form-input" id="editExpertise" value="{{ implode(',', $doctorProfile->expertise ?? []) }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Awards & Memberships (one per line)</label>
                            <textarea class="form-textarea" id="editAwards">{{ $doctorProfile->awards ?? '' }}</textarea>
                        </div>

                        <div style="margin-top: 1.5rem;">
                            <button class="save-btn" onclick="saveProfessional({{ $user->id }})">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <button class="cancel-btn" onclick="cancelEdit('professional')">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Patient Reviews -->
                <div class="card">
                    <h2><i class="fas fa-star"></i> Patient Reviews</h2>
                    @forelse($reviews as $review)
                        <div class="review">
                            <div class="review-header">
                                <span class="reviewer-name">{{ $review->patient->first_name ?? 'Patient' }} {{ $review->patient->last_name ?? '' }}</span>
                                <div class="rating">
                                    @for($i=1;$i<=5;$i++)
                                        <i class="fas fa-star @if($i > $review->rating) far @endif"></i>
                                    @endfor
                                </div>
                            </div>
                            <p>{{ $review->comment }}</p>
                        </div>
                    @empty
                        <p>No reviews yet.</p>
                    @endforelse
                </div>

            </div>

            <!-- Right Column -->
            <div class="right-column">
                <!-- Contact & Availability -->
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h2><i class="fas fa-map-marker-alt"></i> Contact & Location</h2>
                        <button class="edit-btn" onclick="toggleEdit('contact')" id="editContactBtn">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>

                    <!-- Display Mode -->
                    <div id="contactDisplay">
                        <div class="contact-item">
                            <div class="contact-icon"><i class="fas fa-hospital"></i></div>
                            <div>
                                <strong id="hospitalName">{{ $doctorProfile->hospital_name ?? 'N/A' }}</strong><br>
                                <small id="hospitalAddress">{!! nl2br(e($doctorProfile->hospital_address ?? 'Not provided')) !!}</small>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon"><i class="fas fa-clock"></i></div>
                            <div>
                                <strong>Consultation Hours</strong><br>
                                <small id="consultationHours">{!! nl2br(e($doctorProfile->consultation_hours ?? 'Not set')) !!}</small>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon"><i class="fas fa-phone"></i></div>
                            <div>
                                <strong id="phoneNumber">{{ $user->phone ?? 'N/A' }}</strong><br>
                                <small>Call for appointments</small>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                            <div>
                                <strong id="emailAddress">{{ $user->email ?? 'N/A' }}</strong><br>
                                <small>Email for inquiries</small>
                            </div>
                        </div>
                        @if($doctorProfile->hospital_address)
                            <a href="https://maps.google.com/?q={{ urlencode($doctorProfile->hospital_address) }}" target="_blank"
                                style="color: #667eea; text-decoration: none; margin-top: 1rem; display: inline-block;">
                                <i class="fas fa-external-link-alt"></i> View on Google Maps
                            </a>
                        @endif
                    </div>

                    <!-- Edit Mode -->
                    <div class="edit-form" id="contactEdit">
                        <div class="form-group">
                            <label class="form-label">Hospital/Clinic Name</label>
                            <input type="text" class="form-input" id="editHospitalName" value="{{ $doctorProfile->hospital_name ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Address</label>
                            <textarea class="form-textarea" id="editAddress" style="min-height: 80px;">{{ $doctorProfile->hospital_address ?? '' }}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Consultation Hours</label>
                            <textarea class="form-textarea" id="editHours" style="min-height: 80px;">{{ $doctorProfile->consultation_hours ?? '' }}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-input" id="editPhone" value="{{ $user->phone ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-input" id="editEmail" value="{{ $user->email ?? '' }}">
                        </div>

                        <div style="margin-top: 1.5rem;">
                            <button class="save-btn" onclick="saveContact({{ $user->id }})">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <button class="cancel-btn" onclick="cancelEdit('contact')">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Appointment Booking -->
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h2><i class="fas fa-calendar-alt"></i> Appointment Settings</h2>
                        <button class="edit-btn" onclick="toggleEdit('appointment')" id="editAppointmentBtn">
                            <i class="fas fa-edit"></i> Edit Settings
                        </button>
                    </div>

                    <!-- Display Mode -->
                    <div id="appointmentDisplay">
                        <h3>Consultation Fees</h3>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                            <span>In-person: <strong id="inPersonFee">Rs.{{ $doctorProfile->in_person_fee ?? 0 }}</strong></span>
                            <span>Video call: <strong id="videoFee">Rs.{{ $doctorProfile->video_fee ?? 0 }}</strong></span>
                        </div>

                        <h3>Select Date</h3>
                        <div class="date-picker">
                            <input type="date" id="appointmentDate" min="{{ now()->format('Y-m-d') }}">
                        </div>

                        <h3>Available Time Slots</h3>
                        <div class="time-slots" id="timeSlots">
                            @if($doctorSchedule && $doctorSchedule->slots)
                                @foreach($doctorSchedule->slots ?? [] as $slot)
                                    <div class="time-slot" onclick="selectTimeSlot(this)">{{ $slot }}</div>
                                @endforeach
                            @else
                                <p>No slots set for today.</p>
                            @endif
                        </div>

                        <h3>Consultation Mode</h3>
                       <div class="consultation-modes">
                            @foreach($doctorProfile->consultation_modes ?? [] as $mode)
                                @if($mode === 'In-person')
                                    <div class="mode-option selected"><i class="fas fa-user-md"></i><br><small>In-person</small></div>
                                @elseif($mode === 'Video')
                                    <div class="mode-option selected"><i class="fas fa-video"></i><br><small>Video Call</small></div>
                                @elseif($mode === 'Phone')
                                    <div class="mode-option selected"><i class="fas fa-phone"></i><br><small>Phone Call</small></div>
                                @endif
                            @endforeach
                        </div>

                    </div>

                    <!-- Edit Mode -->
                    <div class="edit-form" id="appointmentEdit">
                        <div class="form-group">
                            <label class="form-label">In-Person Consultation Fee ($)</label>
                            <input type="number" class="form-input" id="editInPersonFee" value="{{ $doctorProfile->in_person_fee ?? 0 }}" min="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Video Call Consultation Fee ($)</label>
                            <input type="number" class="form-input" id="editVideoFee" value="{{ $doctorProfile->video_fee ?? 0 }}" min="0">
                        </div>
                        <!-- Consultation Modes (profile-level) -->
                        <div class="form-group">
                            <label class="form-label">Consultation Modes</label>
                            <div>
                                @php $modes = $doctorProfile->consultation_modes ?? []; @endphp
                                <label><input type="checkbox" class="mode-input" value="In-person" @if(in_array('In-person',$modes)) checked @endif> In-person</label>
                                <label><input type="checkbox" class="mode-input" value="Video" @if(in_array('Video',$modes)) checked @endif> Video</label>
                                <label><input type="checkbox" class="mode-input" value="Phone" @if(in_array('Phone',$modes)) checked @endif> Phone</label>
                            </div>
                        </div>



                            <!-- Per-date schedule generator -->
                            <div class="form-group">
                                <label class="form-label">Schedule for a Date</label>
                                <input type="date" id="scheduleDate" min="{{ now()->format('Y-m-d') }}">
                                <div style="display:flex; gap:.5rem; margin-top:.5rem;">
                                    <input type="time" id="scheduleStart">
                                    <input type="time" id="scheduleEnd">
                                    <select id="scheduleDuration">
                                        <option value="15">15 min</option>
                                        <option value="30" selected>30 min</option>
                                        <option value="45">45 min</option>
                                        <option value="60">60 min</option>
                                    </select>
                                    <button type="button" onclick="generateSlots()">Generate</button>
                                </div>

                                <div id="generatedSlots" style="margin-top: .75rem;">
                                    <!-- generated badges will appear here via JS -->
                                </div>

                                <div style="margin-top:.75rem;">
                                    <button type="button" class="save-btn" onclick="saveSchedule({{ $user->id }})">Save Schedule for Date</button>
                                </div>
                            </div>

                        <div style="margin-top: 1.5rem;">
                            <button class="save-btn" onclick="saveAppointment({{ $user->id }})">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <button class="cancel-btn" onclick="cancelEdit('appointment')">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Social Media & Share -->
                                    <!-- Social Media & Share -->
                    <div class="card">
                        <h2><i class="fas fa-share-alt"></i> Connect & Share</h2>
                        <h3>Follow {{ $user->first_name }}</h3>

                        <!-- Social Media Icons -->
                        <div class="social-links">
                            @if($doctorProfile->linkedin)
                                <a href="{{ $doctorProfile->linkedin }}" class="social-link" target="_blank"><i class="fab fa-linkedin"></i></a>
                            @endif
                            @if($doctorProfile->facebook)
                                <a href="{{ $doctorProfile->facebook }}" class="social-link" target="_blank"><i class="fab fa-facebook"></i></a>
                            @endif
                            @if($doctorProfile->twitter)
                                <a href="{{ $doctorProfile->twitter }}" class="social-link" target="_blank"><i class="fab fa-twitter"></i></a>
                            @endif
                            @if($doctorProfile->instagram)
                                <a href="{{ $doctorProfile->instagram }}" class="social-link" target="_blank"><i class="fab fa-instagram"></i></a>
                            @endif
                        </div>

                        <!-- Share Profile Button -->
                            <div class="share-btn-wrapper" style="margin-top: 1.5rem;">
                                <button class="share-btn" onclick="toggleShareOptions()">
                                    <i class="fas fa-share-alt"></i> Share Profile
                                </button>
                            </div>

                            <!-- Hidden Share Options -->
                            <div id="shareOptions" class="share-options" style="display: none; margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed #e2e8f0;">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url('/doctor/'.$user->id)) }}" target="_blank" class="share-option">
                                    <i class="fab fa-facebook-f"></i> Facebook
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url('/doctor/'.$user->id)) }}&text=Check out Dr. {{ urlencode($user->first_name) }}" target="_blank" class="share-option">
                                    <i class="fab fa-twitter"></i> Twitter
                                </a>
                                <a href="mailto:?subject=Check out Dr. {{ $user->first_name }}&body={{ urlencode(url('/doctor/'.$user->id)) }}" class="share-option">
                                    <i class="fas fa-envelope"></i> Email
                                </a>
                            </div>
                        </div>
                    </div>

            </div>
        </div>
    </div>

    <!-- External JS -->
     <script>window.DOCTOR_ID = {{ $user->id }};</script>
     <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ asset('js/doctordashboard.js') }}"></script>
</body>
</html>