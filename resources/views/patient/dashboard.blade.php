<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meddybuddy+ | Patient Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/patientdashboard.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <div class="profile-section">
                <div class="profile-avatar">
                    <form id="profileImageForm" enctype="multipart/form-data">
                        @csrf
                        <label for="profileImageInput" class="profile-image-label">
                            @if(Auth::user()->patientProfile && Auth::user()->patientProfile->profile_image)
                                <img id="profileImagePreview" src="{{ asset('storage/' . Auth::user()->patientProfile->profile_image) }}" alt="Profile">
                            @else
                                <img id="profileImagePreview" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTLXuM2b4djVbMt63hftHrWFFMeQmccyytKlQ&s" alt="Default Profile">
                            @endif
                            <div class="camera-icon">📷</div>
                        </label>
                        <input type="file" id="profileImageInput" name="profile_image" accept="image/*" style="display:none;">
                    </form>
                </div>

                <div class="profile-info">
                    <h1>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h1>
                   <p>Patient ID: {{ Auth::user()->patientProfile->patient_identifier }}</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Personal Information -->
            <div class="card" id="personalInfoCard">
                <h2>📋 Personal Information
                    
                </h2>
                <div id="personalInfoView" class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Date of Birth</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse(Auth::user()->dob)->format('F j, Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Age</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse(Auth::user()->dob)->age }} years</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Gender</span>
                        <span class="info-value">{{ ucfirst(Auth::user()->gender) }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone</span>
                        <span class="info-value">(+91) {{ Auth::user()->phone }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value">{{ Auth::user()->email }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Address</span>
                        <span class="info-value">{{ Auth::user()->address ?? 'Not provided' }}</span>
                    </div>
                    <button class="edit-button" onclick="toggleEdit('personal')">Edit Information</button>
                </div>

                <!-- EDIT FORM (Initially Hidden) -->
                <div id="personalInfoEdit" style="display:none;">
                    <form id="personalInfoForm">
                        @csrf
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" value="{{ Auth::user()->first_name }}" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" value="{{ Auth::user()->last_name }}" required>
                        </div>
                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="date" name="dob" value="{{ Auth::user()->dob }}" required>
                        </div>
                        <div class="form-group">
                            <label>Gender</label>
                            <select name="gender" required>
                                <option value="male" {{ Auth::user()->gender == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ Auth::user()->gender == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ Auth::user()->gender == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" value="{{ Auth::user()->phone }}" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="{{ Auth::user()->email }}" required>
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address">{{ Auth::user()->address }}</textarea>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn-cancel" onclick="toggleEdit('personal')">Cancel</button>
                            <button type="submit" class="btn-save">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Health Information -->
            <div class="card" id="healthInfoCard">
                <h2>🏥 Health Information
                    
                </h2>

                <div id="healthInfoView">
                    @if(Auth::user()->patientProfile)
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Blood Type</span>
                                <span class="info-value">{{ Auth::user()->patientProfile->blood_group ?? 'Not set' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Height</span>
                                <span class="info-value">{{ Auth::user()->patientProfile->height ?? 'Not set' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Weight</span>
                                <span class="info-value">{{ Auth::user()->patientProfile->weight ?? 'Not set' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Emergency Contact</span>
                                <span class="info-value">{{ Auth::user()->patientProfile->emergency_contact ?? 'Not set' }}</span>
                            </div>
                        </div>

                        <div style="margin-top: 20px;">
                            <h3 style="color: #667eea; margin-bottom: 12px;">Current Medications</h3>
                            <div class="badges">
                                @if(Auth::user()->patientProfile->medical_history && isset(json_decode(Auth::user()->patientProfile->medical_history)->medications))
                                    @foreach(json_decode(Auth::user()->patientProfile->medical_history)->medications as $med)
                                        <span class="badge badge-medication">{{ $med }}</span>
                                    @endforeach
                                @else
                                    <span class="badge badge-medication">None added</span>
                                @endif
                            </div>
                        </div>

                        <div style="margin-top: 20px;">
                            <h3 style="color: #667eea; margin-bottom: 12px;">Allergies</h3>
                            <div class="badges">
                                @if(Auth::user()->patientProfile->medical_history && isset(json_decode(Auth::user()->patientProfile->medical_history)->allergies))
                                    @foreach(json_decode(Auth::user()->patientProfile->medical_history)->allergies as $allergy)
                                        <span class="badge badge-allergy">{{ $allergy }}</span>
                                    @endforeach
                                @else
                                    <span class="badge badge-allergy">None added</span>
                                @endif
                            </div>
                        </div>
                    @else
                        <p class="text-center">No health information added yet. Click "Edit Information" to add.</p>
                    @endif
                    <button class="edit-button" onclick="toggleEdit('health')">Edit Information</button>
                </div>

                <!-- EDIT FORM -->
                <div id="healthInfoEdit" style="display:none;">
                    <form id="healthInfoForm">
                        @csrf
                        <div class="form-group">
                            <label>Blood Group</label>
                            <select name="blood_group">
                                <option value="">Select</option>
                                <option value="A+" {{ (Auth::user()->patientProfile && Auth::user()->patientProfile->blood_group == 'A+') ? 'selected' : '' }}>A+</option>
                                <option value="A-" {{ (Auth::user()->patientProfile && Auth::user()->patientProfile->blood_group == 'A-') ? 'selected' : '' }}>A-</option>
                                <option value="B+" {{ (Auth::user()->patientProfile && Auth::user()->patientProfile->blood_group == 'B+') ? 'selected' : '' }}>B+</option>
                                <option value="B-" {{ (Auth::user()->patientProfile && Auth::user()->patientProfile->blood_group == 'B-') ? 'selected' : '' }}>B-</option>
                                <option value="O+" {{ (Auth::user()->patientProfile && Auth::user()->patientProfile->blood_group == 'O+') ? 'selected' : '' }}>O+</option>
                                <option value="O-" {{ (Auth::user()->patientProfile && Auth::user()->patientProfile->blood_group == 'O-') ? 'selected' : '' }}>O-</option>
                                <option value="AB+" {{ (Auth::user()->patientProfile && Auth::user()->patientProfile->blood_group == 'AB+') ? 'selected' : '' }}>AB+</option>
                                <option value="AB-" {{ (Auth::user()->patientProfile && Auth::user()->patientProfile->blood_group == 'AB-') ? 'selected' : '' }}>AB-</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Height (cm)</label>
                            <input type="number" name="height" value="{{ Auth::user()->patientProfile ? Auth::user()->patientProfile->height : '' }}" placeholder="e.g. 170">
                        </div>

                        <div class="form-group">
                            <label>Weight (kg)</label>
                            <input type="number" name="weight" value="{{ Auth::user()->patientProfile ? Auth::user()->patientProfile->weight : '' }}" placeholder="e.g. 57">
                        </div>

                        <div class="form-group">
                            <label>Emergency Contact</label>
                            <input type="text" name="emergency_contact" value="{{ Auth::user()->patientProfile ? Auth::user()->patientProfile->emergency_contact : '' }}" placeholder="Phone number">
                        </div>

                        <div class="form-group">
                            <label>Current Medications (comma separated)</label>
                            <input type="text" name="medications" placeholder="e.g. Lisinopril 10mg, Metformin 500mg"
                                value="@if(Auth::user()->patientProfile && Auth::user()->patientProfile->medical_history)
                                    {{ implode(', ', json_decode(Auth::user()->patientProfile->medical_history)->medications ?? []) }}
                                @endif">
                        </div>

                        <div class="form-group">
                            <label>Allergies (comma separated)</label>
                            <input type="text" name="allergies" placeholder="e.g. Penicillin, Shellfish"
                                value="@if(Auth::user()->patientProfile && Auth::user()->patientProfile->medical_history)
                                    {{ implode(', ', json_decode(Auth::user()->patientProfile->medical_history)->allergies ?? []) }}
                                @endif">
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-cancel" onclick="toggleEdit('health')">Cancel</button>
                            <button type="submit" class="btn-save">Save Health Info</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Appointments Section -->
        <div class="appointments-section">
            <h2 style="color: #667eea; margin-bottom: 20px;">📅 Appointments</h2>

            <div class="appointments-tabs">
                <button class="tab-button active" onclick="switchTab('upcoming')">Upcoming</button>
                <button class="tab-button" onclick="switchTab('history')">History</button>
            </div>

            <div id="upcoming" class="tab-content active">
                @forelse($upcomingAppointments as $appt)
                    <div class="appointment-item">
                        <div class="appointment-info">
                            <h4>{{ $appt->title }}</h4>
                            <p>Dr. {{ $appt->doctor->first_name ?? 'Unknown' }} {{ $appt->doctor->last_name ?? '' }} • {{ \Carbon\Carbon::parse($appt->date_time)->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                        <span class="appointment-status status-upcoming">Upcoming</span>
                    </div>
                @empty
                    <p>No upcoming appointments.</p>
                @endforelse
            </div>

            <div id="history" class="tab-content">
                @forelse($pastAppointments as $appt)
                    <div class="appointment-item">
                        <div class="appointment-info">
                            <h4>{{ $appt->title }}</h4>
                            <p>Dr. {{ $appt->doctor->first_name ?? 'Unknown' }} {{ $appt->doctor->last_name ?? '' }} • {{ \Carbon\Carbon::parse($appt->date_time)->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                        <span class="appointment-status status-completed">Completed</span>
                    </div>
                @empty
                    <p>No past appointments.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Inject Laravel variables into global window object -->
<script>
    window.App = {
        routes: {
            updatePersonal: "{{ route('patient.update.personal') }}",
            updateHealth: "{{ route('patient.update.health') }}",
            updateProfileImage: "{{ route('patient.update.image') }}"
        },
        csrfToken: "{{ csrf_token() }}"
    };
</script>


    <script src="{{ asset('js/patientdashboard.js') }}"></script>
</body>
</html>