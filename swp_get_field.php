<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class SWP_Get_Custom_Field {

	// MAIN FUNCTION
	public function swp_gcf_main_func( $params, $content = null ) {

		extract(shortcode_atts(array(
			'id' 		=> 'id',
			'field'		=> 'field',
			'size'		=> 'size',
			'link'		=> 'link',
			'target'	=> 'target',
			'class'		=> 'class',
		), $params));

		$z = new SWPPostCustomLoop();

		if( $z->swp_validate_param( $id, "id" ) ) {
			// can be post ID, slug or identifier (custom field)
			
			global $wpdb;
			
			if( is_numeric( $id ) ) {
				
				//echo 'SELECT a.id FROM '.$wpdb->prefix.'posts AS a INNER JOIN '.$wpdb->prefix.'postmeta AS b ON a.id=b.post_id WHERE b.meta_value="'.$id.'"';
				
				/*$query = $wpdb->get_results( 'SELECT a.id FROM '.$wpdb->prefix.'posts AS a INNER JOIN '.$wpdb->prefix.'postmeta AS b ON a.id=b.post_id WHERE b.meta_value="'.$id.'"', OBJECT );
				
		    	if( is_numeric( $query[0]->id ) ) {
			    	$this_id = $query[0]->id;
			    } else {*/
			    	$this_id = $id;
			    //}
			    
			    // Restore original Post Data
				//wp_reset_postdata();
				
			} else {
				
				// check if post title or name (slug)
				$explode_id = explode( " ", $id );
				if( count( $explode_id ) > 1 ) {
					// title
					$use_this = "post_title='".$id."' ";
				} else {
					// slug
					$use_this = "post_name='".$id."' ";
				}
					
			    $query = $wpdb->get_results( "SELECT id FROM ".$wpdb->prefix."posts WHERE ".$use_this, OBJECT );
			    
				// Restore original Post Data
				wp_reset_postdata();
			    
			    if( $query[0]->id ) {
			    	
			    	$this_id = $query[0]->id;
			    	
			    } else {
			    	
			    	$query = $wpdb->get_results( 'SELECT a.id FROM '.$wpdb->prefix.'posts AS a INNER JOIN '.$wpdb->prefix.'postmeta AS b ON a.id=b.post_id WHERE b.meta_value="'.$id.'"', OBJECT );
			    	if( $query[0]->id ) {
				    	$this_id = $query[0]->id;
				    }
				    
				    // Restore original Post Data
					wp_reset_postdata();
			    }
			    
			}
			
		} else {
			// retrieving from current post
			$this_id = get_the_ID();
		}

		// -----------------------------------------------
		// GET CUSTOM FIELD DATA
		$get_this = get_post_meta( $this_id, $field, TRUE );
		// -----------------------------------------------

		// validate target
		if( $target == "_blank" ) {
			$targ = "target='".$target."'";
		} else {
			$targ = "";
		}

		// check if field is image
		if( wp_attachment_is_image( $get_this ) ) {
			
			$this_image = wp_get_attachment_image( $get_this, $size );
			
			// check if link is for itself
			if( $link == "_self" ) {

				$link = wp_get_attachment_image_src( $get_this, 'FULL' )[0];
				$a_link = "<a href='".$link."' ".$targ.">".$this_image."</a>";

			} elseif( strtolower( $link ) == 'true' ) {
				
				$a_link = "<a href='".$get_this."' ".$targ.">".$this_image."</a>";

			} else {

				$a_link = $this_image;

			}
			
			return $a_link;

		} else {
			
			if( is_array( $get_this ) ) {
				// ACF's flexible field

				/*foreach( $get_this as $key => $value ) {
					echo $key." = ".$value."<br />";
				}*/
				//var_dump( $get_this );
				
			} else {

				// validate if link 				
				if( strtolower( $link ) == 'true' ) {

					// use content if indicated
					if( $content ) {
						$a_link = "<a href='".$get_this."' ".$targ.">".do_shortcode( $content )."</a>";
					} else {
						// use the link as the name
						$a_link = "<a href='".$get_this."' ".$targ.">".do_shortcode( $get_this )."</a>";
					}

				} else {
					$a_link = $get_this;
				}

				return $a_link;

			}
		}

	}

	// CONSTRUCT
	public function __construct() {

		if( !is_admin() ) {
			add_shortcode( 'swp_get_field', array( $this, 'swp_gcf_main_func' ) );
		}

	}

}

$swp_gcf_load = new SWP_Get_Custom_Field();