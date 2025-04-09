<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Inventory System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome (Icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
        }
        .sidebar .nav-link:hover {
            color: white;
            background: #495057;
        }
        .sidebar .nav-link.active {
            color: white;
            background: #007bff;
        }
        .main-content {
            padding: 20px;
        }
    </style>
</head>
<body>
<!-- Navbar Without Auth -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">
            <i class="fas fa-tractor me-2"></i> Farm Inventory
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                        <i class="fas fa-boxes me-1"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-map-marked-alt me-1"></i> Fields
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-file-alt me-1"></i> Reports
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 d-none d-md-block sidebar p-0">
            <div class="p-3">
                <h5 class="text-center">Menu</h5>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                        <i class="fas fa-boxes me-2"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-map-marked-alt me-2"></i> Fields
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-file-alt me-2"></i> Reports
                    </a>
                </li>
            </ul>
        </div>

        <!-- Page Content -->
        <div class="col-md-10 main-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
