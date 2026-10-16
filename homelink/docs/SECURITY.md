# Security assessment
Scope: HomeLink local academic demo and supplied deployment configuration. This is a practical assessment, not a penetration-test certification.

## Risk register
Likelihood and impact are Low / Medium / High.
| Threat | Likelihood | Impact | Controls implemented | Residual issue |
| --- | --- | --- | --- | --- |
| Credential guessing | High | High | Hashed passwords; 10+ character mixed-case/numeric registration; login throttling | No MFA, password-reset delivery or breached-password check |
| Privilege escalation | Medium | High | Customer/provider registration allow-list; profile ignores role; role middleware | Admin provisioning must remain an operator action |
| Insecure direct object reference | High | High | BookingPolicy and ServiceListingPolicy; ownership-scoped history | Authorized admins can see platform booking details |
| Request forgery | Medium | High | Laravel web request-forgery middleware; CSRF token on every mutation | Deployment must use secure same-site cookies and HTTPS |
| Stored/reflected XSS | Medium | High | Escaped Blade interpolation; no untrusted raw HTML | No full content-security-policy rollout; Alpine requires a considered CSP strategy |
| SQL injection / malicious sorting | Medium | High | Bound ORM queries and explicit sort allow-list | Wildcard search is basic; large catalogs need indexed search |
| Double booking / race | Medium | High | Unique provider/time constraint; atomic booking/slot/event creation; transaction retries | Real multi-process and alternate-database load tests remain pending |
| Session misuse after suspension | Medium | High | ActiveAccount logs out suspended accounts on next request; hidden provider services | Already-delivered page data cannot be recalled |
| Private contact-data exposure | Medium | High | Authentication + owner/provider/admin policy; slot endpoint returns only times | Local disk and backups need OS access controls |
| Secret/demo-account exposure | Medium | High | .env and runtimes ignored; local-only listener; production seeding guard | Demo credentials are public within this package and must never be reused in production |
| Accidental history loss | Medium | Medium | Soft-deleted listings; preserved user rows; quote snapshots | Backup and restore procedures need operational rehearsal |
| Dependency vulnerabilities | Medium | High | Lockfiles; Composer/npm audit; generated CI audit step | Audits describe current advisories, not future safety |

## Implemented security requirements
SR-01: public registration cannot choose administrator.  
SR-02: all mutations require request-forgery protection.  
SR-03: providers can change only their own listings and bookings.  
SR-04: customers can view/cancel/review only their own eligible bookings.  
SR-05: invalid input cannot change privileged attributes or create partial records.  
SR-06: active sessions of suspended accounts are rejected on the next request.  
SR-07: passwords are hashed and never included in user serialization.  
SR-08: public service views escape untrusted text and exclude private contact details.

## Evidence
AuthControllerTest covers weak credentials, administrator-registration rejection, profile role injection, account suspension, throttling and CSRF protection with the testing bypass disabled. CatalogControllerTest covers escaping and sort injection. BookingControllerTest covers price tampering, duplicate slots, wrong-provider updates and unauthorized detail access. ProviderControllerTest covers listing ownership. CancellationTest covers cancellation ownership and timing.

Headers include X-Content-Type-Options: nosniff, X-Frame-Options: SAMEORIGIN and a strict-origin referrer policy. The permissions policy disables camera, microphone and geolocation because the app does not use them.

## Release controls
Use APP_ENV=production, APP_DEBUG=false, HTTPS and SESSION_SECURE_COOKIE=true for public hosting. Use a fresh application key and real credentials; never publish the demo SQLite database. Keep the web root at public/. Protect backups and logs; restrict database and storage permissions. Choose a retention policy for addresses, phone numbers and history with the actual operator before handling real people’s data.

## Known limitations
No provider identity verification, emergency-service guarantee, payment processing, fraud-detection service, formal accessibility certification or legal/compliance certification is implied. TLS, backup monitoring and public hosting are deployment responsibilities that have not been activated in this local delivery.
