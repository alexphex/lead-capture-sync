<?php

namespace LeadCaptureSync;

use LeadCaptureSync\PostTypes\LeadPostType;
use LeadCaptureSync\API\LeadController;
use LeadCaptureSync\Repository\LeadRepository;
use LeadCaptureSync\Services\WebhookService;
use LeadCaptureSync\Admin\SettingsPage;
use LeadCaptureSync\Services\Logger;
use LeadCaptureSync\Services\LeadService;
use LeadCaptureSync\Security\RequestValidator;
use LeadCaptureSync\Integrations\ContactForm7Handler;
use LeadCaptureSync\Integrations\ContactForm7Provider;
use LeadCaptureSync\API\CallbackController;

defined( 'ABSPATH' ) || exit;

class Plugin {

    private LeadPostType $leadPostType;
    private LeadController $leadController;
    private SettingsPage $settingsPage;
    private ContactForm7Handler $contactForm7Handler;
    private CallbackController $callbackController;

    public function __construct() {

        $this->leadPostType = new LeadPostType();

        $this->settingsPage = new SettingsPage();

        $repository = new LeadRepository();

        $webhookUrl = (string) get_option(
            'lead_capture_sync_webhook_url'
        );

        $secretToken = (string) get_option(
            'lead_capture_sync_secret'
        );

        $logger = new Logger();

        $webhookService = new WebhookService(
            $webhookUrl,
            $secretToken,
            $logger
        );

        $leadService = new LeadService(
            $repository,
            $webhookService,
            $logger
        );

        $requestValidator = new RequestValidator();

        $this->callbackController = new CallbackController(
            $repository,
            $requestValidator
        );


        $contactForm7Provider = new ContactForm7Provider();

        $this->contactForm7Handler = new ContactForm7Handler(
            $leadService,
            $contactForm7Provider
        );

        $this->leadController = new LeadController(
            $leadService,
            $requestValidator
        );

    }

    public function init(): void {

        add_action(
            'init',
            [ $this->leadPostType, 'register']
        );

        add_action(
            'rest_api_init',
            [ $this->leadController, 'registerRoutes' ]
        );


        add_action(
            'rest_api_init',
            [ $this->callbackController, 'registerRoutes' ]
        );


        add_action(
            'admin_menu',
            [ $this->settingsPage, 'register' ]
        );

        $this->contactForm7Handler->register();
    }
}