<?php

namespace LeadCaptureSync\PostTypes;

defined( 'ABSPATH' ) || exit;

class LeadPostType {

    public function register(): void {

        register_post_type(
            'lead',
            [
                'labels' => [
                    'name'          => 'Leads',
                    'singular_name' => 'Lead',
                ],
                'public'          => false,
                'show_ui'         => true,
                'show_in_menu'    => true,
                'supports'        => [ 'title' ],
                'show_in_rest'    => false,
                'menu_position'   => 25,
                'menu_icon'       => 'dashicons-groups',
            ]
        );
    }
}