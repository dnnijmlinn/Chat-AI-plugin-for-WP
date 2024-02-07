<?php

// function get_communication_history() {
//     $args = array(
//         'post_type' => 'chat_history',
//         'posts_per_page' => -1 
//     );

//     $query = new WP_Query($args);
//     $history = "";

//     if ($query->have_posts()) {
//         while ($query->have_posts()) {
//             $query->the_post();
//             $history .= '<div><strong>' . get_the_title() . '</strong><br />' . nl2br(get_the_content()) . '</div><hr />';
//         }
//         wp_reset_postdata(); 
//     } else {
//         $history = '<p>No communication history found.</p>';
//     }
//     error_log('Communication history: ' . print_r($history, true));

//     return $history;
// }

function chatbot_settings_page_content() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'api_settings';
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <h2 class="nav-tab-wrapper">
            <a href="?page=chat-bot-settings&tab=api_settings" class="nav-tab <?php echo $active_tab == 'api_settings' ? 'nav-tab-active' : ''; ?>">API Settings</a>
            <a href="?page=chat-bot-settings&tab=materials" class="nav-tab <?php echo $active_tab == 'materials' ? 'nav-tab-active' : ''; ?>">Materials</a>
            <!-- <a href="?page=chat-bot-settings&tab=communication_history" class="nav-tab <?php echo $active_tab == 'communication_history' ? 'nav-tab-active' : ''; ?>">Communication History</a> -->
        </h2>

        <form action="options.php" method="post">
            <?php
            if($active_tab == 'api_settings') {
                settings_fields('chatbot_options');
                do_settings_sections('chatbot');
            } else if($active_tab == 'materials') {
                settings_fields('materials_options');
                do_settings_sections('materials');
            } 
            // else if ($active_tab == 'communication_history') {
            //     echo get_communication_history();
            // }
            
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}

function chatbot_settings_init() {
    register_setting('chatbot_options', 'chatbot_options');
    register_setting('materials_options', 'materials_options');

    add_settings_section(
        'chatbot_section',
        'API Settings',
        'chatbot_section_callback',
        'chatbot'
    );

    add_settings_section(
        'materials_section',
        'Materials Settings',
        'materials_section_callback',
        'materials'
    );

    // add_settings_section(
    //     'communication_history_section',
    //     'Communication History',
    //     'communication_history_section_callback',
    //     'communication_history'
    // );

    add_settings_field(
        'chatbot_field',
        'GPT API Key',
        'chatbot_field_callback',
        'chatbot',
        'chatbot_section'
    );

    add_settings_field(
        'chatbot_hi_text_field',
        'Firs text message from bot', 
        'chatbot_hi_text_callback', 
        'chatbot', 
        'chatbot_section' 
    );
    
    add_settings_field(
        'materials_field',
        'Materials Setting',
        'materials_field_callback',
        'materials',
        'materials_section'
    );
}
add_action('admin_init', 'chatbot_settings_init');

function chatbot_section_callback() {
    echo '<p>Enter your settings below:</p>';
}

function chatbot_field_callback() {
    $options = get_option('chatbot_options');
    echo '<input type="text" class="gpt_api_input" id="chatbot_field" name="chatbot_options[api_key]" value="' . esc_attr($options['api_key']) . '" ">';
}

function chatbot_hi_text_callback(){
    $options = get_option('chatbot_options');
    $chatbot_hi_text = isset($options['chatbot_hi_text']) ? $options['chatbot_hi_text'] : '';
    echo '<textarea id="chatbot_hi_text_field" name="chatbot_options[chatbot_hi_text]" rows="10" cols="80">' . esc_textarea($chatbot_hi_text) . '</textarea>';
}

function materials_section_callback() {
    echo '<p>Here you can add materials:</p>';
}

function materials_field_callback() {
    $options = get_option('materials_options'); 
    $materials_setting = isset($options['materials_setting']) ? $options['materials_setting'] : '';
    echo '<textarea id="materials_field" name="materials_options[materials_setting]" rows="10" cols="80">' . esc_textarea($materials_setting) . '</textarea>';
}

function save_api_key_to_file() {
    $options = get_option('chatbot_options');
    $api_key = esc_attr($options['api_key']);

    $file = fopen(plugin_dir_path(__FILE__) . 'api_key.txt', 'w');
    fwrite($file, $api_key);
    fclose($file);
}
add_action('updated_option', 'save_api_key_to_file');
