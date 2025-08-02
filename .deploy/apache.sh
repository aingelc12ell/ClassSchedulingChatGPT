#!/bin/bash

# CONFIG
APP_DIR="/var/www/school-scheduler"
APP_URL="http://localhost"
APP_PORT="80"
DB_NAME="school_schedule"
DB_USER="scheduler_user"
DB_PASS="StrongPassword123"
JWT_SECRET="your-super-secret-key"

echo "🚀 Starting Deployment..."

# Update and install required packages
sudo apt update && sudo apt install -y apache2 mysql-server php php-cli php-mbstring php-xml php-mysql composer unzip curl

# Clone project repository (replace with your repo URL)
if [ ! -d "$APP_DIR" ]; then
    sudo git clone https://github.com/aingelc12ell/ClassSchedulingChatGPT.git "$APP_DIR"
else
    echo "📁 Project directory already exists. Pulling latest changes..."
    cd "$APP_DIR" && sudo git pull
fi

cd "$APP_DIR"

# Composer install
composer install

# Environment Configuration
if [ ! -f ".env" ]; then
    echo "Creating .env file..."
    cat <<EOL > .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=$DB_NAME
DB_USERNAME=$DB_USER
DB_PASSWORD=$DB_PASS
JWT_SECRET=$JWT_SECRET
EOL
fi

# Database Setup
sudo mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;"
sudo mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';"
sudo mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"

# Run Migrations & Seeders (adjust script to your schema.sql file)
echo "🏗️ Importing Database Schema..."
mysql -u$DB_USER -p$DB_PASS $DB_NAME < ./database/schema.sql

# Apache Config
APACHE_CONF="/etc/apache2/sites-available/scheduler.conf"
if [ ! -f "$APACHE_CONF" ]; then
    echo "Setting up Apache configuration..."
    sudo bash -c "cat <<EOL > $APACHE_CONF
<VirtualHost $APP_URL:$APP_PORT>
    DocumentRoot $APP_DIR/public
    <Directory $APP_DIR/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOL"
    sudo a2ensite scheduler.conf
    sudo a2enmod rewrite
fi

# Restart Apache
sudo systemctl restart apache2

echo "🎉 Deployment Completed. Visit http://localhost to access the system."
