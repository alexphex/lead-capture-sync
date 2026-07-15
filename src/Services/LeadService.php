<?php

namespace LeadCaptureSync\Services;

use LeadCaptureSync\Models\Lead;
use LeadCaptureSync\Models\LeadStatus;
use LeadCaptureSync\Repository\LeadRepository;
use LeadCaptureSync\Services\Logger;

defined( 'ABSPATH' ) || exit;

class LeadService {

    public function __construct(
        private LeadRepository $repository,
        private WebhookService $webhookService,
        private Logger $logger
    ) {
    }

    public function createLead( Lead $lead ): int {

        $existingLead = $this->repository->findByEmail(
            $lead->getEmail()
        );

        if ( $existingLead !== null ) {
            return $existingLead;
        }

        $id = $this->repository->create(
            $lead
        );

        $this->repository->updateStatus(
            $id,
            LeadStatus::PROCESSING
        );

        $result = $this->webhookService->send(
            $lead
        );

        if ( $result === true ) {

            $this->repository->updateStatus(
                $id,
                LeadStatus::PROCESSED
            );

        } else {

            $errorMessage = $result->get_error_message();

            $this->repository->updateStatus(
                $id,
                LeadStatus::FAILED
            );

            $this->repository->updateError(
                $id,
                $errorMessage
            );

            $this->logger->error(
                $errorMessage
            );

        }

        return $id;
    }
}