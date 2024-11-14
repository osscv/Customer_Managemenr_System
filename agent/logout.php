<?php

include "config.php";

session_start();

session_unset();

session_destroy();

header("Location: http://51.222.110.90:288/agent/index.php");

?>