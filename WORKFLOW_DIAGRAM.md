# Assessment Workflow Diagram

## New Assessment Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                        LANDING PAGE                              │
│                     (index.html)                                 │
│                                                                  │
│  [New Assessment] button clicked                                │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│                    STEP 1: Staff Information                     │
│                     (new-assessment.html)                        │
│                                                                  │
│  ┌──────────────────────────────────────────────────┐          │
│  │ Assessment Password: *                            │          │
│  │ [______________] (must be 079777)                │          │
│  └──────────────────────────────────────────────────┘          │
│                                                                  │
│  ┌──────────────────────────────────────────────────┐          │
│  │ Outreach Staff Member: *                          │          │
│  │ [Select staff member... ▼]                       │          │
│  └──────────────────────────────────────────────────┘          │
│                                                                  │
│  [Next: Client Information →]                                   │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│              STEP 2: Previously Assessed Clients                 │
│                                                                  │
│  Previously Assessed Clients                                     │
│  ┌────────────────────────────────────────────────┐            │
│  │ • John Doe (DOB: 1990-05-15) - 2 assessments  │            │
│  │ • Jane Smith - 1 assessment                    │            │
│  │ • Bob Johnson (DOB: 1985-03-22) - 3 assess... │            │
│  └────────────────────────────────────────────────┘            │
│                                                                  │
│  Is the client on this list?                                    │
│                                                                  │
│  [YES - Client is on the list]  [NO - Client is NOT on list]  │
│                                                                  │
└─────────┬──────────────────────────────────────┬───────────────┘
          │                                      │
          │ YES                                  │ NO
          ▼                                      ▼
┌──────────────────────────────────┐  ┌─────────────────────────┐
│  STEP 2a: Update or Extensive    │  │  Create new placeholder │
│                                   │  │  client record          │
│  Are you wanting to update or do │  │                         │
│  a more extensive PIT assessment?│  └────────┬────────────────┘
│                                   │           │
│  [YES - Update/extend assessment] │           │
│  [NO - Return to home]            │           │
│                                   │           │
└─────┬─────────────────┬───────────┘           │
      │                 │                       │
      │ YES             │ NO                    │
      ▼                 ▼                       │
┌─────────────┐  ┌──────────────┐             │
│  STEP 2b:   │  │  Return to   │             │
│  Select     │  │  Landing     │             │
│  Client     │  │  Page        │             │
│             │  └──────────────┘             │
│  [Client ▼] │                               │
│             │                               │
│  [Continue] │                               │
└──────┬──────┘                               │
       │                                      │
       │                                      │
       └──────────────┬───────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────────┐
│                    STEP 3: Consent & Data Sharing                │
│                                                                  │
│  ○ Full Consent - Share full name and information               │
│  ○ Partial Consent (Anonymous) - Only demographics              │
│                                                                  │
│  [Continue to Assessment Selection →]                           │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────────┐
│              STEP 4: Choose Assessment Type                      │
│                                                                  │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐ ┌──────────┐│
│  │   QUICK     │ │   SHORT     │ │   MEDIUM    │ │   HARD   ││
│  │   2-3 min   │ │  6-10 min   │ │  12-20 min  │ │ 25-40 min││
│  │  7 questions│ │ 25 questions│ │ 50-70 quest.│ │100-120 q.││
│  └─────────────┘ └─────────────┘ └─────────────┘ └──────────┘│
│                                                                  │
│  [Begin Assessment →]                                            │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            ▼
                   ┌────────────────┐
                   │   Assessment   │
                   │   Form         │
                   └────────────────┘
```

## Admin Portal - Client Status Management

```
┌─────────────────────────────────────────────────────────────────┐
│                     ADMIN DASHBOARD                              │
│                      (admin.html)                                │
│                                                                  │
│  Client Status Management                                        │
│  ────────────────────────────────────────                       │
│                                                                  │
│  Select Client:                                                  │
│  ┌──────────────────────────────────────────────┐              │
│  │ [Select a client... ▼]                       │              │
│  └──────────────────────────────────────────────┘              │
│                                                                  │
│  (When client selected)                                         │
│  ────────────────────────                                       │
│  Current Living Situation:                                       │
│  ┌──────────────────────────────────────────────┐              │
│  │ [Unhoused ▼]                                 │              │
│  │  - Transition House                          │              │
│  │  - Couch surfing                             │              │
│  │  - Just got a place                          │              │
│  │  - Unhoused                                  │              │
│  │  - Renting a room                            │              │
│  │  - Living in Car                             │              │
│  │  - Prefer not to say                         │              │
│  └──────────────────────────────────────────────┘              │
│                                                                  │
│  [Apply Changes]                                                 │
│                                                                  │
│  ┌────────────────────────────────────────────────┐            │
│  │ ✓ Status updated successfully                  │            │
│  │ Updated at: 2025-10-24 5:35:22 PM             │            │
│  └────────────────────────────────────────────────┘            │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

## Landing Page Count Logic

```
┌─────────────────────────────────────────────────────────────────┐
│                  PEOPLE UNHOUSED WIDGET                          │
│                                                                  │
│  Counts ONLY assessments where currently_staying is:            │
│                                                                  │
│  ✓ "Prefer not to say"                                          │
│  ✓ "Living in Car"                                              │
│  ✓ "Unhoused"                                                   │
│  ✓ "Couch surfing"                                              │
│                                                                  │
│  Excluded:                                                       │
│  ✗ "Transition House"                                           │
│  ✗ "Just got a place"                                           │
│  ✗ "Renting a room"                                             │
│                                                                  │
│  SQL Query:                                                      │
│  SELECT COUNT(*) FROM pit_assessments                           │
│  WHERE is_complete = TRUE                                       │
│  AND currently_staying IN (filter list)                         │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```
