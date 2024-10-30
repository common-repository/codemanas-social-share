<?php
/*
Plugin Name: CodeManas Social Share
Description: A simple social sharing plugin
Plugin URI: https://wordpress.org/plugins/codemanas-social-share/
Author: codemanas
Author URI: https://www.codemanas.com/
Version: 1.0.1
License: http://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
Text Domain: codemanans-social-share
*/

/**
 * Define Plugin FILE PATH
 */
if ( ! defined( 'CODEMANAS_SS_FILE_PATH' ) ) {
	define( 'CODEMANAS_SS_FILE_PATH', __FILE__ );
}
if ( ! defined( 'CODEMANAS_SS_DIR_PATH' ) ) {
	define( 'CODEMANAS_SS_DIR_PATH', plugin_dir_path( __FILE__ ) );
}

/*class to display settings for admin*/
require_once( CODEMANAS_SS_DIR_PATH . '/inc/class/class-codemanas-social-share-admin.php' );

/*the shortcode that displays social sharing*/
require_once( CODEMANAS_SS_DIR_PATH . '/inc/class/class-codemanas-social-share-shortcode.php' );

/*setup basic setting when activating plugin*/
function codemanas_social_share_plugin_setup() {
	$options    = array(
		'cm_allowed'       => array( 'post' ),
		'cm_where_to_show' => array( 'before-the-content' ),
		'cm_choose_color'  => 'original-color',
		'cm_icon_size'     => 'medium'
	);
	$cm_options = get_option( 'codemanas_social_sharing_options' );
	if ( empty( $cm_options ) ) {
		update_option( 'codemanas_social_sharing_options', $options );
	}
}

register_activation_hook( CODEMANAS_SS_FILE_PATH, 'codemanas_social_share_plugin_setup' );

/*delete plugin settings when plugin is uninstalled*/
function codemanas_social_share_uninstall_plugin() {
	delete_option( 'codemanas_social_sharing_options' );
}

register_uninstall_hook( CODEMANAS_SS_FILE_PATH, 'codemanas_social_share_uninstall_plugin' );

/*enqueue frontend scripts and styles*/
function codemanas_social_share_enqueue_scripts() {
	$options       = get_option( 'codemanas_social_sharing_options' );
	$allowed_posts = $options['cm_allowed'];
	wp_register_style( 'codemanas-social-share', plugin_dir_url( CODEMANAS_SS_FILE_PATH ) . 'assets/css/style.css', false, false,
		'all' );
	wp_register_script( 'codemanas-social-sharer', plugin_dir_url( CODEMANAS_SS_FILE_PATH ) . 'assets/js/frontend-script.js',
		array( 'jquery' ), false, true );
	if ( is_singular( $allowed_posts ) ) {
		wp_enqueue_style( 'codemanas-social-share' );
		wp_enqueue_script( 'codemanas-social-sharer' );
	}
}

add_action( 'wp_enqueue_scripts', 'codemanas_social_share_enqueue_scripts' );

/* Hook into the_content filter and add shortcode according to plugin options*/
function show_codemanas_share_button( $content ) {
	$options              = get_option( 'codemanas_social_sharing_options' );
	$allowed_posts        = $options['cm_allowed'];
	$where_to_show_option = $options['cm_where_to_show'];

	if ( ! empty( $allowed_posts ) && is_singular( $allowed_posts ) && in_the_loop()
	     && ! empty( $where_to_show_option )
	) {
		foreach ( $where_to_show_option as $option ) {
			if ( $option == 'before-the-content' ) {
				/*before the content*/
				$content = do_shortcode( '[codemanas_social_share]' ) . $content;

			} else if ( $option == 'after-the-content' ) {
				/*after the content*/
				$content .= do_shortcode( '[codemanas_social_share]' );
			} else if ( $option == 'float-left' ) {
				$content .= '<div class="cm-left-floater">' . do_shortcode( '[codemanas_social_share]' ) . '</div>';
			}
		}
	}

	return $content;
}

add_filter( 'the_content', 'show_codemanas_share_button' );

/* Hook into the post_thumbnail_html to add social sharing inside the featured image*/
function codemanas_social_share_show_in_featured_image( $html ) {
	$options              = get_option( 'codemanas_social_sharing_options' );
	$allowed_posts        = $options['cm_allowed'];
	$where_to_show_option = $options['cm_where_to_show'];
	if ( ! empty( $allowed_posts ) && is_singular( $allowed_posts ) && is_main_query()
	     && ! empty( $where_to_show_option )
	     && in_array( 'featured-image', $where_to_show_option )
	) {
		return '<div class="cm-feature-image-wrap">' . $html . do_shortcode( '[codemanas_social_share]' ) . '</div>';
	}

	return $html;
}

add_filter( 'post_thumbnail_html', 'codemanas_social_share_show_in_featured_image' );