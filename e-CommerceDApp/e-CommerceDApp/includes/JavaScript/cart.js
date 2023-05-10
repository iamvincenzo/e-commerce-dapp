/**
 * 
 * @author Vincenzo Fraello (299647) - Lorenzo Di Palma (299636) 
 *
 */


/**
 * Variabile utilizzata per memorizzare i prodotti scelti dal cliente.
 * Ha la funzione di 'carrello'.
 * 
 * @var {array} myObj
 */

var myOBJ;

/**
 * Il codice JavaScript verrà eseguito solamente dopo che il documento è pronto
 */

$(document).ready(function () {

    $('.cartNumber').replaceWith(JSON.parse(localStorage.getItem("cart") || "[]").length); // Numero di elementi contenuti nel carrello (lunghezza array di oggetti: prodotti)

    $(document).on('click', '#deleteAllFromCartBtn', function () { // azione che si innesca quando viene premuto il tasto svuota il carrello

        localStorage.clear();
    });

    myObj = JSON.parse(localStorage.getItem("cart") || "[]"); // preleva l'array di oggetti (prodotti) contenuti nel localStorage

    if (!jQuery.isEmptyObject(myObj)) { // se il carrello non è vuoto 

        $.post("http://bookstore/infoProductsCart", // richiesta AJAX usata per ottenere tutti i parametri dei prodotti inseriti nel carrello (nel carrello è contenuto solo l'id del prodotto)

            {
                myData: myObj,
            },

            function (response) { // se l'AJAX ha avuto esito positivo

                var content = JSON.parse(response);

                var ultimo = 0;

                var totale = 0;

                for (var i = 0; i < content.length; i++) { // inserimento dei dettagli sui prodotti del carrello nella tabella riassuntiva

                    for (var j = 0; j < myObj.length; j++) {

                        if (myObj[j].id == content[i].IDLibro) {

                            var tmp = i + ultimo

                            totale = totale + (myObj[j].qnta * content[i].Prezzo);

                            $('#cartRow').append('<tr id="row' + tmp + '"></tr>');

                            $('#row' + tmp).html(

                                "<td>" + content[i].Titolo + "</td>" +
                                "<td>" + content[i].Autore + "</td>" +
                                "<td>" + content[i].Editore + "</td>" +
                                "<td>" + myObj[j].qnta + "</td>" +
                                "<td>" + content[i].Prezzo.toFixed(2) + "</td>" +
                                "<td>" + (myObj[j].qnta * content[i].Prezzo).toFixed(2) + "</td>" +
                                "<td style='width:24%'>" +
                                "<a class='trashBtn' data-prodid='" + myObj[j].id + "' href=''>" +
                                "<span class='text-danger'>" +
                                "<i class='bi bi-trash'></i>" +
                                "</span>" +
                                "</a>" +
                                "</td>"
                            );

                            ultimo += content.length;

                            break;
                        }
                    }
                }

                localStorage.setItem("tot", String(totale)); // salva nel localStorage il totale spesa utile nella fase di acquisto

                tmp = tmp + 1;

                $('#cartRow').append(

                    '<tr id="row' + tmp + '">' +

                    '<td colspan="5" align="right">' +
                    'Totale' +
                    '</td>' +

                    '<td> €' +
                    totale.toFixed(2) +
                    '</td>' +

                    '<td>' +

                    '<a id="deleteAllFromCartBtn" class="bi bi-trash btn btn-danger" href="">' +
                    'Svuota carrello' +
                    '</a>' +

                    '&nbsp;&nbsp;' +

                    '<a class="bi bi-cart-check-fill btn btn-primary" href="checkout.php">' +
                    'Check Out' +
                    '</a>' +

                    '</td>' +


                    '</tr>'
                );

            }).fail(function (response) {

                window.location.href = "stmtfailed.php";
            });
    }

    $(document).on('click', '.trashBtn', function () { // azione che si innesca quando viene premuto il tasto elimina prodotto dal carrello

        var prodID = $(this).data('prodid');

        console.log(prodID);

        for (var i = 0; i < myObj.length; i++) {

            if (myObj[i].id == prodID) { // se il prodotto è all'interno del carrello

                if (myObj.length > 2) myObj.splice(i, 1); // se ci sono più di due elementi nel carrello si rimuove l'item alla poszione i-esima

                else if (myObj.length == 2) { // se ci sono due elementi nel carrello si eliminano nel seguente modo in relazione all'indice di posizione

                    if (i == 0) myObj.splice(-2, 1);

                    else myObj.splice(-1, 1);
                }

                else if (myObj.length == 1) {
                    myObj.splice(0, 1);
                }

                localStorage.setItem("cart", JSON.stringify(myObj));  // salvo il carrello all'interno di localStorage

                return; // esco dalla funzione
            }
        }
    });
});
