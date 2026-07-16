<?php

namespace LeadCaptureSync\API;

use LeadCaptureSync\Repository\LeadRepository;
use LeadCaptureSync\Security\RequestValidator;
use LeadCaptureSync\Models\LeadStatus;
use WP_REST_Request;
use WP_REST_Response;

defined( 'ABSPATH' ) || exit;

class CallbackController {

    public function __construct(
        private LeadRepository $repository,
        private RequestValidator $validator
    ) {
    }


    public function registerRoutes(): void {

        register_rest_route(
            'lead-sync/v1',
            '/callback',
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'handle' ],
                'permission_callback' => [ $this, 'authorize' ],
            ]
        );
    }


    public function authorize(
        WP_REST_Request $request
    ): bool {

        return $this->validator->validate(
            $request
        );
    }


    public function handle(
        WP_REST_Request $request
    ): WP_REST_Response {

        $leadId = (int) $request->get_param(
            'lead_id'
        );

        $status = sanitize_text_field(
            $request->get_param(
                'status'
            )
        );


        if ( ! $leadId || ! $status ) {

            return new WP_REST_Response(
                [
                    'success' => false,
                    'message' => 'Missing data',
                ],
                400
            );
        }


        if (
            ! in_array(
                $status,
                [
                    LeadStatus::PROCESSED,
                    LeadStatus::FAILED,
                ],
                true
            )
        ) {

            return new WP_REST_Response(
                [
                    'success' => false,
                    'message' => 'Invalid status',
                ],
                400
            );
        }


        $updated = $this->repository->updateStatus(
            $leadId,
            $status
        );


        return new WP_REST_Response(
            [
                'success' => $updated,
            ],
            200
        );
    }
}