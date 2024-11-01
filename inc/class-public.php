<?php

/**
 * Storefront Pro Sales Pop public class
 */
class Storefront_Pro_Sales_Pop_Public{

	/** @var Storefront_Pro_Sales_Pop_Public Instance */
	private static $_instance = null;

	/* @var string $token Plugin token */
	public $token;

	/* @var string $url Plugin root dir url */
	public $url;

	/* @var string $path Plugin root dir path */
	public $path;

	/* @var string $version Plugin version */
	public $version;
	private $_show_popup = false;

	/**
	 * Storefront Pro Sales Pop public class instance
	 * @return Storefront_Pro_Sales_Pop_Public instance
	 */
	public static function instance() {
		if ( null == self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor function.
	 * @access  private
	 * @since   1.0.0
	 */
	private function __construct() {
		$this->token   =   Storefront_Pro_Sales_Pop::$token;
		$this->url     =   Storefront_Pro_Sales_Pop::$url;
		$this->path    =   Storefront_Pro_Sales_Pop::$path;
		$this->version =   Storefront_Pro_Sales_Pop::$version;
	}

	/**
	 * Gets recently ordered items
	 * @return array
	 */
	public function get_ordered_items() {
		$status  = get_theme_mod( 'sfpsp_post_status', 'wc-completed, wc-processing, publish' );
		$limit   = get_theme_mod( 'sfpsp_posts_per_page', 5 );
		$orderBy = get_theme_mod( 'sfpsp_order', 'DESC' );

		$args   = [
			'post_type'      => 'shop_order',
			'post_status'    => $status,
			'posts_per_page' => $limit,
			'order'          => $orderBy
		];

		$ordered_items = [];

		$orders = get_posts( $args );

//		print_r( $orders ); return;

		foreach ( $orders as $orderPost ) {
			/** @var WP_Post $orderPost */
			$order = new WC_Order( $orderPost->ID );

			$items = $order->get_items();
			foreach ( $items as $item ) {
				/** @var WC_Order_Item $item */
				if ( $item['product_id'] ) {
					$mins_passed = floor( ( time() - get_gmt_from_date( $orderPost->post_date, 'U' ) ) / 60 );
					$id = $item['product_id'];
					$ordered_items[] = [
						'id'      => $id,
						'country' => $order->get_billing_country(),
						'city'    => $order->get_billing_city(),
						'time'    => sprintf( _n( '%s minute ago', '%s minutes ago', $mins_passed, $this->token ), $mins_passed ),
						'title'   => $item->get_name(),
						'img'     => get_the_post_thumbnail( $id ),
						'link'		=> get_the_permalink( $id ),
					];
				}
			}
		}

//		print_awesome_r( $ordered_items );
		return $ordered_items;
	}

	/**
	 * Shows sales pop
	 * @action wp_footer
	 */
	public function sales_pop() {
		if ( ! $this->_show_popup ) return;
		?>
		<div id="sfp-sales-pop" style="display:none;">
			<div class='close fa fa-times'></div>
			<aside>
				<div id="sfpsp-img"></div>
			</aside>
			<section>
				<div class="sfpsp-purchased"><?php printf( __( 'Someone in %s purchased' ), '<span id="sfpsp-location"></span>' ) ?></div>
				<div id="sfpsp-title"></div>
				<div id="sfpsp-time"></div>
			</section>
		</div>
		<?php
	}

	/**
	 * Adds front end stylesheet and js
	 * @action wp_enqueue_scripts
	 */
	public function popup_styles() {
		$layout = get_theme_mod( 'storefront-pro-sfpsp-layout' );
		$position = get_theme_mod( 'storefront-pro-sfpsp-position' );
		$img_size = get_theme_mod( 'storefront-pro-sfpsp-image-size' );
		$background_color = get_theme_mod( 'storefront-pro-sfpsp-background-color' );
		$border_width = get_theme_mod( 'storefront-pro-sfpsp-border-width' );
		$border_color = get_theme_mod( 'storefront-pro-sfpsp-border-color' );
		$font = get_theme_mod( 'storefront-pro-sfpsp-text-font' );
		$color = get_theme_mod( 'storefront-pro-sfpsp-text-font-color' );
		$font_size = get_theme_mod( 'storefront-pro-sfpsp-text-font-size' );
		$title_font = get_theme_mod( 'storefront-pro-sfpsp-title-font' );
		$title_color = get_theme_mod( 'storefront-pro-sfpsp-title-font-color' );

		$css = <<<CSS
		#sfp-sales-pop {
			background-color: $background_color;
			border-width: {$border_width}px;
			border-color: $border_color;
			font-family: $font;
			color: $color;
			font-size: {$font_size}px;
			flex-direction: $layout;
			$position
		}
		#sfpsp-img img {
			width: {$img_size}px;
		}
		#sfpsp-title {
			color: $title_color;
			font-family: {$title_font};
		}
CSS;
		if ( false !== strpos( $layout, 'column' ) ) {
			$css .= "#sfp-sales-pop{width:{$img_size}px;}";
		}
		return $css;
	}

	/**
	 * Adds front end stylesheet and js
	 * @action wp_enqueue_scripts
	 */
	public function enqueue() {
		if ( is_cart() || is_checkout() ) return;

		if ( get_theme_mod( 'storefront-pro-sfpsp-show-sales-pop' ) || is_product() || is_product_taxonomy() || is_shop() ) {

			$token = $this->token;
			$url = $this->url;
			$order_data = $this->get_ordered_items();

			wp_enqueue_style( $token . '-css', $url . '/assets/front.css' );
			wp_add_inline_style( $token . '-css', $this->popup_styles() );

			wp_enqueue_script( $token . '-js', $url . '/assets/front.min.js', array( 'jquery' ) );
			wp_localize_script( $token . '-js', 'sfpspOrderedItems', $order_data );
			wp_localize_script( $token . '-js', 'sfpspSettings', [
				'isPreview' => is_customize_preview(),
				'' => '',
			] );
			$this->_show_popup = true;
		}
	}
}