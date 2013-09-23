<?php

if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

delete_option( 'wpnewsatme' );
// wp_clear_scheduled_hook('wpm_update_stats');

if ( function_exists('delete_site_option') )  delete_site_option('wpnewsatme_notice_shown');

?>
