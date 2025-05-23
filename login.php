<!-- login.php -->
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Manager - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .gradient-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .form-control {
            border-radius: 8px;
            padding: 12px 20px;
        }
        
        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 0;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .link-light {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .link-light:hover {
            color: white;
            text-decoration: underline;
        }
    </style>
</head>
<body class="gradient-custom">
    <div class="container d-flex align-items-center justify-content-center h-100">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4 text-dark">🔒 Password Manager</h2>
                    <form method="post" action="login_handler.php">
                        <div class="mb-4">
                            <label for="username" class="form-label text-muted">👤 Username</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="username" 
                                   name="login" 
                                   placeholder="Enter username"
                                   required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label text-muted">🔑 Password</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Enter password"
                                   required>
                        </div>
                        
                        <button type="submit" class="btn btn-custom w-100 mt-3">Login</button>
                        
                        <div class="text-center mt-4">
                            <p class="text-muted">Don't have an account? 
                                <a href="register.php" class="link-light">Register here</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>