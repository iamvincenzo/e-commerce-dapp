/**
 * 
 * @author Vincenzo Fraello (299647) - Lorenzo Di Palma (299636) 
 *
 */


/**
 * Costante che definisce il numero di prodotti mostrati in ogni pagina.
 * 
 * @costant
 * @type {number} 
 * @default
 */

const elementPerPage = 3;


/**
 * Variabile utilizzata per memorizzare i prodotti scelti dal cliente.
 * Ha la funzione di 'carrello'.
 * 
 * @var {array} cart
 */

 var cart = [];


/**
 * Variabile utilizzata per mostrare il numero di prodotti
 * attualmente contenuti nel carrello.
 * 
 * @var {string} cartBadgeCounter Numero di elementi contenuti nel carrello (lunghezza array di oggetti: prodotti)
 */

var cartBadgeCounter = loadCart(); $('.cartNumber').replaceWith(cartBadgeCounter.length);

/**
 * Questa funzione di servizio viene utilizzata per aggiungere un prodotto nel carrello.
 * Inoltre, se il prodotto risulta essere già presente aggiunge la nuova quantità a quella
 * già esistente (duplicati accorpati).
 * 
 * @param {object} myOBJ - rappresenta l'elemento che deve essere aggiunto al carrello
 */

function addToCart(myOBJ) {

    cart = loadCart(); // ottiene (carica) il carrello dal localStorage

    console.log(cart);

    if(!jQuery.isEmptyObject(cart)) { // controlla che il carrello non sia vuoto
      
        for (var i = 0; i < cart.length; i++) { // cicla tutti gli elementi nel carrello per gestire i doppioni accorpando le quantità
    
            if(cart[i].id == myOBJ.id) {  // controllo che l'elemento attuale nel carrello non sia lo stesso di quello che si vuole aggiungere

                var valqnt = String(parseInt(cart[i].qnta) + parseInt(myOBJ.qnta)); // calcolo della quantità aggiornata

                if(cart.length > 2) cart.splice(i, 1); // se ci sono più di due elementi nel carrello si rimuove l'item alla poszione i-esima

                else if (cart.length == 2) { // se ci sono due elementi nel carrello si eliminano nel seguente modo in relazione all'indice di posizione

                    if(i == 0) cart.splice(-2, 1);

                    else cart.splice(-1, 1);
                }

                else if(cart.length == 1) {
                    cart.splice(0, 1);
                }

                cart.push({id:myOBJ.id, qnta:valqnt}); // inserire nel carrello l'elemento con la quantità aggiornata

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
 * Questa funzione di servizio viene utilizzata per inserire i prodotti nelle 
 * cards che verranno mostrate nella pagina home del sito.
 * 
 * @param {number} page È il numero di pagina a cui si vuole andare. 
 */

function fillCards(page) {
    
    $.ajax({ // richiesta AJAX per ottenere i prodotti da mostrare nelle cards.
        
        url: "http://bookstore/bookCard",
        
        data: {myData: {"page": page, "recordPerPage": elementPerPage}}, // occorre inviare al server il numero di pagina attuale e il numero massimo di elementi che una pagina può contenere (Pagination)

        method: 'post',
        
        success: function(result) { // se la richiesta AJAX ha un esito positivo

            var myObj = JSON.parse(result);

            $('#insertCard').empty(); // svuoto la sezione che contiene le cards.

            for(var i = 0; i < myObj.length; i++) { // inserimento delle cards (contenenti i dati relativi ai prodotti) nella pagina
                
                $('#insertCard').append(

                    '<div class="col" style="margin-bottom: 20px;">'+
                    
                        '<div class="cardHeader card h-100">'+    
                        
                            '<img src="' + myObj[i].ImmagineCopertina + '" class="card-img-top" alt="immagine copertina del libro"></img>'+
                            
                            '<div class="cardBody card-body">'+
                            
                                '<h2 class="card-title"> ' + myObj[i].Titolo + ' </h2>' +
                                '<p class="card-text descrCard"> Autore: ' + myObj[i].Autore + ' </p>' +
                                '<p class="card-text descrCard"> Editore: ' + myObj[i].Editore + ' </p>' +
                                '<p class="card-text descrCard"> Formato: ' + myObj[i].NomeFormato + ' </p>' +
                                '<p class="card-text descrCard"> Genere: ' + myObj[i].NomeGenere + ' </p>' +
                                '<p class="card-text descrCard"> Anno: ' + myObj[i].AnnoPubblicazione + ' </p>' +
                                '<p class="card-text"> Quantita: ' + myObj[i].Quantita + ' </p>' +
                                '<p class="price' + i + '" class="card-text"> Prezzo: € ' + myObj[i].Prezzo.toFixed(2) + ' </p>');

                                if(myObj[i].Quantita > 0) { // se il prodotto è disponibile allora lo si può aggiungere al carrello ma la massima quantità selezionabile è quella effettivamente disponibile

                                    $('.price' + i).append(
                                        '<input id="qnt' + myObj[i].IDLibro + '" class="userQuantity" placeholder="Inserisci quantità" min="1" max="' + myObj[i].Quantita + '" type="number" name="userQuantity" style="width: 180px;" required/> ' +
                                        '<br> <br>' +
                                        '<input type="submit" name="addToCartBtn" class="addToCartBtn btn btn-primary" data-prodid="' + myObj[i].IDLibro + '" value="Aggiungi al carrello"/>' // data-prodid è una stringa standard che viene usata per memorizzare l'id del prodotto
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
                        
                        '</div>'+
                    
                    '</div>'
                );
            }
        },

        error: function (xhr, thrownError) {

            console.log(xhr.status);
            console.log(thrownError);
            window.location.href = "stmtfailed.php";
        },

        async: true
    });
}


/**
 * Il codice JavaScript verrà eseguito solamente dopo che il documento è pronto.
 */

$(document).ready(function() {

    $.ajax({ // richiesta AJAX per ottenere il numero totale di prodotti disponibili nel database dell'e-Commerce

        url: 'http://bookstore/totalPages',

        success: function(response) { // se la richeista AJAX ha avuto esito positivo

            totP = parseInt(response); 

            $(function () { // Pagination

                window.pagObj = $('#pagination').twbsPagination({

                    totalPages: Math.ceil(totP/elementPerPage),

                    visiblePages: Math.ceil(totP*0.3),

                    onPageClick: function (event, page) {

                        //console.info(page + ' (from options)');

                        fillCards(page); // richiamo la funzione che si occupa di inserire i prodotti nelle cards che verranno mostrate nella home del sito
                    }

                }).on('page', function (event, page) {

                    console.info(page + ' (from event listening)');
                });
            });
        },
        
        error: function() {

            console.log(xhr.status);
            console.log(thrownError);
            window.location.href = "stmtfailed.php";
        },

        async: false
    });

    $(document).on('click', '.addToCartBtn', function() { // azione che si innesca quando viene premuto il tasto aggiungi al carrello

        prodID = $(this).data('prodid'); // ottengo l'id del prodotto della card selezionata

        var msg = "Inserisci una quantità valida: min: 1, max: " + $('#qnt' + prodID).attr('max');

        if(($('#qnt' + prodID).val().trim() != '') && ($('#qnt' + prodID).val().trim() <= parseInt($('#qnt' + prodID).attr('max'))) && (parseInt($('#qnt' + prodID).val()) > '0')) { // se la quantità di prodotto desiderata (inserita) è valida
            
            $.ajax({ // richiesta AJAX usata per controllare che l'utente sia loggato (solo gli utenti registarti possono fare acquisti)
            
                url: "includes/PHP/isLogged.inc.php", 
                
                success: function(result) { 
                   
                    if(!JSON.parse(result)) {

                        window.location.href ='login.php'; // se l'utente non è loggato viene rediretto alla pagina di login
                    }

                    else { // se l'utente è già loggato
                        
                        qnt = $('#qnt' + prodID).val();
                
                        addToCart({id:prodID, qnta:qnt}); // richiamo la funzione che aggiunge al carrello il l'id del prodotto selezionato con la relativa quantità desiderata (oggetto)
                        
                        $('#qnt' + prodID).val(''); // svuota il campo quantità

                        cartBadgeCounter = loadCart(); 

                        cartBadgeCounter = loadCart(); $('.cartNumber').replaceWith(cartBadgeCounter.length);

                        //window.location.href ='index.php'; // redirezione alla pagina home del sito (utile per aggiornare il numero di prodotti contenuti all'interno del carrello)
                    }
                },

                async: false
            });            
        }

        else { // quantità inertia non valida --> messaggio di errore

            alert(msg);

            $('#qnt' + prodID).val('');
        }
    });
});
