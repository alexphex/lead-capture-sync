<?php

namespace LeadCaptureSync\Repository;

use LeadCaptureSync\Models\Lead;

defined( 'ABSPATH' ) || exit;

class LeadRepository {

    public function create( Lead $lead ): int {

        $post_id = wp_insert_post(
            [
                'post_type'   => 'lead',
                'post_status' => 'publish',
                'post_title'  => $lead->getName(),
            ]
        );

        if ( is_wp_error( $post_id ) ) {
            return 0;
        }

        update_post_meta( $post_id, 'email', $lead->getEmail() );
        update_post_meta( $post_id, 'phone', $lead->getPhone() );
        update_post_meta( $post_id, 'status', 'pending' );

        return $post_id;
    }
}