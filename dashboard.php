<?php
session_start();
require_once 'PasswordGenerator.php';
require_once 'PasswordManager.php';
require_once 'DatabaseConnection.php';

// Initialize variables
$passwords = [];
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Password Generation
        if (isset($_POST['generate'])) {
            $params = [
                'lower' => (int)$_POST['lower'],
                'upper' => (int)$_POST['upper'],
                'numbers' => (int)$_POST['numbers'],
                'special' => (int)$_POST['special']
            ];
            
            $_SESSION['generated_password'] = PasswordGenerator::generate($params);
            header("Location: dashboard.php");
            exit();
        }
        
        // Password Saving
        if (isset($_POST['save'])) {
            if (!isset($_SESSION['user_id']) || !isset($_SESSION['encryption_key'])) {
                throw new Exception("Session expired - please login again");
            }

            $pm = new PasswordManager(
                $_SESSION['user_id'],
                $_SESSION['encryption_key']
            );

            $pm->save(
                htmlspecialchars($_POST['website']),
                $_POST['password']
            );

            unset($_SESSION['generated_password']);
            header("Location: dashboard.php");
            exit();
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Load saved passwords
try {
    if (isset($_SESSION['user_id']) && isset($_SESSION['encryption_key'])) {
        $pm = new PasswordManager($_SESSION['user_id'], $_SESSION['encryption_key']);
        $passwords = $pm->getAll();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Password Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --gradient-start: #667eea;
            --gradient-end: #764ba2;
        }
        
        body {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            min-height: 100vh;
        }
        
        .nav-custom {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        
        .card-glass {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .password-display {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 1.2rem;
            letter-spacing: 1.5px;
        }
        
        .btn-glow {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            border: none;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-glow:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .table-transparent {
            background: rgba(255, 255, 255, 0.85);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar nav-custom">
        <div class="container">
            <a class="navbar-brand text-white" href="#">
                <i class="fas fa-lock"></i> Password Vault
            </a>
            <div class="d-flex">
                <span class="text-white me-3">Welcome, <?= htmlspecialchars($_SESSION['login'] ?? '') ?></span>
                <a href="logout.php" class="btn btn-glow btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <!-- Password Generator Card -->
        <div class="card-glass p-4 mb-4">
            <h4 class="mb-4"><i class="fas fa-key me-2"></i>Password Generator</h4>
            <form method="post">
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <label class="form-label">Lowercase</label>
                        <input type="number" name="lower" class="form-control" min="0" value="2" required>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label">Uppercase</label>
                        <input type="number" name="upper" class="form-control" min="0" value="2" required>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label">Numbers</label>
                        <input type="number" name="numbers" class="form-control" min="0" value="2" required>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label">Special</label>
                        <input type="number" name="special" class="form-control" min="0" value="2" required>
                    </div>
                </div>
                <button type="submit" name="generate" class="btn btn-glow w-100">
                    <i class="fas fa-magic me-2"></i>Generate Password
                </button>
            </form>

            <?php if (isset($_SESSION['generated_password'])): ?>
            <div class="mt-4">
                <div class="password-display p-3 mb-3 d-flex justify-content-between align-items-center">
                    <span id="generated-password"><?= htmlspecialchars($_SESSION['generated_password']) ?></span>
                    <button class="btn btn-sm copy-btn" onclick="copyPassword()">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <form method="post">
                    <div class="input-group">
                        <input type="text" 
                               name="website" 
                               class="form-control" 
                               placeholder="Enter website name"
                               required>
                        <input type="hidden" 
                               name="password" 
                               value="<?= htmlspecialchars($_SESSION['generated_password']) ?>">
                        <button type="submit" 
                                name="save" 
                                class="btn btn-glow">
                            <i class="fas fa-save me-2"></i>Save
                        </button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>

        <!-- Saved Passwords Card -->
        <div class="card-glass p-4">
            <h4 class="mb-4"><i class="fas fa-lock me-2"></i>Saved Passwords</h4>
            <div class="table-responsive">
                <table class="table table-transparent table-hover">
                    <thead>
                        <tr>
                            <th>Website</th>
                            <th>Password</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($passwords as $pwd): ?>
                        <tr>
                            <td><?= htmlspecialchars($pwd['website'] ?? '') ?></td>
                            <td><?= htmlspecialchars($pwd['password'] ?? '') ?></td>
                            <td><?= date('M d, Y H:i', strtotime($pwd['created_at'] ?? 'now')) ?></td>
                            <td>
                                <button class="btn btn-sm copy-btn" 
                                        onclick="copyPassword('<?= $pwd['password'] ?? '' ?>')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    function copyPassword(password = null) {
        const text = password || document.getElementById('generated-password')?.innerText;
        if (text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Password copied to clipboard!');
            });
        }
    }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>