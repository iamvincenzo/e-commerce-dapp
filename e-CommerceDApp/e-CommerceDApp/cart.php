<?php
    session_start(); // avvio della sessione --> cioè l'insieme di accessi dello stesso utente a pagine web diverse senza richiedere ogni volta le credenziali dell'utente.

    if(isset($_SESSION["Email"])) { // se l'utente è loggato

        include_once 'header.php';
?>

    <title> Carrello </title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

    <script type="text/javascript" src="includes/JavaScript/cart.js"></script> <!-- Inclusione dello script utilizzato per mostrare i prodotti contenuti nel carrello (inseriti dal cliente) -->

    <br>

    <div class="container">

        <div class="alert alert-secondary" role="alert">
            <h2></h2>
            <h2> Riepilogo ordine </h2>
        </div>

        <br>
        <div class="table-responsive">

            <table id="cartTable" class="table">

                <thead class="table-dark">
                    <tr>
                        <th scope="col"> Titolo </th>
                        <th scope="col"> Autore </th>
                        <th scope="col"> Editore </th>
                        <th scope="col"> Quantità </th>
                        <th scope="col"> Prezzo unitario </th>
                        <th scope="col"> Totale </th>
                        <th width="col"> Azione </th>
                    </tr>
                </thead>

                <tbody id="cartRow"></tbody>

            </table>

        </div>

    </div>

    </div>

    <br> <br>

<?php
        include_once 'footer.php';
    }
?>
