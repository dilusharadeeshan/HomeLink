# HomeLink
## Group 3 — ICT2232 Software Engineering Mini Project
University of Ruhuna · Department of Information and Communication Technology  
Report date: 16 September 2026  
Prepared for Group 3. Five member names and student numbers: to be supplied by the group.

## 1. Introduction and problem domain
HomeLink connects households with home-service providers such as electricians, plumbers and cleaners. The provided proposal identifies fragmented discovery through personal contacts and separate communication channels as the central problem. Customers need a clear way to compare available services and coordinate a visit. Providers need a place to explain their work and organize incoming requests.

This implementation continues the proposal’s HomeLink identity, Laravel choice, three user roles, Agile process and 17 functional requirement identifiers. It completes the application and supplies the missing engineering artifacts. The assignment is a software-engineering exercise covering the full SDLC, not only a visual prototype.

## 2. Problem statement
A customer may struggle to identify an appropriate provider, learn what a service includes, agree a time and track a request. A provider may struggle to present services consistently or see overlapping commitments. Information scattered across different channels makes errors and misunderstandings more likely.

HomeLink centralizes service information and booking state. Its main workflow is discover → request → accept → start → complete → review. A separate maintenance increment adds eligible customer cancellation.

## 3. Stakeholders and objectives
| Stakeholder | Need | Delivered response |
| --- | --- | --- |
| Customer | Find relevant expertise and follow a request | Filters, details, booking form, history and timeline |
| Provider | Present work and organize incoming requests | Profile, owned listings, status workflow |
| Administrator | Operate the platform and manage misuse | Account search, suspension and reactivation |
| Group 3 | Understand and explain an SDLC implementation | Source, tests, design, traceability and demo guide |
| Lecturer | Assess decisions, working behavior and contribution | Local deployment package and engineering evidence |

Objectives are to support the proposal’s core requirements; keep records persistent; prevent access to other users’ private bookings; reject conflicting provider slots; maintain a useful status history; and provide reproducible setup and tests. Interview-derived validation is still for the group to perform. These stakeholder needs come from the supplied proposal and design analysis, not from invented interviews.

## 4. Scope, assumptions and constraints
Included: customer/provider accounts, profiles, service categories, service discovery, provider listing management, booking lifecycle, administration, validation, ownership controls, reviews and cancellation.

Excluded: online payment collection, live chat, email/SMS delivery, GPS, native mobile apps, advanced AI recommendations, smart-home integration, background checks, enterprise accounting and multi-region operations. The proposal left some of these undecided. Reviews were selected as a feasible enhancement; payments remain direct between customer and provider.

Assumptions: one provider performs one two-hour appointment at a time; service cities are provider-entered labels; all times use Asia/Colombo; customers and providers communicate using the booking contact details; the listed price is a starting quote, not an online payment charge. Providers keep descriptions and availability accurate. Slots begin at 08:00, 10:00, 12:00, 14:00 and 16:00 and may be requested up to 90 days ahead.

Constraints include academic time, five team members, a Windows demonstration computer, and limited external service setup. SQLite makes the local demo self-contained; MySQL configuration remains supported by Laravel but has not been integration-tested for this delivery. Docker is specified for reproducible team environments but is not installed on the inspected computer.

## 5. Process model and project planning
The proposal selects an iterative Agile model. This is appropriate because identity, service management, discovery, booking and administration can each produce a usable increment. Early tests reduce integration surprises. A small modular monolith keeps those modules in one deployable application.

The proposed five-sprint plan is in AGILE-PLAN.md. It is a planning artifact, not a claim that the group held those sprints. The actual local commit history records AI-assisted implementation, a tested baseline and subsequent changes. Team members should develop and review their own follow-up work under their own identities.

## 6. Requirements engineering
REQUIREMENTS.md preserves FR-01 to FR-17 and NFR-01 to NFR-07. Additional implemented requirements cover reviews, cancellation and completion tracking. It defines business rules, use-case descriptions, MoSCoW priorities and links to implementation and test evidence.

AGILE-PLAN.md expands the proposal’s four fully described stories to fourteen, including the story identifiers already referenced in its unfinished traceability matrix. Acceptance criteria describe observable outcomes. Stakeholders should validate the requirements before treating them as a formally approved baseline.

## 7. System design
The browser receives server-rendered Blade pages. Alpine handles local interface state and availability requests. GSAP supplies progressive motion; reduced-motion preferences disable decorative animation. Vite builds local CSS, JavaScript and fonts.

Laravel web middleware manages sessions, request-forgery protection and authentication. Role middleware restricts workspaces; policies restrict individual records. Controllers validate HTTP input and coordinate application operations. BookingService owns transactional booking creation, state transitions and cancellation. Eloquent models persist domain records.

DESIGN.md contains architecture, use cases, a class/domain model, an ER diagram, two sequence diagrams, an activity diagram and wireframes. Designs reflect the implemented system, including soft-deleted services and quoted-price snapshots.

## 8. Technology decisions
| Technology | Reason |
| --- | --- |
| Laravel 13 / PHP 8.4 | Requested framework; built-in validation, sessions, policies, migrations, testing and ORM |
| Blade | Direct mapping between routes and accessible pages; simple for a student team to explain |
| Alpine.js | Small interactive controls without a separate frontend API application |
| Tailwind CSS 4 and component CSS | Responsive foundations plus a consistent bespoke visual system |
| GSAP | Sequenced entrance effects and progressive card motion with reduced-motion support |
| Lucide and local font files | Consistent icons and typography without runtime CDN dependencies |
| SQLite | Persistent, portable, zero-service local demonstration |
| Vite / npm lockfile | Reproducible frontend asset builds |
| PHPUnit and Pint | Automated behavior checks and consistent PHP formatting |
| Docker Compose | Supplied portable deployment definition; execution remains to be validated |
| Git and GitHub Actions | Local history and a ready CI workflow; no remote repository was created |

Version locks are authoritative. More dependencies were not added simply to increase the technology count.

## 9. Implementation
Three roles have separate capabilities. Public visitors may browse and read service details. A customer can request a slot, inspect their booking timeline and review a completed booking. A provider can maintain their own listings and move their bookings through the allowed stages. An administrator can manage ordinary accounts but cannot suspend administrators.

Booking creation validates the request, reloads the listing inside a transaction, stores a price/title snapshot, reserves a unique provider/time slot and creates the first history event. A conflict rolls back every part of the request. The unique database constraint is the final guard against competing writes; SQLite does not provide the same row-lock behavior as MySQL.

Removing a listing soft-deletes it. Existing bookings retain the relationship and price snapshot, preserving history. Suspending a user prevents the next authenticated request and hides that provider’s services. Reviews require a completed booking, the matching customer, and a unique booking ID.

Fictional sample data is deliberately labeled. The provider profile indicator does not claim professional identity verification. All photographs are illustrative.

## 10. Testing and quality assurance
The tested baseline passed 79 tests with 213 assertions. The cancellation increment added nine cases, resulting in 88 passing tests and 241 assertions. Unit tests cover status transition rules; HTTP feature tests integrate routing, middleware, validation, policies, services and the real in-memory SQLite database.

Tests include valid and invalid registration, prohibited administrator registration, account suspension, login throttling, profile privilege injection, forged booking prices, duplicate provider slots, invalid dates, unauthorized record access, invalid state transitions, completed-only reviews and cancellation boundaries. TESTING.md documents the plan, test cases, defects and evidence paths.

Browser checks exercise the live application using fictional credentials, including request creation and provider processing. Mobile and desktop visual inspection supplements the automated tests. This is not independent stakeholder acceptance or a production load test.

## 11. Deployment
The delivered application runs locally at http://127.0.0.1:8000 through a project-local PHP runtime. The Windows launchers start and stop its background development server. Runtime files, secrets and dependencies are excluded from Git.

The Docker recipe provides PHP/Apache, compiled frontend assets and persistent named volumes. It has not been executed on this computer. A public deployment needs a chosen host, TLS, production credentials, backups and a separate validation run. DEPLOYMENT.md gives the complete installation and release procedure and differentiates tested local setup from supplied deployment configuration.

## 12. Maintenance
CR-01 adds customer cancellation of pending/accepted bookings at least two hours before the appointment. It is perfective maintenance: it improves flexibility after the working baseline. The operation releases the reserved slot, writes a timeline event and preserves the booking.

MAINTENANCE.md includes the change, impact analysis, implementation, estimates and regression evidence. CR-01 is an implemented proposed example. The lecturer has not been represented as approving it; the group must obtain approval or implement the actual requested change.

## 13. Security
SECURITY.md documents threats, likelihood/impact, controls and residual risks. Implemented controls include password hashing, session regeneration, CSRF protection, role middleware, ownership policies, validation, bound queries, escaped output, rate limiting and database uniqueness. At least five relevant threats and three implemented controls are covered.

The local demonstration is bound to loopback. Development credentials are never appropriate for a public deployment. Debug responses are disabled by default. No compliance certification or complete security audit is claimed.

## 14. Teamwork and contribution
The five-person roster and individual contributions are deliberately unfilled. CONTRIBUTIONS.md provides an evidence-based log and suggested role rotation. Every member should run the app, inspect the requirements, understand one end-to-end path, add a meaningful improvement or verification, commit it under their own identity and prepare for the viva.

AI-assisted source must be reviewed and understood by the group. The generated commits identify their AI-assisted origin. They are not evidence that any named student wrote or reviewed the code.

## 15. Conclusion and outstanding evidence
HomeLink implements the assigned domain and the proposal’s core workflows with persistent storage, a polished responsive interface and tested access controls. The delivery includes source, local runtime, deployment configuration and SDLC documentation.

The remaining external evidence is a real stakeholder review, member identities and contributions, lecturer-approved maintenance request, team acceptance sign-off and the final presentation. Public hosting, Docker execution and CI execution are explicitly not claimed as completed.

## 16. References
1. ICT2232_Software_Engineering_Mini_Project.pdf — supplied assignment, 12 pages.
2. HomeLink Project Proposal.pdf — supplied incomplete proposal, 26 pages.
3. Laravel documentation: https://laravel.com/framework/docs/13.x
4. PHPUnit documentation: https://docs.phpunit.de/en/12.5/
5. PHP Windows downloads: https://www.php.net/downloads.php?os=windows&version=8.4
6. Composer: https://getcomposer.org/download/
7. Exact installed dependencies: composer.lock and package-lock.json.

## 17. Appendices
Requirements, Agile plan, design diagrams, test reports, security, maintenance, user/deployment guides, presentation notes, attribution and contribution templates are provided as separate editable files in this directory.
