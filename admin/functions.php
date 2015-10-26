<?php

defined( 'ABSPATH' ) or die( 'No direct access allowed' );
/* Includes font-awesome */
add_action ('init', 'wpptwi_enq_files');
function wpptwi_enq_files(){
	wp_enqueue_style('font-awesome', WPPTWI_PLUGIN_URL.'/css/font-awesome.min.css');
}

/* Includes color picker js and css in WP admin ara*/
add_action( 'admin_enqueue_scripts', 'wpptwi_insert_color_picker' );
function wpptwi_insert_color_picker( $hook ) {
 
    if( is_admin() ) { 
    	wp_enqueue_style('icon', WPPTWI_PLUGIN_URL.'/css/fontawesome-iconpicker.min.css');
        wp_enqueue_style( 'wpptwi-styles', WPPTWI_PLUGIN_URL.'/css/wpptwi-admin.css' );
        wp_enqueue_style( 'wp-color-picker' ); 

		wp_enqueue_script('icon-picker', WPPTWI_PLUGIN_URL.'/js/fontawesome-iconpicker.js');               
        // Include custom jQuery file with WordPress Color Picker dependency
        wp_enqueue_script( 'wpptwi-color-picker', WPPTWI_PLUGIN_URL.'/js/custom.js', array( 'wp-color-picker' ), false, true ); 
        
    }
}

/* Saves meta values on page/post update*/
add_action('save_post', 'wpptwi_save_page_icon');
function wpptwi_save_page_icon ($post_id){
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['wpptwi_meta_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['wpptwi_meta_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'wpptwi_save_meta_box_data' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
                //     so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
	
		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		/* OK, its safe for us to save the data now. */

		// Sanitize the user input.
		$page_icon = sanitize_text_field( $_POST['wpptwi-icon-txt'] );
		$icon_color = sanitize_text_field( $_POST['wpptwi-icon-font-color'] );
		$icon_size = sanitize_text_field( $_POST['wpptwi-icon-font-size'] );

		// Update the meta field.
		update_post_meta( $post_id, '_wpptwi_icon', $page_icon );
		update_post_meta( $post_id, '_wpptwi_icon_color', $icon_color );
		update_post_meta( $post_id, '_wpptwi_icon_size', $icon_size );
}

/* Ouputs icon in Front End with page/post title*/
if(!is_admin())
add_filter('the_title', 'display_pagetitle_icon');

function display_pagetitle_icon($title){
		global $post;
		/* get values from post meta*/
		$value = get_post_meta( $post->ID, '_wpptwi_icon', true );
		$icon_color = get_post_meta( $post->ID, '_wpptwi_icon_color', true );
		$icon_size = get_post_meta( $post->ID, '_wpptwi_icon_size', true );

		/* escapes html entities*/
		$icon = esc_attr( $value );
		$icon_color = esc_attr( $icon_color );
		$icon_size = esc_attr( $icon_size );

		/* checks if the icon is empty, title is in the loop*/	
		if(!empty($icon) && in_the_loop()){

			/* check if this is the current page */
			if( is_page($post->ID) && $title == $post->post_title  )

				return '<i class="fa '.$icon.'" style="color:'.$icon_color.'; font-size:'.$icon_size.'px"></i>  '.$title;

			/* check if this is the current post */
			else if( is_single($post->ID) && $title == $post->post_title  ){

				return '<i class="fa '.$icon.'" style="color:'.$icon_color.'; font-size:'.$icon_size.'px"></i>  '.$title;
			}

			/* else return title */
			else{
				return $title;	
			}
		}
		else{
			return $title;	
		}
	
}