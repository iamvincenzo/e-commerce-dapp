/**
 * 
 * @author Vincenzo Fraello (299647) - Lorenzo Di Palma (299636) 
 *
 */


/**
 * Il codice JavaScript verrà eseguito solamente dopo che il documento è pronto
 */

$(document).ready(function () {

    var x = document.getElementById('tableShow'); 

    x.style.display = 'none';

    var y = document.getElementById('editProfileShow');

    var z = document.getElementById('droneImg');

    var w = document.getElementById('profileETH');

    var t;

    $('#viewHistoryOrders').click(function () {     

        if (!$.fn.DataTable.isDataTable('#tableShow')) { // se la dataTable non è stata istanziata --> viene istanziata

            t = $('#tableShow').DataTable({

                "lengthMenu": [5, 10, 15],
                "iDisplayLength": 5,

                "columnDefs": [
                    { data: 'id' },
                    { data: 'title' },
                    { data: 'author' },
                    { data: 'publisher' },
                    { data: 'purchaseDate' },
                    { data: 'isShipped' }
                ]
            });
        }

        else {

            t.clear();

            window.location.href = "profile.php?viewGif=true";
        }

        if (x.style.display === 'none') { // se ka tabella è nascosta --> si mostra la tabella e si nasconde il resto

            if (y != null) y.style.display = 'none';

            else if (z != null) z.style.display = 'none';

            else if (w != null) w.style.display = 'none';

            x.style.display = 'block';
        }

        else if (x.style.display === 'block') {

            x.style.display = 'none';
        }

        $.ajax({ // richiesta AJAX effettuata per inserire i prodotti all'interno della tabella

            url: 'http://bookstore/viewOrders/' + id,

            success: function (response) {

                console.log("entro");

                console.log(response);

                content = JSON.parse(response);

                for (i = 0; i < content.length; i++) {

                    var c = (content[i].isShipped) ? "Spedito" : "Non spedito";

                    t.row.add([
                        content[i].id,
                        content[i].title,
                        content[i].author,
                        content[i].publisher,
                        content[i].purchaseDate,
                        c
                    ]).draw();
                }
            },

            error: function (xhr, thrownError) {
                console.log(xhr.status);
                console.log(thrownError);
                window.location.href = "stmtfailed.php";
            },

            async: true //false
        });
    });
});
