<?php

namespace LeadCaptureSync\Admin;

defined( 'ABSPATH' ) || exit;

class SettingsPage {

    public function register(): void {

        add_options_page(
            'Lead Capture Sync',
            'Lead Capture Sync',
            'manage_options',
            'lead-capture-sync',
            [ $this, 'render' ]
        );

        register_setting(
            'lead_capture_sync',
            'lead_capture_sync_webhook_url'
        );

        register_setting(
            'lead_capture_sync',
            'lead_capture_sync_secret'
        );
    }

    public function render(): void {
        ?>
        <div class="wrap">
            <h1>Lead Capture Sync</h1>

            <form method="post" action="options.php">

                <?php settings_fields( 'lead_capture_sync' ); ?>

                <table class="form-table">

                    <tr>
                        <th>Webhook URL</th>
                        <td>
                            <input
                                type="url"
                                name="lead_capture_sync_webhook_url"
                                value="<?php echo esc_attr( get_option( 'lead_capture_sync_webhook_url' ) ); ?>"
                                class="regular-text"
                            >
                        </td>
                    </tr>

                    <tr>
                        <th>Secret Token</th>
                        <td>
                            <input
                                type="text"
                                name="lead_capture_sync_secret"
                                value="<?php echo esc_attr( get_option( 'lead_capture_sync_secret' ) ); ?>"
                                class="regular-text"
                            >
                        </td>
                    </tr>

                </table>

                <?php submit_button(); ?>

            </form>

        </div>
        <?php
    }
}