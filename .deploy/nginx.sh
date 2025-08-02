#!/bin/bash

# CONFIG
APP_DIR="/var/www/school-scheduler"
DB_NAME="school_schedule"
DB_USER="scheduler_user"
DB_PASS="StrongPassword123"
JWT_SECRET="your-super-secret-key"
PHP_VERSION=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')

echo "🚀 Starting Nginx + PHP-FPM Deployment with Tuning..."

# Install Nginx, PHP-FPM, MySQL, Composer
sudo apt update && sudo apt install -y nginx mysql-server php-fpm php-cli php-mysql php-mbstring php-xml composer unzip curl

# Clone Project
if [ ! -d "$APP_DIR" ]; then
    sudo git clone https://github.com/aingelc12ell/ClassSchedulingChatGPT.git "$APP_DIR"
else
    echo "📁 Project directory exists. Pulling latest changes..."
    cd "$APP_DIR" && sudo git pull
fi

cd "$APP_DIR"

# Composer Install
composer install

# .env Setup
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
mysql -u$DB_USER -p$DB_PASS $DB_NAME < ./db/schema.sql

# Nginx Configuration
NGINX_CONF="/etc/nginx/sites-available/schedulerchatgpt"
if [ ! -f "$NGINX_CONF" ]; then
    echo "Setting up Nginx configuration..."
    sudo bash -c "cat <<EOL > $NGINX_CONF
server {
    listen 80;
    server_name _;

    root $APP_DIR/public;
    index index.php;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \\.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php$PHP_VERSION-fpm.sock;
    }

    location ~ /\\.ht {
        deny all;
    }

    location ~ ^/(fpm-status|ping)\$ {
        fastcgi_pass unix:/var/run/php/php$PHP_VERSION-fpm.sock;
        include snippets/fastcgi-php.conf;
        allow 127.0.0.1;
        deny all;
    }
}
EOL"
    sudo ln -s $NGINX_CONF /etc/nginx/sites-enabled/
fi

# Remove Default Site
sudo rm -f /etc/nginx/sites-enabled/default

# PHP-FPM Pool Tuning
FPM_POOL_CONF="/etc/php/$PHP_VERSION/fpm/pool.d/www.conf"
echo "🔧 Tuning PHP-FPM pool..."
sudo sed -i 's/^pm.max_children.*/pm.max_children = 20/' $FPM_POOL_CONF
sudo sed -i 's/^pm.start_servers.*/pm.start_servers = 5/' $FPM_POOL_CONF
sudo sed -i 's/^pm.min_spare_servers.*/pm.min_spare_servers = 3/' $FPM_POOL_CONF
sudo sed -i 's/^pm.max_spare_servers.*/pm.max_spare_servers = 10/' $FPM_POOL_CONF
sudo sed -i 's/^;?pm.max_requests.*/pm.max_requests = 500/' $FPM_POOL_CONF
sudo sed -i 's|^;?pm.status_path.*|pm.status_path = /fpm-status|' $FPM_POOL_CONF
sudo sed -i 's|^;?request_slowlog_timeout.*|request_slowlog_timeout = 5s|' $FPM_POOL_CONF
sudo sed -i 's|^;?slowlog.*|slowlog = /var/log/php-fpm-slow.log|' $FPM_POOL_CONF
sudo sed -i 's|^;?php_admin_value\\[memory_limit\\].*|php_admin_value[memory_limit] = 512M|' $FPM_POOL_CONF

# Restart Services
sudo systemctl restart php$PHP_VERSION-fpm
sudo systemctl reload nginx

echo "🎉 Deployment Completed with PHP-FPM Tuning. Access at http://localhost"
