/**
 * 
 * @author Vincenzo Fraello (299647) - Lorenzo Di Palma (299636) 
 *
 */

import { connectBlockChain } from './getSmartContractInfo.js';


/**
 * Questa funzione viene utilizzata per verificare che l'utente che sta accedendo
 * alle informazioni del profilo sugli ordini effettuati con lo Smart Contract 
 * e del bilancio sia effettivamente il cliente.
 */

window.verifyIdentity = function () {

    $(document).ready(function () {

        var addressETH = $('#addressETHlog').val();

        var privateKey = $('.privateKey').val();

        if (privateKey.length < 64 || privateKey.length > 64 || privateKey == '') return;

        var addrFromPrK = web3.eth.accounts.privateKeyToAccount(privateKey).address;

        if (addressETH == addrFromPrK) {

            $.ajax({

                url: 'includes/PHP/loginETH.inc.php',

                success: function () {

                    alert('Sei connesso con Ethereum.');

                    window.location.href = "profileETH.php?viewDAppETH=true";
                },

                error: function () {

                    alert('Non sei connesso ad Ethereum.');
                    window.location.href = "profile.php?viewDAppETH=true&&error=wrongprk";
                },

                async: false
            });
        }

        else {

            alert('Non sei connesso ad Ethereum.');
            window.location.href = "profile.php?viewDAppETH=true&&error=wrongprk";
        }
    });
}

/**
 * Il codice JavaScript verrà eseguito solamente dopo che il documento è pronto
 */

$(document).ready(function () {

    // connessione alla blockchain

    var connection = connectBlockChain("includes/PHP/getSmartContractInfo.inc.php");
    var web3 = connection[0];
    var contract = connection[1];

    var addressETH;

    $.ajax({ // richiesta AJAX utilizzata per ottenere l'indirizzo Ethereum del cliente

        url: "includes/PHP/getAddressETH.inc.php",

        success: function (result) {

            addressETH = JSON.parse(result);

            contract.methods.getOrders(addressETH.trim()).call({ from: addressETH }).then(function (content) { // invoca il metodo dello Smart Contract che permette di ottenere gli acquisti effettuati dal cliente

                for (var i = 0; i < content.length; i++) { // inserimento dei dati dell'oggetto JavaScript all'interno di array JavaScript

                    var c = (content[i].isShipped) ? "Spedito" : "Non spedito";

                    t.row.add([

                        content[i].id,
                        content[i].content,
                        new Date(content[i].purchaseDate * 1000).toLocaleString(),
                        parseFloat(web3.utils.fromWei(content[i].eth, 'ether')).toFixed(2),
                        c

                    ]).draw();
                }
            });
        },

        error: function (xhr, thrownError) {

            console.log(xhr.status);
            console.log(thrownError);
            window.location.href = "stmtfailed.php";
        },

        async: true //false
    });

    $('#viewBalanceBtn').click(function () { // azione innescata quando il cliente clicca sul tasto visualizza bilancio

        web3.eth.getBalance(addressETH).then(function (val) { // https://stackoverflow.com/questions/29516390/how-to-access-the-value-of-a-promise

            $("#viewBalanceBtn span").html($("#viewBalanceBtn span").html() == 'Visualizza bilancio' ? 'Nascondi bilancio' : 'Visualizza bilancio');

            var x = document.getElementById('balanceAfter');

            if (x.style.display === 'none') {

                x.style.display = 'block';
            }

            else if (x.style.display === 'block') {

                x.style.display = 'none';
            }

            document.getElementById("balanceAfter").innerHTML = parseFloat(web3.utils.fromWei(val, 'ether')).toFixed(2) + " ETH";
            $('#balanceAfter').append('<img src="img/ethereum-eth-logo-animated.gif" alt="logo ethereum" width="70px">');

        });
    });

    var t = $('#tableShowETH').DataTable({

        "lengthMenu": [1, 2, 3],
        "iDisplayLength": 2,

        "columnDefs": [
            { data: 'id' },
            { data: 'content' },
            { data: 'purchaseDate' },
            { data: 'eth' },
            { data: 'isShipped' }
        ]
    });

    $(document).on('click', '#bindETH', function () {

        if (($('#addressETHlog').val() == '') || ($('.privateKey').val() == '') ||
            ($('.privateKey').val().length) < 64 || ($('.privateKey').val().length) > 64 ||
                ($('#addressETHlog').val().length < 42)) {

            alert('Credenziali fornite non corrette.');
        }
    });

    $('#headerList').click(function (evt) {

        $.ajax({

            url: 'includes/PHP/destroyETH.inc.php',
            
            async: false
        });
    });
});
