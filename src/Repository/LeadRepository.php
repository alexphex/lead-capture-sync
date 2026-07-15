<?php

namespace LeadCaptureSync\Repository;

use LeadCaptureSync\Models\Lead;
use LeadCaptureSync\Models\LeadStatus;

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
        update_post_meta(
            $post_id,
            'status',
            LeadStatus::PENDING
        );

        return $post_id;
    }

    //change the status of a lead
    public function findByEmail( string $email ): ?int {

        $query = new \WP_Query(
            [
                'post_type'      => 'lead',
                'posts_per_page' => 1,
                'meta_query'     => [
                    [
                        'key'   => 'email',
                        'value' => $email,
                        'compare' => '=',
                    ],
                ],
                'fields' => 'ids',
            ]
        );

        if ( empty( $query->posts ) ) {
            return null;
        }

        return (int) $query->posts[0];
    }

    //change the status of a lead
    public function updateStatus(
        int $leadId,
        string $status
    ): bool {
    
    return (bool) update_post_meta(
            $leadId,
            'status',
            $status
        );
    }

    //save the error message of a lead
    public function updateError(
        int $leadId,
        string $error
    ): bool {

        return (bool) update_post_meta(
            $leadId,
            'webhook_error',
            $error
        );
    }

}