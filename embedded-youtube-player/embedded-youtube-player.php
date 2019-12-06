<?php
/**
 * Plugin Name: Embedded Youtube Player
 * Description: Easy and configurable player that seamlessly plays youtube videos.
 * Version: 0.1
 * Author: Steven Mønsted Nielsen
 * Author URI: http://www.pankado.com
 * Text Domain: embedded-youtube-player
 * Domain Path: /languages
 * License: GPL2 v2.0
**/
if (! defined('ABSPATH')) {
    exit;
}

define('EYTP_URL', plugins_url('', __FILE__));
define('EYTP_VERSION', 0.1);

include(plugin_dir_path(__FILE__) . './includes/class-embedded-yt-player.php');

/* Init */
EmbeddedYTPlayer();

/* Shortcode support */
function eytp_shortcode_function($atts = array())
{
    //Define shortcode attributes
    //Note: Set to empty as defaults are managed by the EmbeddedYTPlayer class
    $settings = array_filter(shortcode_atts(array(
      'video_id' => '',
      'width' => '',
      'height' => '',
      'disable_controls' => '',
      'autoplay' => '',
      'loop' => ''
    ), $atts));
    return EmbeddedYTPlayer()->render_player($settings);
}

add_shortcode('embedded_yt_player', 'eytp_shortcode_function');

/* WPBakery/Visual Composer Support */
add_action('vc_before_init', 'eytp_vc_integrate');
function eytp_vc_integrate()
{
    $current_user = wp_get_current_user();
    if (!user_can($current_user, 'administrator')) return;

    vc_map(array(
        'name' 					=> esc_html__('Embedded Youtube Player', ' embedded-youtube-player'),
        'base' 					=> 'embedded_yt_player',
        'class' 				=> '',
        'icon' 					=> '',
        'category' 			=> esc_html__('Content', 'js_composer'),
        'description' 	=> esc_html__('A highly customizable embedded Youtube player', 'embedded-youtube-player'),
        'params' 				=> array(
            array(
                'type' 		  	=> 'textfield',
                'class' 		  => '',
                'heading' 		=> esc_html__('Video ID', 'embedded-youtube-player'),
                'description' => esc_html__('The ID of the video to display.','embedded-youtube-player'),
                'param_name' 	=> 'video_id'
            ),
            array(
                'type' 			  => 'textfield',
                'class' 	   	=> '',
                'heading' 		=> esc_html__('Width', 'embedded-youtube-player'),
                'description' => esc_html__('The width of the video. Relative values supported.','embedded-youtube-player'),
                'param_name' 	=> 'width',
                'value'       => '100%'
            ),
            array(
                'type' 			  => 'textfield',
                'class' 		  => '',
                'heading' 		=> esc_html__('Height', 'embedded-youtube-player'),
                'description' => esc_html__('The height of the video. Relative values supported.','embedded-youtube-player'),
                'param_name' 	=> 'height',
                'value'       => '100%'
            ),
            array(
                'type' 			  => 'checkbox',
                'class' 		  => '',
                'heading' 		=> esc_html__('Disable Controls', 'embedded-youtube-player'),
                'param_name' 	=> 'disable_controls'
            ),
            array(
                'type' 			  => 'checkbox',
                'class' 	  	=> '',
                'heading' 		=> esc_html__('Autoplay', 'embedded-youtube-player'),
                'param_name' 	=> 'autoplay'
            ),
            array(
                'type' 		  	=> 'checkbox',
                'class' 	  	=> '',
                'heading' 		=> esc_html__('Loop Video', 'embedded-youtube-player'),
                'param_name' 	=> 'loop'
            ),
        )
    ));
}
