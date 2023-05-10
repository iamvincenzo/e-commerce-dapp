/**
 * 
 * @author Vincenzo Fraello (299647) - Lorenzo Di Palma (299636) // https://cryptojobslist.com/blog/how-to-add-ethereum-payments-to-your-site-with-metamask
 *
 */

import { connectBlockChain } from './getSmartContractInfo.js';


/**
 * 
 * Questa funzione si attiva quando si accede alla sezione di pagamento e 
 * consente di acquistare prodotti tramite MetaMask.
 * 
 */

window.addEventListener('click', async () => {

    if (window.ethereum) {
        
        window.web3 = new Web3(ethereum);

        try {
            await ethereum.enable();
            initPayButton()
        }
        catch (err) {
            $('#status').html('User denied account access', err)
        }
    }

    else if (window.web3) {
      
        window.web3 = new Web3(web3.currentProvider)
      
        initPayButton()
    }

    else {
        $('#status').html('No Metamask (or other Web3 Provider) installed')
    }
})

const initPayButton = () => {

  $('.metamaskbtn').click(() => {

    var connection = connectBlockChain("includes/PHP/getSmartContractInfo.inc.php");
    var web3 = connection[0];
    var contract = connection[1];

    var addressETH;

    $.ajax({ // richeista AJAX utilizzata per ottenere l'indirizzo Ethereum dell'utente

      url: "includes/PHP/getAddressETH.inc.php",

      success: function (result) {

        addressETH = JSON.parse(result);
      },

      async: false
    });
    
    const amountEth = localStorage.getItem("tot"); // totale spesa cliente

    viewBalance(amountEth, web3, contract, addressETH); // si richiama la funzione per avviare la fase di acquisto
  });
} 
