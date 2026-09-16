# CR-01 — Customer cancellation
Status: implemented proposed change; lecturer approval pending.  
Classification: perfective maintenance.  
Baseline: local Git commit c0fd7c9, 79 passing tests.

## Request
Allow a customer whose plans have changed to cancel their own pending or accepted booking at least two hours before the scheduled start. Free the provider’s reserved time and retain the historical record.

This example was selected during implementation. The assignment requires a lecturer-provided or approved request, so obtain actual approval before claiming this satisfies the assessed maintenance activity.

## Before and after
Before: a customer could track requests, but only providers could change their status.  
After: the customer sees cancellation controls for eligible bookings, confirms the action, and receives a cancelled status and timeline event. The reserved slot becomes bookable again.

## Acceptance criteria
1. Only the booking’s customer may cancel.
2. Eligible states: pending and accepted.
3. Start time must be >= current time + two hours. The exact boundary is allowed.
4. The action is atomic: state, slot and timeline change together.
5. Ineligible/unauthorized requests leave records unchanged.
6. Repeating cancellation does not add another event.
7. Provider workflow cannot reopen a cancelled booking.
8. Booking list, detail, user guide and traceability include cancellation.

## Impact analysis
| Area | Impact |
| --- | --- |
| Requirements | Adds FR-19 / US-14 / UC-12; refines booking-management scope |
| Design | Adds cancellation branch in D-07; uses existing status enum case and transaction boundary |
| Database | No schema migration required; existing cancelled state and reservation/event tables suffice |
| Code | BookingService::cancel, BookingPolicy::cancel, BookingController::cancel, named POST route |
| UI | Conditional action, confirmation, closed-window guidance and status badge |
| Tests | Nine regression cases in CancellationTest |
| Documentation | Requirements, activity diagram, user guide, demo and this record |
| Operations | No external service, data migration or new dependency |

## Estimate
Planning estimate: approximately 0.5–1 team day including review and regression work. This is not a claim of measured student hours.

## Implementation and rollback
The operation starts a transaction, reloads/locks the booking, checks ownership and current eligibility, changes status, removes the slot and appends an actor-stamped event. State checks occur inside the transaction so a stale page does not authorize an obsolete operation.

For code rollback, revert the CR-01 commit through a reviewed Git revert. Existing cancelled bookings must remain readable; do not rewrite their history or silently rebook slots. No database rollback is required.

## Test result
Nine cancellation tests pass. Full regression result: 88 tests, 241 assertions. Cases cover normal cancellation, the exact cutoff, late requests, another customer, four ineligible states and rebooking the freed slot. See evidence/final-junit.xml.

## Approval record
Lecturer name/date/sign-off: pending.  
Group reviewer and evidence: pending.  
If the lecturer supplies a different request, create CR-02, preserve this record, and perform a fresh impact analysis and regression run.
