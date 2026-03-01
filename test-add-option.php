<?php
require_once('../../../wp-load.php');
delete_option('wpadt_test_false');
$res = add_option('wpadt_test_false', false);
var_dump($res);
$val = get_option('wpadt_test_false', 'DEFAULT');
var_dump($val);
