<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Test - PIT Count</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .test-section {
            background: #f5f5f5;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        h1 { color: #2c5aa0; }
        pre {
            background: #fff;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>PIT Count Application - System Test</h1>
    
    <div class="test-section">
        <h2>1. PHP Version</h2>
        <?php
        if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
            echo '<p class="success">✓ PHP Version: ' . PHP_VERSION . ' (OK)</p>';
        } else {
            echo '<p class="error">✗ PHP Version: ' . PHP_VERSION . ' (Requires 7.4+)</p>';
        }
        ?>
    </div>

    <div class="test-section">
        <h2>2. Required PHP Extensions</h2>
        <?php
        $required_extensions = ['pdo', 'pdo_mysql', 'json', 'session'];
        foreach ($required_extensions as $ext) {
            if (extension_loaded($ext)) {
                echo '<p class="success">✓ ' . $ext . ' - Loaded</p>';
            } else {
                echo '<p class="error">✗ ' . $ext . ' - Missing</p>';
            }
        }
        ?>
    </div>

    <div class="test-section">
        <h2>3. Configuration File</h2>
        <?php
        if (file_exists('config/config.php')) {
            echo '<p class="success">✓ config/config.php exists</p>';
            require_once 'config/config.php';
            echo '<p class="success">✓ Configuration loaded successfully</p>';
        } else {
            echo '<p class="error">✗ config/config.php not found</p>';
        }
        ?>
    </div>

    <div class="test-section">
        <h2>4. Database Connection</h2>
        <?php
        try {
            $db = getDB();
            echo '<p class="success">✓ Database connection successful</p>';
            echo '<p class="success">✓ Connected to: ' . DB_NAME . '</p>';
            
            // Test if tables exist
            $tables = ['outreach_staff', 'clients', 'pit_assessments', 'admin_widgets', 'system_settings'];
            foreach ($tables as $table) {
                $stmt = $db->query("SHOW TABLES LIKE '$table'");
                if ($stmt->rowCount() > 0) {
                    echo '<p class="success">✓ Table exists: ' . $table . '</p>';
                } else {
                    echo '<p class="error">✗ Table missing: ' . $table . '</p>';
                }
            }
        } catch (Exception $e) {
            echo '<p class="error">✗ Database connection failed: ' . $e->getMessage() . '</p>';
            echo '<p class="warning">⚠ Please check your database configuration in config/config.php</p>';
        }
        ?>
    </div>

    <div class="test-section">
        <h2>5. File Permissions</h2>
        <?php
        $paths_to_check = [
            'php/api.php' => 'readable',
            'uploads' => 'writable'
        ];
        
        foreach ($paths_to_check as $path => $permission) {
            if (!file_exists($path)) {
                if ($path === 'uploads') {
                    mkdir($path, 0755, true);
                    echo '<p class="success">✓ Created directory: ' . $path . '</p>';
                } else {
                    echo '<p class="error">✗ Path not found: ' . $path . '</p>';
                    continue;
                }
            }
            
            if ($permission === 'readable' && is_readable($path)) {
                echo '<p class="success">✓ ' . $path . ' is readable</p>';
            } elseif ($permission === 'writable' && is_writable($path)) {
                echo '<p class="success">✓ ' . $path . ' is writable</p>';
            } else {
                echo '<p class="error">✗ ' . $path . ' is not ' . $permission . '</p>';
            }
        }
        ?>
    </div>

    <div class="test-section">
        <h2>6. Initial Data</h2>
        <?php
        try {
            $db = getDB();
            
            // Check staff count
            $stmt = $db->query("SELECT COUNT(*) as count FROM outreach_staff WHERE is_active = TRUE");
            $result = $stmt->fetch();
            $staff_count = $result['count'];
            
            if ($staff_count > 0) {
                echo '<p class="success">✓ Outreach staff loaded: ' . $staff_count . ' members</p>';
            } else {
                echo '<p class="warning">⚠ No outreach staff found. Schema may need to be re-imported.</p>';
            }
            
            // Check widgets
            $stmt = $db->query("SELECT COUNT(*) as count FROM admin_widgets");
            $result = $stmt->fetch();
            $widget_count = $result['count'];
            
            if ($widget_count > 0) {
                echo '<p class="success">✓ Admin widgets loaded: ' . $widget_count . ' widgets</p>';
            } else {
                echo '<p class="warning">⚠ No widgets found. Schema may need to be re-imported.</p>';
            }
            
        } catch (Exception $e) {
            echo '<p class="error">✗ Error checking data: ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>

    <div class="test-section">
        <h2>7. Test Summary</h2>
        <?php
        echo '<p><strong>Server:</strong> ' . $_SERVER['SERVER_SOFTWARE'] . '</p>';
        echo '<p><strong>Document Root:</strong> ' . $_SERVER['DOCUMENT_ROOT'] . '</p>';
        echo '<p><strong>Current Path:</strong> ' . __DIR__ . '</p>';
        ?>
        
        <h3>Next Steps:</h3>
        <ul>
            <li><a href="index.html">Go to Landing Page</a></li>
            <li><a href="new-assessment.html">Start New Assessment</a></li>
            <li><a href="admin-login.html">Admin Login (Passcode: 079777)</a></li>
        </ul>
    </div>

    <div class="test-section">
        <h2>8. API Test</h2>
        <button onclick="testAPI()">Test API Connection</button>
        <div id="api-result"></div>
        
        <script>
        async function testAPI() {
            const resultDiv = document.getElementById('api-result');
            resultDiv.innerHTML = '<p>Testing API...</p>';
            
            try {
                const response = await fetch('./php/api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ action: 'get_total_count' })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    resultDiv.innerHTML = '<p class="success">✓ API is working! Total assessments: ' + data.total + '</p>';
                } else {
                    resultDiv.innerHTML = '<p class="error">✗ API returned error: ' + data.message + '</p>';
                }
            } catch (error) {
                resultDiv.innerHTML = '<p class="error">✗ API connection failed: ' + error.message + '</p>';
            }
        }
        </script>
    </div>
</body>
</html>
