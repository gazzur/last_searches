<?php
/*
Plugin Name: Last Searches
Description: Display the last 3 search phrases used in the search form.
Version: 1.0
Author: Digitex
*/

// Hook into the WordPress search action
add_action('template_redirect', 'last_searches_save_search');

// Function to save search queries
function last_searches_save_search() {
    if (is_search() && isset($_GET['s'])) {
        $search_query = sanitize_text_field($_GET['s']);
        $searches = get_option('last_searches', array());

        // Store only unique searches
        if (!in_array($search_query, $searches)) {
            // Limit the array to store only the last 3 searches
            $searches = array_slice(array_merge(array($search_query), $searches), 0, 3);

            // Update the option in the database
            update_option('last_searches', $searches);
        }
    }
}

// Shortcode to display last searches
function last_searches_shortcode($atts) {
    $searches = get_option('last_searches', array());

    // Output the list of last searches
    $output = '<ul>';
    foreach ($searches as $search) {
        $output .= '<li>' . esc_html($search) . '</li>';
    }
    $output .= '</ul>';

    return $output;
}

// Register the shortcode
add_shortcode('last_searches', 'last_searches_shortcode');

// Function to display the last searches widget on the dashboard
function last_searches_dashboard_widget() {
    ?>
    <div class="last-searches-widget">
        <h2>Last Searches</h2>
        <?php echo do_shortcode('[last_searches]'); ?>
    </div>
    <?php
}

// Function to add the widget to the WordPress dashboard
function last_searches_add_dashboard_widget() {
    wp_add_dashboard_widget(
        'last_searches_widget',
        'Last Searches',
        'last_searches_dashboard_widget'
    );
}

// Hook to add the widget to the dashboard
add_action('wp_dashboard_setup', 'last_searches_add_dashboard_widget');

?>

