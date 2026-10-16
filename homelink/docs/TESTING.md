# Test plan and execution report
## Scope and strategy
Verify the prioritized functional requirements, security boundaries, booking transaction invariants and critical UI workflows.

- **Unit:** the permitted BookingStatus transitions.
- **Integration:** HTTP requests through Laravel middleware/controllers/policies/services against real in-memory SQLite.
- **System:** live browser navigation, login, request creation and provider processing in the local app.
- **Acceptance:** executable scenarios derived from user-story criteria; independent team/stakeholder sign-off remains pending.

Tests must not overwrite the demo database. Tests/TestCase uses LazilyRefreshDatabase; phpunit.xml selects :memory:. Password hashing cost is reduced only in testing. No external service is mocked as a completed integration because none is required.

## Entry and exit criteria
Entry: migrations succeed, dependencies install, frontend builds and seed data is available. Exit: all implemented critical-path tests pass, negative cases preserve records, view compilation succeeds and important screens are inspected. Public-host/Docker/CI approval is a separate unexecuted stage.

## Recorded results
Baseline: 79 tests, 213 assertions, all passed.  
After CR-01: 88 tests, 241 assertions, all passed.  
Machine-readable evidence: evidence/baseline-junit.xml and evidence/final-junit.xml.  
Detailed generated case register: TEST-REGISTER.md, including test identifiers, actual outcome and requirement mapping.

The baseline run took approximately 4.3 seconds and the post-change run approximately 5.2 seconds in this environment. These measure the automated suite, not website loading time.

## Representative test cases
| Case | Requirement | Preconditions / input | Expected result | Actual result |
| --- | --- | --- | --- | --- |
| TC-AUTH-01 | FR-01 | Valid customer registration | Account saved, hashed password, signed in | Passed |
| TC-AUTH-02 | FR-01, FR-17 | Register with role=admin | Error, no account | Passed |
| TC-AUTH-03 | FR-02 | Wrong password / suspended user | Generic credential error, guest remains | Passed |
| TC-PROFILE-01 | FR-04, FR-17 | Valid edit plus role/is_active injection | Allowed details update; privilege unchanged | Passed |
| TC-SERVICE-01 | FR-05 | Provider creates valid listing | Listing belongs to provider | Passed |
| TC-SERVICE-02 | FR-06, FR-17 | Another provider edits/deletes listing | Forbidden; original preserved | Passed |
| TC-SEARCH-01 | FR-07–09 | Combined text/category/city query | Only matching active listings | Passed |
| TC-BOOK-01 | FR-10 | Future valid request with forged price | Pending booking; server quote; slot and event saved | Passed |
| TC-BOOK-02 | FR-10, FR-16 | Same provider/time through another listing | Validation error and transaction rollback | Passed |
| TC-BOOK-03 | FR-10 | Past/too-distant date or bad contact fields | Error; no booking/slot | Passed |
| TC-STATUS-01 | FR-13, FR-20 | Assigned provider accepts then processes | Valid state and timeline changes | Passed |
| TC-STATUS-02 | FR-13 | Attempt pending → completed | Error; pending retained | Passed |
| TC-PRIVACY-01 | FR-17 | Other customer opens booking | Forbidden; contact details absent | Passed |
| TC-REVIEW-01 | FR-18 | Completed booking, correct customer | One valid review persists | Passed |
| TC-REVIEW-02 | FR-18 | Duplicate / unfinished / wrong owner | Error or forbidden; no extra review | Passed |
| TC-ADMIN-01 | FR-14–15 | Admin suspends ordinary account | Suspended state persists | Passed |
| TC-ADMIN-02 | FR-15, FR-17 | Suspend admin / ordinary user opens admin | Forbidden | Passed |
| TC-SEC-01 | NFR-04 | Missing CSRF token with bypass disabled | 419; no registration | Passed |
| TC-SEC-02 | NFR-04 | Script text and malicious sort | Escaped output / validation error | Passed |
| TC-CR-01 | FR-19 | Owned pending booking >=2h ahead | Cancelled, slot released, event added | Passed |
| TC-CR-02 | FR-19 | Exact two-hour boundary | Cancellation allowed | Passed |
| TC-CR-03 | FR-19 | Late, foreign-owner or terminal booking | No unauthorized mutation | Passed |
| TC-CR-04 | FR-19 | Book the newly released slot | New reservation succeeds | Passed |

## Defect log
| ID | Observed issue | Correction | Verification |
| --- | --- | --- | --- |
| DEF-01 | Default application URL already included :8000; naive replacement duplicated the port | Replace the complete APP_URL line | Migrations, routes and live server succeeded |
| DEF-02 | Fresh user objects did not contain database role/active defaults until reload | Explicit model attribute defaults | Previously failing auth/provider cases pass |
| DEF-03 | GSAP attempted hero animations on routes without a hero, producing console warnings | Check the target exists before animating | Browser logs after rebuilding |
| DEF-04 | Native confirmation dialog interrupted automated browser interaction | Accessible in-page HTML dialog with cancel/confirm controls | Provider start/completion completed through the live UI |
| DEF-05 | Importing the entire icon collection inflated JavaScript | Import only used icons and Latin font subsets | Build reduced JS from about 510 KB to about 184 KB |
| DEF-06 | Old slot responses could overwrite results after rapid date changes | Apply availability only for the current requested date | Source review; server still enforces conflict protection |

## Browser record
The local browser verified public catalog, customer sign-in, customer booking HL-00030 with fictional data, provider sign-in, acceptance, start and completion, updated timelines, and mobile booking detail at 390×844. Desktop layout was reviewed at the browser’s normal viewport. The mobile drawer opens and the booking detail has no horizontal page overflow. Complete additional final observations are recorded in evidence/QA-SUMMARY.md.

## Reproduction
Run php artisan test --compact from homelink. For one area: php artisan test --compact tests/Feature/BookingControllerTest.php. Regenerate JUnit with --log-junit docs/evidence/final-junit.xml. Do not edit expected outcomes merely to make a failing test pass.

## Limits
No real customer/provider interviews, external acceptance signatures, production concurrency benchmark, cross-database test matrix, full WCAG audit, Docker execution or remote CI execution is claimed.
