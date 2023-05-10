<?php

    session_start();

    if(isset($_SESSION["ETH"])) {
        
        unset($_SESSION["ETH"]);
    }