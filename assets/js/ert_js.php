<?php
session_start();
header("Content-type: text/javascript");
echo 'jQuery(document).ready(function() {';
if (isset($_SESSION['ert_js'])) {
    echo  $_SESSION['ert_js'];
    unset($_SESSION['ert_js']);
}
echo '});';
?>