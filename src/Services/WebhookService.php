<?php

namespace LeadCaptureSync\Services;

use LeadCaptureSync\Models\Lead;
use LeadCaptureSync\Services\Logger;

defined( 'ABSPATH' ) || exit;

class WebhookService {

    public function __construct(
        private string $webhookUrl, 
        private string $secretToken, 
        private Logger $logger
    ) {
    }

    public function send( Lead $lead ): bool {

        $response = wp_remote_post(
            $this->webhookUrl,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Lead-Sync-Token' => $this->secretToken,
                ],
                'body' => wp_json_encode(
                    [
                        'name'  => $lead->getName(),
                        'email' => $lead->getEmail(),
                        'phone' => $lead->getPhone(),
                    ]
                ),
                'timeout' => 15,
            ]
        );

        return ! is_wp_error( $response );
    }
}