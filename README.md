# Cubo

Personal life management dashboard. Built WP-first — each life area is a
self-contained WordPress plugin with custom Gutenberg blocks. Everything
managed through WP admin, served directly by WordPress.

## Stack

- **CMS**: WordPress + custom plugins (CPTs, meta fields, Gutenberg blocks)
- **Server**: PHP 8.3-FPM + Nginx + MySQL 8.0 (Docker)
- **Block tooling**: @wordpress/scripts (per plugin)

## Directory Structure

- `docroot/` — WordPress installation
- `nginx/` — Nginx configuration
- `Dockerfile` — PHP 8.3-FPM image with WP-CLI
- `docker-compose.yml` — MySQL + PHP + Nginx services

## Plugin Architecture

- `cubo-core` — always active, shared foundation (utilities, base classes, WP customizations)
- `cubo-work` — work area: portfolio + job application tracker
- `cubo-finances` — finance area: expenses dashboard
- `cubo-personal` — personal area: contact/family ledger

## Requirements

- Docker + Docker Compose
- Node.js + npm (per plugin, for block development)

## Running Locally

```bash
docker-compose up -d
```

- Site: http://localhost:8080
- WP Admin: http://localhost:8080/wp-admin

## WP-CLI

```bash
docker exec -it wp-cubo-php-1 bash
wp --path=/var/www/html <command>
```
