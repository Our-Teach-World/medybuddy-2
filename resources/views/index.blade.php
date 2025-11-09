<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MeddyBuddy+ - Your Trusted Medical Appointment Platform</title>
    <meta name="description" content="Book medical appointments with trusted doctors. MeddyBuddy+ connects you with healthcare professionals for quality medical care.">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
</head>
<body>
    @if(session('success'))
        <div class="toast" id="toast">
            {{ session('success') }}
        </div>
    @endif

    <!-- Navigation Bar -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <i class="fas fa-heartbeat"></i>
                <span>MeddyBuddy+</span>
            </div>
            
            <ul class="nav-menu" id="nav-menu">
                <li class="nav-item"><a href="#home" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="#categories" class="nav-link">Categories</a></li>
                <li class="nav-item"><a href="#doctors" class="nav-link">Find Doctors</a></li>
                <li class="nav-item"><a href="#about" class="nav-link">About Us</a></li>

                @auth
                    <li class="nav-item profile-dropdown">
                        <a href="#" class="nav-link">
                            @if(Auth::user()->profile_image)
                                <img src="{{ asset('storage/profile/' . Auth::user()->profile_image) }}" 
                                    alt="Profile" class="nav-profile-pic">
                            @else
                                <div class="nav-profile-initials">
                                    {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
                                </div>
                            @endif
                            <span>{{ Auth::user()->first_name }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                @if(Auth::user()->role === 'patient')
                                    <a href="{{ route('patient.dashboard') }}">Dashboard</a>
                                @elseif(Auth::user()->role === 'doctor')
                                    <a href="{{ route('doctor.dashboard') }}">Dashboard</a>
                                @endif
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="logout-btn">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth

                @guest
                    <li class="nav-item"><a href="{{ route('authentication') }}" class="nav-link cta-button">Login/Register</a></li>
                @endguest
            </ul>

            <div class="hamburger" id="hamburger">
                <span class="bar"></span><span class="bar"></span><span class="bar"></span>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="swiper hero-swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="slide-content">
                        <div class="slide-text">
                            <h1>Your Health, Our Priority</h1>
                            <p>Connect with trusted medical professionals and book appointments with ease. Quality healthcare is just a click away.</p>
                            <div class="slide-buttons">
                                <a href="{{ route('doctor.index') }}" class="btn btn-primary">Book Now</a>
                                <a href="#about" class="btn btn-secondary">Learn More</a>
                            </div>
                        </div>
                        <div class="slide-image">
                            <img src="images/hooooosss.png" alt="Medical Professional">
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="slide-content">
                        <div class="slide-text">
                            <h1>24/7 Medical Support</h1>
                            <p>Get instant access to healthcare professionals anytime, anywhere. Emergency consultations and routine check-ups made simple.</p>
                            <div class="slide-buttons">
                                <a href="{{ route('doctor.index') }}" class="btn btn-primary">Emergency Care</a>
                                <a href="#categories" class="btn btn-secondary">View Services</a>
                            </div>
                        </div>
                        <div class="slide-image">
                            <img src="images/hosss2.jpg" alt="24/7 Medical Support">
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="slide-content">
                        <div class="slide-text">
                            <h1>Specialized Care</h1>
                            <p>Access specialists across various medical fields. From cardiology to dermatology, find the right expert for your needs.</p>
                            <div class="slide-buttons">
                                <a href="#categories" class="btn btn-primary">Find Specialist</a>
                                <a href="{{ route('doctor.index') }}" class="btn btn-secondary">All Categories</a>
                            </div>
                        </div>
                        <div class="slide-image">
                            <img src="images/hooooosss.png" alt="Medical Specialists">
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <button class="autoplay-toggle" id="autoplay-toggle"><i class="fas fa-pause"></i></button>
        </div>
    </section>

    <!-- Categories Section -->
    <section id="categories" class="categories-section">
        <div class="container">
            <div class="section-header">
                <h2>Medical Categories</h2>
                <p>Explore our comprehensive range of medical specialties</p>
            </div>
            <div class="swiper categories-swiper">
                <div class="swiper-wrapper">
                    @forelse($categories as $cat)
                        <div class="swiper-slide">
                            <a href="{{ route('doctor.index', ['specialization' => $cat->specialization]) }}" class="category-card-link">
                                <div class="category-card">
                                    <div class="category-icon"><i class="fas fa-user-md"></i></div>
                                    <h3>{{ $cat->specialization }}</h3>
                                    <p>{{ $cat->specialization }} specialists</p>
                                    <span class="doctor-count">{{ $cat->count }} Doctors</span>
                                </div>
                            </a>

                        </div>
                    @empty
                        <p>No categories available.</p>
                    @endforelse
                </div>
                <div class="swiper-button-next categories-next"></div>
                <div class="swiper-button-prev categories-prev"></div>
            </div>
        </div>
    </section>

    <!-- Doctors Section -->
    <section id="doctors" class="doctors-section">
        <div class="container">
            <div class="section-header">
                <h2>Meet Our Doctors</h2>
                <p>Experienced healthcare professionals dedicated to your well-being</p>
            </div>
            <div class="doctors-grid">
                @forelse($doctors as $doc)
                    <div class="doctor-card">
                        <div class="doctor-image">
                            <img src="{{ $doc->doctorProfile && $doc->doctorProfile->profile_image ? asset('storage/'.$doc->doctorProfile->profile_image) : asset('images/default.jpg') }}" alt="{{ $doc->name }}">
                            <div class="doctor-status available">Available</div>
                        </div>
                        <div class="doctor-info">
                            <h3>Dr.{{ $doc->first_name }} {{ $doc->last_name }}</h3>
                            <p class="specialty">{{ $doc->specialization }}</p>
                            <p class="experience">{{ $doc->experience }} years experience</p>
                            <div class="rating">
                                <i class="fas fa-star"></i>
                                <span>{{ $doc->doctorProfile->rating ?? 'N/A' }} ({{ $doc->doctorProfile->reviews_count ?? 0 }} reviews)</span>
                            </div>
                            <a href="{{ route('doctor.show', $doc->id) }}" class="btn btn-primary">Book Appointment</a>
                        </div>
                    </div>
                @empty
                    <p>No doctors found.</p>
                @endforelse
            </div>
            <div class="view-all-doctors">
                <a href="{{ route('doctor.index') }}"><button class="btn btn-outline">View All Doctors</button></a>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section id="about" class="about-section">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <div class="section-header">
                        <h2>About MeddyBuddy+</h2>
                        <p>Revolutionizing healthcare accessibility through technology</p>
                    </div>
                    <div class="about-description">
                        <p>Founded by a team of healthcare professionals and technology experts, we're committed to bridging the gap between patients and doctors through innovative digital solutions.</p>
                    </div>
                    <div class="about-stats">
                        <div class="stat-item"><h3>10,000+</h3><p>Happy Patients</p></div>
                        <div class="stat-item"><h3>500+</h3><p>Qualified Doctors</p></div>
                        <div class="stat-item"><h3>50+</h3><p>Medical Specialties</p></div>
                        <div class="stat-item"><h3>24/7</h3><p>Support Available</p></div>
                    </div>
                </div>
                <div class="about-image">
                    <img src="images/doctor img.png" alt="MeddyBuddy+ Healthcare Team">
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <i class="fas fa-heartbeat"></i>
                        <span>MeddyBuddy+</span>
                    </div>
                    <p>Your trusted partner in healthcare. Connecting patients with quality medical professionals for better health outcomes.</p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#categories">Categories</a></li>
                        <li><a href="#doctors">Find Doctors</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="{{ route('doctor.index') }}">Book Appointment</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Services</h3>
                    <ul>
                        <li><a href="#">Online Consultations</a></li>
                        <li><a href="#">Emergency Care</a></li>
                        <li><a href="#">Health Checkups</a></li>
                        <li><a href="#">Specialist Care</a></li>
                        <li><a href="#">Prescription Services</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact Info</h3>
                    <div class="contact-info">
                        <div class="contact-item"><i class="fas fa-phone"></i><span>+91 6375874059</span></div>
                        <div class="contact-item"><i class="fas fa-envelope"></i><span>bhumikarathor.com</span></div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <p>&copy; 2024 MeddyBuddy+. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="{{ asset('js/index.js') }}"></script>
</body>
</html>
