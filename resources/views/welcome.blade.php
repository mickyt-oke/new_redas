<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', '') }} | Home </title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/nis.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="resources/css/styles.css">
  
</head>
<body>

    <!-- Fixed NIS Logo -->
    {{-- <div style="position: fixed; top: 10px; left: 10px; z-index: 9999;">
        <img src="{{ asset('assets/images/nis.png') }}" alt="NIS Logo" style="height: 40px;">
    </div> --}}

    <!-- Navigation Bar -->
    <nav id="mainNavbar" class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <img src="{{ asset('assets/images/nis-logo.png') }}" alt="Logo" style="height: 80px;">
                
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="main-nav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Learn More</a></li>
                    <li class="nav-item"><a class="nav-link" href="#news">News</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-bold" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-sign-in-alt me-1"></i> Access Portals
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="login"><i class="fas fa-building fa-fw me-2"></i>Login</a></li>
                        </ul>
                    </li>
                </ul>
                {{-- <div class="form-check form-switch ms-lg-3">
                    <input class="form-check-input" type="checkbox" id="darkModeSwitch">
                    <label class="form-check-label small" for="darkModeSwitch"><i class="fas fa-moon"></i></label>
                </div> --}}
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section text-white text-center" style="background: url('{{ asset('assets/images/nis-cover.jpeg') }}') no-repeat center center; background-size: cover; height: 100vh; display: flex; align-items: center;">
        <div class="container">
            <h1 class="display-3 fw-bold">Reporting Dashboard & Archiving System</h1>
            <p class="lead my-4">Streamlining communication and data management for the Nigeria Immigration Service.</p>
        </div>
    </header>

    <!-- Features Section -->
    <section id="features" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">A Unified Platform for Modern Operations</h2>
                <p class="text-muted">REDAS provides a robust toolset for every command level.</p>
            </div>
            <div class="row text-center g-4">
                <div class="col-lg-4">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body p-4">
                            <div class="feature-icon bg-success bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="fas fa-cogs"></i></div>
                            <h5 class="card-title fw-bold">Secure Reporting</h5>
                            <p class="card-text text-muted">Submit, view, and manage reports from all formations in one secure location.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body p-4">
                            <div class="feature-icon bg-success bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="fas fa-archive"></i></div>
                            <h5 class="card-title fw-bold">Digital Archiving</h5>
                            <p class="card-text text-muted">Securely archive and retrieve important documents, eliminating physical paperwork.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow-sm h-100 border-0">
                        <div class="card-body p-4">
                            <div class="feature-icon bg-success bg-gradient text-white rounded-3 mb-4 mt-n4"><i class="fas fa-chart-bar"></i></div>
                            <h5 class="card-title fw-bold">Data Analytics</h5>
                            <p class="card-text text-muted">Generate insightful statistics and visualizations for better decision-making.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dashboard Preview Section -->
    <section id="statistics" class="py-5">
        <div class="container">
            <div class="row align-items-center gx-5">
                <div class="col-lg-6">
                    <h2 class="fw-bold">Live Operational Insights</h2>
                    <p class="text-muted">The REDAS platform provides a real-time overview of key metrics, including passport applications and visa issuance. This powerful analytics tool helps leadership make informed, data-driven decisions.</p>
                    <p class="text-muted">The dashboard visualizes trends over time, ensuring all levels of command can grasp the operational landscape at a glance.</p>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-lg border-0">
                        <div class="card-body p-4">
                            <h5 class="card-title fw-bold mb-3">Monthly Issuance Statistics</h5>
                            <canvas id="passportChart" style="height: 300px; width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- News & Announcements Section -->
    <section id="news" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">News & Announcements</h2>
                <p class="text-muted">Stay updated with the latest circulars and information.</p>
            </div>
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="list-group shadow-sm">
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold text-success">Circular: New Passport Issuance Guidelines</h6>
                                <small>Posted by HQ (Visa & Residency) - 2 hours ago</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">New</span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <h6 class="mb-1 fw-bold">System Maintenance Scheduled</h6>
                            <small>Posted by ICT Directorate - Yesterday</small>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <h6 class="mb-1 fw-bold">Promotion Exams 2023 Results Released</h6>
                            <small>Posted by HRM - 3 days ago</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white pt-5 pb-4">
        <div class="container text-center text-md-start">
            <div class="row">
                <div class="col-md-4 col-lg-4 col-xl-4 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 fw-bold primary-text">REDAS</h5>
                    <p>The Reporting Dashboard and Archiving System is a proprietary tool of the Nigeria Immigration Service designed to enhance operational efficiency and data integrity.</p>
                </div>
                <div class="col-md-4 col-lg-2 col-xl-2 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 fw-bold">Quick Links</h5>
                    <p><a href="https://immigration.gov.ng/" target="_blank" class="text-white-50 text-decoration-none">NIS Main Site</a></p>
                    <p><a href="login.html?role=hq" class="text-white-50 text-decoration-none">HQ Portal</a></p>
                    <p><a href="#" class="text-white-50 text-decoration-none" data-bs-toggle="modal" data-bs-target="#contactSupportModal">Support</a></p>
                </div>
                <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
                    <h5 class="text-uppercase mb-4 fw-bold">Contact</h5>
                    <p><i class="fas fa-home me-3"></i>Service Headquarters, Abuja, FCT</p>
                    <p><i class="fas fa-envelope me-3"></i>support.redas@immigration.gov.ng</p>
                    <p><i class="fas fa-phone me-3"></i>+234 800 123 4567</p>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p>&copy; 2026 Nigeria Immigration Service. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Contact Support Modal -->
    <div class="modal fade" id="contactSupportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-headset me-2"></i>Contact ICT Support</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Officer Name</label>
                                <input type="text" class="form-control" placeholder="Enter full name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Service Number</label>
                                <input type="text" class="form-control" placeholder="NIS/XX/XXXX">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Issue Category</label>
                            <select class="form-select">
                                <option selected>Login / Access Issue</option>
                                <option>File Upload Error</option>
                                <option>Dashboard Glitch</option>
                                <option>Other Technical Issue</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="4" placeholder="Describe the issue in detail..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" onclick="alert('Support ticket submitted successfully!')" data-bs-dismiss="modal">Submit Ticket</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance Mode Overlay -->
    <div id="maintenanceOverlay" class="position-fixed top-0 start-0 w-100 h-100 bg-dark text-white d-flex flex-column justify-content-center align-items-center d-none" style="z-index: 9999; opacity: 0.95;">
        <i class="fas fa-tools fa-5x mb-4 text-warning"></i>
        <h1 class="display-4 fw-bold">System Maintenance</h1>
        <p class="lead">We are currently performing scheduled maintenance.</p>
        <p class="text-muted">Please check back later.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="resources/js/app.js"></script>
    <script src="resources/js/chart-setup.js"></script>
</body>
</html>