<?php

namespace LeadCaptureSync\Models;

defined( 'ABSPATH' ) || exit;

final class LeadStatus {

    public const PENDING = 'pending';

    public const PROCESSING = 'processing';

    public const PROCESSED = 'processed';

    public const FAILED = 'failed';

    private function __construct() {
    }
}
