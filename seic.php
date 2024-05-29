<?php
/**
 * Plugin Name: Search Engine Indexing Checker
 * Description: A plugin that periodically checks if search engines are discouraged from indexing and disables that option if it's set.
 * Version: 1.0
 * Author: Stephen Phillips
 * Plugin URI: https://github.com/sphillips1337/seic
 * Author URI: https://www.stephenphillips.co.uk
 * License: GPL v2 or later 
 */

// Hook into WP Cron to run our check periodically
add_action('wp', 'seic_schedule_check');

function seic_schedule_check() {
    if (!wp_next_scheduled('seic_check_indexing')) {
        wp_schedule_event(time(), 'hourly', 'seic_check_indexing');
    }
}

// Function to check and disable search engine indexing
function seic_check_indexing() {
    $discourage_indexing = get_option('blog_public');

    if ($discourage_indexing) {
        // Disable search engine indexing
        update_option('blog_public', 0);
        error_log('Search engine indexing was disabled.');
    }
}

// Hook into the activation and deactivation of the plugin
register_activation_hook(__FILE__, 'seic_activate');
register_deactivation_hook(__FILE__, 'seic_deactivate');

function seic_activate() {
    // Schedule the initial check
    wp_schedule_event(time(), 'hourly', 'seic_check_indexing');
}

function seic_deactivate() {
    // Clear the scheduled event
    wp_clear_scheduled_hook('seic_check_indexing');
}
