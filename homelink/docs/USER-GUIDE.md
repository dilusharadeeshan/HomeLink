# HomeLink user guide
## Launch
In the Desktop SE_project folder, double-click START HOMELINK.cmd. The browser opens http://127.0.0.1:8000. The local server stays running until you use STOP HOMELINK.cmd or shut down the computer.

## Demo accounts
All use password **HomeLink@123**.
- Customer: customer@homelink.test
- Provider: provider@homelink.test
- Administrator: admin@homelink.test

Fictional data is provided for demonstration. The provider account is Nimal Perera and owns the electrical services. Other listed providers have separate accounts.

## Customer
1. Browse the catalog. Enter a service or provider name, choose a city/category, or change the sort.
2. Open a service to read the description, pricing note, provider profile and reviews.
3. Sign in as a customer. Choose a date and available start time, then enter the service address and phone number.
4. Select Request booking. A request starts as Pending; it is not confirmed until the provider accepts.
5. Use My bookings to open a request and see its timeline.
6. Pending/accepted requests can be cancelled at least two hours before the start. Open the request and confirm Cancel booking.
7. After completion, submit a rating and comment once.

Unavailable slots are disabled. Availability can change while a form is open; the server rechecks the reservation on submission. If a slot has just been taken, choose another one.

## Provider
1. Register as a provider or use the provider demo account.
2. Add your contact details and introduction in My profile.
3. Open My services, choose Add a service, and supply a category, title, description, city, price and pricing note.
4. Turn off Accept new bookings to pause a service.
5. Open Booking requests. Accept or decline pending requests; accepted work can be started and then completed.
6. Confirm important actions in the in-page dialog.
7. Editing a service does not rewrite an existing booking’s quote. Removing a service hides it but preserves historical bookings.

## Administrator
Sign in and open Administration. Search accounts by name/email or filter by role. Suspend or reactivate ordinary accounts. A suspended account loses authenticated access on its next request and its services disappear from discovery. Administrators cannot be suspended through this interface.

## Profile and password
My profile saves your name, email, phone, city and introduction. Change password with your current password and a confirmed replacement of at least 10 characters including uppercase, lowercase and a number. Account roles cannot be changed through profile forms.

## Booking states
Pending: waiting for provider. Accepted: provider agreed. In progress: provider started work. Completed: provider marked work done. Rejected: provider declined. Cancelled: customer cancelled within the permitted window.

## Payment and contact
HomeLink does not collect money. The displayed quote is a starting price. Confirm extras and arrange payment directly with the provider. Contact details are visible only to the booking’s participants and administrators.

## Navigation and accessibility
Use Tab/Shift+Tab to move between controls, Enter/Space to activate them, and Escape to close dialogs/navigation. The mobile menu opens the same workspace links. Your device’s reduced-motion preference turns off decorative animation.

## Troubleshooting
- Wrong credentials: use the correct demo email/role, or register your own account.
- Rate-limit message: wait one minute before trying to sign in again.
- Port already used: stop the other app or adjust the launcher port and APP_URL together.
- Server unavailable after reboot: run START HOMELINK.cmd again.
- Old styling after source changes: run npm run build, then refresh.
- No visible services: clear filters or make sure the listing/provider is active.
- Expired form/419: reload the page to obtain a fresh session token.
