#! /usr/local/bin/php -f
<?php
include_once(dirname(__FILE__) . "/com/nexmotion/common/util/PasswordEncrypt.php");

$pw = "1111";

echo  password_hash($pw, PASSWORD_DEFAULT);
echo "\n";
?>
