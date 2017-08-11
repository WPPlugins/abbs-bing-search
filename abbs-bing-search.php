<?php

/*
Plugin Name: Bing Search API Plugin
Description: Override your wordpress search functionality with Bing Search API.
Version: 0.3.3
Text Domain: abbs-bing-search
Author: Askew Brook
Author URI: https://www.askewbrook.com
License: GPL V3
 */

class Askew_Brook_Bing_Search {
	private static $instance = NULL;
	private $plugin_path;
	private $plugin_url;
	private $text_domain = 'abbs-bing-search';

	private function __construct() {
		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->plugin_url  = plugin_dir_url( __FILE__ );

		load_plugin_textdomain( $this->text_domain, FALSE, $this->plugin_path . '\lang' );

		add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_styles' ] );

		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_styles' ] );

		register_activation_hook( __FILE__, [ $this, 'activation' ] );
		register_deactivation_hook( __FILE__, [ $this, 'deactivation' ] );

		$this->run_plugin();
	}

	private function run_plugin() {
		// action hooks
		add_action( 'admin_menu', 'abbs_bing_search_register_admin_page' );
		add_action( 'pre_get_posts', 'abbs_bing_search_check_if_search' );
		add_action( 'admin_init', 'abbs_bing_search_register_settings' );
		add_action( 'admin_notices', 'abbs_bing_search_notify_user' );

		function abbs_bing_search_register_admin_page() {
			add_submenu_page(
				"options-general.php",
				"Bing Search",
				"Bing Search",
				"manage_options",
				"abbs_bing_search_options",
				"abbs_bing_search_submenu_callback"
			);
		}

		function abbs_bing_search_register_settings() {

			register_setting( "abbs_bing_search_options", "abbs_bing_api_key", [
				'type'              => 'string',
				'description'       => __( 'The API Key provided by Bing to use the search functionality', 'abbs-bing-search' ),
				'sanitize_callback' => NULL,
				'show_in_rest'      => FALSE,
			] );

			register_setting( "abbs_bing_search_options", "abbs_bing_search_count", [
				'type'              => 'intval',
				'description'       => __( 'The amount of Search items per page', 'abbs-bing-search' ),
				'sanitize_callback' => NULL,
				'show_in_rest'      => FALSE,
			] );

			register_setting( "abbs_bing_search_options", "abbs_bing_search_market", [
				'type'              => 'string',
				'description'       => __( 'The market that the search will use', 'abbs-bing-search' ),
				'sanitize_callback' => NULL,
				'show_in_rest'      => FALSE,
			] );

			register_setting( "abbs_bing_search_options", "abbs_bing_search_website", [
				'type'              => 'string',
				'description'       => __( 'The website that will be used in the search closure', 'abbs-bing-search' ),
				'sanitize_callback' => NULL,
				'show_in_rest'      => FALSE,
			] );

			register_setting( "abbs_bing_search_options", "abbs_bing_inline_search", [
				'type'              => 'string',
				'description'       => __( 'Set if you would like a inline search on the results column', 'abbs-bing-search' ),
				'sanitize_callback' => NULL,
				'show_in_rest'      => FALSE,
			] );

			register_setting( "abbs_bing_search_options", "abbs_bing_search_custom_css", [
				'type'              => 'string',
				'description'       => __( 'Inject any custom styling here to be used on the plugin theme file', 'abbs-bing-search' ),
				'sanitize_callback' => NULL,
				'show_in_rest'      => FALSE,
			] );

			register_setting( "abbs_bing_search_options", "abbs_custom_search_bool", [
				'type'              => 'string',
				'description'       => __( 'Use the new custom search functionality (currently in preview).', 'abbs-bing-search' ),
				'sanitize_callback' => NULL,
				'show_in_rest'      => FALSE,
			] );

			register_setting( "abbs_bing_search_options", "abbs_custom_search_string", [
				'type'              => 'string',
				'description'       => __( 'Code given by bing for your custom search.', 'abbs-bing-search' ),
				'sanitize_callback' => NULL,
				'show_in_rest'      => FALSE,
			] );

			add_settings_section(
				'abbs_bing_search_settings_section',
				__( 'API Settings', 'abbs-bing-search' ),
				'',
				'abbs_bing_search_options'
			);

			add_settings_section( "abbs_bing_inject_css_section",
				__( "Custom CSS", 'abbs-bing-search' ),
				'',
				"abbs_bing_search_options"
			);

			add_settings_field( "abbs_bing_api_key", __( "Bing API Key", 'abbs-bing-search' ), "abbs_bing_api_key_callback", "abbs_bing_search_options", 'abbs_bing_search_settings_section' );
			add_settings_field( "abbs_bing_website_search", __( "Website", 'abbs-bing-search' ), "abbs_bing_website_callback", "abbs_bing_search_options", 'abbs_bing_search_settings_section' );
			add_settings_field( "abbs_bing_search_count", __( "Items Per Page", 'abbs-bing-search' ), "abbs_bing_search_count_callback", "abbs_bing_search_options", 'abbs_bing_search_settings_section' );
			add_settings_field( "abbs_bing_search_market", __( "Country/Market", 'abbs-bing-search' ), "abbs_bing_search_market_callback", "abbs_bing_search_options", 'abbs_bing_search_settings_section' );
			add_settings_field( "abbs_bing_inline_search", __( "Inline Search field", 'abbs-bing-search' ), "abbs_bing_inline_search_callback", "abbs_bing_search_options", 'abbs_bing_search_settings_section' );
			add_settings_field( "abbs_custom_search_bool", __( "Use custom search API", 'abbs-bing-search' ), "abbs_custom_search_bool_callback", "abbs_bing_search_options", 'abbs_bing_search_settings_section' );
			add_settings_field( "abbs_custom_search_string", __( "Custom search Code", 'abbs-bing-search' ), "abbs_custom_search_string_callback", "abbs_bing_search_options", 'abbs_bing_search_settings_section' );
			add_settings_field( "abbs_bing_search_custom_css", __( "Custom Styles", 'abbs-bing-search' ), "abbs_bing_search_custom_css_callback", "abbs_bing_search_options", 'abbs_bing_inject_css_section' );
		}

		function abbs_custom_search_bool_callback() {
			$setting = get_option( 'abbs_custom_search_bool' );

			?>
          <label>
            <input type="radio" name="abbs_custom_search_bool" value="1"
                   required="" <?= checked( $setting, "1", "checked" ); ?>> Yes
          </label>
          <label>
            <input type="radio" name="abbs_custom_search_bool" value="0" <?= checked( $setting, "0", "checked" ); ?>> No
          </label>
			<?php
		}

		function abbs_custom_search_string_callback() {
			$setting = get_option( 'abbs_custom_search_string' );
			?>
          <input type="text" name="abbs_custom_search_string" class="regular-text"
                 value="<?= isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
			<?php
		}

		function abbs_bing_search_custom_css_callback() {
			// get the value of the setting we've registered with register_setting()
			$setting = get_option( 'abbs_bing_search_custom_css' );
			?>
          <textarea name="abbs_bing_search_custom_css" class="large-text" cols="30" placeholder="#main {
	background-color: blue;
}" rows="10"><?= isset( $setting ) ? esc_attr( $setting ) : ''; ?></textarea>
			<?php
		}

		function abbs_bing_inline_search_callback() {
			$setting = get_option( 'abbs_bing_inline_search' );

			?>
          <label>
            <input type="radio" name="abbs_bing_inline_search" value="1"
                   required="" <?= checked( $setting, "1", "checked" ); ?>> Yes
          </label>
          <label>
            <input type="radio" name="abbs_bing_inline_search" value="0" <?= checked( $setting, "0", "checked" ); ?>> No
          </label>
			<?php
		}

		function abbs_bing_api_key_callback() {
			// get the value of the setting we've registered with register_setting()
			$setting = get_option( 'abbs_bing_api_key' );
			// output the field
			?>
          <input type="text" name="abbs_bing_api_key" class="regular-text"
                 value="<?= isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
			<?php
		}

		function abbs_bing_search_count_callback() {
			// get the value of the setting we've registered with register_setting()
			$setting = get_option( 'abbs_bing_search_count' );
			// output the field
			?>
          <input type="number" name="abbs_bing_search_count" class="small-text" min="1" max="20" required=""
                 value="<?= isset( $setting ) && $setting != '' ? esc_attr( $setting ) : '10'; ?>"> <?php __( "Items", "ab-bing-search" ); ?>
			<?php
		}

		function abbs_bing_search_market_callback() {
			include "shortcodes.php";
			$setting = get_option( 'abbs_bing_search_market' );
			?>
          <select name="abbs_bing_search_market">
			  <?php foreach ( $markets as $option ): ?>
                <option <?php echo( $setting == $option['shortcode'] ? "selected=''" : "" ); ?>
                    value="<?= $option['shortcode']; ?>"><?= $option['longform']; ?></option>
			  <?php endforeach; ?>
          </select>
			<?php
		}

		function abbs_bing_website_callback() {
			$setting = get_option( 'abbs_bing_search_website' );

			?>
          <p>Setting this to empty will use the default server name.</p>
          <input type="text" name="abbs_bing_search_website" class="regular-text"
                 value="<?= isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
			<?php
		}

		function abbs_bing_search_submenu_callback() {
			// check user capabilities
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			add_filter( 'admin_footer_text', 'abbs_admin_footer_content' );
			?>
          <div class="wrap">
            <div id="icon-tools" class="icon32"></div>
            <h1><?= esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
				<?php
				// output security fields for the registered setting "wporg_options"
				settings_fields( 'abbs_bing_search_options' );
				// output setting sections and their fields
				// (sections are registered for "wporg", each field is registered to a specific section)
				do_settings_sections( 'abbs_bing_search_options' );
				// output save settings button
				submit_button( 'Save Settings' );
				?>
            </form>
          </div>
			<?php
		}

		function abbs_bing_search_notify_user() {
			// check if user has set their api key for bing search
			$key = get_option( "abbs_bing_api_key", FALSE );
			if ( ! isset( $key ) || $key == '' ) { ?>
              <div class="notice notice-error is-dismissible">
                <p><?= __( "You have not yet set your bing search API Key. You can obtain one", 'abbs-bing-search' ); ?>
                  <a target="_blank"
                     href="https://www.microsoft.com/cognitive-services/en-us/bing-web-search-api"><?= __( "here.", "abbs-bing-search" ); ?></a>
                </p>
              </div>
			<?php }
		}

		function abbs_bing_search_fire_search_func() {
			$bing_key      = get_option( "abbs_bing_api_key" );
			$search_count  = get_option( "abbs_bing_search_count" );
			$search_market = urlencode( get_option( 'abbs_bing_search_count' ) );
			if (get_option( "abbs_custom_search_bool" )) {
				$custom_config = get_option( "abbs_custom_search_string" );
			}
			$page         = ( isset( $_GET['s_page'] ) ? $_GET['s_page'] : 0 );
			$query_string = urlencode( $_GET['s'] );

			$website_string = get_option( "abbs_bing_search_website" );
			if ( ! empty( $website_string ) ) {
				$url = $website_string;
			} else {
				$url = $_SERVER['SERVER_NAME'];
			}

			$page_offset = $page * $search_count;
			try {
				if (get_option( "abbs_custom_search_bool" )) {
            $endpoint = "https://api.cognitive.microsoft.com/bingcustomsearch/v5.0/search?q=$query_string&customconfig=$custom_config&responseFilter=Webpages&mkt=$search_market&safesearch=Moderate&count=$search_count&offset=$page_offset";
        } else {
            $endpoint = "https://api.cognitive.microsoft.com/bing/v5.0/search?q=$query_string%20site%3A$url&count=$search_count&offset=$page_offset&mkt=$search_market&safesearch=moderate&responseFilter=WebPages&textDecorations=true&textFormat=HTML";
				}
				$curl = curl_init();

				curl_setopt_array( $curl, [
					CURLOPT_URL            => $endpoint,
					CURLOPT_RETURNTRANSFER => TRUE,
					CURLOPT_ENCODING       => "",
					CURLOPT_MAXREDIRS      => 10,
					CURLOPT_TIMEOUT        => 30,
					CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST  => "GET",
					CURLOPT_HTTPHEADER     => [
						"cache-control: no-cache",
						"ocp-apim-subscription-key: $bing_key",
					],
				] );

				$response = curl_exec( $curl );
				$err      = curl_error( $curl );

				curl_close( $curl );

				if ( $err ) {
					global $bing_query_object;
					$bing_query_object = "ERROR";
				} else {
					global $bing_query_object;
					global $bing_query_options;
					$bing_query_object = json_decode( $response );
					if ( ! isset( $bing_query_object->webPages->value ) ) {
						$bing_query_object = "NORESULTS";
					} else {
						$bing_query_object  = $bing_query_object->webPages->value;
						$bing_query_options = [
							"next_page" => "<a href=\"" . get_site_url() . "?s=" . $query_string . "&s_page=" . ( $page + 1 ) . "\">Next Page</a>",
							"prev_page" => ( $page != 0 ? "<a href=\"" . get_site_url() . "?s=" . $query_string . "&s_page=" . ( $page - 1 ) . "\">Previous Page</a>" : "" ),
						];
					}
				}
			} catch ( Exception $e ) {
				global $bing_query_object;
				$bing_query_object = "ERROR";
			}

			return __DIR__ . "/bing-search-template.php";
		}

		function abbs_admin_footer_content() {
			echo '<span id="footer-thankyou">Thank you for creating with <a href="https://wordpress.org/">WordPress</a>.</span> | <span>Plugin Created By <a target="_blank" href="https://askewbrook.com">Askew Brook</a></span>';
		}

		function abbs_bing_search_check_if_search( $query ) {
			if ( $query->is_search ) {
				add_action( 'template_include', 'abbs_bing_search_fire_search_func' );
			}
		}
	}

	public static function get_instance() {
		// If an instance hasn't been created and set to $instance create an instance and set it to $instance.
		if ( NULL == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function get_plugin_url() {
		return $this->plugin_url;
	}

	public function get_plugin_path() {
		return $this->plugin_path;
	}

	public function activation() {

	}

	public function deactivation() {

	}

	public function register_scripts() {

	}

	public function register_styles() {

	}
}

Askew_Brook_Bing_Search::get_instance();
