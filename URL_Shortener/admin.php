<?php
require_once 'conf.php';

$token = $_GET['token'] ?? '';
$isLoggedIn = ($token === ADMIN_TOKEN);

if (!$isLoggedIn) {
    header('HTTP/1.0 401 Unauthorized');
    echo '<h1>Access Denied</h1><p>Invalid token</p>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><?php echo SITE_NAME; ?> - Admin Panel</h1>
            <nav>
                <a href="index.php">← Back to Shortener</a>
            </nav>
        </header>

        <main>
            <div class="admin-stats">
                <div class="stat-card">
                    <h3>Total URLs</h3>
                    <div class="stat-number" id="totalUrls">0</div>
                </div>
                <div class="stat-card">
                    <h3>Total Clicks</h3>
                    <div class="stat-number" id="totalClicks">0</div>
                </div>
                <div class="stat-card">
                    <h3>Active URLs</h3>
                    <div class="stat-number" id="activeUrls">0</div>
                </div>
            </div>

            <div class="admin-controls">
                <button onclick="refreshData()" class="btn btn-primary">Refresh Data</button>
            </div>

            <div class="urls-table">
                <h3>All URLs</h3>
                <table id="urlsTable">
                    <thead>
                        <tr>
                            <th>Short Code</th>
                            <th>Original URL</th>
                            <th>Title</th>
                            <th>Clicks</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="urlsTableBody">
                        <tr>
                            <td colspan="6">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        let urls = [];

        async function loadData() {
            try {
                const response = await fetch('api.php?action=admin_urls&token=<?php echo $token; ?>');
                const data = await response.json();

                if (data.success) {
                    urls = data.urls;
                    updateStats();
                    renderTable();
                } else {
                    alert('Error loading data: ' + data.error);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to load data');
            }
        }

        function updateStats() {
            const totalUrls = urls.length;
            const totalClicks = urls.reduce((sum, url) => sum + parseInt(url.total_clicks || 0), 0);
            const activeUrls = urls.filter(url => url.is_active).length;

            document.getElementById('totalUrls').textContent = totalUrls;
            document.getElementById('totalClicks').textContent = totalClicks;
            document.getElementById('activeUrls').textContent = activeUrls;
        }

        function renderTable() {
            const tbody = document.getElementById('urlsTableBody');

            if (urls.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6">No URLs found</td></tr>';
                return;
            }

            tbody.innerHTML = urls.map(url => `
                <tr>
                    <td>
                        <a href="<?php echo BASE_URL; ?>/${url.short_code}" target="_blank">
                            ${url.short_code}
                        </a>
                        ${url.custom_alias ? `<br><small>Alias: ${url.custom_alias}</small>` : ''}
                    </td>
                    <td>
                        <a href="${url.original_url}" target="_blank" title="${url.original_url}">
                            ${url.original_url.length > 50 ? url.original_url.substring(0, 50) + '...' : url.original_url}
                        </a>
                    </td>
                    <td>${url.title || '-'}</td>
                    <td>${url.total_clicks || 0}</td>
                    <td>${new Date(url.created_at).toLocaleDateString()}</td>
                    <td>
                        <button onclick="viewStats('${url.short_code}')" class="btn btn-secondary btn-small">Stats</button>
                        <button onclick="deleteUrl(${url.id})" class="btn btn-danger btn-small">Delete</button>
                    </td>
                </tr>
            `).join('');
        }

        async function deleteUrl(urlId) {
            if (!confirm('Are you sure you want to delete this URL?')) return;

            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete_url&token=<?php echo $token; ?>&url_id=${urlId}`
                });

                const data = await response.json();

                if (data.success) {
                    loadData(); // Refresh the data
                } else {
                    alert('Error deleting URL: ' + data.error);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to delete URL');
            }
        }

        function viewStats(code) {
            window.open(`stats.php?code=${code}`, '_blank');
        }

        function refreshData() {
            loadData();
        }

        // Load data on page load
        loadData();
    </script>
</body>
</html>