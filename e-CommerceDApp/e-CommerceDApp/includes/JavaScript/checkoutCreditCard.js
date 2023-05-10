/**
 * 
 * @author Vincenzo Fraello (299647) - Lorenzo Di Palma (299636) 
 *
 */


/**
 * Questa funzione di servizio viene utilizzata per controllare che il cliente
 * abbia compilato tutti i campi del form.
 * 
 * @returns {boolean} Ritorna l'esito del controllo
 */

function emptyInput() {

    if($('#titotlareCC').val().trim() == '' || $('#cardNumber').val().trim() == '' || 
        $('#month').val().trim() == '' || $('#year').val().trim() == '' || 
            $('#cvv').val().trim() == '') return true;

    else return false;
}


/**
 * Questa funzione di servizio viene utilizzta per effettuare l'acuqisto
 * dei prodotti che sono contenuti nel carrello.
 */

function buy() {

    var myObj = JSON.parse(localStorage.getItem("cart") || "[]"); // estrazione dal localStorage del carrello (in cui sono salvati i prodotti)

    if(Object.keys(myObj).length === 0) { // se nel carrello non c'è nulla non fa attivare la procedura di acquisto

        alert('Non puoi fare acquisti nulli.');
        
        return;
    }

    $.post("http://bookstore/buyProducts/" + IDCliente, //  richiesta AJAX per verificare la disponibilità dei prodotti contenuti nel carrello

        {
            myData: myObj,
        },

        function (response) {

            var content = JSON.parse(response);

            // LA RISPOSTA CONTIENE I LIBRI E GLI ESITI
                // COSI LE COSE CHE SI SONO POTUTE DECREMENTARE SI TOLGONO DAL CARRELLO E SI INSERISCONO NELLA TRANSAZIONE
                    // LE COSE CHE NON SI SONO POTUTE DECREMENTARE SI LASCIANO NEL CARRELLO E SI AVVISA IL CLIENTE

            for(var i = 0; i < content.length; i++) { // per ogni elemento contenuto nella risposta fornita dal server

                myObj = JSON.parse(localStorage.getItem("cart") || "[]");         

                if(content[i].Esito) { // se l'esito dell'acuqisto del prodotto i-esimo è positiva
               
                    for(var j = 0; j < myObj.length; j++) { // si elimina il prodotto dal carrello
                    
                        if(myObj[j].id == content[i].IDLibro) {

                            if(myObj.length > 2) myObj.splice(j, 1); // se ci sono più di due elementi nel carrello si rimuove l'item alla poszione i-esima
        
                            else if (myObj.length == 2) { // se ci sono due elementi nel carrello si eliminano nel seguente modo in relazione all'indice di posizione
        
                                if(j == 0) myObj.splice(-2, 1);
        
                                else myObj.splice(-1, 1);
                            }
        
                            else if(myObj.length == 1) myObj.splice(0, 1);
        
                            localStorage.setItem("cart", JSON.stringify(myObj));  // salvo il carrello all'interno di localStorage
        
                            break; // esco dalla funzione
                        }         
                    }
                }                  
            }

            if(Object.keys(myObj).length === 0) { // se il carrello dopo l'acquisto risulta essere vuoto

                alert("Tutti i prodotti sono stati acquistati.");

                window.location.href = "profile.php?viewGif=true";
            }

            else { // se non si sono potuti acquistare alcuni prodotti si avvisa il cliente

                alert("Non è stato possibile effettuare l\'acquisto di alcuni o tutti i prodotti. Controlla il carrello.");

                window.location.href = "cart.php";
            }

    }).fail(function(response) {

        window.location.href = "stmtfailed.php";
    });
}


/**
 * Il codice JavaScript verrà eseguito solamente dopo che il documento è pronto
 */

$(document).ready(function() {

    $(document).on('click', '#confirmOrderCC', function() { // azione innescata quando il cliente clicca il tasto di conferma del pagamento

        var d = new Date();

        var curr_year = String(d.getFullYear());

        // controlli sulla validità dei dati immessi nel form

        if(emptyInput()) { // si richiama la funzione per controllare che tutti i campi siano stati compilati

            alert('Devi compilare tutti i campi.');
            return;
        }

        else if(!$('#titotlareCC').val().match('^[a-zA-Z]+([ ][a-zA-Z]+)*$')) {

            alert('Il titotale della carta deve essere composto da sole lettere.');
            return;
        }

        else if(($('#cardNumber').val().length != 16  || parseInt($('#cardNumber').val()) < 0)) {

            alert('Il numero della carta può essere composto al più da 16 cifre.');
            return;
        }

        else if(parseInt($('#month').val()) < 1 ||  parseInt($('#month').val()) > 12 || $('#month').val() == '') {

            alert("Mese non valido");
            
        }

        else if(parseInt($('#year').val()) < curr_year.substring(2, 4) || $('#year').val() == '') {

            alert('Anno non valido.');
            return;
        }

        else if($('#cvv').val().length != 3 || parseInt($('#cvv').val()) < 0) {

            alert('Il CVV può essere composto al più da 3 cifre.');
            return;
        }

        else { // Avvio della fase di acquisto con AJAX
            
            buy(); // richiamo la funzione che si occupa dell'acquisto dei prodotti se il processo di inseriemnto di dati della carta va a buon fine

            return;
        }
    });
});
