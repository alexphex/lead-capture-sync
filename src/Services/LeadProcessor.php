<?php

namespace LeadCaptureSync\Services;

use LeadCaptureSync\Models\Lead;
use LeadCaptureSync\Repository\LeadRepository;

defined( 'ABSPATH' ) || exit;

class LeadProcessor {

    public function __construct(
        private LeadRepository $repository,
        private WebhookService $webhookService
    ) {
    }


    public function process( Lead $lead ): int {

        $existingLead = $this->repository->findByEmail(
            $lead->getEmail()
        );

        if ( $existingLead !== null ) {
            return $existingLead;
        }


        $leadId = $this->repository->create(
            $lead
        );


        $this->webhookService->send(
            $lead
        );


        return $leadId;
    }
}