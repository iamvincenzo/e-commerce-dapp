/**
 *
 *  @author Vincenzo Fraello (299647) - Lorenzo Di Palma (299636) 
 *
 */


/**
 * Variabile utilizzata per memorizzare i prodotti scelti dal cliente.
 * Ha la funzione di 'carrello'.
 * 
 * @var {array} cart
 */

var cart = [];


/**
 * Questa funzione di servizio viene utilizzata per aggiungere un prodotto nel carrello.
 * Inoltre, se il prodotto risulta essere già presente aggiunge la nuova quantità a quella
 * già esistente (duplicati accorpati).
 * 
 * @param {object} myOBJ - rappresenta l'elemento che deve essere aggiunto al carrello
 */

function addToCart(myOBJ) {

    cart = loadCart(); // ricarica il carrello dal localStorage

    if (!jQuery.isEmptyObject(cart)) { // controlla che il carrello non sia vuoto

        for (var i = 0; i < cart.length; i++) { // cicla tutti gli elementi nel carrello per gestire i doppioni accorpando le quantità

            if (cart[i].id == myOBJ.id) {  // controllo che l'elemento attuale nel carrello non sia lo stesso di quello che si vuole aggiungere

                var valqnt = String(parseInt(cart[i].qnta) + parseInt(myOBJ.qnta)); // calcolo della quantità aggiornata

                if (cart.length > 2) cart.splice(i, 1); // se ci sono più di due elementi nel carrello si rimuove l'item alla poszione i-esima

                else if (cart.length == 2) { // se ci sono due elementi nel carrello si eliminano nel seguente modo in relazione all'indice di posizione

                    if (i == 0) cart.splice(-2, 1);

                    else cart.splice(-1, 1);
                }

                else if (cart.length == 1) {
                    cart.splice(0, 1);
                }

                cart.push({ id: myOBJ.id, qnta: valqnt }); // inserire nel carrello l'elemento con la quantità aggiornata

                localStorage.setItem("cart", JSON.stringify(cart));  // salvo il carrello all'interno di localStorage

                return; // esco dalla funzione
            }
        }

        // se arrivo quà allora non ho trovato prodotti simili già inseriti

        cart.push(myOBJ); // inserisco il prodotto nel carrello

        localStorage.setItem("cart", JSON.stringify(cart)); // salvo il carrello all'interno di localStorage
    }

    else { // la prima volta entro quà perchè il carrello è vuoto

        cart.push(myOBJ); // inserisco l'oggetto nel carrello

        localStorage.setItem("cart", JSON.stringify(cart));  // salvo il carrello all'interno di localStorage
    }
}


/**
 * Questa funzione di servizio viene utilizzata per estrapolare 
 * e fornire il carrello (array di oggetti: prodotti scelti dal cliente) 
 * dal local storage.
 * 
 * N.B: si è coluto utilizzare il localStorage per memorizzare i prodotti scelti dal cliente 
 * in maniera permanente così da non perdere le sue scelte in caso di ricaricamento della pagina.
 * 
 * @returns {array} Array che contiene i prodotti scelti dal cliente
 */

function loadCart() { // funzione usata per recuperare il carrello dal localStorage

    return JSON.parse(localStorage.getItem("cart") || "[]");
}


/**
 * Il codice JavaScript verrà eseguito solamente dopo che il documento è pronto
 */

$(document).ready(function () {

    var cartBadgeCounter = loadCart(); $('.cartNumber').replaceWith(cartBadgeCounter.length);

    $(document).on('click', '.addToCartBtn', function () {

        prodID = $(this).data('prodid');

        if (($('#qnt' + prodID).val() != '') && ($('#qnt' + prodID).val() <= parseInt($('#qnt' + prodID).attr('max'))) && (parseInt($('#qnt' + prodID).val()) > '0')) {
            
            $.ajax({ // richiesta AJAX utilizzata per controllare che l'untente sia loggato

                url: "includes/PHP/isLogged.inc.php",

                success: function (result) {

                    if (!JSON.parse(result)) {

                        window.location.href = 'login.php';
                    }

                    else {

                        qnt = $('#qnt' + prodID).val();

                        addToCart({ id: prodID, qnta: qnt });

                        $('#qnt' + prodID).val('');

                        window.location.href = 'index.php';
                    }
                },

                async: false
            });
        }

        else {

            alert("Errore: inserisci una quantità valida: min: 1, max: " + $('#qnt' + prodID).attr('max'));

            $('#qnt' + prodID).val('');
        }
    });

    var x = document.getElementById('searchArea');

    if (x.style.visibility === 'hidden') {

        x.style.visibility = 'visible';
    }

    $('#searchSectionLink').remove();

    $(document).on('click', '#searchbtn', function () {

        if($('#searchField').val().trim() !== '') search();

        else alert("Errore: compila il campo di ricerca");
        
    });

    $(document).keypress(function (event) {
        
        var keycode = (event.keyCode ? event.keyCode : event.which);
        
        if (keycode == '13') {
            
            if($('#searchField').val().trim() !== '') search();

            else alert("Errore: compila il campo di ricerca");
        }
    });

    function search() {

        $.post("http://bookstore/searchBook", 

            {
                myData: $('#searchField').val(),
            },

            function (result) {

                $('#insertCard').empty();

                $('#startIMG').remove();

                console.log(result);

                var myObj = JSON.parse(result);

                console.log(myObj);

                if (Object.keys(myObj).length === 0) { // la ricerca non produce risultati

                    $('#insertCard').append(

                        '<div class="alert alert-warning" role="alert" style="height: 60px;">' +
                        'Siamo spiacenti, nessun risultato.' +
                        '</div>' +

                        '<div class="row justify-content-md-center">' +
                        '<img src="img/searchNotFound.gif" class="img-fluid" alt="Immagine di risultato di ricerca non trovato"/>' +
                        '</div>'
                    );
                }

                else {

                    for (var i = 0; i < myObj.length; i++) { // la ricerca ha prodotto risultati

                        $('#insertCard').append(

                            '<div class="col" style="margin-bottom: 20px;">' +

                            '<div class="cardHeader card h-100">' +

                            '<img src="' + myObj[i].ImmagineCopertina + '" class="card-img-top" alt="immagine copertina del libro"></img>' +

                            '<div class="cardBody card-body">' +

                            '<h2 class="card-title"> ' + myObj[i].Titolo + ' </h2>' +
                            '<p class="card-text descrCard"> Autore: ' + myObj[i].Autore + ' </p>' +
                            '<p class="card-text descrCard"> Editore: ' + myObj[i].Editore + ' </p>' +
                            '<p class="card-text descrCard"> Formato: ' + myObj[i].NomeFormato + ' </p>' +
                            '<p class="card-text descrCard"> Genere: ' + myObj[i].NomeGenere + ' </p>' +
                            '<p class="card-text descrCard"> Anno: ' + myObj[i].AnnoPubblicazione + ' </p>' +
                            '<p class="card-text"> Quantita: ' + myObj[i].Quantita + ' </p>' +
                            '<p class="price' + i + '" class="card-text"> Prezzo: € ' + myObj[i].Prezzo.toFixed(2) + ' </p>');

                      
                            if (myObj[i].Quantita > 0) { // se il prodotto è disponibile allora lo si può aggiungere al carrello ma la massima quantità selezionabile è quella effettivamente disponibile

                                $('.price' + i).append( //<form action="#insertCard"></form>
                                    '<input id="qnt' + myObj[i].IDLibro + '" class="userQuantity" placeholder="Inserisci quantità" min="1" max="' + myObj[i].Quantita + '" type="number" name="userQuantity" style="width: 180px;" required/> ' +
                                    '<br> <br>' +
                                    '<input type="submit" name="addToCartBtn" class="addToCartBtn btn btn-primary" data-prodid="' + myObj[i].IDLibro + '" value="Aggiungi al carrello"/>'
                                );
                            }

                            else { // se il prodotto non è disponibile allora si disabilitano i tasti per poter aggiugnere il prodotto al carrello

                                $('.price' + i).append(
                                    '<input id="qnt' + myObj[i].IDLibro + '" class="userQuantity" placeholder="Inserisci quantità" min="1" max="' + myObj[i].Quantita + '" type="number" name="userQuantity" style="width: 180px;" required disabled/> ' +
                                    '<br> <br>' +
                                    '<input type="submit" name="addToCartBtn" class="addToCartBtn btn btn-primary" data-prodid="' + myObj[i].IDLibro + '" value="Aggiungi al carrello" disabled/>'
                                );
                            }

                        $('#insertCard').append(

                            '</div>' +

                            '</div>' +

                            '</div>'
                        );
                    }
                }

                $('#searchField').val('');

            }).fail(function (response) {
                window.location.href = "stmtfailed.php";
            });
    }

});
