<?php
session_start(); // avvio della sessione

if (isset($_SESSION["Email"])) { // se l'utente si è loggato può visualizzare/eseguire le azioni permesse dopo l'autenticazione

    require_once 'includes/PHP/dbh.inc.php';
    require_once 'includes/PHP/functions.inc.php';
    include_once 'header.php';
?>

    <title> Pagina profilo </title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/web3@latest/dist/web3.min.js"></script> <!--https://github.com/ChainSafe/web3.js-->

    <script type="module" src="includes/JavaScript/profileETH.js"></script>

    <script type='text/javascript'>

        $(document).ready(function() {
            $('.cartNumber').replaceWith(JSON.parse(localStorage.getItem("cart") || "[]").length);
        });

    </script>

    <div class="container-fluid">

        <div class="row">

            <div class="col-sm-2 sideBar">

                <ul class="nav flex-column" style="margin-top: 10px;">

                    <li class="nav-item">
                       <a class="nav-link sideBarText" id="viewHistoryOrders">  <!-- href="profile.php?viewOrders=true"> -->
                            Visualizza storico ordini
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link sideBarText" id="viewDetailsAccount" href="profile.php?viewDetails=true">  <!-- href="profile.php?viewDAppETH=true" -->
                            Visualizza dettagli account
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link sideBarText" id="viewOperationETH" href="profile.php?viewDAppETH=true">    
                            Visualizza operazioni ETH
                        </a>
                    </li>

                </ul>

            </div>

            <div id="containerProfile" class="col-sm-10">

                <br>

                <?php
                if (isset($_GET['viewGif'])) { // codice usato per visualizzare una gif quando l'utente accede alla pagina profilo
                
                    echo
                    '<div class="row justify-content-md-center">
                        <img id="droneImg" src="img/animation.gif" class="img-fluid" alt="this slowpoke moves" style="display: block"/>
                    </div>';
                }
                ?>

                <?php

                if (isset($_GET['viewDAppETH'])) {

                    $res =  viewAddressETH($conn, $_SESSION["IDCliente"]); // richiamo la funzione utilizzata per visualizzare i dettagli del proprio account
                ?>

                    <div id="profileETH" class="container rounded bg-white mt-5" style="display: block">

                        <div class="row">

                            <div class="col-md-3 border-right">

                                <div class="d-flex flex-column align-items-center p-3 py-5">

                                    <img src="https://media.giphy.com/media/Wu4TiWLLFqxk4KMRiU/giphy.gif" class="img-fluid" alt="this slowpoke moves" />

                                </div>

                            </div>                          

                            <p id="appendAlert"></p>

                            <div class="col-md-9">

                                <div class="p-3 py-5">

                                    <div class="d-flex justify-content-between align-items-center mb-3">

                                        <h2 class="text-right"> Collega il profilo Ethereum </h2>

                                        <?php // codice PHP utilizzato per gestire gli errori di aggiornamento 
                                        if (isset($_GET["error"])) {

                                            if ($_GET["error"] == "wrongprk") { // la nuova mail inserita per aggiornare la precedente non è valida
                                                echo "<small style=\"color: red\"> *Chiave privata/indirizzo non valida/o </small>";
                                            }
                                        }
                                        ?>

                                    </div>

                                    <div class="row mt-2">

                                        <div class="col-md-6">


                                            <?php

                                            if (mysqli_num_rows($res) > 0) {

                                                while ($row = mysqli_fetch_assoc($res)) { // ciclo che scorre i risultati della chiamata della funzione precedente

                                            ?>
                                                    <input id="addressETHlog" name="addressETHlog" type="text" class="form-control" placeholder="Inserisci l'indirizzo" value="<?php echo $row["AddressETH"] ?>" pattern="[a-zA-Z0-9_.-]{42}" required>
                                                <?php

                                                }
                                            } else {

                                                ?>
                                                <input id="addressETHlog" name="addressETHlog" type="text" class="form-control" placeholder="Inserisci l'indirizzo" value="" pattern="[a-zA-Z0-9_.-]{42}" required>
                                            <?php
                                            }
                                            ?>

                                        </div>

                                        <div class="col-md-6">

                                            <script type="text/javascript" src="includes/JavaScript/passShow.js"></script>

                                            <div class="input-group" id="show_hide_password">
                                                <input type="password" name="privateKey" class="privateKey form-control" id="exampleInputPassword1" placeholder="Inserisci la chiave privata" pattern="[a-zA-Z0-9_.-]{64}" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><a href=""><i class="bi bi-eye-slash" aria-hidden="true"></i></a></span>
                                                </div>
                                            </div>

                                        </div>

                                    </div>


                                    <div class="mt-5 text-right">
                                        <button onclick="verifyIdentity()" id="bindETH" type="submit" class="btn btn-primary profile-button"> Collega </button>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                <?php

                }
                ?>

                <?php

                //if(isset($_SESSION["Email"])) { 

                if (isset($_GET['viewDetails'])) { // se l'utente clicca il link visualizzare i dettagli del proprio account

                    $ID = $_SESSION["IDCliente"]; // memorizzo il valore dell'id del cliente all'interno del database

                    $res =  viewDetails($conn, $ID); // richiamo la funzione utilizzata per visualizzare i dettagli del proprio account

                    while ($row = mysqli_fetch_assoc($res)) { // ciclo che scorre i risultati della chiamata della funzione precedente

                ?>
                        <div id="editProfileShow" class="container rounded bg-white mt-5" style="display: block;">

                            <div class="row">

                                <div class="col-md-3 border-right">

                                    <div class="d-flex flex-column align-items-center p-3 py-5">

                                        <img class="rounded-circle mt-5" src="https://picsum.photos/200" width="90">

                                        <span class="font-weight-bold">
                                            <?php echo $row["Nome"] . " " . $row["Cognome"] ?>
                                        </span>

                                        <span class="text-black-50">
                                            <?php echo $row["Email"] ?>
                                        </span>

                                    </div>

                                </div>

                                <div class="col-md-9">

                                    <div class="p-3 py-5">

                                        <form action="includes/PHP/editdetails.inc.php" method="post">
                                            <!-- Quando l'utente clicca sul tasto salva, invoca la funzione per modificare i dettagli del suo account salvati nel database di sistema -->

                                            <div class="d-flex justify-content-between align-items-center mb-3">

                                                <h2 class="text-right">
                                                    Modifica profilo
                                                    <img src="img/penEraser.gif" alt="penna e gomma" width="80">
                                                </h2>

                                                <?php // codice PHP utilizzato per gestire gli errori di aggiornamento 
                                                if (isset($_GET["error"])) {

                                                    if ($_GET["error"] === "invalidemail") { // la nuova mail inserita per aggiornare la precedente non è valida
                                                        echo "<small style=\"color: red\"> *E-mal non valida </small>";
                                                    } else if ($_GET["error"] === "emailexists") { // la nuova mail inserita per aggiornare la precedente è già stata registrata da un altro utente
                                                        echo "<small style=\"color: red\"> *E-mail già registrata </small>";
                                                    } else if ($_GET["error"] === "passdoesntmatch") { // le password non coincidono
                                                        echo "<small style=\"color: red\"> *Le password non coincidono </small>";
                                                    } else if ($_GET["error"] === "invalidpassword") {
                                                        echo "<small style=\"color: red\"> *criteri minimi non rispettati </small>";
                                                    }
                                                }
                                                ?>
                                            </div>

                                            <!-- Nei value degli input, si mostrano i risultati ottenuti dalla richiesta fatta al database -->

                                            <div class="row mt-2">

                                                <div class="col-md-6">

                                                    <input name="name" type="text" class="form-control" placeholder="Nome" value="<?php echo $row["Nome"] ?>" required>

                                                </div>

                                                <div class="col-md-6">

                                                    <input name="surname" type="text" class="form-control" placeholder="Cognome" value="<?php echo $row["Cognome"] ?>" required>

                                                </div>

                                            </div>

                                            <div class="row mt-3">

                                                <div class="col-md-6">
                                                    <input name="email" type="text" class="form-control" placeholder="Email" value="<?php echo $row["Email"] ?>" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <input name="tel" type="tel" class="form-control" pattern="[0-9]{10}" placeholder="Numero di telefono" value="<?php echo $row["Cellulare"] ?>">
                                                </div>

                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <input name="address" type="text" class="form-control" placeholder="Indirizzo" value="<?php echo $row["Indirizzo"] ?>" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <input name="city" type="text" class="form-control" placeholder="Città" value="<?php echo $row["Citta"] ?>" required>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <input name="province" type="text" class="form-control" placeholder="Provincia" value="<?php echo $row["Provincia"] ?>" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <input name="addressETH" type="text" class="form-control" placeholder="Indirizzo blockchain" value="<?php echo $row["AddressETH"] ?>">
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <input name="pwd" type="password" class="form-control" placeholder="Password">
                                                </div>

                                                <div class="col-md-6">
                                                    <input name="pwdRepeat" type="password" class="form-control" placeholder="Conferma Password">
                                                </div>
                                            </div>

                                            <div class="mt-5 text-right">
                                                <button name="saveDetailsBtn" type="submit" class="btn btn-primary profile-button"> Salva </button>
                                            </div>

                                        </form>

                                    </div>

                                </div>

                            </div>

                        </div>

                <?php

                    }
                }

                ?>

                <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
                <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
                <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>


                <script type="text/javascript">
                    var id = <?php echo $_SESSION["IDCliente"] ?>;
                </script>

                <script type="text/javascript" src="includes/JavaScript/profile.js"></script>

                <div class="table-responsive">

                    <table class="table" id="tableShow" style="margin-top: 20px; display:none;">

                        <thead class="table-dark">

                            <tr>
                                <th scope="col"> #Numero ordine </th>
                                <th scope="col"> Titolo </th>
                                <th scope="col"> Autore </th>
                                <th scope="col"> Editore </th>
                                <th scope="col"> Data ordine </th>
                                <th scope="col"> Stato </th>
                            </tr>

                        </thead>

                        <tbody id="tr_dynamic"></tbody>

                    </table>
                </div>
            </div>

        </div>

    </div>

<?php
    
    include_once 'footer.php';
} 

else { // se l'utente non è loggato
    header("location: index.php");
    exit();
}
