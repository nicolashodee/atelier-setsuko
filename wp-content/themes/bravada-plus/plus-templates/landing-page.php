<?php
/**
 * Plus Landing Page content handler
 *
 * @package Cryout Plus
 */

$cryout_lporder = cryout_get_option( _CRYOUT_THEME_PREFIX . '_lporder' );
$cryout_lporder = explode( ',', $cryout_lporder );

foreach ($cryout_lporder as $cryout_lporder) {
    switch ( $cryout_lporder ):
        case 'slider': call_user_func( _CRYOUT_THEME_SLUG . '_lpslider' ); break;
        case 'blocks-1': call_user_func( _CRYOUT_THEME_SLUG . '_lpblocks', 1 ); break;
        case 'blocks-2': call_user_func( _CRYOUT_THEME_SLUG . '_lpblocks', 2 ); break;
        case 'boxes-1': call_user_func( _CRYOUT_THEME_SLUG . '_lpboxes', 1 ); break;
        case 'boxes-2': call_user_func( _CRYOUT_THEME_SLUG . '_lpboxes', 2 );  break;
        case 'boxes-3': call_user_func( _CRYOUT_THEME_SLUG . '_lpboxes', 3 ); ; break;
        case 'text-zero': call_user_func( _CRYOUT_THEME_SLUG . '_lptext', 'zero' ); break;
        case 'text-one': call_user_func( _CRYOUT_THEME_SLUG . '_lptext', 'one' ); break;
        case 'text-two': call_user_func( _CRYOUT_THEME_SLUG . '_lptext', 'two' ); break;
        case 'text-three': call_user_func( _CRYOUT_THEME_SLUG . '_lptext', 'three' ); break;
        case 'text-four': call_user_func( _CRYOUT_THEME_SLUG . '_lptext', 'four' ); break;
        case 'text-five': call_user_func( _CRYOUT_THEME_SLUG . '_lptext', 'five' ); break;
        case 'text-six': call_user_func( _CRYOUT_THEME_SLUG . '_lptext', 'six' ); break;
        case 'portfolio': cryout_lpportfolioplus(); break;
        case 'testimonials': cryout_lptestimonialsplus(); break;
        case 'index': cryout_lpindexplus(); break;
    endswitch;
}

// FIN
