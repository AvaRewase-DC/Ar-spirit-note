# Mass Pages — API Reference

Base URL: `http://41.130.162.206:3000/api/`

---

## 1. Mass Settings — Firebase Realtime Database

The mobile app reads settings from **Firebase Realtime Database**, not the REST API.

### Direct URL

```
GET https://kenesty.firebaseio.com/mass-settings.json
```

### Node path
```
mass-settings/
```

### Fields returned
| Field | Type | Description |
|-------|------|-------------|
| `massEnabled` | boolean | Whether booking is open |
| `massMessageTitle` | string | Title shown when booking is disabled |
| `massMessageBody` | string | Body text shown when booking is disabled |
| `massPolicy` | string | Policy/instructions shown on the confirm page |
| `radioEnabled` | boolean | Whether the radio stream is enabled |
| `radioLink` | string | Radio stream URL |

---

### Firebase Credentials

#### Production
| Key | Value |
|-----|-------|
| `databaseURL` | `https://kenesty.firebaseio.com` |
| `projectId` | `kenesty` |
| `apiKey` | `AIzaSyBkFLPuAO3PwIBlSXEkAWNygo1-o4KVdoU` |
| `authDomain` | `kenesty.firebaseapp.com` |
| `storageBucket` | `kenesty.appspot.com` |
| `messagingSenderId` | `12594696976` |
| `appId` | `1:12594696976:web:ef72712f12e0f3938a0b95` |

#### Development / Test
| Key | Value |
|-----|-------|
| `databaseURL` | `https://kenesty-test.firebaseio.com` |
| `projectId` | `kenesty-test` |
| `apiKey` | `AIzaSyDULHJ_LKEtagNKu-iBkliRCzf6Lgvkchc` |
| `authDomain` | `kenesty-test.firebaseapp.com` |
| `storageBucket` | `kenesty-test.appspot.com` |
| `messagingSenderId` | `58434892515` |
| `appId` | `1:58434892515:web:6cb6b92f51c7742b2b4bd6` |
| `measurementId` | `G-860QP978GT` |

---

## 2. Appointments

| # | Method | Endpoint | Description | Used In |
|---|--------|----------|-------------|---------|
| 2 | `GET` | `mass-appointments/get-active-appointments` | Get the list of upcoming/active mass appointments (returns id, title, appointmentDate, place, availableSeats) | `mass-request` page (commented out, lazily called) · `select-mass-modal` page on load |

---

## 3. Requests

| # | Method | Endpoint | Description | Used In |
|---|--------|----------|-------------|---------|
| 3 | `GET` | `requests/search-by-membership/{membershipNumber}` | Search all booking requests for a membership number. `membershipNumber` format: `E1C1F{familyNumber}NR{familyMemberCode}` | `mass-list` page on search submit |
| 4 | `POST` | `requests` | Submit a new booking request | `mass-request` page on form submit |
| 5 | `POST` | `requests/cancel-request-by-user/{requestId}/{nationalId}` | Cancel an existing booking request | `mass-details` page |

---

## Request Body — POST `requests`

```json
{
  "massAppointmentId": "string",
  "membershipNumber":  "E1C1F{familyNumber}NR{familyMemberCode}",
  "memberName":        "string",
  "birthDate":         "YYYY-MM-DD",
  "gender":            "0 | 1",
  "seatNumber":        "string",
  "nationalId":        "string (14 digits)",
  "mobile":            "string (11 digits, starts with 01)",
  "familyNumber":      "string (max 5 digits)",
  "familyMemberCode":  "string (max 2 digits)"
}
```

> `birthDate` and `gender` are auto-derived from `nationalId`:
> - Digits `[1-2]` → century prefix (`2`→`19`, `3`→`20`), `[3-6]` → year/month/day
> - Digit `[13]` (index 12) % 2 → `0` = male, `1` = female

---

## Response Codes — POST `requests`

| Code | Meaning |
|------|---------|
| `1` | Success — request submitted |
| `2` | Invalid membership number |
| `3` | No available seats, choose another time |
| `4` | Member exceeded max allowed requests |
| other | Generic server error |

---

## Response Code — POST `requests/cancel-request-by-user/{requestId}/{nationalId}`

| Code | Meaning |
|------|---------|
| `1` | Cancellation successful |
| other | Cancellation failed |

---

## Laravel Implementation Mapping

| Mobile API Call | Laravel Route | Controller Method |
|-----------------|---------------|-------------------|
| Firebase `mass-settings/` | `GET /events/policy` (+ controller helper) | `MassController::massSettings()` → `MassRepository::getMassSettings()` |
| `GET mass-appointments/get-active-appointments` | `GET /events/request` | `MassController::createRequest()` |
| `GET requests/search-by-membership/{id}` | `POST /events/search` | `MassController::search()` |
| `POST requests` | `POST /events/request` | `MassController::storeRequest()` |
| `POST requests/cancel-request-by-user/{id}/{nid}` | `POST /events/cancel` | `MassController::cancelRequest()` |
