<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class SWPEmbedVideo {

	// MAIN FUNCTION
	public function swp_show_video( $params ) {

		extract(shortcode_atts(array(
			'id' 		=> 'id',
			'field'		=> 'field',
		), $params));

		$get_meta = get_post_meta( $id, $field, TRUE );

		if( $get_meta ) {

			/**
			 * Detect Shortcodes Ultimate. For use on Front End only.
			 */
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			// check for plugin using plugin name
			if ( is_plugin_active( 'shortcodes-ultimate/shortcodes-ultimate.php' ) ) {

				// shortcode is currently limited to YouTube but will work on displaying videos from Vimeo, DailyMotion, etc
				// [swp_youtube_advanced url="" playlist="" width="600" height="400" responsive="yes" controls="yes" autohide="alt" autoplay="no" mute="no" loop="no" rel="yes" fs="yes" modestbranding="no" theme="dark" https="no" wmode="" playsinline="no" title="" class=""]
				return do_shortcode( '[swp_youtube_advanced url="'.$get_meta.'" width="600" height="400" responsive="yes" controls="yes" autohide="alt" autoplay="no" mute="no" loop="no" rel="yes" fs="yes" modestbranding="no" theme="dark" https="yes" wmode="" playsinline="no" title="" class=""]' );

			} else {

				return 'Please download and install the <a href="https://wordpress.org/plugins/shortcodes-ultimate/" target="_blank">Shortcodes Ultimate</a> plugin';

			}

		} else {
			return "Not sure what you're lookig for. Please contact admin.";
		}

	}

	// CONSTRUCT
	public function __construct() {

		if( !is_admin() ) {
			add_shortcode( 'swp_show_video', array( $this, 'swp_show_video' ) );
		}

	}

}

$SWPEmbedVideo = new SWPEmbedVideo();