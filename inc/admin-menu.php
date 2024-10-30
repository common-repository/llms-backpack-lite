<?php
namespace llms_bkpk;
use ReflectionClass;
class AdminMenu extends Boot {
	/**
	 * class constructor
	 */
	public function __construct() {
		if ( is_admin() ) {
			// future use if required
		}
	}
	/**
	 * Create Plugin options menu
	 */
	public static function register_options_menu_page() {
		$page_title = esc_html__( 'LearningTemplates LifterLMS BackPack', 'learningtemplates-lifterlms-backpack' );
		$menu_title = esc_html__( 'LearningTemplates BackPack', 'learningtemplates-lifterlms-backpack' );
		$capability = 'manage_options';
		$menu_slug  = 'learningtemplates-LifterLMS-backpack';
		$function   = array( __CLASS__, 'options_menu_page_output' );
		$admin_color_scheme = get_user_meta( get_current_user_id(), 'admin_color', true );
		if ( 'fresh' === $admin_color_scheme ) {
			$icon_url = Config::get_admin_media( 'menu-icon-light.png' );
		} else {
			$icon_url = Config::get_admin_media( 'menu-icon.png' );
		}
		$position = 81;
		add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
	}
	public static function register_options_menu_page_settings() {
		register_setting( 'llms_bkpk-group', 'learningtemplates_backpack_active_classes' );
	}
	public static function scripts( $hook ) {
		if ( 'toplevel_page_learningtemplates-LifterLMS-backpack' === $hook || 'learningtemplates-backpack_page_learningtemplates-pro-license-activation' === $hook ) {
			wp_enqueue_style( 'lt-menu-slug-css', Config::get_admin_css( 'admin-style.css' ), array(), LT_BACKPACK_VERSION );
			wp_enqueue_script( 'lt-menu-slug-js', Config::get_admin_js( 'script.js' ), array( 'jquery' ), LT_BACKPACK_VERSION, true );
			wp_enqueue_script( 'lt-quicksand-js', Config::get_admin_js( 'jquery.quicksand.js' ), array( 'jquery' ), LT_BACKPACK_VERSION, true );
			wp_enqueue_script( 'quicksand-js', Config::get_admin_js( 'quicksand.js' ), array( 'jquery' ), LT_BACKPACK_VERSION, true );
			wp_enqueue_style( 'lt-menu-slug-css-fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
		}
	}
	public static function options_menu_page_output() {
		$lt_custom_classes['path']      = self::check_for_other_lt_plugin_classes( 'custom' );
		$lt_custom_classes['namespace'] = 'llms_bkpk';
		$classes_available = self::get_available_classes( array( $lt_custom_classes, $lt_classes ) );
		$active_classes = Config::get_active_classes();
		?>
		<div class="lt-admin-header lt-wrap">
			<a href="http://www.learning-templates.com" target="_blank">
				<img src="<?php echo esc_url( Config::get_admin_media( 'LT_Logo.png' ) ); ?>"/>
			</a>
			<hr class="lt-underline">
			<h2><?php esc_html_e( 'Thanks for using the LearningTemplates LifterLMS BackPack!', 'learningtemplates-lifterlms-backpack' ); ?></h2>
		</div>
		<div class="module_activated" id="module_activated">
			<i class="fa fa-check-circle" aria-hidden="true"></i><span></span>
		</div>
		<div class="module_deactivated" id="module_deactivated">
			<i class="fa fa-times-circle" aria-hidden="true"></i><span></span>
		</div>
		<hr class="lt-underline">
		<div class="lt_feature_container">
			<?php self::create_features( $classes_available, $active_classes ); ?>
		</div>
		<?php
	}
	/* Load Scripts */
	public static function check_for_other_lt_plugin_classes( $lt_plugin ) {
		// plugins dir
		$directory_contents = scandir( WP_PLUGIN_DIR );
		// loop through all contents
		foreach ( $directory_contents as $content ) :
			// exclude parent directories
			if ( '.' !== $content or '..' !== $content ) :
				// create absolute path
				$plugin_dir = WP_PLUGIN_DIR . '/' . $content;
				if ( is_dir( $plugin_dir ) ) {
					if ( 'pro' === $lt_plugin ) {
						if ( 'lt-plugin-pro' === $content || 'learningtemplates-backpack-pro' === $content ) {
							// Check if plugin is active
							if ( is_plugin_active( $content . '/learningtemplates-backpack-pro.php' ) ) {
								return $plugin_dir . '/src/classes/';
							}
						}
					}
					if ( 'custom' === $lt_plugin ) {
						$explode_directory = explode( '-', $content );
						if ( 3 === count( $explode_directory ) ) {
							if ( in_array( 'lt', $explode_directory, true ) && in_array( 'custom', $explode_directory, true ) && in_array( 'plugin', $explode_directory, true ) ) {
								// Check if plugin is active
								if ( is_plugin_active( $content . '/learningtemplates-backpack-custom.php' ) ) {
									return $plugin_dir . '/src/classes/';
								}
							}
							if ( 'learningtemplates-backpack-custom' === $content ) {
								// Check if plugin is active
								if ( is_plugin_active( $content . '/learningtemplates-backpack-custom.php' ) ) {
									return $plugin_dir . '/src/classes/';
								}
							}
						}
					}
				}
			endif;
		endforeach;
		return false;
	}
	public static function get_available_classes( $external_classes = false ) {
		$class_details = array();
		$path = dirname( __FILE__ ) . '/modules/';
		$files = scandir( $path );
		$internal_details = self::get_class_details( $path, $files, __NAMESPACE__ );
		$class_details = array_merge( $class_details, $internal_details );
		if ( false !== $external_classes ) {
			foreach ( $external_classes as $external_class ) {
				if ( isset($external_class['path'])  &&  false !== $external_class['path'] ) {
					$external_files   = scandir( $external_class['path'] );
					$external_details = self::get_class_details( $external_class['path'], $external_files, $external_class['namespace'] );
					$class_details    = array_merge( $class_details, $external_details );
				}
			}
		}
		return $class_details;
	}
	private static function get_class_details( $path, $files, $name_space ) {
		$details = array();
		foreach ( $files as $file ) {
			if ( is_dir( $path . $file ) || '..' === $file || '.' === $file ) {
				continue;
			}
			$class_name = str_replace( '.php', '', $file );
			$class_name = str_replace( '-', ' ', $class_name );
			$class_name = ucwords( $class_name );
			$class_name = $name_space . '\\' . str_replace( ' ', '', $class_name );
			if ( ! class_exists( $class_name ) ) {
				continue;
			}
			$class = new ReflectionClass( $class_name );
			if ( $class->implementsInterface( 'llms_bkpk\RequiredFunctions' ) ) {
				$details[ $class_name ] = $class_name::get_details();
			} else {
				$details[ $class_name ] = false;
			}
		}
		return $details;
	}
	public static function create_features( $classes_available, $active_classes ) {
		if ( function_exists( 'get_magic_quotes_gpc' ) ) {
			if ( get_magic_quotes_gpc() ) {
				$active_classes = Config::stripslashes_deep( $active_classes );
			}
		}
		$modal_html = '';
		foreach ( $classes_available as $key => $class ) {
			if ( ! isset( $class['settings'] ) || false === $class['settings'] ) {
				$class['settings']['modal'] = '';
				$class['settings']['link']  = '';
			}
			// Setting Modal Popup
			$modal_html .= $class['settings']['modal'];
		}
		$backpack_html = '';
		$backpack_html .= '<ul id="features">';
		$add_on_titles = array();
		foreach ( $classes_available as $key => $row ) {
			$add_on_titles[ $key ] = $row['title'];
		}
		array_multisort( $add_on_titles, SORT_ASC, $classes_available );
		foreach ( $classes_available as $key => $class ) {
			$class_name = $key;
			if ( false === $class ) {
				$backpack_html .= '<li class="lt_feature" data-id="' . str_replace( array(
						'llms_bkpk',
						'\\',
						'learningtemplates_backpack',
					), '', $class_name ) . '" data-tags="' . $class['tags'] . '" data-active="0" data-type="' . $class['type'] . '">';
				$backpack_html .= '<div class="lt_feature_title"> ' . esc_html( $key ) . '</div>';
				$backpack_html .= '<div class="lt_feature_description">' . esc_html_e( 'This class is not configured properly. Contact Support for assistance.', 'learningtemplates-lifterlms-backpack' ) . '</div>';
				$backpack_html .= '</li>';
				continue;
			}
			$dependants_exist = $class['dependants_exist'];
			$is_activated = 'lt_feature_deactivated';
			$is_active = 2;
			if ( isset( $active_classes[ $class_name ] ) || isset( $active_classes[ stripslashes( $class_name ) ] ) ) {
				$is_activated = 'lt_feature_activated';
				$is_active    = 1;
			}
			if ( true !== $dependants_exist ) {
				$is_activated = 'lt_feature_needs_dependants';
			}
			$icon = '<div class="lt_icon"></div>';
			if ( $class['icon'] ) {
				$icon = $class['icon'];
			}
			if ( ! isset( $class['settings'] ) || false === $class['settings'] ) {
				$class['settings']['modal'] = '';
				$class['settings']['link']  = '';
			}
			// Setting Modal Popup
			//$backpack_html .= $class['settings']['modal'];
			if ( key_exists( 'tags', $class ) && ! empty( $class['tags'] ) ) {
				$tags = $class['tags'];
			} else {
				$tags = 'general';
			}
			if ( key_exists( 'type', $class ) && ! empty( $class['type'] ) ) {
				$type = $class['type'];
			} else {
				$type = 'custom';
			}
			$backpack_html .= '<li class="lt_feature" data-id="' . str_replace( array(
					'llms_bkpk',
					'learningtemplates_backpack',
				), '', stripslashes( $class_name ) ) . '" data-tags="' . $tags . '" data-active="' . $is_active . '" data-type="' . $type . '">';
			// Settings Modal Popup trigger
			$backpack_html .= $class['settings']['link'];
			$backpack_html .= '<div class="lt_feature_title">';
			$backpack_html .= $class['title'];
			$backpack_html .= '</div>';
			$backpack_html .= '<div class="backpack-customizer-container">';
			// Link to Customizer if applicable
			if ( null !== $class['customizer_link'] ) {
				$backpack_html .= '<a id="backpack-customizer-input" href="' . $class['customizer_link'] . '" style="text-decoration:none;">Customize</a>';
			}
			// Link Field customizer 
			if(!isset($class['fields_link'])){
				
				$class['fields_link'] = null;
				}
			if ( null !== $class['fields_link'] ) {
				$display = 'none';
				if($is_active    == 1){
					$display = 'block';
				}
				$backpack_html .= '<a id="backpack-fields-link" href="' . $class['fields_link'] . '" style="text-decoration:none;display:'.$display.';">Configure</a>';
			}
			$backpack_html .= '</div>';
			$backpack_html .= '<div class="lt_feature_description">' . $class['description'] . '</div>';
			
			if($type != 'inactive'){
				$backpack_html .= '<div class="lt_feature_button ' . $is_activated . '">';
				?>
				<?php
				if ( true !== $dependants_exist ) {
					if ( strpos( $dependants_exist, '@lt_custom_message' ) !== false ) {
						$dependants_exist = str_replace( '@lt_custom_message', '', $dependants_exist );
						$backpack_html     .= '<div><strong>' . esc_html( $dependants_exist ) . '</strong></div>';
					} else {
						$backpack_html .= '<div><strong>' . esc_html( $dependants_exist ) . '</strong>' . esc_html__( ' is needed for this add-on', 'learningtemplates-lifterlms-backpack' ) . '</div>';
					}
				} else {
					$backpack_html .= '<div class="lt_feature_button_toggle"></div>';
					$backpack_html .= '<label class="lt_feature_label" for="' . esc_attr( $class_name ) . '">' . esc_html__( 'Activate ', 'learningtemplates-lifterlms-backpack' ) . $class['title'] . '</label>';
					$backpack_html .= '<input class="lt_feature_checkbox" data-class="' . $class['title'] . '" type="checkbox" id="' . esc_attr( $class_name ) . '" name="learningtemplates_backpack_active_classes[' . esc_attr( $class_name ) . ']" value="' . esc_attr( $class_name ) . '"';
					if ( array_key_exists( $class_name, $active_classes ) ) {
						$backpack_html .= checked( stripslashes( $active_classes[ $class_name ] ), stripslashes( $class_name ), false );
					}
					$backpack_html .= ' />';
				}
				$backpack_html .= '</div>';
			}
			$backpack_html .= '</li>';
		}
		$backpack_html .= '</ul>';
		echo $modal_html . $backpack_html;
	}
}