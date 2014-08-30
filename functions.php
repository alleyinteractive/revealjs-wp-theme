<?php

define( 'REVEAL_VERSION', '2.6.2' );
define( 'REVEAL_PARENT_THEME_URI', get_template_directory_uri() );

function reveal_setup_theme() {
	add_action( 'init', 'reveal_post_types' );
	add_action( 'init', 'reveal_remove_default_objects' );
	add_action( 'fm_post_slide', 'reveal_slides' );
	add_action( 'admin_menu', 'reveal_admin_menu' );
	add_filter( 'wp_title', 'reveal_wp_title', 10, 2 );

	add_filter( 'show_admin_bar', '__return_false' );
	add_action( 'after_switch_theme', 'reveal_flush_rewrites' );
	add_action( 'switch_theme', 'reveal_flush_rewrites' );

	if ( ! is_admin() ) {
		add_action( 'wp_footer', 'reveal_initialize_script', 20 );
		add_action( 'pre_get_posts', 'reveal_homepage_slides' );

		wp_enqueue_style( 'reveal-core-css', REVEAL_PARENT_THEME_URI . '/css/reveal.min.css', array(), REVEAL_VERSION );
		wp_enqueue_style( 'reveal-theme-css', REVEAL_PARENT_THEME_URI . '/css/theme/default.css', array(), REVEAL_VERSION );
		wp_enqueue_style( 'reveal-zenburn-css', REVEAL_PARENT_THEME_URI . '/lib/css/zenburn.css', array(), REVEAL_VERSION );

		wp_enqueue_script( 'reveal-head-js', REVEAL_PARENT_THEME_URI . '/lib/js/head.min.js', array(), REVEAL_VERSION, true );
		wp_enqueue_script( 'reveal-core-js', REVEAL_PARENT_THEME_URI . '/js/reveal.min.js', array(), REVEAL_VERSION, true );
	}
}
add_action( 'after_setup_theme', 'reveal_setup_theme' );

function reveal_flush_rewrites() {
	delete_option( 'rewrite_rules' );
}

function reveal_initialize_script() {
	?>
	<script>
		// Full list of configuration options available here:
		// https://github.com/hakimel/reveal.js#configuration
		var reveal_config = {
			controls: true,
			progress: true,
			history: true,
			center: true,

			theme: Reveal.getQueryHash().theme, // available themes are in /css/theme
			transition: Reveal.getQueryHash().transition || 'default', // default/cube/page/concave/zoom/linear/fade/none

			// Optional libraries used to extend on reveal.js
			dependencies: [
				<?php
				echo implode( ",\n", apply_filters( 'reveal_default_dependencies', array(
					'classList' => "{ src: '" . get_template_directory_uri() . "/lib/js/classList.js', condition: function() { return !document.body.classList; } }",
					'marked'    => "{ src: '" . get_template_directory_uri() . "/plugin/markdown/marked.js', condition: function() { return !!document.querySelector( '[data-markdown]' ); } }",
					'markdown'  => "{ src: '" . get_template_directory_uri() . "/plugin/markdown/markdown.js', condition: function() { return !!document.querySelector( '[data-markdown]' ); } }",
					'highlight' => "{ src: '" . get_template_directory_uri() . "/plugin/highlight/highlight.js', async: true, callback: function() { hljs.initHighlightingOnLoad(); } }",
					'zoom'      => "{ src: '" . get_template_directory_uri() . "/plugin/zoom-js/zoom.js', async: true, condition: function() { return !!document.body.classList; } }",
					'notes'     => "{ src: '" . get_template_directory_uri() . "/plugin/notes/notes.js', async: true, condition: function() { return !!document.body.classList; } }",
				) ) );
				?>
			]
		};
		<?php do_action( 'reveal_initialize' ) ?>
		Reveal.initialize( reveal_config );
	</script>
	<?php
}

/**
 * Filters wp_title to add site name.
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
function reveal_wp_title( $title, $sep ) {
	return $title . get_bloginfo( 'name', 'display' );
}


/**
 * Boilerplate Reveal.js Theme stuff
 */
function reveal_post_types() {
	register_post_type( 'slide', array(
		'public'   => true,
		'supports' => array( 'title', 'page-attributes' ),
		'menu_position' => 5,
		'menu_icon' => 'dashicons-format-image',
		'labels'   => array(
			'name'               => __( 'Slides', 'reveal' ),
			'singular_name'      => __( 'Slide', 'reveal' ),
			'add_new'            => __( 'Add New Slide', 'reveal' ),
			'add_new_item'       => __( 'Add New Slide', 'reveal' ),
			'edit_item'          => __( 'Edit Slide', 'reveal' ),
			'new_item'           => __( 'New Slide', 'reveal' ),
			'view_item'          => __( 'View Slide', 'reveal' ),
			'search_items'       => __( 'Search Slides', 'reveal' ),
			'not_found'          => __( 'No Slides found', 'reveal' ),
			'not_found_in_trash' => __( 'No Slides found in Trash', 'reveal' ),
			'parent_item_colon'  => __( 'Parent Slide:', 'reveal' ),
			'menu_name'          => __( 'Slides', 'reveal' ),
		),
	) );
}

function reveal_remove_default_objects() {
	global $wp_post_types, $wp_taxonomies;
	// Remove core post types and taxonomies
	if ( apply_filters( 'reveal_remove_posts', true ) && isset( $wp_post_types['post'] ) ) {
		unset( $wp_post_types['post'] );
	}

	if ( apply_filters( 'reveal_remove_categories', true ) && isset( $wp_taxonomies['category'] ) ) {
		unset( $wp_taxonomies['category'] );
	}

	if ( apply_filters( 'reveal_remove_tags', true ) && isset( $wp_taxonomies['post_tag'] ) ) {
		unset( $wp_taxonomies['post_tag'] );
	}

	if ( apply_filters( 'reveal_remove_link_categories', true ) && isset( $wp_taxonomies['link_category'] ) ) {
		unset( $wp_taxonomies['link_category'] );
	}

	if ( apply_filters( 'reveal_remove_post_formats', true ) && isset( $wp_taxonomies['post_format'] ) ) {
		unset( $wp_taxonomies['post_format'] );
	}
}

function reveal_admin_menu() {
	if ( apply_filters( 'reveal_remove_posts', true ) ) {
		remove_menu_page( 'edit.php' );
	} else {
		if ( apply_filters( 'reveal_remove_categories', true ) ) {
			remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=category' );
		}
		if ( apply_filters( 'reveal_remove_tags', true ) ) {
			remove_submenu_page( 'edit.php', 'edit-tags.php?taxonomy=post_tag' );
		}
	}
	if ( apply_filters( 'reveal_remove_comments', true ) ) {
		remove_menu_page( 'edit-comments.php' );
	}
}

function reveal_slides() {
	$attr_group = new Fieldmanager_Group( array(
		'label'          => __( 'Slide HTML Attributes', 'reveal' ),
		'limit'          => 0,
		'extra_elements' => 0,
		'add_more_label' => __( 'Add attribute', 'reveal' ),
		'label_macro'    => array( __( '%s', 'reveal' ), 'key' ),
		'children'       => array(
			'key' => new Fieldmanager_TextField( __( 'Attribute Name', 'reveal' ) ),
			'value' => new Fieldmanager_TextField( __( 'Attribute Value', 'reveal' ) ),
		)
	) );
	$bg_group = new Fieldmanager_Media( array(
		'label' => __( 'Background', 'reveal' ),
		'preview_size'       => 'medium',
		'button_label'       => __( 'Add Image', 'reveal' ),
		'modal_button_label' => __( 'Select Image', 'reveal' ),
		'modal_title'        => __( 'Slide Background', 'reveal' ),
	) );


	$fm = new Fieldmanager_Group( array(
		'name'           => 'slides',
		'label'          => __( 'Slide', 'reveal' ),
		'limit'          => 0,
		'extra_elements' => 0,
		'add_more_label' => __( 'Add vertical slide', 'reveal' ),
		'label_macro'    => array( __( '%s', 'reveal' ), 'title' ),
		'sortable'       => true,
		'collapsible'    => true,
		'children'       => array(
			'title' => new Fieldmanager_TextField( __( 'Title', 'reveal' ) ),
			'content' => new Fieldmanager_RichTextArea( __( 'Content', 'reveal' ) ),
			'background' => $bg_group,
			'attr' => $attr_group,
			'notes' => new Fieldmanager_RichTextArea( __( 'Notes', 'reveal' ) ),
		)
	) );
	$fm->add_meta_box( __( 'Vertical Slides', 'reveal' ), 'slide' );

	$fm = new Fieldmanager_Group( array(
		'name'           => 'wrapper',
		'description'    => __( 'These options may be used if you have more than one vertical slide', 'reveal' ),
		'children'       => array(
			'background' => $bg_group,
			'attr' => $attr_group,
		)
	) );
	$fm->add_meta_box( __( 'Wrapper Slide Options', 'reveal' ), 'slide' );

	add_filter( 'tiny_mce_before_init', 'reveal_tiny_mce_before_init' );
}

function reveal_tiny_mce_before_init( $options ) {
	$options['external_plugins']['code'] = get_stylesheet_directory_uri() . '/js/tinymce.code.js';
	$options['toolbar2'] = 'styleselect,' . $options['toolbar2'];
	// $options['theme_advanced_styles'] = 'Fragment=fragment';
	$options['style_formats'] = array(
		array( 'title' => 'Fragment', 'selector' => 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', 'classes' => 'fragment' )
	);
	return $options;
}

function reveal_homepage_slides( &$query ) {
	if ( $query->is_main_query() && $query->is_home() ) {
		$query->set( 'post_type', 'slide' );
		$query->set( 'posts_per_page', -1 );
		$query->set( 'paged', 1 );
		$query->set( 'orderby', 'menu_order date' );
		$query->set( 'order', 'ASC' );
	}
}


/**
 * Template Tags
 */

function reveal_get_slides() {
	$slides = get_post_meta( get_the_ID(), 'slides', true );
	if ( empty( $slides ) ) {
		return array();
	}
	return (array) $slides;
}

function reveal_section_attr( $slide ) {
	if ( empty( $slide ) ) {
		return '';
	}

	if ( ! empty( $slide['background'] ) && $url = wp_get_attachment_url( $slide['background'] ) ) {
		$slide['attr'][] = array( 'key' => 'data-background', 'value' => $url );
	}
	if ( ! empty( $slide['attr'] ) ) {
		$return = array( '' );
		foreach ( $slide['attr'] as $attr ) {
			$return[] = sprintf( '%s="%s"', sanitize_key( $attr['key'] ), esc_attr( $attr['value'] ) );
		}
		return implode( ' ', $return );
	}
	return '';
}

/**
 * Process a block of content as the_content() does.
 *
 * @param  string $content Content.
 * @return string
 */
function reveal_process_html_field( $content ) {
	$content = apply_filters( 'the_content', $content );
	return str_replace( ']]>', ']]&gt;', $content );
}
