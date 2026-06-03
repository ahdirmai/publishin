# Server Setup — publishin.ahdirmai.id

## Architecture

```
Internet → HTTPS → Cloudflare Edge
                       ↓ (encrypted tunnel)
               cloudflared daemon (server)
                       ↓
               localhost:8003
                       ↓
               Nginx container (port 80 internal)
                       ↓
               app container (PHP-FPM :9000)
```

Cloudflare handles SSL/TLS — no Let's Encrypt needed on the server.

---

## Prerequisites

- Ubuntu 22.04+ (or Debian 12)
- Docker Engine 24+
- Docker Compose plugin v2
- `cloudflared` installed and authenticated
- Git

---

## Step 1 — Install Docker

```bash
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker $USER
newgrp docker
```

---

## Step 2 — Install cloudflared

```bash
curl -L --output cloudflared.deb \
  https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64.deb
sudo dpkg -i cloudflared.deb
rm cloudflared.deb

# Authenticate (opens browser — run on local machine with SSH tunnel if headless)
cloudflared tunnel login
```

---

## Step 3 — Create Cloudflare Tunnel

```bash
cloudflared tunnel create publishin

# Note the tunnel UUID from output, e.g.: abc123-xxxx-xxxx-xxxx-xxxxxxxxxxxx
```

Create tunnel config at `~/.cloudflared/config.yml`:

```yaml
tunnel: <TUNNEL_UUID>
credentials-file: /root/.cloudflared/<TUNNEL_UUID>.json

ingress:
  - hostname: publishin.ahdirmai.id
    service: http://localhost:8003
  - service: http_status:404
```

Route DNS:

```bash
cloudflared tunnel route dns publishin publishin.ahdirmai.id
```

---

## Step 4 — Clone Repository

```bash
cd /opt
git clone https://github.com/ahdirmai/publishin.git
cd publishin
```

---

## Step 5 — Configure Environment

```bash
cp .env.example .env
nano .env
```

Minimum required values for production:

```dotenv
APP_ENV=production
APP_KEY=                        # generate: php artisan key:generate --show
APP_DEBUG=false
APP_URL=https://publishin.ahdirmai.id

DB_HOST=mysql
DB_DATABASE=publishin
DB_USERNAME=publishin
DB_PASSWORD=<strong-password>
DB_ROOT_PASSWORD=<strong-root-password>

SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
CACHE_STORE=redis

REDIS_HOST=redis

REVERB_APP_ID=<random-id>
REVERB_APP_KEY=<random-key>
REVERB_APP_SECRET=<random-secret>
REVERB_HOST=publishin.ahdirmai.id
REVERB_PORT=443
REVERB_SCHEME=https

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

# Social OAuth
INSTAGRAM_CLIENT_ID=
INSTAGRAM_CLIENT_SECRET=
THREADS_APP_ID=
THREADS_APP_SECRET=
TIKTOK_CLIENT_KEY=
TIKTOK_CLIENT_SECRET=

ANTHROPIC_API_KEY=
```

Generate APP_KEY:

```bash
docker run --rm php:8.3-cli php -r "echo 'base64:' . base64_encode(random_bytes(32)) . PHP_EOL;"
```

---

## Step 6 — Build & Start Containers

```bash
cd /opt/publishin

# Build images
docker compose build

# Start all services
docker compose up -d

# Verify all containers running
docker compose ps
```

Expected output — all containers `Up`:
- `publishin_app`
- `publishin_nginx`
- `publishin_mysql` (healthy)
- `publishin_redis`
- `publishin_queue`
- `publishin_scheduler`
- `publishin_reverb`

---

## Step 7 — First-Time Application Setup

```bash
# Storage symlink
docker compose exec app php artisan storage:link

# Run migrations
docker compose exec app php artisan migrate --force

# Cache config/routes/views
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

---

## Step 8 — Start Cloudflare Tunnel

```bash
# Test first
cloudflared tunnel run publishin

# Install as systemd service
sudo cloudflared service install
sudo systemctl enable cloudflared
sudo systemctl start cloudflared
sudo systemctl status cloudflared
```

Visit `https://publishin.ahdirmai.id` — should load the app over HTTPS.

---

## Deploy Updates

```bash
cd /opt/publishin
git pull origin main

# If composer.json changed
docker compose exec app composer install --no-dev --optimize-autoloader

# Run migrations
docker compose exec app php artisan migrate --force

# Clear & rebuild caches
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

# Restart queue workers
docker compose exec app php artisan queue:restart

# If Dockerfile changed (rarely)
docker compose build app queue scheduler reverb
docker compose up -d
```

---

## Useful Commands

```bash
# View logs
docker compose logs -f app
docker compose logs -f nginx
docker compose logs -f queue

# MySQL shell
docker compose exec mysql mysql -u publishin -p publishin

# Redis CLI
docker compose exec redis redis-cli

# Artisan
docker compose exec app php artisan <command>

# Restart single service
docker compose restart queue

# Full restart
docker compose down && docker compose up -d
```

---

## Troubleshooting

### App returns 502 Bad Gateway
```bash
docker compose ps          # Check app container is Up
docker compose logs app    # Check PHP-FPM errors
```

### Cloudflare Tunnel not routing
```bash
sudo systemctl status cloudflared
cloudflared tunnel info publishin
```

### Queue jobs not processing
```bash
docker compose logs queue
docker compose restart queue
docker compose exec app php artisan queue:restart
```

### "HTTPS" URLs are HTTP in app
Ensure `bootstrap/app.php` has `$middleware->trustProxies(at: '*')` — already set.
Also verify `fastcgi_param HTTPS on` in `docker/nginx/default.conf` — already set.

### Database connection refused
```bash
docker compose ps mysql          # Must show (healthy)
docker compose logs mysql        # Check init errors
```

---

## File Structure Reference

```
/opt/publishin/
├── docker-compose.yml
├── .env                         # NOT in git — copy from .env.example
├── docker/
│   ├── Dockerfile
│   ├── nginx/default.conf
│   └── mysql/my.cnf
├── docs/
│   └── SERVER_SETUP.md          # This file
└── CLAUDE.md                    # Claude Code project context
```

---

## Reverb WebSocket (Real-time)

Reverb runs internally on port 8080. Clients connect via:
- `wss://publishin.ahdirmai.id/app` (Cloudflare proxies WebSocket upgrade → Nginx → Reverb)

Nginx config already has WebSocket proxy at `/app`. Cloudflare must have **WebSocket** enabled for the tunnel hostname — check Cloudflare dashboard → Network → WebSockets.
