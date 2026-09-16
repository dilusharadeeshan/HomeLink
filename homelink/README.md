# HomeLink — Group 3
ICT2232 Software Engineering Mini Project · University of Ruhuna

A working home services booking platform built from the supplied incomplete HomeLink proposal. Laravel 13, PHP 8.4, Blade, Alpine.js, Tailwind CSS 4, GSAP, Lucide, Vite, and a persistent SQLite database.

## Start on this computer
Open the parent **SE_project** folder and double-click **START HOMELINK.cmd**. The app runs at **http://127.0.0.1:8000**. Use **STOP HOMELINK.cmd** to stop only the server started by the launcher.

| Demo role | Email | Password |
| --- | --- | --- |
| Customer | customer@homelink.test | HomeLink@123 |
| Provider | provider@homelink.test | HomeLink@123 |
| Administrator | admin@homelink.test | HomeLink@123 |

All seeded people, contact details, bookings, and reviews are fictional. Demo credentials are for the local demonstration only. There are six categories and nine service listings.

## What works
- Customer/provider registration, login, logout, profile and password changes.
- Public service discovery, text/category/city filters, sorting and pagination.
- Provider-owned service creation, editing, pausing and soft deletion.
- Date/time requests, live slot lookup, price snapshots, transactional conflict prevention.
- Role-specific dashboards and booking histories.
- Provider acceptance, rejection, start and completion, with a timestamped timeline.
- Reviews restricted to the customer on a completed booking.
- Administrator account search, suspension and reactivation.
- Customer cancellation at least two hours ahead; the reserved slot is released.
- Responsive layouts, native accessible confirmation dialogs, keyboard focus styles and reduced-motion support.

Payments are arranged directly with the provider. There is no payment gateway, chat, GPS tracking, email/SMS delivery, identity verification, or production hosting integration.

## Project evidence
Start with [PROJECT-REPORT.md](docs/PROJECT-REPORT.md), the [printable report](docs/HomeLink-Group-3-Report.pdf), or the [editable presentation](docs/HomeLink-Group-3-Presentation.pptx).

- [Requirements and traceability](docs/REQUIREMENTS.md)
- [User stories, priorities and sprint plan](docs/AGILE-PLAN.md)
- [Architecture, UML and ER diagrams](docs/DESIGN.md)
- [Testing and recorded results](docs/TESTING.md)
- [Maintenance change CR-01](docs/MAINTENANCE.md)
- [Security assessment](docs/SECURITY.md)
- [User guide](docs/USER-GUIDE.md)
- [Installation, Docker and deployment](docs/DEPLOYMENT.md)
- [Presentation, demo and viva guide](docs/DEMO.md)
- [Contribution record and remaining approvals](docs/CONTRIBUTIONS.md)

## Development
Use the portable PHP executable in ../tools/php/php.exe or install PHP 8.4 with the extensions listed in the deployment guide.

```powershell
..\tools\php\php.exe artisan test --compact
..\tools\php\php.exe vendor/bin/pint --dirty --format agent
npm run build
```

Baseline: 79 tests / 213 assertions. After CR-01: 88 tests / 241 assertions. JUnit evidence is in docs/evidence. Tests use a separate in-memory database.

## Submission status
The app is implemented and demonstrable locally. Docker and GitHub Actions configuration are supplied but were not executed on this computer. Public hosting has not been provisioned. Stakeholder interviews, lecturer approval of CR-01, real member identities/contributions, and the group’s viva must be supplied by the team; these are not fabricated.
