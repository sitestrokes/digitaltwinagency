# TwinProfit HQ

**Digital Twin Agency Command Center** — a multi-tenant SaaS built on CodeIgniter 4 that helps digital agency owners find prospects, build service packages, generate AI-powered proposals, and calculate UGC revenue — all powered by OpenAI and organized into a clean 5-step pipeline.

---

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Prerequisites](#prerequisites)
- [Local Setup](#local-setup)
- [Environment Configuration](#environment-configuration)
- [Database](#database)
- [API Endpoints](#api-endpoints)
- [Security](#security)
- [File Structure](#file-structure)
- [Development Notes](#development-notes)

---

## Overview

TwinProfit HQ gives digital agency owners a complete command center to run their AI Digital Twin agency. Each registered user gets their own isolated workspace — prospects, packages, and proposals are scoped per user. OpenAI API keys are stored per user, encrypted with AES-256, and used to generate bespoke client proposals on demand.

The app is structured as a **5-step sales pipeline**:

```
[01 Opportunity Finder] → [02 Service Packager] → [03 Proposal Generator] → [04 UGC Calculator] → [05 Settings]
```

A visual pipeline progress bar at the top tracks which steps have been completed in the current session.

---

## Features

### Tab 1 — Opportunity Finder
- Input any local business's details: name, website URL, niche, website/video/social status, budget, and competitor activity
- Calculates an instant **Digital Twin Readiness Score** (0–100) with animated ring chart
- Classifies prospect as **Hot** (≥75), **Warm** (≥50), or **Cold** (<50)
- Displays niche-specific pain points (15 niches supported)
- Generates a printable **One-Pager** PDF with score, pain points, and prospect URL
- Saves prospects to database with all fields including `website_url`

### Tab 2 — Service Packager & Pricer
- 10 AI Digital Twin services selectable via checkbox grid
- Auto-generates **3 pricing tiers** (Starter / Growth / Premium) with margin calculations
- Revenue projection table (3 / 5 / 10 / 20 clients)
- Save named package sets to the database

### Tab 3 — Proposal Generator
- Fill in client details, package tier, and services
- **Template mode**: instant professional proposal with no API key required
- **AI mode**: OpenAI-powered bespoke proposal (requires saved API key), with skeleton loading UI and token usage display
- Falls back to template automatically if AI is unavailable
- Copy to clipboard / Print to PDF
- Save proposals to database with status tracking (draft → sent → accepted/rejected)

### Tab 4 — UGC Revenue Calculator
- Configure video price, volume per week, and AI cost per video
- Live revenue projection (weekly / monthly / annual) with profit margins
- Profit margin comparison: AI UGC vs Human creators
- UGC pricing reference by niche (10 niches)
- 10 platforms to find UGC clients

### Tab 5 — Settings
- Save and encrypt OpenAI API key (AES-256 via CI4 Encrypter)
- Select AI model: `gpt-4.1-nano` (fast/cheap), `gpt-4o-mini` (balanced), `gpt-4o` (best quality)
- Test API key connection with rate limiting (max 5 tests/hour)
- Agency profile (name, email, phone) that auto-fills proposals
- Account info and sign-out

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | CodeIgniter 4.7 |
| PHP | 8.2+ (with `intl`, `mbstring`, `curl` extensions) |
| Database | MySQL 8.0 (via XAMPP) |
| AI | OpenAI API (`gpt-4.1-nano` default) |
| Frontend | Vanilla JS + custom CSS (no npm / no build step) |
| Icons | Phosphor Icons (CDN) |
| Fonts | Google Fonts — Outfit, Plus Jakarta Sans, DM Mono |
| Encryption | CI4 AES-256 Encrypter (for OpenAI key storage) |
| Sessions | File-based (CI4 FileHandler) |
| Dev Server | PHP built-in server (Homebrew PHP 8.2) |

---

## Architecture

```
app/
├── Controllers/
│   ├── Auth.php                  # Login / register / logout
│   ├── Dashboard.php             # Protected dashboard route
│   ├── Settings.php              # save() + testKey() API handlers
│   └── Api/
│       ├── ProspectController.php
│       ├── PackageController.php
│       ├── ProposalController.php
│       └── UgcController.php
├── Models/
│   ├── UserModel.php
│   ├── UserSettingModel.php
│   ├── ProspectModel.php
│   ├── PackageModel.php
│   ├── ProposalModel.php
│   └── AuditLogModel.php
├── Services/
│   ├── AuthService.php           # Login/register/session logic
│   ├── EncryptionService.php     # AES-256 wrap/unwrap + key masking
│   ├── OpenAIService.php         # cURL to OpenAI chat completions + validation
│   └── ProposalService.php       # Orchestrates AI vs template proposal generation
├── Filters/
│   ├── AuthFilter.php            # Redirects unauthenticated web requests to /login
│   └── ApiFilter.php             # Returns 401 JSON for unauthenticated API calls
└── Views/
    ├── layouts/main.php          # Base HTML layout with CSS/JS injection
    ├── auth/login.php
    ├── auth/register.php
    └── dashboard/index.php       # Full 5-tab SPA view
```

**Request flow:**
1. All web requests hit `public/index.php` (CI4 front controller)
2. `AuthFilter` checks `session()->get('user_id')` for protected routes
3. `ApiFilter` checks session AND enforces JSON response envelope `{ data, error }`
4. Controllers delegate business logic to Services, data access to Models
5. All models scope queries with `user_id` for multi-tenancy

---

## Prerequisites

| Requirement | Notes |
|---|---|
| PHP 8.2+ | With `intl`, `mbstring`, `curl`, `json`, `mysqlnd` extensions |
| MySQL 5.7+ / 8.0 | XAMPP bundle works well |
| Composer | For CI4 dependency management |
| OpenAI API Key | `sk-proj-...` format — optional, enables AI proposals |

> **macOS XAMPP Note:** XAMPP's bundled PHP (5.6/7.x) lacks the `intl` extension required by CI4. Use **Homebrew PHP 8.2** as the development server instead (`/opt/homebrew/opt/php@8.2/bin/php`).

---

## Local Setup

### 1. Clone the repository

```bash
git clone https://github.com/sitestrokes/digitaltwinagency.git
cd digitaltwinagency
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Configure environment

```bash
cp env .env
```

Then edit `.env` — see [Environment Configuration](#environment-configuration) below.

### 4. Set up the database

**Option A — PHP spark (recommended if `intl` is available):**
```bash
php spark migrate
```

**Option B — Direct SQL (for XAMPP PHP without `intl`):**
```bash
# Start XAMPP MySQL, then:
/Applications/XAMPP/bin/mysql -u root < migrate_direct.sql
```

`migrate_direct.sql` creates all 6 tables and seeds the CI4 migrations tracking table.

### 5. Create writable directories

```bash
mkdir -p writable/session writable/cache writable/logs writable/uploads
chmod -R 755 writable/
```

### 6. Start the development server

```bash
# Using Homebrew PHP 8.2 (recommended on macOS with XAMPP MySQL):
/opt/homebrew/opt/php@8.2/bin/php -S localhost:8181 -t public/

# Or using standard php if intl is available:
php spark serve
```

### 7. Open in browser

```
http://localhost:8181/
```

Register a new account or log in. The registration page is at `/register`.

---

## Environment Configuration

Key settings in `.env`:

```ini
# Application
CI_ENVIRONMENT = development
app.baseURL     = 'http://localhost:8181/'
app.indexPage   = ''

# Database (XAMPP MySQL default — no root password)
database.default.hostname = 127.0.0.1
database.default.database = digitaltwinagency
database.default.username = root
database.default.password =
database.default.port     = 3306
database.default.DBDriver = MySQLi

# Encryption key (AES-256 — used for OpenAI key storage)
# Generate with: php -r "echo 'hex2bin:'.bin2hex(random_bytes(32));"
encryption.key = hex2bin('7a1f3c8e2b4d0f9e5a7c1b3d8f2e4a6c9b0d2e5f7a1c3e8b4d6f0a2c5e7b9d1f')

# Session
session.savePath = '/path/to/digitaltwinagency/writable/session'
```

> **Security:** Never commit a real `.env` to version control. The `.gitignore` already excludes it.

---

## Database

### Schema Overview

#### `users`
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED | PK, auto-increment |
| `email` | VARCHAR(191) | Unique |
| `password_hash` | VARCHAR(255) | bcrypt |
| `name` | VARCHAR(255) | |
| `role` | ENUM(user, admin) | Default: user |
| `status` | ENUM(active, inactive, suspended) | Default: active |
| `last_login_at` | DATETIME | Updated on login |
| `created_at` / `updated_at` | DATETIME | CI4 timestamps |

#### `user_settings`
| Column | Type | Notes |
|---|---|---|
| `user_id` | BIGINT | FK → users |
| `openai_api_key` | TEXT | AES-256 encrypted, base64 |
| `openai_model` | VARCHAR(100) | Default: gpt-4.1-nano |
| `agency_name` / `agency_email` / `agency_phone` | VARCHAR | Agency profile |

#### `prospects`
| Column | Type | Notes |
|---|---|---|
| `user_id` | BIGINT | FK → users |
| `name` | VARCHAR(255) | Business name |
| `website_url` | VARCHAR(512) | Optional — prospect's website |
| `niche` | VARCHAR(100) | e.g. "Restaurant / Café" |
| `website_status` | VARCHAR(50) | modern / outdated / none |
| `video_status` | VARCHAR(50) | none / minimal / active |
| `social_status` | VARCHAR(50) | none / basic / active |
| `budget` | VARCHAR(50) | low / mid / high / premium |
| `competitors` | VARCHAR(50) | none / some / many |
| `score` | TINYINT UNSIGNED | 0–100 readiness score |
| `readiness_level` | ENUM(hot, warm, cold) | |
| `pain_points` | LONGTEXT | JSON array |

#### `packages`
| Column | Type | Notes |
|---|---|---|
| `user_id` | BIGINT | FK → users |
| `name` | VARCHAR(255) | Package set name |
| `starter_price` / `growth_price` / `premium_price` | INT UNSIGNED | Monthly price per tier |
| `starter_services` / `growth_services` / `premium_services` | LONGTEXT | JSON arrays |
| `selected_services` | LONGTEXT | JSON array |

#### `proposals`
| Column | Type | Notes |
|---|---|---|
| `user_id` | BIGINT | FK → users |
| `prospect_id` / `package_id` | BIGINT | Optional FKs |
| `agency_name` / `client_name` / `contact_name` | VARCHAR | |
| `niche` | VARCHAR(100) | |
| `tier` | ENUM(starter, growth, premium, custom) | |
| `price` | INT UNSIGNED | Monthly investment |
| `services` | LONGTEXT | JSON array |
| `content` | LONGTEXT | Full HTML proposal |
| `generation_mode` | ENUM(ai, template) | |
| `status` | ENUM(draft, sent, accepted, rejected) | Default: draft |
| `valid_until` | DATE | 14 days from creation |

#### `audit_logs`
| Column | Type | Notes |
|---|---|---|
| `user_id` | BIGINT | Nullable (for system events) |
| `action` | VARCHAR(100) | e.g. `settings.save`, `settings.test-key` |
| `entity_type` / `entity_id` | VARCHAR / BIGINT | Optional target |
| `meta` | LONGTEXT | JSON — additional context |
| `ip_address` | VARCHAR(45) | IPv4 / IPv6 |
| `user_agent` | VARCHAR(500) | Truncated to 500 chars |
| `created_at` | DATETIME | Insert-only (no updated_at) |

---

## API Endpoints

All API routes are under `/api/` and require an active session (HTTP 401 if unauthenticated). Responses use the envelope `{ "data": ..., "error": null }`.

### Settings

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/settings/save` | Save OpenAI key (encrypted) and/or agency profile |
| `POST` | `/api/settings/test-key` | Validate saved key against OpenAI `/models` endpoint |

**`POST /api/settings/save` body:**
```json
{
  "openai_api_key": "sk-proj-...",
  "openai_model":   "gpt-4.1-nano",
  "agency_name":    "My Agency",
  "agency_email":   "hello@agency.com",
  "agency_phone":   "+1 555 000 0000"
}
```

### Prospects

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/prospects` | List user's prospects |
| `POST` | `/api/prospects/save` | Save a scored prospect |
| `DELETE` | `/api/prospects/{id}` | Delete a prospect |

### Packages

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/packages` | List user's package sets |
| `POST` | `/api/packages/save` | Save a 3-tier package set |
| `DELETE` | `/api/packages/{id}` | Delete a package set |

### Proposals

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/proposals` | List user's proposals |
| `GET` | `/api/proposals/{id}` | Get single proposal |
| `POST` | `/api/proposals/generate` | Generate via AI or template |
| `POST` | `/api/proposals/save` | Save a generated proposal |
| `DELETE` | `/api/proposals/{id}` | Delete a proposal |

**`POST /api/proposals/generate` body:**
```json
{
  "mode":         "ai",
  "agency_name":  "TwinMedia Agency",
  "client_name":  "Bella's Bistro",
  "contact_name": "Maria Rossi",
  "niche":        "Restaurant / Café",
  "tier":         "growth",
  "price":        1997,
  "services":     ["AI Avatar Creation", "30 Social Videos/mo"],
  "pain_points":  ["No video content", "Staff won't go on camera"],
  "notes":        ""
}
```

### UGC Calculator

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/ugc/calculate` | Server-side UGC revenue calculation |

---

## Security

| Feature | Implementation |
|---|---|
| **Password hashing** | `password_hash()` with `PASSWORD_BCRYPT` |
| **OpenAI key encryption** | CI4 AES-256 `Encrypter` + `base64_encode` — key never stored in plain text |
| **Key masking** | Display format: `sk-proj-•••••••••last4` |
| **Session auth** | `AuthFilter` and `ApiFilter` gate all protected routes |
| **Rate limiting** | Max 5 key test attempts per user per hour (via `audit_logs`) |
| **Multi-tenancy isolation** | Every model query is scoped with `WHERE user_id = ?` |
| **Input handling** | No direct `$_POST`; uses `$request->getPost()` / `getJSON()` |
| **Output escaping** | All view output uses `esc()` |
| **Secrets management** | All secrets in `.env`, gitignored |
| **Audit logging** | Critical actions written to `audit_logs` with IP and user agent |

---

## File Structure

```
digitaltwinagency/
├── app/
│   ├── Config/
│   │   ├── Filters.php           # auth + api filter aliases
│   │   ├── Routes.php            # All route definitions
│   │   └── ...                   # CI4 config files
│   ├── Controllers/
│   │   ├── Auth.php
│   │   ├── Dashboard.php
│   │   ├── Settings.php
│   │   └── Api/
│   │       ├── ProspectController.php
│   │       ├── PackageController.php
│   │       ├── ProposalController.php
│   │       └── UgcController.php
│   ├── Database/
│   │   └── Migrations/           # 6 migration files (2026-02-23)
│   ├── Filters/
│   │   ├── AuthFilter.php
│   │   └── ApiFilter.php
│   ├── Models/
│   │   ├── UserModel.php
│   │   ├── UserSettingModel.php
│   │   ├── ProspectModel.php
│   │   ├── PackageModel.php
│   │   ├── ProposalModel.php
│   │   └── AuditLogModel.php
│   ├── Services/
│   │   ├── AuthService.php
│   │   ├── EncryptionService.php
│   │   ├── OpenAIService.php
│   │   └── ProposalService.php
│   └── Views/
│       ├── layouts/main.php
│       ├── auth/login.php
│       ├── auth/register.php
│       └── dashboard/index.php
├── public/
│   ├── assets/
│   │   ├── css/app.css           # All custom styles (~1,800 lines)
│   │   ├── js/app.js             # All frontend logic (~800 lines)
│   │   └── images/
│   │       └── opportunity-finder.png
│   └── index.php                 # CI4 front controller
├── migrate_direct.sql            # Direct SQL fallback for environments without intl
├── .env                          # Local config (gitignored)
├── env                           # .env template (committed)
├── composer.json
└── README.md
```

---

## Development Notes

### Running with XAMPP on macOS

XAMPP for macOS bundles an older PHP that lacks the `intl` extension required by CodeIgniter 4. Use the **MySQL from XAMPP** (port 3306) but the **PHP from Homebrew**:

```bash
# Install Homebrew PHP 8.2 if not present
brew install php@8.2

# Start the app (from project root)
/opt/homebrew/opt/php@8.2/bin/php -S localhost:8181 -t public/
```

### Running Migrations Without `intl`

Use `migrate_direct.sql` instead of `php spark migrate`:

```bash
/Applications/XAMPP/bin/mysql -u root < migrate_direct.sql
```

### Adding a Test User via MySQL

```sql
INSERT INTO digitaltwinagency.users (email, password_hash, name, role, status)
VALUES (
  'you@example.com',
  '$2y$10$...bcrypt_hash...',  -- generate with: php -r "echo password_hash('YourPass123!', PASSWORD_BCRYPT);"
  'Your Name',
  'user',
  'active'
);
```

Or just register at `/register` — the registration form creates the user and an empty `user_settings` row automatically.

### OpenAI Key Format

The app validates keys against the regex `/^sk-[A-Za-z0-9\-_]{20,}$/`, which matches both legacy `sk-...` and current project-scoped `sk-proj-...` keys.

### JSON Field Casting

CI4 4.7 requires explicit type hints on `$casts`. All nullable JSON fields use the `?json-array` prefix:

```php
protected array $casts = [
    'pain_points' => '?json-array',  // nullable JSON → PHP array
];
```

### Audit Log — No `updated_at`

`audit_logs` is an insert-only table. CI4's timestamp behaviour is disabled via:

```php
protected $updatedField = '';  // empty string disables updated_at
```

### CSRF

CSRF token is rendered in all forms and sent as a request header from `app.js`. The global CSRF filter in `Config/Filters.php` is commented out for local development — re-enable for production by uncommenting `'csrf'` in `$globals`.

---

## License

MIT — see [LICENSE](LICENSE).
