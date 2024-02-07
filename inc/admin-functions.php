<?php

 function chatbot_settings_page() {
    add_submenu_page(
        'tools.php',
        'AI chat bot',
        'AI chat bot',
        'manage_options',
        'chat-bot-settings',
        'chatbot_settings_page_content'
    );
}
add_action('admin_menu', 'chatbot_settings_page');


 function chatbot_materials_menu() {
    add_submenu_page(
        'chatbot', 
        'Materials Settings',
        'Materials', 
        'manage_options', 
        'chatbot_materials', 
        'chatbot_materials_callback' 
    );
}
add_action('admin_menu', 'chatbot_materials_menu');

function chatbot_materials_callback() {
    $options = get_option('materials_options');
    $materials_text = isset($options['materials_text']) ? $options['materials_text'] : '';
    ?>
    <h2>Materials Settings</h2>
    <p>Here you can configure the materials settings.</p>
    <form method="post" action="options.php">
        <?php
        settings_fields('materials_options');
        do_settings_sections('materials');
        ?>
        <textarea name="materials_options[materials_text]" rows="20" cols="80"><?php echo esc_textarea($materials_text); ?></textarea>
        <?php submit_button(); ?>
    </form>
    <?php
}

// function create_chat_history_post_type() {
//     register_post_type( 'chat_history',
//         array(
//             'labels' => array(
//                 'name' => __( 'Chat Histories' ),
//                 'singular_name' => __( 'Chat History' )
//             ),
//             'public' => true,
//             'has_archive' => true,
//         )
//     );
// }
// add_action( 'init', 'create_chat_history_post_type' );



// function save_chat_history() {
//     $user_message = sanitize_text_field($_POST['user_message']);
//     $bot_response = sanitize_text_field($_POST['bot_response']);

//     // Логируем данные в debug.log
//     error_log('User message: ' . $user_message);
//     error_log('Bot response: ' . $bot_response);

//     $post_id = wp_insert_post(array(
//         'post_title'    => 'Chat on ' . current_time('mysql'),
//         'post_content'  => "User: $user_message\n\nBot: $bot_response",
//         'post_status'   => 'publish',
//         'post_type'     => 'chat_history',
//     ));

//     if ($post_id == 0 || is_wp_error($post_id)) {
//         error_log('Failed to save chat history');
//         if (is_wp_error($post_id)) {
//             error_log('WP Error: ' . $post_id->get_error_message());
//         }
//     } else {
//         error_log('Chat history saved with post ID: ' . $post_id);
//     }

//     wp_send_json_success();
// }

// add_action( 'wp_ajax_save_chat_history', 'save_chat_history' );
// add_action( 'wp_ajax_nopriv_save_chat_history', 'save_chat_history' );
