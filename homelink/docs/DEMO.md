# Presentation, demonstration and viva guide
Suggested duration: 10–12 minutes plus questions. This is an editable presentation script; assign actual speakers after the group reviews the work.

## Editable presentation
Open HomeLink-Group-3-Presentation.pptx in this directory. The twelve slides include editable text/diagrams and source notes:
1. HomeLink / Group 3
2. The coordination problem
3. Scope and requirements
4. Three roles, controlled access
5. One Laravel application
6. Bookings preserve the important details
7. The booking journey
8. Interface and motion
9. Verification evidence
10. Security controls
11. Maintenance change CR-01
12. Demonstration and handover

Add actual member names and assign speakers after reviewing the source. Use the live application when presenting slide 8.

## Live demonstration
Before the demo, start HomeLink and run the tests. Use fictional data only. Keep three browser sessions or switch accounts so the lecturer can distinguish each role.

1. Browse the catalog publicly. Filter to Electrical or a city and show results.
2. Open a service. Explain the provider description, pricing note and illustrative photograph.
3. Sign in with customer@homelink.test. Choose an available date/time and enter fictional contact details. Submit and show Pending plus the saved quote.
4. Sign in with provider@homelink.test for that provider’s electrical service. Open the new request; accept, start and complete it. Show the timeline.
5. Return as the customer. Show the completed state and submit a valid review once.
6. Create another future request. Cancel it at least two hours ahead. Show Cancelled and availability returning.
7. Demonstrate a conflicting time request or unauthorized access through the automated tests, preserving other demo records.
8. Sign in as admin@homelink.test. Show account filtering and explain suspension. If changing state, use only a designated demo account and restore it afterward.
9. Run php artisan test --compact and show the saved JUnit report.
10. Explain what was generated with assistance and what each group member actually reviewed or improved.

The UI permits the provider’s accepted → started → completed sequence without waiting for the scheduled appointment, which supports demonstration. Real operational discipline remains with the provider; automated clock-based workflow gating is outside this mini-project scope.

## Likely viva questions
- Why use a modular monolith? Fewer deployment boundaries and easier traceability for a five-person mini project.
- Why a unique slot table? It gives the database the final say against two requests for one provider/time.
- Why transaction + validation? Validation alone can become stale before a write; all booking artifacts must succeed together.
- What does SQLite lockForUpdate do? SQLite does not supply MySQL-style row locks; the unique constraint and transaction protect the reservation invariant.
- Why store quoted_price and service_title? A later listing edit must not rewrite a customer’s historical agreement.
- Why policies as well as roles? Being a provider does not authorize modifying another provider’s service.
- How is review trust scoped? A review proves matching completed booking ownership, not a professional background check.
- Why no online payment? The proposal left it undecided; a gateway would require an additional external security and operational scope.
- Why reduced-motion support? The same information and actions remain usable without decorative animation.
- Which claims remain unverified? Docker startup, public hosting, CI execution, formal stakeholder acceptance and student contributions.

## Team participation
Each member should demonstrate code or tests they personally understand and have changed/reviewed. Do not assign fictitious past work. Attach real commits, PR links, review notes and test evidence in CONTRIBUTIONS.md.
