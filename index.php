<?php

/**
 * Plugin Name: WhatsApp Floating Button for WordPress
 * Plugin URI: https://ksrio.com
 * Description: Adds a floating WhatsApp button to your WordPress website.
 * Version: 1.0.0
 * Author: KSRIO.COM
 * Author URI: https://ksrio.com
 * Text Domain: whatsapp-floating-button
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Add settings page to the admin menu
function wfb_add_settings_page()
{
    add_options_page(
        'WhatsApp Floating Button Settings',
        'WhatsApp Button',
        'manage_options',
        'whatsapp-floating-button',
        'wfb_render_settings_page'
    );
}
add_action('admin_menu', 'wfb_add_settings_page');

// Register settings
function wfb_register_settings()
{
    register_setting('wfb_settings_group', 'wfb_phone_number');
    register_setting('wfb_settings_group', 'wfb_button_position', array(
        'default' => 'right'
    ));
    register_setting('wfb_settings_group', 'wfb_button_color', array(
        'default' => '#25D366'
    ));
    register_setting('wfb_settings_group', 'wfb_message', array(
        'default' => 'Hello, I have a question about your website.'
    ));
}
add_action('admin_init', 'wfb_register_settings');

// Render settings page
function wfb_render_settings_page()
{
?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('wfb_settings_group');
            do_settings_sections('wfb_settings_group');
            ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="wfb_phone_number">WhatsApp Phone Number</label>
                    </th>
                    <td>
                        <input type="text" id="wfb_phone_number" name="wfb_phone_number"
                            value="<?php echo esc_attr(get_option('wfb_phone_number')); ?>" class="regular-text" />
                        <p class="description">Enter your WhatsApp number with country code (e.g., 11234567890)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="wfb_button_position">Button Position</label>
                    </th>
                    <td>
                        <select id="wfb_button_position" name="wfb_button_position">
                            <option value="right" <?php selected(get_option('wfb_button_position'), 'right'); ?>>Right</option>
                            <option value="left" <?php selected(get_option('wfb_button_position'), 'left'); ?>>Left</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="wfb_button_color">Button Color</label>
                    </th>
                    <td>
                        <input type="color" id="wfb_button_color" name="wfb_button_color"
                            value="<?php echo esc_attr(get_option('wfb_button_color', '#25D366')); ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="wfb_message">Default Message</label>
                    </th>
                    <td>
                        <input type="text" id="wfb_message" name="wfb_message"
                            value="<?php echo esc_attr(get_option('wfb_message', 'Hello, I have a question about your website.')); ?>" class="regular-text" />
                        <p class="description">This message will be pre-filled when users click the WhatsApp button</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

// Add WhatsApp button to frontend
function wfb_add_whatsapp_button()
{
    $phone_number = get_option('wfb_phone_number');

    // Don't show the button if no phone number is set
    if (empty($phone_number)) {
        return;
    }

    $position = get_option('wfb_button_position', 'right');
    $color = get_option('wfb_button_color', '#25D366');
    $message = urlencode(get_option('wfb_message', 'Hello, I have a question about your website.'));

    $whatsapp_url = "https://wa.me/{$phone_number}?text={$message}";

    // CSS for the button
    echo '<style>
        .wfb-floating-button {
            position: fixed;
            bottom: 20px;
            z-index: 9999;
            ' . ($position === 'right' ? 'right: 20px;' : 'left: 20px;') . '
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: ' . esc_attr($color) . ';
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .wfb-floating-button:hover {
            transform: scale(1.1);
        }
        
        .wfb-floating-button svg {
            width: 35px;
            height: 35px;
            fill: white;
        }
    </style>';

    // HTML for the button
    echo '<a href="' . esc_url($whatsapp_url) . '" class="wfb-floating-button" target="_blank" rel="noopener noreferrer" aria-label="Chat on WhatsApp">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
            <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>
        </svg>
    </a>';
}
add_action('wp_footer', 'wfb_add_whatsapp_button');

// Add settings link on plugin page
function wfb_settings_link($links)
{
    $settings_link = '<a href="options-general.php?page=whatsapp-floating-button">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'wfb_settings_link');


?>