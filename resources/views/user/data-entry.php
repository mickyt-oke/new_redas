<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Entry - REDAS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../assets/nis.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

    <!-- Fixed NIS Logo -->
    <div style="position: fixed; top: 10px; left: 10px; z-index: 9999;">
        <img src="assets/images/nis.png" alt="NIS Logo" style="height: 40px;">
    </div>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <div class="sidebar-heading text-center py-4 primary-text fs-4 fw-bold text-uppercase border-bottom">
                <img src="../assets/nis.png" alt="Logo" class="me-2" style="height: 40px;">REDAS
            </div>
            <div class="list-group list-group-flush my-3">
                <a href="dashboard-hq.html" class="list-group-item list-group-item-action bg-transparent second-text fw-bold">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a href="data-entry.html" class="list-group-item list-group-item-action bg-transparent second-text active">
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
                <a href="../login.html" class="list-group-item list-group-item-action bg-transparent text-danger fw-bold">
                    <i class="fas fa-power-off me-2"></i>Logout
                </a>
            </div>
        </div>

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white py-3 px-4 border-bottom">
                <div class="d-flex align-items-center">
                    <i class="fas fa-align-left primary-text fs-4 me-3" id="menu-toggle"></i>
                    <h2 class="fs-2 m-0">Data Entry</h2>
                </div>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle second-text fw-bold" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-2"></i>DCI Jane Doe
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="profile-setting.html">Profile</a></li>
                                <li><a class="dropdown-item" href="../login.html">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="container-fluid px-4">
                <div class="row my-5">
                    <h3 class="fs-4 mb-3">New Report Submission</h3>
                    <div class="col">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <form>
                                    <div class="mb-3">
                                        <label for="reportTitle" class="form-label">Report Title</label>
                                        <input type="text" class="form-control" id="reportTitle" placeholder="e.g., Monthly Returns for Jan 2026">
                                    </div>
                                    <div class="mb-3">
                                        <label for="reportCategory" class="form-label">Category</label>
                                        <select class="form-select" id="reportCategory">
                                            <option selected>Choose...</option>
                                            <option value="1">Incident Report</option>
                                            <option value="2">Monthly Returns</option>
                                            <option value="3">Personnel Update</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reportFile" class="form-label">Upload Document (PDF, DOCX)</label>
                                        <input class="form-control" type="file" id="reportFile">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit Report</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/app.js"></script>
</body>
</html>
