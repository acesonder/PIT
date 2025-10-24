# Deployment Guide - PIT Count Application

This guide provides step-by-step instructions for deploying the PIT Count application to a production server.

## Pre-Deployment Checklist

- [ ] MySQL database server is available
- [ ] Web server (Apache/Nginx) with PHP 7.4+ is installed
- [ ] SSL certificate for HTTPS (recommended for production)
- [ ] Domain name or server IP address
- [ ] Backup strategy in place

## Quick Deployment (Using Setup Script)

### Option 1: Automated Setup

1. Upload all files to your web server
2. SSH into your server
3. Navigate to the application directory
4. Run the setup script:

```bash
cd /path/to/PIT
chmod +x setup.sh
./setup.sh
```

5. Follow the prompts to configure database connection
6. Configure your web server (see below)

### Option 2: Manual Setup

Follow these steps if the automated script doesn't work for your environment.

## Step-by-Step Manual Deployment

### 1. Upload Files

Upload all application files to your web server:

```bash
# Using SCP
scp -r PIT/* user@yourserver.com:/var/www/html/pit/

# Or using SFTP
sftp user@yourserver.com
put -r PIT/* /var/www/html/pit/
```

### 2. Create Database

SSH into your server and create the database:

```bash
mysql -u root -p
```

```sql
CREATE DATABASE pit_count CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'pit_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON pit_count.* TO 'pit_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Import Database Schema

```bash
mysql -u pit_user -p pit_count < /var/www/html/pit/database/schema.sql
```

### 4. Configure Application

Edit the configuration file:

```bash
nano /var/www/html/pit/config/config.php
```

Update these values:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'pit_user');
define('DB_PASS', 'secure_password_here');
define('DB_NAME', 'pit_count');
```

**Important**: Change the admin passcode from default:

```php
define('ADMIN_PASSCODE', 'your_secure_passcode');
```

### 5. Set File Permissions

```bash
cd /var/www/html/pit
chmod -R 755 .
chmod 644 config/config.php
mkdir -p uploads
chmod 755 uploads
chown -R www-data:www-data .
```

### 6. Configure Web Server

#### For Apache

Create a virtual host configuration:

```bash
sudo nano /etc/apache2/sites-available/pit.conf
```

```apache
<VirtualHost *:80>
    ServerName pit.yourdomain.com
    ServerAlias www.pit.yourdomain.com
    DocumentRoot /var/www/html/pit
    
    <Directory /var/www/html/pit>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/pit-error.log
    CustomLog ${APACHE_LOG_DIR}/pit-access.log combined
</VirtualHost>
```

Enable the site:

```bash
sudo a2ensite pit.conf
sudo systemctl reload apache2
```

#### For Nginx

Create a server block:

```bash
sudo nano /etc/nginx/sites-available/pit
```

```nginx
server {
    listen 80;
    server_name pit.yourdomain.com www.pit.yourdomain.com;
    root /var/www/html/pit;
    index index.html index.php;

    location / {
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    location ~ /config/ {
        deny all;
    }

    access_log /var/log/nginx/pit-access.log;
    error_log /var/log/nginx/pit-error.log;
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/pit /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 7. Enable HTTPS (Recommended)

Using Let's Encrypt (free SSL certificate):

```bash
sudo apt install certbot python3-certbot-apache  # For Apache
# OR
sudo apt install certbot python3-certbot-nginx   # For Nginx

sudo certbot --apache  # For Apache
# OR
sudo certbot --nginx   # For Nginx
```

Follow the prompts to set up SSL.

### 8. Test Installation

Visit the test page to verify everything is working:

```
https://pit.yourdomain.com/test.php
```

Check all green checkmarks appear. If any issues:
- Check database connection
- Verify file permissions
- Review web server error logs

### 9. Security Hardening

#### A. Update PHP Configuration

Edit `php.ini` (location varies by system):

```bash
sudo nano /etc/php/7.4/apache2/php.ini  # For Apache
# OR
sudo nano /etc/php/7.4/fpm/php.ini      # For Nginx
```

Recommended settings:

```ini
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log
upload_max_filesize = 5M
post_max_size = 6M
max_execution_time = 30
session.cookie_httponly = 1
session.cookie_secure = 1
```

#### B. Protect Sensitive Files

Create `.htaccess` (for Apache) in root directory:

```apache
# Deny access to sensitive files
<FilesMatch "^\.">
    Require all denied
</FilesMatch>

# Protect config directory
<Directory "config">
    Require all denied
</Directory>

# Protect database directory
<Directory "database">
    Require all denied
</Directory>
```

For Nginx, these protections are in the server block above.

#### C. Set Up Database Backups

Create a backup script:

```bash
sudo nano /usr/local/bin/backup-pit.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/pit"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u pit_user -p'secure_password_here' pit_count | gzip > $BACKUP_DIR/pit_db_$DATE.sql.gz

# Backup uploads
tar -czf $BACKUP_DIR/pit_uploads_$DATE.tar.gz /var/www/html/pit/uploads/

# Keep only last 30 days of backups
find $BACKUP_DIR -name "pit_*.gz" -mtime +30 -delete
```

Make it executable and schedule with cron:

```bash
sudo chmod +x /usr/local/bin/backup-pit.sh
sudo crontab -e
```

Add daily backup at 2 AM:

```
0 2 * * * /usr/local/bin/backup-pit.sh
```

### 10. Configure Firewall

```bash
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 22/tcp  # SSH
sudo ufw enable
```

### 11. Set Up Monitoring (Optional)

Consider setting up monitoring for:
- Server uptime
- Database connectivity
- Disk space
- Error logs

Tools: Nagios, Zabbix, or simple scripts with cron.

## Post-Deployment

### 1. Change Default Credentials

- [ ] Admin passcode changed from `079777`
- [ ] Database password is strong and unique
- [ ] Any default staff accounts reviewed

### 2. Initial Data Verification

1. Log into admin portal
2. Verify default staff members are loaded
3. Check that widgets are configured
4. Test creating a sample assessment
5. Verify data appears on landing page

### 3. Train Staff

Provide training to outreach staff on:
- Starting new assessments
- Client duplicate checking
- Consent procedures
- Completing assessment forms
- Using skip/prefer not to say options

### 4. Documentation for Staff

Create a quick reference guide:
- Admin passcode
- Support contact information
- Common troubleshooting steps

## Troubleshooting

### Database Connection Issues

```bash
# Check MySQL is running
sudo systemctl status mysql

# Check database exists
mysql -u pit_user -p -e "SHOW DATABASES;"

# Check user permissions
mysql -u root -p -e "SHOW GRANTS FOR 'pit_user'@'localhost';"
```

### PHP Errors

```bash
# Apache error log
sudo tail -f /var/log/apache2/pit-error.log

# Nginx error log
sudo tail -f /var/log/nginx/pit-error.log

# PHP error log
sudo tail -f /var/log/php_errors.log
```

### Permissions Issues

```bash
# Reset permissions
sudo chown -R www-data:www-data /var/www/html/pit
sudo chmod -R 755 /var/www/html/pit
sudo chmod 644 /var/www/html/pit/config/config.php
```

### Clear Sessions (if login issues)

```bash
# Find and clear PHP sessions
sudo rm /var/lib/php/sessions/sess_*
```

## Maintenance

### Regular Tasks

- [ ] **Daily**: Check error logs
- [ ] **Weekly**: Review database backups
- [ ] **Monthly**: Update PHP and system packages
- [ ] **Quarterly**: Review and archive old assessment data
- [ ] **Annually**: Review and update consent language

### Updates

To update the application:

1. Backup current installation
2. Upload new files
3. Check for database schema changes
4. Run any migration scripts
5. Clear cache if applicable
6. Test thoroughly

## Support

For deployment issues:
- Check `/test.php` for system diagnostics
- Review web server error logs
- Verify database connection
- Check file permissions

For application issues:
- Contact: info@outsinc.ca

## Compliance

Ensure compliance with:
- **PIPEDA** (Personal Information Protection and Electronic Documents Act)
- **Ontario privacy legislation**
- **Municipal data protection policies**

Regular privacy audits recommended.
