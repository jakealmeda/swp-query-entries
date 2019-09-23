<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class SWPWPQueryPosts {
	
	public function swp_query_archive_posts( $post_type, $page_id, $num_of_posts, $tax_name, $tax_term, $paged, $orderbymeta, $orderby, $order ) {

		// sort
		if( is_null( $orderby ) ) {
			$orderby = 'date';
			$order = 'DESC';
		}

		// pagination
		/*if( $paged ) {
			$paged = $paged;
		} else {
			$paged = get_query_var( 'paged' );
		}*/

		// check posts per page value
		if( is_numeric( $num_of_posts ) ) {
			$num_of_posts = $num_of_posts;
		} else {
			$num_of_posts = -1;
		}

		if( $tax_name ) {
			$condition = TRUE;
		} else {
			$condition = FALSE;
		}

		$args = array(
			'post_type' 		=> $post_type,
			'page_id'			=> $page_id,
			'post_status'    	=> 'publish',
			'posts_per_page' 	=> $num_of_posts,
			//'paged' 			=> $paged,
			'meta_key'			=> $orderbymeta,
			'orderby'			=> $orderby,
			'order'				=> $order,
		) + ( $condition ? array(
			'tax_query' 		=> array(
				array(
					'taxonomy' 		=> $tax_name,
					'field'    		=> 'slug',
					'terms'    		=> $tax_term,
				),
		)) : array());

		return new WP_Query( $args );

	}

}