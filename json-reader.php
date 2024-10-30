<?php
/*
Plugin Name: Json Reader
Description: Reads Json files and display the feed, with: [jsonReader src="feed.json"]
Version: 1.0
Author: Dessain Saraiva
Author URI: https://github.com/jdsaraiva
License: GPLv2

Retrieve data from a Json feed into your WordPress website:
[jsonReader src="link.json" key="name"]

If no Key is defined all the values will be retrieved
*/

defined( 'ABSPATH' ) or die( 'Skippy, plugin file cannot be accessed directly.' );

if ( ! class_exists( 'jsonReader' ) ) {
	class jsonReader
	{

		protected $tag = 'jsonReader';
		protected $name = 'JsonReader';
		protected $version = '0.1';
		protected $options = array();

		/**
		 * Initiate the plugin by setting the default values and assigning any
		 * required actions and filters.
		 */
		public function __construct()
		{
			if ( $options = get_option( $this->tag ) ) {
				$this->options = $options;
			}
			add_shortcode( $this->tag, array( &$this, 'shortcode' ) );
			if ( is_admin() ) {
				add_action( 'admin_init', array( &$this, 'settings' ) );
			}
		}


		/* Allow the shortcode to be used. */
		public function shortcode( $atts, $content = null )
		{

			extract( shortcode_atts( array(
                                'src' => false, // Json file source
                                'key' => false, // key used  to fetch for specific content
				                'class' => false
			), $atts ) );

			$this->_enqueue();
			$classes = array(
				$this->tag
			);
			if ( !empty( $class ) ) {
				$classes[] = esc_attr( $class );
			}

                        $response = wp_remote_get( $src, $args = array ('sslverify' => false) );
			            $body_values = $response["body"];

                        // Option 1: user is looking for a specific value
                        if($key) {
                            $jsonArray = json_decode($body_values,true);
                            echo $jsonArray[$key];
                        }
                        else echo $body_values;

		}

		/**
		 * Enqueue the required scripts and styles, only if they have not
		 * previously been queued. **/
		protected function _enqueue()
		{
                         // Define the URL path to the plugin...
			$plugin_path = plugin_dir_url( __FILE__ );
                         // Enqueue the styles in they are not already...
			if ( !wp_style_is( $this->tag, 'enqueued' ) ) {
				wp_enqueue_style(
					$this->tag,
					$plugin_path . 'style.css',
					array(),
					$this->version
				);
			}
		}

	}
	new jsonReader;
}
