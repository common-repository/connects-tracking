<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.connects.ch
 * @since      1.0.0
 *
 * @package    Connects_Tracking
 * @subpackage Connects_Tracking/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="connects-wrapper">
    <div class="panel-header">
        <a href="https://www.connects.ch/" target="_blank" class="connects-logo" title="Connects Affiliate-Marketing-Network"><img width="160px" src="<?= LOGO_URL ?>" alt="Logo" /></a>
        <span class="connects-subtitle">WP Plugin</span>
        <span class="connects-version">v<?= CONNECTS_TRACKING_VERSION ?></span>
    </div>

    <div class="connects-settings-wrapper">
        <?php if (!Connects_Tracking::is_woocommerce_active()) { ?>
            <div class="notice notice-error">
                <?php echo __('<strong>WooCommerce Plugin not active!</strong> Please activate WooCommerce before using this Plugin.', 'connects'); ?>
            </div>
        <?php } ?>

        <p><?php __('Enter your Connects ID and enable the needed tracking type to automatically integrate the Connects Tracking into your Woocommerce store.', 'connects') ?></p>
        <form method="post" action="options.php">
            <?php
            settings_fields('connects_option_group');
            do_settings_sections('connects-tracking-settings');
            submit_button();
            ?>
        </form>
    </div>
</div>