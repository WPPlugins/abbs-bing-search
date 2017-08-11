<?php

/* If uninstall is not called from WordPress exit. */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit ();
}

delete_option( "abbs_bing_api_key" );
delete_option( "abbs_bing_search_count" );
delete_option( "abbs_bing_search_market" );
delete_option( "abbs_bing_search_website" );
delete_option( "abbs_bing_inline_search" );
delete_option( "abbs_bing_search_custom_css" );
delete_option( "abbs_custom_search_bool" );
delete_option( "abbs_custom_search_string" );