# System design
Diagrams are editable Mermaid sources. GitHub and many Markdown viewers render them. The application screens themselves are the implemented UI prototype.

## D-01 Architecture
```mermaid
flowchart LR
  B["Browser: desktop / mobile"] --> W["Laravel web routes"]
  W --> M["Sessions + CSRF + ActiveAccount"]
  M --> R["Authentication + EnsureRole"]
  R --> C["Controllers: Auth / Catalog / Provider / Booking / Admin"]
  C --> P["Policies: Booking / ServiceListing"]
  C --> S["BookingService transactions"]
  C --> E["Eloquent models"]
  S --> E
  E --> DB[("SQLite persistent database")]
  C --> V["Blade pages + components"]
  V --> B
  A["Vite: CSS / Alpine / GSAP / local fonts"] --> B
  B -->|"read-only slot lookup"| W
```

One deployable Laravel application is appropriate for this mini project. Server-rendered pages work without a second frontend API architecture. The isolated slot endpoint returns only unavailable times, not private booking details.

## D-02 Use case diagram
```mermaid
flowchart LR
  Customer["Customer"] --- Register(["Register / log in"])
  Provider["Service provider"] --- Register
  Admin["Administrator"] --- Login(["Log in"])
  Visitor["Visitor"] --- Search(["Browse / search services"])
  Customer --- Search
  Customer --- Details(["View provider and service"])
  Customer --- Book(["Request booking"])
  Customer --- History(["View own history"])
  Customer --- Cancel(["Cancel eligible booking"])
  Customer --- Review(["Review completed booking"])
  Provider --- Listings(["Manage own listings"])
  Provider --- Respond(["Accept / reject / start / complete"])
  Provider --- History
  Provider --- Profile(["Update own profile"])
  Customer --- Profile
  Admin --- Manage(["Search / suspend / reactivate accounts"])
```

## D-03 Domain/class model
```mermaid
classDiagram
  class User {
    +name
    +email
    +role
    +is_active
    +listings()
  }
  class Category {
    +name
    +slug
    +icon
  }
  class ServiceListing {
    +title
    +description
    +price
    +city
    +is_active
    +deleted_at
    +available()
  }
  class Booking {
    +service_title
    +quoted_price
    +starts_at
    +status: BookingStatus
    +reference()
  }
  class BookingSlot {
    +provider_id
    +starts_at
    +booking_id
  }
  class BookingEvent {
    +actor_id
    +status
    +note
    +created_at
  }
  class Review {
    +rating
    +comment
  }
  class BookingService {
    +create()
    +transition()
    +cancel()
  }
  User "1" --> "*" ServiceListing : provider
  Category "1" --> "*" ServiceListing
  ServiceListing "1" --> "*" Booking
  User "1" --> "*" Booking : customer / provider
  Booking "1" --> "0..1" BookingSlot
  Booking "1" --> "*" BookingEvent
  Booking "1" --> "0..1" Review
  BookingService --> Booking
```

BookingSlot is a database reservation record accessed intentionally through the query builder; the remaining persisted domain entities are Eloquent models.

## D-04 Database / ER diagram
```mermaid
erDiagram
  users ||--o{ service_listings : provides
  categories ||--o{ service_listings : groups
  users ||--o{ bookings : requests
  users ||--o{ bookings : fulfills
  service_listings ||--o{ bookings : references
  bookings ||--o| booking_slots : reserves
  bookings ||--o{ booking_events : records
  users ||--o{ booking_events : acts
  bookings ||--o| reviews : receives
  users ||--o{ reviews : writes
  users {
    bigint id PK
    string email UK
    string name
    string password
    string role
    boolean is_active
    string phone
    string city
    text bio
  }
  categories {
    bigint id PK
    string slug UK
    string name
    string icon
    string color
  }
  service_listings {
    bigint id PK
    bigint provider_id FK
    bigint category_id FK
    string slug UK
    string title
    text description
    integer price
    string pricing_note
    string city
    boolean is_active
    timestamp deleted_at
  }
  bookings {
    bigint id PK
    bigint customer_id FK
    bigint provider_id FK
    bigint service_listing_id FK
    string service_title
    integer quoted_price
    datetime starts_at
    string address
    string phone
    text notes
    string status
  }
  booking_slots {
    bigint id PK
    bigint booking_id FK,UK
    bigint provider_id FK
    datetime starts_at
  }
  booking_events {
    bigint id PK
    bigint booking_id FK
    bigint actor_id FK
    string status
    string note
    timestamp created_at
  }
  reviews {
    bigint id PK
    bigint booking_id FK,UK
    bigint customer_id FK
    integer rating
    text comment
  }
```

Composite UNIQUE(provider_id, starts_at) on booking_slots is the conflict guard. Each booking has at most one reservation and one review. User references are retained; suspension replaces destructive deletion. Prices are whole LKR integers to avoid floating-point rounding. Framework tables also store sessions, password-reset tokens, cache and queue jobs. No queue delivery is required by the current workflows.

## D-05 Sequence: request a booking
```mermaid
sequenceDiagram
  actor Customer
  participant UI as Booking page
  participant Controller as BookingController
  participant Service as BookingService
  participant DB as Database
  Customer->>UI: Select date/time and enter details
  UI->>Controller: POST /services/{slug}/book + CSRF
  Controller->>Controller: Authenticate, authorize role, validate
  Controller->>Service: create(customer, listing, data)
  Service->>DB: Begin transaction; reload listing
  Service->>DB: Insert pending booking + quote snapshot
  Service->>DB: Insert unique provider/time slot
  alt Slot is already reserved
    DB-->>Service: Unique constraint failure
    Service->>DB: Rollback all writes
    Service-->>UI: Validation error; choose another time
  else Available
    Service->>DB: Insert first timeline event; commit
    Service-->>Controller: Booking
    Controller-->>UI: Redirect to booking detail
    UI-->>Customer: Pending status and reference
  end
```

## D-06 Sequence: provider responds
```mermaid
sequenceDiagram
  actor Provider
  participant UI as Booking detail
  participant Controller as BookingController
  participant Policy as BookingPolicy
  participant Service as BookingService
  participant DB as Database
  Provider->>UI: Accept, reject, start or complete
  UI->>UI: Accessible confirmation dialog
  UI->>Controller: PATCH status + CSRF
  Controller->>Service: transition(booking, provider, status)
  Service->>DB: Begin transaction; lock/reload booking
  Service->>Policy: Authorize assigned provider
  Service->>Service: Validate allowed transition
  Service->>DB: Update booking and append event
  opt Rejected
    Service->>DB: Remove reserved slot
  end
  Service->>DB: Commit
  Controller-->>UI: Success and updated timeline
```

## D-07 Activity: booking lifecycle and CR-01
```mermaid
flowchart TD
  Start([Customer requests service]) --> Validate{Valid and slot available?}
  Validate -->|No| Error[Show errors; no partial write]
  Error --> Start
  Validate -->|Yes| Pending[Pending]
  Pending --> Choice{Provider response}
  Choice -->|Accept| Accepted[Accepted]
  Choice -->|Reject| Rejected[Rejected; release slot]
  Accepted --> Progress[In progress]
  Progress --> Complete[Completed]
  Complete --> Review[Customer may review once]
  Pending --> Cancel{Owner cancels at least 2h ahead?}
  Accepted --> Cancel
  Cancel -->|Yes| Cancelled[Cancelled; release slot; keep history]
  Cancel -->|No| Retain[Reject action; retain current state]
  Rejected --> End([History retained])
  Cancelled --> End
  Review --> End
```

## D-08 Interface wireframes
### Discovery
```text
[Sidebar: Explore / Overview / Bookings / Profile] [Location / Account]
[Page title]
[Home-care banner with compact introduction]
[Search text                       | City | Find services]
[All] [Cleaning] [Plumbing] [Electrical] [Painting] [Gardening] [AC]
[Result heading                                     | Sort]
[Service card]              [Service card]          [Service card]
[Pagination]
```
### Service and booking
```text
[Back]
[Photo / category]                         [Starting quote]
[Title / city / review summary]            [Date]
[Service description / pricing note]       [Available two-hour slots]
[Provider introduction]                    [Address / contact / notes]
[Completed-booking reviews]                [Request booking]
```
### Workspace
```text
[Greeting                                  | Add/find service]
[Total] [Active] [Completed] [Quoted value]
[Recent bookings: title, person, date, status]
[Booking detail: plan + event timeline | participant + actions]
```
### Mobile
At 760 px and below, navigation becomes a drawer. Forms stack, category cards scroll horizontally, booking details stack and tables scroll inside their container. Under 440 px, service cards use one column. Motion never supplies essential information.

## Decisions and tradeoffs
- Fixed slots give predictable overlap prevention; custom schedules are future scope.
- Active pending requests reserve time immediately, preventing duplicate requests for the same provider.
- Soft deletes and snapshots preserve history; no hard user deletion UI.
- Independent policies protect object-level access even when a route role is correct.
- CSRF and session authentication remain within Laravel’s standard web stack.
- SQLite is suitable for demonstration, not a claim of enterprise write capacity.
- Reviews are verified against completed booking ownership, not against a real-world provider identity.
