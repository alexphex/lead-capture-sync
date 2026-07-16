<?php

namespace LeadCaptureSync\Integrations;

use LeadCaptureSync\Models\Lead;

defined( 'ABSPATH' ) || exit;

class ContactForm7Provider implements FormProviderInterface {

    public function getLead(): ?Lead {

        if ( ! class_exists( 'WPCF7_Submission' ) ) {
            return null;
        }

        $submission = \WPCF7_Submission::get_instance();

        if ( ! $submission ) {
            return null;
        }

        $data = $submission->get_posted_data();

        $name = sanitize_text_field(
            $data['your-name'] ?? ''
        );

        $email = sanitize_email(
            $data['your-email'] ?? ''
        );

        $phone = sanitize_text_field(
            $data['tel-301'] ?? ''
        );

        if ( empty( $name ) || empty( $email ) ) {
            return null;
        }

        return new Lead(
            $name,
            $email,
            $phone
        );
    }
}