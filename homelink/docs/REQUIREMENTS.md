# Requirements specification and traceability
Version 1.1 · Group 3 · 16 September 2026

Source: supplied HomeLink proposal. FR-01–FR-17 and NFR-01–NFR-07 retain their original meaning. FR-18–FR-20 are implementation refinements. Status: implemented baseline for stakeholder review, not an invented stakeholder sign-off.

## Functional requirements
| ID | The system shall… | Priority | Story / use case | Implementation | Main test evidence |
| --- | --- | --- | --- | --- | --- |
| FR-01 | register customers and providers with role selection; reject public admin registration | Must | US-01 / UC-01 | AuthController::register | AuthControllerTest::test_customer_registration_creates_a_hashed_account; test_provider_can_register; test_invalid_registration_is_rejected |
| FR-02 | authenticate active accounts using valid credentials and support logout | Must | US-01 / UC-02 | AuthController::login, logout | test_active_user_can_log_in; test_incorrect_password_does_not_authenticate; test_logout_ends_the_authenticated_session |
| FR-03 | direct signed-in users to their role workspace | Must | US-01 / UC-02 | DashboardController | HomeLinkTest::test_role_dashboard_displays_relevant_workspace; test_admin_dashboard_redirects_to_administration |
| FR-04 | allow users to view and update their own profile | Should | US-02, US-09 / UC-10 | AuthController::profile; profile view | test_profile_updates_ignore_role_and_active_flag_injection |
| FR-05 | allow providers to create listings under predefined categories | Must | US-02 / UC-03 | ProviderController::store | ProviderControllerTest::test_provider_can_create_an_owned_service |
| FR-06 | allow providers to edit, pause or remove their own listings | Should | US-02, US-09 / UC-03 | ProviderController; ServiceListingPolicy; soft deletes | test_provider_can_edit_and_pause_their_service; test_other_provider_cannot_modify_or_delete_service; test_removing_service_preserves_booking_history |
| FR-07 | expose service categories for public browsing | Must | US-03 / UC-04 | CatalogController::index; Category | CatalogControllerTest::test_public_catalog_shows_active_services_and_categories |
| FR-08 | filter services by search text, category and city, with allowed sort options | Must | US-03 / UC-04 | CatalogController::index | test_search_combines_text_category_and_city; test_price_sort_orders_matching_services |
| FR-09 | display service and provider details | Must | US-08 / UC-05 | CatalogController::show; service view | test_public_can_view_service_and_provider_details |
| FR-10 | let customers request a valid future service slot with contact/address details | Must | US-04 / UC-06 | BookingController::store; BookingService::create | BookingControllerTest::test_customer_creates_pending_booking_with_a_price_snapshot; test_invalid_booking_does_not_reserve_a_slot; test_conflicting_request_across_services_is_rejected_atomically |
| FR-11 | show customers their booking statuses | Must | US-04, US-11 / UC-08 | BookingController::index, show | test_customers_see_only_their_own_booking_history |
| FR-12 | show providers incoming requests for their own services | Must | US-05 / UC-07 | BookingController::index; DashboardController | HomeLinkTest::test_role_dashboard_displays_relevant_workspace; role and ownership checks |
| FR-13 | allow the assigned provider to accept or reject pending requests | Must | US-05 / UC-07 | BookingService::transition; BookingPolicy | test_provider_acceptance_updates_status_and_timeline; test_rejection_releases_the_provider_slot; test_other_provider_cannot_update_booking |
| FR-14 | let administrators list and search registered accounts | Should | US-06 / UC-09 | AdminController::index | AdminControllerTest::test_admin_can_view_accounts_and_platform_stats |
| FR-15 | let administrators suspend/reactivate ordinary accounts | Could | US-12 / UC-09 | AdminController::toggle; ActiveAccount | test_admin_can_suspend_a_customer; test_admin_can_reactivate_a_suspended_user; test_suspended_existing_session_is_revoked |
| FR-16 | validate submitted data and show recoverable errors without partial writes | Must | US-07 / all | Controller validation; transactions; flash messages | invalid registration/booking/listing tests and duplicate-slot rollback |
| FR-17 | enforce roles and record ownership on server requests | Must | US-10 / all protected cases | EnsureRole; BookingPolicy; ServiceListingPolicy | test_ordinary_users_cannot_open_admin_tools; test_other_customer_cannot_view_booking_contact_details; role and ownership checks |
| FR-18 | allow one 1–5 star review by the customer on a completed booking | Should | US-13 / UC-11 | BookingController::review; Review unique constraint | test_customer_can_review_a_completed_booking_once; test_unfinished_booking_cannot_be_reviewed |
| FR-19 | cancel an owned pending/accepted booking at least two hours ahead and release its slot | Should / CR-01 | US-14 / UC-12 | BookingService::cancel; BookingPolicy::cancel | CancellationTest, including boundary and rebooking cases |
| FR-20 | allow accepted → in progress → completed and record each transition | Should | US-05, US-11 / UC-07 | BookingStatus; BookingService; BookingEvent | HomeLinkTest::test_provider_can_finish_an_accepted_job; BookingStatusTest |

FR-15 implements the proposal’s disable-or-remove alternative through suspension; permanent user deletion is intentionally not supplied because historical bookings reference users.

## Non-functional requirements and verification
| ID | Category | Measurable interpretation | Verification and current limit |
| --- | --- | --- | --- |
| NFR-01 | Performance | Core page responses should finish within 3 seconds on the local demo under ordinary single-user use | See performance evidence. Not a production concurrency guarantee |
| NFR-02 | Usability | Desktop and phone layouts remain usable; fields have labels; status includes text; keyboard focus is visible; reduced-motion preferences are respected | Browser review at desktop and 390 px. A formal WCAG audit and stakeholder usability study remain pending |
| NFR-03 | Reliability | Rejected requests do not leave partial bookings/slots; errors explain how to retry | Transaction rollback, validation and collision tests |
| NFR-04 | Security | Hash passwords; restrict roles and record ownership; retain CSRF protection | Security tests, policies and middleware |
| NFR-05 | Maintainability | Separate HTTP, domain operations, data models and templates; reproducible tests and format checks | Laravel structure, BookingService, PHPUnit, Pint and lockfiles |
| NFR-06 | Availability | The app is available while its demonstration host/process is running; restart preserves records | File-backed SQLite and launcher. No uptime SLA |
| NFR-07 | Portability | A documented container configuration reproduces the runtime and storage structure | Dockerfile/Compose supplied; Docker execution remains unverified on this host |

## Business rules
- Roles are customer, provider and admin. Registration never accepts admin.
- Browse is public; booking creation requires an active customer.
- A listing is bookable only when it and its provider are active.
- A provider has at most one reserved appointment for a given start time across all their listings.
- Five non-overlapping two-hour start slots operate daily. No holidays or custom weekly availability are implemented.
- Quote and service title are copied into bookings at request time.
- Provider transitions: pending → accepted/rejected; accepted → in_progress; in_progress → completed.
- Customer cancellation applies only to pending/accepted bookings with start >= now + two hours.
- Completed, rejected and cancelled states cannot be reopened.
- Rejection/cancellation frees the reserved slot. Completed bookings remain history.
- Reviews require the matching customer, completed status, integer rating 1–5 and a 10–1000 character comment.
- Administrators cannot suspend another administrator or themselves.
- HTML input is displayed as escaped text.

## Use cases
| ID | Actor / precondition | Main flow | Alternate / result |
| --- | --- | --- | --- |
| UC-01 Register | Visitor | Enter details, select customer/provider, submit, create hashed account, sign in | Invalid fields or duplicate email keep the form and show errors |
| UC-02 Authenticate | Active account holder | Submit credentials, regenerate session, open role dashboard | Incorrect or suspended account returns a generic credential error; repeated attempts are limited |
| UC-03 Manage service | Provider signed in | Create category/title/description/price/city listing; edit, pause or remove owned listing | Wrong owner is forbidden; soft deletion preserves bookings |
| UC-04 Discover | Any visitor | Enter text/city, choose category and sort, inspect results | Empty state offers clear filters |
| UC-05 View details | Active listing | Open service; read provider, price note and completed-booking reviews | Hidden/removed service returns not found |
| UC-06 Request booking | Customer and active listing | Choose date and slot, enter address/phone, validate, persist booking + reservation + event atomically | Collision or invalid data leaves no partial request |
| UC-07 Process booking | Assigned provider | Review details; accept/reject; start; complete | Wrong provider is forbidden; stale/invalid transitions show errors |
| UC-08 Review history | Signed-in customer/provider | List owned requests; filter status; open detail/timeline | Another ordinary user cannot inspect contact details |
| UC-09 Manage accounts | Administrator | Search, inspect account role/status, suspend or reactivate | Admin accounts remain protected |
| UC-10 Maintain profile | Signed-in user | Update personal details and bio; change password with current password | Role injection is ignored; duplicate email/incorrect current password rejected |
| UC-11 Submit review | Customer, completed owned booking | Enter rating/comment; save once; show on service | Unfinished/wrong-owner/duplicate review rejected |
| UC-12 Cancel | Customer, owned eligible booking | Confirm cancellation; transactional state change, slot deletion and event | Inside two hours or terminal state leaves records unchanged |

## Design linkage
Identity and roles map to the architecture’s web middleware and policy layer. FR-05–FR-09 map to Category, ServiceListing and User in the domain/ER diagrams. FR-10–FR-13 and FR-18–FR-20 map to the booking sequence, lifecycle activity, Booking, BookingSlot, BookingEvent and Review. UC-12 is shown explicitly in the maintenance flow. Test case IDs and expected/actual results are in TESTING.md and the generated test register.

## Validation record
Requirements derived from the provided proposal and refined during implementation. Group stakeholder review: pending. Lecturer acceptance of maintenance CR-01: pending. Record actual reviewers, dates and changes in CONTRIBUTIONS.md.
