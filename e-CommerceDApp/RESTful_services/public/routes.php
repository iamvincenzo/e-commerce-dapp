<?php

// File che contiene dei servizi RESTful: routes & endpoints

header("Access-Control-Allow-Origin: *"); // necessario per la Cross-Origin-Policy

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require '../vendor/autoload.php';

// instantiate the App object
$app = new \Slim\App();

// Add route callbacks
$app->get('/hello/{name}', function (Request $request, Response $response, $args) {

    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");

    return $response;
});

/**
 * Route - Endpoint che si occupa di fornire l'elenco e le informazioni sui 
 * prodotti disponibili nel database di sistema.
 */

$app->get('/book', function (Request $request, Response $response, $args) {

    require_once 'includes/PHP/dbh.inc.php';

    $sql = "SELECT IDLibro, Titolo, Autore, Editore, AnnoPubblicazione, NomeFormato, 
                    Prezzo, NomeGenere, ImmagineCopertina, Quantita, NomeLingua
            FROM Libro, Formato, Genere, Lingua
            WHERE CodiceGenere = IDGenere
            AND CodiceFormato = IDFormato
            AND CodiceLingua = IDLingua
            ORDER BY IDLibro;";

    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {

        header("location: stmtfailed.php?error=stmtfailed");
        header('HTTP/1.1 500 Internal Server Error 505');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
        exit();
    }

    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    $data = array();

    while ($row = mysqli_fetch_assoc($resultData)) $data[] = $row;

    mysqli_stmt_close($stmt);

    header('Content-Type: application/json');
    echo json_encode($data);
});


/**
 * Route - Endpoint che si occupa di fornire l'elenco degli indirizzi
 * degli accounts Ethereum dei clienti.
 */

$app->get('/userAddressETH', function (Request $request, Response $response, $args) {

    require_once 'includes/PHP/dbh.inc.php';

    $sql = "SELECT AddressETH
            FROM Cliente;";

    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {

        header("location: stmtfailed.php?error=stmtfailed");
        header('HTTP/1.1 500 Internal Server Error 505');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
        exit();
    }

    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    $data = array();

    while ($row = mysqli_fetch_assoc($resultData)) {

        if (!empty(trim($row["AddressETH"]))) $data[] = $row["AddressETH"];
    }

    mysqli_stmt_close($stmt);

    header('Content-Type: application/json');
    echo json_encode($data);
});


/**
 * Route - Endpoint che si occupa di fornire l'elenco dei prodotti
 * che soddisfano i criteri di ricerca.
 */

$app->post('/searchBook', function (Request $request, Response $response, $args) {

    require_once 'includes/PHP/dbh.inc.php';

    $body = $request->getParsedBody();

    if (isset($body["myData"])) {

        $searchField = $body["myData"];

        /* sostituzione degli spazi con i caratteri pipe poichè il formato della REGEX di ricerca 
                deve essere il seguente: Io|Sono|Un|Esempio */

        $searchField = preg_replace('/\s+/', '|', $searchField);

        $sql = "SELECT Titolo, Autore, Editore, AnnoPubblicazione, NomeGenere, ImmagineCopertina, 
                       Descrizione, Prezzo, NomeFormato, Quantita, IDLibro
                FROM Libro, Genere, Formato 
                WHERE CodiceGenere = IDGenere 
                AND CodiceFormato = IDFormato
                AND CONCAT_WS(Titolo, Autore, Editore, AnnoPubblicazione, NomeGenere) REGEXP ?;";

        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {

            header("location: stmtfailed.php?error=stmtfailed");
            header('HTTP/1.1 500 Internal Server Error 505');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
            exit();
        }

        mysqli_stmt_bind_param($stmt, "s", $searchField);

        mysqli_stmt_execute($stmt);

        $resultData = mysqli_stmt_get_result($stmt);

        $data = array();

        $i = 0;

        while ($row = mysqli_fetch_assoc($resultData)) {

            $bookArray = array(
                'IDLibro' =>    $row["IDLibro"],
                'Titolo'  =>    $row["Titolo"],
                'Autore' => $row["Autore"],
                'Editore' => $row["Editore"],
                'AnnoPubblicazione' => $row["AnnoPubblicazione"],
                'NomeFormato' => $row["NomeFormato"],
                'Prezzo' => $row["Prezzo"],
                'NomeGenere' => $row["NomeGenere"],
                'ImmagineCopertina' => $row["ImmagineCopertina"],
                'Quantita' => $row["Quantita"]
            );

            $data[$i] = $bookArray;

            $i = $i + 1;
        }

        mysqli_stmt_close($stmt);

        header('Content-Type: application/json');
        echo json_encode($data);
    }
});


/**
 * Route - Endpoint che si occupa di fornire l'elenco delle informazioni
 * sui prodotti che sono contenuti nel carrello del cliente.
 */

$app->post('/infoProductsCart', function (Request $request, Response $response, $args) {

    require_once 'includes/PHP/dbh.inc.php';

    $body = $request->getParsedBody();

    if (isset($body["myData"])) {

        $condizione = "";

        foreach ($body["myData"] as $keys => $values) {

            $tmp = "IDLibro=" . $values["id"] . " ";

            $condizione = $condizione . $tmp;
        }

        $condizione = trim($condizione);

        $condizione = preg_replace('/\s+/', ' OR ', $condizione);

        $sql = "SELECT IDLibro, Titolo, Autore, Editore, Prezzo 
                FROM Libro
                WHERE $condizione
                ORDER BY IDLibro;";

        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {

            header("location: stmtfailed.php?error=stmtfailed");
            header('HTTP/1.1 500 Internal Server Error 505');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
            exit();
        }

        mysqli_stmt_execute($stmt);

        $resultData = mysqli_stmt_get_result($stmt);

        $data = array();

        $i = 0;

        while ($row = mysqli_fetch_assoc($resultData)) {

            $bookArray = array(
                'IDLibro' =>    $row["IDLibro"],
                'Titolo'  =>    $row["Titolo"],
                'Autore' => $row["Autore"],
                'Editore' => $row["Editore"],
                'Prezzo' => $row["Prezzo"]
            );

            $data[$i] = $bookArray;

            $i = $i + 1;
        }

        mysqli_stmt_close($stmt);

        header('Content-Type: application/json');
        echo json_encode($data);
    } else {

        header("location: stmtfailed.php?error=stmtfailed");
        header('HTTP/1.1 500 Internal Server Error 505');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
        exit();
    }
});


/**
 * Route - Endpoint che si occupa del processo di acquisto di uno o più 
 * prodotti.
 */

$app->post('/buyProducts/{id}', function (Request $request, Response $response, $args) {

    require_once 'includes/PHP/dbh.inc.php';

    $body = $request->getParsedBody();

    if (isset($body["myData"])) {

        $i = 0;

        $Tot = 0.0;

        $data = array();

        $id = $request->getAttribute('id');

        foreach ($body["myData"] as $keys => $values) { // CICALRE UNO PER UNO E I LIBRI VERIFCARE SE PUO DECREMENTARE E FARLO EVENTUALMENTE

            $sql = "SELECT Prezzo 
                    FROM libro 
                    WHERE IDLibro = ?";

            $stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt, $sql)) {

                header("location: stmtfailed.php?error=stmtfailed");
                header('HTTP/1.1 500 Internal Server Error 505');
                header('Content-Type: application/json; charset=UTF-8');
                die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
                exit();
            }

            mysqli_stmt_bind_param($stmt, "s", $values["id"]);

            mysqli_stmt_execute($stmt);

            $resultData = mysqli_stmt_get_result($stmt);

            while ($row = mysqli_fetch_assoc($resultData)) {

                $Tot = $row["Prezzo"] * $values["qnta"];
            }

            mysqli_stmt_close($stmt); // QUERY USATA PER STRARRE IL TOTALE

            $sql1 = "CALL Acquisto(?, ?, ?, ?, @msg);"; // chiamata della routine di tipo Procedure per effettuare l'acquisto

            $stmt1 = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt1, $sql1)) {

                header("location: stmtfailed.php?error=stmtfailed");
                header('HTTP/1.1 500 Internal Server Error 505');
                header('Content-Type: application/json; charset=UTF-8');
                die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
                exit();
            }

            mysqli_stmt_bind_param($stmt1, "ssss", $values["id"], $id, $values["qnta"], $Tot);

            mysqli_stmt_execute($stmt1);

            $sql2 = "SELECT @msg AS msg;"; // esito dell'acquisto

            $stmt2 = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt2, $sql2)) {

                header("location: stmtfailed.php?error=stmtfailed");
                header('HTTP/1.1 500 Internal Server Error 505');
                header('Content-Type: application/json; charset=UTF-8');
                die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
                exit();
            }

            mysqli_stmt_execute($stmt2);

            $resultData2 = mysqli_stmt_get_result($stmt2);

            while ($row = mysqli_fetch_assoc($resultData2)) {

                $msg = $row["msg"];
            }

            mysqli_stmt_close($stmt2);

            if ($msg === 'Acquisto completato.') $esito = true; // acquisto completato

            else $esito = false; // acquisto non completato

            $bookArray = array(
                'IDLibro' => $values["id"],
                'Esito' =>   $esito,
            );

            $data[$i] = $bookArray;

            $i = $i + 1;
        }

        header('Content-Type: application/json');
        echo json_encode($data);
    }
});


/**
 * Route - Endpoint che si occupa del processo di verificare che la quantità
 * desiderata di prodotto sia ancora disponibile ed eventualmente
 * decrementarla nel database di sistema a quella attuale.
 */

$app->post('/verifyAvailabilityAndDecrement', function (Request $request, Response $response, $args) {

    require_once 'includes/PHP/dbh.inc.php';

    $body = $request->getParsedBody();

    if (isset($body["myData"])) {

        $i = 0;

        $data = array();

        foreach ($body["myData"] as $keys => $values) { // CICALRE UNO PER UNO E I LIBRI VERIFCARE SE PUO DECREMENTARE E FARLO EVENTUALMENTE

            $sql = "UPDATE libro
                    SET Quantita = Quantita - ?
                    WHERE IDLibro = ?
                    AND Quantita >= ?;";

            $stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt, $sql)) {

                header("location: stmtfailed.php?error=stmtfailed");
                header('HTTP/1.1 500 Internal Server Error 505');
                header('Content-Type: application/json; charset=UTF-8');
                die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
                exit();
            }

            mysqli_stmt_bind_param($stmt, "sss", $values["qnta"], $values["id"], $values["qnta"]);

            mysqli_stmt_execute($stmt);

            if (mysqli_affected_rows($conn) > 0) $esito = true; // acquisto completato

            else $esito = false; // acquisto non completato

            mysqli_stmt_close($stmt);

            $sql1 = "SELECT IDLibro, Titolo, Autore, Editore, Prezzo
                     FROM libro
                     WHERE IDLibro = ?";

            $stmt1 = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt1, $sql1)) {

                header("location: stmtfailed.php?error=stmtfailed");
                header('HTTP/1.1 500 Internal Server Error 505');
                header('Content-Type: application/json; charset=UTF-8');
                die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
                exit();
            }

            mysqli_stmt_bind_param($stmt1, "s", $values["id"]);

            mysqli_stmt_execute($stmt1);

            $resultData = mysqli_stmt_get_result($stmt1);

            while ($row = mysqli_fetch_assoc($resultData)) {

                $bookArray = array(
                    'IDLibro' =>    $row["IDLibro"],
                    'Titolo'  =>    $row["Titolo"],
                    'Autore' =>     $row["Autore"],
                    'Editore' =>    $row["Editore"],
                    'Prezzo' =>     $row["Prezzo"],
                    'Esito' =>      $esito,
                    'userQnt' =>    $values["qnta"]
                );

                $data[$i] = $bookArray;

                $i = $i + 1;
            }

            mysqli_stmt_close($stmt1);
        }

        header('Content-Type: application/json');
        echo json_encode($data);
    }
});


/**
 * Route - Endpoint che si occupa di gestire il processo di aggiornamento
 * della quantità di prodotto decrementata quando la transazione in blockchain 
 * non va a buon fine.
 */

$app->post('/reloadBooks', function (Request $request, Response $response, $args) {

    require_once 'includes/PHP/dbh.inc.php';

    $body = $request->getParsedBody();

    if (isset($body["myData2"])) {

        $res = true;

        foreach ($body["myData2"] as $keys => $values) { // CICALRE UNO PER UNO E I LIBRI VERIFCARE SE PUO DECREMENTARE E FARLO EVENTUALMENTE

            if ($values["Esito"]) {

                $sql = "UPDATE libro
                        SET Quantita = Quantita + ?
                        WHERE IDLibro = ?";

                $stmt = mysqli_stmt_init($conn);

                if (!mysqli_stmt_prepare($stmt, $sql)) {

                    header("location: stmtfailed.php?error=stmtfailed");
                    header('HTTP/1.1 500 Internal Server Error 505');
                    header('Content-Type: application/json; charset=UTF-8');
                    die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
                    exit();
                }

                mysqli_stmt_bind_param($stmt, "ss", $values["userQnt"], $values["IDLibro"]);

                mysqli_stmt_execute($stmt);

                mysqli_stmt_close($stmt);
            }
        }

        header('Content-Type: application/json');
        echo json_encode($res);
    }
});


/**
 * Route - Endpoint che si occupa di fornire al cliente l'elenco dei
 * suoi ordini (pagati con carta di credito).
 */

$app->get('/viewOrders/{id}', function (Request $request, Response $response, $args) {

    require_once 'includes/PHP/dbh.inc.php';

    $id = $request->getAttribute('id');

    $i = 0;

    $sql = "SELECT O.IDOrdine, O.Spedito, O.DataOrdine, O.Totale, O.Quantita, Titolo, Autore, 
                   Editore, AnnoPubblicazione, Descrizione, NomeFormato, Prezzo, Pagine, NomeLingua, NomeGenere
            FROM Ordine AS O, Libro, Formato, Genere, Lingua
            WHERE IDCli = ? 
            AND IDL = IDLibro
            AND CodiceGenere = IDGenere
            AND CodiceFormato = IDFormato
            AND CodiceLingua = IDLingua
            ORDER BY O.IDOrdine;";

    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {

        header("location: stmtfailed.php?error=stmtfailed");
        header('HTTP/1.1 500 Internal Server Error 505');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $id);

    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    $data = array();

    while ($row = mysqli_fetch_assoc($resultData)) {

        $bookArray = array(

            'id' => $row["IDOrdine"],
            'title' => $row["Titolo"],
            'author' => $row["Autore"],
            'publisher' => $row["Editore"],
            'purchaseDate' => $row["DataOrdine"],
            'isShipped' => $row["Spedito"]
        );

        $data[$i] = $bookArray;

        $i = $i + 1;
    }

    mysqli_stmt_close($stmt);

    header('Content-Type: application/json');
    echo json_encode($data);
});


/**
 * Route - Endpoint che si occupa di fornire all'impiegato le statistiche
 * sulle vendite dei prodotti.
 */

$app->get('/viewStats', function (Request $request, Response $response, $args) {

    require_once 'includes/PHP/dbh.inc.php';

    $sql = "SELECT IDL, Titolo, ROUND((count(*)/(SELECT count(*) FROM ordine))*100, 2) AS Percentuale
            FROM ordine, libro
            WHERE IDL = IDLibro
            GROUP BY IDL, Titolo;";

    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {

        header("location: stmtfailed.php?error=stmtfailed");
        header('HTTP/1.1 500 Internal Server Error 505');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
        exit();
    }

    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    $data = array();

    while ($row = mysqli_fetch_assoc($resultData)) $data[] = $row;

    mysqli_stmt_close($stmt);

    header('Content-Type: application/json');
    echo json_encode($data);
});


/**
 * Route - Endpoint che si occupa di fornire all'impiegato l'elenco
 * dei prodotti che devono essere spediti ai clienti.
 */

$app->get('/viewOrdersToShip', function (Request $request, Response $response, $args) {

    require_once 'includes/PHP/dbh.inc.php';

    $sql = "SELECT O.IDOrdine, O.Spedito, O.DataOrdine, O.Totale, O.Quantita, Titolo, Autore, Editore, 
                   AnnoPubblicazione, Descrizione, NomeFormato, Prezzo, Pagine, NomeLingua, NomeGenere
            FROM Ordine AS O, Libro, Formato, Genere, Lingua 
            WHERE IDL = IDLibro
            AND CodiceGenere = IDGenere
            AND CodiceFormato = IDFormato
            AND CodiceLingua = IDLingua
            AND O.Spedito = 0
            ORDER BY O.IDOrdine;";

    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {

        header("location: stmtfailed.php?error=stmtfailed");
        header('HTTP/1.1 500 Internal Server Error 505');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
        exit();
    }

    mysqli_stmt_execute($stmt);

    $resultData =  mysqli_stmt_get_result($stmt);

    $data = array();

    $i = 0;

    while ($row = mysqli_fetch_assoc($resultData)) {

        $bookArray = array(

            'id' => $row["IDOrdine"],
            'title' => $row["Titolo"],
            'author' => $row["Autore"],
            'publisher' => $row["Editore"],
            'year' => $row["AnnoPubblicazione"],
            'format' => $row["NomeFormato"],
            'language' => $row["NomeLingua"],
            'quantity' => $row["Quantita"]
        );

        $data[$i] = $bookArray;

        $i = $i + 1;
    }

    mysqli_stmt_close($stmt);

    header('Content-Type: application/json');
    echo json_encode($data);
});


/**
 * Route - Endpoint che si occupa di gestire il processo di spedizione
 * degli ordini (cambiare lo stato salvato nel database di sistema).
 */

$app->post('/empSendOrder', function (Request $request, Response $response, $args) {

    require_once 'includes/PHP/dbh.inc.php';

    $body = $request->getParsedBody();

    if (isset($body['myData'])) {

        $idO = $body['myData']['idO'];
        $idImp = $body['myData']['idImp'];

        $sql = "UPDATE Ordine 
                SET Spedito = 1
                WHERE IDOrdine=?;";

        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {

            header("location: stmtfailed.php?error=stmtfailed");
            header('HTTP/1.1 500 Internal Server Error 505');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
            exit();
        }

        mysqli_stmt_bind_param($stmt, "s", $idO);

        mysqli_stmt_execute($stmt);

        $sql1 = "INSERT INTO spedizione(CodOrdine, CodImpiegato) VALUES(?, ?);"; // si tiene traccia di chi esegue le operazioni

        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql1)) {

            header("location: stmtfailed.php?error=stmtfailed");
            header('HTTP/1.1 500 Internal Server Error 505');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
            exit();
        }

        mysqli_stmt_bind_param($stmt, "ss", $idO, $idImp);

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

        header('Content-Type: text');
        echo json_encode("200 OK");
    }
});


/**
 * Route - Endpoint che si occupa di gestire il processo di aggiornamento della
 * quantità di prodotto dispnibile nel database di sistema da parte 
 * dell'impiegato.
 */

$app->post('/empAddQuantity/{id}', function (Request $request, Response $response, $args) {

    require_once 'includes/PHP/dbh.inc.php';

    $body = $request->getParsedBody();

    $ide = $request->getAttribute('id');

    if (isset($body['myData'])) {

        $idl = $body['myData']['idL'];
        $qnt = $body['myData']['qnta'];

        $sql = "UPDATE Libro 
                SET Quantita = Quantita + ?
                WHERE IDLibro=?;";

        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {

            header("location: stmtfailed.php?error=stmtfailed");
            header('HTTP/1.1 500 Internal Server Error 505');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
            exit();
        }

        mysqli_stmt_bind_param($stmt, "ss", $qnt, $idl);

        mysqli_stmt_execute($stmt);

        $sql1 = "INSERT INTO Ricarica(CodLibro, CodImpiegato, Quantita) VALUES(?, ?, ?);"; // si tiene traccia di chi effettua l'operazione

        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql1)) {

            header("location: stmtfailed.php?error=stmtfailed");
            header('HTTP/1.1 500 Internal Server Error 505');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
            exit();
        }

        mysqli_stmt_bind_param($stmt, "sss", $idl, $ide, $qnt);

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

        header('Content-Type: text');
        echo json_encode("200 OK");
    }
});


/**
 * Route - Endpoint che si occupa di fornire il numero di prodotti
 * contenuti nel database di sistema (utile al sistema di Pagination).
 */

$app->get('/totalPages', function (Request $request, Response $response, $args) {

    require_once 'includes/PHP/dbh.inc.php';

    $sql = "SELECT COUNT(*) AS totalPages
            FROM Libro";

    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {

        header("location: stmtfailed.php?error=stmtfailed");
        header('HTTP/1.1 500 Internal Server Error 505');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
        exit();
    }

    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    $data;

    while ($row = mysqli_fetch_assoc($resultData)) $data = $row["totalPages"];

    mysqli_stmt_close($stmt);

    header('Content-Type: application/json');
    echo json_encode($data);
});


/**
 * Route - Endpoint che si occupa di fornire l'elenco dei prodotti
 * contenuti nel sistema (secondo il sistema Pagination che si limita ad N risultati).
 */

$app->post('/bookCard', function (Request $request, Response $response, $args) {

    require_once 'includes/PHP/dbh.inc.php';

    $body = $request->getParsedBody();

    if (isset($body['myData'])) {

        $page = $body['myData']['page'];
        $record_per_page = $body['myData']['recordPerPage'];

        $start_from = ($page - 1) * $record_per_page;

        $sql = "SELECT IDLibro, Titolo, Autore, Editore, AnnoPubblicazione, NomeFormato, 
                       Prezzo, NomeGenere, ImmagineCopertina, Quantita, NomeLingua
                FROM Libro, Formato, Genere, Lingua
                WHERE CodiceGenere = IDGenere
                AND CodiceFormato = IDFormato
                AND CodiceLingua = IDLingua
                ORDER BY IDLibro
                LIMIT $start_from, $record_per_page;";

        $stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt, $sql)) {

            header("location: stmtfailed.php?error=stmtfailed");
            header('HTTP/1.1 500 Internal Server Error 505');
            header('Content-Type: application/json; charset=UTF-8');
            die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
            exit();
        }

        mysqli_stmt_execute($stmt);

        $resultData = mysqli_stmt_get_result($stmt);

        $data = array();

        while ($row = mysqli_fetch_assoc($resultData)) $data[] = $row;

        mysqli_stmt_close($stmt);

        header('Content-Type: application/json');
        echo json_encode($data);
    }
});

// Run application
$app->run();
