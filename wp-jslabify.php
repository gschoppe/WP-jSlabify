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
		private $in_line = false;
		private $do_js   = false;
		private $themes  = array();

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
			add_filter( 'extra_wp_jslabify_custom_theme_headers', array( $this, 'wp_jslabify_custom_theme_headers' ) );
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_resources' ) );
			add_action( 'wp_footer', array( $this, 'maybe_do_js' ) );
			add_shortcode( 'slab', array( $this, 'shortcode_slab' ) );
			add_shortcode( 'slabline', array( $this, 'shortcode_slabline' ) );
			add_shortcode( 'slabbreak', array( $this, 'shortcode_slabbreak' ) );
		}

		public function enqueue_resources() {
			wp_enqueue_script( 'jslabify', $this->plugin_url . '/lib/jSlabify.min.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_style( 'jslabify', $this->plugin_url . '/lib/jSlabify.min.css', array(), $this->version );
		}

		public function shortcode_slab( $atts, $content = '' ) {
			$this->do_js = true;
			$atts = shortcode_atts( array(
				'element' => 'div',
				'ratio'   => get_option( 'jslabify_default_ratio' ),
				'hcenter' => 'false',
				'vcenter' => 'false',
				'force'   => 'false',
				'id'      => '',
				'class'   => '',
				'style'   => '',
				'href'    => '',
				'title'   => '',
				'target'  => '',
				'theme'   => get_option( 'jslabify_default_theme' )
			), $atts, 'slab' );

			if( !$atts['ratio'] ) {
				$atts['ratio'] = '1';
			}

			// handle theme
			if( $atts['theme'] ) {
				$atts['class'] = trim( 'theme-' . sanitize_title( $atts['theme'] ) . ' ' . $atts['class'] );
				$this->load_theme( $atts['theme'] );
				unset( $atts['theme'] );
			}

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

			$this->in_line = true;
			$content = do_shortcode( $content );
			$this->in_line = false;

			if( !$this->in_slab ) {
				return $content;
			}

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

		public function shortcode_slabbreak( $atts, $content = '' ) {
			$atts = shortcode_atts( array(
				'id'      => '',
				'class'   => '',
				'style'   => '',
				'type'    => get_option( 'jslabify_default_divider' )
			), $atts, 'slabline' );

			if( !$this->in_slab || $this->in_line ) {
				return $content;
			}

			if( !$atts['type'] ) {
				$atts['type'] = 'default';
			}

			$atts['class'] = trim( 'slabbreak-' . sanitize_title( $atts['type'] ) . ' ' . $atts['class'] );
			unset( $atts['type'] );

			// add the standard class
			$atts['class'] = trim( 'slabbedtext ' . $atts['class'] );

			// get rid of any empty elements
			$atts = array_filter( $atts );

			// build the tag
			$output  = '<span';
			foreach( $atts as $att => $val ) {
				$output .= ' ' . $att . '="' . esc_attr( $val ) . '"';
			}
			$output .= '></span>';

			return $output;
		}

		public function maybe_do_js() {
			if( $this->do_js ) {
				$default_options = apply_filters( 'jslabify-default-options', array(
					'postTweak' => false
				) );
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

		public function load_theme( $theme_slug ) {
			if( isset( $this->themes[ $theme_slug ] ) ) {
				$theme = $this->themes[ $theme_slug ];
				wp_enqueue_style( 'wp-jslabify-' . $theme_slug, $theme['URL'], array(), $theme['Version'] );
			}
		}

		public function wp_jslabify_custom_theme_headers( $headers ) {
			$headers[] = "Theme Name";
			$headers[] = "Theme URI";
			$headers[] = "Slug";
			$headers[] = "Description";
			$headers[] = "Icon";
			$headers[] = "Version";
			$headers[] = "Author";
			$headers[] = "Author URI";
			$headers[] = "License";
			$headers[] = "License URI";
			return $headers;
		}

		public function init() {
			$this->enumerate_themes();
			require_once( $this->plugin_dir . '/includes/admin-options.php' );
		}

		public function enumerate_themes() {
			if( !$this->themes ) {
				$template_dir = get_template_directory();
				$stylesheet_dir = get_stylesheet_directory();
				$theme_directories = array(
					$this->plugin_dir . 'themes' => $this->plugin_url . 'themes'
				);
				$additional_dirs = apply_filters( 'jslabify-theme-folders', array() );
				$theme_directories = array_merge( $theme_directories, $additional_dirs );
				$theme_directories[ $template_dir . '/wp-jslabify' ] = get_template_directory_uri() . '/wp-jslabify';
				if( $template_dir != $stylesheet_dir ) {
					$theme_directories[ $stylesheet_dir . '/wp-jslabify' ] = get_stylesheet_directory_uri() . '/wp-jslabify';
				}
				$themes = array();
				foreach( $theme_directories as $path=>$url ) {
					$theme_set = $this->scan_folder_for_themes( $path, $url );
					$themes = array_merge( $themes, $theme_set );
				}
				$this->themes = $themes;
			}
			return $this->themes;
		}

		private function scan_folder_for_themes( $path, $url ) {
			$themes = array();
			if( is_dir( $path ) ) {
				$files = $this->rglob( $path, '*.css' );
				foreach( $files as $file ) {
					$file_url = str_replace( $path, $url, $file );
					$header = get_file_data( $file, array(), "wp_jslabify_custom_theme" );
					if( isset( $header['Theme Name'] ) && $header['Theme Name'] ) {
						if( isset( $header['Slug'] ) && $header['Slug'] ) {
							$slug = sanitize_title( $header['Slug'] );
						} else {
							$slug = sanitize_title( $header['Theme Name'] );
						}
						$slug = str_replace( '-', '_', $slug );
						$header['File'] = $file;
						$header['URL'] = $file_url;
						$themes[ $slug ] = $header;
					}
				}
			}
			return $themes;
		}

		private function rglob($dir, $pattern, $flags = 0) {
			$files = glob( $dir . '/' . $pattern, $flags );
			foreach( glob( $dir.'/*', GLOB_ONLYDIR|GLOB_NOSORT ) as $dir ) {
				$files = array_merge( $files, $this->rglob( $dir, $pattern, $flags ) );
			}
			return $files;
		}
	}
	WPJSlabify::Instance();
}
