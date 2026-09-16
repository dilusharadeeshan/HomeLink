# Technical and deployment guide
## 1. Delivered environment
The tested local deployment runs on Windows using project-local PHP 8.4.25, Laravel 13.32.0 and SQLite. Frontend dependencies are locked by package-lock.json; PHP dependencies by composer.lock. Source is in SE_project/homelink. Portable PHP/Composer are in SE_project/tools. Secrets, runtimes, dependency folders and the local database are excluded from Git.

Local URL: http://127.0.0.1:8000. The Windows server binds to loopback and is suitable for a lecturer demonstration on this computer. It is not a public production endpoint.

## 2. Start the delivered copy
Double-click START HOMELINK.cmd in the parent directory. The launcher checks port 8000, starts the bundled PHP server hidden, records its PID and waits for /up. Logs are in .runtime/. STOP HOMELINK.cmd verifies the executable/command before stopping that process. It does not terminate unrelated PHP processes.

The database is already migrated and contains fictional demo data. Seeding again is non-destructive and returns without changes when the demo admin exists.

## 3. Fresh source installation
Requirements: PHP 8.4, Composer 2, Node 22 LTS or a Vite-compatible Node version, npm, Git. PHP extensions: ctype, curl, dom, fileinfo, filter, hash, intl, mbstring, openssl, pcre, PDO, pdo_sqlite, session, tokenizer, XML and zip. SQLite is the default; pdo_mysql is included in the portable runtime for future configuration.

From the homelink directory:
```powershell
composer install --no-interaction --prefer-dist
Copy-Item .env.example .env
php artisan key:generate
New-Item -ItemType File database/database.sqlite -Force
php artisan migrate --force
npm ci
npm run build
php artisan serve --host=127.0.0.1 --port=8000
```
On Unix use cp and touch instead of the PowerShell file commands. On the delivered Windows copy use ../tools/php/php.exe and ../tools/composer.phar if PHP/Composer are not on PATH.

For a new local demo only: keep APP_ENV=local, set HOMELINK_DEMO=true, then run php artisan db:seed. Demo seeding is explicitly refused in production. Do not use migrate:fresh on a database you need to keep.

## 4. Tests and formatting
```powershell
php artisan test --compact
php vendor/bin/pint --dirty --format agent
npm exec -- prettier --write resources/css/app.css resources/js/app.js
npm run build
composer audit
npm audit
```
Tests use DB_DATABASE=:memory: and do not clear the delivered SQLite file. JUnit reports can be generated with --log-junit docs/evidence/final-junit.xml.

## 5. Docker Compose (supplied, not executed here)
Docker was not available on the inspected computer, so the container build and startup are unverified. Install Docker Desktop, then from homelink run:
```sh
docker compose up --build -d
docker compose logs -f app
```
Open http://127.0.0.1:8080. The recipe builds assets in a Node stage and runs PHP/Apache with public/ as the web root. It stores SQLite data in /var/lib/homelink and framework storage in a separate volume. The demonstration Compose file sets APP_ENV=local and seeds fictional data once. Its generated application key persists in the storage volume.

Stop without deleting data: docker compose down. Removing volumes is destructive; do it only for an intentionally disposable environment.

## 6. Production adaptation
Choose a PHP-capable host; use PHP-FPM behind Nginx or a validated Apache/container setup. Serve only public/. Build assets with npm ci && npm run build. Install dependencies with composer install --no-dev --optimize-autoloader. Supply a fresh APP_KEY, APP_ENV=production, APP_DEBUG=false, correct HTTPS APP_URL, SESSION_SECURE_COOKIE=true and HOMELINK_DEMO=false. Never deploy the supplied demo database or local demo passwords.

Supply database credentials through host secrets. MySQL support is configured by Laravel but requires a separate integration-test run. Use a dedicated account with least necessary privileges. For SQLite, keep a single persistent writable volume and design backups around the running database.

Run migrations as a controlled deployment step, then php artisan optimize. Make storage/ and bootstrap/cache writable by the web-service user. Configure TLS at the host/reverse proxy. Validate /up and the full login/booking workflow before switching traffic.

No public deployment, hosting account, domain or billing resource was created in this delivery.

## 7. Backup / restore
Back up the SQLite database consistently with SQLite’s backup API or while the app is stopped. Back up relevant storage files and preserve the application key separately in secure storage. A copied database without its matching application key can invalidate encrypted data/sessions.

Suggested local backup:
```powershell
# Stop HomeLink first, then copy database/database.sqlite to a dated, protected backup.
# Restore only to a stopped application, retain the original backup, and run a smoke test.
```
A backup procedure is supplied; a disaster-recovery drill is not claimed as completed.

## 8. CI and collaboration
The repository root .github/workflows/ci.yml installs PHP/Node dependencies, runs tests/build and Composer audit. It becomes active after the team pushes this repository to GitHub. No GitHub remote or CI run was created. The local commits are AI-assisted setup evidence, not individual student contributions.

## 9. Time, currency and service boundaries
All stored appointment times are interpreted in Asia/Colombo. Fixed two-hour slots are a deliberate domain constraint. Prices are integer LKR values. No external mail, SMS, queue worker or payment credentials are required by the current workflows.

## 10. Runtime provenance
Portable PHP was downloaded from the official PHP Windows release and verified against its SHA-256. Composer’s installer was verified against the official SHA-384 signature endpoint. The included runtime retains vendor licenses. Build dependencies come from Packagist/npm with committed lockfiles.
