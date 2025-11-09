// doctordashboard.js — updated (no Blade inside this file)

/* =====================
   Helper: get doctorId (robust)
   Tries multiple fallbacks so we can use this file as a static asset
   ===================== */
function getDoctorId() {
    // 1) explicit global set from Blade (recommended)
    if (window.DOCTOR_ID) return parseInt(window.DOCTOR_ID, 10);

    // 2) container with data attribute (if you add it)
    const container = document.getElementById('doctorDashboard');
    if (container && container.dataset && container.dataset.doctorId) {
        return parseInt(container.dataset.doctorId, 10);
    }

    // 3) try to parse from inline onclick attributes like saveSchedule(3)
    const inline = document.querySelector('button[onclick*="saveSchedule("]') || document.querySelector('button[onclick*="saveAppointment("]');
    if (inline) {
        const onclick = inline.getAttribute('onclick') || '';
        const m = onclick.match(/(?:saveSchedule|saveAppointment)\(\s*(\d+)\s*\)/);
        if (m) return parseInt(m[1], 10);
    }

    // fallback
    console.warn('doctorId not found in DOM. Some schedule features will be disabled until you provide doctor id (window.DOCTOR_ID or data-doctor-id).');
    return null;
}

/* =====================
   axios CSRF default
   ===================== */
document.addEventListener('DOMContentLoaded', function () {
    const tokenEl = document.querySelector('meta[name="csrf-token"]');
    if (tokenEl) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = tokenEl.getAttribute('content');
    }
});

/* =====================
   Toggle Edit & Display (unchanged)
   ===================== */
function toggleEdit(section) {
    const display = document.getElementById(section + 'Display');
    const edit = document.getElementById(section + 'Edit');
    if (display && edit) {
        display.style.display = 'none';
        edit.style.display = 'block';
    }
}

function cancelEdit(section) {
    const display = document.getElementById(section + 'Display');
    const edit = document.getElementById(section + 'Edit');
    if (display && edit) {
        display.style.display = 'block';
        edit.style.display = 'none';
    }
}

/* =====================
   Language Selector
   ===================== */
function toggleLanguage(el) {
    el.classList.toggle('selected');
}

/* =====================
   Time Slot Selection (patient/booking UI)
   ===================== */
function selectTimeSlot(el) {
    const slots = document.querySelectorAll('#timeSlots .time-slot');
    slots.forEach(slot => slot.classList.remove('selected'));
    el.classList.add('selected');
}

/* =====================
   Consultation Mode (static card click)
   ===================== */
function selectMode(el) {
    const modes = document.querySelectorAll('.consultation-modes .mode-option');
    modes.forEach(m => m.classList.remove('selected'));
    el.classList.add('selected');
}

/* =====================
   Add New Time Slot in Edit Mode
   ===================== */
function addTimeSlot() {
    const container = document.getElementById('timeSlotEditor');
    if (!container) return;

    const input = document.createElement('input');
    input.type = 'time';
    input.classList.add('time-input');
    container.appendChild(input);
}

/* =====================
   Profile Photo Preview (unchanged)
   ===================== */
document.addEventListener('DOMContentLoaded', function () {
    const photoInput = document.getElementById('photoUpload');
    const photoWrapper = document.querySelector('.profile-photo');

    if (photoInput && photoWrapper) {
        photoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    let img = photoWrapper.querySelector('img');
                    if (!img) {
                        img = document.createElement('img');
                        img.style.width = '100%';
                        img.style.height = '100%';
                        img.style.objectFit = 'cover';
                        img.style.borderRadius = '50%';
                        photoWrapper.innerHTML = '';
                        photoWrapper.appendChild(img);
                    }
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
});

/* =====================
   FETCH & REFRESH SLOTS (core)
   - fetchSlots: calls backend safely
   - refreshSlots: updates the #timeSlots DOM (no page reload)
   ===================== */
async function fetchSlots(doctorId, date) {
    try {
        const res = await axios.get('/doctor/schedule', {
            params: { doctor_id: doctorId, date: date }
        });
        // backend returns { slots: [...], source: 'schedule'|'default' }
        return Array.isArray(res.data.slots) ? res.data.slots : [];
    } catch (err) {
        console.error('fetchSlots error:', err.response?.data || err.message || err);
        return [];
    }
}

async function refreshSlots(doctorId, date) {
    const container = document.getElementById('timeSlots');
    if (!container) return;

    container.innerHTML = '<p>Loading slots...</p>';
    const slots = await fetchSlots(doctorId, date);

    // Replace content
    if (slots && slots.length > 0) {
        container.innerHTML = '';
        slots.forEach(s => {
            const div = document.createElement('div');
            div.className = 'time-slot';
            div.textContent = s;
            div.addEventListener('click', () => selectTimeSlot(div));
            container.appendChild(div);
        });
    } else {
        container.innerHTML = '<p>No slots set for this date.</p>';
    }
}

/* =====================
   Initialize appointmentDate default = today + auto-fetch
   This block avoids blade interpolation; it discovers doctor id at runtime.
   ===================== */
document.addEventListener('DOMContentLoaded', async function () {
    const dateInput = document.getElementById('appointmentDate');
    const doctorId = getDoctorId();
    const today = new Date().toISOString().split('T')[0];

    if (dateInput) {
        dateInput.setAttribute('min', today);
        if (!dateInput.value) dateInput.value = today;

        // initial load
        if (doctorId) {
            await refreshSlots(doctorId, dateInput.value);
        }

        // change listener
        dateInput.addEventListener('change', async function () {
            if (!doctorId) return;
            await refreshSlots(doctorId, this.value);
        });
    }

    // Also ensure scheduleDate min
    const scheduleDate = document.getElementById('scheduleDate');
    if (scheduleDate) scheduleDate.setAttribute('min', today);
});

/* =====================
   generateSlots — now stores both display ranges and start-times (startTimes used to send to backend)
   ===================== */
function generateSlots() {
    const start = document.getElementById('scheduleStart').value;
    const end = document.getElementById('scheduleEnd').value;
    const duration = parseInt(document.getElementById('scheduleDuration').value, 10);
    const container = document.getElementById('generatedSlots');
    container.innerHTML = '';

    if (!start || !end) {
        alert('Please select start and end times');
        return;
    }

    let current = new Date(`1970-01-01T${start}:00`);
    const endTime = new Date(`1970-01-01T${end}:00`);
    const slotsFull = [];
    const slotsStart = [];

    while ((new Date(current.getTime() + duration * 60000)) <= endTime) {
        const next = new Date(current.getTime() + duration * 60000);
        const pad = v => v.toString().padStart(2, '0');
        const startStr = `${pad(current.getHours())}:${pad(current.getMinutes())}`;
        const nextStr = `${pad(next.getHours())}:${pad(next.getMinutes())}`;
        const slotRange = `${startStr}-${nextStr}`;
        slotsFull.push(slotRange);
        slotsStart.push(startStr);
        current = next;
    }

    // store both representations
    container.dataset.slotsFull = JSON.stringify(slotsFull);
    container.dataset.slotsStarts = JSON.stringify(slotsStart);

    container.innerHTML = slotsFull.map(s => `<span class="slot-badge" style="margin-right:.5rem;">${s}</span>`).join('');
}

/* =====================
   saveSchedule — sends start-times to backend to match controller checks
   After success: close edit, refresh static slots (if the saved date is currently selected)
   ===================== */
function saveSchedule(doctorId) {
    doctorId = doctorId || getDoctorId();
    if (!doctorId) {
        alert('Doctor ID not found. Cannot save schedule.');
        return;
    }

    const date = document.getElementById('scheduleDate').value;
    const container = document.getElementById('generatedSlots');
    // Prefer explicit start times if available
    let slots = [];

    if (container && container.dataset.slotsStarts) {
        try {
            slots = JSON.parse(container.dataset.slotsStarts) || [];
        } catch (e) {
            // fallback: try parsing full ranges and extract start
            try {
                const full = JSON.parse(container.dataset.slotsFull || '[]');
                slots = full.map(s => s.split('-')[0]);
            } catch (ex) {
                slots = [];
            }
        }
    } else if (container && container.dataset.slotsFull) {
        try {
            const full = JSON.parse(container.dataset.slotsFull);
            slots = full.map(s => s.split('-')[0]);
        } catch (e) {
            slots = [];
        }
    }

    if (!date || !slots.length) {
        alert('Select a date and generate slots first.');
        return;
    }

    axios.post('/doctor/schedule/save', {
        date: date,
        slots: slots
    }, {
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(async res => {
            alert('✅ Schedule saved.');
            // close edit mode and refresh static slots for currently selected date (if matches)
            cancelEdit('appointment');

            const appointmentDateEl = document.getElementById('appointmentDate');
            if (appointmentDateEl && appointmentDateEl.value === date) {
                await refreshSlots(doctorId, date);
            }
            // else we keep the currently selected appointmentDate unchanged (doctor can manually switch)
        })
        .catch(err => {
            console.error('Save schedule error:', err.response?.data || err);
            alert(err.response?.data?.message || 'Failed to save schedule.');
        });
}

/* =====================
   Update consultation modes display in static card
   ===================== */
function updateConsultationModesDisplay(modes = []) {
    const container = document.querySelector('#appointmentDisplay .consultation-modes');
    if (!container) return;

    // Map to icons + labels
    const map = {
        'In-person': { icon: 'fa-user-md', label: 'In-person' },
        'Video': { icon: 'fa-video', label: 'Video Call' },
        'Phone': { icon: 'fa-phone', label: 'Phone Call' }
    };

    container.innerHTML = ''; // clear

    if (!Array.isArray(modes) || modes.length === 0) {
        container.innerHTML = '<p>No consultation modes selected.</p>';
        return;
    }

    modes.forEach(mode => {
        if (!map[mode]) return;
        const el = document.createElement('div');
        el.className = 'mode-option selected';
        el.innerHTML = `<i class="fas ${map[mode].icon}"></i><br><small>${map[mode].label}</small>`;
        container.appendChild(el);
    });
}

/* =====================
   saveAppointment — profile-level settings (fees, consultation modes, default time slots)
   After success: update static card (no page reload)
   ===================== */
function saveAppointment(doctorId) {
    doctorId = doctorId || getDoctorId();
    if (!doctorId) {
        alert('Doctor ID not found. Cannot save appointment settings.');
        return;
    }

    const inPersonFee = document.getElementById('editInPersonFee')?.value || 0;
    const videoFee = document.getElementById('editVideoFee')?.value || 0;

    const timeInputs = document.querySelectorAll('#timeSlotEditor .time-input');
    const timeSlots = Array.from(timeInputs).map(input => input.value).filter(v => v);

    const selectedModes = Array.from(document.querySelectorAll('.mode-input:checked'))
        .map(el => el.value);

    axios.post(`/doctor/appointment/${doctorId}/update`, {
        in_person_fee: inPersonFee,
        video_fee: videoFee,
        time_slots: timeSlots,
        consultation_modes: selectedModes
    }, {
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(async res => {
            alert('✅ Appointment settings updated!');
            // update static UI elements
            const inPersonEl = document.getElementById('inPersonFee');
            const videoEl = document.getElementById('videoFee');
            if (inPersonEl) inPersonEl.textContent = `$${inPersonFee || 0}`;
            if (videoEl) videoEl.textContent = `$${videoFee || 0}`;

            updateConsultationModesDisplay(selectedModes);

            // Close edit form
            cancelEdit('appointment');

            // Refresh currently selected date's slots (because server stored defaults in profile)
            const appointmentDateEl = document.getElementById('appointmentDate');
            if (appointmentDateEl && appointmentDateEl.value) {
                await refreshSlots(doctorId, appointmentDateEl.value);
            }
        })
        .catch(err => {
            console.error('Appointment Update Error:', err.response?.data || err.message || err);
            alert('❌ Failed to update appointment settings.');
        });
}

/* =====================
   Other save functions left mostly unchanged (profile/professional/contact) — keep reload for now
   If you want no-reload UX for these as well, we can update them similarly.
   ===================== */

function saveProfile(doctorId) {
    const firstName = document.getElementById('editFirstName')?.value || '';
    const lastName = document.getElementById('editLastName')?.value || '';
    const specialization = document.getElementById('editSpecialization')?.value || '';
    const qualifications = document.getElementById('editQualifications')?.value || '';
    const experience = document.getElementById('editExperience')?.value || '';
    const license = document.getElementById('editLicense')?.value || '';

    const selectedLanguages = Array.from(document.querySelectorAll('.language-selector .selected'))
        .map(el => el.textContent.trim());

    const formData = new FormData();
    formData.append('first_name', firstName);
    formData.append('last_name', lastName);
    formData.append('specialization', specialization);
    formData.append('qualifications', qualifications);
    formData.append('experience', experience);
    formData.append('license', license);
    formData.append('languages', JSON.stringify(selectedLanguages));

    const photoInputEl = document.getElementById('photoUpload');
    if (photoInputEl && photoInputEl.files.length > 0) {
        formData.append('profile_image', photoInputEl.files[0]);
    }

    axios.post(`/doctor/profile/${doctorId}/update`, formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
        .then(res => {
            alert('✅ Profile updated successfully!');
            location.reload(); // keep for now
        })
        .catch(err => {
            console.error('Profile Update Error:', err.response?.data || err.message);
            alert('❌ Failed to update profile. Check console for details.');
        });
}

function saveProfessional(doctorId) {
    const bio = document.getElementById('editBio')?.value || '';
    const treatments = document.getElementById('editTreatments')?.value.split(',').map(t => t.trim()).filter(t => t);
    const expertise = document.getElementById('editExpertise')?.value.split(',').map(e => e.trim()).filter(e => e);
    const awards = document.getElementById('editAwards')?.value || '';

    axios.post(`/doctor/professional/${doctorId}/update`, {
        bio,
        treatments: JSON.stringify(treatments),
        expertise: JSON.stringify(expertise),
        awards
    }, {
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(res => {
            alert('✅ Professional details updated!');
            location.reload();
        })
        .catch(err => {
            console.error('Professional Update Error:', err.response?.data || err.message);
            alert('❌ Failed to update professional details.');
        });
}

function saveContact(doctorId) {
    const hospital = document.getElementById('editHospitalName')?.value || '';
    const address = document.getElementById('editAddress')?.value || '';
    const hours = document.getElementById('editHours')?.value || '';
    const phone = document.getElementById('editPhone')?.value || '';
    const email = document.getElementById('editEmail')?.value || '';

    axios.post(`/doctor/contact/${doctorId}/update`, {
        hospital_name: hospital,
        hospital_address: address,
        consultation_hours: hours,
        phone,
        email
    }, {
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(res => {
            alert('✅ Contact information updated!');
            location.reload();
        })
        .catch(err => {
            console.error('Contact Update Error:', err.response?.data || err.message);
            alert('❌ Failed to update contact info.');
        });
}

/* =====================
   toggleShareOptions (unchanged)
   ===================== */
function toggleShareOptions() {
    const shareOptions = document.getElementById('shareOptions');
    if (!shareOptions) return;
    if (shareOptions.style.display === 'none' || shareOptions.style.display === '') {
        shareOptions.style.display = 'flex';
    } else {
        shareOptions.style.display = 'none';
    }
}

/* =====================
   Example legacy fetchSlots wrapper removed (we use the robust one above)
   If you have other code calling fetchSlots(...) it's compatible.
   ===================== */
document.addEventListener("DOMContentLoaded", () => {
    const photoUpload = document.getElementById("photoUpload");
    const profilePhoto = document.querySelector(".profile-photo img, .profile-photo i");

    if (photoUpload) {
        photoUpload.addEventListener("change", function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    // Agar <i> icon laga ho to usko hata ke <img> lagana
                    if (profilePhoto.tagName.toLowerCase() === "i") {
                        const img = document.createElement("img");
                        img.src = e.target.result;
                        img.alt = "Doctor Photo Preview";
                        profilePhoto.parentNode.replaceChild(img, profilePhoto);
                    } else {
                        profilePhoto.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
