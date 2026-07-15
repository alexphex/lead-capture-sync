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

    public function send( Lead $lead ): true|\WP_Error {

        $response = wp_remote_post(
            $this->webhookUrl,
            [
                'headers' => [
                    'Content-Type'      => 'application/json',
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


        if ( is_wp_error( $response ) ) {

            return $response;
        }


        $statusCode = wp_remote_retrieve_response_code(
            $response
        );


        if ( $statusCode < 200 || $statusCode >= 300 ) {

            return new \WP_Error(
                'webhook_failed',
                'Webhook returned HTTP status: ' . $statusCode
            );
        }


        return true;
    }

}