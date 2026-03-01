<?php
require_once('../../../wp-load.php');
// pristine condition
delete_option('wpadt_enable_dark_theme');

$enabled = ( get_option( 'wpadt_enable_dark_theme', '1' ) === '1' );
var_dump("Initial: ", $enabled);

$new_val = $enabled ? '0' : '1';
update_option( 'wpadt_enable_dark_theme', $new_val );

$enabled2 = ( get_option( 'wpadt_enable_dark_theme', '1' ) === '1' );
var_dump("After toggle: ", $enabled2);
