<?php

namespace LeadCaptureSync\Services;

defined( 'ABSPATH' ) || exit;

class Logger {

    public function error( string $message ): void {

        error_log(
            '[Lead Capture Sync] ' . $message
        );
    }
}