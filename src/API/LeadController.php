<?php

namespace LeadCaptureSync\API;

use LeadCaptureSync\Models\Lead;
use LeadCaptureSync\Repository\LeadRepository;
use LeadCaptureSync\Services\WebhookService;
use WP_REST_Request;
use WP_REST_Response;

defined( 'ABSPATH' ) || exit;

class LeadController {

    public function __construct(
        private LeadRepository $repository,
        private WebhookService $webhookService
    ) {
    }

    public function registerRoutes(): void {

        register_rest_route(
            'lead-sync/v1',
            '/leads',
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'store' ],
                'permission_callback' => '__return_true',
            ]
        );
    }

    public function store( WP_REST_Request $request ): WP_REST_Response {

        $name  = sanitize_text_field(
            $request->get_param( 'name' )
        );

        $email = sanitize_email(
            $request->get_param( 'email' )
        );

        $phone = sanitize_text_field(
            $request->get_param( 'phone' )
        );


        if ( empty( $name ) || empty( $email ) ) {

            return new WP_REST_Response(
                [
                    'success' => false,
                    'message' => 'Name and email are required',
                ],
                400
            );
        }


        if ( ! is_email( $email ) ) {

            return new WP_REST_Response(
                [
                    'success' => false,
                    'message' => 'Invalid email',
                ],
                400
            );
        }

        $lead = new Lead(
            $name,
            $email,
            $phone
        );

        $id = $this->repository->create( $lead );

        $this->webhookService->send( $lead );

        return new WP_REST_Response(
            [
                'success' => true,
                'id'      => $id,
            ],
            201
        );
    }
}