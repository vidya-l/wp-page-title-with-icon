<?php
/**********
Plugin Name: WP Page Title With Icon
Plugin URI: http://www.wpresource.net/
Description: A simple plugin to display icons with title in posts/pages. 
Author: Vidya L
Author URI: http://www.wpresource.net/
Version: 1.0.0
Text Domain: wp-page-title-icon
License: GPLv2 or later
**********/

defined( 'ABSPATH' ) or die( 'No direct access allowed' );
class wp_page_title_with_icon{
	/* Defines plugin url, plugin directory path etc*/
	public function __construct(){
		define( 'WPPTWI_PLUGIN', __FILE__ );

		define( 'WPPTWI_PLUGIN_BASENAME', plugin_basename( WPPTWI_PLUGIN ) );

		define( 'WPPTWI_PLUGIN_NAME', trim( dirname( WPPTWI_PLUGIN_BASENAME ), '/' ) );

		define( 'WPPTWI_PLUGIN_DIR', untrailingslashit( dirname( WPPTWI_PLUGIN ) ) );

		define( 'WPPTWI_PLUGIN_URL', untrailingslashit( plugins_url( '', WPPTWI_PLUGIN ) ) );

		$this->includeWPPTWIFiles();

		$this->addMetaBoxWPPTWI();

	}
	public function includeWPPTWIFiles(){
		require_once(WPPTWI_PLUGIN_DIR.'/admin/functions.php');
	}
	/* Adds meta box in pages/posts*/
	public function addMetaBoxWPPTWI(){

		function wpptwi_create_metabox(){
			$screens = array( 'page', 'post' );
			foreach ( $screens as $screen ) {
				add_meta_box('wpptwi-meta', __( 'WP Page Title Icon', 'wp-page-title-icon' ), 'wpptwi_meta_fun',	$screen);
			}
		}
		add_action('add_meta_boxes', 'wpptwi_create_metabox');

		function wpptwi_meta_fun( $post ){
			// Add a nonce field so we can check for it later.
			wp_nonce_field( 'wpptwi_save_meta_box_data', 'wpptwi_meta_box_nonce' );

			$value = get_post_meta( $post->ID, '_wpptwi_icon', true );
			$icon_color = get_post_meta( $post->ID, '_wpptwi_icon_color', true );
			$icon_size = get_post_meta( $post->ID, '_wpptwi_icon_size', true );
			echo '<div class="wpptwi-control">
					<label for="wpptwi-icon-txt">';
					_e( 'Choose Icon', 'wp-page-title-icon' );
					echo '</label> ';

			echo '<div class="input-group">
                        <input data-placement="bottomRight" class="form-control icp icp-auto" value="'.esc_attr( $value ).'" type="text" name="wpptwi-icon-txt" />
                        <span class="input-group-addon"></span>
                  </div>
                  </div>';          

            echo '<div class="wpptwi-control"><label for="wpptwi-icon-font-color">';
					_e( 'Choose Icon Color', 'wp-page-title-icon' );
					echo '</label> ';

			echo '<input type="text" name="wpptwi-icon-font-color" value="'.esc_attr( $icon_color ).'" id="wpptwi-icon-font-color"/></div>';

			echo '<div class="wpptwi-control"><label for="wpptwi-icon-font-size">';
					_e( 'Choose Icon Size', 'wp-page-title-icon' );
					echo '</label> ';

			echo '<input type="text" name="wpptwi-icon-font-size" value="'.esc_attr( $icon_size ).'" id="wpptwi-icon-font-size"/> px</div>';
		}
	}	
}

new wp_page_title_with_icon();