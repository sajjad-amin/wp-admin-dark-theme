<?php
require_once('../../../wp-load.php');
// pristine condition
delete_option('wpadt_test_toggle');

$e1 = get_option('wpadt_test_toggle', true);
var_dump("Initial: ", $e1);

update_option('wpadt_test_toggle', ! $e1);

$e2 = get_option('wpadt_test_toggle', true);
var_dump("After toggle: ", $e2);
