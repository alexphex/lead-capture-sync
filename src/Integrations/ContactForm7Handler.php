<?php

namespace LeadCaptureSync\Integrations;

use LeadCaptureSync\Services\LeadService;

defined( 'ABSPATH' ) || exit;

class ContactForm7Handler {

    public function __construct(
        private LeadService $leadService,
        private ContactForm7Provider $provider
    ) {
    }

    public function register(): void {

        add_action(
            'wpcf7_mail_sent',
            [ $this, 'handle' ]
        );
    }

    public function handle(): void {

        $lead = $this->provider->getLead();

        if ( ! $lead ) {
            return;
        }

        $this->leadService->createLead(
            $lead
        );
    }
}