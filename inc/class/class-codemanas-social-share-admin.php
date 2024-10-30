<?php

class codemanasSocialShareAdmin {
	public static $instance;
	public $plugin_url = 'codemanas-social-share';
	public $settings = '';
	public $social_links = array();
	private $message = null;

	/**
	 * description: returns current instance of class
	 *
	 * @return codemanasSocialShareAdmin
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		$this->generate_social_link_options();
		add_action( 'admin_menu', array( $this, 'admin_menu_page' ) );
		add_action( 'admin_init', array( $this, 'save_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );
	}

	public function admin_menu_page() {
		add_menu_page(
			'CodeManas Social Share',
			'Social Share',
			'manage_options',
			$this->plugin_url,
			array( $this, 'generate_admin_page' ),
			'dashicons-share'
		);
	}

	public function generate_social_link_options() {
		/*Initialize all default available social links first*/
		$social_links       = array(
			'facebook'    => array(
				'name'       => 'facebook',
				'label'      => 'Facebook',
				'icon_class' => 'cm-icon-facebook-official',
				'title_attr' => 'Share on Facebook',
				'link_class' => 'cm-soc-fb',
				'href_link'  => '#',
				'visible'    => true

			),
			'twitter'     => array(
				'name'       => 'twitter',
				'label'      => 'Twitter',
				'icon_class' => 'cm-icon-twitter',
				'title_attr' => 'Share on Twitter',
				'link_class' => 'cm-soc-tw',
				'href_link'  => '#',
				'visible'    => true

			),
			'google-plus' => array(
				'name'       => 'google-plus',
				'label'      => 'Google Plus',
				'icon_class' => 'cm-icon-gplus',
				'title_attr' => 'Share on Google Plus',
				'link_class' => 'cm-soc-gplus',
				'href_link'  => '#',
				'visible'    => true

			),
			'linkedin'    => array(
				'name'       => 'linkedin',
				'label'      => 'Linkedin',
				'icon_class' => 'cm-icon-linkedin',
				'title_attr' => 'Share on Linkedin',
				'link_class' => 'cm-soc-linkedin',
				'href_link'  => '#',
				'visible'    => true

			),
			'pinterest'   => array(
				'name'       => 'pinterest',
				'label'      => 'Pinterest',
				'icon_class' => 'cm-icon-pinterest',
				'title_attr' => 'Share on Pinterest',
				'link_class' => 'cm-soc-pinterest',
				'href_link'  => '#',
				'visible'    => true

			),
			'whatsapp'    => array(
				'name'       => 'whatsapp',
				'label'      => 'Whatsapp',
				'icon_class' => 'cm-icon-whatsapp',
				'title_attr' => 'Share on Whatsapp',
				'link_class' => 'cm-whatsapp',
				'href_link'  => '',
				'visible'    => true

			)
		);
		$this->social_links = apply_filters( 'cm-social-link-options', $social_links );
	}

	public function load_scripts( $hook ) {
		if ( $hook !== 'toplevel_page_codemanas-social-share' ) {
			return;
		}
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'cm-admin-script',
			plugins_url( 'assets/js/admin-script.js', CODEMANAS_SS_FILE_PATH ),
			array( 'wp-color-picker', 'jquery-ui-sortable' ), false, true );
		wp_enqueue_style( 'codemanas-social-share', plugins_url( 'assets/css/style.css', CODEMANAS_SS_FILE_PATH ), false, false,
			'all' );
	}

	public function save_settings() {
		if ( isset( $_POST['cm_settings_nonce'] )
		     && wp_verify_nonce( $_POST['cm_settings_nonce'],
				'verify_cm_settings_nonce' )
		) {

			$options['cm_allowed']       = ! empty( $_POST['cm_allowed'] ) ? $_POST['cm_allowed'] : '';
			$options['cm_where_to_show'] = ! empty( $_POST['cm_where_to_show'] ) ? $_POST['cm_where_to_show'] : '';
			$options['cm_choose_color']  = ! empty( $_POST['cm_choose_color'] ) ? $_POST['cm_choose_color'] : '';
			$options['cm_icon_color']    = ! empty( $_POST['cm_icon_color'] ) ? $_POST['cm_icon_color']
				: '';
			$options['cm_icon_size']     = ! empty( $_POST['cm_icon_size'] ) ? $_POST['cm_icon_size']
				: '';
			/* option determines is not used but determines what networks are visible */
			$options['cm_selected_social_networks'] = ! empty( $_POST['cm_selected_social_networks'] )
				? $_POST['cm_selected_social_networks'] : '';

			$all_social_networks    = $this->social_links;
			$ordered_social_network = array();

			if ( ! empty( $_POST['cm_selected_social_networks'] ) ) {
				$hidden_networks = array_diff_key( $all_social_networks, $_POST['cm_selected_social_networks'] );
				foreach ( $hidden_networks as $key => $value ) {
					$all_social_networks[ $key ]['visible'] = false;
				}
				foreach ( $_POST['cm_social_link_order'] as $link_order ) {
					$ordered_social_network[ $link_order ] = $all_social_networks[ $link_order ];
				}
			} else {
				foreach ( $_POST['cm_social_link_order'] as $link_order ) {
					$ordered_social_network[ $link_order ]            = $all_social_networks[ $link_order ];
					$ordered_social_network[ $link_order ]['visible'] = false;
				}
			}

			$options['social_networks_to_show'] = $ordered_social_network;


			if ( ! empty( $_POST['cm_choose_color'] ) && ! empty( $option['cm_choose_color'] ) ) {
				$options['cm_icon_color'] = ! empty( $_POST['cm_icon_color'] ) ? $_POST['cm_icon_color'] : '';
			}


			if ( ! empty( $options ) ) {
				update_option( 'codemanas_social_sharing_options', $options );
				$this->set_message( 'updated', 'Settings Saved' );
			}

		}
		$this->settings = get_option( 'codemanas_social_sharing_options' );
	}

	public function set_message( $class, $message ) {
		$this->message = '<div class=' . $class . '><p>' . $message . '</p></div>';
	}

	public function get_message() {
		return $this->message;
	}

	public function generate_admin_page() {
		require_once( CODEMANAS_SS_DIR_PATH . '/inc/views/admin.php' );
	}
}

add_action( 'plugins_loaded', array( 'codemanasSocialShareAdmin', 'get_instance' ) );