<?php
header("Content-type: text/css");
session_start();
if (isset($_SESSION['ert_css'])) {
    echo $_SESSION['ert_css'];
}
?>