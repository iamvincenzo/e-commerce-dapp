/**
 *
 *  @author Vincenzo Fraello (299647) - Lorenzo Di Palma (299636) 
 *
 */

/**
 * Questa funzione di servizio viene utilizzata per ottenere
 * le informazioni che sono contenute nel database di sistema
 * necessarie al collegamento con lo Smart Contract.
 * 
 * @param {string} _url Indirizzo del file a cui chiedere la risorsa.
 */

function getSmartContractInfo(_url) {

    var _result;

    $.ajax({ // richiesta AJAX effettuata per richiedere le informazioni relative allo Smart Contract al database di sistema

        url: _url,

        success: function (result) {

            _result = JSON.parse(result);
        },

        async: false
    });

    return _result;
}


/**
 * Funzione utilizzata per realizzare la connessione con
 * la blockchain e lo Smart Contract.
 * 
 * @param {string} _url Indirizzo del file a cui chiedere la risorsa.
 * @returns {array} Ritorna i parametri di collegamento con la blockchain e lo Smart Contract.
 */

export function connectBlockChain(_url) {

    var result = getSmartContractInfo(_url);

    // Load WEB3 --> Questa soluzione si adotta quando si vuole usare solo metamaks per pagare

    // Check wether it's already injected by something else (like Metamask or Parity Chrome plugin)
    // if (typeof web3 !== 'undefined') {

    //     console.log("qua");
    //     web3 = new Web3(web3.currentProvider);
    //     // Or connect to a node
    // } 
    
    // else {
    //     console.log("qui");
    //     web3 = new Web3(new Web3.providers.HttpProvider("http://localhost:8545"));
    // }

    web3 = new Web3(new Web3.providers.HttpProvider("http://localhost:8545")); // https://ethereum.stackexchange.com/questions/26244/how-can-i-get-my-address-via-web3

    // Check the connection
    // if (!web3.isConnected()) {

    //     console.error("Not connected");
    // }

    var address = result.AddressSmartContract;

    var addrOwner = result.AddressOwner;

    var abi = JSON.parse(result.ABI);

    var contract = new web3.eth.Contract(abi, address);

    return [web3, contract, addrOwner];
}
