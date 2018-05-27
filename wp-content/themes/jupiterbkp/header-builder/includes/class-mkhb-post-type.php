<?php
/**
 * Header Builder: Shortcode Render, MKHB_Post_Type class.
 *
 * For use in creating custom post type for HB.
 *
 * @package Header_Builder
 * @subpackage Elements_Generator
 * @since 6.0.0
 */

/**
 * Run hooks to create custom post types for HB.
 *
 * @since 6.0.0
 */
class MKHB_Post_Type {
	/**
	 * HB_Render constructor. Run some action to render HB.
	 *
	 * @since 6.0.0
	 */
	public function __construct() {
		// Create custom post type mkhb_header.
		add_action( 'init', array( $this, 'register_mkhb_header' ) );
	}

	/**
	 * Create mkhb_header custom post type.
	 *
	 * @since 6.0.0
	 */
	public function register_mkhb_header() {
		$labels = array(
			'name'                  => _x( 'Headers', 'Post type general name', 'mk_framework' ),
			'singular_name'         => _x( 'Header', 'Post type singular name', 'mk_framework' ),
			'menu_name'             => _x( 'Headers', 'Admin Menu text', 'mk_framework' ),
			'name_admin_bar'        => _x( 'Header', 'Add New on Toolbar', 'mk_framework' ),
			'add_new'               => __( 'Add New', 'mk_framework' ),
			'add_new_item'          => __( 'Add New Header', 'mk_framework' ),
			'new_item'              => __( 'New Header', 'mk_framework' ),
			'edit_item'             => __( 'Edit Header', 'mk_framework' ),
			'view_item'             => __( 'View Header', 'mk_framework' ),
			'all_items'             => __( 'All Headers', 'mk_framework' ),
			'search_items'          => __( 'Search Headers', 'mk_framework' ),
			'parent_item_colon'     => __( 'Parent Headers:', 'mk_framework' ),
			'not_found'             => __( 'No headers found.', 'mk_framework' ),
			'not_found_in_trash'    => __( 'No headers found in Trash.', 'mk_framework' ),
			'featured_image'        => _x( 'Header Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'mk_framework' ),
			'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'mk_framework' ),
			'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'mk_framework' ),
			'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'mk_framework' ),
			'archives'              => _x( 'Header archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'mk_framework' ),
			'insert_into_item'      => _x( 'Insert into header', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'mk_framework' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this header', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'mk_framework' ),
			'filter_items_list'     => _x( 'Filter headers list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'mk_framework' ),
			'items_list_navigation' => _x( 'Headers list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'mk_framework' ),
			'items_list'            => _x( 'Headers list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'mk_framework' ),
		);

		// This post type should not be displayed in admin menu. Temporary for development.
		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'rewrite'            => array(
				'slug' => 'mkhb_header',
			),
			'capability_type'    => 'post',
			'supports'           => array( 'title', 'editor', 'author' ),
		);

		register_post_type( 'mkhb_header', $args );
	}

}

new MKHB_Post_Type();
