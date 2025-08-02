
# ClassSchedulingChatGPT

The Class Scheduling API using ChatGPT's output. Prompt is based on the cultured responses of Claude and Qwen.

A comprehensive modular scheduling system built with PHP (Slim Framework), MySQL, Docker, and FullCalendar.js. It supports automated class scheduling with conflict detection and manual overrides.

---

## Features
- RESTful API (Slim 4 + Eloquent ORM)
- JWT Authentication
- Automated Weekly Class Scheduling Engine
- Conflict Detection & Resolution
- Manual Override UI with FullCalendar
- Nginx + PHP-FPM + MySQL Dockerized Stack
- CI/CD with GitHub Actions
- Production-Ready Tuning (PHP-FPM, SSL, Scaling)

---

## Project Structure
```
/
├── src/                # Slim application source code
├── public/             # Public webroot (index.php)
├── db/
│   └── schema.sql      # DB schema & seed data
├── client/             # Frontend UI
├── nginx.conf          # Nginx configuration
├── Dockerfile          # PHP-FPM + Composer build
├── docker-compose.yml  # Docker services
├── .env.sample         # Environment variables (save as .env)
├── .github/workflows/  # CI/CD pipelines
├── .deploy/            # Developer scripts
├── client/             # Frontend UI
└── README.md           # This file

```

---

## Setup Instructions with Docker & Docker Compose v2+

### First-Time Setup
```bash
# Clone Repository
git clone https://github.com/aingelc12ell/ClassSchedulingChatGPT.git
cd scheduler

# Build and Run Docker Stack
docker-compose up --build -d

# Run Composer Install
docker exec -it classschedulingchatgpt composer install

# Import Database Schema
docker exec -i mysql_scheduler mysql -uscheduler_user -pStrongPassword123 school_schedule < ./database/schema.sql

# Generate JWT Secret Key
docker exec -it classschedulingchatgpt php -r "echo bin2hex(random_bytes(32));"
# Add the key to your .env file
```

### Access
- **API Base URL**: `http://localhost`
- **FullCalendar UI**: `http://localhost`

---

## API Endpoints
| Method | Endpoint                   | Description                        |
|--------|----------------------------|------------------------------------|
| GET    | /curriculums                | List all curriculums               |
| GET    | /classes                    | List all classes                   |
| GET    | /schedule/conflicts         | List scheduling conflicts          |
| POST   | /schedule/force             | Force override conflicting schedules |
| POST   | /classes                    | Create a new class                 |
| PUT    | /classes/{id}               | Update class schedule              |

Authentication: Pass JWT token in `Authorization: Bearer {token}` header.

---

## CI/CD (GitHub Actions)
- Automated build, test, and deploy pipeline on push to `main`.
- Located at: `.github/workflows/deploy.yml`

---

## Production Deployment Guide
1. Deploy on VPS/Cloud with Docker.
2. Secure with Nginx SSL (Let's Encrypt Certbot).
3. Scale using Load Balancers (AWS ALB, Nginx).
4. Tuning PHP-FPM Pools for performance.
5. Automated DB backups & monitoring (Prometheus/Grafana).

Refer to the detailed **Production Deployment Strategy Guide** in documentation.

---

## Developer Scripts
| Script                 | Purpose                                  |
|------------------------|------------------------------------------|
| `./.deploy/apache.sh`  | Automates Apache + PHP setup             |
| `./.deploy/nginx.sh`   | Automates Nginx + PHP-FPM setup + tuning |
| `./docker-compose.yml` | Multi-service Docker orchestration       |

---

## Postman Collection
A Postman collection for API testing is provided in `/postman/SchoolScheduler.postman_collection.json`

---

## TODO (Advanced Next Steps)
- Implement GitOps Deployment (ArgoCD/Kubernetes)
- Enable Zero Downtime Deployments
- Add WebSocket Notifications for Real-time Conflict Updates

---

## License
GNU GENERAL PUBLIC LICENSE v3.0

---

## Author
Aingel Carbonell (https://github.com/aingelc12ell)
