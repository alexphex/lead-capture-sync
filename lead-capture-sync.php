<?php
/**
 * Plugin Name: Lead Capture Sync
 * Description: Sync WordPress leads with external automation platforms.
 * Version: 1.0.0
 * Author: alex
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/vendor/autoload.php';

$plugin = new LeadCaptureSync\Plugin();
$plugin->init();