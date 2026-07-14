<?php

namespace LeadCaptureSync;

use LeadCaptureSync\PostTypes\LeadPostType;
use LeadCaptureSync\API\LeadController;
use LeadCaptureSync\Repository\LeadRepository;
use LeadCaptureSync\Services\WebhookService;
use LeadCaptureSync\Admin\SettingsPage;
use LeadCaptureSync\Services\Logger;

defined( 'ABSPATH' ) || exit;

class Plugin {

    private LeadPostType $leadPostType;
    private LeadController $leadController;
    private SettingsPage $settingsPage;

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

        $this->leadController = new LeadController(
            $repository,
            $webhookService
        );
    }

    public function init(): void {

        add_action(
            'init',
            [ $this->leadPostType, 'register' ]
        );

        add_action(
            'rest_api_init',
            [ $this->leadController, 'registerRoutes' ]
        );

        add_action(
            'admin_menu',
            [ $this->settingsPage, 'register' ]
        );
    }
}