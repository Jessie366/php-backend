# Strata Management Backend

PHP endpoints for the strata management site. Public endpoints accept contact and repair submissions; admin read endpoints require a server-side bearer token.

## Security Notice

The previous repository history contained a real Railway PostgreSQL password. Rotate that Railway database password immediately. Removing the current file contents is not enough because the exposed password remains in Git history.

## Environment Variables

Copy `.env.example` into your Railway/Vercel environment settings and replace every placeholder:

```text
PGHOST=postgres.railway.internal
PGPORT=5432
PGDATABASE=railway
PGUSER=postgres
PGPASSWORD=<rotated Railway password>
ALLOWED_ORIGINS=https://your-frontend-domain.example
ADMIN_API_TOKEN=<long random token used by the frontend server>
```

`ALLOWED_ORIGINS` can contain multiple comma-separated origins. Do not use `*` in production.

## Endpoints

Public:

```text
POST /contact.php
POST /repair.php
```

Admin-only:

```text
GET /get_contacts.php
GET /get_repairs.php
Authorization: Bearer <ADMIN_API_TOKEN>
```

## Database Schema

See `schema.sql`.

## Local Checks

Install dependencies only when needed:

```sh
npm install
```

Do not commit `node_modules`; it is ignored.
