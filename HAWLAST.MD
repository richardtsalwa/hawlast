# HAWLAST — Unified Platform Blueprint

**Status:** Architecture / Specification Document (no code implemented yet)
**Target Stack:** PHP 8.2 · MySQL 8 (InnoDB / utf8mb4_unicode_ci) · Apache (XAMPP local, cPanel production) · Vanilla JS AJAX
**Database:** `hawlast`
**Delivery model:** **CLI-first** — every operational action is a CLI command first; the web app and the Android app are consumers of the same service layer (§12).
**Positioning:** *Not a web hosting company — a technical solution provider. Code + AI + Kenyan ingenuity to solve business problems.* (§14)

---

## 1. Problem Statement (Current State)

Today the business runs **disconnected systems**:

- A **separate login** is required to manage **SMS / Airtime** clients.
- A **separate login / flow** is required to click **Renew** for **web hosting / domain registration**.
- Domain search and registration are **manual**.
- Hosting accounts are **provisioned manually** by the admin.
- Renewal reminders are **manual** — nothing chases the client before expiry.
- M-Pesa Paybill money arrives and airtime is dispatched **by hand**.

**Goal:** One unified platform, **one login**, one dashboard, one source of truth.

---

## 2. Target State (Unified Platform)

```
                        ┌──────────────────────────┐
                        │      HAWLAST UNIFIED     │
   Client ─────────────►│   CLIENT DASHBOARD (SSO)  │
   Admin  ─────────────►│      ADMIN CONTROL PANEL │
                        └────────────┬─────────────┘
                                     │
        ┌───────────────┬────────────┼────────────┬──────────────────┐
        ▼               ▼            ▼            ▼                  ▼
  Domain Services   Hosting      SMS/Airtime   Service Orders   Billing Engine
  (Sav.com API)   (WHM/cPanel)   (Daraja)      (SEO/AI/Automation)  (Cron/Reminders)
```

### 2.1 Unified Authentication (Single Sign-On)
- **One account, one password, one session** for every client.
- A client who buys a domain, hosting, or airtime logs in **once** and sees everything.
- Roles: `client`, `support`, `admin` (least-privilege; no PII leakage in listings).
- Session hardening: `session_regenerate_id(true)` on login / privilege elevation.

### 2.2 Client Dashboard Features
| Module | Capability |
|---|---|
| **Domains** | View **all** registered domains (portfolio view), registrar status, expiry date, auto-renew flag, renew action, nameservers |
| **Hosting** | View hosting packages, server hostname (masked), cPanel login link, due date, suspend/reactivate request, **upgrade / downgrade package**, **add storage (disk quota) add-on** |
| **Invoices** | View paid & outstanding invoices, download, online payment (M-Pesa) |
| **SMS/Airtime Balance** | View **KES** balance, top up via M-Pesa Paybill, transaction history, **buy SMS credit bundles**, **run bulk SMS campaigns**, **order & manage SMS Sender IDs** |
| **WhatsApp** | Buy a WhatsApp Business API number/subscription and **send WhatsApp messages to their own customers** from the dashboard |
| **Service Orders** | Order **SEO Services**, **AI Integration**, **Business Automation Consultancy** |
| **Support** | Raise tickets, see ticket status |

> Full roadmap and rules for the self-service portal in **§18**.

### 2.3 Admin Control Panel Features
| Module | Capability |
|---|---|
| **Clients** | Create/edit/disable unified client accounts, view all services per client |
| **Hosting** | **Create client account in WHM (InMotion) via API** on purchase, **Reactivate** suspended account, terminate |
| **Domains** | Bulk search, register, renew, transfer, set auto-renew |
| **SMS/Airtime** | Manage clients, top up balances, view dispatch logs, retry failed dispatches |
| **Service Orders** | Receive, assign, quote and fulfil SEO / AI / Automation requests |
| **Billing** | Generate invoices, run renewal reminder cycle, view revenue in KES |
| **System** | Cron job monitor, API credential status, activity log |

> **Explicit requirement:** *"Me as the user (admin) can login and reactivate a hosting account."*
> Admin login → Hosting → select client → **Reactivate** (one click) → calls the InMotion WHM API and syncs local status.

---

## 3. Domain Services — Sav.com API Integration

### 3.1 Flow
1. Client searches a domain name in the dashboard.
2. Platform calls the **Sav.com domain availability API**.
3. Results rendered: available / unavailable, price (KES), TLD, renewal price.
4. Client selects → adds to cart → pays via **M-Pesa Paybill**.
5. On confirmed payment, a **background job** places the order with Sav.com.
6. Sav.com callback / poll updates status → domain appears in the client dashboard with its expiry date.
7. The expiry date is written into the renewals engine (see §5).

### 3.2 Requirements
- Availability search, TLD price list, register, renew, transfer, nameservers, get expiry.
- API key/secret stored **only** in the root `config.php` (never committed, never echoed).
- All Sav.com calls wrapped in `try/catch`, logged, retried with backoff.
- Idempotency: an order must not double-register on cron retry.

---

## 4. Hosting Provisioning — InMotion WHM / cPanel API

### 4.1 On Purchase (Automated)
```
Payment confirmed → create pending order
  → WHM API: create account (package, domain, username, password, quota)
  → success : hosting row status = 'active', invoice paid, client notified
  → failure : order status = 'provision_failed', admin alerted, client refunded/queued
```

### 4.2 Admin Actions
- **Reactivate** — re-enable a suspended hosting account (explicit admin requirement).
- **Suspend**, **Terminate**, **Change package**, **Reset cPanel password**.
- Auto-sync expiry from WHM so the reminder engine stays accurate.

### 4.3 Security
- WHM API token + cPanel API token live in `config.php` only.
- Generated cPanel passwords stored hashed; shown to the client **once** at creation.

---

## 5. Renewal & Invoice Reminder Engine (Cron)

### 5.1 Schedule
| Reminder | Days before expiry | Channel |
|---|---|---|
| R1 | **90 days** | Email |
| R2 | **60 days** | Email + client dashboard notice |
| R3 | **30 days** | Email + SMS + dashboard notice |
| R4 | **15 days** | Email + SMS + **M-Pesa STK push request for payment** |

### 5.2 Behaviour
- A daily cron scans the renewals table for hosting **and** domain services falling inside each window.
- Sends a reminder **once per window per service** (dedupe key: `service_id + window`).
- R4 additionally raises an invoice and, where the client has opted in, fires an **M-Pesa STK push** asking them to pay.
- Expired services are flagged `grace` → then `suspended`; hosting suspension can be one-click reverted by the admin (§4.2).
- All runs logged to a `job_runs` table with start, end and counts for the admin monitor.

### 5.3 Suggested Cron Entries
```
0  6 * * *    php /path/cron/renewal_reminders.php
*/15 * * * *  php /path/cron/mpesa_reconcile.php
0  * * * *    php /path/cron/airtime_dispatch.php
```

---

## 6. M-Pesa Daraja & Airtime Automation

### 6.1 Paybill Flow
```
Client pays Hawlast Paybill
  → Safaricom calls the Daraja IPN callback URL (hawlast/mpesaapi)
  → validate (timestamp, amount, receipt, token)
  → persist raw callback + parsed fields to hawlast.mpesaapi
  → route by intent:
        'airtime'  → credit airtime wallet, queue dispatch
        'topup'    → credit SMS/Airtime KES balance
        'invoice'  → settle invoice, trigger provisioning
```

### 6.2 Airtime Dispatch
- Client submits: phone number, network, amount.
- Payment confirmed → job row created → **scheduled job** sends airtime via the network/provider API.
- Dispatch is retried on failure; final state `delivered` / `failed` (visible to client and admin).
- Idempotent: a dispatched transaction is never sent twice.

### 6.3 Balances in KES
- Single wallet per client, currency **KES**, shown on the dashboard.
- Ledger table — every credit and debit is a row, so the balance is derived and auditable.

---

## 7. Service Orders — SEO, AI Integration, Business Automation

- Catalogue table of the three service lines with pricing, scope and delivery SLA.
- Client raises an order from the dashboard → appears in the admin panel as a **lead/quote**.
- Status flow: `requested → quoted → approved → in_progress → delivered → invoiced`.
- Optionally auto-generate an invoice on approval so the whole loop stays inside the platform.

---

## 8. Database — FULL SCHEMA REWRITE

> **Explicit requirement:** *Full schema rewrite. **No constraints carried over from the old database tables.***

### 8.1 Rewrite Principles
- All tables are **rebuilt from scratch** under the unified model. Old tables are **not** referenced, joined or inherited from — no legacy foreign keys, no legacy column types, no legacy indexes.
- New naming convention: plural, snake_case, `hawlast_` prefix for the core unified tables.
- Every table: `InnoDB`, `utf8mb4`, `utf8mb4_unicode_ci`, with `created_at` / `updated_at`.
- PK: `BIGINT UNSIGNED AUTO_INCREMENT`.
- Timestamps: `DATETIME`, with app-level timezone `Africa/Nairobi` (PHP side, per project rules).
- Money: `DECIMAL(12,2)`, **KES only**.
- Secrets (password hashes, API tokens) stored hashed; **PII never selected via `SELECT *`**.

### 8.2 Core Unified Tables (part 1)

**`hawlast_users`** — unified identity for clients + staff.
| Column | Type | Notes |
|---|---|---|
| `id` | BIGINT UNSIGNED AI | PK |
| `client_code` | VARCHAR(32) UNIQUE | Public reference (e.g. `HWL-0001`) |
| `email` | VARCHAR(191) UNIQUE | Login identifier |
| `password_hash` | VARCHAR(255) | bcrypt |
| `role` | ENUM('client','support','admin') | |
| `status` | ENUM('pending','active','disabled') | |
| `is_admin` | TINYINT(1) | Fast role check |
| `mpesa_optin` | TINYINT(1) | Allow STK push prompts |
| `last_login_at` | DATETIME NULL | |
| `created_at` / `updated_at` | DATETIME | |

**`hawlast_wallets`** — KES balance per user.
| `id` | BIGINT UNSIGNED AI | PK |
| `user_id` | BIGINT UNSIGNED | → `hawlast_users(id)`, UNIQUE |
| `balance` | DECIMAL(12,2) | KES, default 0.00 |
| `currency` | CHAR(3) | `KES` |

**`hawlast_wallet_ledger`** — append-only balance history.
| `id` | BIGINT UNSIGNED AI | PK |
| `user_id` | BIGINT UNSIGNED | → `hawlast_users(id)` |
| `direction` | ENUM('credit','debit') | |
| `amount` | DECIMAL(12,2) | KES |
| `balance_after` | DECIMAL(12,2) | Snapshot |
| `reference_type` | VARCHAR(40) | e.g. `mpesaapi`, `order` |
| `reference_id` | BIGINT UNSIGNED NULL | |
| `narration` | VARCHAR(255) | |
| `created_at` | DATETIME | |

**`hawlast_orders`** — one table for every purchasable line (domain, hosting, service).
| `id` | BIGINT UNSIGNED AI | PK |
| `user_id` | BIGINT UNSIGNED | → `hawlast_users(id)` |
| `type` | ENUM('domain','hosting','seo','ai_integration','automation_consultancy') | |
| `title` | VARCHAR(191) | Human label |
| `meta` | JSON | Type-specific payload (domain name, package, …) |
| `status` | ENUM('pending_payment','paid','provisioning','active','failed','cancelled','delivered') | |
| `amount` | DECIMAL(12,2) | KES |
| `invoice_id` | BIGINT UNSIGNED NULL | → `hawlast_invoices(id)` |
| `created_at` / `updated_at` | DATETIME | |

**`hawlast_domains`**
| `id` | BIGINT UNSIGNED AI | PK |
| `user_id` | BIGINT UNSIGNED | → `hawlast_users(id)` |
| `order_id` | BIGINT UNSIGNED NULL | → `hawlast_orders(id)` |
| `domain_name` | VARCHAR(191) UNIQUE | |
| `tld` | VARCHAR(32) | |
| `registrar` | VARCHAR(64) | `sav.com` |
| `sav_order_id` | VARCHAR(64) NULL | External reference |
| `status` | ENUM('pending','active','expired','suspended','transferring') | |
| `registered_at` | DATETIME NULL | |
| `expires_at` | DATETIME NULL | |
| `auto_renew` | TINYINT(1) | |
| `created_at` / `updated_at` | DATETIME | |

**`hawlast_hosting`**
| `id` | BIGINT UNSIGNED AI | PK |
| `user_id` | BIGINT UNSIGNED | → `hawlast_users(id)` |
| `order_id` | BIGINT UNSIGNED NULL | → `hawlast_orders(id)` |
| `package` | VARCHAR(64) | InMotion plan |
| `primary_domain` | VARCHAR(191) | |
| `cpanel_username` | VARCHAR(64) | |
| `cpanel_password_hash` | VARCHAR(255) | Hashed |
| `server_hostname` | VARCHAR(191) | |
| `server_ip` | VARCHAR(45) | |
| `whm_account_id` | VARCHAR(64) NULL | InMotion reference |
| `status` | ENUM('pending','active','suspended','terminated','provision_failed') | |
| `activated_at` | DATETIME NULL | |
| `expires_at` | DATETIME NULL | |
| `suspended_at` | DATETIME NULL | |
| `created_at` / `updated_at` | DATETIME | |

> **Reactivate flow:** admin sets `status='active'`, clears `suspended_at`, and calls the InMotion WHM API to re-enable the account.

**`hawlast_invoices`**
| `id` | BIGINT UNSIGNED AI | PK |
| `invoice_no` | VARCHAR(32) UNIQUE | |
| `user_id` | BIGINT UNSIGNED | → `hawlast_users(id)` |
| `order_id` | BIGINT UNSIGNED NULL | → `hawlast_orders(id)` |
| `subtotal` | DECIMAL(12,2) | KES |
| `tax` | DECIMAL(12,2) | KES |
| `total` | DECIMAL(12,2) | KES |
| `status` | ENUM('draft','issued','part_paid','paid','overdue','void') | |
| `issued_at` / `due_at` / `paid_at` | DATETIME | |
| `created_at` / `updated_at` | DATETIME | |

**`hawlast_invoice_items`**
| `id` | BIGINT UNSIGNED AI | PK |
| `invoice_id` | BIGINT UNSIGNED | → `hawlast_invoices(id)` ON DELETE CASCADE |
| `description` | VARCHAR(255) | |
| `qty` | INT | |
| `unit_price` | DECIMAL(12,2) | KES |
| `line_total` | DECIMAL(12,2) | KES |

**`hawlast_renewals`** — *feeds the reminder engine for domains + hosting.*
| `id` | BIGINT UNSIGNED AI | PK |
| `user_id` | BIGINT UNSIGNED | → `hawlast_users(id)` |
| `service_type` | ENUM('domain','hosting') | |
| `service_id` | BIGINT UNSIGNED | → `hawlast_domains(id)` or `hawlast_hosting(id)` |
| `invoice_id` | BIGINT UNSIGNED NULL | → `hawlast_invoices(id)` |
| `expiry_date` | DATE | Indexed — the cron scans this |
| `amount` | DECIMAL(12,2) | KES |
| `auto_renew` | TINYINT(1) | |
| `status` | ENUM('pending','notified','invoiced','paid','expired') | |
| `last_reminder_window` | ENUM('none','90','60','30','15') NULL | Dedupe |
| `last_reminded_at` | DATETIME NULL | |
| `created_at` / `updated_at` | DATETIME | |

**`hawlast_mpesaapi`** — persisted M-Pesa Paybill callbacks.
| `id` | BIGINT UNSIGNED AI | PK |
| `transaction_id` | VARCHAR(64) | `TransID` (receipt) |
| `mpesa_receipt` | VARCHAR(64) | `ReceiptNo` |
| `msisdn` | VARCHAR(20) | Payer MSISDN — treat as PII |
| `account_number` | VARCHAR(32) | Paybill / account reference |
| `amount` | DECIMAL(12,2) | KES |
| `intent` | ENUM('airtime','topup','invoice') NULL | Routing |
| `phone_number` | VARCHAR(20) NULL | Airtime target |
| `network` | VARCHAR(20) NULL | |
| `user_id` | BIGINT UNSIGNED NULL | → `hawlast_users(id)` |
| `result_code` | VARCHAR(10) | From Daraja |
| `result_desc` | VARCHAR(255) | |
| `raw_payload` | JSON | Full callback for audit |
| `processed` | TINYINT(1) | Idempotency gate |
| `processed_at` | DATETIME NULL | |
| `created_at` | DATETIME | |

**`hawlast_airtime_dispatches`**
| `id` | BIGINT UNSIGNED AI | PK |
| `mpesaapi_id` | BIGINT UNSIGNED NULL | → `hawlast_mpesaapi(id)` |
| `user_id` | BIGINT UNSIGNED NULL | → `hawlast_users(id)` |
| `phone_number` | VARCHAR(20) | |
| `network` | VARCHAR(20) | |
| `amount` | DECIMAL(12,2) | KES |
| `status` | ENUM('queued','processing','delivered','failed') | |
| `attempts` | TINYINT UNSIGNED | Retry counter |
| `provider_ref` | VARCHAR(64) NULL | |
| `failure_reason` | VARCHAR(255) NULL | |
| `dispatched_at` | DATETIME NULL | |
| `created_at` / `updated_at` | DATETIME | |

**`hawlast_service_catalog`** — SEO / AI / Automation offerings.
| `id` | BIGINT UNSIGNED AI | PK |
| `service_type` | ENUM('seo','ai_integration','automation_consultancy') | |
| `title` | VARCHAR(191) | |
| `description` | TEXT | |
| `price` | DECIMAL(12,2) | KES |
| `sla_days` | INT NULL | |
| `is_active` | TINYINT(1) | |
| `created_at` / `updated_at` | DATETIME | |

**`hawlast_notifications`** — single queue feeding email, SMS, in-app and (later) FCM push.
| `id` | BIGINT UNSIGNED AI | PK |
| `user_id` | BIGINT UNSIGNED NULL | → `hawlast_users(id)` |
| `type` | VARCHAR(40) | e.g. `renewal_90`, `renewal_15`, `payment`, `airtime`, `provisioning`, `service_update` |
| `channel` | ENUM('email','sms','push','inapp') | |
| `title` | VARCHAR(191) | |
| `body` | TEXT | |
| `deep_link` | VARCHAR(255) NULL | Target for the Android app / web |
| `status` | ENUM('queued','sent','failed','read') | |
| `sent_at` | DATETIME NULL | |
| `read_at` | DATETIME NULL | |
| `created_at` | DATETIME | |

**`hawlast_tickets`**, **`hawlast_job_runs`**, **`hawlast_activity_log`** — support, cron monitoring and audit trail.

### 8.3 Legacy Tables
- Old tables remain **untouched** during the rewrite.
- No new table declares a foreign key to, or otherwise depends on, an old table.
- Any migration is a **one-way copy** into the new tables, run explicitly and reviewed — nothing is merged in place.
- Legacy tables are archived/renamed only after the new platform is verified in production.

> ⚠️ **Schema-alteration safeguard (project rule):** any `CREATE TABLE` / `ALTER TABLE` / `DROP` against the live database requires explicit user confirmation of the exact SQL before it is run.

---

## 9. File / Module Layout (proposed)

```
/config.php                  # ONLY location for DB creds + Sav.com / WHM / Daraja keys
/bin/hawlast                 # CLI dispatcher (single entry point, §12) + .bat/.sh shims
/api/v1/                     # JSON API consumed by the web UI and the Android app (§13)
/pos/                        # existing SMS/airtime app (to be absorbed)
/admin/                      # admin control panel
/dashboard/  or  /client/    # unified client dashboard
/services/sav.php            # Sav.com API wrapper
/services/whm.php            # InMotion WHM / cPanel API wrapper (create/reactivate/suspend)
/services/mpesa.php          # Daraja validation + IPN routing
/services/billing.php        # invoices, reminders, renewal logic
/services/notifications.php  # notification queue writer (email / SMS / push, §13.4)
/mpesaapi/callback.php       # public Daraja IPN endpoint
/cron/renewal_reminders.php  # 90/60/30/15-day engine
/cron/airtime_dispatch.php   # scheduled airtime sender
/cron/mpesa_reconcile.php    # payment reconciliation
/schema/                     # new DDL for the rewritten schema
```

> Each cron file is a thin wrapper that calls the matching CLI command (§12.4), so there is exactly one implementation of every job.

All DB access follows the project rules: `require_once` the module's `config.php`, use the **global `$pdo`**, prepared statements, `PDO::ERRMODE_EXCEPTION`, `PDO::FETCH_ASSOC`, `EMULATE_PREPARES => false`, timezone `Africa/Nairobi`.

---

## 10. Implementation Phases

| Phase | Scope |
|---|---|
| **0** | Create the new schema (after the SQL is approved). Build the shared `config.php` API wrappers. |
| **0.5** | **CLI skeleton** (`bin/hawlast`) + app-agnostic `/services/*` conventions (`// CLI+API+WEB`). |
| **1** | Unified auth + roles + client dashboard shell (domains, invoices, wallet, KES balance). |
| **2** | Sav.com domain search → checkout → auto-registration. |
| **3** | InMotion WHM auto-provisioning + admin **reactivate/suspend** controls. |
| **4** | M-Pesa Daraja IPN into `hawlast_mpesaapi`, wallet crediting, scheduled airtime dispatch. |
| **5** | Renewal reminder cron (90/60/30/15) + STK push on R4 + `hawlast_notifications` queue. |
| **6** | Service orders (SEO / AI Integration / Automation Consultancy) end-to-end. |
| **6.5** | Pillar landing pages & copy written to §14 (cloud migration, fintech/M-Pesa, AI automation, data sovereignty). |
| **7** | Admin control panel polish, job monitor, audit log, go-live. |
| **8** | Public API hardening, token auth, FCM push wiring — Android app becomes buildable. |
| **9** | **Client self-service portal (§18)** — multi-domain portfolio, self-serve renewals, hosting package change + storage add-ons. |
| **10** | **Messaging products (§18.5–§18.7)** — SMS credit bundles, bulk SMS campaigns, Sender ID ordering, WhatsApp Business API subscription + sending. |

---

## 11. The Offer — Service Pillars

The platform described above is the **delivery vehicle**. The thing being sold is the solution; hosting, domains, SMS and airtime are the enablers. Full messaging guidance is in §14.

| Pillar | Target client | What we deliver |
|---|---|---|
| **Cloud Migration & Cost Optimisation** | SMEs, schools, churches | Cloud migration, right-sizing, spend reduction, backup & DR |
| **Fintech / M-Pesa Integrations + Secure AWS Architecture** | SMEs & institutions | Daraja STK/C2B/Till/B2C, reconciliation, secure AWS design |
| **AI Automation (WhatsApp + AI + Business Systems)** | SMEs, schools, churches | AI agents on WhatsApp wired into CRM/helpdesk/inventory/admin |
| **Kenya Data Sovereignty** | All | Data residency, retention, DPA compliance, exit planning |

Every pillar is quoted, delivered and invoiced through the same `hawlast_orders` → `hawlast_invoices` → M-Pesa payment flow (see §7 and §8.2).

---

## 12. CLI-First Operation

> **Requirement:** The web application must be operable from the command line. Anything an admin can do in the browser, an operator must also be able to do from a shell — headless, scriptable, and safe to schedule.

### 12.1 Principles
- **One service layer, many front-ends.** Business logic lives in `/services/*`, never inside page files. The web UI, the CLI and (later) the Android app all call the same functions.
- **CLI is the reference implementation.** If an action can be done in the browser, it must have a CLI equivalent. No admin-only capability is browser-exclusive.
- **Scriptable & idempotent.** Every command is safe to re-run; a repeated run is a no-op, not a duplicate charge or duplicate dispatch.
- **Machine-readable output.** Each command supports `--json` for piping into other tools, plus a human-readable default.
- **Exit codes are contractual.** `0` = success, `1` = failure, `2` = invalid usage, `3` = not found. Cron and monitoring depend on this.

### 12.2 Entry Point
A single dispatcher, e.g. `bin/hawlast` (with a `.bat`/`.sh` shim for Windows/XAMPP and cPanel):
```
php bin/hawlast <command> [options]
```

### 12.3 Command Surface
| Command | Purpose |
|---|---|
| `hawlast user:create` | Create a unified client account |
| `hawlast user:list` | List clients (non-PII columns only) |
| `hawlast domain:check <name>` | Sav.com availability + KES price lookup |
| `hawlast domain:register <order_id>` | Place a paid domain order with Sav.com |
| `hawlast hosting:create <order_id>` | Provision a cPanel account in WHM (InMotion) |
| `hawlast hosting:reactivate <hosting_id>` | **Re-enable a suspended hosting account** (admin requirement) |
| `hawlast hosting:suspend <hosting_id> [--reason]` | Suspend a hosting account |
| `hawlast invoice:create` / `invoice:send` | Raise and dispatch invoices |
| `hawlast renewal:scan` | Run the 90/60/30/15-day reminder engine now |
| `hawlast mpesa:reconcile` | Re-check unprocessed Daraja callbacks |
| `hawlast airtime:dispatch` | Drain the airtime dispatch queue |
| `hawlast wallet:credit <user_id> <amount>` | Manual KES credit (logged, admin-only) |
| `hawlast job:status` | Show last run of each cron job |
| `hawlast service:order:list` | List SEO / AI / Automation service orders |

### 12.4 Operational Requirements
- All cron jobs (§5.3) are the **same** CLI commands — cPanel Cron Manager, Windows Task Scheduler, or a Linux crontab all call the identical entry point.
- Output and failures logged to `php_errors.log`; `display_errors` OFF in production.
- Secrets read from `config.php` only; CLI must never print credentials.
- `--dry-run` supported on all destructive or outbound actions (registration, provisioning, dispatch, suspension).
- Multi-step operations wrapped in `beginTransaction()` / `commit()` / `rollBack()`.
- Safe under concurrency: a simple file lock (or DB advisory pattern) prevents overlapping runs of the same command.

---

## 13. Groundwork for the Hawlast Android App

> **Requirement:** the platform is built so a native Android client can be shipped later **without reworking the backend**. Every decision below exists for that reason.

### 13.1 Build the App Against an API, Not the Pages
- Every dashboard/admin capability is exposed as a **JSON API** under `/api/v1/`, and the web UI simply consumes it.
- **No logic in templates.** Page files only render; they never compute. Anything the UI calculates, the API can also return.
- The Android app is then a first-class client, not a bolted-on afterthought.

### 13.2 API Conventions (contract from day one)
- Versioned namespace `/api/v1/…`; breaking changes get `/api/v2/`.
- Envelope: `{ "success": bool, "data": mixed, "message": string, "code": int }`.
- Auth: **token-based** (long-lived access token + refresh token) so the app does not depend on PHP sessions/cookies.
- Timestamps in **ISO-8601 UTC** (`2026-03-14T08:30:00Z`); money as `{"amount":"4500.00","currency":"KES"}` — never a bare float.
- Errors carry a stable machine-readable `code`; human `message` may be localised in the app.
- Pagination and filtering from the start, because a phone screen needs it.
- Rate limiting and a per-device token revocation path.

### 13.3 Service Layer is App-Agnostic
- `/services/*` functions take and return plain arrays/objects — **no `$_SESSION`, no `$_POST`, no `echo`**.
- Auth context is passed in explicitly (e.g. `acting_user_id`, `role`), so the same function serves a browser session, a CLI flag, or an API token.
- This is the single most important rule for Android readiness — see §12.1.

### 13.4 Mobile-Relevant Features to Design For Now
- **Push notifications** via Firebase Cloud Messaging: renewal reminders (R1–R4), payment received, airtime delivered, provisioning complete, service order updates.
- Reminders §5 therefore also **persist a notification queue** (`hawlast_notifications`) rather than only sending email/SMS — one table feeds email, SMS, push and in-app.
- **M-Pesa STK push** must be triggerable from the app (same endpoint as the 15-day reminder).
- **Deep-link payloads** stored per notification so tapping opens the exact order/invoice/domain.
- Offline-tolerant reads: the app caches last-synced domain/hosting/invoice data.
- OTP/2FA for login and for high-risk admin actions.

### 13.5 Code-Commenting Convention (for future maintainers)
When writing backend code, leave markers where mobile-relevant behaviour is decided, e.g.:
```php
// ANDROID: exposed as GET /api/v1/hosting — returns id, package, status, expires_at only.
// ANDROID: never send cpanel_password_hash to the app; the app requests a one-time reveal instead.
// CLI+API+WEB: shared by all three front-ends — keep this function free of $_SESSION/$_POST/echo.
```
- Tag service-layer functions with `// CLI+API+WEB`.
- Mark mobile-specific hooks with `// ANDROID:` so the app team can grep the codebase.
- Note auth/session assumptions that will later need a token-based equivalent.

### 13.6 Data Model Choices That Help the App
- `hawlast_users.client_code` gives the app a stable public identifier to display.
- `hawlast_orders.status` and `hawlast_renewals.status` are enums, so the app can map them to badges without guessing.
- All money is `DECIMAL(12,2)` KES — safe to render exactly on a phone.
- `hawlast_notifications` (user, type, title, body, deep_link, read_at) gives the app a single in-app inbox.

---

## 14. Brand Positioning & Messaging Principle

> **Guiding principle:** *We are not selling websites or web hosting. We are a **technical solution provider** — using **code + AI + Kenyan ingenuity** to solve business problems.*

This principle governs all design, copy, naming and sales material across the web app, the CLI output, emails/SMS and any future Android app.

### 14.1 Core Statement
| Element | Message |
|---|---|
| **What we are** | A technical solution provider |
| **What we use** | Code + AI + Kenyan ingenuity |
| **What we do** | Solve business problems |
| **What we are *not*** | A cheap web-hosting reseller |

### 14.2 Service Pillars (the actual offer)

**1. Cloud Migration & Cost Optimisation**
- For **SMEs, schools and churches** that overpay for, or are stranded on, legacy infrastructure.
- Migrate to the cloud, right-size, and cut monthly spend without losing data.
- Copy angle: *"Cut your IT costs without cutting your service."*

**2. Fintech & M-Pesa Integrations + Secure AWS Architecture**
- M-Pesa Daraja (STK push, C2B, Till, B2C), payment reconciliation, automated receipts.
- Secure AWS architecture: least-privilege IAM, encryption at rest/in transit, audit logging, backups.
- Copy angle: *"Get paid on M-Pesa and reconciled by morning."*

**3. AI Automation — WhatsApp + AI + Business Systems**
- Connect WhatsApp to AI agents tied into the client's existing tools (CRM, helpdesk, inventory, school admin).
- Automate repetitive work: order intake, FAQs, appointment booking, fee reminders, receipts.
- Copy angle: *"Answer your customers at 2am while you sleep."*

**4. Kenya Data Sovereignty**
- Data resident in Kenya / the customer's chosen jurisdiction; understand Data Protection Act obligations.
- Residency, retention and exit planning handled as a first-class deliverable.
- Copy angle: *"Your data stays where your business is."*

### 14.3 How This Changes the Product
- **Hosting and domains are the foundation, not the headline.** They are how clients buy our pillar services; they are not what we lead with.
- **Lead with outcomes, not specs.** Feature copy describes a solved problem first, the technology second.
- **Segment the language:** schools, churches and SMEs each get their own page, examples and proof points.
- **Every dashboard module should name itself in outcome terms**, e.g. "Pay & Renew" rather than "Billing", "Auto-Respond" rather than "WhatsApp Bot".
- **Testimonials and case studies by sector** (school, church, SME) outrank generic hosting testimonials.

### 14.4 Copy Rules
| Do | Don't |
|---|---|
| Lead with the business problem solved | Lead with bandwidth, diskspace, or "unlimited" |
| Say "solution provider" / "technical partner" | Say "hosting company" or "web host" |
| Quote in **KES**, plainly | Hide prices behind "contact us" on core services |
| Name outcomes (lower bill, faster payroll, fewer manual errors) | List technologies as the headline benefit |
| Speak plainly to an owner/manager — minimal jargon | Deep technical jargon in client-facing copy |
| Show real Kenyan context (M-Pesa, DPA, local support) | Generic global marketing filler |
| Be specific about limits and trade-offs | Over-promise ("permanent", "unbreakable", "100% uptime") |

### 14.5 Tone
Confident, plain-spoken, technical but accessible, and locally grounded — *Kenyan ingenuity* is the differentiator, not an afterthought. Every claim should be something we can actually deliver and demonstrate.

---

## 15. Open Decisions
1. Pricing model for domains/hosting markup over Sav.com / InMotion cost (fixed KES markup or %).
2. Whether clients may self-serve hosting reactivation, or admin-only (currently **admin-only**).
3. STK push consent capture and opt-out wording for the 15-day reminder.
4. Airtime provider(s), and whether a single API covers all networks.
5. Whether the old `/pos/` app is retired entirely or kept read-only during transition.
6. Android app: Kotlin vs Flutter, and whether it ships in-house or to a contractor.
7. Whether the public marketing site is rebuilt on this platform or kept separate.
8. Cloud provider for the pillars (AWS only, or AWS + local Kenyan options for data sovereignty).
9. Airtime churn: is the "purchaser" the receiving number, the reseller (`airtimeid`), or both as separate KPIs? (S17, B2.8)
10. Hosting/domain churn: confirm the 30-day grace period, and whether churn is measured per calendar month or on each client's own renewal anniversary. (S17, B3.2)
11. Retention window after which airtime rows are auto-anonymised by cron (proposal: 24 months). (S17, B6)
12. Whether a Safaricom `TransID` receipt number may be retained after erasure - needed for disputes, but indirectly linkable to the payer inside Safaricom's records. (S17, B5.4)
13. Column width: widen `airtime_transactions.phone` and `sms_logs.receiver` from `CHAR(13)` to `VARCHAR(40)`, or shorten the anonymisation token to 13 characters. (S17, B6.1 - schema change needs explicit approval)


---

## 16. Marketing - Hero Redesign Brief (Pathmonk-Style)

_Migrated verbatim from `DESIGN.MD` (Part A). Front-end brief only; it does not alter the platform architecture in S1-S15._

**Role:** You are a senior front‑end engineer and conversion‑focused UI designer with full read access to the Hawlast website codebase (HTML, PHP, CSS, Tailwind config, assets).

**Goal:** Redesign only the **hero section** of `hawlast.com` to follow the same **pattern** as the hero on `pathmonk.com`, while **retaining Hawlast's current color scheme, typography, and overall brand feel**. Hawlast offers:

- Domain names  
- Web hosting  
- AI chatbots  
- Business automation  
- Business software integration  

You have **full access to all website files**; use them to infer colors, spacing, typography, and existing components. Do not change unrelated sections of the site.

---

### 1. Reference Pattern: Pathmonk‑Style Hero

Use the Pathmonk homepage hero as a **structural and strategic template** (do not copy their exact words or visuals). Key characteristics:

- **Desktop layout:**  
  - Full‑width section with two columns:  
    - Left: H1 headline, subheadline, primary + secondary CTAs, short trust line  
    - Right: large hero visual (illustration or product‑style graphic)  
- **Mobile layout:**  
  - Single column, stacked: headline → subhead → CTAs → visual → trust line.  

- **Content hierarchy:**  
  1. **H1 headline** – 6–12 words, clear, benefit‑led, outcome‑oriented.  
  2. **Subheadline** – 1–3 short sentences explaining what they offer and why it matters.  
  3. **Primary CTA button** – strong action ("Get Started", "See Plans", "Book a Demo").  
  4. **Secondary CTA (optional)** – lower friction ("Talk to an Expert", "View Pricing").  
  5. **Trust / proof line** – one short sentence or small stat/logos row.  
  6. **Hero visual** – clean, modern illustration or mock dashboard that supports the message.  

- **Design feel:**  
  - Lots of whitespace  
  - Clear typographic hierarchy  
  - Minimal distractions  
  - Visual that supports the message, not overwhelms it  

Use this as a **pattern**, not a copy target.

---

### 2. Use Existing Codebase for Branding

From the Hawlast codebase:

#### Colors

- Inspect current CSS / Tailwind config / existing hero styles.  
- Identify primary, secondary, accent, background, and text colors (e.g. `bg-emerald-600`, `text-slate-900`, custom `colors.primary`, etc.).  
- Reuse those exact Tailwind classes or CSS variables in the new hero.  

#### Typography & Spacing

- Match existing heading sizes, line heights, and spacing scale (e.g. `text-4xl md:text-5xl`, `leading-tight`, `gap-6`, `py-16`, etc.).  
- Reuse existing container widths and grid/flex patterns where possible.  

#### Components

- If there are existing button components, form inputs, or section wrappers, reuse or extend them instead of reinventing.  

Your output must **align with the current design system** inferred from the repo.

---

### 3. Messaging & Positioning for New Hero

Emphasize that Hawlast is an all‑in‑one platform for:

- Registering domains  
- Fast, reliable web hosting  
- AI chatbots  
- Business automation  
- Business software integration (CRM, ERP, payments, analytics, etc.)  

Tone: clear, practical, business‑focused, oriented to SMEs, startups, and agencies.

---

### 4. Desired New Hero Structure

Implement a new hero with this structure:

1. **H1 headline**  
   - 6–12 words, outcome‑focused. Examples (refine to match brand voice):  
     - "Launch and automate your online business in one place"  
     - "Domains, hosting, and AI automation for growing businesses"  
     - "From domain to automation: your all‑in‑one growth stack"  

2. **Subheadline**  
   - 1–3 sentences, explicitly mentioning:  
     - Domain names  
     - Hosting  
     - AI chatbots  
     - Business automation  
     - Software integration  
   - Example direction:  
     "Get a custom domain, fast and reliable hosting, AI chatbots, and end‑to‑end business automation. We integrate your tools so you can focus on growth, not manual work."

3. **Primary CTA button**  
   - Label options: "Get Started", "See Hosting Plans", "Talk to Us".  
   - Link to the existing signup / contact / plans page (e.g. `signup.php`, `contact.php`, `plans.php`).  

4. **Secondary CTA (optional)**  
   - Label options: "View Pricing", "Book a Demo", "See Examples".  

5. **Trust / proof line**  
   - Example: "Trusted by startups, SMEs, and agencies across Kenya and beyond."  
   - Or: "X+ domains managed • Y+ sites hosted" (use real numbers if present in the codebase or content).  

6. **Hero visual concept**  
   - Right‑side (desktop) illustration or composite graphic showing:  
     - Domain + hosting (domain name, server, dashboard vibe)  
     - Chatbots (conversation bubbles, flows)  
     - Automation (workflow nodes, arrows)  
     - Integrations (connected app icons: CRM, payments, analytics)  
   - Style: clean, modern SaaS illustration, using Hawlast's existing color classes.  

On mobile, stack: headline → subhead → primary CTA → secondary CTA → visual → trust line.

---

### 5. Technical Requirements

- **Stack:** HTML + PHP + Tailwind CSS (as used in the current Hawlast repo).  
- **Scope:** Only the hero section; do not modify unrelated pages or sections.  
- **Implementation:**  
  - Use Tailwind utility classes for layout, spacing, typography, and colors.  
  - Ensure responsive behavior (mobile‑first, then `md:` and `lg:` breakpoints).  
  - Maintain or improve accessibility (contrast, focus states, semantic HTML).  
  - Keep performance in mind (no heavy new dependencies; optimize images/SVGs if added).  

- **File changes:**  
  - Identify the file(s) that currently contain the hero (e.g. `index.php`, `hero.php`, `header.php`, or a template partial).  
  - Replace only the hero markup with the new version.  
  - If needed, create a new partial (e.g. `partials/hero-new.php`) and update the including file(s).  

---

### 6. Required Output

Produce:

1. **Brief rationale (2–5 sentences)**  
   - How you adapted the Pathmonk pattern to Hawlast's services and existing design system.  

2. **Exact code changes**  
   - Show the **before** and **after** for the hero section (or clearly indicate the file path and the new code block to replace).  
   - Use Hawlast's existing Tailwind color classes and typography scale.  
   - Ensure the code is ready to commit and deploy.  

3. **Copy variants (in comments or separate section)**  
   - 3 headline options (6–12 words each)  
   - 2 subheadline options (1–3 sentences)  
   - 2 primary CTA labels  
   - 1 trust/proof line  

4. **Optional recommendations**  
   - Any Tailwind config additions you suggest (e.g., a custom `primary` color alias if not already defined).  
   - Suggestions for the hero visual (e.g., "create an SVG illustration at `assets/images/hero-automation.svg` using these color classes").  

Your output should be directly usable by a developer to update the live site with minimal editing.

## 17. Dashboard Analytics & Data-Retention - Churn, Erasure & Privacy

_Migrated verbatim from `DESIGN.MD` (Part B). Sub-labels keep the original B-numbers for cross-referencing._

> **Scope:** `saas/dashboard.php` (KPI tiles + Chart.js), `saas/churn.php`, `pesa/me.php` and the
> tables `airtime_transactions`, `mpesaapi`, `mpesaapisafi`, `renewals`, `renewalsprepaid`, `sms_logs`.
> **Hard rule (`.clinerules`):** analytics must aggregate only (`COUNT` / `SUM`). Never `SELECT *`,
> and never print a phone number, name, or email address on the dashboard.

### B1. Ground truth about the stored data

| Data item | Location | State today |
|---|---|---|
| Receiving number of every airtime sent | `airtime_transactions.phone` (`char(13)`) | **PLAIN TEXT, always.** Written by `saas/airtime/buy*.php`, read back by `saas/airtimepost.php` for SMS |
| Amount sent, discount, status | `airtime_transactions.amount` / `.discount` / `.status` | Numeric + `'Success'` / `'Failed'` |
| Payer MSISDN (Safaricom M-Pesa Daraja callback) | `mpesaapi.MSISDN`, `mpesaapisafi.MSISDN` | **Encrypted / ciphertext as delivered by Daraja** — no decryption key is held. Legacy rows may still be plaintext |
| Payer names, bill ref | `mpesaapi.FirstName` / `.MiddleName` / `.LastName` / `.BillRefNumber` | Plain text PII |
| Hosting & domain clients | `renewals` (`service`, `url`, `signup_date`, `renewal_date`, `phone`) | Plain text PII |
| Renewal payments | `renewalsprepaid` (`renewalsid`, `prepaid`, `TransID`, `status`, `date`) | Payment history |
| SMS recipients | `sms_logs.receiver`, `sms_logs.message` | Plain text PII |

### B2. Churn rate — airtime purchasers

**B2.1 Who counts as a purchaser.** A purchaser is one distinct receiving MSISDN on a
`status = 'Success'` row of `airtime_transactions`. Rows with `Failed`, pending, or `NULL`
status are excluded from both the numerator and the denominator (they are not churn, they are noise).

**B2.2 Normalisation (blocking requirement).** `phone` is stored in mixed formats
(`07xxxxxxxx`, `2547xxxxxxxx`, `+2547xxxxxxxx`). Grouping on the raw column splits one human into
several "purchasers" and silently understates churn. Every churn computation must key on
`add254($phone)` (already in `saas/functions.php`), never on the raw column. Invalid numbers that
`add254()` rejects are counted into a visible `unparsed_rows` figure, never dropped silently.

**B2.3 Definitions.**

- **Active purchasers in period P** = distinct normalised MSISDNs with ≥1 successful purchase inside P.
- **Churned** = active in period P1 **and** zero successful purchases in the following period P2 of equal length.
- **Churn rate %** = `churned ÷ active_in_P1 × 100`.
- **Retention %** = `100 − churn rate %`.

**B2.4 Required windows (all rolling, all relative to "today", Nairobi time):**

1. **30-day** — P1 = `today-60d … today-31d`, P2 = `today-30d … today`.
2. **90-day** — P1 = `today-180d … today-91d`, P2 = `today-90d … today`.
3. **Monthly cohort trend** — one point per calendar month since the launch cutoff, plotted as a line.
4. **Dormant count** — purchasers whose last successful purchase is older than 30 / 60 / 90 days (3 buckets).

**B2.5 Segmentations.** Every KPI above must be filterable by: reseller (`airtimeid`) vs public
paybill customers, calendar month, and amount band (`<100`, `100–499`, `500–999`, `≥1000`).

**B2.6 Dashboard presentation.** Add to `saas/dashboard.php`: 4 KPI tiles (30-day churn, 90-day churn,
active purchasers, dormant >30d) plus a month-by-month churn line reusing the existing Chart.js setup.
Each tile shows the current %, the previous equal-length period %, and an up/down delta.

**B2.7 Fix the hardcoded cutoff.** `saas/churn.php` and `saas/dashboard.php` hardcode
`'2024-12-20 01:00:00'` / `'2024-12-20'`. Replace with a rolling window driven by
`CURDATE() - INTERVAL <n> DAY`. The launch date becomes an optional `?from=YYYY-MM-DD` query
parameter used only to exclude the pre-launch test period.

**B2.8 Keying on the encrypted payer MSISDN.** Where the payer rather than the recipient must be
tracked (`mpesaapi.MSISDN`), group by `SHA2(MSISDN, 256)` as `msisdn_hash` and **never render the
ciphertext itself** — display only the first 6 characters of the hash followed by `…`. Deterministic
hashing keeps the encrypted values groupable for churn maths without ever storing or exposing a
dialable number.

**B2.9 Win-back.** Keep the SMS win-back already in `saas/churn.php` (`TumaSMS()`), but it must run
only from a CLI or cron entry point — never from a browser-facing GET — and must respect the
suppression list written by B5.

### B3. Churn rate — hosting & domain clients

**B3.1 Unit of measure.** Unlike airtime (transactional), this is a **subscription** churn: the unit is
a `renewals.id` line (one hosted site or one domain), not a transaction. A client with 4 domains and
2 hosting accounts contributes 6 units.

**B3.2 Status derivation.**

- **Active** = latest paid term has not expired: a `renewalsprepaid` row with `status = 1` whose
  `date + prepaid` term ≥ `CURDATE()`.
- **In grace** = `renewal_date` passed but `CURDATE() <= renewal_date + grace_days` (**default
  `grace_days = 30`**, exposed as a dashboard control, not hardcoded).
- **Churned (lapsed)** = `renewal_date + grace_days < CURDATE()` **and** no `renewalsprepaid` row with
  `status = 1` dated after that `renewal_date`.
- **At risk** = `renewal_date` falls inside the next 30 days (this feeds renewal reminders, not churn).

**B3.3 Formula.** `monthly churn % = lapsed_units_at_month_end ÷ active_units_at_month_start × 100`.
Also expose **annual churn %** computed over 12 months, and **logo retention %** (clients with ≥1
still-active unit ÷ clients acquired in that cohort).

**B3.4 Segmentation.** Split every figure by `renewals.service` so **hosting churn** and **domain churn**
are reported as two separate KPIs and two separate chart series, never blended into one number.

**B3.5 Presentation.** Two KPI tiles (hosting churn %, domain churn %) + one 12-month trend line, and
an "At risk in next 30 days" counter. Listing rows are allowed **only** as `renewals.id | service |
renewal_date | status` — never `firstname`, `lastname`, `email`, `emailalt`, or `phone`.

**B3.6 Data-quality caveats to surface in the UI.** `renewals.renewal_date` is maintained by hand and
`renewalsprepaid` has no foreign key to `renewals`, so show a `records_missing_payments` counter
(renewals with zero `renewalsprepaid` rows) beside the churn figure; those rows are reported as
`unknown`, not as churned, so the KPI is not inflated.

**B3.7 Aggregates must outlive erasure.** Churn is reported from a monthly snapshot table
(e.g. `churn_monthly(period, product, active_units, churned_units, rate, created_at)`) written by cron
and read by the dashboard. Raw PII can then be erased under B5 without destroying historical trends.

### B4. Display rules for encrypted vs plain-text numbers

**B4.1** `mpesaapi.MSISDN` currently arrives from the Daraja callback as an **encrypted string**, and
the platform holds no key to reverse it — and **must not** attempt to. Older rows written by
`pesa/stk.php` hold plain-text numbers. Both cases render masked:

| Stored form | What the dashboard may show |
|---|---|
| Legacy plain text `2547XXXXXXXX` | `2547•••••` + **last 2 digits only** |
| Daraja ciphertext | first 6 characters of `SHA2(MSISDN,256)` + `…` |
| Any value after a B5 purge | `ANONYMISED` |

**B4.2** No phone number, name, or email is ever emitted to HTML, JSON, chart tooltips, `console.log`,
CSV/Excel exports, `error_log`, or `php_errors.log`. Exports carry `msisdn_hash` instead.

**B4.3** Stop the leakage at the source: remove the `<td><?php echo htmlspecialchars($row['MSISDN']);
?></td>` column in `pesa/me.php` (line ~265) and the raw `Phone: …` output in `saas/churn.php`
(line ~57), replacing them with the masked forms in B4.1.

### B5. Delete / anonymise airtime purchase data (right to erasure)

An airtime purchase row today ties together **three** sensitive facts: the **amount sent**, the
**receiving number (always plain text)**, and the **payer MSISDN from the Safaricom Daraja callback
(encrypted)**. The platform must offer a supported way to remove or anonymise that combination on
request (ODPC / Kenya DPA 2019 right to erasure, and for disputed or fraudulent transactions).

**B5.1 Entry point.** A single admin-only screen `saas/privacy.php` (linked from `saas/index.php`
next to Dashboard) guarded by `$_SESSION['access_level'] == 2`, **POST only**, CSRF-protected, with a
re-entry of the admin password. No destructive action may ever fire on a GET, and no link to it may
carry the target number in the query string.

**B5.2 Scope selectors.** The operator picks exactly one: a receiving number (accepted in any format,
normalised through `add254()`), a single transaction (`id` / `requestid` / `TransID`), a payer MSISDN
hash, or a closed date range for bulk erasure.

**B5.3 Dry-run first.** Before anything is written, the screen shows a **count-only** preview per table
("airtime_transactions: 14, mpesaapi: 3, sms_logs: 9"). No identifiers, amounts, or names are echoed in
the preview. Execution requires typing the word `PURGE`.

**B5.4 The two operations.**

| Operation | Effect | Use when |
|---|---|---|
| **Delete** | `DELETE` the matching rows from every table in B5.6. Irreversible. | Disputed / fraudulent / duplicate transactions, or the person was never a customer |
| **Anonymise** | Row is **kept** for accounting and churn analytics; every identifier is overwritten with an irrecoverable random value | Normal data-subject request — the business still needs the revenue history |

Anonymise rules: `airtime_transactions.phone` → `'ANON-' . bin2hex(random_bytes(5))`;
`mpesaapi.MSISDN` / `mpesaapisafi.MSISDN` → a fresh random token; `FirstName`, `MiddleName`,
`LastName`, `BillRefNumber`, `InvoiceNumber` → `'REDACTED'`. Preserved unchanged: `date`, `amount`,
`discount`, `status`, `requestid`, `TransID`, `airtimeid` — so daily sales, commissions, and the B2/B3
churn windows stay numerically correct while becoming unlinkable to a person.

**B5.5 Forbidden substitutions.** Never replace a number with `MD5()`/`SHA2()`/`sha1()` of that number
(a 9–10 digit keyspace is trivially brute-forced, so it stays personal data). Never write a mapping or
"backup" column that can turn a token back into a number. Never anonymise by truncation or masking
(`25472…`) — masking is still pseudonymisation, not erasure.

**B5.6 Cascade map — every place the data lives.**

| Table / file | Columns touched | Delete | Anonymise |
|---|---|---|---|
| `airtime_transactions` | `phone` | row | `phone` tokenised |
| `mpesaapi`, `mpesaapisafi` | `MSISDN`, `FirstName`, `MiddleName`, `LastName`, `BillRefNumber`, `InvoiceNumber` | row | tokenised / `REDACTED` |
| `sms_logs` | `receiver`, `message` | row | `receiver` tokenised, `message` cleared |
| `airtime_commission` | `affiliate` | row | tokenised |
| `airtime_user`, `sms_users` | `phone`, `phonea`, `fname`, `lname`, `idno`, `email` | row + account closure | tokenised, login disabled |
| `renewals` | `phone`, `email`, `emailalt`, `firstname`, `lastname` | row | tokenised / `REDACTED` |
| `commission_logs.txt`, `TumaSmsError.txt`, Daraja `mlog()` callback logs, `airtimepost.txt` | free-text numbers / bill refs | strip matching lines | strip matching lines |

**B5.7 Suppression list.** Every erasure also upserts the pseudonym into `privacy_suppression(token,
created_at)` so the B2.9 win-back SMS job and renewal reminders can never contact that person again.

**B5.8 Atomicity & audit.** All statements run inside `beginTransaction()` / `commit()` with
`rollBack()` on any `PDOException` (logged to `php_errors.log`), using prepared statements only. The
operation is idempotent — re-running it changes nothing and reports zero affected rows. Write one row
to `privacy_audit(id, action, scope_token, tables_affected, rows_affected, admin_id, created_at)`
containing **no PII**, and show the operator only the affected row counts.

**B5.9 Known limitation to state in the UI.** Erasure covers the live database and the text logs, but
not off-site backups or replica lag; the UI must state that backups are purged on the next rotation
(and the rotation interval must be documented).

### B6. Schema changes implied — **blocked until the exact SQL is approved**

Per `.clinerules`, no `ALTER TABLE` / `CREATE TABLE` runs until the statements have been shown and
explicitly approved. The expected set:

1. **Width problem (must be resolved before any anonymise ships):** `airtime_transactions.phone` is
   `char(13)` and `sms_logs.receiver` is `char(13)`, but the B5.4 token `ANON-` + 10 hex chars is
   **15 characters**. Either widen both columns to `VARCHAR(40)` (preferred) or shorten the token to
   `'A' . 12 hex` so it fits 13. Pick one; do not let MySQL silently truncate the token.
2. `airtime_transactions`: add `purged_at DATETIME NULL DEFAULT NULL`, `purge_type VARCHAR(10) NULL`,
   plus indexes on `(phone)` and `(status, date)` for the churn queries.
3. `churn_monthly(period DATE, product VARCHAR(20), active_units INT, churned_units INT,
   rate DECIMAL(5,2), created_at TIMESTAMP)` — the erasure-proof aggregate store from B3.7.
4. `privacy_suppression(token VARCHAR(40), created_at TIMESTAMP)`, unique index on `token`.
5. `privacy_audit(id, action, scope_token, tables_affected VARCHAR(255), rows_affected INT,
   admin_id INT, created_at TIMESTAMP)`.
All new tables use `InnoDB` with `utf8mb4` / `utf8mb4_unicode_ci`.

### B7. Acceptance criteria

1. `saas/dashboard.php` shows airtime 30-day and 90-day churn % with previous-period comparison and a
   monthly churn trend line; dormant buckets 30/60/90 are visible.
2. No hardcoded `'2024-12-20'` literal remains in the churn or dashboard queries; changing the window
   changes the numbers.
3. A test number stored three ways (`07…`, `254…`, `+254…`) is counted as **one** purchaser.
4. Hosting churn % and domain churn % appear as separate KPIs and separate series; changing `grace_days`
   changes both; renewals with no payment rows are counted as `unknown`, not churned.
5. Scraping the dashboard, `saas/churn.php`, `pesa/me.php`, and every export with `curl | grep -E
   '254[0-9]{8}|07[0-9]{8}'` returns **zero** matches, and no Daraja ciphertext is rendered in full.
6. Anonymising one transaction leaves `amount`, `date`, `status`, `requestid`, `TransID` byte-identical,
   makes `phone` unreadable, and leaves the churn KPI value unchanged (the row is still counted).
7. Deleting a number zeroes its rows across every table in the B5.6 map; running the same job twice
   affects 0 rows the second time and never errors.
8. A logged-in user with `access_level != 2` receives 403 and no data changes; a GET to the purge
   endpoint changes nothing.
9. A number erased today is never texted again by win-back or renewal reminders (suppression list proven
   by a dry-run list diff).
10. Historical churn charts still render after a bulk purge (they read `churn_monthly`).
11. `php -l` passes on every modified PHP file, and each erasure writes exactly one PII-free audit row.

### B8. Assumptions & open questions (answer before implementation)

- **"Purchaser" ambiguity:** in the self-serve paybill flow the receiving number *is* the buyer, but for
  resellers (`airtimeid > 1`) the receiving number is the beneficiary and the true buyer is the
  reseller. Confirm whether the KPI should key on receiving number, reseller, or both as separate tiles.
- Confirm `grace_days = 30` for hosting/domain, and whether churn should be measured on calendar month
  or on each client's own renewal anniversary.
- Confirm the retention period after which rows are auto-anonymised by cron (proposal: 24 months).
- Confirm whether `TransID` (a Safaricom receipt number) may be retained after erasure — it is needed
  for disputes but is indirectly linkable to the payer inside Safaricom's records.

### B9. Out of scope for Part B

No change to how airtime is actually dispatched (Africa's Talking calls stay as-is), no new payment
rails, no rework of login/session handling beyond the existing `access_level` gate, and no changes to
the S16 hero redesign work.

---

*Document created as a planning artefact only. No database changes have been executed.*


1. Pricing model for domains/hosting markup over Sav.com / InMotion cost (fixed KES markup or %).
2. Whether clients may self-serve hosting reactivation, or admin-only (currently **admin-only**).
3. STK push consent capture and opt-out wording for the 15-day reminder.
4. Airtime provider(s), and whether a single API covers all networks.
5. Whether the old `/pos/` app is retired entirely or kept read-only during transition.

---

## 18. Client Self-Service Portal — Future Roadmap

> **Business driver:** renewal reminders are currently sent **to the client by the admin**, which is
> manual, easy to forget, and does not scale. The strategic fix is to stop chasing clients by hand and
> instead give every client a dashboard where they **manage their own domains, renewals, hosting,
> storage and messaging** — 24/7, without a phone call. Admin moves from *doing the work* to
> *approving exceptions*.
>
> **Status:** planning artefact only. No code, no database changes. Every `CREATE TABLE` / `ALTER TABLE`
> implied here is **blocked until the exact SQL is shown and explicitly approved** (`.clinerules`).

### 18.1 What Changes For The Admin Today

| Today (manual) | Tomorrow (self-service) |
|---|---|
| Admin emails/SMS's "your domain `example.co.ke` expires in 15 days, please renew" | Dashboard shows the client an **expiry countdown** + a **Pay & Renew** button with the amount |
| Admin renews the domain on Sav.com on the client's behalf | Client clicks **Renew**, pays via M-Pesa, the renewal fires on the backend |
| Admin quotes a bigger hosting plan over the phone | Client clicks **Upgrade**, price difference prorated, invoice raised, account auto-provisioned |
| Admin resells SMS credit manually, one client at a time | Client tops up the **KES wallet** and buys **SMS bundles** from the dashboard |
| Admin never runs campaigns for the client | Client creates a **bulk SMS campaign** and sends it to their own list |
| "Do you have a Sender ID?" — never formalised | Client **orders a Sender ID** in the dashboard and tracks approval status |
| WhatsApp outreach is a consultancy project each time | Client buys a **WhatsApp Business API** subscription and **sends to their own customers** |

**Admin keeps control** of: price approval, refunds, disputes, abuse/complaint escalation, and any
high-value or out-of-policy action (see §18.8).

### 18.2 Multi-Domain Portfolio

- A client may hold **any number of domains** under **one account / one login** (§2.1).
- The dashboard's default landing module is a **portfolio table**, not a single-domain form:
  `domain · registrar · expiry date · days remaining · auto-renew · status · actions`.
- Filters and sorting: expiring soonest, auto-renew off, grace/suspended, by TLD, by registrar status.
- **Bulk actions** on the portfolio (select many → renew all / toggle auto-renew) — each action
  produces its own invoice line so money is never bundled ambiguously.
- Domain search & registration stays on the §3 Sav.com flow, but the **cart may hold several domains**,
  paid in one M-Pesa transaction and registered each idempotently (§3.2).
- **Nameservers** editable by the client from the dashboard, validated against the zone format before
  being pushed to the registrar; a zone/ownership check is required before any nameserver change.
- **Transfer in:** the client starts an inbound transfer, pays the 1-year renewal, and the platform
  monitors the transfer until the registrar confirms, then flips the record to `active`.
- Guard: a domain may never be renewed, transferred or released without an explicit confirmation step
  showing the **amount in KES** and the **resulting expiry date**.

### 18.3 Self-Serve Renewals

- Every service row carries `renewal_price_kes`, `expires_at`, `auto_renew` and `grace_days`.
- **Renew action** on any service: shows price + resulting expiry, then M-Pesa checkout; on confirmed
  payment a **renewal job** calls the registrar / host (§3, §4) and writes the new expiry back.
- **Auto-renew**: opt-in per service with an explicit consent tick and a saved payment method; the daily
  cron attempts the charge inside the renewal window (§5) and warns the client before it fires.
- If auto-renew fails (insufficient balance, declined STK), the client sees a **retry** banner and the
  service follows the existing escalation policy `grace` → `suspended` (§5.2).
- The §5 reminder ladder (90/60/30/15) remains, but it now **points the client at the dashboard** rather
  than asking them to reply to a person — reminders deep-link to the service's renewal screen.

### 18.4 Hosting Self-Service — Package Change & Storage Add-Ons

- **Change package** (upgrade or downgrade) directly from the dashboard:
  `current package → target package` → show **prorated cost** and the new due date → M-Pesa payment →
  WHM/cPanel change-package call (§4). Downgrades past a usage threshold require admin approval.
- **Add storage** as a metered add-on: extra disk quota (GB) purchased per service, priced per GB per
  year and shown as `used / total` in the dashboard; the purchase is instant and lands on the invoice.
- Add-on catalogue (covering the storage requirement above): **extra disk space**, extra bandwidth,
  extra databases, extra mailboxes, dedicated IP — priced in a `hawlast_addons` catalogue so pricing is
  data, not code.
- The client can also self-request **suspend** of their own hosting; **reactivate remains admin-only**
  until §15 item 2 is decided (§18.9).
- All changes are idempotent and logged; a failed WHM call leaves the package unchanged and raises an
  admin task — the client is never charged for a change that did not happen.

### 18.5 Buy SMS Credit & Bulk SMS

- **Wallet-funded SMS**: the client tops up the **KES** wallet (§6.3), then buys **SMS bundles**
  (e.g. 1k / 10k / 100k pages) at the configured markup; pages are credited to an SMS page balance
  through a ledger row.
- **Bulk SMS campaign** in the dashboard:
  `create campaign → choose Sender ID (§18.6) → upload/list recipients → compose → review → send`.
- Recipient lists are stored encrypted; every send writes a per-recipient status
  (`queued → sent → delivered / failed`) surfaced as a campaign report and downloadable export.
- Scheduling (send now / send at a time), a message-length guard (parts vs characters), and a hard
  **cost preview** — the exact page count and KES cost are always shown and must be confirmed before
  dispatch.
- Every message must carry an **opt-out** instruction and honour a **STOP** reply that flips the
  recipient to suppressed, consistent with the §17.B5 privacy/suppression design.
- Abuse guardrails: per-client daily send ceiling, a complaint threshold that auto-pauses a campaign,
  and an admin kill-switch both per client and platform-wide.

### 18.6 Order SMS Sender ID

- A **Sender ID** is the alphanumeric name shown to the recipient; it must be **registered and approved
  by the telco/aggregator before use**, so the order is a *workflow*, not a checkout.
- Order flow: `choose name + type (Alphanumeric / Numeric) → business name & registration details
  → submit → pending_approval → approved (usable) | rejected (reason shown, may re-order)`.
- The dashboard shows each Sender ID's **status, linked content type and monthly usage**; a campaign
  can only select an **approved** Sender ID.
- **Ownership:** a Sender ID belongs to one client account; names are unique platform-wide and are
  validated against reserved/prohibited patterns before submission.
- Registration details are PII → stored encrypted, never selected via `SELECT *`, and only ever rendered
  to the owning client and to admins on an explicit action (consistent with §17).

### 18.7 WhatsApp — Clients Message Their Own Customers

- The platform becomes the **on-ramp for WhatsApp Business API**, not just a consultancy (this aligns
  with the **AI Automation** pillar in §14.2).
- **Subscribe:** the client buys a WhatsApp Business subscription and gets a linked number provisioned;
  the platform holds the **WABA + phone number + API credentials** on the client's behalf.
- **Send:** from the dashboard the client sends WhatsApp messages to **their own opted-in customers**
  via a campaign composer similar to §18.5 (audience list, template, schedule, per-message status).
- **Templates:** business-initiated messages must use **approved templates**; the platform keeps a
  template library with approval status and rejects unapproved free-form sends outside the customer-
  service window.
- Chat **handoff**: a conversation can be escalated to a human agent or an AI agent (AI Automation
  pillar), and keyword-triggered auto-replies are configured on the same screen.
- Consent & compliance: recipients must be opted-in; the platform records consent proof, honours
  opt-outs, enforces Meta's conversation categories/tiers, and surfaces 24-hour customer-service windows.
- Credentials and message bodies are PII — content is **never** dumped into logs or exports; reports
  carry only counts and statuses (§17.B display rules).

### 18.8 Non-Functional & Guardrails

- **Permissions:** every §18 action is role-checked (client vs admin) server-side; hiding UI is cosmetic.
- **Money:** pricing, proration and cost previews are computed server-side in KES and shown before any
  irreversible action; balances are derived from the ledger, never from a mutable cached field.
- **Idempotency:** renewals, package changes, add-ons, campaigns and provisioning each get a request id
  + job row, so retries never double-charge or double-send.
- **Rate limits & abuse:** per-client send quotas, per-IP login throttling, and an admin kill-switch.
- **Privacy/PII:** recipient numbers, message bodies, Sender ID business details and chat content are all
  PII — encrypt at rest, mask in listings, never `SELECT *`, and honour the §17.B erasure/suppression
  flows (an erasure request must also suppress that contact from future outbound campaigns).
- **Audit:** every self-service mutation writes an audit row (who, what, amount, before/after status).

### 18.9 Assumptions & Open Questions

- **Reactivation:** allow clients to self-serve hosting reactivation, or keep it admin-only (§15 item 2)?
- **Pricing/markup:** fixed KES vs % markup on domains, hosting, SMS pages, Sender IDs and the WhatsApp
  subscription (§15 item 1)?
- **Billing model:** are SMS pages sold **prepaid as bundles**, **post-paid on a monthly invoice**, or
  both? Same question for storage add-ons (annual vs monthly).
- **Reseller tier:** do larger clients get sub-accounts / resale pricing (wholesale pages, custom Sender
  IDs), and may a client ever send **on behalf of** another client?
- **Sender ID provider:** which telco/aggregator handles registration and approval turnaround?
- **WhatsApp:** Meta-hosted (WABA) vs an aggregator/BSP — who owns the number and who bills?
- **Proration:** how are mid-term upgrades/downgrades and storage add-ons prorated and refunded?
- **Compliance:** confirm retention/consent rules for recipient lists, message bodies and chat logs so
  they align with §17.B and current data-protection expectations.

---

*Section 18 added as a planning artefact only. No database or code changes have been executed; all
implied schema changes remain blocked pending explicit SQL approval.*
