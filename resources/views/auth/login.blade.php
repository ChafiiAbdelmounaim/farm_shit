<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Farm Inventory System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .login-card {
            border-radius: 1rem;
            overflow: hidden;
        }
        .login-image {
            border-radius: 1rem 0 0 1rem;
            height: 100%;
            object-fit: cover;
        }
        .btn-login {
            background-color: #2c3e50;
            border: none;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background-color: #1a252f;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
<section class="vh-100" style="background-image: url('{{ asset('images/background.jpg') }}'); background-size: cover; background-position: center;">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col col-xl-10">
                <div class="card login-card shadow">
                    <div class="row g-0">
                        <!-- Left Side - Image -->
                        <div class="col-md-6 col-lg-5 d-none d-md-flex">
                            <img src="{{ asset('images/login2.jpg') }}"
                                 alt="Farm Inventory Login"
                                 class="img-fluid login-image">
                        </div>

                        <!-- Right Side - Form -->
                        <div class="col-md-6 col-lg-7 d-flex align-items-center">
                            <div class="card-body p-4 p-lg-5">
                                <!-- Logo/Header -->
                                <div class="d-flex align-items-center mb-3 pb-1">
                                    <i class="fas fa-tractor fa-2x me-3 text-primary"></i>
                                    <span class="h1 fw-bold mb-0 text-primary">FarmInventory</span>
                                </div>

                                <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Connectez-vous à votre compte</h5>

                                <!-- Login Form -->
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf

                                    <!-- Email Input -->
                                    <div class="form-floating mb-4">
                                        <input type="email"
                                               id="email"
                                               name="email"
                                               class="form-control form-control-lg @error('email') is-invalid @enderror"
                                               value="{{ old('email') }}"
                                               required autocomplete="email" autofocus>
                                        <label for="email">Adresse Email</label>
                                        @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <!-- Password Input -->
                                    <div class="form-floating mb-4">
                                        <input type="password"
                                               id="password"
                                               name="password"
                                               class="form-control form-control-lg @error('password') is-invalid @enderror"
                                               required autocomplete="current-password">
                                        <label for="password">Mot de passe</label>
                                        @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <!-- Remember Me -->
                                    <div class="form-check mb-4">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remember">
                                            Se souvenir de moi
                                        </label>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="pt-1 mb-4">
                                        <button type="submit" class="btn btn-login btn-lg btn-block w-100 text-white">
                                            <i class="fas fa-sign-in-alt me-2"></i> Connexion
                                        </button>
                                    </div>

                                    <!-- Links -->
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <a class="small text-muted" href="#">
                                            Mot de passe oublié?
                                        </a>
                                        @if(Route::has('password.request'))
                                            <a class="small text-muted" href="{{ route('password.request') }}">
                                                Réinitialiser le mot de passe
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
