<?php
session_start(); // avvio della sessione

if (isset($_SESSION["Email"])) { // se l'utente è logggato

    include_once 'header.php';
?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/web3@latest/dist/web3.min.js"></script> <!--https://github.com/ChainSafe/web3.js-->

    <title> Check-out </title>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">

    <div class="container" style="margin-top: 50px; margin-bottom: 50px;">

        <div class="row mb-4"> </div>

        <div class="row">

            <div class="col-lg-7 mx-auto">

                <div class="card ">

                    <div class="card-header">

                        <div class="bg-white shadow-sm pt-4 pl-2 pr-2 pb-2">

                            <ul role="tablist" class="nav bg-light nav-pills rounded nav-fill mb-3">

                                <li class="nav-item">
                                    <a data-toggle="pill" href="#credit-card" class="nav-link active ">
                                        <img src="img/credit-card.png" width="35"></img>
                                        Carta di credito
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a data-toggle="pill" href="#ethereum" class="nav-link ">
                                        <img src="img/ethereum.png" width="35"></img>
                                        Ethereum
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a data-toggle="pill" href="#metamask" class="nav-link ">
                                        <img src="img/metamask.png" width="35"></img>
                                        Meta Mask
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <script type="text/javascript">
                            var IDCliente = <?php echo json_encode($_SESSION["IDCliente"]); ?>; // script utilizzato per ottenere l'id del cliente da passare come valore allo script che segue
                        </script>

                        <script type="text/javascript" src="includes/JavaScript/checkoutCreditCard.js"></script> <!-- Inclusione dello script utilizzato per gestire la fase di acqusito dei prodotti contenuti nel carrello tramite carta di credito -->

                        <div class="tab-content">

                            <div id="credit-card" class="tab-pane fade show active pt-3">

                                <div class="form-group">

                                    <label for="username">

                                        <h6> Titolare carta </h6>

                                    </label>

                                    <input id="titotlareCC" type="text" name="username" placeholder="Nome proprietario carta" required class="form-control" pattern="^[a-zA-Z ]*$" title="Inserisci del testo">

                                </div>

                                <div class="form-group">

                                    <label for="cardNumber">

                                        <h6> Numero della carta </h6>

                                    </label>

                                    <div class="input-group">

                                        <input id="cardNumber" type="number" name="cardNumber" min=1000000000000000 placeholder="Inserisci un numero di carta valido" class="form-control" pattern="[0-9]{16}" required title="Inserisci 16 cifre">

                                        <div class="input-group-append">

                                            <span class="input-group-text text-muted">

                                                <i class="fab fa-cc-visa mx-1"></i>
                                                <i class="fab fa-cc-mastercard mx-1"></i>
                                                <i class="fab fa-cc-amex mx-1"></i>

                                            </span>
                                        </div>

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-sm-8">

                                        <div class="form-group">

                                            <label>

                                                <span class="hidden-xs">

                                                    <h6>

                                                        Data di scadenza

                                                    </h6>

                                                </span>

                                            </label>

                                            <div class="input-group">

                                                <input id="month" type="number" placeholder="MM" name="" class="form-control" min=1 max=30 required>

                                                <input id="year" type="number" placeholder="YY" name="" class="form-control" min=21 required>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-sm-4">

                                        <div class="form-group mb-4">

                                            <label data-toggle="tooltip" title="Inserisci le tre cifre del CV presenti nel retro della carta">

                                                <h6> CVV <i class="fa fa-question-circle d-inline"> </i></h6>

                                            </label>

                                            <input id="cvv" type="number" required class="form-control" min=100 pattern="[0-9]{3}" title="Inserisci 3 cifre">

                                        </div>

                                    </div>

                                </div>

                                <div class="card-footer">

                                    <button id="confirmOrderCC" name="makeOrderBtn" type="submit" class="subscribe btn btn-primary btn-block shadow-sm">

                                        Conferma l'ordine

                                    </button>
                                </div>

                            </div>

                            <script type="module" src="includes/JavaScript/checkoutETH.js"></script> <!-- Inclusione dello script utiilizzato per la gestione del pagametno dei prodotti nel carrello tramite la blockchain Ethereum e lo Smart Contract -->

                            <div id="ethereum" class="tab-pane fade pt-3">

                                <h4 class="pb-2"> Segui la procedura guidata per completare l'acquisto. </h4>

                                <p>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo"> Paga </button>
                                </p>

                                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

                                    <div class="modal-dialog" role="document">

                                        <div class="modal-content">

                                            <div class="modal-header">

                                                <h5 class="modal-title" id="exampleModalLabel">
                                                    <img src="img/blocks.png" alt="logo blockchain" width="30">
                                                    Inserisci la chiave privata per completare l'acquisto
                                                </h5>

                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>


                                            <div class="modal-body">

                                                <script type="text/javascript" src="includes/JavaScript/passShow.js"></script>

                                                <input id="addressETHlog" name="addressETHlog" type="text" class="form-control" placeholder="Inserisci l'indirizzo" value="<?php echo $_SESSION["AddressETH"] ?>" pattern="[a-zA-Z0-9_.-]{42}" style="margin-bottom: 10px;" disabled>

                                                <div class="row" style="margin-bottom: 20px;">

                                                    <div class="col">
                                                        <input id="gasPriceETH" name="gasPriceETH" type="number" class="form-control" placeholder="Inserisci il gas price" value="" min="1" size="1">
                                                    </div>

                                                    <div class="col">
                                                        <input id="gasLimitETH" name="gasLimitETH" type="number" class="form-control" placeholder="Inserisci il gas limit" value="" min="21000" size="1">
                                                    </div>

                                                </div>

                                                <div class="input-group" id="show_hide_password">

                                                    <input type="password" name="privateKey" class="privateKey form-control" id="exampleInputPassword1" placeholder="Inserisci la chiave privata" pattern="[a-zA-Z0-9_.-]{64}" required>

                                                    <div class="input-group-append">
                                                        <span class="input-group-text"><a href=""><i class="bi bi-eye-slash" aria-hidden="true"></i></a></span>
                                                    </div>

                                                </div>

                                            </div>

                                            <div class="modal-footer">
                                                <button id="closePayement" type="button" class="btn btn-secondary" data-dismiss="modal"> Chiudi </button>
                                                <button onclick="payETHFunction()" name="ethereumPayBtn" type="submit" class="btn btn-primary"> Conferma pagamento </button>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <script type="text/javascript">
                                var AddressETH = <?php echo json_encode($_SESSION["AddressETH"]); ?>; // address passato al file metamask.js
                            </script>

                            <script type="module" src="includes/JavaScript/metamask.js"></script> <!-- Inclusione dello script usato per la gestione del pagamento dei prodotti tramite metamask -->

                            <div id="metamask" class="tab-pane fade pt-3">
                                <h4> Segui la procedura guidata per completare l'acquisto. </h4>

                                <div class="row justify-content-center mb-4">
                                    <button type="button" class="metamaskbtn btn btn-primary"> Paga con MetaMask </button>
                                </div>
                                <div id="status"></div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>

    <br>

<?php
    include_once 'footer.php';
}
?>