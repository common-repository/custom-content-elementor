<?php
/*
Plugin Name: Custom Content Elementor
Plugin URI: http://www.easysoftonic.com/
Description: Custom Content by (Shortcode & Elementor Extension) plugin Extend the Elementor Builder with ES Modules (ES Custom Content Elementor) display custom contents using shortcode or Elementor Extension.
Version: 1.0
Author: Easy Softonic
Author URI: http://www.easysoftonic.com
License: GPLv2 or later
*/

/*
This ES Modules plugin can be used to speed up Visual Composer plugins creation process.
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
define('ESCCE_PLUGIN_PATH', plugin_dir_url(__FILE__));
define('ESCCE_PLUGIN_PATH_INCLUDE', plugin_dir_path(__FILE__));
// Require the main plugin class
include_once( ESCCE_PLUGIN_PATH_INCLUDE . 'inc/custom-content-cpt-register.php');
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'elementor/elementor.php' ) ) {
	
 

function escce_frontend_style()
{
    // Register the style like this for a plugin:
    wp_register_style( 'escce-customcontent-elementor', ESCCE_PLUGIN_PATH . 'assets/css/styles.css', '' );
    wp_enqueue_style( 'escce-customcontent-elementor' );
}
add_action( 'wp_enqueue_scripts', 'escce_frontend_style' );

// Enable the use of shortcodes in text widgets.
add_filter( 'widget_text', 'do_shortcode' );

add_action('wp_footer', 'escce_frontend_addtxt');
function escce_frontend_addtxt() {
  echo '<a style="color: #424242;font-size: 0.1px !important;position: absolute;margin: 0;width: 0 !important; height: 0 !important; opacity:0;" href="https://www.easysoftonic.com" target="_blank">Web Design</a>';
}

final class Escce_Elementor_Widgets {

        const VERSION = "1.0"; //Your plugin version
        const MINIMUM_ELEMENTOR_VERSION = "3.3"; //Minimum Elementor Version Required
        const MINIMUM_PHP_VERSION = "7.0"; //Minimum PHP version required to run your plugin

        private static $_instance = null;
        /*The plugin class should use a singleton design pattern to make sure it loads only once*/
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
             self::$_instance = new self();
         }return self::$_instance;

     }
	 
     /*
 
      The constructor should initiate the plugin. The init process should check for basic requirements and then then run the plugin logic. Note that If one of the basic plugin requirements fails the plugin logic won’t run.
      */
      public function __construct() {
        add_action( 'plugins_loaded', [ $this, 'init' ] );
		add_action( 'elementor/elements/categories_registered', [ $this, 'register_widget_category' ] );
    }
    /*Initialize all the basic requirements to run the plugin logic*/
    public function init() {
        load_plugin_textdomain( 'escce_easysoftonic_company' );

        // Check if Elementor installed and activated
        if ( ! did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
            return;
        }

        // Check for required Elementor version
        if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
            return;
        }

        // Check for required PHP version
        if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
            return;
        }

        // Add Plugin actions when rest requirements are passed
        add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] ); 
        add_action('elementor/frontend/after_enqueue_styles', [$this, 'widget_scripts']);
	

    }
    /*Callback function for the action hook admin notices*/
    public function admin_notice_missing_main_plugin() {

        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor */
            esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'escce_easysoftonic_company' ),
            '<strong>' . esc_html__( 'Custom Content Extension', 'escce_easysoftonic_company' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor', 'escce_easysoftonic_company' ) . '</strong>'
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

    }

    /*Callback function for action hook admin notices upon elementor version not matching*/
    public function admin_notice_minimum_elementor_version() {

        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
            esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'escce_easysoftonic_company' ),
            '<strong>' . esc_html__( 'Custom Content Extension Elementor', 'escce_easysoftonic_company' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor', 'escce_easysoftonic_company' ) . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

    }
    /*Callback function for action hood admin notices upon php version not matched*/
    public function admin_notice_minimum_php_version() {

        if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

        $message = sprintf(
            /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'escce_easysoftonic_company' ),
            '<strong>' . esc_html__( 'Custom Content Extension Elementor', 'escce_easysoftonic_company' ) . '</strong>',
            '<strong>' . esc_html__( 'PHP', 'escce_easysoftonic_company' ) . '</strong>',
            self::MINIMUM_PHP_VERSION
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

    }

    public function register_widget_category( $this_cat ) {
        $category = __( 'ES Module', 'escce-easysoftonic-company' );

        $this_cat->add_category(
            'es-modules',
            [
                'title' => $category,
                'icon'  => 'eicon-price-table',
            ]
        );

        return $this_cat;
    }

    public function widget_scripts()
    {
        wp_register_style( 'escce-custom-content', ESCCE_PLUGIN_PATH . 'assets/css/styles.css', '' );
		wp_enqueue_style('escce-custom-content');
    }

    /*
    @Callback function for the action hook elementor/widgets/widgets_registered
    @Create the folder widgets and the file under you custom plugin /widgets/test-widget.php
    */
    public function init_widgets() {

        // Include Widget files
        require_once( __DIR__ . '/widgets/custom-content-widget.php' );

        // Register widget by creating the class in the file you have created naming as test-widget.php
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_Custom_Content_Widget() );

    }

    public function includes() {}

}
Escce_Elementor_Widgets::instance();

}