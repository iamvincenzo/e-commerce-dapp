<?php
    session_start(); // avvio della sessione --> cioè l'insieme di accessi dello stesso utente a pagine web diverse senza richiedere ogni volta le credenziali dell'utente.
    
    require_once 'includes/PHP/dbh.inc.php';
    require_once 'header.php'
?>

    <title> Home </title>

    <script src="includes/JavaScript/twbs-pagination-master/jquery.twbsPagination.js" type="text/javascript"></script> <!-- Plugin utilizzato per gestire il pagination dei prodotti nella pagina  -->
        
    <div class="container">

        <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">

            <ol class="carousel-indicators">

                <li data-target="#carouselExampleCaptions" data-slide-to="0" class="active"></li>

                <li data-target="#carouselExampleCaptions" data-slide-to="1"></li>

            </ol>

            <div class="carousel-inner">

                <div class="carousel-item active">

                    <img src="img/libroCarousel1.jpg" class="d-block w-100" alt="Immagine di un libro">

                    <div class="carousel-caption d-none d-md-block">

                        <h5> I nostri prodotti sono i migliori </h5>

                        <p> Registrati e tieniti sempre aggiornato sulle novità pù interessanti </p>

                    </div>

                </div>

                <div class="carousel-item">

                    <img src="img/libroCarousel2.jpg" class="d-block w-100" alt="Immagine di un libro">

                    <div class="carousel-caption d-none d-md-block">

                        <h5> I nostri prodotti sono i migliori </h5>

                        <p> Bisogna sempre essereprudenti con i libri e con ciò che contengono, perché le parole hanno il
                            potere di cambiarci </p>

                    </div>

                </div>

            </div>

            <a class="carousel-control-prev" href="#carouselExampleCaptions" role="button" data-slide="prev">

                <span class="carousel-control-prev-icon" aria-hidden="true"></span>

                <span class="sr-only"> Precedente </span>

            </a>

            <a class="carousel-control-next" href="#carouselExampleCaptions" role="button" data-slide="next">

                <span class="carousel-control-next-icon" aria-hidden="true"></span>

                <span class="sr-only"> Successiva </span>

            </a>

        </div>

    </div>

    <br>

    <script type="text/javascript" src="includes/JavaScript/loadCardProducts.js"></script> <!-- Inclusione dello script utilizzato per gestire il carrello e la visualizzazione dei prodotti nella pagina -->

    <p id="appendAlert"></p>

    <div class="container">

        <div id="insertCard" class="row row-cols-1 row-cols-md-3 g-4 mb-3"></div>

    </div>

    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center" id="pagination"></ul>
    </nav>


    <div class="testi container">

        <div class="row">

            <div class="col-sm" style="text-align: justify;  text-justify: inter-word;">

                <h2 class="titoloParagrafi"> Presentazione </h2>

                <p>
                    Lorvincfralma permette di effettuare ricerche all'interno del proprio database
                    utilizzando diversi
                    parametri, permettendo così all'utente di affinare la ricerca e di vedere
                    visualizzati solamente quei
                    titoli che soddisfano i criteri di ricerca da lui imposti. E' possibile effettuare
                    ricerche sia su
                    titoli in catalogo che fuori catalogo, permettendo così un servizio di ricerca
                    bibliografica molto utile
                    per tutti.
                </p>

            </div>

            <div class="col-sm" style="text-align: justify;  text-justify: inter-word;">

                <h2 class="titoloParagrafi"> I nostri obiettivi </h2>

                <p>
                    Lorvincfralma nasce per fornire al mondo universitario ed a tutti gli internauti,
                    una scelta vastissima
                    di libri e riviste specializzate e vuole differenziarsi per l’accurato servizio di
                    ricerca dei titoli
                    più difficili da reperire sul mercato. Offre, a questo proposito, il servizio di
                    ORDINE PERSONALIZZATO
                    che prevede la ricerca sul mercato di quanto non è stato trovato all’interno del
                    nostro catalogo.
                </p>

            </div>

            <div class="col-sm" style="text-align: justify;  text-justify: inter-word;">

                <h2 class="titoloParagrafi"> 18app & Carta Docente </h2>

                <p>
                    Lorvincfralma è partner ufficiale dell'iniziativa Carta del Docente e 18app Bonus
                    Cultura. È possibile
                    acquistare, utilizzando Bonus Cultura e 18app, solo prodotti venduti e spediti da
                    IBS: libri e
                    audiolibri, libri in inglese, eBook, eBook in inglese, CD musicali (solo per i nati
                    nel 1999). Nel sito
                    Libri puoi trovare tutte le novità, libri universitari e professionali, libri per
                    ragazzi, saggi e
                    manuali.
                </p>

            </div>

        </div>

    </div>

    <br> <br>

<?php
    include_once 'footer.php';
?>