/**
 *
 *  @author Vincenzo Fraello (299647) - Lorenzo Di Palma (299636) 
 *
 */

import { connectBlockChain } from './getSmartContractInfo.js';


/**
 * Variabile utilizzata per clonare il carrello prima dell'acquisto.
 * Se la transazione in blockchain non dovesse andare a buon fine si devono
 * ricaricare nel database di sistema i prodotti che sono stati decrementati.
 * 
 * @var {array} cartDiComodo
 */

var cartDiComodo;


/**
 * Variabile utilizzata per gestire il gas price della transazione.
 * 
 * @var {number} _gasPrice 
 */

var _gasPrice;


/**
 * Variabile utilizzata per gestire il gas limit della transazione.
 * 
 * @var {number} _gasLimit 
 */

var _gasLimit;


/**
 * Variabile utilizzata per gestire il bilancio del cliente
 * che vuole concludere l'acuqisot in ether. 
 * 
 * @var {number} ethBalance
 */

var ethBalance;


/**
 * Variabile utilizzta per gestire il valore della spesa totale
 * del cliente da EUR in ETH.
 * 
 * @var {number} ethValue
 */

var ethValue;


/**
 * @description - Questa funzione viene utilizzata per effettuare l'operazione di pagamento.
 * 
 * @param {string} - It is beutiful
 * @returns {number} - It returns asdas
 */

$(document).ready(function () {

    $('.cartNumber').replaceWith(JSON.parse(localStorage.getItem("cart") || "[]").length); // Numero di elementi contenuti nel carrello (lunghezza array di oggetti: prodotti)

    $(document).on('click', '#closePayement', function () { // quando si chiude una finestra di messaggio si svuota il campo del form usato per inserire i dati di Ethereum

        $('.privateKey').val('');
    });
});


/**
 * Questa funzione di servizio viene utilizzata per innescare il pagamento
 * dei prodotti tramite blockchain.
 */

window.payETHFunction = function () { // funzione attivata quando il cliente clicca sul tasto paga --> 1) Controlla che il cliente abbia inserito tutti i parametri di acquisto correttamente

    // connessione alla blockchain

    var connection = connectBlockChain("includes/PHP/getSmartContractInfo.inc.php");
    var web3 = connection[0];
    var contract = connection[1];

    var addressETH;

    var totEUR = localStorage.getItem("tot"); // prelievo del totale spesa del cliente dal localStorage

    $.ajax({ // richiesta AJAX usata per ottenere l'indirizzo Ethereum dell'utente

        url: "includes/PHP/getAddressETH.inc.php",

        success: function (result) {

            addressETH = JSON.parse(result);
        },

        async: false
    });

    var privateKey = $('.privateKey').val(); // prelevo il valore della chiave privata inserita nel form

    if (privateKey.length < 64 || privateKey == '') { // se la chiave privata non è valida

        alert('Chiave privata non valida.');
        $('.privateKey').val('');
        
        return;
    }

    var addrFromPrK = web3.eth.accounts.privateKeyToAccount(privateKey).address; // ricavo dell'indirizzo del cliente a partire dalla sua chiave privata

    if (addressETH == addrFromPrK) { // se l'indirizzo inserito dal cliente corrisponde con quello ottenuto a partire dalla chiave privata inserita nel form di pagamento

        if ($('#gasLimitETH').val() == '') _gasLimit = 21000; // se il gas limit non viene inserito si usa quello minimo di default

        else if (parseInt($('#gasLimitETH').val()) < 21000) { // se il gas limit viene inserito ma è minore del gas limit minimo obbligatorio

            alert('Gas Limit minimo: 21000.');
            $('#gasLimitETH').val('');
            
            return;
        }

        else _gasLimit = parseInt($('#gasLimitETH').val()); // gas limit soddisfa parametri di accettazione

        if ($('#gasPriceETH').val() != '') _gasPrice = parseFloat($('#gasPriceETH').val()); // se viene inserito il gas Price

        else { // se non viene inserito il gas price si usa quello di default che si ottiene con il metodo che segue

            web3.eth.getGasPrice().then(val => {

                sessionStorage.setItem("gasPrice", val);
            });

            _gasPrice = JSON.parse(sessionStorage.getItem("gasPrice"));
        }

        $('#closePayement').click(); // se tutto è andato a buon fine si chiude la finestra di inserimento dei parametri di acquisto

        viewBalance(totEUR, web3, contract, addressETH); // viene innescata la funzione di acquisto --> 2) Controlla che il cliente abbia abbastanza criptovaluta (ETH) per concludere l'acquisto
    }

    else {

        alert('Chiave privata e/o indirizzo sbagliato.');

        return;
    }
}


/**
 * Questa funzione di servizio viene utilizzata pe controllare che il cliente
 * abbia abbastanza criptovaluta per poter concludere l'acquisto.
 * 
 * @param {number} totEUR Totale spesa cliente in euro.
 * @param {object} web3 Collegamento alla blockchain.
 * @param {object} contract Istanza del contratto.
 * @param {string} addressETH Indirizzo Ethereum del cliente
 */

window.viewBalance = function (totEUR, web3, contract, addressETH) {

    $.ajax({ // richiesta AJAX utilizzata per ottenere il valore attuale di ETH in EUR e verificare che il cliente abbia abbastanza criptovaluta

        url: "https://api.coinbase.com/v2/exchange-rates?currency=ETH",

        success: function (result) {

            ethValue = parseFloat(totEUR) / parseFloat(result.data.rates.EUR); // valore della spesa totale del cliente in ETH

            web3.eth.getBalance(addressETH).then(function (val) { // https://stackoverflow.com/questions/29516390/how-to-access-the-value-of-a-promise

                ethBalance = parseFloat(web3.utils.fromWei(val, "ether")); // conversione in ETH da WEI del bilancio del cliente

                if (ethBalance < ethValue) { // se il cliente non ha abbastanza criptovaluta per concludere l'acquisto questo, non viene portato a termine

                    alert('Non hai abbastanza criptovaluta per concludere l\'acquisto. Controlla il bilancio.');

                    return;
                }

                else {

                    okBalnceThenBuy(totEUR, contract, addressETH); // funzione che viene attivata quando il cliente ha abbastanza criptovaluta --> 3) Il bilancio è ok quindi si procede con l'acuqisto
                }
            });
        },

        async: false
    });
}


/**
 * Questa funzione di servizio viene utilizzata per verificare la disponibilità di
 * prodotto nel database di sistema ed effettuare l'acquisto.
 * 
 * @param {number} totEUR Totale spesa cliente.
 * @param {object} contract Istanza del contratto.
 * @param {string} addressETH Indirizzo Ethereum del cliente.
 */

function okBalnceThenBuy(totEUR, contract, addressETH) {

    var myObj = JSON.parse(localStorage.getItem("cart") || "[]"); // estrazione dal localStorage del carrello (in cui sono salvati i prodotti)

    if (Object.keys(myObj).length === 0) { // se nel carrello non c'è nulla non fa attivare la procedura di acquisto

        alert('Non puoi fare acquisti nulli.');

        return;
    }

    $.post("http://bookstore/verifyAvailabilityAndDecrement", // richiesta AJAX effettuata per controllare che nel database di sistema ci sia abbastanza quantità di prodotti desiderati ed eventualmente decrementare la quantità voluta

        {
            myData: myObj,
        },

        function (response) {

            var content = JSON.parse(response);

            cartDiComodo = content; // salvo il risultato degli acquisti con i relativi acquisti mandati in risposta dal server. Viene utilizzato per effettuare il reso dei soli prodotti effettivamente decrementati nel qual caso la transazione in blockchain non vada a buon fine

            var _content = "";

            // LA RISPOSTA CONTIENE I LIBRI E GLI ESITI
                // COSI LE COSE CHE SI SONO POTUTE DECREMENTARE SI TOLGONO DAL CARRELLO E SI INSERISCONO NELLA TRANSAZIONE
                    // LE COSE CHE NON SI SONO POTUTE DECREMENTARE SI LASCIANO NEL CARRELLO E SI AVVISA IL CLIENTE

            for (var i = 0; i < content.length; i++) { // si scorre il risultato degli esiti degli acquisti ottenuti in risposta dal server

                myObj = JSON.parse(localStorage.getItem("cart") || "[]"); // si estraggono i prodotti contenuti nel carrello          

                if (content[i].Esito) { // se l'esito dell'acuqsito risulta essere positivo

                    for (var j = 0; j < myObj.length; j++) { // si ricerca tale prodotto nel carrello e si rimuove - se l'esito è negativo lo si lascia nel carrello

                        if (myObj[j].id == content[i].IDLibro) {

                            _content += 'IDLibro: ' + content[i].IDLibro + ' - Titolo: ' + content[i].Titolo + ' - Autore: ' + content[i].Autore + ' - Editore: ' + content[i].Editore + ' - PrezzoUnitario: ' + content[i].Prezzo + ' - QuantitaAcquistata: ' + myObj[j].qnta + '\n'; // NEL CICLO SI METTONO IN UNA STRINGA COMUNE (CONTENUTO DELLA TRANSAZIONE)  // I LIBRI CHE SONO STATI DECREMENTATI

                            console.log(_content);

                            if (myObj.length > 2) myObj.splice(j, 1); // se ci sono più di due elementi nel carrello si rimuove l'item alla poszione i-esima

                            else if (myObj.length == 2) { // se ci sono due elementi nel carrello si eliminano nel seguente modo in relazione all'indice di posizione

                                if (j == 0) myObj.splice(-2, 1);

                                else myObj.splice(-1, 1);
                            }

                            else if (myObj.length == 1) myObj.splice(0, 1);

                            localStorage.setItem("cart", JSON.stringify(myObj));  // salvo il carrello all'interno di localStorage

                            break; // esco dalla funzione
                        }
                    }
                }
            }

            if (!jQuery.isEmptyObject(myObj)) alert('Non è stato possibile effettuare l\'acquisto di alcuni o tutti i prodotti. Controlla il carrello.'); // se nel carrello c'è ancora almeno un prodotto

            if (_content != '') smartContractTransaction(totEUR, _content, contract, addressETH); // se _content non è vuoto (cioè almeno un prodotto è stato possibile acquistarlo) --> 4) Si attiva la funzione che effettua la transazione in blockchain tramite lo Smart Contract o se non va a buon fine la transazione ricarica il quantitativo di prodotti di nuovo nel database di sistema

            else { // non è stato fatto nemmeno un acquisto

                alert('Non è stato possibile effettuare l\'acquisto di alcuni o tutti i prodotti. Controlla il carrello.');
                return;
            }

    }).fail(function(response) {

        window.location.href = "stmtfailed.php";
    });
}


/**
 * Quetsa funzione di servizio viene utilizzata per effettuare la transazione
 * in blockchain e il pagamento dei prodotti per mezzo dello Smart Contract.
 * 
 * @param {number} totEUR Totale spesa cliente.
 * @param {string} _content Informazioni sull'acuqsito del cliente da inserire nella blockchain.
 * @param {object} contract Istanza del contratto.
 * @param {string} addressETH Indirizzo Ethereum del cliente.
 */

function smartContractTransaction(totEUR, _content, contract, addressETH) {

    $.ajax({ // richiesta AJAX utilizzata per richiedere il valore attuale di ETH in EUR

        url: "https://api.coinbase.com/v2/exchange-rates?currency=ETH",

        success: function (result) {

            ethValue = parseFloat(totEUR) / parseFloat(result.data.rates.EUR);

            web3.eth.getAccounts().then(function (accounts) {

                return contract.methods.makePurchase(_content).send({ // invocazione del metodo dello Smart Contract che consente di acquistare prodotti

                    from: addressETH,
                    value: web3.utils.toWei(String(ethValue), 'ether'),
                    gas: 3000000,
                    // gasPrice: web3.utils.toWei(String(_gasPrice), 'ether'),
                    // gasLimit: _gasLimit
                });

            }).then(function (tx) { // se l'aqcuisto va a buon fine

                alert('Acquisto completato.');
                
                console.log(tx);

                window.location.href = 'profile.php?viewDAppETH=true';

            }).catch(function (tx) { // se l'acquisto non va a buon fine si ricaricano i prodotti che non sono stati acquistati ma sono stati decrementati

                alert('Acquisto non completato. Qualcosa è andato storto.');
                console.log(tx);

                $.post("http://bookstore/reloadBooks",

                    {
                        myData2: cartDiComodo,
                    },

                    function (response) {

                        console.log(response);

                        window.location.href = 'index.php';

                }).fail(function(response) {
                    
                    window.location.href = "stmtfailed.php";
                });
            });
        },

        async: true
    });
}
