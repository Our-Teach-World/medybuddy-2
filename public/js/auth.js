document.addEventListener('DOMContentLoaded', function () {
    // Elements
    const modeButton = document.getElementById('modeButton');
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const switchToRegister = document.getElementById('switchToRegister');
    const switchToLogin = document.getElementById('switchToLogin');

    let currentMode = 'login';
    let isAnimating = false;

    // Mode switching
    function switchMode(targetMode) {
        if (isAnimating || currentMode === targetMode) return;
        isAnimating = true;

        const currentForm = currentMode === 'login' ? loginForm : registerForm;
        const targetForm = targetMode === 'login' ? loginForm : registerForm;

        currentForm.classList.add('slide-out');

        setTimeout(() => {
            currentForm.classList.remove('active', 'slide-out');
            targetForm.classList.add('active');
            updateModeButton(targetMode);
            resetFormAnimations(targetForm);
            currentMode = targetMode;
            isAnimating = false;
        }, 300);
    }

    function updateModeButton(mode) {
        modeButton.innerHTML = mode === 'login'
            ? '<i class="fas fa-user"></i> Login'
            : '<i class="fas fa-stethoscope"></i> Register';
    }

    function resetFormAnimations(container) {
        const animatedElements = container.querySelectorAll('.animate-fadeInUp');
        animatedElements.forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.animationDelay = `${(index + 1) * 0.1}s`;
            el.offsetHeight;
            el.style.animation = 'fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards';
        });
    }

    // Event listeners
    modeButton.addEventListener('click', () => {
        const targetMode = currentMode === 'login' ? 'register' : 'login';
        switchMode(targetMode);
    });

    switchToRegister.addEventListener('click', e => {
        e.preventDefault();
        switchMode('register');
    });

    switchToLogin.addEventListener('click', e => {
        e.preventDefault();
        switchMode('login');
    });

    // Doctor/Patient toggle (only visual, backend handles required fields)
    const userTypeSelect = document.getElementById('user-type');
    const doctorFields = document.getElementById('doctor-fields');
    const patientFields = document.getElementById('patient-fields');

    userTypeSelect.addEventListener('change', function () {
        doctorFields.classList.remove('conditional-enter-active');
        patientFields.classList.remove('conditional-enter-active');

        // disable sabse pehle sabhi inputs
        doctorFields.querySelectorAll('input, select').forEach(el => el.disabled = true);
        patientFields.querySelectorAll('input, select').forEach(el => el.disabled = true);

        setTimeout(() => {
            if (this.value === 'doctor') {
                doctorFields.classList.add('conditional-enter-active');
                doctorFields.querySelectorAll('input, select').forEach(el => el.disabled = false);
            } else {
                patientFields.classList.add('conditional-enter-active');
                patientFields.querySelectorAll('input, select').forEach(el => el.disabled = false);
            }
        }, 100);
    });

    // Loader + validation helpers
    function handleFormSubmission(btn) {
        btn.classList.add('loading');
        btn.disabled = true;
    }

    function validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function showMessage(message, type) {
        document.querySelectorAll('.message').forEach(msg => msg.remove());
        const div = document.createElement('div');
        div.className = `message ${type}`;
        div.textContent = message;
        const activeForm = document.querySelector('.form-container.active .card-content');
        activeForm.insertBefore(div, activeForm.firstChild);
        setTimeout(() => div.remove(), 5000);
    }

    // Login form
    document.getElementById('loginForm').addEventListener('submit', e => {
        const email = document.getElementById('login-email').value;
        const pass = document.getElementById('login-password').value;

        if (!validateEmail(email) || pass.length < 6) {
            e.preventDefault();
            showMessage('Enter valid email and password', 'error');
            return;
        }

        document.getElementById('loader-overlay').style.display = 'flex';
    });

    // Register form
    document.getElementById('registerForm').addEventListener('submit', function (e) {
        const btn = this.querySelector('.btn');
        const pass = document.getElementById('register-password').value;
        const confirm = document.getElementById('confirm-password').value;
        const email = document.getElementById('register-email').value;
        const terms = document.getElementById('terms').checked;

        if (!validateEmail(email)) {
            e.preventDefault();
            showMessage('Please enter a valid email address', 'error');
            return;
        }
        if (pass.length < 8) {
            e.preventDefault();
            showMessage('Password must be at least 8 characters long', 'error');
            return;
        }
        if (pass !== confirm) {
            e.preventDefault();
            showMessage('Passwords do not match', 'error');
            return;
        }
        if (!terms) {
            e.preventDefault();
            showMessage('Please accept terms and conditions', 'error');
            return;
        }

        handleFormSubmission(btn);
    });

    // Focus animations
    document.querySelectorAll('input, select').forEach(input => {
        input.addEventListener('focus', function () {
            const icon = this.parentElement.querySelector('.input-icon');
            if (icon) {
                icon.style.color = '#2563eb';
                icon.style.transform = 'translateY(-50%) scale(1.1)';
            }
        });
        input.addEventListener('blur', function () {
            const icon = this.parentElement.querySelector('.input-icon');
            if (icon) {
                icon.style.color = '#9ca3af';
                icon.style.transform = 'translateY(-50%) scale(1)';
            }
        });
    });

    // Phone formatting
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function () {
        let value = this.value.replace(/\D/g, '');
        if (value.length >= 6) value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
        else if (value.length >= 3) value = value.replace(/(\d{3})(\d{0,3})/, '($1) $2');
        this.value = value;
    });

    // Password confirmation live check
    document.getElementById('confirm-password').addEventListener('input', function () {
        const pass = document.getElementById('register-password').value;
        if (this.value && pass !== this.value) {
            this.style.borderColor = '#ef4444';
            this.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';
        } else {
            this.style.borderColor = '#d1d5db';
            this.style.boxShadow = 'none';
        }
    });

    // Initial animations
    setTimeout(() => {
        document.querySelectorAll('#login-form .animate-fadeInUp').forEach((el, i) => {
            el.style.animationDelay = `${(i + 1) * 0.1}s`;
            el.style.animation = 'fadeInUp 0.6s cubic-bezier(0.4,0,0.2,1) forwards';
        });
    }, 100);

    // Default: patient fields visible
    setTimeout(() => patientFields.classList.add('conditional-enter-active'), 500);
    patientFields.querySelectorAll('input, select').forEach(el => el.disabled = false);
    doctorFields.querySelectorAll('input, select').forEach(el => el.disabled = true);

});
