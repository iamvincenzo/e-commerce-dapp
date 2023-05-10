<?php
    if(isset($_POST["submit"])) { // se viene premuto il tasto Accedi

        $username = $_POST["username"];
        $pwd = $_POST["pwd"];

        require_once 'dbh.inc.php';
        require_once 'functions.inc.php';

        if(emptyInputLogin($username, $pwd) !== false) { // se l'utente non compila tutti i campi durante la fase di accesso
            header("location: ../../employee/index.php?error=emptyinput");
            exit();
        }

        loginEmp($conn, $username, $pwd); // richiama la funzione di accesso al sistema da parte dell'utente
    }

    else {
        header("location: ../../employee/index.php");
        exit();
    }
?>