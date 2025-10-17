# URL Shortener

A simple and effective URL shortener with custom aliases, click tracking, QR code generation, and analytics dashboard.

## Features

- ✅ Shorten URLs with custom aliases
- ✅ Click tracking and analytics
- ✅ QR code generation
- ✅ Admin panel with token authentication
- ✅ Responsive design
- ✅ Clean, modern UI

## Installation

### 1. Database Setup

1. Create a MySQL database
2. Run the SQL in `database_schema.sql` to create tables with the `shortener_` prefix:
   - `shortener_urls` - Main URLs table
   - `shortener_clicks` - Click tracking table  
   - `shortener_admin_users` - Admin authentication
   - `shortener_qr_cache` - QR code caching (optional)
3. Copy `conf.example.php` to `conf.php`
4. Edit `conf.php` with your database credentials and settings

### 2. Configuration

Edit `conf.php` with your settings:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'url_shortener');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('BASE_URL', 'https://yourdomain.com');
define('ADMIN_TOKEN', 'your_secure_token');
```

### 3. Web Server Setup

Make sure your web server (Apache/Nginx) is configured to:
- Point to the URL_Shortener directory
- Allow URL rewriting for clean URLs (optional, but recommended)

For Apache, add this to your `.htaccess`:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ api.php [QSA,L]
```

### 4. Dependencies

The project uses:
- PHP 7.4+ with PDO extension
- MySQL 5.7+
- Chart.js (loaded from CDN)
- QRCode.js (loaded from CDN)

## Usage

### Creating Short URLs

1. Visit the main page
2. Enter a URL to shorten
3. Optionally add a custom alias and title
4. Click "Shorten URL"
5. Copy the generated short URL
6. View QR code and statistics

### Admin Panel

Access the admin panel at: `admin.php?token=your_token`

Features:
- View all URLs and their statistics
- Delete URLs
- See total clicks and active URLs

### Analytics

Click the "View Statistics" link for any shortened URL to see:
- Total clicks
- Click history chart
- QR code
- Creation date and status

## API Endpoints

- `POST /api.php?action=shorten` - Create short URL
- `GET /api.php?action=stats&code=CODE` - Get URL statistics
- `GET /api.php?action=admin_urls&token=TOKEN` - Admin: Get all URLs
- `POST /api.php?action=delete_url&token=TOKEN` - Admin: Delete URL
- `GET /api.php/CODE` - Redirect to original URL

## Security

- Token-based admin authentication
- Input validation and sanitization
- Rate limiting (configurable)
- IP address tracking for analytics

## Customization

### Styling

Edit `style.css` to customize the appearance. The design uses:
- Modern CSS Grid and Flexbox
- Gradient backgrounds
- Responsive design
- Clean typography

### Features

Enable/disable features in `conf.php`:
- QR code generation
- Analytics tracking
- Custom aliases
- Click tracking

## File Structure

```
URL_Shortener/
├── api.php              # Main API handler
├── index.php            # Frontend
├── admin.php            # Admin panel
├── stats.php            # Statistics page
├── style.css            # Styles
├── script.js            # Frontend JavaScript
├── conf.example.php     # Configuration template
├── database_schema.sql  # Database setup
└── README.md           # This file
```

## Browser Support

- Chrome 70+
- Firefox 65+
- Safari 12+
- Edge 79+

## License

This project is open source. Feel free to use and modify as needed.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## Troubleshooting

### Common Issues

1. **"Database connection failed"**
   - Check your database credentials in `conf.php`
   - Make sure the database exists
   - Verify MySQL server is running

2. **Short URLs not working**
   - Check URL rewriting is enabled
   - Verify `.htaccess` file is present (Apache)
   - Check BASE_URL setting

3. **Admin panel access denied**
   - Verify ADMIN_TOKEN in `conf.php`
   - Check token parameter in URL

4. **QR codes not showing**
   - Check internet connection (loaded from CDN)
   - Verify JavaScript is enabled

### Debug Mode

Add this to `conf.php` for debugging:

```php
define('DEBUG', true);
error_reporting(E_ALL);
ini_set('display_errors', 1);
```