<?php
    session_start();

    if(isset($_SESSION["Email"])) {

        $var = true;
    }

    else {
        $var = false;
    }

    echo json_encode($var);

    