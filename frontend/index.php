<?php
// AWS Function URL
$api_url = "https://ktaglnmfyjbvuo4ulymybjelv40twbdq.lambda-url.ap-southeast-2.on.aws/";

function fetchData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    $output = curl_exec($ch);
    curl_close($ch);
    return json_decode($output, true);
}

$data = fetchData($api_url);
$current = $data['current'] ?? null;
$history = $data['history'] ?? [];

$status_class = ($current && $current['status'] === 'ONLINE') ? 'status-green' : 'status-red';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trust Monitor Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>System Trust Monitor</h1>
        
        <div class="card">
            <h2>Target: <?php echo htmlspecialchars($current['target'] ?? 'Loading...'); ?></h2>
            <div class="indicator <?php echo $status_class; ?>">
                <?php echo htmlspecialchars($current['status'] ?? 'CONNECTING...'); ?>
            </div>
            <div class="details">
                <p>HTTP Code: <strong><?php echo htmlspecialchars($current['http_code'] ?? '-'); ?></strong></p>
            </div>
        </div>

        <div class="history-section">
            <h3>Recent Checks (DynamoDB)</h3>
            <table>
                <tr>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Code</th>
                </tr>
                <?php foreach ($history as $log): ?>
                <tr>
                    <td><?php echo date('H:i:s', strtotime($log['timestamp'])); ?></td>
                    <td class="<?php echo ($log['status'] === 'ONLINE') ? 'text-green' : 'text-red'; ?>">
                        <?php echo htmlspecialchars($log['status']); ?>
                    </td>
                    <td><?php echo htmlspecialchars($log['http_code']); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        
        <p class="footer">Powered by AWS Lambda, DynamoDB & PHP</p>
    </div>
</body>
</html>