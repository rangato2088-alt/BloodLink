<?php

session_start();

/* Destroy donor session */

session_unset();

session_destroy();

/* Return to home page */

header("Location: ../index.php");

exit;

?>