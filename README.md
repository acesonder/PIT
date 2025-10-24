# PIT - Point-in-Time Count Web Application

**OUTSINC - Point-in-Time Count for Cobourg, Ontario and Northumberland County**

A comprehensive web application for conducting Point-in-Time (PIT) counts of people experiencing homelessness, designed to support the 2026 warming room planning and service provision.

## 🎯 Features

- **Landing Page** with live count display and PIT information
- **Three Assessment Types**: SHORT (6-10 min), MEDIUM (12-20 min), HARD (25-40 min)
- **Client Duplicate Prevention** - Prevents counting the same person twice
- **Consent Management** - Full or partial consent options
- **Admin Portal** with passcode protection (079777)
- **30+ Data Widgets** - Customizable data displays
- **Staff Management** - Add/remove outreach staff
- **Responsive Design** - Works on desktop, tablet, and mobile
- **AJAX-powered** - Smooth, real-time updates
- **All Questions Optional** - "Prefer not to say" or "Skip" options

## 📋 Requirements

- **Web Server**: Apache or Nginx with PHP support
- **PHP**: Version 7.4 or higher with PDO MySQL extension
- **MySQL**: Version 5.7 or higher
- **Modern Browser**: Chrome, Firefox, Safari, or Edge

## 🚀 Installation

### 1. Clone or Download Repository

```bash
git clone https://github.com/acesonder/PIT.git
cd PIT
```

### 2. Set Up Database

Create a MySQL database and import the schema:

```bash
mysql -u root -p
```

```sql
CREATE DATABASE pit_count;
EXIT;
```

Then import the schema:

```bash
mysql -u root -p pit_count < database/schema.sql
```

### 3. Configure Database Connection

Edit `config/config.php` and update these lines with your database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_mysql_username');
define('DB_PASS', 'your_mysql_password');
define('DB_NAME', 'pit_count');
```

### 4. Set Permissions

Ensure the web server has read access to all files:

```bash
chmod -R 755 /path/to/PIT
```

### 5. Configure Web Server

**For Apache:**

Create a virtual host or point your document root to the PIT directory:

```apache
<VirtualHost *:80>
    ServerName pit.local
    DocumentRoot /path/to/PIT
    
    <Directory /path/to/PIT>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**For Nginx:**

```nginx
server {
    listen 80;
    server_name pit.local;
    root /path/to/PIT;
    index index.html;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

### 6. Access the Application

Open your browser and navigate to:

```
http://localhost/PIT/
```

or

```
http://pit.local/
```

## 🔐 Admin Access

- **URL**: `/admin-login.html`
- **Default Passcode**: `079777`

### Admin Features:
- View all assessments (by type: SHORT, MEDIUM, HARD)
- Edit or delete assessments
- Manage 30+ data widgets
- Control which widgets appear on landing page
- Add/remove outreach staff members
- Export data to CSV
- View comprehensive dashboard statistics

## 👥 Default Outreach Staff

The system comes pre-populated with these staff members:
- Jordan
- London
- Chance
- Deb
- Jenni

Additional staff can be added through the Admin Portal.

## 📊 Assessment Types

### SHORT Assessment (6-10 minutes)
- ~25 questions across 7 sections
- Essential data: location, duration, demographics
- Best for rapid enumeration

### MEDIUM Assessment (12-20 minutes)
- 50-70 questions across 10 sections
- Includes health, income, barriers to housing
- Best for comprehensive service planning

### HARD Assessment (25-40 minutes)
- 100-120 questions across 12 sections
- Full housing timeline, detailed health assessment
- Best for individual case planning

## 🎨 Customization

### Adding Custom Widgets

1. Log in to Admin Portal
2. Navigate to "Widgets" section
3. Each widget has a checkbox to display on landing page
4. Widgets update in real-time based on assessment data

### Graphics and Images

The application includes placeholder descriptions for images. To add actual images:

1. Place images in the `/images/` directory
2. Update image references in HTML files
3. Recommended images:
   - **OUTSINC Logo**: Organization icon (suggested: 200x200px)
   - **Hero Image**: Community outreach scene showing diverse people
   - **Background Images**: Cobourg landmarks or community scenes

### Styling

Edit `/css/style.css` to customize:
- Color scheme (CSS variables at top of file)
- Fonts and typography
- Layout and spacing
- Mobile responsiveness

## 🔧 Troubleshooting

### Database Connection Issues

If you see "Database connection failed":
1. Verify MySQL is running
2. Check database credentials in `config/config.php`
3. Ensure database exists and schema is imported
4. Verify PHP PDO MySQL extension is installed

### Admin Login Not Working

- Confirm passcode is `079777`
- Check browser console for JavaScript errors
- Ensure PHP sessions are enabled

### AJAX Requests Failing

- Check that `php/api.php` is accessible
- Verify PHP error logs for server-side errors
- Ensure proper file permissions

## 📱 Browser Support

- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ⚠️ IE 11 (limited support)

## 🔒 Security Notes

- Change the default admin passcode in `config/config.php`
- Use HTTPS in production environments
- Regularly backup the database
- Keep PHP and MySQL updated
- Sanitize all user inputs (already implemented)
- Use prepared statements for SQL queries (already implemented)

## 📄 License

This project is created for OUTSINC (Outreach Services, Inc.) to support homelessness services in Cobourg and Northumberland County, Ontario.

## 🤝 Support

For issues, questions, or feature requests related to this PIT Count application:
- Email: info@outsinc.ca
- Organization: OUTSINC

## 🙏 Acknowledgments

Created to support the 2026 warming room planning for Cobourg and Northumberland County, in partnership with Northumberland County services.

---

**Note**: This application handles sensitive personal information. Always ensure compliance with privacy laws and regulations, including Canada's Personal Information Protection and Electronic Documents Act (PIPEDA).
