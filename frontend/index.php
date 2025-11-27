<?php
// REPLACE THIS with your actual AWS Function URL
$api_url = "https://ktaglnmfyjbvuo4ulymybjelv40twbdq.lambda-url.ap-southeast-2.on.aws/";

// FUNCTION: Use cURL to fetch data (More robust than file_get_contents)
function fetchData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    // IMPORTANT: Fix for Windows Localhost SSL issues
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    
    $output = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || !$output) {
        return null;
    }
    
    return json_decode($output, true);
}

// Fetch the data
$monitor = fetchData($api_url);

// Default values if the connection fails
if (!$monitor) {
    $monitor = [
        'target' => 'Connection Failed',
        'status' => 'ERROR',
        'http_code' => 'N/A',
        'timestamp' => date('Y-m-d H:i:s')
    ];
    $status_class = 'status-red';
} else {
    // Determine color based on status
    $status_class = ($monitor['status'] === 'ONLINE') ? 'status-green' : 'status-red';
}
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
            <h2>Target: <?php echo htmlspecialchars($monitor['target']); ?></h2>
            
            <div class="indicator <?php echo $status_class; ?>">
                <?php echo htmlspecialchars($monitor['status']); ?>
            </div>

            <div class="details">
                <p>HTTP Code: <strong><?php echo htmlspecialchars($monitor['http_code']); ?></strong></p>
                <p>Last Checked: <?php echo htmlspecialchars($monitor['timestamp']); ?></p>
            </div>
        </div>
        <p class="footer">Powered by AWS Lambda & PHP</p>
    </div>
</body>
</html>