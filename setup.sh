#!/bin/bash

# PIT Count Application - Quick Setup Script
# This script helps set up the PIT Count application

echo "================================================"
echo "  PIT Count Application - Setup Script"
echo "  OUTSINC - Cobourg & Northumberland"
echo "================================================"
echo ""

# Check if MySQL is installed
if ! command -v mysql &> /dev/null; then
    echo "❌ Error: MySQL is not installed or not in PATH"
    echo "Please install MySQL and try again"
    exit 1
fi

echo "✓ MySQL found"

# Prompt for database credentials
echo ""
echo "Please enter your MySQL credentials:"
read -p "MySQL Host [localhost]: " DB_HOST
DB_HOST=${DB_HOST:-localhost}

read -p "MySQL Username [root]: " DB_USER
DB_USER=${DB_USER:-root}

read -sp "MySQL Password: " DB_PASS
echo ""

read -p "Database Name [pit_count]: " DB_NAME
DB_NAME=${DB_NAME:-pit_count}

# Create database
echo ""
echo "Creating database..."
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;" 2>/dev/null

if [ $? -eq 0 ]; then
    echo "✓ Database created successfully"
else
    echo "❌ Error creating database. Please check your credentials."
    exit 1
fi

# Import schema
echo "Importing database schema..."
mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < database/schema.sql 2>/dev/null

if [ $? -eq 0 ]; then
    echo "✓ Schema imported successfully"
else
    echo "❌ Error importing schema"
    exit 1
fi

# Update config file
echo "Updating configuration file..."
sed -i.bak "s/define('DB_HOST', 'localhost');/define('DB_HOST', '$DB_HOST');/" config/config.php
sed -i.bak "s/define('DB_USER', 'root');/define('DB_USER', '$DB_USER');/" config/config.php
sed -i.bak "s/define('DB_PASS', '');/define('DB_PASS', '$DB_PASS');/" config/config.php
sed -i.bak "s/define('DB_NAME', 'pit_count');/define('DB_NAME', '$DB_NAME');/" config/config.php

echo "✓ Configuration updated"

# Create necessary directories
echo "Creating directories..."
mkdir -p uploads
chmod 755 uploads

echo "✓ Directories created"

# Set permissions
echo "Setting permissions..."
chmod -R 755 .
chmod 644 config/config.php

echo "✓ Permissions set"

# Final instructions
echo ""
echo "================================================"
echo "  Setup Complete! ✓"
echo "================================================"
echo ""
echo "Next steps:"
echo "1. Configure your web server (Apache or Nginx)"
echo "2. Point document root to: $(pwd)"
echo "3. Access application at: http://localhost/PIT/"
echo "4. Admin login at: http://localhost/PIT/admin-login.html"
echo "   - Passcode: 079777"
echo ""
echo "Default staff members added:"
echo "  - Jordan, London, Chance, Deb, Jenni"
echo ""
echo "For more information, see README.md"
echo "================================================"
