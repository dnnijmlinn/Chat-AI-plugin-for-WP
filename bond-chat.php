<?php
/*
Plugin Name: AI chat bot
Description: Interface for our custom chatbot.
Version: 1.0
Author: Denis Bogdanov
*/

include(plugin_dir_path(__FILE__) . 'inc/log-functions.php');
include(plugin_dir_path(__FILE__) . 'inc/chatbot-interface-functions.php');
include(plugin_dir_path(__FILE__) . 'inc/settings-functions.php');
include(plugin_dir_path(__FILE__) . 'inc/admin-functions.php');


$pythonScriptPath = plugin_dir_path(__FILE__) . 'chatgpt/chatgpt.py';
$command = escapeshellcmd("/usr/bin/python3 $pythonScriptPath");
$output = shell_exec($command);


function get_file_content() {
    $filepath = sanitize_text_field($_POST['filepath']);
    my_log('File path: ' . $filepath);

    if (file_exists($filepath)) {
        $content = file_get_contents($filepath);
        my_log('File content: ' . $content);

        echo $content;
    } else {
        my_log('"File not found"');

        echo 'File not found.';
    }

    wp_die(); 
}
add_action('wp_ajax_get_file_content', 'get_file_content');


add_action('rest_api_init', function() {
    register_rest_route('my_namespace/v1', '/settings/', array(
        'methods' => 'GET',
        'callback' => 'get_my_settings',
    ));
});

function get_my_settings(WP_REST_Request $request) {
    $materials_options = get_option('materials_options');
    return new WP_REST_Response($materials_options, 200);
}

function chatbot_enqueue_admin_styles() {
    wp_enqueue_style('chatbot_admin_styles', plugins_url('styles/style.css', __FILE__));
}

add_action('admin_enqueue_scripts', 'chatbot_enqueue_admin_styles');


function my_custom_admin_styles() {
    global $pagenow;

    if ($pagenow == 'tools.php' && $_GET['page'] == 'chat-bot-settings') {
        echo '<style>
            .wrap h1 {
                background: url(https://img.freepik.com/free-vector/tropical-landscape-with-ocean-and-sunset_107791-2244.jpg) no-repeat center center;
                background-size: cover;
                color: white; 
                padding: 1.3rem 0;
            }
        </style>';
    }
}
add_action('admin_head', 'my_custom_admin_styles');


function hide_update_notice() {
    global $pagenow;

    if ($pagenow == 'tools.php' && isset($_GET['page']) && $_GET['page'] == 'chat-bot-settings') {
        echo '<style>.update-nag, .notice-warning { display: none; }</style>';
    }
}
add_action('admin_head', 'hide_update_notice');


?>
