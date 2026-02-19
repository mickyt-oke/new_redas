<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HQ Dashboard - REDAS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/images/nis.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <div class="sidebar-heading text-center py-4 primary-text fs-4 fw-bold text-uppercase border-bottom">
                <img src="../assets/images/nis.png" alt="Logo" class="me-2">REDAS
            </div>
            <div class="list-group list-group-flush my-3">
                <a href="dashboard-hq.html" class="list-group-item list-group-item-action bg-transparent second-text active">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a href="data-entry.html" class="list-group-item list-group-item-action bg-transparent second-text fw-bold">
                    <i class="fas fa-edit me-2"></i>Data Entry
                </a>
                <a href="Report-generation.html" class="list-group-item list-group-item-action bg-transparent second-text fw-bold">
                    <i class="fas fa-chart-pie me-2"></i>Report Generation
                </a>
                <a href="nominal-roll.html" class="list-group-item list-group-item-action bg-transparent second-text fw-bold">
                    <i class="fas fa-users me-2"></i>Nominal Roll
                </a>
                <a href="directorate.html" class="list-group-item list-group-item-action bg-transparent second-text fw-bold">
                    <i class="fas fa-sitemap me-2"></i>Directorates
                </a>
                <a href="REDAS.html" class="list-group-item list-group-item-action bg-transparent second-text fw-bold">
                    <i class="fas fa-info-circle me-2"></i>About REDAS
                </a>
                
                <hr class="mx-3">

                <a href="profile-setting.html" class="list-group-item list-group-item-action bg-transparent second-text fw-bold">
                    <i class="fas fa-user-cog me-2"></i>Profile Settings
                </a>
                <a href="index.html" class="list-group-item list-group-item-action bg-transparent text-danger fw-bold">
                    <i class="fas fa-power-off me-2"></i>Logout
                </a>

                <!-- Dark Mode Toggle -->
                <div class="list-group-item bg-transparent mt-4 border-top">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="darkModeSwitch">
                        <label class="form-check-label second-text small" for="darkModeSwitch">Dark Mode</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 px-4 border-bottom">
                <div class="d-flex align-items-center">
                    <i class="fas fa-align-left primary-text fs-4 me-3" id="menu-toggle"></i>
                    <h2 class="fs-2 m-0">HQ Dashboard</h2>
                </div>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Search Bar -->
                    <form class="d-flex ms-auto me-4 w-50">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="search" id="sidebarSearch" class="form-control border-start-0 ps-0" placeholder="Search menu items...">
                        </div>
                    </form>

                    <ul class="navbar-nav mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle second-text fw-bold" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-2"></i>DCI Jane Doe
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="profile-setting.html">Profile</a></li>
                                <li><a class="dropdown-item" href="#">Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="index.html">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="bg-light py-2 px-4 border-bottom">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>

            <div class="container-fluid px-4">
                <div class="row g-3 my-2">
                    <div class="col-md-3">
                        <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                            <div>
                                <h3 class="fs-2">720</h3>
                                <p class="fs-5">Reports</p>
                            </div>
                            <i class="fas fa-file-alt fs-1 primary-text border rounded-full bg-light p-3"></i>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                            <div>
                                <h3 class="fs-2">49</h3>
                                <p class="fs-5">Alerts</p>
                            </div>
                            <i class="fas fa-bell fs-1 primary-text border rounded-full bg-light p-3"></i>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                            <div>
                                <h3 class="fs-2">38</h3>
                                <p class="fs-5">Pending</p>
                            </div>
                            <i class="fas fa-hourglass-half fs-1 primary-text border rounded-full bg-light p-3"></i>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                            <div>
                                <h3 class="fs-2">%25</h3>
                                <p class="fs-5">Increase</p>
                            </div>
                            <i class="fas fa-chart-line fs-1 primary-text border rounded-full bg-light p-3"></i>
                        </div>
                    </div>
                </div>

                <!-- Staff Analytics Charts -->
                <div class="row my-5">
                    <h3 class="fs-4 mb-3">Staff Analytics</h3>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white fw-bold border-bottom">
                                <i class="fas fa-chart-bar me-2"></i>Staff Numbers by Zone (Histogram)
                            </div>
                            <div class="card-body">
                                <canvas id="hqStaffHistogram" style="max-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white fw-bold border-bottom">
                                <i class="fas fa-chart-pie me-2"></i>Overall Gender Distribution
                            </div>
                            <div class="card-body">
                                <canvas id="hqGenderPie" style="max-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white fw-bold border-bottom">
                                <i class="fas fa-chart-bar me-2"></i>Overall Ranks Distribution
                            </div>
                            <div class="card-body">
                                <canvas id="hqRanksBar" style="max-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white fw-bold border-bottom">
                                <i class="fas fa-chart-bar me-2"></i>Reports by Zone
                            </div>
                            <div class="card-body">
                                <canvas id="hqReportsBar" style="max-height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row my-5">
                    <h3 class="fs-4 mb-3">Recent Reports</h3>
                    <div class="col">
                        <table class="table bg-white rounded shadow-sm  table-hover">
                            <thead>
                                <tr>
                                    <th scope="col" width="50">#</th>
                                    <th scope="col">Source</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <td>Lagos Command</td>
                                    <td>Monthly Returns - Jan 2026</td>
                                    <td>01-02-2026</td>
                                    <td><span class="badge bg-success">Approved</span></td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <td>Zone A</td>
                                    <td>Weekly Incident Report</td>
                                    <td>31-01-2026</td>
                                    <td><span class="badge bg-warning text-dark">Pending</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Return to Landing Page -->
            <footer class="bg-light text-center py-3 mt-auto">
                <a href="../index.html" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-2"></i>Back to Landing Page</a>
            </footer>
        </div>
    </div>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="btn btn-primary btn-lg rounded-circle shadow">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/app.js"></script>
    <script src="../js/staff-charts.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            loadHQCharts();
        });
    </script>
</body>
</html>
