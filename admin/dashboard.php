<?php
session_start();
require_once '../config/config.php';

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || !($_SESSION['is_admin'] ?? false)) {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch summary data
$totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalDemands = (int)$pdo->query("SELECT COUNT(*) FROM demands")->fetchColumn();
$totalDeposits = (float)$pdo->query("SELECT COALESCE(SUM(amount), 0) FROM deposits")->fetchColumn();
$totalAccepted = (float)$pdo->query("SELECT COALESCE(SUM(amount), 0) FROM demands WHERE status = 'accepted'")->fetchColumn();
$availableBudget = $totalDeposits - $totalAccepted;
$budgetUsage = $totalDeposits > 0 ? ($totalAccepted / $totalDeposits) * 100 : 0;

// Fetch recent users
$usersList = $pdo->query("SELECT name, email, role FROM users ORDER BY id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

// Fetch demands
$demandsList = $pdo->query("
    SELECT d.amount, d.created_at AS demand_date, d.status, u.name
    FROM demands d
    JOIN users u ON d.user_id = u.id
    ORDER BY d.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch registrations for chart
$userStats = $pdo->query("
    SELECT DATE(created_at) as date, COUNT(*) as count
    FROM users
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY DATE(created_at)
    ORDER BY DATE(created_at)
")->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for registration chart
$chartLabels = [];
$chartData = [];
$dates = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dates[$date] = 0;
}
foreach ($userStats as $stat) {
    $dates[$stat['date']] = (int)$stat['count'];
}
foreach ($dates as $date => $count) {
    $chartLabels[] = $date;
    $chartData[] = $count;
}

// Fetch spending by department for pie chart
$departmentSpending = $pdo->query("
    SELECT dept.name AS department, SUM(d.amount) AS total
    FROM demands d
    JOIN departments dept ON d.department_id = dept.id
    WHERE d.status = 'accepted'
    GROUP BY dept.id
")->fetchAll(PDO::FETCH_ASSOC);

// Extract labels and data for pie chart
$deptLabels = array_column($departmentSpending, 'department');
$deptData = array_column($departmentSpending, 'total');

// Utility function
function e($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../assets/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <main class="container">
        <h1>Admin Dashboard</h1>

        <!-- Flash message -->
        <?php if (!empty($_SESSION['message'])): ?>
            <div class="message"><?= e($_SESSION['message']); ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <!-- Summary cards -->
        <section class="dashboard-summary">
            <div class="card">
                <h3><?= $totalUsers; ?></h3>
                <p>Total Users</p>
            </div>
            <div class="card">
                <h3><?= $totalDemands; ?></h3>
                <p>Total Demands</p>
            </div>
            <div class="card">
                <h3><?= number_format($availableBudget, 2); ?> TND</h3>
                <p>Available Budget</p>
            </div>
        </section>

        <!-- Budget Usage -->
        <section>
            <h2>Budget Usage</h2>
            <div class="progress-bar-container">
                <div class="progress-bar" style="width: <?= number_format($budgetUsage, 2); ?>%;"></div>
            </div>
            <p><?= number_format($budgetUsage, 2); ?>% of the budget has been used.</p>
        </section>

        <!-- Registrations Chart -->
        <section>
            <h2>User Registrations (Last 7 Days)</h2>
            <canvas id="userStatsChart" width="400" height="200"></canvas>
        </section>

        <!-- Spending by Department Pie Chart -->
        <section>
            <h2>Spending by Department</h2>
            <div class="chart-container">
                <canvas id="deptPieChart"></canvas>
            </div>
        </section>

        <!-- Search Demands -->
        <section>
            <h2>All Demands</h2>
            <input type="text" id="searchInput" placeholder="Search demands..." onkeyup="filterTable()" />
            <?php if (empty($demandsList)): ?>
                <p>No demands found.</p>
            <?php else: ?>
                <table class="styled-table" id="demandsTable">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Amount (TND)</th>
                            <th>Demand Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($demandsList as $demand): ?>
                            <tr>
                                <td><?= e($demand['name']); ?></td>
                                <td><?= number_format($demand['amount'], 2); ?></td>
                                <td><?= e($demand['demand_date']); ?></td>
                                <td><?= e(ucfirst($demand['status'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>

    <?php include '../includes/footer.php'; ?>

    <!-- JavaScript -->
    <script>
        // User Registrations Chart
        const ctxUser = document.getElementById('userStatsChart').getContext('2d');
        new Chart(ctxUser, {
            type: 'line',
            data: {
                labels: <?= json_encode($chartLabels); ?>,
                datasets: [{
                    label: 'User Registrations',
                    data: <?= json_encode($chartData); ?>,
                    backgroundColor: 'rgba(0, 123, 255, 0.2)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        });

        // Spending by Department Pie Chart
        const ctxPie = document.getElementById('deptPieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: <?= json_encode($deptLabels); ?>,
                datasets: [{
                    data: <?= json_encode($deptData); ?>,
                    backgroundColor: [
                        '#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6610f2'
                    ]
                }]
            },
            options: {
                responsive: true
            }
        });

        // Filter Demands Table
        function filterTable() {
            const input = document.getElementById("searchInput");
            const filter = input.value.toLowerCase();
            const table = document.getElementById("demandsTable");
            const tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let visible = false;
                const td = tr[i].getElementsByTagName("td");
                for (let j = 0; j < td.length; j++) {
                    if (td[j] && td[j].textContent.toLowerCase().indexOf(filter) > -1) {
                        visible = true;
                        break;
                    }
                }
                tr[i].style.display = visible ? "" : "none";
            }
        }
    </script>

</body>

</html>