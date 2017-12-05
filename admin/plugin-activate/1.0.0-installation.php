<?php

if ( ! get_site_option( 'translationmanager_api_url' ) ) {
	update_site_option( 'translationmanager_api_url', 'http://api.eurotext.de/api' );
}