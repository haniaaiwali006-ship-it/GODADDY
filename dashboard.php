<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

$domains = [];
try {
    // Fetch all registered domains
    $stmt = $pdo->query("SELECT * FROM registered_domains ORDER BY registered_at DESC");
    $domains = $stmt->fetchAll();
} catch (PDOException $e) {
    // Handle table missing or other DB errors
    $db_error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - GoDaddy Clone</title>
    <style>
        :root {
            --primary-bg: #000000;
            --accent-color: #008a32;
            --text-main: #ffffff;
            --card-bg: #111;
            --glass: rgba(255, 255, 255, 0.05);
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--primary-bg);
            color: var(--text-main);
        }

        .navbar {
            padding: 20px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #000;
            border-bottom: 1px solid var(--glass);
        }

        .logo {
            font-size: 24px;
            font-weight: 800;
            color: var(--accent-color);
            text-decoration: none;
        }

        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .dashboard-header {
            margin-bottom: 40px;
            animation: slideInLeft 0.8s ease;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 50px;
        }

        .stat-card {
            background: linear-gradient(145deg, #1a1a1a, #0a0a0a);
            padding: 30px;
            border-radius: 20px;
            border: 1px solid var(--glass);
            text-align: center;
            animation: zoomIn 0.5s ease;
        }

        .stat-card h3 {
            color: #888;
            margin: 0;
            font-size: 1rem;
        }

        .stat-card h2 {
            font-size: 2.5rem;
            margin: 15px 0;
            color: var(--accent-color);
        }

        .domain-table-wrapper {
            background: #111;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid var(--glass);
            animation: fadeInUp 1s ease;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            text-align: left;
            color: #888;
        }

        td {
            padding: 20px;
            border-bottom: 1px solid var(--glass);
        }

        .domain-name {
            font-weight: bold;
            color: #fff;
        }

        .status-badge {
            background: rgba(0, 138, 50, 0.1);
            color: #00ff5d;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        .error-msg {
            background: rgba(255, 0, 0, 0.1);
            color: #ff4d4d;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 0, 0, 0.2);
        }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes zoomIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="logo">GoDaddy Clone</a>
        <a href="index.php" style="color: white; text-decoration: none;">&larr; Search More</a>
    </nav>

    <div class="container">
        <div class="dashboard-header">
            <h1>My Domains</h1>
            <p style="color: #888;">Manage your registered domain names.</p>
        </div>

        <?php if (isset($db_error)): ?>
            <div class="error-msg">
                <strong>Database Error:</strong> <?php echo $db_error; ?><br>
                Please ensure you have run the <code>setup.sql</code> script to create the required tables.
            </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Domains</h3>
                <h2><?php echo count($domains); ?></h2>
            </div>
            <div class="stat-card">
                <h3>Annual Cost</h3>
                <h2>$<?php 
                    $total = 0;
                    foreach($domains as $d) $total += $d['price'];
                    echo number_format($total, 2);
                ?></h2>
            </div>
        </div>

        <div class="domain-table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Domain Name</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Registered Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($domains) > 0): ?>
                        <?php foreach ($domains as $domain): ?>
                            <tr>
                                <td class="domain-name"><?php echo $domain['domain_name']; ?></td>
                                <td>$<?php echo $domain['price']; ?>/yr</td>
                                <td><span class="status-badge">Active</span></td>
                                <td><?php echo date('M d, Y', strtotime($domain['registered_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #666; padding: 50px;">
                                No domains registered yet. <a href="index.php" style="color: var(--accent-color);">Find your domain &rarr;</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
