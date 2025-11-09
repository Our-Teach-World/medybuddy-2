const { doctorId, scheduleGetUrl, appointmentStoreUrl, csrf } = window.doctorConfig;

function formatSlotToDisplay(slot) {
  const parts = slot.split(':');
  let h = parseInt(parts[0], 10);
  const m = parts[1] || '00';
  const ampm = h >= 12 ? 'PM' : 'AM';
  h = h % 12;
  if (h === 0) h = 12;
  return `${h}:${String(m).padStart(2, '0')} ${ampm}`;
}

function fetchSlots(date) {
  const el = document.getElementById('timeSlots');
  el.innerHTML = '<div class="empty">Loading slots…</div>';

  fetch(`${scheduleGetUrl}?doctor_id=${doctorId}&date=${date}`)
    .then(r => r.json())
    .then(data => {
      const slots = data.slots || [];
      if (!slots.length) {
        el.innerHTML = '<div class="empty">No slots available for this date.</div>';
        return;
      }
      el.innerHTML = '';
      slots.forEach(s => {
        const div = document.createElement('div');
        div.className = 'time-slot';
        div.dataset.slot = s;
        div.textContent = formatSlotToDisplay(s);
        div.addEventListener('click', function () {
          document.querySelectorAll('.time-slot').forEach(x => x.classList.remove('selected'));
          this.classList.add('selected');
        });
        el.appendChild(div);
      });
    })
    .catch(err => {
      el.innerHTML = '<div class="empty">Error loading slots.</div>';
      console.error(err);
    });
}

document.addEventListener('DOMContentLoaded', function () {
  const dateInput = document.getElementById('appointmentDate');
  const today = new Date().toISOString().split('T')[0];
  dateInput.value = today;

  fetchSlots(dateInput.value);

  dateInput.addEventListener('change', function () {
    fetchSlots(this.value);
  });

  document.getElementById('modes').addEventListener('click', function (e) {
    const t = e.target.closest('.mode-option');
    if (!t) return;
    document.querySelectorAll('.mode-option').forEach(x => x.classList.remove('selected'));
    t.classList.add('selected');
  });

  document.getElementById('bookNowBtn').addEventListener('click', function () {
    const btn = this;
    const errorBox = document.getElementById('errorBox');
    errorBox.textContent = ''; // clear old messages
    errorBox.style.color = 'red';

    const date = document.getElementById('appointmentDate').value;
    const selectedSlotEl = document.querySelector('.time-slot.selected');
    const modeEl = document.querySelector('.mode-option.selected');

    if (!date) {
      errorBox.textContent = 'Please select a date.';
      return;
    }
    if (!selectedSlotEl) {
      errorBox.textContent = 'Please select a time slot.';
      return;
    }
    if (!modeEl) {
      errorBox.textContent = 'Please select a consultation mode.';
      return;
    }

    const payload = {
      doctor_id: doctorId,
      date: date,
      time: selectedSlotEl.dataset.slot,
      consultation_mode: modeEl.dataset.mode
    };

    // 🔄 loader start
    btn.disabled = true;
    btn.textContent = 'Booking...';

    fetch(appointmentStoreUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    })
      .then(async res => {
        if (res.status === 422) {
          const err = await res.json();
          errorBox.textContent = err.message || 'Validation error';
          return;
        }
        if (res.status === 401) {
          window.location.href = "/authentication";
          return;
        }
        return res.json();
      })
      .then(json => {
        if (!json) return;
        if (json.success) {
          errorBox.style.color = 'green';
          errorBox.textContent = json.message || 'Appointment booked successfully!';
          const sel = document.querySelector('.time-slot.selected');
          if (sel) sel.remove();
        } else {
          errorBox.textContent = json.message || 'Could not book appointment.';
        }
      })
      .catch(err => {
        console.error(err);
        errorBox.textContent = 'Booking failed. Please try again.';
      })
      .finally(() => {
        // 🔄 loader stop
        btn.disabled = false;
        btn.textContent = 'Book Appointment';
      });
  });
});

function shareProfile() {
  if (navigator.share) {
    navigator.share({
      title: document.title,
      text: 'Check this doctor profile',
      url: window.location.href
    });
  } else {
    navigator.clipboard.writeText(window.location.href).then(() =>
      alert('Profile link copied to clipboard!')
    );
  }
}
