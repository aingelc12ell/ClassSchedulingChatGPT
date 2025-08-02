# First-Time Setup Checklist

1. Install Docker & Docker Compose
```
sudo apt update && sudo apt install -y docker.io docker-compose
sudo systemctl enable docker
```
2. Build & Run Docker Containers
```
docker-compose up --build -d
```
Ensure services: app, nginx, mysql are running.
```
docker ps
```
2. Run Database Migrations / Import Schema

If schema.sql exists in /db/schema.sql:
```
docker exec -i mysql_scheduler mysql -uscheduler_user -pStrongPassword123 school_schedule < ./db/schema.sql
```

Alternatively, you can connect to MySQL and manually import if needed.

3. Generate JWT Secret Key (if not yet done)
```
docker exec -it classschedulingchatgpt php -r "echo bin2hex(random_bytes(32));"
```

Copy the output and update .env:
```
JWT_SECRET=your-generated-key
```
4. Composer Dependencies (Inside App Container)
```
docker exec -it classschedulingchatgpt composer install
```
5. Set Permissions (Optional but recommended)
```
docker exec -it classschedulingchatgpt chown -R www-data:www-data /var/www/html
docker exec -it classschedulingchatgpt chmod -R 755 /var/www/html
```
6. Test PHP-FPM Status Page (Optional Monitoring)

Ensure `/fpm-status` works:

Access: `http://localhost/fpm-status`

7. Test API Endpoints

Use the Postman Collection you created.

Ensure JWT Authentication works.

Test `/schedule/conflicts`, `/schedule/force`, and CRUD APIs.

8. Frontend UI Validation

Access: `http://localhost`

Ensure FullCalendar, Conflict Override Panel, and API integrations work seamlessly.

9. (Optional) Persistent Data Volumes Check

Ensure MySQL data persists even after container restarts:
```
docker-compose down
docker-compose up -d
```

Verify data still exists.

10. (Optional) Database Backup & Management
```
docker exec mysql_scheduler /usr/bin/mysqldump -u root --password=rootpassword school_schedule > backup.sql
```
11. (Optional) Enable SSL/TLS (Let's Encrypt)
```
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
```