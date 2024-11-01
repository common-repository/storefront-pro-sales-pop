<?php
/*
Plugin Name: Storefront Pro Sales Pop
Plugin URI: http://pootlepress.com/storefront-pro-sales-pop
Description: Shows fully customizable sales pop showing real time purchases, build trust and boost sales!
Author: pootlepress
Version: 1.1.0
Author URI: http://pootlepress.com/
@developer shramee <shramee.srivastav@gmail.com>
*/

/** Plugin admin class */
require 'inc/class-admin.php';
/** Plugin public class */
require 'inc/class-public.php';

/**
 * Storefront Pro Sales Pop main class
 * @static string $token Plugin token
 * @static string $file Plugin __FILE__
 * @static string $url Plugin root dir url
 * @static string $path Plugin root dir path
 * @static string $version Plugin version
 */
class Storefront_Pro_Sales_Pop{

	/** @var Storefront_Pro_Sales_Pop Instance */
	private static $_instance = null;

	/** @var string Token */
	public static $token;

	/** @var string Version */
	public static $version;

	/** @var string Plugin main __FILE__ */
	public static $file;

	/** @var string Plugin directory url */
	public static $url;

	/** @var string Plugin directory path */
	public static $path;

	/** @var Storefront_Pro_Sales_Pop_Admin Instance */
	public $admin;

	/** @var Storefront_Pro_Sales_Pop_Public Instance */
	public $public;

	/**
	 * Return class instance
	 * @return Storefront_Pro_Sales_Pop instance
	 */
	public static function instance( $file = __FILE__ ) {
		if ( null == self::$_instance ) {
			self::$_instance = new self( $file );
		}
		return self::$_instance;
	}

	/**
	 * Constructor function.
	 * @param string $file __FILE__ of the main plugin
	 * @access  private
	 * @since   1.0.0
	 */
	private function __construct( $file ) {

		self::$token   = 'sfp-sales-pop';
		self::$file    = $file;
		self::$url     = plugin_dir_url( $file );
		self::$path    = plugin_dir_path( $file );
		self::$version = '1.1.0';

		add_action( 'after_setup_theme', array( $this, 'setup' ) );
	}

	/**
	 * Initiates the plugin
	 * @action init
	 */
	public function setup() {
		if ( class_exists( 'Storefront_Pro' ) && class_exists( 'WooCommerce' ) ) {
			$theme = wp_get_theme();
			if ( $theme->name == 'Storefront' || $theme->parent_theme == 'Storefront' ) {
				$this->_admin(); //Initiate admin
				$this->_public(); //Initiate public
			}
		}
	}

	/**
	 * Initiates admin class and adds admin hooks
	 */
	private function _admin() {
		//Instantiating admin class
		$this->admin = Storefront_Pro_Sales_Pop_Admin::instance();

		//Enqueue admin end JS and CSS
		add_action( 'wp_ajax_sfp_sales_pop_data', array( $this->admin, 'ajax_sfp_sales_pop_data' ) );
		add_action( 'wp_ajax_nopriv_sfp_sales_pop_data', array( $this->admin, 'ajax_sfp_sales_pop_data' ) );

		add_filter( 'storefront_pro_fields', array( $this->admin, 'fields' ) );
//		add_filter( 'storefront-pro-section-sales-pop-filter-args', array( $this->admin, 'section' ) );
	}

	/**
	 * Initiates public class and adds public hooks
	 */
	private function _public() {
		//Instantiating public class
		$this->public = Storefront_Pro_Sales_Pop_Public::instance();

		//Enqueue front end JS and CSS
		add_action( 'wp_enqueue_scripts',	array( $this->public, 'enqueue' ) );
		add_action( 'wp_footer',	array( $this->public, 'sales_pop' ) );

	}
}

/** Intantiating main plugin class */
Storefront_Pro_Sales_Pop::instance( __FILE__ );
