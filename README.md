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



## Verification
88 automated tests / 241 assertions passed. Production assets build successfully. Live browser checks covered the booking journey, customer review, cancellation, mobile layout and administration. Evidence is in homelink/docs/evidence.

## For another computer
The source ZIP contains source, documents and built frontend assets. It excludes PHP/Composer binaries, vendor, node_modules, the local .env and the demo database.
Follow **homelink/docs/DEPLOYMENT.md** to install dependencies, generate a new application key, migrate and seed a fresh local demonstration database. The desktop launcher expects the local tools/ layout; otherwise use php artisan serve as documented.


