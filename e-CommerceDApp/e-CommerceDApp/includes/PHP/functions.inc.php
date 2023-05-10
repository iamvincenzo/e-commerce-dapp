<?php

                                    // REGISTRAZIONE //

/**
 * Funzione di servizio utilizzata per controllare che i valori immessi nei campi
 * del form di registrazione al database di sistema non siano vuoti.
 */

function emptyInputSignup($name, $surname, $email, $address, $city, $province, $pwd, $pwdRepeat) {

    if(empty($name) || empty($surname) || empty($email) || empty($address) || empty($city) 
        || empty($province) || empty($pwd) || empty($pwdRepeat)) {

        return true;
    }

    else {
        return false;
    }
}


/**
 * Funzione di servizio utilizzata per controllare la validità dell'e-mail immessa
 * nell'apposito campo.
 */

function invalidEmail($email) {

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    }

    else{
        return false;
    }
}


/**
 * Funzione di servizio utilizzata per controllare che l'email utilizzata in fase di 
 * registrazione non sia già stata usata da un altro utente.
 */

function emailExists($conn, $email) {

    // protezione da SQL injection
    
    $email = stripslashes($email);

    $email = mysqli_real_escape_string($conn, $email);
    
    // fine protezione da SQL injection

    $sql = "SELECT * 
            FROM Cliente 
            WHERE Email = ?;";

    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../signup.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $email);

    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)) {
        return $row; // qualcuno ha già usato l'e-mail inserita
    }

    else {
        return false; // nessuno ha usato tale e-mail
    }

    mysqli_stmt_close($stmt);
}


/**
 * Funzione di servizo utilizzata per controllare che le password inserite in fase di
 * registrazione coincidano.
 */

function passwordMatch($pwd, $pwdRepeat) {

    if($pwd !== $pwdRepeat) {
        return true;
    }

    else {
        return false;
    }
}


/**
 * Funzione di servizo utilizzata per controllare che la password inserita rispetti determinati
 * criteri minimi di sicurezza.
 */

function invalidPassword($pwd) { // lungehzza caratteri, caratteri speciali, ecc.ecc.
    
    $uppercase = preg_match('@[A-Z]@', $pwd); 
    $lowercase = preg_match('@[a-z]@', $pwd);
    $number    = preg_match('@[0-9]@', $pwd);

    if(!$uppercase || !$lowercase || !$number || strlen($pwd) < 8) {
        return true;
    }
    
    else {
        return false;
    }
}


/**
 * Funzione di servizio utilizzata per inserire all'interno del database di sistema
 * i dati inseriti dall'utente in fase di registrazione
 */

function createUser($conn, $name, $surname, $email, $pwd, $address, $province, $city) {

    // protezione da SQL injection

    $name = stripslashes($name);
    $surname = stripslashes($surname);
    $email = stripslashes($email);
    $pwd = stripslashes($pwd);
    $address = stripslashes($address);
    $city = stripslashes($city);
    $province = stripslashes($province);

    $name = mysqli_real_escape_string($conn, $name);
    $surname = mysqli_real_escape_string($conn, $surname);
    $email = mysqli_real_escape_string($conn, $email);
    $pwd = mysqli_real_escape_string($conn, $pwd);
    $address = mysqli_real_escape_string($conn, $address);
    $city = mysqli_real_escape_string($conn, $city);
    $province = mysqli_real_escape_string($conn, $province);

    // fine protezione da SQL injection

    $sql = "INSERT INTO Cliente(Nome, Cognome, Email, Password, Indirizzo, Citta, Provincia) 
            VALUES(?, ?, ?, ?, ?, ?, ?);";

    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql)) { 
        header("location: ../../signup.php?error=stmtfailed");
        exit();
    }        

    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

    mysqli_stmt_bind_param($stmt, "sssssss", $name, $surname, $email, $hashedPwd, $address, $city, $province);

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    header("location: ../../login.php?error=none");
    exit();
}

                                    // ACCESSO Cliente//

/**
 * Funzione di servizio utilizzata per controllare che i valori immessi nei campi
 * del form di accesso sistema non siano vuoti.
 */

function emptyInputLogin($email, $pwd) {

    if(empty($email) || empty($pwd)) {
        return true;
    }

    else {
        return false;
    }
}


/**
 * Funzione di servizio utilizzata per permettere l'accesso al sistema all'utente.
 */

function loginUser($conn, $email, $pwd) {

    // protezione da SQL injection

    $email = stripslashes($email);
    $pwd = stripslashes($pwd);
    
    $email = mysqli_real_escape_string($conn, $email);
    $pwd = mysqli_real_escape_string($conn, $pwd);

    // fine protezione da SQL injection

    $sql = "SELECT * 
            FROM Cliente 
            WHERE Email = ?;";

    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../login.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $email);

    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)) { // si ottiene una sola riga come risultato

        $pwdHashed = $row["Password"];

        $checkPwd = password_verify($pwd, $pwdHashed);

        if($checkPwd === false) {
            header("location: ../../login.php?error=incorrectpassword");
            exit();
        }

        else if($checkPwd === true) { // se l'utente si è registrato e la password è corretta allora 

            session_start(); // si avvia una sessione

            // impostazione delle variabili di sessione (variabili globali)

            $_SESSION["IDCliente"] = $row["IDCliente"]; 
            $_SESSION["Nome"] = $row["Nome"];
            $_SESSION["Email"] = $row["Email"];

            if(!empty(trim($row["AddressETH"]))) { // ATTENZIONE CONTROLLARE

                $_SESSION["AddressETH"] = $row["AddressETH"];
            }

            header("location: ../../index.php"); // reindirizzamento ad un'altra pagina
            exit();
        }
    }

    else { // non è stato trovato alcun utente con l'e-mail indicata

        header("location: ../../login.php?error=wronglogin");
        exit();
    }

    mysqli_stmt_close($stmt);
}


/**
 * Funzione di servizio utilizzata per poter visualizzare nella pagina profilo del cliente
 * i dettagli che (il cliente) ha inserito in fase di registrazione.
 */

function viewDetails($conn, $ID) {

    $sql = "SELECT *
            FROM Cliente 
            WHERE IDCliente = ?;"; 

    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../login.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $ID);

    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);
        
    mysqli_stmt_close($stmt);

    return $resultData;
}


/**
 * Funzione di servizio utilizzata per controllare che l'email utilizzata in fase di 
 * aggiornamento dei dettagli dell'utente non sia già stata usata da un altro utente
 * (ad esclusione di se stesso).
 */
    
function emailDetailsExists($conn, $email, $id) {

    // protezione da SQL injection
    
    $email = stripslashes($email);

    $email = mysqli_real_escape_string($conn, $email);
    
    // fine protezione da SQL injection

    $sql = "SELECT * 
            FROM Cliente 
            WHERE Email = ?
            AND IDCliente <> ?;";

    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../profile.php?viewDetails=true&&error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ss", $email, $id);

    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)) {
        return $row;
    }

    else {
        $result = false;

        return $result;
    }

    mysqli_stmt_close($stmt);
}


/**
 * Funzione di servizio utilizzata per poter modificare i dettagli dell'account del cliente
 * memorizzati nel database di sistema.
 */

function editUserDetails($conn, $name, $surname, $email, $pwd, $address, $city, $tel, $province, $addressETH, $id) {

        // protezione da SQL injection

        $name = stripslashes($name);
        $surname = stripslashes($surname);
        $email = stripslashes($email);
        $pwd = stripslashes($pwd);
        $address = stripslashes($address);
        $city = stripslashes($city);
        $tel = stripslashes($tel);
        $province = stripslashes($province);
        $addressETH = stripslashes($addressETH);

        $name = mysqli_real_escape_string($conn, $name);
        $surname = mysqli_real_escape_string($conn, $surname);
        $email = mysqli_real_escape_string($conn, $email);
        $pwd = mysqli_real_escape_string($conn, $pwd);
        $address = mysqli_real_escape_string($conn, $address);
        $city = mysqli_real_escape_string($conn, $city);
        $tel = mysqli_real_escape_string($conn, $tel);
        $province = mysqli_real_escape_string($conn, $province);
        $addressETH = mysqli_real_escape_string($conn, $addressETH);

        // fine protezione da SQL injection
    
        if(!empty($pwd)){ // istruzioni eseguite se si vuole modificare anche la password
        
        $sql = "UPDATE Cliente 
                SET Nome=?, Cognome=?, Email=?, Password=?, Indirizzo=?, Citta=?, Cellulare=?, 
                    Provincia = ?, AddressETH = ? 
                WHERE IDCliente=?;";

        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sql)) { 
            header("location: ../../profile.php?viewDetails=true&&error=stmtfailed");
            exit();
        }        

        $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

        mysqli_stmt_bind_param($stmt, "ssssssssss", $name, $surname, $email, $hashedPwd, $address, $city, $tel, $province, trim($addressETH), $id);

        }

        else { // istruzioni eseguite se si vogliono modificare gli altri campi ma non la password
        
        $sql = "UPDATE Cliente 
                SET Nome=?, Cognome=?, Email=?, Indirizzo=?, Citta=?, Cellulare=?,
                    Provincia = ?, AddressETH = ? 
                WHERE IDCliente=?;";

        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sql)) { 
            header("location: ../../profile.php?viewDetails=true&&error=stmtfailed");
            exit();
        }        

        mysqli_stmt_bind_param($stmt, "sssssssss", $name, $surname, $email, $address, $city, $tel, $province, trim($addressETH), $id);
        }
    
        mysqli_stmt_execute($stmt);

        if(mysqli_affected_rows($conn) > 0) { // se sono state effettuate modifiche

            if(!empty($addressETH)) $_SESSION["AddressETH"] = $addressETH; // agiornare la variabile globale
        }

        if(mysqli_affected_rows($conn) > 0) { // se sono state effettuate modifiche

        if(!empty($email)) $_SESSION["Email"] = $email; // agiornare la variabile globale
        }

        mysqli_stmt_close($stmt);

        header("location: ../../profile.php?viewDetails=true&&error=none");
        exit();
}


                                    // ACCESSO Impiegato //

/**
 * Funzione di servizio utilizzata per permettere l'accesso al sistema all'utente.
 */

function loginEmp($conn, $username, $pwd) {

    // protezione da SQL injection

    $username = stripslashes($username);
    $pwd = stripslashes($pwd);
    
    $username = mysqli_real_escape_string($conn, $username);
    $pwd = mysqli_real_escape_string($conn, $pwd);

    // fine protezione da SQL injection

    $sql = "SELECT * 
            FROM Impiegato
            WHERE NomeUtente = ?;";

    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../employee/index.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $username);

    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)) {

        $pwdDB = $row["Password"];

        if($pwd !== $pwdDB) {
            header("location: ../../employee/index.php?error=incorrectpassword");
            exit();
        }

        else if($pwd === $pwdDB) { // se il nome utente è valido e la password è corretta allora 

            session_start(); // si avvia una sessione

            // impostazione delle variabili di sessione (variabili globali)

            $_SESSION["NomeUtente"] =  $row["NomeUtente"];
            $_SESSION["IDImpiegato"] =  $row["IDImpiegato"];

            header("location: ../../employee/dashboard.php?viewStats=true");
            exit();
        }
    }

    else {

        header("location: ../../employee/index.php?error=wronglogin");
        exit();
    }

    mysqli_stmt_close($stmt);
}


/**
 * Funzione di servizio utilizzata per poter visualizzare i formati
 * disponibili per i nuovi prodotti da inserire.
 */

function viewFormat($conn) {

    $sql = "SELECT *
            FROM Formato;";

    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../employee/dashboard.php?viewForm=true&&error=stmtfailed");
        exit();
    }

    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    mysqli_stmt_close($stmt);

    return $resultData;
}


/**
 * Funzione di servizio utilizzata per poter visualizzare le lingue
 * disponibili per i nuovi prodotti da inserire.
 */

function viewLanguage($conn) {

    $sql = "SELECT *
            FROM Lingua;";

    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../employee/dashboard.php?viewForm=true&&error=stmtfailed");
        exit();
    }

    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    mysqli_stmt_close($stmt);

    return $resultData;
}


/**
 * Funzione di servizio utilizzata per poter visualizzare i generi letterari
 * disponibili per i nuovi prodotti da inserire.
 */

function viewGenre($conn) {

    $sql = "SELECT *
            FROM Genere;";

    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../employee/dashboard.php?viewForm=true&&error=stmtfailed");
        exit();
    }

    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    mysqli_stmt_close($stmt);

    return $resultData;
}


/**
 * Funzione di servizio utilizzata per inserire un nuovo prodotto all'interno
 * del database di sistema.
 */

function buyNewBook($conn, $title, $author, $publisher, $year, $description, $file, $format, $price, $qnt, $numPage, $lang, $genre, $ide) {

    // protezione da SQL injection

    $title = stripslashes($title);
    $author = stripslashes($author);
    $publisher = stripslashes($publisher);
    $year = stripslashes($year);
    $description = stripslashes($description);
    $file = stripslashes($file);
    $format = stripslashes($format);
    $price = stripslashes($price);
    $lang = stripslashes($lang);
    $qnt = stripslashes($qnt);
    $genre = stripslashes($genre);
    $numPage = stripslashes($numPage);

    $title = mysqli_real_escape_string($conn, $title);
    $author = mysqli_real_escape_string($conn, $author);
    $publisher = mysqli_real_escape_string($conn, $publisher);
    $year = mysqli_real_escape_string($conn, $year);
    $description = mysqli_real_escape_string($conn, $description);
    $file = mysqli_real_escape_string($conn, $file);
    $format = mysqli_real_escape_string($conn, $format);
    $price = mysqli_real_escape_string($conn, $price);
    $lang = mysqli_real_escape_string($conn, $lang);
    $qnt = mysqli_real_escape_string($conn, $qnt);
    $genre = mysqli_real_escape_string($conn, $genre);
    $numPage = mysqli_real_escape_string($conn, $numPage);
    
    // fine protezione da SQL injection

    $sql = "INSERT INTO Libro(Titolo, Autore, Editore, AnnoPubblicazione, Descrizione, ImmagineCopertina, CodiceFormato, Prezzo, Quantita, Pagine, CodiceLingua, CodiceGenere) 
            VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";

    $img = 'img/'.$file;

    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../employee/dashboard.php?error=stmtfailed1");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ssssssssssss", $title, $author, $publisher, $year, $description, $img, $format, $price, $qnt, $numPage, $lang, $genre);

    mysqli_stmt_execute($stmt);

    if (mysqli_errno($conn) == 1062) { // controllo che non sia stato generato un codice di errore di duplicate entry
        
        mysqli_stmt_close($stmt);
        header("location: ../../employee/dashboard.php?viewForm=true&&error=duplicateentry");
        exit();
    }

    else {

        // query utilizzata per ottenere l'ID del libro appena inserito nel database

        $sql1 = "SELECT IDLibro
                FROM Libro
                WHERE Titolo = ?
                AND Autore = ?
                AND Editore = ?
                AND AnnoPubblicazione = ?
                AND CodiceFormato = ?
                AND CodiceLingua = ?
                AND CodiceGenere = ? ;"; 

        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sql1)) {
            header("location: ../../employee/dashboard.php?viewForm=true&&error=stmtfailed2");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "sssssss", $title, $author, $publisher, $year, $format, $lang, $genre);

        mysqli_stmt_execute($stmt);

        $resultData = mysqli_stmt_get_result($stmt);

        while($row = mysqli_fetch_assoc($resultData)) {

            $idL = $row["IDLibro"];
        }

        $sql2 = "INSERT INTO Ricarica(CodLibro, CodImpiegato, Quantita) VALUES(?, ?, ?);"; // si tiene traccia di chi esegue le operazioni

        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sql2)) {
            header("location: ../../employee/dashboard.php?viewForm=true&&error=stmtfailed3");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "sss", $idL, $ide, $qnt);

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
    }
}


                                    // DApp


function viewAddressETH($conn, $ID) {

    $sql = "SELECT AddressETH
            FROM Cliente 
            WHERE IDCliente = ?;";

    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../index.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $ID);

    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    mysqli_stmt_close($stmt);

    return $resultData;
}
