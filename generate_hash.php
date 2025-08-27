<?php
$plainPassword = "heslo123";
$hash = password_hash($plainPassword, PASSWORD_DEFAULT);
echo $hash;
?>
