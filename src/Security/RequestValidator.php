<?php

namespace LeadCaptureSync\Security;

use WP_REST_Request;

defined( 'ABSPATH' ) || exit;

class RequestValidator {

    public function validate( WP_REST_Request $request ): bool {

        $token = (string) $request->get_header(
            'X-Lead-Sync-Token'
        );

        $secret = (string) get_option(
            'lead_capture_sync_secret'
        );

        if ( $secret === '' || $token === '' ) {
            return false;
        }

        return hash_equals(
            $secret,
            $token
        );

    }
}