/**
 * 
 * @author Vincenzo Fraello (299647) - Lorenzo Di Palma (299636) 
 *
 */

import { connectBlockChain } from './getSmartContractInfo.js';


/**
 * Connection to blockchain.
 */

var connection = connectBlockChain("../includes/PHP/getSmartContractInfo.inc.php");
var web3 = connection[0];
var contract = connection[1];
var addressOwner = connection[2];

var i;

var phpVars;


/**
 * Questa funzione di servizio viene utilizzata per ottenere
 * il bilancio del contratto; ovvero il quantitativo di Ether che
 * sono stati versati nello Smart Contract.
 */

function _contractBalance() {

    contract.methods.contractBalance().call({ from: addressOwner }).then(function (content) {

        document.getElementById("balanceSmartContract").innerHTML = "Bilancio Smart Contract: " + parseFloat(web3.utils.fromWei(content, 'ether')).toFixed(2) + " ETH";
        $('#balanceSmartContract').append('<img src="../img/ethereum-eth-logo-animated.gif" alt="logo ethereum" width="70px">'); // ethereum.png

        if (parseFloat(web3.utils.fromWei(content, 'ether')).toFixed(2) == 0.00) $('#ritirabtn').prop('disabled', true);
    });
}


/**
 * Questa funzione di servizio viene utilizzata per ottenere
 * il bilancio del possessore del contratto; ovvero il quantitativo
 * di Ether che possiede. 
 */

function _ownerBalance() {

    web3.eth.getBalance(addressOwner).then(function (val) { // soluzione: https://stackoverflow.com/questions/29516390/how-to-access-the-value-of-a-promise

        document.getElementById("balanceShop").innerHTML = "Bilancio negozio: " + parseFloat(web3.utils.fromWei(val, 'ether')).toFixed(2) + " ETH";
        $('#balanceShop').append('<img src="../img/ethereum-eth-logo-animated.gif" alt="logo ethereum" width="70px">'); // ethereum.png

    });
}


/**
 * Il codice JavaScript verrà eseguito solamente dopo che il documento è pronto.
 */

$(document).ready(function () {

    $(document).on('click', '#viewBalanceBtn' , function () { // azione che si innesca quando l'impiegato clicca il tasto visualizza bilancio

        var x = document.getElementById('balanceEmp');

        if (x.style.display === 'none') {

            $("#viewBalanceBtn span").html("Nascondi bilancio");

            console.log("entro");

            x.style.display = 'block';
        }

        else if (x.style.display === 'block') {

            $("#viewBalanceBtn span").html("Visualizza bilancio");

            x.style.display = 'none';
        }

        _ownerBalance(); // richiamo la funzione per ottenere il bilancio del possessore del contratto 

        _contractBalance(); // richiamo la funzione per ottenere il bilancio dello Smart Contract 
    });

    $('#ritirabtn').click(function () { // azione che si innesca quando l'impiegato clicca sul bottone ritira

        contract.methods.withdraw().send({ from: addressOwner }).then(function (content) { // richiamo il metodo (che può essere richiamato solo dal possessore del contratto) per versare gli ether dal conto del contratto a quello del possessore del contratto

            alert("Trasferimento ether eseguito con successo.");

            _ownerBalance(); // aggiorno i valori del bilancio dell'impiegato

            _contractBalance(); // e del contratto
        });

    });


    var t = $('#tableShow').DataTable({

        "lengthMenu": [1, 2, 3],
        "iDisplayLength": 2,
        
        "columnDefs": [
            { data: 'id' },
            { data: 'owner' },
            { data: 'content' },
            { data: 'eth' },
            { data: 'purchaseDate' },
            { data: 'isShipped' },
            { data: 'action' }
        ]
    }); 
    
    $.ajax({ // richiesta AJAX effettuata per ottenere gli indirizzi dell'account ethereum dei clienti per poter visualizzare il loro storico ordini

        url: "http://bookstore/userAddressETH",

        success: function (response) {

            phpVars = JSON.parse(response);

            console.log(phpVars);

            for (i = 0; i < phpVars.length; i++) {

                if (phpVars[i].trim() != '') {

                    contract.methods.getOrders(phpVars[i].trim()).call({ from: addressOwner }).then(function (content) { // invocazione del metodo del contratto che permette di ottenere gli ordini dei vari clienti

                        for (var j = 0; j < content.length; j++) {

                            var c = (content[j].isShipped) ? "Spedito" : "Non spedito";

                            var a = (content[j].isShipped) ? '<button type="submit" id="spediscibtn" name="submit" class="btn btn-primary" disabled> Spedisci </button>' : '<button type="submit" id="spediscibtn" name="submit" class="btn btn-primary"> Spedisci </button>';

                             t.row.add([
                                
                                content[j].id, 
                                content[j].owner, 
                                content[j].content, 
                                parseFloat(web3.utils.fromWei(content[j].eth, 'ether')).toFixed(2),
                                new Date(content[j].purchaseDate * 1000).toLocaleString(), 
                                c, 
                                a
                            ]).draw();
                        }
                    })
                }
            }
        },

        error: function (xhr, thrownError) {
            
            console.log(xhr.status);
            console.log(thrownError);
            window.location.href = "../stmtfailed.php";
        },

        async: true //false
    });
    
    $('#tableShow tbody').on('click', '#spediscibtn', function () { // azione che si innesca quando l'impiegato clicca il bottone spedisci (Blockchain)

        var data = t.row($(this).parents('tr')).data();
        
        console.log(data);

        contract.methods.shipOrder(data['1'] , parseInt(data['0'])).send({ from: addressOwner }).then(function (content) { // invocazione del metodo dello Smart Contract che permette di modificare lo stato dell'ordine

            alert("Spedizione effettuata con successo.");

            window.location.href = 'dashboard.php?viewOrderETH=true';

        }).catch(function (tx) {

            console.log(tx);

            alert("Spedizione non riuscita")
        });
    });


    var t2 = $('#shipTable').DataTable({

        "lengthMenu": [5, 10, 15],
        "iDisplayLength": 5,
        
        "columnDefs": [
            { data: 'id' },
            { data: 'title' },
            { data: 'author' },
            { data: 'publisher' },
            { data: 'year' },
            { data: 'format' },
            { data: 'language' },
            { data: 'quantity' },
            { data: 'action' }
        ]
    }); 

    $.ajax({ // richiesta AJAX effettuata per ottenere gli ordini che devono essere spediti (database di sistema)

        url: "http://bookstore/viewOrdersToShip",  

        success: function (response) {

            var content = JSON.parse(response);

            console.log(content);

            for (i = 0; i < content.length; i++) {

                var a = '<input style="margin-top: 6px;" type="submit" id="sendOrder" name="sendOrder" class="btn btn-primary" value="Spedisci" />';

                t2.row.add([
                    content[i].id,
                    content[i].title,
                    content[i].author,
                    content[i].publisher,
                    content[i].year,
                    content[i].format,
                    content[i].language,
                    content[i].quantity,
                    a
                ]).draw();
            }
        },

        error: function (xhr, thrownError) {
            console.log(xhr.status);
            console.log(thrownError);
            window.location.href = "../stmtfailed.php";
        },

        async: true //false,
    });

    $('#shipTable tbody').on('click', '#sendOrder', function () { // azione che si innesca quando l'impiegato clicca su spedisci della tabella che contiene gli ordini (salvati nel database di sistema)

        var data = t2.row($(this).parents('tr')).data();
        console.log(data);

        $.ajax({

            url: "http://bookstore/empSendOrder",  
    
            data: {myData: {"idO": data['0'], "idImp": idImp}},

            method: 'post',
    
            success: function (response) {
    
                console.log(response);
            },
    
            error: function (xhr, thrownError) {

                console.log(xhr.status);
                console.log(thrownError);
                window.location.href = "../stmtfailed.php";
            },
    
            async: true //false,
        });

        alert("Spedizione effettuata con successo.");

        window.location.href = 'dashboard.php?viewOrderToShip=true';
    });


    var t3 = $('#addQuantityTable').DataTable({

        "lengthMenu": [2, 5, 10, 15],
        "iDisplayLength": 5,
        
        "columnDefs": [
            { data: 'id' },
            { data: 'title' },
            { data: 'author' },
            { data: 'publisher' },
            { data: 'year' },
            { data: 'format' },
            { data: 'language' },
            { data: 'quantity' },
            { data: 'action' }
        ]
    }); 

    $.ajax({ // richiesta AJAX effettuata per ottenere i prodotti che sono contenuti nel database di sistema 

        url: "http://bookstore/book",  

        success: function (response) {

            var content = JSON.parse(response);

            console.log(content);

            for (i = 0; i < content.length; i++) {

                var a = '<input placeholder="Inserisci quantità" required min="1" type="number" id="userQuantity" name="userQuantity" style="width: 180px;"/>'+                         
                        '<input style="margin-top: 6px;" type="submit" id="addQuantity" name="addQuantity" class="btn btn-primary" value="Aggiungi" />';

                t3.row.add([
                    content[i].IDLibro,
                    content[i].Titolo,
                    content[i].Autore,
                    content[i].Editore,
                    content[i].AnnoPubblicazione,
                    content[i].NomeFormato,
                    content[i].NomeLingua,
                    content[i].Quantita,
                    a
                ]).draw();
            }
        },

        error: function (xhr, thrownError) {
            console.log(xhr.status);
            console.log(thrownError);
            window.location.href = "../stmtfailed.php";
        },

        async: true //false,
    });
    
    $('#addQuantityTable tbody').on('click', '#addQuantity', function () { // azione che si inncesca quando un impiegato clicca sul tasto aggiorna quantità

        var data = t3.row($(this).parents('tr')).data();

        var data1 = t3.$('input').serializeArray();

        for (var j = 0; j < data1.length; j++) {

            if(data1[j].value.trim() != "" && parseInt(data1[j].value.trim()) > 0) {
                
                var qnt= data1[j].value.trim();

                $.ajax({ // richiesta AJAX effettuata per aggiornare la quantità di prodotto disponibile

                    url: 'http://bookstore/empAddQuantity/' + idImp,  
            
                    data: {myData: {"idL": data['0'], "qnta": qnt}},
        
                    method: 'post',
            
                    success: function (response) {
            
                        console.log(response);
                    },
            
                    error: function (xhr, thrownError) {
        
                        console.log(xhr.status);
                        console.log(thrownError);
                        window.location.href = "../stmtfailed.php";
                    },
            
                    async: true //false,
                });
        
                alert("Quantità aggiornata con successo.");
        
                window.location.href = 'dashboard.php?viewStock=true';

                return;
            }
        }

        alert("Errore. Valore immesso non valido.");
    });
});
