<?php
/**
 * Plugin Name: SWP - Query Array
 * Description: Custom querying of entries
 * Version: 1.3
 * Author: Jake Almeda
 * Author URI: http://smarterwebpackages.com/
 * Network: true
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// include query file
include_once( 'swp_wp_query.php' );
include_once( 'swp_get_field.php' );
include_once( 'swp_video_shortcode.php' );

class SWPPostCustomLoop {

	// MAIN FUNCTION
	public function swppostcustomloopdisplay( $params ) {

		extract(shortcode_atts(array(
			'post_type' 	=> 'post_type',
			'page_id'		=> 'page_id',
			'template'		=> 'template',
			'tax_name'		=> 'tax_name',
			'tax_term'		=> 'tax_term',
			//'paged'			=> 'paged',
			'orderbymeta'	=> 'orderbymeta',
			'orderby'		=> 'orderby',
			'order'			=> 'order',
			'show'			=> 'show',
		), $params));

		// check if template is declared
		if( $template && $template != 'template' ) {
			$temp = $template;
		} else {
			$temp = $this->swp_template_filename();
		}

		// show this number of entries
		if( $show && $show != 'show' ) {
			$show = $show;
		} else {
			$show = get_option('posts_per_page');
		}
		
		// validate template directory
		$template_dir = 'templates';
        $this->swp_check_templates( $template_dir );

        $out = '';

		//$paged1 = isset( $_GET['paged1'] ) ? (int) $_GET['paged1'] : 1;
		$swp_query_posts = new SWPWPQueryPosts();
		$the_query = $swp_query_posts->swp_query_archive_posts(
											$this->swp_validate_param( $post_type, 'post_type' ),
											$this->swp_validate_param( $page_id, 'page_id' ),
											$show,
											$this->swp_validate_param( $tax_name, 'tax_name' ),
											$this->swp_validate_param( $tax_term, 'tax_term' ),
											$this->swp_validate_param( $paged, 'paged' ),
											$this->swp_validate_param( $orderbymeta, 'orderbymeta' ),
											$this->swp_validate_param( $orderby, 'orderby' ),
											$this->swp_validate_param( $order, 'order' )
										);
    
		// The Loop
		if ( $the_query->have_posts() ) {
            
			while ( $the_query->have_posts() ) {

				$the_query->the_post();
				
				$out .= $this->get_local_file_contents( plugin_dir_path( __FILE__ ).$template_dir."/".$temp );
				
			}

			/* PAGINATION
			 * ---------------------------------------------------------------------------- */
				/* With previous and next pages
				 * -------------- */
				//previous_posts_link(); next_posts_link();

				/* Without previous and next pages
				 * -------------- */
				//the_posts_pagination( array( 'mid_size'  => 2 ) );

				/* Pagination with Alternative Prev/Next Text
				 * -------------- */
				/*echo get_the_posts_pagination( array(
				    'mid_size' => 2,
				    'prev_text' => __( '<<', 'textdomain' ),
				    'next_text' => __( '>>', 'textdomain' ),
				) );*/
			/* PAGINATION END
			 * ---------------------------------------------------------------------------- */

			/* Restore original Post Data */
			wp_reset_postdata();
		
		}

		return $out;
		
	}

	// GET CONTENTS OF THE TEMPLATE FILE
	public function get_local_file_contents( $file_path ) {

	    ob_start();
	    include $file_path;
	    return ob_get_clean();

	}

	// SIMPLE VALIDATION OF PARAMETER CONTENTS
	public function swp_validate_param( $parameter, $value ) {

		if( $parameter == $value ) {
			return NULL;
		} else {
			return $parameter;
		}

	}
	
	// VALIDATE TEMPLATE FOLDER AND FILE, AND CREATE IF MISSING
	private function swp_check_templates( $template_dir ) {
	    
	    $this_dir = plugin_dir_path( __FILE__ ).$template_dir;
	    
	    if( ! is_dir( $this_dir ) ) {
	        mkdir( $this_dir );
	    }
	    
	    if( ! file_exists ( $this_dir.'/'.$this->swp_template_filename() ) ) {
	        
	        $fp = fopen( $this_dir.'/'.$this->swp_template_filename(), 'w' );
	        
            fwrite( $fp, $this->swp_sample_template() );
            
            fclose( $fp );
            
	    }

	    return TRUE;
	    
	}
	
	// SAMPLE TEMPLATE CONTENTS
	private function swp_sample_template() {
	    
        return '<?php
        
        if ( ! defined( "ABSPATH" ) ) {
            exit; // Exit if accessed directly
        }
        
        ?>
        
        <section>
        
        	<div class="item-title">
        		<h4><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h4>
        	</div>
        
        
        	<?php $swp_field = get_post_meta( get_the_ID(), "alt_content", TRUE ); ?>
        	<?php if ( $swp_field ): ?>
        		<div class="item-content"><?php echo $swp_field; ?></div>
        	<?php endif ?>
        
        
        	<?php $swp_field = get_post_meta( get_the_ID(), "pic", TRUE ); ?>
        	<?php if ( $swp_field ): ?>
        		<div class="item-pic"><?php echo wp_get_attachment_image( $swp_field, $size = "medium", $icon = false ); ?></div>
        	<?php endif ?>
        
        </section>';
	    
	}

	// Sample Template filename
	public function swp_template_filename () {
		return 'sample_template.php';
	}

	// CONSTRUCT
	public function __construct() {

		if( !is_admin() ) {
			add_shortcode( 'swppostcustomloop', array( $this, 'swppostcustomloopdisplay' ) );
		}

	}

}

$swppostcustomloop = new SWPPostCustomLoop();