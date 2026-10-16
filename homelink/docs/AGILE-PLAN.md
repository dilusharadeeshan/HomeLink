# Agile plan, backlog and acceptance criteria
Group 3 · Five-person team · Planning artifact for team adoption.

The proposal specifies Agile with five increments. The schedule below is a recommended plan, not a fabricated sprint history. Assign actual names, dates and evidence after team review.

## Product backlog
| ID | Scope | Priority | Estimate | Dependencies |
| --- | --- | --- | --- | --- |
| PB-01 | Accounts, sessions, profiles and roles | Must | 5 points | None |
| PB-02 | Provider profiles and service management | Must | 5 | PB-01 |
| PB-03 | Browse, search, category/city filtering and details | Must | 5 | PB-02 |
| PB-04 | Requests, conflict prevention and booking lifecycle | Must | 8 | PB-01, PB-02 |
| PB-05 | Administrative accounts and oversight | Should | 3 | PB-01 |
| PB-06 | Validation, error handling and security integration | Must | 3 | Cross-cutting |
| PB-07 | Completed-booking reviews | Should | 3 | PB-04 |
| PB-08 | Customer cancellation, CR-01 | Should | 3 | Tested PB-04 baseline |

## User stories
| ID | As a… I want… so that… | Acceptance criteria |
| --- | --- | --- |
| US-01 | customer/provider, to register and sign in securely, so I reach my workspace | Valid customer/provider details create an account; passwords are hashed; invalid/admin registration is rejected; session regenerates on login |
| US-02 | provider, to introduce myself and publish services, so customers can understand my work | Profile bio is shown; category/title/description/price/city are required; listing belongs to the signed-in provider |
| US-03 | customer, to browse/filter services, so I find relevant help | Text, category and city combine; inactive services/providers are hidden; sort choices are restricted |
| US-04 | customer, to request a date/time, so I can plan a service | Future allowed slot plus valid address/phone creates pending request; quote is saved; conflicting slot produces an error and no partial records |
| US-05 | provider, to respond to requests and record progress, so customers know what happens next | Only assigned provider may act; pending accepts/rejects; accepted starts; in-progress completes; every change has an actor/time |
| US-06 | administrator, to list and search accounts, so I can operate the platform | Admin dashboard shows account search, role/status and activity; ordinary users receive forbidden |
| US-07 | user, to receive clear form errors, so I can fix my input | Errors appear near the main content; safe previous input is retained; invalid requests do not mutate records |
| US-08 | customer, to inspect details before booking, so I can make an informed choice | Service scope, provider, city, starting price and pricing note appear; reviews originate from completed bookings |
| US-09 | provider, to edit/pause/remove my listings, so information remains current | Ownership is enforced; pausing hides catalog availability; removal keeps historical booking details |
| US-10 | account holder, to have role-specific access, so private data and operations stay protected | Registration cannot grant admin; profile submission cannot change role; policies block cross-user booking/listing access |
| US-11 | customer/provider, to see booking history and status, so I can follow the work | Own booking list is paginated/filterable; a detail timeline displays all transitions; private contact details require ownership/admin |
| US-12 | administrator, to suspend/reactivate an ordinary user, so misuse can be managed | Suspension ends the next authenticated request and hides provider services; admin accounts are protected; reactivation restores access |
| US-13 | customer, to review completed work, so others can choose with useful feedback | Matching customer may submit one 1–5 rating with a valid comment; unfinished, duplicate and wrong-owner reviews fail |
| US-14 | customer, to cancel an eligible appointment, so changed plans free the provider’s time | Explicit confirmation required; owner only; pending/accepted and >=2 hours ahead; slot released and history kept |

## Recommended sprint plan
| Sprint | Goal and working increment | Planned owners | Review evidence |
| --- | --- | --- | --- |
| 1 | Identity, requirements, roles and profile | Members 1+2; member 3 reviews | Auth demo, initial requirements and auth tests |
| 2 | Provider listings and domain/database design | Members 2+3; member 4 reviews | Listing CRUD, migration, ER and ownership tests |
| 3 | Discovery and responsive UI | Members 3+4; member 5 reviews | Search demo, interface prototype, keyboard/mobile checks |
| 4 | Booking requests and state workflow | Members 4+5; member 1 reviews | End-to-end demo, sequences and collision tests |
| 5 | Administration, review, deployment, security | All; rotating reviewer | Test report, deploy guide, risk register and acceptance record |
| Maintenance | Lecturer-provided/approved change | Assign after approval | Baseline diff, impact analysis, regression evidence |

Suggested cadence: one week per sprint, 15-minute daily coordination, backlog refinement twice per sprint, review and retrospective at the end. Dates must follow the actual course calendar.

## Definition of ready
A story has an actor, business outcome, acceptance criteria, priority and dependencies; privacy and validation expectations are understood; the team can explain the design.

## Definition of done
Code is reviewed; relevant automated tests pass; important browser paths work; no secrets are committed; requirements/design/user guides are updated; the increment can be demonstrated; actual contributors and reviewers are recorded.

## Team working agreement
Use a branch per change, descriptive commits and reviewed pull requests once your GitHub repository is created. Commit real work under the member’s own identity. Rotate design, implementation, testing and demonstration responsibilities. Do not report these suggested activities as completed without evidence.

## Actual build record
The initial local implementation and baseline tests were produced with AI assistance. Local commits identify that origin. The separate CR-01 increment follows the tested baseline. No team meetings, sprint burndown, stakeholder sign-off or GitHub collaboration history are invented.
