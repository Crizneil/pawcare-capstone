# 📊 Data Flow Diagram (Gane-Sarson Notation)

This document illustrates how data moves through the PawCare Veterinary Management System.

## Level 0: Context Diagram

The Context Diagram shows the system as a single process and its interaction with external entities.

```mermaid
graph LR
    %% External Entities (Squares)
    Owner[Pet Owner]
    Staff[Clinic Staff]
    Admin[Admin]
    Semaphore[Semaphore SMS API]
    Gmail[Gmail SMTP API]
    Public[Public User]

    %% Main Process (Rounded Rectangle with ID at top)
    System(("0<hr/>PawCare Management System"))

    %% Flows with clear labels
    Owner -->|Registration Data| System
    System -->|Digital ID / Notifications| Owner

    Staff -->|Medical Records| System
    System -->|Schedules| Staff

    Admin -->|User Management| System
    System -->|System Logs| Admin

    System -->|SMS Content| Semaphore
    System -->|Contact Info| Gmail
    
    Public -->|QR Scan| System
    System -->|Pet Profile| Public

    %% Styling
    style System fill:#fff,stroke:#333,stroke-width:2px
    style Owner fill:#fff,stroke:#333
    style Staff fill:#fff,stroke:#333
    style Admin fill:#fff,stroke:#333
    style Public fill:#fff,stroke:#333
    style Semaphore fill:#fafafa,stroke:#333,stroke-dasharray: 5 5
    style Gmail fill:#fafafa,stroke:#333,stroke-dasharray: 5 5
```

---

## Level 1: Data Flow Diagram

The Level 1 DFD decomposes the system into major functional processes and data stores.

```mermaid
graph TD
    %% External Entities
    E1[Pet Owner]
    E2[Clinic Staff]
    E3[Admin]
    E4[External APIs]

    %% Processes (Rounded with ID at top)
    P1(("1.0<hr/>Authentication & Profile"))
    P2(("2.0<hr/>Appointment Scheduling"))
    P3(("3.0<hr/>Health Record Mgmt"))
    P4(("4.0<hr/>Notification Engine"))

    %% Data Stores (Open Rectangle)
    D1[["D1: Users"]]
    D2[["D2: Pets"]]
    D3[["D3: Appointments"]]
    D4[["D4: Medical Records"]]

    %% Flows
    E1 -->|Login Credentials| P1
    P1 -->|User Details| D1
    D1 -->|Profile History| P1
    
    E1 -->|Book Request| P2
    P2 -->|Save Appointment| D3
    E2 -->|Approve/Reject| P2
    D3 -->|Update Status| P2

    E2 -->|Update History| P3
    P3 -->|Save Record| D4
    D4 -->|Fetch History| P3
    P3 -->|QR Digital ID| E1

    P2 -->|Event Trigger| P4
    P4 -->|Format Msg| E4

    %% Styling for Gane-Sarson
    classDef process fill:#fff,stroke:#333,stroke-width:2px;
    classDef datastore fill:#fff,stroke:#333,stroke-width:2px;
    classDef entity fill:#fff,stroke:#333,stroke-width:1px;
    
    class P1,P2,P3,P4 process;
    class D1,D2,D3,D4 datastore;
    class E1,E2,E3,E4 entity;
```

---

### Key Components Definition (Gane-Sarson)

1.  **External Entities**: Represented as squares, these are the sources/destinations of data (Owner, Staff, Admin, and external APIs).
2.  **Processes**: Represented as rounded rectangles, these transform inputs into outputs (Authentication, Scheduling, Medical Tracking).
3.  **Data Stores**: Represented as open-ended rectangles, these hold the system's persistent information (Users, Pets, Appointments, Vaccines).
4.  **Data Flows**: Arrows showing the direction of information movement.
