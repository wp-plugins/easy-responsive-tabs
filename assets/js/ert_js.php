<?php
header("Content-type: text/javascript");
session_start();
echo 'jQuery(document).ready(function() {';
echo $_SESSION['ert_js'];
echo '});';
?>