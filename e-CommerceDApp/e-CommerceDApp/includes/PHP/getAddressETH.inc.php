<?php
    session_start();

    if(isset($_SESSION["AddressETH"])) {

        $var = $_SESSION["AddressETH"];
    }

    else {
        $var = $_SESSION["AddressETH"];
    }

    echo json_encode($var);