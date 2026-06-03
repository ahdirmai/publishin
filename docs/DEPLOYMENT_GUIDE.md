# Deployment Guide
## Publishin — Infrastructure & Deployment (Home Server)
### Wireframe Reference: `kontentku-wireframe.html`

**Last Updated:** 2026-06-03  
**Infrastructure:** Home Server (Self-hosted)  
**Stack:** Docker Compose + Nginx + MySQL + Redis  

---

## 1. Server Requirements

### 1.1 Minimum Specs (Development/Staging)
| Komponen | Minimum |
|---|---|
| CPU | 4 cores |
| RAM | 8 GB |
| Storage | 100 GB SSD |
| Network | 100 Mbps upload |
| OS | Ubuntu 22.04 LTS |

### 1.2 Recommended Specs (Production)
| Komponen | Recommended |
|---|---|
| CPU | 8 cores |
| RAM | 16 GB |
| Storage | 500 GB SSD + 1TB HDD (backup) |
| Network | 500 Mbps upload + static IP |
| OS | Ubuntu 22.04 LTS |

### 1.3 Required Ports
| Port | Service | Keterangan |
|---|---|---|
| 80 | Nginx | HTTP (redirect ke HTTPS) |
| 443 | Nginx | HTTPS |
| 3306 | MySQL | Internal only |
| 6379 | Redis | Internal only |
| 8080 | Horizon | Internal, akses via proxy |
| 6001 | Reverb | WebSocket |
| 22 | SSH | Admin access |

---

## 2. Prerequisites

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker $USER

# Install Docker Compose
sudo apt install docker-compose-plugin -y

# Install Nginx (reverse proxy di host, bukan container)
sudo apt install nginx -y

# Install Certbot (Let's Encrypt SSL)
sudo apt install certbot python3-certbot-nginx -y

# Install Git
sudo apt install git -y
```

---

## 3. Project Structure

```
/opt/publishin/
├── docker-compose.yml
├── docker-compose.staging.yml
├── .env
├── .env.staging
├── nginx/
│   ├── conf.d/
│   │   ├── publishin.conf
│   │   └── publishin-websocket.conf
│   └── ssl/
├── app/                    ← Laravel application code
├── storage/                ← Persistent storage (mounted volume)
│   ├── app/
│   ├── logs/
│   └── framework/
├── mysql/
│   ├── data/               ← MySQL data volume
│   └── conf.d/
│       └── my.cnf
├── redis/
│   └── data/
├── scripts/
│   ├── deploy.sh
│   ├── backup.sh
│   └── rollback.sh
└── backups/
```

---

## 4. Docker Compose

### 4.1 docker-compose.yml (Production)

```yaml
# /opt/publishin/docker-compose.yml
version: '3.8'

services:
  app:
    build:
      context: ./app
      dockerfile: Dockerfile
      target: production
    container_name: publishin_app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./app:/var/www
      - ./storage:/var/www/storage
    environment:
      - PHP_MEMORY_LIMIT=256M
      - PHP_MAX_EXECUTION_TIME=60
    depends_on:
      mysql:
        condition: service_healthy
      redis:
        condition: service_healthy
    networks:
      - publishin_network

  nginx:
    image: nginx:1.25-alpine
    container_name: publishin_nginx
    restart: unless-stopped
    ports:
      - "9000:80"           # Nginx di container; host Nginx proxy ke sini
    volumes:
      - ./app:/var/www
      - ./nginx/conf.d:/etc/nginx/conf.d
    depends_on:
      - app
    networks:
      - publishin_network

  mysql:
    image: mysql:8.0
    container_name: publishin_mysql
    restart: unless-stopped
    volumes:
      - ./mysql/data:/var/lib/mysql
      - ./mysql/conf.d:/etc/mysql/conf.d
    environment:
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_USER: ${DB_USERNAME}
      MYSQL_PASSWORD: ${DB_PASSWORD}
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 10s
      retries: 5
    networks:
      - publishin_network

  redis:
    image: redis:7-alpine
    container_name: publishin_redis
    restart: unless-stopped
    command: redis-server --appendonly yes --requirepass ${REDIS_PASSWORD}
    volumes:
      - ./redis/data:/data
    healthcheck:
      test: ["CMD", "redis-cli", "ping"]
      interval: 10s
    networks:
      - publishin_network

  queue-worker:
    build:
      context: ./app
      dockerfile: Dockerfile
      target: production
    container_name: publishin_queue
    restart: unless-stopped
    command: php artisan horizon
    working_dir: /var/www
    volumes:
      - ./app:/var/www
      - ./storage:/var/www/storage
    depends_on:
      - mysql
      - redis
    networks:
      - publishin_network

  scheduler:
    build:
      context: ./app
      dockerfile: Dockerfile
      target: production
    container_name: publishin_scheduler
    restart: unless-stopped
    command: php artisan schedule:work
    working_dir: /var/www
    volumes:
      - ./app:/var/www
    depends_on:
      - mysql
      - redis
    networks:
      - publishin_network

  reverb:
    build:
      context: ./app
      dockerfile: Dockerfile
      target: production
    container_name: publishin_reverb
    restart: unless-stopped
    command: php artisan reverb:start --host=0.0.0.0 --port=8080
    ports:
      - "8080:8080"
    working_dir: /var/www
    volumes:
      - ./app:/var/www
    depends_on:
      - redis
    networks:
      - publishin_network

networks:
  publishin_network:
    driver: bridge
```

### 4.2 Dockerfile

```dockerfile
# app/Dockerfile
FROM php:8.3-fpm-alpine AS base

RUN apk add --no-cache \
    nginx \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    unzip \
    ffmpeg \
    nodejs \
    npm

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        gd \
        exif \
        pcntl \
        bcmath \
        opcache

# Redis extension
RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# ---- Development stage ----
FROM base AS development
ENV APP_ENV=local

# ---- Production stage ----
FROM base AS production
ENV APP_ENV=production

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm ci && npm run build
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
```

### 4.3 Nginx Container Config

```nginx
# nginx/conf.d/app.conf
server {
    listen 80;
    server_name _;
    root /var/www/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    client_max_body_size 512M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        fastcgi_buffers 8 16k;
        fastcgi_buffer_size 32k;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 5. Host Nginx (Reverse Proxy + SSL)

```nginx
# /etc/nginx/sites-available/publishin
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;

    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256;

    # Security headers
    add_header Strict-Transport-Security "max-age=63072000; includeSubDomains" always;
    add_header X-Frame-Options DENY;
    add_header X-Content-Type-Options nosniff;

    # Main app
    location / {
        proxy_pass http://127.0.0.1:9000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 120s;
        proxy_connect_timeout 30s;
        client_max_body_size 512M;
    }

    # WebSocket (Laravel Reverb)
    location /app/ {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_read_timeout 86400s;
    }

    # Laravel Horizon dashboard (IP whitelist)
    location /horizon {
        allow 192.168.1.0/24;     # Local network only
        deny all;
        proxy_pass http://127.0.0.1:9000;
        proxy_set_header Host $host;
        proxy_set_header X-Forwarded-Proto $scheme;
    }

    # Static assets cache (termasuk SVG hatch pattern assets dari wireframe design)
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|woff2)$ {
        proxy_pass http://127.0.0.1:9000;
        proxy_cache_valid 200 30d;
        add_header Cache-Control "public, max-age=2592000";
    }
    # Note: SVG hatch fill patterns (h-r, h-b, h-g, h-k) diinline di AppLayout.vue
    # sebagai SVG <defs> — tidak perlu file terpisah
}
```

```bash
# Enable site dan test SSL
sudo ln -s /etc/nginx/sites-available/publishin /etc/nginx/sites-enabled/
sudo nginx -t

# Get SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renew (add ke crontab)
0 3 * * * /usr/bin/certbot renew --quiet && systemctl reload nginx
```

---

## 6. Environment Configuration

### 6.1 .env (Production)

```dotenv
APP_NAME="Publishin"
APP_ENV=production
APP_KEY=                             # php artisan key:generate
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=mysql                        # Docker container name
DB_PORT=3306
DB_DATABASE=publishin_production
DB_USERNAME=publishin
DB_PASSWORD=STRONG_PASSWORD_HERE
DB_ROOT_PASSWORD=STRONG_ROOT_PASSWORD

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=redis
REDIS_PASSWORD=REDIS_PASSWORD_HERE
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Publishin"

# Storage
FILESYSTEM_DISK=local
MEDIA_DISK=public

# Platform APIs
INSTAGRAM_APP_ID=
INSTAGRAM_APP_SECRET=
FACEBOOK_APP_ID=
FACEBOOK_APP_SECRET=
TIKTOK_CLIENT_KEY=
TIKTOK_CLIENT_SECRET=
TWITTER_CLIENT_ID=
TWITTER_CLIENT_SECRET=
YOUTUBE_CLIENT_ID=
YOUTUBE_CLIENT_SECRET=

# AI
ANTHROPIC_API_KEY=

# Payment
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=true

# Reverb WebSocket
REVERB_APP_ID=publishin-prod
REVERB_APP_KEY=
REVERB_APP_SECRET=
REVERB_HOST=reverb
REVERB_PORT=8080

# Security
ENCRYPTION_KEY=                      # Untuk encrypt OAuth tokens, 32 chars
```

### 6.2 MySQL Config

```ini
# mysql/conf.d/my.cnf
[mysqld]
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci
default-authentication-plugin = mysql_native_password

# Performance
innodb_buffer_pool_size = 2G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
query_cache_type = 0
max_connections = 200
tmp_table_size = 64M
max_heap_table_size = 64M

# Slow query log
slow_query_log = 1
slow_query_log_file = /var/lib/mysql/slow.log
long_query_time = 2

[client]
default-character-set = utf8mb4
```

---

## 7. Database Migration Strategy

### 7.1 Initial Setup

```bash
# Create database & user
docker exec -it publishin_mysql mysql -u root -p -e "
  CREATE DATABASE publishin_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  CREATE USER 'publishin'@'%' IDENTIFIED BY 'STRONG_PASSWORD';
  GRANT ALL PRIVILEGES ON publishin_production.* TO 'publishin'@'%';
  FLUSH PRIVILEGES;
"

# Run migrations
docker exec publishin_app php artisan migrate --force

# Seed subscription plans
docker exec publishin_app php artisan db:seed --class=SubscriptionPlanSeeder
```

### 7.2 Migration Workflow

```bash
# Sebelum migration ke production:
# 1. Test di staging terlebih dahulu
# 2. Backup production DB
# 3. Cek apakah migration destructive (DROP, RENAME)
# 4. Untuk migration besar: gunakan --pretend dulu

docker exec publishin_app php artisan migrate --pretend  # Dry run
docker exec publishin_app php artisan migrate --force    # Actual run
```

### 7.3 Zero-Downtime Migrations

Untuk tabel dengan data besar (>100K rows), gunakan pendekatan:
1. Tambah kolom nullable dulu
2. Backfill data secara batch via job
3. Set NOT NULL constraint setelah data terisi
4. Drop kolom lama

```php
// Gunakan chunk untuk update besar
Post::chunk(1000, function ($posts) {
    foreach ($posts as $post) {
        $post->updateQuietly(['new_column' => ...]);
    }
});
```

---

## 8. CI/CD Pipeline (GitHub Actions)

```yaml
# .github/workflows/deploy.yml
name: Deploy to Production

on:
  push:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_DATABASE: publishin_test
          MYSQL_ROOT_PASSWORD: password
        ports: ['3306:3306']
      redis:
        image: redis:7
        ports: ['6379:6379']

    steps:
      - uses: actions/checkout@v4

      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          extensions: pdo_mysql,redis

      - run: composer install --no-dev
      - run: cp .env.testing .env && php artisan key:generate
      - run: php artisan migrate --force
      - run: ./vendor/bin/pest --parallel

      - uses: actions/setup-node@v4
        with: { node-version: '20' }
      - run: npm ci && npm run test:unit

  deploy:
    needs: test
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'

    steps:
      - uses: actions/checkout@v4

      - name: Deploy via SSH
        uses: appleboy/ssh-action@v1
        with:
          host: ${{ secrets.SERVER_HOST }}
          username: ${{ secrets.SERVER_USER }}
          key: ${{ secrets.SSH_PRIVATE_KEY }}
          script: |
            cd /opt/publishin
            bash scripts/deploy.sh

  notify:
    needs: deploy
    runs-on: ubuntu-latest
    if: always()
    steps:
      - name: Notify deployment result
        uses: 8398a7/action-slack@v3
        with:
          status: ${{ job.status }}
          text: "Deploy Publishin: ${{ job.status }}"
        env:
          SLACK_WEBHOOK_URL: ${{ secrets.SLACK_WEBHOOK }}
```

### 8.1 Deploy Script

```bash
#!/bin/bash
# scripts/deploy.sh

set -e

echo "=== Publishin Deployment - $(date) ==="
cd /opt/publishin

# Pull latest code
git pull origin main

# Build new Docker image
docker compose build app

# Enable maintenance mode
docker exec publishin_app php artisan down --secret="YOUR_BYPASS_SECRET"

# Run migrations
docker exec publishin_app php artisan migrate --force

# Clear & warm caches
docker exec publishin_app php artisan config:cache
docker exec publishin_app php artisan route:cache
docker exec publishin_app php artisan view:cache
docker exec publishin_app php artisan event:cache
docker exec publishin_app php artisan optimize

# Restart services
docker compose up -d --no-deps --remove-orphans app queue-worker scheduler reverb

# Disable maintenance mode
docker exec publishin_app php artisan up

echo "=== Deployment complete ==="
```

---

## 9. Environments

### 9.1 Development (Local)

```bash
# Clone repo
git clone https://github.com/yourorg/publishin.git
cd publishin

# Setup .env
cp .env.example .env.local
# Edit .env.local dengan credentials lokal

# Start containers
docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d

# Setup aplikasi
docker exec publishin_app composer install
docker exec publishin_app php artisan key:generate
docker exec publishin_app php artisan migrate --seed
docker exec publishin_app npm install && npm run dev
```

### 9.2 Staging

- Subdomain: `staging.yourdomain.com`
- Database: `publishin_staging` (terpisah dari production)
- Deploy trigger: push ke branch `staging`
- Data: anonymized dump dari production (tanpa token asli)
- Auto-reset setiap Minggu (jalankan fresh seed)

### 9.3 Production

- Domain: `yourdomain.com`
- Deploy trigger: push ke branch `main` setelah CI pass
- Database backup: sebelum setiap deploy
- Zero-downtime: maintenance mode + rolling restart

---

## 10. Monitoring & Logging

### 10.1 Laravel Telescope (Dev/Staging only)

```php
// config/telescope.php
'enabled' => env('TELESCOPE_ENABLED', false),
```

### 10.2 Laravel Horizon (Production)

Akses: `https://yourdomain.com/horizon` (IP whitelist)

Monitor:
- Queue throughput (posts per menit)
- Failed jobs
- Job latency
- Redis memory usage

### 10.3 Log Management

```php
// config/logging.php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['daily', 'slack-errors'],
    ],
    'daily' => [
        'driver' => 'daily',
        'path' => storage_path('logs/laravel.log'),
        'level' => 'warning',
        'days' => 14,
    ],
    'slack-errors' => [
        'driver' => 'slack',
        'url' => env('SLACK_LOG_WEBHOOK'),
        'username' => 'Publishin Error',
        'emoji' => ':boom:',
        'level' => 'error',
    ],
]
```

### 10.4 System Monitoring

```bash
# Install Netdata untuk real-time monitoring
bash <(curl -Ss https://my-netdata.io/kickstart.sh)

# Akses di: http://your-server-ip:19999
# Monitor: CPU, RAM, disk I/O, network, MySQL, Redis
```

### 10.5 Uptime Monitoring

Gunakan **UptimeRobot** (free tier) atau **self-hosted Uptime Kuma**:

```bash
# Uptime Kuma (self-hosted)
docker run -d --restart=always \
  -p 3001:3001 \
  -v uptime-kuma:/app/data \
  --name uptime-kuma \
  louislam/uptime-kuma:1

# Monitor endpoints:
# - https://yourdomain.com/health
# - WebSocket: reverb port
# - MySQL (TCP)
# - Redis (TCP)
```

**Health Check Endpoint:**
```php
// routes/web.php
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'database' => DB::connection()->getPdo() ? 'ok' : 'error',
        'redis' => Cache::store('redis')->get('health_check') !== null ? 'ok' : 'error',
        'queue' => Queue::size() < 1000 ? 'ok' : 'warning',
    ]);
});
```

---

## 11. Backup & Disaster Recovery

### 11.1 Automated Backup Script

```bash
#!/bin/bash
# scripts/backup.sh

BACKUP_DIR="/opt/publishin/backups"
DATE=$(date +%Y%m%d_%H%M%S)
RETENTION_DAYS=30

# Database backup
docker exec publishin_mysql mysqldump \
    -u root -p"${DB_ROOT_PASSWORD}" \
    --single-transaction \
    --routines \
    --triggers \
    publishin_production | gzip > "${BACKUP_DIR}/db_${DATE}.sql.gz"

# Storage backup (media files)
tar -czf "${BACKUP_DIR}/storage_${DATE}.tar.gz" \
    /opt/publishin/storage/app/public/

# .env backup (encrypted)
openssl enc -aes-256-cbc -in /opt/publishin/.env \
    -out "${BACKUP_DIR}/env_${DATE}.enc" \
    -k "${BACKUP_ENCRYPTION_KEY}"

# Remove old backups
find "${BACKUP_DIR}" -name "*.gz" -mtime +${RETENTION_DAYS} -delete
find "${BACKUP_DIR}" -name "*.enc" -mtime +${RETENTION_DAYS} -delete

echo "Backup completed: ${DATE}"
```

```bash
# Crontab untuk backup otomatis
# Backup harian jam 02:00 WIB
0 2 * * * /opt/publishin/scripts/backup.sh >> /var/log/publishin-backup.log 2>&1
```

### 11.2 Offsite Backup (Opsional)

```bash
# Sync ke external HDD atau cloud storage
rsync -avz --progress \
    /opt/publishin/backups/ \
    user@external-server:/backup/publishin/

# Atau ke rclone (Google Drive, S3, dsb)
rclone copy /opt/publishin/backups/ remote:publishin-backups
```

### 11.3 Recovery Procedure

```bash
# 1. Stop aplikasi
docker compose down

# 2. Restore database
gunzip < backups/db_YYYYMMDD_HHMMSS.sql.gz | \
    docker exec -i publishin_mysql mysql \
    -u root -p"${DB_ROOT_PASSWORD}" \
    publishin_production

# 3. Restore storage
tar -xzf backups/storage_YYYYMMDD_HHMMSS.tar.gz -C /

# 4. Restart
docker compose up -d
```

### 11.4 RTO & RPO Targets

| Skenario | RPO (Data Loss) | RTO (Recovery Time) |
|---|---|---|
| Server crash | < 24 jam | < 2 jam |
| Database corruption | < 24 jam | < 4 jam |
| Accidental data delete | < 24 jam | < 1 jam |
| Full server loss | < 24 jam | < 8 jam |

---

## 12. Security Hardening

```bash
# Firewall setup (UFW)
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable

# Fail2ban untuk SSH brute force
sudo apt install fail2ban -y
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local
# Edit jail.local: bantime=3600, maxretry=5

# Disable root SSH login
sudo sed -i 's/PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config
sudo systemctl restart ssh

# MySQL: hanya accessible dari localhost/Docker network
# Redis: password protected + bind 127.0.0.1

# Automatic security updates
sudo apt install unattended-upgrades -y
sudo dpkg-reconfigure -plow unattended-upgrades
```

---

## 13. Rollback Procedure

```bash
#!/bin/bash
# scripts/rollback.sh

# Lihat 3 deployment terakhir
git log --oneline -5

# Rollback ke commit tertentu
ROLLBACK_HASH=$1

if [ -z "$ROLLBACK_HASH" ]; then
  echo "Usage: rollback.sh <commit-hash>"
  exit 1
fi

echo "Rolling back to: $ROLLBACK_HASH"

# Checkout ke commit target
git checkout $ROLLBACK_HASH

# Rebuild dan restart
docker compose build app
docker exec publishin_app php artisan down
docker compose up -d --no-deps app queue-worker
docker exec publishin_app php artisan migrate --force
docker exec publishin_app php artisan optimize
docker exec publishin_app php artisan up

echo "Rollback complete"
```

---

## 14. Maintenance Procedures

### Weekly
- [ ] Cek ukuran log files (`storage/logs/`)
- [ ] Cek Horizon queue health
- [ ] Review failed jobs dan error rate
- [ ] Cek disk usage

### Monthly
- [ ] Update Docker images (minor versions)
- [ ] Review dan rotate API credentials platform
- [ ] Test restore dari backup
- [ ] Update SSL certificate jika perlu (auto via Certbot)
- [ ] Review slow query log MySQL

### Quarterly
- [ ] PHP & Composer dependency update
- [ ] npm dependency update + security audit
- [ ] Penetration testing ringan (OWASP ZAP)
- [ ] Review kapasitas server & scaling plan

---

*Dokumen ini diasumsikan untuk home server dengan koneksi internet stabil. Untuk high-availability production, pertimbangkan dedicated server atau VPS dengan SLA.*
