<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$results = [];

if ($search_query) {
    $extensions = ['.com', '.net', '.org', '.io', '.tech'];
    $base_name = preg_replace('/[^a-zA-Z0-9]/', '', $search_query);
    
    foreach ($extensions as $ext) {
        $full_domain = $base_name . $ext;
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM registered_domains WHERE domain_name = ?");
            $stmt->execute([$full_domain]);
            $is_registered = $stmt->fetch();
        } catch (Exception $e) {
            $is_registered = false;
        }
        
        $results[] = [
            'name' => $full_domain,
            'price' => rand(10, 50) . ".99",
            'available' => !$is_registered
        ];
    }
}

if (isset($_POST['register_domain'])) {
    $domain = $_POST['domain_name'];
    $price = $_POST['price'];
    $ext = substr($domain, strrpos($domain, '.'));
    
    try {
        $stmt = $pdo->prepare("INSERT INTO registered_domains (domain_name, extension, price) VALUES (?, ?, ?)");
        $stmt->execute([$domain, $ext, $price]);
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        $error = "Registration failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoDaddy Clone - Search Your Domain</title>
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
            background: var(--primary-bg);
            color: var(--text-main);
            overflow-x: hidden;
        }

        .navbar {
            padding: 20px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(15px);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid var(--glass);
        }

        .logo {
            font-size: 26px;
            font-weight: 800;
            color: var(--accent-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-links {
            display: flex;
            gap: 25px;
            align-items: center;
        }

        .nav-link {
            color: #ccc;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: var(--accent-color);
        }

        .hero {
            height: 60vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: radial-gradient(circle at center, #001f0a, #000);
            text-align: center;
            padding: 0 20px;
        }

        .hero h1 {
            font-size: 4rem;
            margin-bottom: 20px;
            background: linear-gradient(to bottom, #fff, #888);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: fadeInDown 1s ease;
        }

        .search-container {
            width: 100%;
            max-width: 800px;
            position: relative;
            animation: fadeInUp 1s ease 0.2s both;
        }

        .search-container input {
            width: 100%;
            padding: 25px 30px;
            border-radius: 50px;
            border: 1px solid var(--glass);
            background: #111;
            color: white;
            font-size: 1.2rem;
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
            transition: all 0.3s;
        }

        .search-container input:focus {
            outline: none;
            box-shadow: 0 0 0 3px var(--accent-color);
        }

        .search-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 14px 40px;
            border-radius: 40px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }

        .search-btn:hover {
            transform: translateY(-50%) scale(1.05);
        }

        .results {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
        }

        .domain-card {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid var(--glass);
            transition: border-color 0.3s;
            animation: fadeIn 0.5s ease;
        }

        .domain-card:hover {
            border-color: var(--accent-color);
        }

        .domain-info h3 {
            margin: 0;
            font-size: 1.5rem;
        }

        .price {
            font-size: 1.2rem;
            color: #aaa;
            margin-top: 5px;
        }

        .action-btn {
            background: var(--accent-color);
            border: none;
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }

        .action-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .registered-lable {
            color: #ff4d4d;
            font-weight: bold;
            background: rgba(255, 77, 77, 0.1);
            padding: 5px 15px;
            border-radius: 5px;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="logo">GoDaddy Clone</a>
        <div class="nav-links">
            <a href="dashboard.php" class="nav-link" style="color: var(--accent-color); font-weight: bold;">My Dashboard</a>
        </div>
    </nav>

    <section class="hero">
        <h1>Find your perfect domain.</h1>
        <div class="search-container">
            <form action="index.php" method="GET">
                <input type="text" name="search" placeholder="Search for your next domain..." value="<?php echo htmlspecialchars($search_query); ?>" required>
                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>
    </section>

    <div class="results">
        <?php if ($search_query): ?>
            <h2 style="margin-bottom: 30px;">Results for "<?php echo htmlspecialchars($search_query); ?>"</h2>
            <?php foreach ($results as $res): ?>
                <div class="domain-card">
                    <div class="domain-info">
                        <h3><?php echo $res['name']; ?></h3>
                        <p class="price">$<?php echo $res['price']; ?>/yr</p>
                    </div>
                    <div>
                        <?php if ($res['available']): ?>
                            <form method="POST">
                                <input type="hidden" name="domain_name" value="<?php echo $res['name']; ?>">
                                <input type="hidden" name="price" value="<?php echo $res['price']; ?>">
                                <button type="submit" name="register_domain" class="action-btn">Register Now</button>
                            </form>
                        <?php else: ?>
                            <span class="registered-lable">Already Taken</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>
</html>
