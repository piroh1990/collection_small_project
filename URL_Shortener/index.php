<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Shorten Your URLs</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><?php echo SITE_NAME; ?></h1>
            <p>Shorten your URLs with custom aliases and track clicks</p>
        </header>

        <main>
            <div class="shortener-form">
                <form id="urlForm">
                    <div class="form-group">
                        <label for="originalUrl">URL to shorten:</label>
                        <input type="url" id="originalUrl" name="url" required
                               placeholder="https://example.com/very/long/url">
                    </div>

                    <div class="form-group">
                        <label for="customAlias">Custom alias (optional):</label>
                        <input type="text" id="customAlias" name="alias"
                               placeholder="my-link" pattern="[a-zA-Z0-9_-]+">
                        <small>Only letters, numbers, hyphens, and underscores</small>
                    </div>

                    <div class="form-group">
                        <label for="title">Title (optional):</label>
                        <input type="text" id="title" name="title" placeholder="My Website">
                    </div>

                    <button type="submit" class="btn btn-primary">Shorten URL</button>
                </form>
            </div>

            <div id="result" class="result hidden">
                <h3>Success!</h3>
                <div class="short-url">
                    <input type="text" id="shortUrl" readonly>
                    <button onclick="copyToClipboard()" class="btn btn-secondary">Copy</button>
                </div>
                <div class="qr-code" id="qrContainer"></div>
                <div class="stats-link">
                    <a href="#" id="statsLink" target="_blank">View Statistics</a>
                </div>
            </div>

            <div id="error" class="error hidden"></div>
        </main>

        <footer>
            <p>&copy; 2025 <?php echo SITE_NAME; ?> | <a href="admin.php">Admin</a></p>
        </footer>
    </div>

    <script src="script.js"></script>
</body>
</html>
