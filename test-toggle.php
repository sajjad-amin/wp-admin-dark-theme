<?php
require_once('../../../wp-load.php');

$enabled = get_option( 'wpadt_enable_dark_theme', true );
var_dump($enabled);
update_option( 'wpadt_enable_dark_theme', ! $enabled );
$enabled2 = get_option( 'wpadt_enable_dark_theme', true );
var_dump($enabled2);
