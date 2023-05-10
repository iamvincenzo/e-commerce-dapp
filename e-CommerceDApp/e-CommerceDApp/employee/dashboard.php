<?php
session_start(); // avvio della sessione --> cioè l'insieme di accessi dello stesso utente a pagine web diverse senza richiedere ogni volta le credenziali dell'utente.

if (isset($_SESSION["NomeUtente"])) { // se l'impiegato è loggato

    require_once '../includes/PHP/dbh.inc.php';
    require_once '../includes/PHP/functions.inc.php';
    include_once 'header.php';
?>

    <title> Dashboard </title>  

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/web3@latest/dist/web3.min.js"></script><!--https://github.com/ChainSafe/web3.js-->

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>

    <script type="text/javascript">
        var idImp = <?php echo json_encode($_SESSION["IDImpiegato"]);?>; // script eseguito per salvare in una variabile l'ID dell'impiegato
    </script>

    <script type="module" src="../includes/JavaScript/dashboard.js"></script> <!-- Inclusione dello script utilizzato per la gestione della dashboard dell'impiegato -->

    <div class="container-fluid">

        <div class="row">

            <div class="col-sm-2 sideBar">

                <ul class="nav flex-column" style="margin-top: 10px;">

                    <li class="nav-item">
                        <a class="nav-link sideBarText" href="dashboard.php?viewStock=true">
                            Aggiungi nuove copie
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link sideBarText" href="dashboard.php?viewForm=true"> Inserisci un nuovo libro </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link sideBarText" id="shipOrders" href="dashboard.php?viewOrderToShip=true"> Spedisci </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link sideBarText" href="dashboard.php?viewStats=true"> Statistiche vendite </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link sideBarText" href="dashboard.php?viewOrderETH=true"> Smart Contract & Blockchain </a>
                    </li>

                </ul>

            </div>

            <div class="col-sm-10">

                <br>

                <?php

                if (isset($_GET["viewOrderETH"])) {
                ?>

                    <div class="container" style="margin-top: 55px;">

                        <ul class="nav nav-tabs nav-fill">

                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="#" id="viewBalanceBtn">
                                    <span>
                                        Visualizza bilancio
                                    </span>
                                </a>
                            </li>

                            <li class="nav-item"></li>

                            <li class="nav-item"></li>

                        </ul>

                        <div id="balanceEmp" style="display:none;">
                            <div style="padding-left: 110px;"><span id="balanceShop"></span></div>
                            <div style="padding-left: 110px;"><span id="balanceSmartContract"></span></div>
                            <button type="submit" id="ritirabtn" name="submit" class="btn btn-primary" style="margin-left: 110px;"> Ritira </button>
                        </div>

                    </div>

                    <br>

                    <div class="container">

                        <div id='rowTable' class="row">

                            <div class="table-responsive">

                                <table class="table" id="tableShow" style="margin-top: 20px;">

                                    <thead class="table-dark">

                                        <tr>
                                            <th scope="col"> #Numero acquisto </th>
                                            <th scope="col"> Address </th>
                                            <th scope="col"> Contenuto </th>
                                            <th scope="col"> Ether </th>
                                            <th scope="col"> Data acquisto </th>
                                            <th scope="col"> Stato </th>
                                            <th scope="col"> Azione </th>
                                        </tr>

                                    </thead>

                                    <tbody id="tr_dynamic"></tbody>

                                </table>

                            </div>

                        </div>

                    </div>


                <?php

                    }

                    if (isset($_GET['viewStock'])) { // se viene premuto il link acquista nuove copie 
                ?>

                    <div class="table-responsive">
                                
                        <table id="addQuantityTable" class="table" style="margin-top: 20px;"> 
                            
                            <thead class="table-dark">
                                
                                <tr>
                                    <th scope="col"> #Codice </th>
                                    <th scope="col"> Titolo </th>
                                    <th scope="col"> Autore </th>
                                    <th scope="col"> Editore </th>
                                    <th scope="col"> Anno </th>
                                    <th scope="col"> Formato </th>
                                    <th scope="col"> Lingua </th>
                                    <th scope="col"> Quantità </th>
                                    <th scope="col"> Azione </th>
                                </tr>
                            
                            </thead>
                            
                            <tbody> </tbody>
                        </table>
                    </div>
                                    
                <?php   
                    }                

                    if (isset($_GET['viewForm'])) { // se viene premuto il link acquista un nuovo libro
                ?>

                    <div class="container rounded mt-5">

                        <div class="row justify-content-md-center">

                            <div class="col-md-9">

                                <div class="p-3 py-5">

                                    <form action="../includes/PHP/buyNewBook.inc.php" method="post" enctype="multipart/form-data">

                                        <div class="d-flex justify-content-between align-items-center mb-3">

                                            <h2 class="text-right">
                                                Inserisci un nuovo libro
                                                <img src="../img/book-loader.gif" alt="caricamento libro" width="50">
                                            </h2>

                                            <?php
                                            if (isset($_GET["error"])) {

                                                if ($_GET["error"] === "none") { // se acquisto andato a buon fine
                                                    echo "<small style=\"color: green\"> Inserimento andato a buon fine </small>";
                                                } else if ($_GET["error"] === "duplicateentry") { // se acquisto non andato a buon fine
                                                    echo "<small style=\"color: red\"> *Inserimento fallito: libro già esistente </small>";
                                                }
                                            }
                                            ?>

                                        </div>

                                        <div class="row mt-2">

                                            <div class="col-md-6">

                                                <input name="title" type="text" class="form-control" placeholder="Titolo" required>

                                            </div>

                                            <div class="col-md-6">

                                                <input name="author" type="text" class="form-control" placeholder="Autore" required>

                                            </div>

                                        </div>

                                        <div class="row mt-3">

                                            <div class="col-md-6">

                                                <input name="publisher" type="text" class="form-control" placeholder="Editore" required>

                                            </div>

                                            <div class="col-md-6">

                                                <input name="year" type="number" min=1980 max=2021 class="form-control" placeholder="Anno pubblicazione">

                                            </div>

                                        </div>

                                        <div class="row mt-3">

                                            <div class="col-md-6">

                                                <input name="description" type="textarea" class="form-control" placeholder="Descrizione" required>

                                            </div>


                                            <div class="col-md-6">

                                                <label class="custom-file-upload">

                                                    <input name="file" type="file" required />

                                                </label>

                                            </div>

                                        </div>

                                        <div class="row mt-3">

                                            <div class="col-md-6">

                                                <select name="format" class="custom-select" id="inputGroupSelect04" aria-label="Example select with button addon">

                                                    <option selected> Scegli il formato del libro </option>

                                                    <?php

                                                    $res =  viewFormat($conn);

                                                    while ($row = mysqli_fetch_assoc($res)) {

                                                        echo '<option value="' . $row["IDFormato"] . '">' . $row["NomeFormato"] . '</option>';
                                                    }

                                                    ?>

                                                </select>

                                            </div>

                                            <div class="col-md-6">

                                                <input name="price" min=1 type="number" step="0.01" class="form-control" placeholder="Prezzo" required>

                                            </div>

                                        </div>

                                        <div class="row mt-3">

                                            <div class="col-md-6">

                                                <select name="lang" class="custom-select" id="inputGroupSelect04" aria-label="Example select with button addon">

                                                    <option selected> Scegli la lingua del libro </option>

                                                    <?php

                                                    $res =  viewLanguage($conn);

                                                    while ($row = mysqli_fetch_assoc($res)) {

                                                        echo '<option value="' . $row["IDLingua"] . '">' . $row["NomeLingua"] . '</option>';
                                                    }

                                                    ?>

                                                </select>

                                            </div>

                                            <div class="col-md-6">

                                                <input name="qnt" min=1 type="number" class="form-control" placeholder="Quantità" required>

                                            </div>

                                        </div>

                                        <div class="row mt-3">

                                            <div class="col-md-6">

                                                <select name="genre" class="custom-select" id="inputGroupSelect04" aria-label="Example select with button addon">

                                                    <option selected> Scegli il genere del libro </option>

                                                    <?php

                                                    $res =  viewGenre($conn);

                                                    while ($row = mysqli_fetch_assoc($res)) {

                                                        echo '<option value="' . $row["IDGenere"] . '">' . $row["NomeGenere"] . '</option>';
                                                    }

                                                    ?>

                                                </select>

                                            </div>

                                            <div class="col-md-6">

                                                <input name="numPage" min=1 type="number" class="form-control" placeholder="Numero pagine" required>

                                            </div>

                                        </div>

                                        <div class="mt-5 text-right">

                                            <button name="submitBuyBtn" type="submit" class="btn btn-primary profile-button">
                                                Inserisci </button>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        </div>

                    </div>
                <?php

                }
                ?>

                <?php

                if (isset($_GET["viewOrderToShip"])) {
                ?>

                <div class="table-responsive">

                    <table id="shipTable" class="table" style="margin-top: 20px;">

                        <thead class="table-dark">

                            <tr>
                                <th scope="col"> #Codice </th>
                                <th scope="col"> Titolo </th>
                                <th scope="col"> Autore </th>
                                <th scope="col"> Editore </th>
                                <th scope="col"> Anno </th>
                                <th scope="col"> Formato </th>
                                <th scope="col"> Lingua </th>
                                <th scope="col"> Quantità </th>
                                <th scope="col"> Azione </th>
                            </tr>

                        </thead>

                        <tbody id="trship_dynamic"> </tbody>

                    </table>
                </div>

                <?php
                    }
                ?>

                <script type="text/javascript" src="../includes/JavaScript/charts.js"></script> <!-- Inclusione dello script utilizzato per ottenere e gestire le statistiche sulle vendite -->

                <?php
                    if (isset($_GET["viewStats"])) { // se viene premuto il link Statistiche vendite
                ?>

                    <div class="container" style="width: 100%; height: 80vh;">
                        <canvas id="myCanvas"></canvas>
                    </div>

                    <div class="row justify-content-md-center">
                        <h5> 
                            libri 
                        </h5> 
                    </div>

                <?php
                    }
                ?>

            </div>

        </div>

    </div>

    </body>

    </html>

<?php
}
?>