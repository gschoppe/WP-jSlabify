<?php if(!defined('ABSPATH')) { die(); }

if( !class_exists('WPJSlabifyOptions') ) {
	class WPJSlabifyOptions {
		private $wpjs;
		private $plugin_dir;
		private $plugin_url;

		public static function Instance() {
			static $instance = null;
			if ($instance === null) {
				$instance = new self();
			}
			return $instance;
		}

		private function __construct() {
			$this->wpjs = WPJSlabify::Instance();
			$this->plugin_dir = $wpjs->plugin_dir;
			$this->plugin_url = $wpjs->plugin_url;
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_init', array( $this, 'admin_init' ) );
		}

		public function admin_menu() {
			add_options_page(
				__( 'WP jSlabify Settings', 'wpslabify' ),
				__( 'WP jSlabify', 'wpslabify' ),
				'manage_options',
				'jslabify_theme_settings',
				array( $this, 'settings_page_callback' )
			);
		}

		public function settings_page_callback() {
			?>
			<h1><?php _e( 'WP jSlabify Settings', 'wpslabify' ); ?></h1>
			<form method="post" action="<?php echo admin_url('options.php'); ?>">
				<?php
				settings_fields( 'jslabify_theme_settings' );
				do_settings_sections( 'jslabify_theme_settings' );
				submit_button();
				?>
			</form>
			<?php
		}

		public function admin_init() {
			register_setting( 'jslabify_theme_settings', 'jslabify_default_divider', array(
				'type' => 'string',
				'default' => ''
			) );
			add_settings_section(
				'jslabify_theme_settings',
				__( 'Theme Options', 'wpslabify' ),
				array( $this, 'theme_settings_section_callback' ),
				'jslabify_theme_settings'
			);
			register_setting( 'jslabify_theme_settings', 'jslabify_default_theme', array(
				'type' => 'string',
				'default' => 'league'
			) );
			add_settings_field(
				'jslabify_default_theme',
				'Default Theme',
				array( $this, 'select_field_callback' ),
				'jslabify_theme_settings',
				'jslabify_theme_settings',
				array(
					'label_for'   => 'jslabify_default_theme',
					'value'       => get_option( 'jslabify_default_theme' ),
					'options'     => $this->list_all_themes()
				)
			);

			register_setting( 'jslabify_theme_settings', 'jslabify_default_divider', array(
				'type' => 'string',
				'default' => ''
			) );
			add_settings_field(
				'jslabify_default_divider',
				'Default Divider',
				array( $this, 'select_field_callback' ),
				'jslabify_theme_settings',
				'jslabify_theme_settings',
				array(
					'label_for'   => 'jslabify_default_divider',
					'value'       => get_option( 'jslabify_default_divider' ),
					'options'     => $this->list_all_dividers()
				)
			);

			register_setting( 'jslabify_theme_settings', 'jslabify_default_ratio', array(
				'type' => 'number',
				'default' => 1
			) );
			add_settings_field(
				'jslabify_default_ratio',
				'Default Ratio',
				array( $this, 'number_field_callback' ),
				'jslabify_theme_settings',
				'jslabify_theme_settings',
				array(
					'label_for' => 'jslabify_default_ratio',
					'value'     => get_option( 'jslabify_default_ratio' ),
					'min'       => '0.01',
					'max'       => '10',
					'step'      => '0.01'
				)
			);
		}

		public function theme_settings_section_callback() {
			?>
			<p>
				These settings control the default appearance of slabs
				on the site. if you select 'none', you may need to add
				custom styles, such as <code>line-height: 1;</code> for
				slabs to display properly
			</p>
			<?php
		}

		public function select_field_callback( $args ) {
			?>
			<select id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>">
				<?php
				foreach( $args['options'] as $value => $label ) {
					?>
					<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $args['value'], $value ); ?>><?php echo esc_attr( $label ); ?></option>
					<?php
				}
				?>
			</select>
			<?php
			if( !empty( $args['description'] ) ) {
				?>
				<p><?php echo $args['description']; ?></p>
				<?php
			}
		}

		public function number_field_callback( $args ) {
			echo '<input type="number"';
			echo ' id="' . esc_attr( $args['label_for'] ) . '"';
			echo ' name="' . esc_attr( $args['label_for'] ) . '"';
			echo ' value="' . esc_attr( $args['value'] ) . '"';
			echo ' min="' . esc_attr( $args['min'] ) . '"';
			echo ' max="' . esc_attr( $args['max'] ) . '"';
			echo ' step="' . esc_attr( $args['step'] ) . '"';

			echo '>';
			if( !empty( $args['description'] ) ) {
				?>
				<p><?php echo $args['description']; ?></p>
				<?php
			}
		}

		private function list_all_themes() {
			$theme_list = $this->wpjs->enumerate_themes();
			$themes = array(
				'' => 'None'
			);
			foreach( $theme_list as $slug => $atts ) {
				$themes[ $slug ] = $atts['Theme Name'] . ' (' . $slug . ')';
			}
			return $themes;
		}

		private function list_all_dividers() {
			return array(
				''                  => 'Theme Default',
				'line'              => 'Line (line)',
				'ellipsis'          => "Ellipsis (ellipsis)",
				'arrow-left'        => "Contemporary Arrow Left (arrow-left)",
				'arrow-right'       => "Contemporary Arrow Right (arrow-right)",
				'arrow-both'        => "Contemporary Arrow Both (arrow-both)",
				'west-arrow-left'   => "Western Arrow Left (west-arrow-left)",
				'west-arrow-right'  => "Western Arrow Right (west-arrow-right)",
				'thick-arrow-left'  => "Thick Arrow Left (thick-arrow-left)",
				'thick-arrow-right' => "Thick Arrow Right (thick-arrow-right)",
				'banner'            => "Banner (banner)",
				'banner-reverse'    => "Banner Reverse (banner-reverse)",
			);
		}

	}
	WPJSlabifyOptions::Instance();
}
