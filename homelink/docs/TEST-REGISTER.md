# Executed automated test register

Source: final-junit.xml. 88 tests, 241 assertions, 0 failures, 0 errors. Elapsed 5.229775 seconds.

Requirement mappings identify the area exercised; inspect the actual assertions for the precise coverage. Every dataset is listed separately.

| ID | Test class / case | Area | Assertions | Seconds | Result |
| --- | --- | --- | --- | --- | --- |
| AT-001 | BookingStatusTest / test_status_transition_contract with data set "accept" | FR-13/19/20 | 1 | 0.002799 | PASS |
| AT-002 | BookingStatusTest / test_status_transition_contract with data set "decline" | FR-13/19/20 | 1 | 0.002007 | PASS |
| AT-003 | BookingStatusTest / test_status_transition_contract with data set "start" | FR-13/19/20 | 1 | 0.001648 | PASS |
| AT-004 | BookingStatusTest / test_status_transition_contract with data set "finish" | FR-13/19/20 | 1 | 0.001151 | PASS |
| AT-005 | BookingStatusTest / test_status_transition_contract with data set "skip" | FR-13/19/20 | 1 | 0.001110 | PASS |
| AT-006 | BookingStatusTest / test_status_transition_contract with data set "reopen" | FR-13/19/20 | 1 | 0.001019 | PASS |
| AT-007 | BookingStatusTest / test_status_transition_contract with data set "reaccept" | FR-13/19/20 | 1 | 0.001003 | PASS |
| AT-008 | BookingStatusTest / test_status_transition_contract with data set "restart cancelled" | FR-13/19/20 | 1 | 0.000969 | PASS |
| AT-009 | ExampleTest / test_that_true_is_true | Scaffold smoke check | 1 | 0.000984 | PASS |
| AT-010 | AdminControllerTest / test_admin_can_view_accounts_and_platform_stats | FR-14/15/17 | 2 | 0.611233 | PASS |
| AT-011 | AdminControllerTest / test_admin_can_suspend_a_customer | FR-14/15/17 | 2 | 0.029368 | PASS |
| AT-012 | AdminControllerTest / test_admin_can_reactivate_a_suspended_user | FR-14/15/17 | 2 | 0.021640 | PASS |
| AT-013 | AdminControllerTest / test_administrators_are_protected_from_suspension | FR-14/15/17 | 2 | 0.033235 | PASS |
| AT-014 | AdminControllerTest / test_ordinary_users_cannot_open_admin_tools with data set #0 | FR-14/15/17 | 1 | 0.027875 | PASS |
| AT-015 | AdminControllerTest / test_ordinary_users_cannot_open_admin_tools with data set #1 | FR-14/15/17 | 1 | 0.023441 | PASS |
| AT-016 | AuthControllerTest / test_customer_registration_creates_a_hashed_account | FR-01/02/04/16/17 | 7 | 0.041241 | PASS |
| AT-017 | AuthControllerTest / test_provider_can_register | FR-01/02/04/16/17 | 3 | 0.024088 | PASS |
| AT-018 | AuthControllerTest / test_invalid_registration_is_rejected with data set "admin role" | FR-01/02/04/16/17 | 3 | 0.024207 | PASS |
| AT-019 | AuthControllerTest / test_invalid_registration_is_rejected with data set "weak password" | FR-01/02/04/16/17 | 3 | 0.028133 | PASS |
| AT-020 | AuthControllerTest / test_invalid_registration_is_rejected with data set "mismatch" | FR-01/02/04/16/17 | 3 | 0.030673 | PASS |
| AT-021 | AuthControllerTest / test_invalid_registration_is_rejected with data set "invalid email" | FR-01/02/04/16/17 | 3 | 0.023921 | PASS |
| AT-022 | AuthControllerTest / test_invalid_registration_is_rejected with data set "invalid phone" | FR-01/02/04/16/17 | 3 | 0.031550 | PASS |
| AT-023 | AuthControllerTest / test_invalid_registration_is_rejected with data set "missing city" | FR-01/02/04/16/17 | 3 | 0.041189 | PASS |
| AT-024 | AuthControllerTest / test_duplicate_email_is_rejected | FR-01/02/04/16/17 | 3 | 0.026158 | PASS |
| AT-025 | AuthControllerTest / test_active_user_can_log_in | FR-01/02/04/16/17 | 5 | 0.032554 | PASS |
| AT-026 | AuthControllerTest / test_incorrect_password_does_not_authenticate | FR-01/02/04/16/17 | 3 | 0.237757 | PASS |
| AT-027 | AuthControllerTest / test_suspended_user_cannot_log_in | FR-01/02/04/16/17 | 3 | 0.236811 | PASS |
| AT-028 | AuthControllerTest / test_suspended_existing_session_is_revoked | FR-01/02/04/16/17 | 3 | 0.047217 | PASS |
| AT-029 | AuthControllerTest / test_profile_updates_ignore_role_and_active_flag_injection | FR-01/02/04/16/17 | 2 | 0.043600 | PASS |
| AT-030 | AuthControllerTest / test_password_change_requires_current_password | FR-01/02/04/16/17 | 3 | 0.040088 | PASS |
| AT-031 | AuthControllerTest / test_password_can_be_changed_with_current_password | FR-01/02/04/16/17 | 2 | 0.039091 | PASS |
| AT-032 | AuthControllerTest / test_logout_ends_the_authenticated_session | FR-01/02/04/16/17 | 3 | 0.030535 | PASS |
| AT-033 | AuthControllerTest / test_login_is_throttled_after_six_failed_attempts | FR-01/02/04/16/17 | 1 | 1.286566 | PASS |
| AT-034 | AuthControllerTest / test_missing_csrf_token_is_rejected_outside_testing_bypass | FR-01/02/04/16/17 | 2 | 0.036170 | PASS |
| AT-035 | BookingControllerTest / test_customer_creates_pending_booking_with_a_price_snapshot | FR-10/11/12/13/16/17/18/20 | 6 | 0.041215 | PASS |
| AT-036 | BookingControllerTest / test_guest_must_sign_in_to_book | FR-10/11/12/13/16/17/18/20 | 3 | 0.021704 | PASS |
| AT-037 | BookingControllerTest / test_provider_cannot_book_as_customer | FR-10/11/12/13/16/17/18/20 | 2 | 0.024787 | PASS |
| AT-038 | BookingControllerTest / test_invalid_booking_does_not_reserve_a_slot with data set "past date" | FR-10/11/12/13/16/17/18/20 | 4 | 0.023517 | PASS |
| AT-039 | BookingControllerTest / test_invalid_booking_does_not_reserve_a_slot with data set "beyond horizon" | FR-10/11/12/13/16/17/18/20 | 4 | 0.023280 | PASS |
| AT-040 | BookingControllerTest / test_invalid_booking_does_not_reserve_a_slot with data set "non-slot time" | FR-10/11/12/13/16/17/18/20 | 4 | 0.023869 | PASS |
| AT-041 | BookingControllerTest / test_invalid_booking_does_not_reserve_a_slot with data set "past same-day slot" | FR-10/11/12/13/16/17/18/20 | 4 | 0.024785 | PASS |
| AT-042 | BookingControllerTest / test_invalid_booking_does_not_reserve_a_slot with data set "short address" | FR-10/11/12/13/16/17/18/20 | 4 | 0.024511 | PASS |
| AT-043 | BookingControllerTest / test_invalid_booking_does_not_reserve_a_slot with data set "bad phone" | FR-10/11/12/13/16/17/18/20 | 4 | 0.023035 | PASS |
| AT-044 | BookingControllerTest / test_invalid_booking_does_not_reserve_a_slot with data set "oversized notes" | FR-10/11/12/13/16/17/18/20 | 4 | 0.025014 | PASS |
| AT-045 | BookingControllerTest / test_hidden_service_cannot_be_booked | FR-10/11/12/13/16/17/18/20 | 3 | 0.025667 | PASS |
| AT-046 | BookingControllerTest / test_conflicting_request_across_services_is_rejected_atomically | FR-10/11/12/13/16/17/18/20 | 5 | 0.030641 | PASS |
| AT-047 | BookingControllerTest / test_slot_endpoint_exposes_only_unavailable_times | FR-10/11/12/13/16/17/18/20 | 1 | 0.028440 | PASS |
| AT-048 | BookingControllerTest / test_provider_acceptance_updates_status_and_timeline | FR-10/11/12/13/16/17/18/20 | 4 | 0.030501 | PASS |
| AT-049 | BookingControllerTest / test_rejection_releases_the_provider_slot | FR-10/11/12/13/16/17/18/20 | 3 | 0.030636 | PASS |
| AT-050 | BookingControllerTest / test_provider_cannot_skip_from_pending_to_completed | FR-10/11/12/13/16/17/18/20 | 4 | 0.025348 | PASS |
| AT-051 | BookingControllerTest / test_other_provider_cannot_update_booking | FR-10/11/12/13/16/17/18/20 | 2 | 0.030769 | PASS |
| AT-052 | BookingControllerTest / test_other_customer_cannot_view_booking_contact_details | FR-10/11/12/13/16/17/18/20 | 2 | 0.027173 | PASS |
| AT-053 | BookingControllerTest / test_customers_see_only_their_own_booking_history | FR-10/11/12/13/16/17/18/20 | 2 | 0.046761 | PASS |
| AT-054 | BookingControllerTest / test_customer_can_review_a_completed_booking_once | FR-10/11/12/13/16/17/18/20 | 5 | 0.033781 | PASS |
| AT-055 | BookingControllerTest / test_unfinished_booking_cannot_be_reviewed | FR-10/11/12/13/16/17/18/20 | 2 | 0.026448 | PASS |
| AT-056 | BookingControllerTest / test_wrong_customer_cannot_review_completed_booking | FR-10/11/12/13/16/17/18/20 | 2 | 0.025622 | PASS |
| AT-057 | BookingControllerTest / test_invalid_review_rating_is_rejected | FR-10/11/12/13/16/17/18/20 | 3 | 0.024775 | PASS |
| AT-058 | CancellationTest / test_customer_cancellation_releases_slot_and_records_history | FR-19 / CR-01 | 4 | 0.028052 | PASS |
| AT-059 | CancellationTest / test_exact_two_hour_cutoff_allows_cancellation | FR-19 / CR-01 | 2 | 0.026750 | PASS |
| AT-060 | CancellationTest / test_late_cancellation_is_rejected_without_releasing_slot | FR-19 / CR-01 | 4 | 0.026659 | PASS |
| AT-061 | CancellationTest / test_other_customer_cannot_cancel_booking | FR-19 / CR-01 | 3 | 0.027117 | PASS |
| AT-062 | CancellationTest / test_ineligible_states_cannot_be_cancelled with data set #0 | FR-19 / CR-01 | 3 | 0.025577 | PASS |
| AT-063 | CancellationTest / test_ineligible_states_cannot_be_cancelled with data set #1 | FR-19 / CR-01 | 3 | 0.026553 | PASS |
| AT-064 | CancellationTest / test_ineligible_states_cannot_be_cancelled with data set #2 | FR-19 / CR-01 | 3 | 0.025324 | PASS |
| AT-065 | CancellationTest / test_ineligible_states_cannot_be_cancelled with data set #3 | FR-19 / CR-01 | 3 | 0.027077 | PASS |
| AT-066 | CancellationTest / test_cancelled_slot_can_be_booked_again | FR-19 / CR-01 | 3 | 0.030987 | PASS |
| AT-067 | CatalogControllerTest / test_public_catalog_shows_active_services_and_categories | FR-07/08/09/16 | 2 | 0.052482 | PASS |
| AT-068 | CatalogControllerTest / test_search_combines_text_category_and_city | FR-07/08/09/16 | 2 | 0.072930 | PASS |
| AT-069 | CatalogControllerTest / test_inactive_and_suspended_provider_services_are_hidden | FR-07/08/09/16 | 2 | 0.036340 | PASS |
| AT-070 | CatalogControllerTest / test_public_can_view_service_and_provider_details | FR-07/08/09/16 | 3 | 0.044922 | PASS |
| AT-071 | CatalogControllerTest / test_hidden_service_direct_link_is_not_found | FR-07/08/09/16 | 1 | 0.033858 | PASS |
| AT-072 | CatalogControllerTest / test_empty_search_has_a_recovery_action | FR-07/08/09/16 | 2 | 0.039307 | PASS |
| AT-073 | CatalogControllerTest / test_sort_parameter_cannot_inject_sql | FR-07/08/09/16 | 3 | 0.022070 | PASS |
| AT-074 | CatalogControllerTest / test_price_sort_orders_matching_services | FR-07/08/09/16 | 1 | 0.051271 | PASS |
| AT-075 | CatalogControllerTest / test_html_in_provider_and_service_text_is_escaped | FR-07/08/09/16 | 8 | 0.066083 | PASS |
| AT-076 | ExampleTest / test_the_application_returns_a_successful_response | Scaffold smoke check | 1 | 0.052600 | PASS |
| AT-077 | HomeLinkTest / test_role_dashboard_displays_relevant_workspace with data set #0 | Integration / NFR-04 | 1 | 0.071649 | PASS |
| AT-078 | HomeLinkTest / test_role_dashboard_displays_relevant_workspace with data set #1 | Integration / NFR-04 | 1 | 0.108962 | PASS |
| AT-079 | HomeLinkTest / test_admin_dashboard_redirects_to_administration | Integration / NFR-04 | 2 | 0.075633 | PASS |
| AT-080 | HomeLinkTest / test_provider_can_finish_an_accepted_job | Integration / NFR-04 | 4 | 0.057342 | PASS |
| AT-081 | HomeLinkTest / test_guest_profile_redirects_to_login | Integration / NFR-04 | 2 | 0.034236 | PASS |
| AT-082 | HomeLinkTest / test_security_headers_are_sent | Integration / NFR-04 | 4 | 0.037819 | PASS |
| AT-083 | ProviderControllerTest / test_provider_can_create_an_owned_service | FR-05/06/17 | 3 | 0.073247 | PASS |
| AT-084 | ProviderControllerTest / test_provider_can_edit_and_pause_their_service | FR-05/06/17 | 3 | 0.083644 | PASS |
| AT-085 | ProviderControllerTest / test_other_provider_cannot_modify_or_delete_service | FR-05/06/17 | 3 | 0.067711 | PASS |
| AT-086 | ProviderControllerTest / test_removing_service_preserves_booking_history | FR-05/06/17 | 5 | 0.118178 | PASS |
| AT-087 | ProviderControllerTest / test_customer_cannot_create_service | FR-05/06/17 | 2 | 0.048320 | PASS |
| AT-088 | ProviderControllerTest / test_invalid_price_and_description_are_rejected | FR-05/06/17 | 4 | 0.033764 | PASS |
