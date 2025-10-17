<?php
require_once 'conf.php';

$code = $_GET['code'] ?? '';
if (empty($code)) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Statistics - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>URL Statistics</h1>
            <nav>
                <a href="index.php">← Back to Shortener</a>
            </nav>
        </header>

        <main>
            <div id="loading">Loading statistics...</div>

            <div id="stats" class="hidden">
                <div class="url-info">
                    <h2 id="shortUrl"></h2>
                    <p id="originalUrl"></p>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h3>Total Clicks</h3>
                            <div class="stat-number" id="totalClicks">0</div>
                        </div>
                        <div class="stat-card">
                            <h3>Created</h3>
                            <div class="stat-date" id="createdDate">-</div>
                        </div>
                        <div class="stat-card">
                            <h3>Last Click</h3>
                            <div class="stat-date" id="lastClick">-</div>
                        </div>
                        <div class="stat-card">
                            <h3>Status</h3>
                            <div class="stat-status" id="status">Active</div>
                        </div>
                    </div>
                </div>

                <div class="chart-container">
                    <h3>Clicks Over Time</h3>
                    <canvas id="clicksChart" width="400" height="200"></canvas>
                </div>

                <div class="qr-section">
                    <h3>QR Code</h3>
                    <div id="qrCode"></div>
                </div>
            </div>

            <div id="error" class="error hidden"></div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script>
        let statsData = null;

        async function loadStats() {
            try {
                const response = await fetch(`api.php?action=stats&code=<?php echo $code; ?>`);
                const data = await response.json();

                if (data.success) {
                    statsData = data;
                    displayStats();
                } else {
                    showError(data.error);
                }
            } catch (error) {
                console.error('Error:', error);
                showError('Failed to load statistics');
            }
        }

        function displayStats() {
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('stats').classList.remove('hidden');

            const url = statsData.url;
            const baseUrl = '<?php echo BASE_URL; ?>';

            document.getElementById('shortUrl').textContent = `${baseUrl}/${url.short_code}`;
            document.getElementById('originalUrl').textContent = url.original_url;
            document.getElementById('totalClicks').textContent = url.total_clicks || 0;
            document.getElementById('createdDate').textContent = url.created_date;
            document.getElementById('lastClick').textContent = url.last_clicked ?
                new Date(url.last_clicked).toLocaleDateString() : 'Never';
            document.getElementById('status').textContent = url.is_active ? 'Active' : 'Inactive';

            // Generate QR Code
            const qrContainer = document.getElementById('qrCode');
            QRCode.toCanvas(qrContainer, `${baseUrl}/${url.short_code}`, {
                width: 200,
                height: 200
            });

            // Create chart
            createChart();
        }

        function createChart() {
            const ctx = document.getElementById('clicksChart').getContext('2d');
            const clickData = statsData.click_data;

            const labels = clickData.map(item => item.date).reverse();
            const data = clickData.map(item => item.clicks).reverse();

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Clicks',
                        data: data,
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }

        function showError(message) {
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('error').classList.remove('hidden');
            document.getElementById('error').textContent = message;
        }

        // Load stats on page load
        loadStats();
    </script>
</body>
</html>