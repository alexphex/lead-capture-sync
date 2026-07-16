# Lead Capture Sync

WordPress plugin for collecting leads from forms, saving them in WordPress, and sending them to external services through webhooks.

**Main idea:**
Form submission → Create Lead → Save in WordPress → Send data to external service → Receive callback and update status.

---

## Project Workflow

```
Contact Form 7
      ↓
ContactForm7Handler
      ↓
ContactForm7Provider
      ↓
   Lead Object
      ↓
  LeadService
      ↓
 LeadRepository
      ↓
 WebhookService
      ↓
n8n / CRM / External Service
      ↓
  Callback API
      ↓
Update Lead Status
```

---

## Features

- Custom Post Type for leads
- REST API endpoint for creating leads
- Contact Form 7 integration
- Duplicate checking by email
- Lead status management
- Webhook communication
- Callback endpoint for external services
- Simple token validation
- Composer autoloading
- Basic logging

---

## Plugin Structure

```
src
├── API
│   ├── LeadController.php
│   └── CallbackController.php
│
├── Integrations
│   ├── ContactForm7Handler.php
│   ├── ContactForm7Provider.php
│   └── FormProviderInterface.php
│
├── Models
│   ├── Lead.php
│   └── LeadStatus.php
│
├── Repository
│   └── LeadRepository.php
│
├── Services
│   ├── LeadService.php
│   ├── WebhookService.php
│   └── Logger.php
│
├── Security
│   └── RequestValidator.php
│
├── Admin
│   └── SettingsPage.php
│
└── Plugin.php
```

---

## How It Works

### Creating a Lead

When a user submits a form:

1. Contact Form 7 triggers an action.
2. The provider converts form data into a `Lead` object.
3. `LeadService` handles the business logic.
4. `LeadRepository` saves the lead.
5. `WebhookService` sends data to the external service.

```
User submits form
      ↓
Lead object created
      ↓
Lead saved as WordPress post
      ↓
Webhook sent
      ↓
Status updated
```

---

## Lead Statuses

The plugin uses a simple lead lifecycle:

```
pending
   ↓
processing
   ↓
processed  or  failed
```

---

## REST API

### Create Lead

**Endpoint:**
```
POST /wp-json/lead-sync/v1/leads
```

**Request body:**
```json
{
    "name": "Tom",
    "email": "tom@test.com",
    "phone": "+123456789"
}
```

**Response:**
```json
{
    "success": true,
    "id": 1583
}
```

### Callback Endpoint

External services can update lead status.

**Endpoint:**
```
POST /wp-json/lead-sync/v1/callback
```

**Header:**
```
X-Lead-Sync-Token: abc123
```

**Request body:**
```json
{
    "lead_id": 1583,
    "status": "processed"
}
```

**Response:**
```json
{
    "success": true
}
```

---

## Contact Form 7 Integration

The plugin integrates with Contact Form 7 using the `wpcf7_mail_sent` hook.

**Currently supported fields:**
- `your-name`
- `your-email`
- `tel-301`

The integration uses the **Provider pattern**. The idea: different form plugins can have different data formats, but they all return the same `Lead` object.

```
Contact Form 7
      ↓
ContactForm7Provider
      ↓
     Lead
```

This makes it possible to later add, without changing `LeadService`:

- Gravity Forms Provider
- Elementor Forms Provider
- Custom Form Provider

---

## Installation

**Requirements:**
- WordPress
- PHP 8+
- Composer

**Steps:**

```bash
# Install dependencies
composer install

# Generate autoload
composer dump-autoload
```

Then activate the plugin from the WordPress admin panel.

---

## Development Notes

The plugin uses a simple layered structure:

### Controller
Responsible for receiving requests.

```
REST request
      ↓
  Controller
```

### Service
Contains business logic.

**Example — `LeadService`:**
- check duplicates
- create lead
- update status
- send webhook

### Repository
Responsible for working with WordPress data.

**Example — `LeadRepository`:**
- create lead
- find by email
- update status

### Integration Layer
Connects external plugins with the core logic.

```
Contact Form 7
      ↓
   Provider
      ↓
 LeadService
```

---

## Possible Improvements

- [ ] Add support for Gravity Forms
- [ ] Add support for Elementor Forms
- [ ] Add retry logic for failed webhooks
- [ ] Add admin UI for viewing lead history
- [ ] Add unit tests

---

## Author

**alex**
