<?php
if (!defined('ABSPATH')) {
    exit; 
}

function chatbot_interface_shortcode() {
    $options = get_option('chatbot_options');
    $chatbot_hi_text = isset($options['chatbot_hi_text']) ? $options['chatbot_hi_text'] : 'Hi';  // Значение по умолчанию 'Hi'

    $content = '<div id="chatbot_area" data-hi-text="' . esc_attr($chatbot_hi_text) . '">
    <div id="chat_messages"></div>
    <div id="input_wrapper">
        <textarea id="chat_input" placeholder="Type your question..." rows="1"></textarea>
        <button id="chat_send">Send</button>
    </div>
</div>
';

    return $content;
}
add_shortcode('chatbot_interface', 'chatbot_interface_shortcode');

function chatbot_interface_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('chatbot_interface_script', plugins_url('scripts/script.js', dirname(__FILE__)), array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'chatbot_interface_scripts');

function chatbot_interface_styles() {
    wp_enqueue_style('chatbot_interface_style', plugins_url('styles/style.css', dirname(__FILE__)));
}
add_action('wp_enqueue_scripts', 'chatbot_interface_styles');
