<?php
/**
 * Storefront Pro Sales Pop Admin class
 */
class Storefront_Pro_Sales_Pop_Admin {

	/** @var Storefront_Pro_Sales_Pop_Admin Instance */
	private static $_instance = null;

	/* @var string $token Plugin token */
	public $token;

	/* @var string $url Plugin root dir url */
	public $url;

	/* @var string $path Plugin root dir path */
	public $path;

	/* @var string $version Plugin version */
	public $version;

	/**
	 * Main Storefront Pro Sales Pop Instance
	 * Ensures only one instance of Storefront_Extension_Boilerplate is loaded or can be loaded.
	 * @return Storefront_Pro_Sales_Pop_Admin instance
	 * @since 	1.0.0
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Constructor function.
	 * @access  private
	 * @since 	1.0.0
	 */
	private function __construct() {
		$this->token   =   Storefront_Pro_Sales_Pop::$token;
		$this->url     =   Storefront_Pro_Sales_Pop::$url;
		$this->path    =   Storefront_Pro_Sales_Pop::$path;
		$this->version =   Storefront_Pro_Sales_Pop::$version;
	} // End __construct()

	/**
	 * Adds row settings panel tab
	 * @param array $tabs The array of tabs
	 * @return array Tabs
	 * @filter pootlepb_row_settings_tabs
	 * @since 	1.0.0
	 */
	public function row_settings_tabs( $tabs ) {
		$tabs[ $this->token ] = array(
			'label' => 'Sample Tab',
			'priority' => 5,
		);
		return $tabs;
	}

	private function field( $label, $type, $args = [] ) {
		$id = preg_replace( '/[^a-z0-9]/', '-', strtolower( $label ) );
		return $args + [
			'id'      => "sfpsp-$id",
			'label'   => $label,
			'section' => 'Sales Pop',
			'type'    => $type,
		];
	}

	/** AJAX action to show sales pop up data */
	public function fields( $f ) {
		$fields = [

			// Popup properties
			$this->field( 'Position', 'select', [
				'choices' => [
					'' => 'Left',
					'left:50%;-webkit-transform:translateX(-50%);transform:translateX(-50%);' => 'Center',
					'right:25px;left:auto;' => 'Right',
				]
			] ),
			$this->field( 'Layout', 'select', [
				'choices' => [
					'' => 'Image Left',
					'row-reverse' => 'Image right',
					'column' => 'Image top',
					'column-reverse' => 'Image Bottom',
				]
			] ),
			$this->field( 'Image Size', 'range', [ 'input_attrs' => [ 'min' => 100, 'max' => 300, 'step' => 20 ] ] ),
			$this->field( 'Background color', 'alpha-color' ),
			$this->field( 'Border width', 'number', [ 'input_attrs' => [ 'max' => 10 ] ] ),
			$this->field( 'Border color', 'color' ),

			// Font
			$this->field( 'Text Font', 'font' ),
			$this->field( 'Text Font color', 'color' ),
			$this->field( 'Text Font size', 'range', [ 'input_attrs' => [ 'min' => 7, 'max' => 25 ] ] ),

			// Title font
			$this->field( 'Title Font', 'font' ),
			$this->field( 'Title Font color', 'color' ),

			$this->field( 'Show Sales Pop', 'select', [
				'choices' => [
					'' => 'on WooCommerce Pages',
					'site-wide' => 'Site Wide',
				]
			] ),
		];
		return array_merge( $fields, $f );
	}

	/**
	 * Adds front end stylesheet and js
	 * @action wp_enqueue_scripts
	 */
	public function admin_enqueue() {
	}
}