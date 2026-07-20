# WORKFLOW.md

# Lead Capture Sync — Full Plugin Workflow

```text
                                    WORDPRESS
                                        │
                                        │
                                        ▼
                           lead-capture-sync.php
                                        │
                           require vendor/autoload.php
                                        │
                                        ▼
                                 new Plugin()
                                        │
                                        ▼
                              Plugin::__construct()

        ┌──────────────────────────────────────────────────────────────┐
        │ Creates all application objects                              │
        │                                                              │
        │ LeadRepository                                               │
        │ Logger                                                       │
        │ WebhookService                                               │
        │ LeadService                                                  │
        │ RequestValidator                                             │
        │ LeadController                                               │
        │ CallbackController                                           │
        │ ContactForm7Provider                                         │
        │ ContactForm7Handler                                          │
        │ SettingsPage                                                 │
        │ LeadPostType                                                 │
        └──────────────────────────────────────────────────────────────┘
                                        │
                                        ▼
                                  Plugin::init()
                                        │
      ┌─────────────────────────────────┼─────────────────────────────────┐
      │                                 │                                 │
      ▼                                 ▼                                 ▼
Register CPT                    Register REST API               Register CF7 Hook
LeadPostType                    LeadController                  ContactForm7Handler
                                 CallbackController

================================================================================
                     SCENARIO #1 (Main Flow) Contact Form 7
================================================================================

User
      │
      ▼
Fills in the form
      │
      ▼
Clicks Submit
      │
      ▼
Contact Form 7
      │
      ▼
Hook: wpcf7_mail_sent
      │
      ▼
ContactForm7Handler::handle()
      │
      ▼
ContactForm7Provider::getLead()
      │
      │  Reads:
      │  your-name
      │  your-email
      │  your-phone
      │
      ▼
new Lead(name, email, phone)
      │
      ▼
LeadService::createLead()
      │
      ├─────────────────────────────────────────────┐
      │                                             │
      ▼                                             │
Repository::findByEmail()                           │
      │                                             │
      ▼                                             │
WP_Query                                            │
      │                                             │
      ├──────────── Lead exists? ───────────────┐   │
      │                                         │   │
      │YES                                      │NO
      ▼                                         ▼
Return existing ID                      Repository::create()
                                                │
                                                ▼
                                          wp_insert_post()
                                                │
                                                ▼
                                        update_post_meta()
                                                │
                                                ▼
                                       status = pending
                                                │
                                                ▼
                                   updateStatus(processing)
                                                │
                                                ▼
                                  WebhookService::send()
                                                │
                                                ▼
                                          wp_remote_post()
                                                │
                                                ▼
                                          n8n / CRM
                                                │
                              ┌─────────────────┴─────────────────┐
                              │                                   │
                              ▼                                   ▼
                           SUCCESS                            FAILED
                              │                                   │
                              ▼                                   ▼
updateStatus(processed)                  updateStatus(failed)

================================================================================
                     SCENARIO #2 (REST API)
================================================================================

POST /wp-json/lead-sync/v1/leads
              │
              ▼
LeadController::store()
              │
              ▼
Sanitize input
              │
              ▼
Validate input
              │
              ▼
new Lead()
              │
              ▼
LeadService::createLead()

              ↓↓↓

From this point, the flow is identical
to the Contact Form 7 workflow.

================================================================================
                     SCENARIO #3 (Callback from n8n / CRM)
================================================================================

n8n / CRM
 │
 ▼
POST /wp-json/lead-sync/v1/callback
 │
 ▼
CallbackController
 │
 ▼
RequestValidator
 │
 ├──────── Token valid? ─────────────┐
 │                                   │
 │YES                                │NO
 ▼                                   ▼
Repository::updateStatus()        HTTP 401
 │
 ▼
status = processed
or
status = failed

================================================================================
                     CLASS RESPONSIBILITIES
================================================================================

LeadController
    │
    └── Receives REST API requests.

ContactForm7Handler
    │
    └── Listens for Contact Form 7 events.

ContactForm7Provider
    │
    └── Converts form data into a Lead object.

Lead
    │
    └── Business model used across the application.

LeadService
    │
    └── Contains all business logic.

LeadRepository
    │
    └── Handles all WordPress database operations.

WebhookService
    │
    └── Sends lead data to external services.

CallbackController
    │
    └── Receives callback requests from n8n / CRM.

RequestValidator
    │
    └── Validates the secret token for protected endpoints.
```
