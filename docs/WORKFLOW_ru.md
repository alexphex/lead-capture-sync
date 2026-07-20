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
        │ Создаются ВСЕ объекты                                        │
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
register CPT                    register REST                    register CF7 Hook
LeadPostType                    LeadController                   ContactForm7Handler
                                 CallbackController

================================================================================
                     СЦЕНАРИЙ №1 (основной) Contact Form 7
================================================================================

Пользователь
      │
      ▼
Заполняет форму
      │
      ▼
Нажимает Submit
      │
      ▼
Contact Form 7
      │
      ▼
hook: wpcf7_mail_sent
      │
      ▼
ContactForm7Handler::handle()
      │
      ▼
ContactForm7Provider::getLead()
      │
      │  читает:
      │  your-name
      │  your-email
      │  your-phone
      │
      ▼
new Lead(name,email,phone)
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
      ├──────────── Lead найден? ───────────────┐   │
      │                                         │   │
      │ДА                                       │НЕТ
      ▼                                         ▼
вернуть ID                               Repository::create()
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
                                              n8n
                                                │
                              ┌─────────────────┴─────────────────┐
                              │                                   │
                              ▼                                   ▼
                           SUCCESS                            FAILED
                              │                                   │
                              ▼                                   ▼
updateStatus(processed)                  updateStatus(failed)

================================================================================
                     СЦЕНАРИЙ №2 (REST API)
================================================================================

POST /wp-json/lead-sync/v1/leads
              │
              ▼
LeadController::store()
              │
              ▼
sanitize()
              │
              ▼
validate()
              │
              ▼
new Lead()
              │
              ▼
LeadService::createLead()

              ↓↓↓

Дальше поток полностью совпадает
с Contact Form 7

================================================================================
                     СЦЕНАРИЙ №3 (Callback от n8n)
================================================================================

n8n
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
 ├──────── Token OK? ─────────────┐
 │                                │
 │ДА                              │НЕТ
 ▼                                ▼
Repository::updateStatus()     HTTP 401
 │
 ▼
status = processed
или
status = failed

================================================================================
                  ГЛАВНЫЕ ОТВЕТСТВЕННОСТИ
================================================================================

LeadController
    │
    └── принимает REST запрос

ContactForm7Handler
    │
    └── реагирует на событие CF7

ContactForm7Provider
    │
    └── превращает данные формы в Lead

Lead
    │
    └── единая модель данных

LeadService
    │
    └── вся бизнес-логика

LeadRepository
    │
    └── вся работа с WordPress

WebhookService
    │
    └── отправка данных наружу

CallbackController
    │
    └── принимает ответ от n8n

RequestValidator
    │
    └── проверяет секретный токен