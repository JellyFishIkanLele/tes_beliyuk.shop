<?php
// email-dashboard.php
$today = date('Y-m-d');
$logFile = "email-usage-$today.txt";

// Read sent emails
$sentToday = 0;
$emailList = [];
if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES);
    $sentToday = count($lines);
    
    foreach ($lines as $line) {
        list($time, $email) = explode(' | ', $line);
        $emailList[] = ['time' => $time, 'email' => $email];
    }
}

// Calculate statistics
$hourly = [];
foreach ($emailList as $item) {
    $hour = substr($item['time'], 0, 2);
    $hourly[$hour] = ($hourly[$hour] ?? 0) + 1;
}

$remaining = max(0, 500 - $sentToday);
$percentage = ($sentToday / 500) * 100;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Email Dashboard - yongkyiman@gmail.com</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin: 30px 0; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        .stat-number { font-size: 36px; font-weight: bold; }
        .stat-label { color: #666; margin-top: 10px; }
        .limit-warning { background: #fef2f2; border-left: 4px solid #ef4444; padding: 15px; margin: 20px 0; }
        .chart-container { max-width: 800px; margin: 40px auto; }
    </style>
</head>
<body>
    <h1>📊 Email Usage Dashboard</h1>
    <p>Account: <strong>yongkyiman@gmail.com</strong> | Date: <?= $today ?></p>
    
    <?php if ($sentToday >= 450): ?>
    <div class="limit-warning">
        <h3>⚠️ WARNING: High Usage (<?= $sentToday ?>/500)</h3>
        <p>You have sent <?= $sentToday ?> emails today. Only <?= $remaining ?> emails remaining.</p>
    </div>
    <?php endif; ?>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number" style="color: #10b981;"><?= $sentToday ?></div>
            <div class="stat-label">Sent Today</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #3b82f6;"><?= $remaining ?></div>
            <div class="stat-label">Remaining</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #f59e0b;"><?= round($percentage, 1) ?>%</div>
            <div class="stat-label">Usage</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #8b5cf6;"><?= count(array_unique(array_column($emailList, 'email'))) ?></div>
            <div class="stat-label">Unique Recipients</div>
        </div>
    </div>
    
    <div class="chart-container">
        <canvas id="hourlyChart"></canvas>
    </div>
    
    <h3>📋 Recent Emails Sent</h3>
    <table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">
        <tr style="background: #f3f4f6;">
            <th>Time</th>
            <th>Recipient Email</th>
        </tr>
        <?php foreach (array_slice($emailList, -20) as $item): ?>
        <tr>
            <td><?= $item['time'] ?></td>
            <td><?= $item['email'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <script>
        // Hourly chart
        const ctx = document.getElementById('hourlyChart').getContext('2d');
        const hourlyData = <?= json_encode($hourly) ?>;
        
        const labels = Array.from({length: 24}, (_, i) => i.toString().padStart(2, '0') + ':00');
        const data = labels.map(label => hourlyData[label] || 0);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Emails Sent Per Hour',
                    data: data,
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Emails'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Hour of Day'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>