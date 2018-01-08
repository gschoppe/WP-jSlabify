<?php if(!defined('ABSPATH')) { die(); }
/**
 * Plugin Name: jSlabify Slabbed Text Shortcode
 * Plugin URI: https://gschoppe.com/
 * Description: Create slabbed typography with the jSlabify library in WordPress
 * Author: Greg Schoppe
 * Author URI: https://gschoppe.com
 * Version: 1.0.0
 **/

if( !class_exists('WPJSlabify') ) {
	class WPJSlabify {
		public $version = '1.0.0';
		public $plugin_dir;
		public $plugin_url;

		private $in_slab = false;
		private $do_js   = false;

		public static function Instance() {
			static $instance = null;
			if ($instance === null) {
				$instance = new self();
			}
			return $instance;
		}

		private function __construct() {
			$this->plugin_dir = plugin_dir_path( __FILE__ );
			$this->plugin_url = plugin_dir_url( __FILE__ );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_resources' ) );
			add_action( 'wp_footer', array( $this, 'maybe_do_js' ) );
			add_shortcode( 'slab', array( $this, 'shortcode_slab' ) );
			add_shortcode( 'slabline', array( $this, 'shortcode_slabline' ) );
		}

		public function enqueue_resources() {
			wp_enqueue_script( 'jslabify', $this->plugin_url . '/lib/jSlabify.min.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_style( 'jslabify', $this->plugin_url . '/lib/jSlabify.min.css', array(), $this->version );
		}

		public function shortcode_slab( $atts, $content = '' ) {
			$this->do_js = true;
			$atts = shortcode_atts( array(
				'element' => 'div',
				'ratio'   => '1',
				'hcenter' => 'false',
				'vcenter' => 'false',
				'force'   => 'false',
				'id'      => '',
				'class'   => '',
				'style'   => '',
				'href'    => '',
				'title'   => '',
				'target'  => '',

			), $atts, 'slab' );

			// add the standard class
			$atts['class'] = trim( 'jslabify ' . $atts['class'] );

			// grab the element name
			$element = $atts['element'];
			unset( $atts['element'] );

			// prefix the data values
			$data_atts = array( 'ratio', 'hcenter', 'vcenter', 'force' );
			foreach( $data_atts as $att ) {
				if( isset( $atts[$att] ) ) {
					$atts['data-'.$att] = $atts[$att];
					unset( $atts[$att] );
				}
			}

			// get rid of any empty elements
			$atts = array_filter( $atts );

			// get rid of any unused link attributes
			if( $element != 'a' ) {
				unset( $atts['href'] );
				unset( $atts['title'] );
				unset( $atts['target'] );
			}

			// make the contents
			$content = strip_tags ( $content, 'a' );
			$content = str_replace( '\n', ' ', $content );
			$this->in_slab = true;
			$content = do_shortcode( $content );
			$this->in_slab = false;

			// build the tag
			$output  = '<' . esc_attr( $element );
			foreach( $atts as $att => $val ) {
				$output .= ' ' . $att . '="' . esc_attr( $val ) . '"';
			}
			$output .= '>';
			$output .= $content;
			$output .= '</' . esc_attr( $element ) . '>';

			return $output;
		}

		public function shortcode_slabline( $atts, $content = '' ) {
			$atts = shortcode_atts( array(
				'id'      => '',
				'class'   => '',
				'style'   => '',
			), $atts, 'slabline' );

			// add the standard class
			$atts['class'] = trim( 'slabbedtext ' . $atts['class'] );

			// get rid of any empty elements
			$atts = array_filter( $atts );

			// build the tag
			$output  = '<span';
			foreach( $atts as $att => $val ) {
				$output .= ' ' . $att . '="' . esc_attr( $val ) . '"';
			}
			$output .= '>';
			$output .= $content;
			$output .= '</span>';

			return $output;
		}

		public function maybe_do_js() {
			if( $this->do_js ) {
				$default_options = apply_filters( 'jslabify-default-options', array() );
				?>
				<script>
				jQuery(function($) {
					var defaults = <?php echo json_encode( $default_options ); ?>;
					$(window).on('load', function() {
						$('.jslabify').each(function() {
							var $this    = $(this),
									settings = {
										slabRatio       : $this.data('ratio'),
										hCenter         : $this.data('hcenter'),
										vCenter         : $this.data('vcenter'),
										constrainHeight : $this.data('force')
									};
							settings = $.extend({}, defaults, settings);
							$this.jSlabify(settings);
						});
					});
				});
				</script>
				<?php
			}
		}
	}
	WPJSlabify::Instance();
}
