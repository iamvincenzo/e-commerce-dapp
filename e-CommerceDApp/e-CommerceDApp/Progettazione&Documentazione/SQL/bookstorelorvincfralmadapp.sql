-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mar 19, 2021 alle 11:08
-- Versione del server: 10.1.39-MariaDB
-- Versione PHP: 7.3.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookstorelorvincfralmadapp`
--
CREATE DATABASE IF NOT EXISTS `bookstorelorvincfralmadapp` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `bookstorelorvincfralmadapp`;

DELIMITER $$
--
-- Procedure
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `Acquisto` (IN `IDProdotto` INT, IN `IDCliente` INT, IN `QntDesired` INT, IN `Tot` FLOAT, OUT `Msg` VARCHAR(255))  ProcAcquisto: BEGIN

	START TRANSACTION;
    
    IF QntDesired < 0 THEN
		ROLLBACK; 
        SET Msg='Errore quantità desiderata negativa.';
		LEAVE ProcAcquisto;
	END IF;
    
    CALL Ordine(IDProdotto, IDCliente, QntDesired, Tot, @EseguitoO);
	
	CASE @EseguitoO
		WHEN -1 THEN SET Msg='Errore nella conferma dell''ordine.';
		ELSE /* Risultato=1 */ BEGIN END;
	END CASE;
	
	IF @EseguitoO < 0 THEN
		ROLLBACK; 
		LEAVE ProcAcquisto;
	END IF;
	
	CALL Decrementa(IDProdotto, QntDesired, @EseguitoD);
	
	CASE @EseguitoD
		WHEN -1 THEN SET Msg='Errore nel decrementare quantità disponibile nel database.';
		ELSE /* Risultato=1 */ BEGIN END;
	END CASE;
	
	IF @EseguitoD < 0 THEN
		ROLLBACK; 
		LEAVE ProcAcquisto;
	ELSE	
		COMMIT;
		SET Msg='Acquisto completato.';
	END IF;
	
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Decrementa` (`IDProdotto` INTEGER, `QntBought` INTEGER, OUT `EseguitoD` INT)  ProcDecr: BEGIN

	DECLARE QntDisp INT;

	SELECT Quantita INTO QntDisp
    FROM libro
    WHERE IDLibro = IDProdotto;
    
    IF QntDisp < QntBought THEN
        SET EseguitoD = -1;
        LEAVE ProcDecr;
    END IF;
    
    IF QntDisp >= QntBought THEN
    	UPDATE libro SET Quantita = Quantita - QntBought WHERE IDLibro = IDProdotto;
        SET EseguitoD = 1;
    ELSE
    	SET EseguitoD = -1;
    END IF;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Ordine` (`IDProdotto` INT, `IDCliente` INT, `QntDesired` INT, `Tot` FLOAT, OUT `EseguitoO` INT)  ProcOrd: BEGIN

	DECLARE QntDisp INT;
    
    SELECT Quantita INTO QntDisp
    FROM libro
    WHERE IDLibro = IDProdotto;
    
    IF QntDisp < QntDesired THEN
        SET EseguitoO = -1;
        LEAVE ProcOrd;
    END IF;
    
    IF QntDisp >= QntDesired THEN
    	INSERT INTO ordine(IDCli, IDL, Quantita, Totale) VALUES(IDCliente, IDProdotto, QntDesired, Tot);
        SET EseguitoO = 1;
    ELSE
    	SET EseguitoO = -1;
    END IF;
    
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `cliente`
--

CREATE TABLE `cliente` (
  `IDCliente` int(11) NOT NULL,
  `Nome` varchar(255) NOT NULL,
  `Cognome` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Indirizzo` varchar(255) NOT NULL,
  `Cellulare` varchar(10) DEFAULT NULL,
  `Citta` varchar(255) NOT NULL,
  `AddressETH` varchar(42) DEFAULT NULL,
  `Provincia` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `cliente`
--

INSERT INTO `cliente` (`IDCliente`, `Nome`, `Cognome`, `Email`, `Password`, `Indirizzo`, `Cellulare`, `Citta`, `AddressETH`, `Provincia`) VALUES
(1, 'Vincenzo', 'Fraello', 'vincenzo.fraello@outlook.it', '$2y$10$LYcA1bQq0rjpcK5KZHMBq.6p27aQ47VdZaqt31s/ByBwxv5l7WAIy', 'Via P. G. Cianci, 28', '3332662989', 'Sortino', '0x460fa7FcCbdD132F6709d0D251B6234292149827', 'Siracusa'),
(2, 'Lorenzo', 'Di Palma', 'lorenzo.dipalma@gmail.com', '$2y$10$og5YE.h5JqjZb7CxVWtTle9mAGP45vqxUuGim027KC5LicSySuAeq', 'Via P. G. Cianci, 26', '3928729246', 'Castel San Pietro Terme', '0x83e33e7841Ce24737ff0C35dEa2Be6Bc0a3454dB', 'Bologna'),
(3, 'a', 'b', 'a@b.it', '$2y$10$QbVAZr.6sw8HrgJIREMZj.oMS8yaQfUrbVIqdMdnnCAzAYIoqglRq', 'Via Piemonte, 13', '', 'Parma', '           ', 'PR'),
(4, 'asd', 'asd', 'asd.f@e.it', '$2y$10$jNSiF46Pwl4Z0zhPVX1BPOm2tiCcoR2tw3YxEaAcURyx1OZYAVTFm', 'asd', NULL, 'asd', '', 'asd');

-- --------------------------------------------------------

--
-- Struttura della tabella `contratto`
--

CREATE TABLE `contratto` (
  `IDContratto` int(11) NOT NULL,
  `ABI` longtext NOT NULL,
  `AddressSmartContract` varchar(42) NOT NULL,
  `AddressOwner` varchar(42) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `contratto`
--

INSERT INTO `contratto` (`IDContratto`, `ABI`, `AddressSmartContract`, `AddressOwner`) VALUES
(1, '[\r\n    {\r\n      \"anonymous\": false,\r\n      \"inputs\": [\r\n        {\r\n          \"indexed\": true,\r\n          \"internalType\": \"address\",\r\n          \"name\": \"previousOwner\",\r\n          \"type\": \"address\"\r\n        },\r\n        {\r\n          \"indexed\": true,\r\n          \"internalType\": \"address\",\r\n          \"name\": \"newOwner\",\r\n          \"type\": \"address\"\r\n        }\r\n      ],\r\n      \"name\": \"OwnershipTransferred\",\r\n      \"type\": \"event\"\r\n    },\r\n    {\r\n      \"inputs\": [],\r\n      \"name\": \"owner\",\r\n      \"outputs\": [\r\n        {\r\n          \"internalType\": \"address\",\r\n          \"name\": \"\",\r\n          \"type\": \"address\"\r\n        }\r\n      ],\r\n      \"stateMutability\": \"view\",\r\n      \"type\": \"function\",\r\n      \"constant\": true\r\n    },\r\n    {\r\n      \"inputs\": [],\r\n      \"name\": \"renounceOwnership\",\r\n      \"outputs\": [],\r\n      \"stateMutability\": \"nonpayable\",\r\n      \"type\": \"function\"\r\n    },\r\n    {\r\n      \"inputs\": [\r\n        {\r\n          \"internalType\": \"address\",\r\n          \"name\": \"newOwner\",\r\n          \"type\": \"address\"\r\n        }\r\n      ],\r\n      \"name\": \"transferOwnership\",\r\n      \"outputs\": [],\r\n      \"stateMutability\": \"nonpayable\",\r\n      \"type\": \"function\"\r\n    },\r\n    {\r\n      \"inputs\": [\r\n        {\r\n          \"internalType\": \"string\",\r\n          \"name\": \"_content\",\r\n          \"type\": \"string\"\r\n        }\r\n      ],\r\n      \"name\": \"makePurchase\",\r\n      \"outputs\": [],\r\n      \"stateMutability\": \"payable\",\r\n      \"type\": \"function\",\r\n      \"payable\": true\r\n    },\r\n    {\r\n      \"inputs\": [\r\n        {\r\n          \"internalType\": \"address\",\r\n          \"name\": \"_addr\",\r\n          \"type\": \"address\"\r\n        }\r\n      ],\r\n      \"name\": \"getOrders\",\r\n      \"outputs\": [\r\n        {\r\n          \"components\": [\r\n            {\r\n              \"internalType\": \"uint256\",\r\n              \"name\": \"id\",\r\n              \"type\": \"uint256\"\r\n            },\r\n            {\r\n              \"internalType\": \"uint256\",\r\n              \"name\": \"purchaseDate\",\r\n              \"type\": \"uint256\"\r\n            },\r\n            {\r\n              \"internalType\": \"string\",\r\n              \"name\": \"content\",\r\n              \"type\": \"string\"\r\n            },\r\n            {\r\n              \"internalType\": \"address\",\r\n              \"name\": \"owner\",\r\n              \"type\": \"address\"\r\n            },\r\n            {\r\n              \"internalType\": \"uint256\",\r\n              \"name\": \"eth\",\r\n              \"type\": \"uint256\"\r\n            },\r\n            {\r\n              \"internalType\": \"bool\",\r\n              \"name\": \"isShipped\",\r\n              \"type\": \"bool\"\r\n            }\r\n          ],\r\n          \"internalType\": \"struct PurchaseProducts.Purchase[]\",\r\n          \"name\": \"\",\r\n          \"type\": \"tuple[]\"\r\n        }\r\n      ],\r\n      \"stateMutability\": \"view\",\r\n      \"type\": \"function\",\r\n      \"constant\": true\r\n    },\r\n    {\r\n      \"inputs\": [],\r\n      \"name\": \"withdraw\",\r\n      \"outputs\": [],\r\n      \"stateMutability\": \"nonpayable\",\r\n      \"type\": \"function\"\r\n    },\r\n    {\r\n      \"inputs\": [],\r\n      \"name\": \"contractBalance\",\r\n      \"outputs\": [\r\n        {\r\n          \"internalType\": \"uint256\",\r\n          \"name\": \"\",\r\n          \"type\": \"uint256\"\r\n        }\r\n      ],\r\n      \"stateMutability\": \"view\",\r\n      \"type\": \"function\",\r\n      \"constant\": true\r\n    },\r\n    {\r\n      \"inputs\": [\r\n        {\r\n          \"internalType\": \"address\",\r\n          \"name\": \"_addr\",\r\n          \"type\": \"address\"\r\n        },\r\n        {\r\n          \"internalType\": \"uint256\",\r\n          \"name\": \"_index\",\r\n          \"type\": \"uint256\"\r\n        }\r\n      ],\r\n      \"name\": \"shipOrder\",\r\n      \"outputs\": [],\r\n      \"stateMutability\": \"nonpayable\",\r\n      \"type\": \"function\"\r\n    }\r\n]', '0xDEc7EC3925877F6602F546373B455C2DD95bb859', '0xc247519460785208bd563957520bA1BDE341C1A0');

-- --------------------------------------------------------

--
-- Struttura della tabella `formato`
--

CREATE TABLE `formato` (
  `IDFormato` int(11) NOT NULL,
  `NomeFormato` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `formato`
--

INSERT INTO `formato` (`IDFormato`, `NomeFormato`) VALUES
(1, 'Tascabile'),
(2, 'Brossura'),
(3, 'Rilegato'),
(4, 'Cartonato'),
(5, 'AudioLibro'),
(6, 'Rilegatura di pregio');

-- --------------------------------------------------------

--
-- Struttura della tabella `genere`
--

CREATE TABLE `genere` (
  `IDGenere` int(11) NOT NULL,
  `NomeGenere` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `genere`
--

INSERT INTO `genere` (`IDGenere`, `NomeGenere`) VALUES
(1, 'Poliziesco'),
(2, 'Fantasy'),
(3, 'Cucina'),
(4, 'Informatica'),
(5, 'Scienze');

-- --------------------------------------------------------

--
-- Struttura della tabella `impiegato`
--

CREATE TABLE `impiegato` (
  `IDImpiegato` int(11) NOT NULL,
  `Nome` varchar(255) NOT NULL,
  `Cognome` varchar(255) NOT NULL,
  `NomeUtente` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `impiegato`
--

INSERT INTO `impiegato` (`IDImpiegato`, `Nome`, `Cognome`, `NomeUtente`, `Password`) VALUES
(1, 'admin', 'admin', 'root', 'root'),
(2, 'a', 'b', 'a@b', '123');

-- --------------------------------------------------------

--
-- Struttura della tabella `libro`
--

CREATE TABLE `libro` (
  `IDLibro` int(11) NOT NULL,
  `Titolo` varchar(255) NOT NULL,
  `Autore` varchar(255) NOT NULL,
  `Editore` varchar(255) NOT NULL,
  `AnnoPubblicazione` year(4) NOT NULL,
  `Descrizione` text NOT NULL,
  `ImmagineCopertina` varchar(255) NOT NULL,
  `CodiceFormato` int(11) NOT NULL,
  `Prezzo` float NOT NULL,
  `Quantita` int(11) NOT NULL,
  `Pagine` int(11) NOT NULL,
  `CodiceLingua` int(11) NOT NULL,
  `CodiceGenere` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `libro`
--

INSERT INTO `libro` (`IDLibro`, `Titolo`, `Autore`, `Editore`, `AnnoPubblicazione`, `Descrizione`, `ImmagineCopertina`, `CodiceFormato`, `Prezzo`, `Quantita`, `Pagine`, `CodiceLingua`, `CodiceGenere`) VALUES
(1, 'Sviluppo in PHP', 'Enrico Zimuel', 'Tecniche Nuove', 2019, 'Questa seconda edizione del libro, aggiornata con le ultime novità del PHP 7.3 e 7.4, è ricca di novità per consentire agli sviluppatori un utilizzo continuo e puntuale del linguaggio PHP, la tecnologia Open Source consolidata utilizzata dal 79% dei siti internet in tutto il mondo per lo sviluppo di applicazioni web e API professionali. Il manuale presenta i principali design pattern utilizzati nella progettazione di applicazioni professionali, per facilitare la manutenzione e il riutilizzo del codice, creando architetture software robuste e flessibili, forte dell’esperienza e dei progetti curati o ai quali ha partecipato l’autore. Inoltre contiene un nuovo capitolo di approfondimento sulla programmazione orientata agli oggetti in PHP e numerosi aggiornamenti sulle novità introdotte dalle versioni 7.3 e 7.4 del PHP. All’indirizzo: www.sviluppareinphp7.it si possono scaricare i codici sorgenti presenti nel libro.', 'img/sviluppoPHPEnricoZimuel.jpg', 2, 10000, 123, 408, 1, 4),
(2, 'Profumi di Sicilia. Il libro della cucina siciliana', 'Giuseppe Coria', 'Cavallotto', 2019, 'Esistono alcuni libri che, nel tempo diventano dei classici. \"Profumi di Sicilia\" da più parti considerato \"la Bibbia della cucina siciliana\", non è soltanto un ricchissimo testo di cucina che nelle sue 700 pagine racchiude le più autentiche e genuine ricette della tradizione gastronomica siciliana: contiene anche una ricchissima serie di informazioni storiche, folcloristiche, etimologiche e sulle tradizioni che offrono al lettore una chiave privilegiata per indagare, da un originale e piacevole punto di vista, sul carattere della Sicilia e dei siciliani. Proposto in una tiratura speciale limitata e numerata arricchita da un elegante cofanetto e da una stampa d\'arte su carta cotone.', 'img/profumiCucinaGiuseppeCoria.jpg', 3, 119, 91, 670, 1, 3),
(3, 'Profumi di Sicilia. Il libro della cucina siciliana', 'Giuseppe Coria', 'Cavallotto', 2019, 'Esistono alcuni libri che, nel tempo diventano dei classici. \"Profumi di Sicilia\" da più parti considerato \"la Bibbia della cucina siciliana\", non è soltanto un ricchissimo testo di cucina che nelle sue 700 pagine racchiude le più autentiche e genuine ricette della tradizione gastronomica siciliana: contiene anche una ricchissima serie di informazioni storiche, folcloristiche, etimologiche e sulle tradizioni che offrono al lettore una chiave privilegiata per indagare, da un originale e piacevole punto di vista, sul carattere della Sicilia e dei siciliani. Proposto in una tiratura speciale limitata e numerata arricchita da un elegante cofanetto e da una stampa d\'arte su carta cotone.', 'img/profumiCucinaGiuseppeCoria.jpg', 2, 119, 16, 670, 1, 3),
(4, 'Blockchain criptovalute e ICO', 'Alessandro Basile', 'Flaccovio', 2019, 'La blockchain Ã¨ una realtÃ  tecnologica sempre piÃ¹ attuale che costituisce la base per nuovi business e imprese. Il libro fornisce unâ€™accurata introduzione al tema del paradigma tecnologico della blockchain, ripercorrendone la storia ed esplicitandone gli aspetti teorici e pratici, le caratteristiche e anche gli aspetti legali. Spaziando tra la sfera tecnica, la sfera legale e la sfera economica e finanziaria, lâ€™autore illustra come strutturare la catena, â€œminareâ€ le criptovalute e raccogliere capitali grazie alla blockchain e ai token. Inoltre riporta alcuni casi concreti di applicazione pratica della blockchain in diversi settori industriali senza dimenticare di analizzarne le implicazioni legali.', 'img/blockchain.jpg', 2, 22.3, 111, 201, 1, 4),
(5, 'Hacking finance. La rivoluzione digitale nella finanza tra bitcoin e crowdfunding', 'Francesco De Collibus, Lovercraft-Turing Ralph', 'Agenzia X', 2016, 'La crisi del 2008-09 ha accelerato le dinamiche di trasformazione del sistema finanziario che oggi appare prossimo a una rivoluzione simile a quella accaduta nellâ€™industria musicale, nel giornalismo e nellâ€™editoria. In questi settori le tecnologie digitali hanno portato cambiamenti radicali, modificando i modelli di business, gli attori principali, le modalitÃ  di fruizione dei contenuti e la struttura generale dellâ€™industria. Nella finanza il sistema Ã¨ ben piÃ¹ complesso, trilioni e trilioni di dollari sono accumulati, trasferiti in pochi secondi e impiegati ogni giorno tra molteplici metodi di pagamento, mercati azionari, contratti derivati e investimenti internazionali. Lâ€™innovazione finanziaria, specialmente quella che proviene dal basso, sta minando le fondamenta del sistema: nuove pratiche e tecnologie stanno portando lâ€™economia e la finanza verso una struttura basata su processi decentralizzati. Hacking Finance esplora queste linee di cambiamento e ne identifica limiti e possibilitÃ , con un linguaggio e un approccio hacker, vale a dire â€œsmontare la scatolaâ€, non seguire le istruzioni, ri-arrangiare i pezzi in modo non previsto. Centinaia di progetti indipendenti e startup stanno oggi partecipando a questa trasformazione che, per la prima volta, non Ã¨ guidata dai grandi attori come le banche di investimento, i governi e le banche centrali.', 'img/hackingFinance.jpg', 2, 11.99, 11, 160, 1, 4),
(6, 'Harry Potter e il Principe mezzosangue', 'J. K. Rowling', 'Salani', 2020, 'Alla fine dello scorso volume, abbiamo lasciato Harry Potter sconvolto, solo e preoccupato. Il suo amato padrino Sirius Black Ã¨ morto, e le parole di Albus Silente sulla profezia gli confermano che lo scontro con Lord Voldemort Ã¨ ormai inevitabile. Niente Ã¨ piÃ¹ come prima: l\\\'ultimo legame con la sua famiglia Ã¨ troncato, perfino Hogwarts non Ã¨ piÃ¹ la dimora accogliente dei primi anni, mentre Voldemort Ã¨ piÃ¹ forte, crudele e disumano che mai. Harry stesso sa di essere cambiato. La frustrazione e il senso di impotenza dei quindici anni hanno ceduto il posto a una fermezza e a una determinazione diverse, piÃ¹ adulte. Nella sesta e penultima avventura di Harry Potter, J.K. Rowling arricchisce il suo scenario di indizi e segreti stupefacenti; sospetti e veritÃ  che non offrono risposte ma moltiplicano gli enigmi; nuovi personaggi e nuove magie ma anche inattese rivelazioni su personaggi giÃ  noti... EtÃ  di lettura: da 12 anni.', 'img/HarryPotter6.jpg', 2, 14.5, 15, 576, 1, 2),
(7, 'La Bussola D\'oro', 'Philip Pullman', 'Salani', 2018, 'Lyra, undicenne sveglia e con un debole per l\\\'ignoto, Ã¨ orfana fin dalla tenera etÃ  e ha sempre vissuto al Jordan College di Oxford. Curiosa e piena di intraprendenza, Lyra non sopporta la quotidianitÃ  cui Ã¨ costretta, fatta di tediosi accademici parrucconi, interessati solo alla teologia e alla politica. Gli unici momenti di evasione consistono nelle sue scorribande in compagnia dell\\\'amico Roger, lo sguattero, e dei bambini gyziani, nomadi emarginati e malvisti dalla societÃ . Il suo sogno Ã¨ di fuggire con l\\\'avventuroso zio, Lord Asriel, verso i misteriosi territori del Nord, dove l\\\'esploratore sta conducendo oscure ricerche. Ma a un tratto la noiosa esistenza di Lyra Ã¨ stravolta: Roger viene rapito, in giro si inizia a mormorare dei malvagi Ingoiatori...Che siano stati loro? Alla ricerca della veritÃ , Lyra scopre di avere piÃ¹ nemici di quello che pensa, ma anche imprevedibili alleati. Ad accompagnarla nel suo viaggio verso il Nord sarÃ  un dono fattole dallo zio, l\\\'aletiometro, uno strumento d\\\'oro in grado di rispondere a qualsiasi domanda, a patto di saperla porre nel modo giusto. In un mondo corrotto e fondato su un precario equilibrio di menzogne, nuove scoperte stanno per rimettere tutto in discussione. EtÃ  di lettura: da 10 anni.', 'img/laBussolaDoro.jpg', 3, 15, 23, 180, 1, 2),
(8, 'JavaScript & JQuery. Sviluppare interfacce web interattive', 'Jon Duckett', 'Salani', 2017, 'Libro molto istruttivo', 'img/JsJQ.jpg', 1, 13.99, 15, 123, 1, 4);

-- --------------------------------------------------------

--
-- Struttura della tabella `lingua`
--

CREATE TABLE `lingua` (
  `IDLingua` int(11) NOT NULL,
  `NomeLingua` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `lingua`
--

INSERT INTO `lingua` (`IDLingua`, `NomeLingua`) VALUES
(1, 'Italiano'),
(2, 'Inglese'),
(3, 'Tedesco'),
(4, 'Francese'),
(5, 'Spagnolo');

-- --------------------------------------------------------

--
-- Struttura della tabella `ordine`
--

CREATE TABLE `ordine` (
  `IDOrdine` int(11) NOT NULL,
  `IDCli` int(11) NOT NULL,
  `IDL` int(11) NOT NULL,
  `Quantita` int(11) NOT NULL,
  `Spedito` tinyint(1) NOT NULL DEFAULT '0',
  `DataOrdine` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Totale` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `ordine`
--

INSERT INTO `ordine` (`IDOrdine`, `IDCli`, `IDL`, `Quantita`, `Spedito`, `DataOrdine`, `Totale`) VALUES
(1, 1, 2, 1, 1, '2021-02-18 14:10:00', 119),
(2, 1, 4, 1, 1, '2021-02-18 16:45:27', 22),
(3, 1, 5, 1, 1, '2021-02-18 16:45:27', 11.99),
(4, 2, 4, 1, 1, '2021-02-18 16:46:27', 22),
(5, 2, 1, 1, 1, '2021-02-18 16:46:27', 32.9),
(6, 2, 2, 2, 1, '2021-02-18 16:50:01', 238),
(7, 2, 1, 1, 1, '2021-02-18 16:50:01', 32.9),
(10, 1, 4, 1, 1, '2021-02-18 21:17:30', 22),
(11, 2, 1, 2, 1, '2021-02-18 21:18:12', 65.8),
(12, 1, 1, 2, 1, '2021-02-18 22:27:14', 65.8),
(13, 1, 3, 1, 1, '2021-02-18 22:27:14', 119),
(14, 1, 4, 1, 1, '2021-02-28 21:19:05', 22.3),
(15, 1, 4, 1, 1, '2021-02-28 21:46:24', 22.3),
(16, 1, 4, 1, 1, '2021-02-28 21:46:59', 22.3),
(17, 1, 4, 1, 1, '2021-02-28 21:56:06', 22.3),
(18, 1, 4, 1, 1, '2021-02-28 21:56:38', 22.3),
(19, 1, 2, 1, 1, '2021-03-01 12:14:45', 119),
(20, 1, 2, 1, 1, '2021-03-01 12:15:43', 119),
(21, 1, 2, 1, 1, '2021-03-01 12:16:38', 119),
(22, 1, 2, 1, 1, '2021-03-01 12:29:45', 119),
(23, 1, 2, 1, 1, '2021-03-01 12:30:54', 119),
(24, 1, 1, 1, 1, '2021-03-01 12:31:10', 10000),
(25, 1, 2, 1, 1, '2021-03-01 13:02:36', 119),
(26, 1, 4, 1, 1, '2021-03-01 13:03:03', 22.3),
(27, 1, 2, 1, 1, '2021-03-01 13:03:36', 119),
(28, 1, 2, 1, 1, '2021-03-01 13:07:33', 119),
(29, 1, 1, 1, 1, '2021-03-01 13:09:39', 10000),
(30, 1, 1, 1, 1, '2021-03-01 13:11:05', 10000),
(31, 1, 3, 2, 1, '2021-03-01 13:12:26', 238),
(32, 1, 1, 1, 1, '2021-03-01 13:13:08', 10000),
(33, 1, 3, 2, 1, '2021-03-01 13:13:54', 238),
(34, 1, 1, 1, 1, '2021-03-01 13:14:32', 10000),
(35, 1, 3, 1, 1, '2021-03-01 13:15:02', 119),
(36, 1, 1, 1, 1, '2021-03-01 13:15:02', 10000),
(37, 1, 3, 2, 1, '2021-03-01 13:17:02', 238),
(38, 1, 4, 1, 1, '2021-03-01 13:18:39', 22.3),
(39, 1, 4, 1, 1, '2021-03-01 13:18:57', 22.3),
(40, 1, 1, 1, 1, '2021-03-01 13:18:57', 10000),
(41, 1, 4, 1, 1, '2021-03-01 13:21:34', 22.3),
(42, 1, 1, 2, 1, '2021-03-01 13:23:02', 20000),
(43, 1, 4, 1, 1, '2021-03-01 13:23:02', 22.3),
(44, 1, 1, 2, 1, '2021-03-01 13:23:41', 20000),
(45, 1, 4, 1, 1, '2021-03-01 13:24:12', 22.3),
(46, 1, 1, 1, 1, '2021-03-01 13:24:12', 10000),
(47, 1, 3, 4, 1, '2021-03-01 13:25:23', 476),
(48, 1, 1, 1, 1, '2021-03-01 13:26:36', 10000),
(49, 1, 2, 2, 1, '2021-03-01 13:27:15', 238),
(50, 1, 2, 1, 1, '2021-03-01 14:50:38', 119),
(51, 1, 1, 1, 1, '2021-03-01 14:51:17', 10000),
(52, 1, 2, 1, 1, '2021-03-01 14:55:27', 119),
(53, 1, 3, 1, 1, '2021-03-01 14:55:27', 119),
(54, 1, 2, 1, 1, '2021-03-01 14:57:05', 119),
(55, 1, 1, 1, 1, '2021-03-01 14:58:06', 10000),
(56, 1, 2, 2, 1, '2021-03-01 15:09:16', 238),
(57, 1, 1, 1, 1, '2021-03-01 15:09:33', 10000),
(58, 1, 4, 1, 1, '2021-03-01 15:11:15', 22.3),
(59, 1, 1, 1, 1, '2021-03-01 15:11:42', 10000),
(60, 1, 2, 1, 1, '2021-03-01 15:12:43', 119),
(61, 1, 1, 1, 1, '2021-03-01 15:12:44', 10000),
(62, 1, 4, 1, 1, '2021-03-01 15:13:26', 22.3),
(63, 1, 1, 1, 1, '2021-03-01 15:13:26', 10000),
(64, 1, 4, 1, 1, '2021-03-01 15:16:29', 22.3),
(65, 1, 1, 2, 1, '2021-03-01 15:18:07', 20000),
(66, 1, 2, 2, 1, '2021-03-01 15:20:28', 238),
(67, 1, 1, 1, 1, '2021-03-01 15:22:14', 10000),
(68, 1, 1, 1, 1, '2021-03-01 15:23:39', 10000),
(69, 1, 4, 1, 1, '2021-03-01 15:23:39', 22.3),
(70, 1, 2, 2, 1, '2021-03-01 15:24:33', 238),
(71, 1, 1, 1, 1, '2021-03-01 15:24:33', 10000),
(72, 1, 4, 1, 1, '2021-03-01 17:21:37', 22.3),
(73, 1, 2, 1, 1, '2021-03-01 17:21:37', 119),
(74, 1, 1, 1, 1, '2021-03-01 17:24:19', 10000),
(75, 1, 2, 1, 1, '2021-03-01 17:24:19', 119),
(76, 1, 4, 1, 1, '2021-03-01 17:24:19', 22.3),
(77, 1, 1, 1, 1, '2021-03-06 18:06:09', 10000),
(78, 1, 4, 1, 1, '2021-03-06 18:07:08', 22.3),
(79, 1, 2, 1, 1, '2021-03-06 18:07:08', 119),
(80, 1, 2, 1, 1, '2021-03-06 18:19:48', 119),
(81, 1, 2, 1, 1, '2021-03-06 18:22:17', 119),
(82, 1, 1, 2, 1, '2021-03-06 18:22:17', 20000),
(83, 2, 1, 1, 0, '2021-03-06 18:23:36', 10000),
(84, 2, 2, 2, 0, '2021-03-06 18:23:36', 238),
(85, 1, 5, 2, 0, '2021-03-14 13:57:15', 23.98),
(86, 1, 1, 2, 0, '2021-03-14 13:57:15', 20000),
(87, 1, 1, 1, 0, '2021-03-14 21:02:43', 10000),
(88, 1, 2, 1, 0, '2021-03-14 21:02:43', 119),
(89, 1, 2, 1, 0, '2021-03-14 21:04:47', 119),
(90, 1, 5, 1, 1, '2021-03-14 21:05:23', 11.99),
(91, 1, 5, 32, 0, '2021-03-18 07:35:32', 383.68);

-- --------------------------------------------------------

--
-- Struttura della tabella `ricarica`
--

CREATE TABLE `ricarica` (
  `IDRicarica` int(11) NOT NULL,
  `CodLibro` int(11) NOT NULL,
  `CodImpiegato` int(11) NOT NULL,
  `Quantita` int(11) NOT NULL,
  `DataRicarica` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `ricarica`
--

INSERT INTO `ricarica` (`IDRicarica`, `CodLibro`, `CodImpiegato`, `Quantita`, `DataRicarica`) VALUES
(1, 4, 1, 13, '2021-02-18 16:15:25'),
(2, 5, 1, 13, '2021-02-18 16:23:50'),
(3, 5, 1, 1, '2021-02-18 16:54:20'),
(4, 1, 1, 1, '2021-02-18 22:05:40'),
(6, 2, 1, 1, '2021-02-27 10:32:52'),
(7, 2, 1, 1, '2021-02-27 10:35:32'),
(8, 3, 1, 1, '2021-02-27 10:35:38'),
(9, 4, 1, 123, '2021-02-27 10:35:45'),
(10, 1, 1, 15, '2021-03-13 17:43:53'),
(28, 2, 1, 0, '2021-03-13 20:06:06'),
(29, 2, 1, 4, '2021-03-13 20:06:12'),
(30, 1, 1, 3, '2021-03-13 20:06:19'),
(31, 1, 1, -1, '2021-03-13 20:06:27'),
(32, 1, 1, -1, '2021-03-13 20:06:54'),
(33, 1, 1, 0, '2021-03-13 20:08:10'),
(34, 1, 1, 5, '2021-03-13 20:11:43'),
(35, 1, 1, 12, '2021-03-13 20:13:05'),
(36, 5, 1, 23, '2021-03-13 20:13:15'),
(37, 5, 1, 12, '2021-03-13 20:13:31'),
(38, 1, 1, 14, '2021-03-13 20:13:58'),
(39, 2, 1, 2, '2021-03-13 20:14:09'),
(40, 2, 1, 23, '2021-03-13 20:15:44'),
(41, 2, 1, 5, '2021-03-13 20:41:09'),
(42, 1, 1, 12, '2021-03-13 20:44:05'),
(43, 1, 1, 23, '2021-03-13 20:44:58'),
(44, 5, 1, 5, '2021-03-13 20:51:20'),
(45, 5, 1, 12, '2021-03-13 20:59:48'),
(46, 2, 1, 100, '2021-03-13 21:00:45'),
(47, 4, 1, 100, '2021-03-13 21:01:11'),
(48, 5, 1, 3, '2021-03-13 21:01:28'),
(49, 4, 1, 23, '2021-03-13 21:02:31'),
(50, 1, 1, 2, '2021-03-17 17:38:50'),
(51, 2, 1, 23, '2021-03-17 23:51:49'),
(52, 3, 1, 2, '2021-03-17 23:51:55'),
(53, 2, 1, 2, '2021-03-17 23:52:05'),
(54, 3, 1, 4, '2021-03-18 07:34:28'),
(55, 6, 1, 15, '2021-03-19 08:45:45'),
(56, 7, 1, 23, '2021-03-19 08:48:49'),
(57, 8, 1, 15, '2021-03-19 10:04:24');

-- --------------------------------------------------------

--
-- Struttura della tabella `spedizione`
--

CREATE TABLE `spedizione` (
  `IDSpedizione` int(11) NOT NULL,
  `CodOrdine` int(11) NOT NULL,
  `CodImpiegato` int(11) NOT NULL,
  `DataSpedizione` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `spedizione`
--

INSERT INTO `spedizione` (`IDSpedizione`, `CodOrdine`, `CodImpiegato`, `DataSpedizione`) VALUES
(1, 1, 1, '2021-02-18 16:44:40'),
(2, 2, 1, '2021-02-18 16:54:26'),
(3, 3, 1, '2021-02-18 16:54:26'),
(4, 4, 1, '2021-02-18 16:54:27'),
(5, 5, 1, '2021-02-18 16:54:28'),
(6, 6, 1, '2021-02-18 16:54:29'),
(7, 7, 1, '2021-02-18 16:54:29'),
(8, 8, 1, '2021-02-18 22:06:11'),
(9, 10, 1, '2021-02-27 10:39:02'),
(10, 11, 1, '2021-02-27 10:40:38'),
(11, 12, 1, '2021-02-27 10:40:41'),
(12, 13, 1, '2021-02-27 10:40:42'),
(13, 76, 1, '2021-03-01 17:25:16'),
(14, 14, 1, '2021-03-04 12:34:43'),
(15, 15, 1, '2021-03-04 12:34:43'),
(16, 16, 1, '2021-03-04 12:34:44'),
(17, 17, 1, '2021-03-04 12:34:45'),
(18, 18, 1, '2021-03-04 12:34:45'),
(19, 19, 1, '2021-03-04 12:34:46'),
(20, 20, 1, '2021-03-04 12:34:46'),
(21, 21, 1, '2021-03-04 12:34:46'),
(22, 22, 1, '2021-03-04 12:34:47'),
(23, 23, 1, '2021-03-04 12:34:47'),
(24, 24, 1, '2021-03-04 12:34:48'),
(25, 25, 1, '2021-03-04 12:34:48'),
(26, 26, 1, '2021-03-04 12:34:49'),
(27, 27, 1, '2021-03-04 12:34:49'),
(28, 28, 1, '2021-03-04 12:34:50'),
(29, 29, 1, '2021-03-04 12:34:50'),
(30, 30, 1, '2021-03-04 12:34:50'),
(31, 31, 1, '2021-03-04 12:34:51'),
(32, 32, 1, '2021-03-04 12:34:51'),
(33, 33, 1, '2021-03-04 12:34:51'),
(34, 34, 1, '2021-03-04 12:34:52'),
(35, 35, 1, '2021-03-04 12:34:52'),
(36, 36, 1, '2021-03-04 12:34:54'),
(37, 37, 1, '2021-03-04 12:34:54'),
(38, 38, 1, '2021-03-04 12:34:54'),
(39, 39, 1, '2021-03-04 12:34:55'),
(40, 40, 1, '2021-03-04 12:34:55'),
(41, 41, 1, '2021-03-04 12:34:56'),
(42, 42, 1, '2021-03-04 12:34:56'),
(43, 43, 1, '2021-03-04 12:34:57'),
(44, 44, 1, '2021-03-04 12:34:57'),
(45, 45, 1, '2021-03-04 12:34:58'),
(46, 75, 1, '2021-03-04 12:35:01'),
(47, 54, 1, '2021-03-04 12:35:01'),
(48, 55, 1, '2021-03-04 12:35:02'),
(49, 56, 1, '2021-03-04 12:35:02'),
(50, 57, 1, '2021-03-04 12:35:03'),
(51, 58, 1, '2021-03-04 12:35:03'),
(52, 59, 1, '2021-03-04 12:35:04'),
(53, 60, 1, '2021-03-04 12:35:05'),
(54, 61, 1, '2021-03-04 12:35:05'),
(55, 62, 1, '2021-03-04 12:35:06'),
(56, 63, 1, '2021-03-04 12:35:06'),
(57, 64, 1, '2021-03-04 12:35:06'),
(58, 65, 1, '2021-03-04 12:35:07'),
(59, 66, 1, '2021-03-04 12:35:07'),
(60, 67, 1, '2021-03-04 12:35:08'),
(61, 68, 1, '2021-03-04 12:35:08'),
(62, 69, 1, '2021-03-04 12:35:09'),
(63, 70, 1, '2021-03-04 12:35:09'),
(64, 71, 1, '2021-03-04 12:35:09'),
(65, 72, 1, '2021-03-04 12:35:10'),
(66, 73, 1, '2021-03-04 12:35:11'),
(67, 74, 1, '2021-03-04 12:35:11'),
(68, 53, 1, '2021-03-04 12:35:26'),
(69, 52, 1, '2021-03-04 12:35:28'),
(70, 51, 1, '2021-03-04 12:35:29'),
(71, 50, 1, '2021-03-04 12:35:30'),
(72, 49, 1, '2021-03-04 12:35:31'),
(74, 1, 2, '2021-03-13 15:51:28'),
(75, 46, 1, '2021-03-13 17:39:34'),
(76, 47, 1, '2021-03-13 17:39:50'),
(77, 48, 1, '2021-03-13 17:40:29'),
(78, 77, 1, '2021-03-13 17:40:39'),
(79, 78, 1, '2021-03-13 17:40:53'),
(80, 79, 1, '2021-03-13 17:41:04'),
(81, 80, 1, '2021-03-13 17:41:21'),
(82, 81, 1, '2021-03-13 21:02:48'),
(83, 90, 1, '2021-03-17 23:53:49'),
(84, 82, 1, '2021-03-18 07:34:37');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`IDCliente`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indici per le tabelle `contratto`
--
ALTER TABLE `contratto`
  ADD PRIMARY KEY (`IDContratto`);

--
-- Indici per le tabelle `formato`
--
ALTER TABLE `formato`
  ADD PRIMARY KEY (`IDFormato`);

--
-- Indici per le tabelle `genere`
--
ALTER TABLE `genere`
  ADD PRIMARY KEY (`IDGenere`);

--
-- Indici per le tabelle `impiegato`
--
ALTER TABLE `impiegato`
  ADD PRIMARY KEY (`IDImpiegato`),
  ADD UNIQUE KEY `Email` (`NomeUtente`);

--
-- Indici per le tabelle `libro`
--
ALTER TABLE `libro`
  ADD PRIMARY KEY (`IDLibro`),
  ADD UNIQUE KEY `Titolo` (`Titolo`,`Autore`,`Editore`,`AnnoPubblicazione`,`CodiceFormato`,`CodiceLingua`),
  ADD KEY `CodiceFormato` (`CodiceFormato`),
  ADD KEY `CodiceLingua` (`CodiceLingua`),
  ADD KEY `CodiceGen` (`CodiceGenere`);

--
-- Indici per le tabelle `lingua`
--
ALTER TABLE `lingua`
  ADD PRIMARY KEY (`IDLingua`);

--
-- Indici per le tabelle `ordine`
--
ALTER TABLE `ordine`
  ADD PRIMARY KEY (`IDOrdine`,`IDCli`,`IDL`),
  ADD KEY `IDCli` (`IDCli`),
  ADD KEY `IDL` (`IDL`);

--
-- Indici per le tabelle `ricarica`
--
ALTER TABLE `ricarica`
  ADD PRIMARY KEY (`IDRicarica`,`CodLibro`,`CodImpiegato`),
  ADD KEY `CodImpiegato` (`CodImpiegato`),
  ADD KEY `CodLibro` (`CodLibro`);

--
-- Indici per le tabelle `spedizione`
--
ALTER TABLE `spedizione`
  ADD PRIMARY KEY (`IDSpedizione`,`CodOrdine`,`CodImpiegato`),
  ADD KEY `CodImpiegato` (`CodImpiegato`),
  ADD KEY `CodOrdine` (`CodOrdine`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `cliente`
--
ALTER TABLE `cliente`
  MODIFY `IDCliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la tabella `contratto`
--
ALTER TABLE `contratto`
  MODIFY `IDContratto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `formato`
--
ALTER TABLE `formato`
  MODIFY `IDFormato` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT per la tabella `genere`
--
ALTER TABLE `genere`
  MODIFY `IDGenere` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT per la tabella `impiegato`
--
ALTER TABLE `impiegato`
  MODIFY `IDImpiegato` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `libro`
--
ALTER TABLE `libro`
  MODIFY `IDLibro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT per la tabella `lingua`
--
ALTER TABLE `lingua`
  MODIFY `IDLingua` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT per la tabella `ordine`
--
ALTER TABLE `ordine`
  MODIFY `IDOrdine` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT per la tabella `ricarica`
--
ALTER TABLE `ricarica`
  MODIFY `IDRicarica` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT per la tabella `spedizione`
--
ALTER TABLE `spedizione`
  MODIFY `IDSpedizione` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `libro`
--
ALTER TABLE `libro`
  ADD CONSTRAINT `libro_ibfk_1` FOREIGN KEY (`CodiceFormato`) REFERENCES `formato` (`IDFormato`),
  ADD CONSTRAINT `libro_ibfk_2` FOREIGN KEY (`CodiceLingua`) REFERENCES `lingua` (`IDLingua`),
  ADD CONSTRAINT `libro_ibfk_3` FOREIGN KEY (`CodiceGenere`) REFERENCES `genere` (`IDGenere`);

--
-- Limiti per la tabella `ordine`
--
ALTER TABLE `ordine`
  ADD CONSTRAINT `ordine_ibfk_1` FOREIGN KEY (`IDCli`) REFERENCES `cliente` (`IDCliente`),
  ADD CONSTRAINT `ordine_ibfk_2` FOREIGN KEY (`IDL`) REFERENCES `libro` (`IDLibro`);

--
-- Limiti per la tabella `ricarica`
--
ALTER TABLE `ricarica`
  ADD CONSTRAINT `ricarica_ibfk_1` FOREIGN KEY (`CodImpiegato`) REFERENCES `impiegato` (`IDImpiegato`),
  ADD CONSTRAINT `ricarica_ibfk_2` FOREIGN KEY (`CodLibro`) REFERENCES `libro` (`IDLibro`);

--
-- Limiti per la tabella `spedizione`
--
ALTER TABLE `spedizione`
  ADD CONSTRAINT `spedizione_ibfk_1` FOREIGN KEY (`CodImpiegato`) REFERENCES `impiegato` (`IDImpiegato`),
  ADD CONSTRAINT `spedizione_ibfk_2` FOREIGN KEY (`CodOrdine`) REFERENCES `ordine` (`IDOrdine`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
