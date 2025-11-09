<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MediBuddy+ - Authentication</title>
    <link rel="stylesheet" href="{{ asset('css/authentication.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="auth-wrapper">
            <!-- Header with Animation -->
            <div class="header animate-fadeInDown">
                <div class="logo animate-pulse-custom">
                    <i class="fas fa-heart"></i>
                </div>
                <h1>MediBuddy+</h1>
                <p>Your trusted medical companion</p>
            </div>

            <!-- Single Mode Button -->
            <div class="mode-button-container">
                <div class="mode-button-wrapper">
                    <button id="modeButton" class="mode-button">
                        <i class="fas fa-user"></i>
                        Login
                    </button>
                </div>
            </div>

            <!-- Login Form -->
            <div class="form-container active" id="login-form">
                <div class="card hover-lift">
                    <div class="card-header">
                        <h2>Welcome Back</h2>
                        <p>Sign in to your account to continue</p>
                    </div>
                    <div class="card-content">
                        <form id="loginForm" method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="form-group animate-fadeInUp" style="animation-delay: 0.1s;">
                                <label for="login-email">Email</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input type="email" id="login-email" name="email" placeholder="Enter your email" class="focus-ring" required>
                                </div>
                            </div>

                            <div class="form-group animate-fadeInUp" style="animation-delay: 0.2s;">
                                <label for="login-password">Password</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" id="login-password" name="password" placeholder="Enter your password" class="focus-ring" required>
                                </div>
                            </div>

                            <div class="form-options animate-fadeInUp" style="animation-delay: 0.3s;">
                                <div class="checkbox-wrapper">
                                    <input type="checkbox" id="remember" name="remember">
                                    <label for="remember">Remember me</label>
                                </div>
                                <a href="#" class="forgot-password hover-scale">Forgot password?</a>
                            </div>

                            <div class="animate-fadeInUp" style="animation-delay: 0.4s;">
                                <button type="submit" class="btn btn-primary btn-ripple hover-lift">LogIn</button>
                            </div>

                            <div class="form-footer animate-fadeInUp" style="animation-delay: 0.5s;">
                                Don't have an account? 
                                <a href="#" id="switchToRegister" class="switch-link hover-scale">Sign up here</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Register Form -->
            <div class="form-container" id="register-form">
                <div class="card hover-lift">
                    <div class="card-header">
                        <h2>Create Account</h2>
                        <p>Join our healthcare community today</p>
                    </div>
                    <div class="card-content">
                        <form id="registerForm" method="POST" action="{{ route('register') }}">
                            @if ($errors->any())
                                <div class="message error">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @csrf
                            <!-- User Type Selection -->
                            <div class="form-group animate-fadeInUp" style="animation-delay: 0.1s;">
                                <label for="user-type">I am a</label>
                                <select id="user-type" name="role" class="form-select focus-ring">
                                    <option value="patient">👤 Patient</option>
                                    <option value="doctor">🩺 Doctor</option>
                                </select>
                            </div>

                            <!-- Name Fields -->
                            <div class="form-row animate-fadeInUp" style="animation-delay: 0.2s;">
                                <div class="form-group">
                                    <label for="first-name">First Name</label>
                                    <input type="text" id="first-name" name="first_name" placeholder="bhumika" class="focus-ring" required>
                                </div>
                                <div class="form-group">
                                    <label for="last-name">Last Name</label>
                                    <input type="text" id="last-name" name="last_name" placeholder="rathor" class="focus-ring" required>
                                </div>
                            </div>

                            <!-- Common Fields -->
                            <div class="form-group animate-fadeInUp" style="animation-delay: 0.3s;">
                                <label for="register-email">Email</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input type="email" id="register-email" name="email" placeholder="bhumika.rathor@example.com" class="focus-ring" required>
                                </div>
                            </div>

                            <div class="form-group animate-fadeInUp" style="animation-delay: 0.4s;">
                                <label for="phone">Phone Number</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-phone input-icon"></i>
                                    <input type="tel" id="phone" name="phone" placeholder="+91 11111 00000" class="focus-ring" required>
                                </div>
                            </div>

                            <!-- Doctor-specific fields -->
                            <div id="doctor-fields" class="conditional-fields">
                                <div class="form-group animate-fadeInUp" style="animation-delay: 0.5s;">
                                    <label for="specialization">Specialization</label>
                                    <select id="specialization" name="specialization" class="form-select focus-ring">
                                        <option value="">Select your specialization</option>
                                        <option value="cardiology">Cardiology</option>
                                        <option value="dermatology">Dermatology</option>
                                        <option value="neurology">Neurology</option>
                                        <option value="pediatrics">Pediatrics</option>
                                        <option value="psychiatry">Psychiatry</option>
                                        <option value="general">General Medicine</option>
                                    </select>
                                </div>
                                <div class="form-group animate-fadeInUp" style="animation-delay: 0.6s;">
                                    <label for="license">Medical License Number</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-graduation-cap input-icon"></i>
                                        <input type="text" id="license" name="license" placeholder="Enter license number" class="focus-ring">
                                    </div>
                                </div>
                                <div class="form-group animate-fadeInUp" style="animation-delay: 0.7s;">
                                    <label for="experience">Years of Experience</label>
                                    <select id="experience" name="experience" class="form-select focus-ring">
                                        <option value="">Select experience</option>
                                        <option value="0-2">0-2 years</option>
                                        <option value="3-5">3-5 years</option>
                                        <option value="6-10">6-10 years</option>
                                        <option value="11-15">11-15 years</option>
                                        <option value="15+">15+ years</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Patient-specific fields -->
                            <div id="patient-fields" class="conditional-fields conditional-enter-active">
                                <div class="form-group animate-fadeInUp" style="animation-delay: 0.5s;">
                                    <label for="dob">Date of Birth</label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-calendar input-icon"></i>
                                        <input type="date" id="dob" name="dob" class="focus-ring">
                                    </div>
                                </div>
                                <div class="form-group animate-fadeInUp" style="animation-delay: 0.6s;">
                                    <label for="gender">Gender</label>
                                    <select id="gender" name="gender" class="form-select focus-ring">
                                        <option value="">Select gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                        <option value="prefer-not-to-say">Prefer not to say</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group animate-fadeInUp" style="animation-delay: 0.8s;">
                                <label for="address">Address</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-map-marker-alt input-icon"></i>
                                    <input type="text" id="address" name="address" placeholder="Enter your address" class="focus-ring" required>
                                </div>
                            </div>

                            <div class="form-group animate-fadeInUp" style="animation-delay: 0.9s;">
                                <label for="register-password">Password</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" id="register-password" name="password" placeholder="Create a strong password" class="focus-ring" required>
                                </div>
                            </div>

                            <div class="form-group animate-fadeInUp" style="animation-delay: 1.0s;">
                                <label for="confirm-password">Confirm Password</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" id="confirm-password" name="password_confirmation" placeholder="Confirm your password" class="focus-ring" required>
                                </div>
                            </div>

                            <div class="checkbox-wrapper animate-fadeInUp" style="animation-delay: 1.1s;">
                                <input type="checkbox" id="terms" required>
                                <label for="terms">
                                    I agree to the <a href="#" class="hover-scale">Terms of Service</a> and <a href="#" class="hover-scale">Privacy Policy</a>
                                </label>
                            </div>

                            <div class="animate-fadeInUp" style="animation-delay: 1.2s;">
                                <button type="submit" class="btn btn-success btn-ripple hover-lift">Create Account</button>
                            </div>

                            <div class="form-footer animate-fadeInUp" style="animation-delay: 1.3s;">
                                Already have an account? 
                                <a href="#" id="switchToLogin" class="switch-link hover-scale">Sign in here</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer animate-fadeInUp" style="animation-delay: 0.6s;">
                <p>© 2024 MediBuddy+. All rights reserved.</p>
            </div>
        </div>
    </div>

    <div id="loader-overlay">
            <div class="loader"></div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
</body>
</html>
