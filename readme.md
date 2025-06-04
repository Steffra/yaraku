# Yaraku Coding Solution

**Live demo:** [https://yaraku.app](https://yaraku.app)

## Setup Locally

### Prerequisites

- [Docker](https://docs.docker.com/install)
- [Docker Compose](https://docs.docker.com/compose/install)

### Installation

Clone the repository and bring the stack up:

```bash
git clone https://github.com/Steffra/yaraku.git bertold_krausz_yaraku_assignment
cd bertold_krausz_yaraku_assignment
docker-compose up -d
docker-compose exec laravel bash setup.sh
```
When the script finishes the UI is available at [localhost:5173](http://localhost:5173).

## Project Overview

### Infrastructure

The application is containerised with **docker‑compose**.  
In addition to the pre‑provided services, a dedicated **frontend** image was added so the Vue client runs in its own container.

A demo instance is hosted on a DigitalOcean droplet behind a Caddy reverse proxy.  
The domain **yaraku.app** was purchased solely for this assignment.

### Stack

- A  **Laravel** backend exposing a **REST API**
- A **Vue 3** frontend powered by **Vite**

## Testing

### Backend

```bash
docker-compose exec laravel php vendor/bin/phpunit
```
Add `--coverage-html coverage-report` to generate a coverage report.

### Frontend 
```bash
docker-compose exec frontend npx vitest run
```

## Deployment
After provisioning a server with a public IP, opening port 443, registering a domain, and installing SSL certificates, deploy the stack using the **same command as during installation**.

The frontend listens on port 5173; point your reverse proxy (e.g. Nginx, Caddy, Traefik) to that port.