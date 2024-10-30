<?php
// add here my functions
//Registering Custom Post Type Themes
add_action( 'init', 'register_escce', 20 );
function register_escce() {
    $labels = array(
        'name' => _x( 'Custom Contents', 'escce_custom_post', 'escce_easysoftonic_company' ),
        'singular_name' => _x( 'Custom Content', 'escce_custom_post', 'escce_easysoftonic_company' ),
        'add_new' => _x( 'Add New', 'escce_custom_post', 'escce_easysoftonic_company' ),
        'add_new_item' => _x( 'Add New Custom Content', 'escce_custom_post', 'escce_easysoftonic_company' ),
        'edit_item' => _x( 'Edit Custom Content', 'escce_custom_post', 'escce_easysoftonic_company' ),
        'new_item' => _x( 'New Custom Content', 'escce_custom_post', 'escce_easysoftonic_company' ),
        'view_item' => _x( 'View Custom Content', 'escce_custom_post', 'escce_easysoftonic_company' ),
        'search_items' => _x( 'Search Custom Contents', 'escce_custom_post', 'escce_easysoftonic_company' ),
        'not_found' => _x( 'No Custom Contents found', 'escce_custom_post', 'escce_easysoftonic_company' ),
        'not_found_in_trash' => _x( 'No Custom Contents found in Trash', 'escce_custom_post', 'escce_easysoftonic_company' ),
        'parent_item_colon' => _x( 'Parent Custom Content:', 'escce_custom_post', 'escce_easysoftonic_company' ),
        'menu_name' => _x( 'Custom Contents', 'escce_custom_post', 'escce_easysoftonic_company' ),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'description' => 'Custom Contents Elementor',
        'supports' => array( 'title', 'editor', 'author' ),
        //'taxonomies' => array( 'escce_category'),
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 20,
        'menu_icon' => plugins_url('assets/images/menuicon.png', dirname(__FILE__) ),
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => array('slug' => 'content-elementor','with_front' => FALSE), // you can rewrite url of Custom Content post
        'public' => true,
        'has_archive' => true,
        'capability_type' => 'post'
    );  
    register_post_type( 'content-elementor', $args );//max 20 charachter cannot contain capital letters and spaces
}  
// register end CPT

// register column and display shortcode their
add_filter( 'manage_content-elementor_posts_columns', 'escce_revealid_add_id_column', 5 );
add_action( 'manage_content-elementor_posts_custom_column', 'escce_revealid_id_column_content', 5, 2 );

function escce_revealid_add_id_column( $columns ) {
   $columns['post_id'] = 'Shortcode';
   return $columns;
}

function escce_revealid_id_column_content( $column, $id ) {
   if( 'post_id' == $column ) {
   echo esc_html('[es_content_elementor id="'.$id.'"]');
 }
}

/**
 * Register all shortcodes
 *
 * @return null
 */
function register_shortcodes_escce() {
    add_shortcode( 'es_content_elementor', 'escce_display_contents' );   
}
add_action( 'init', 'register_shortcodes_escce' );

// function display custom contents elementor
function escce_display_contents( $atts ) {
    ob_start();
    
    global $wp_query;
    $original_query = $wp_query;

    $atts = shortcode_atts( array(
        'id' => ''
    ), $atts );

    $loop = new WP_Query(array(
        'post_type' => array(
            'content-elementor',
            // more to come
        ),
        'post_status' => 'publish',
        'post__in' => array($atts['id'])
    ) );
 $wp_query = $loop;
    if( ! $loop->have_posts() ) {
        return false;
    }
echo '<div class="main_escce">';
    if($loop->have_posts()) {
        while($loop->have_posts()) {
            $loop->the_post(); 
echo \Elementor\Plugin::$instance->frontend->get_builder_content( $atts['id'], true );
     
 } }   
echo '</div>'; 
    wp_reset_query();
    return ob_get_clean();
}