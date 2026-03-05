<?php
session_start();
include 'db.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$full_name, $email, $password]);
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['user_name'] = $full_name;
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = "Email already registered!";
        } else {
            $error = "Signup failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join GoDaddy Clone - Create Account</title>
    <style>
        :root {
            --primary-bg: #000000;
            --accent-color: #008a32;
            --text-main: #ffffff;
            --card-bg: #1a1a1a;
            --glass: rgba(255, 255, 255, 0.05);
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at bottom left, #001a08, #000);
            color: var(--text-main);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .auth-card {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            border: 1px solid var(--glass);
            animation: fadeInScale 0.6s ease;
        }

        .auth-card h2 {
            font-size: 2rem;
            margin-bottom: 30px;
            text-align: center;
            color: var(--accent-color);
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: #888;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid var(--glass);
            background: #111;
            color: white;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--accent-color);
        }

        .auth-btn {
            width: 100%;
            padding: 15px;
            background: var(--accent-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .auth-btn:hover {
            transform: scale(1.02);
        }

        .footer-link {
            text-align: center;
            margin-top: 20px;
            color: #888;
        }

        .footer-link a {
            color: var(--accent-color);
            text-decoration: none;
        }

        .error {
            color: #ff4d4d;
            background: rgba(255, 77, 77, 0.1);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>

    <div class="auth-card">
        <h2>Create Account</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="input-group">
                <label>Full Name</label>
                <input type="text" name="full_name" required>
            </div>
            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="auth-btn">Sign Up</button>
        </form>
        <div class="footer-link">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>

</body>
</html>
