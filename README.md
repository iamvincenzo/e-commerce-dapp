# e-CommerceDApp

### Autori: Vincenzo Fraello & Lorenzo Di Palma

Specifiche progetto:

Creare una Decentralized application (DApp) per un sistema di vendita di prodotti online:
- I prodotti online in questione sono: Libri.
- Il sistema deve essere accessibile agli utenti tramite un sito web dinamico.
- Home del sito dove si possono:
  - Visualizzare i prodotti.
  - Acquistare prodotti. Metodi di pagamento:
    - Carta di Credito
    - Ether (Blockchain Ethereum)
  - Effettuare ricerche sui prodotti per:
    - Titolo.
    - Autore.
    - Editore.
    - Anno di pubblicazione.
    - Nome del genere letterario.
- Pagina di registrazione.
- Pagina di login.
- Pagina profilo dove si possono:
  - Visualizzare lo storico degli ordini.
  - Aggiornare i dettagli del proprio account.
  - Collegarsi con la blockchain Ethereum per:
    - Visualizzare il proprio bilancio (ETH)
    - Visualizzare i propri ordini effettuati con lo Smart Contract
- Pagine per effettuare l’acquisto di uno o più prodotti.
- Il sistema deve essere accessibile agli impiegati tramite un sito web dinamico.
  - Pagina di login.
  - Dashboard usata dagli impiegati per:
    - Aggiornare il numero di copie disponibili.
    - Acquisire i prodotti mancanti. I prodotti non devono essere duplicati. Si devono al massimo aggiornare il numero di copie esistenti. Quindi se si deve acquisire un prodotto, questo non deve essere già presente nel database.
    - Spedire gli ordini.
    - Visualizzare le statistiche delle vendite per ogni prodotto.
    - Visualizzare il bilancio del negozio (ETH).
    - Visualizzare il bilancio dello Smart Contract (ETH).
    - Ritirare gli Ether dallo Smart Contract (deposito del conto del proprietario del negozio).
    - Visualizzare gli ordini effettuati tramite lo Smart Contract.
    - Spedire gli ordini effettuati tramite lo Smart Contract.

Specifiche Tecniche:

• e-Commerce con interfaccia web dinamica realizzato con (pattern MVC):
  
  - Model:
    - JSON
  
  - View:
    - Framework Bootstrap
    - HTML
    - CSS
 
 - Controller:
    - PHP
    - Slim Framework
    - JavaScript
    - JQuery
    - DataTables
    - JQuery Pagination plugin
    - Web3.js
    - MetaMask
    
  - MySQL
  
  - Apache Web Server

• Smart Contract Ethereum utilizzato per effettuare il pagamento di prodotti e per tenere traccia di chi compra cosa.
Realizzato con:
 - Truffle
 - Ganache
 - Solidity
 - OpenZeppelin (standard per la sicurezza)

• Editor di testo:
 - Visual Studio Code
