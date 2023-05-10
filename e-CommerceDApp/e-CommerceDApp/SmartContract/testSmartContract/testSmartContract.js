/**
 * @author Vincenzo Fraello (299647) - Lorenzo Di Palma (299636) 
 */

import { connectBlockChain } from '../../includes/JavaScript/getSmartContractInfo.js';


/**
 * Connessione alla blockchain.
 */

var connection;
var web3;
var contract;

const ok = "✔";
const not_ok = "❌";

/**
 * Indirizzo dell'account Ethereum di un cliente
 */

var addressETH;


/**
 * Indirizzo dell'account Ethereum dell'impiegato
 */

var addressOwner;


/**
 * Array che contiene le funzioni che devono essere eseguite
 * in maniera sequenziale.
 */

var tasks = [_testMakePurchase,
             _testShipOrder,
             _testContractBalance,
             _testWithdraw];


/**
 * Funzione di servizio utilizzata per eseguire i tasks
 * in maniera sequenziale.
 * 
 * @param {string} err 
 * @param {*ìstring} result 
 */

function next(err, result) {
    
    if (err) throw err;
    
    var currentTask = tasks.shift();
    
    if (currentTask) currentTask(result);
}


/**
 * Questa funzione viene utilizzata per testare la funzione 
 * dello Smart Contract di acquisto/emissione di un ordine
 * e di visualizzazione dell'ordine.
 */

function _testMakePurchase() {

    var content_ = "Questo è un acquisto di test";

    var totEUR = 100;

    $.ajax({ // richiesta AJAX utilizzata per richiedere il valore attuale di ETH in EUR

        url: "https://api.coinbase.com/v2/exchange-rates?currency=ETH",

        success: function (result) {

            var ethValue = parseFloat(totEUR) / parseFloat(result.data.rates.EUR);

            web3.eth.getAccounts().then(function (accounts) {

                return contract.methods.makePurchase(content_).send({ // invocazione del metodo dello Smart Contract che consente di acquistare prodotti

                    from: addressETH,
                    value: web3.utils.toWei(String(ethValue), 'ether'),
                    gas: 3000000,
                    // gasPrice: web3.utils.toWei(String(_gasPrice), 'ether'),
                    // gasLimit: _gasLimit
                });

            }).then(function (tx) { // se l'aqcuisto va a buon fine

                contract.methods.getOrders(addressETH.trim()).call({ from: addressETH }).then(function (content) { // invoca il metodo dello Smart Contract che permette di ottenere gli acquisti effettuati dal cliente

                    var _owner = content[content.length - 1].owner;
                    var _eth = parseFloat(web3.utils.fromWei(content[content.length - 1].eth, 'ether')).toFixed(2);
                    var _content = content[content.length - 1].content;

                    if ((_owner === addressETH) &&
                        (_eth === parseFloat(ethValue).toFixed(2)) &&
                        (_content === content_)) $('#test1').append('<p>' + ok + ' Test "makePurchase" </p>');

                    else $('#test1').append('<p>' + not_ok + ' Test "makePurchase" </p>');

                    
                }).then(function() {

                    console.log(tx);

                    next();
                });

            }).catch(function (tx) { 
                
                console.log(tx); 
                next();
            });
        },

        async: false //true
    });
}


/**
 * Questa funzione viene utilizzata per testare la funzione 
 * dello Smart Contract di versamento del bilancio dello 
 * Smart Contract nel SOLO nel conto del proprietario.
 */

function _testWithdraw() {

    var address = [addressOwner, addressETH];

    for (var i = 0; i < 2; i++) {

        contract.methods.withdraw().send({ from: address[i] }).then(function (content) { // richiamo il metodo (che può essere richiamato solo dal possessore del contratto) per versare gli ether dal conto del contratto a quello del possessore del contratto

            if (Object.keys(content).length !== 0) $('#test1').append('<p>' + ok + ' Test "withdraw" addressOwner </p>');

            else $('#test1').append('<p>' + not_ok + ' Test "withdraw" addressOwner </p>');

        }).catch(function (tx) {

            console.log(tx);
            $('#test1').append('<p>' + not_ok + ' Test "withdraw" addressNotOwner </p>');
        });
    }

    next();
}


/**
 * Questa funzione viene utilizzata per testare la funzione
 * dello Smart Contract di spedizione di un ordine .
 * 
 * @param {string} _addr 
 * @param {number} _index 
 */

function _testShipOrder(_addr, _index) {

    var address = [addressOwner, addressETH];

    contract.methods.getOrders(addressETH.trim()).call({ from: addressETH }).then(function (content) { // invoca il metodo dello Smart Contract che permette di ottenere gli acquisti effettuati dal cliente

        var _owner = content[content.length - 1].owner;
        var _id = content[content.length - 1].id;

        for (var i = 0; i < 2; i++) {

            contract.methods.shipOrder(_owner, _id).send({ from: address[i] }).then(function (content) { // invocazione del metodo dello Smart Contract che permette di modificare lo stato dell'ordine

                if (Object.keys(content).length !== 0) $('#test1').append('<p>' + ok + ' Test "shipOrder" addressOwner </p>');

                else $('#test1').append('<p>' + not_ok + ' Test "shipOrder" addressOwner </p>');

            }).catch(function (tx) {

                console.log(tx);

                $('#test1').append('<p>' + not_ok + ' Test "shipOrder" addressNotOwner </p>');
            });
        }

    }).then(function() {
        
        next();
    })
}


/**
 * Questa funzione viene utilizzata per testare la funzione 
 * dello Smart Contract di visualizzazione del bilancio.
 */

function _testContractBalance() {

    var address = [addressOwner, addressETH];

    
    for (var i = 0; i < 2; i++) {

        contract.methods.contractBalance().call({ from: address[i] }).then(function (content) {

            if (content > 0) $('#test1').append('<p>' + ok + ' Test "contractBalance" addressOwner </p>');

            else $('#test1').append('<p>' + not_ok + ' Test "contractBalance" addressOwner </p>');

        }).catch(function (tx) {

            console.log(tx);
            $('#test1').append('<p>' + not_ok + ' Test "contractBalance" addressNotOwner </p>');
        });
    }

    next();
}


/**
 * Il codice JavaScript verrà eseguito solamente dopo che il documento è pronto.
 */

$(document).ready(function () {

    // connessione alla blockchain

    connection = connectBlockChain("../../includes/PHP/getSmartContractInfo.inc.php");
    web3 = connection[0];
    contract = connection[1];

    const _expectedContent = "Questo è un test";

    const errorMsg = 'the # is not even';

    addressETH = "0x91021A141EDf21c497f78F65559F68b8a4985F1b";

    addressOwner = "0x6dFff82623ee7e80c5691dC187aB1BEc1976D6Ae";

    next();
});
