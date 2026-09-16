# HomeLink - Group 3

## Open the application
On this computer, double-click **START HOMELINK.cmd** in this folder.
Open **http://127.0.0.1:8000**. Use **STOP HOMELINK.cmd** when finished.

The installed Desktop copy includes PHP, Composer, dependencies, built assets, configuration and a fictional demonstration database. No online account or payment is needed.

| Role | Email |
| --- | --- |
| Customer | customer@homelink.test |
| Provider | provider@homelink.test |
| Administrator | admin@homelink.test |

All demo accounts use **HomeLink@123**.

## Project contents
- **homelink/** - Laravel application, tests and locked dependencies.
- **homelink/docs/HomeLink-Group-3-Report.pdf** - 30-page engineering report.
- **homelink/docs/HomeLink-Group-3-Presentation.pptx** - 12 editable slides with speaker notes.
- **homelink/docs/** - editable Markdown report, requirements, stories, design diagrams, risk assessment, maintenance, user/deployment guides, test register and viva notes.
- **homelink/references/** - untouched copies of your two supplied PDFs.
- **tools/** - project-local PHP and Composer for this computer.
- **.github/workflows/ci.yml** - CI recipe, ready for the team's repository.
- **homelink/Dockerfile** and **compose.yaml** - supplied container setup; not executed because Docker was unavailable.

## Verification
88 automated tests / 241 assertions passed. Production assets build successfully. Live browser checks covered the booking journey, customer review, cancellation, mobile layout and administration. Evidence is in homelink/docs/evidence.

## For another computer
The source ZIP contains source, documents and built frontend assets. It excludes PHP/Composer binaries, vendor, node_modules, the local .env and the demo database.
Follow **homelink/docs/DEPLOYMENT.md** to install dependencies, generate a new application key, migrate and seed a fresh local demonstration database. The desktop launcher expects the local tools/ layout; otherwise use php artisan serve as documented.

## Before submission
Fill the real five-member details and contribution record in **homelink/docs/CONTRIBUTIONS.md**. Validate requirements with stakeholders and obtain lecturer approval for CR-01. The implementation is AI-assisted; no interviews, student commits, approvals, Docker run, remote CI run or public deployment are fabricated.
