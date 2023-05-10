<?php
    echo
    '<!doctype html>
     <html lang="it">

        <head>

            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

	        <link rel="shortcut icon" href="img/logo.png" />

            <meta charset="utf-8">

            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

            <link rel="stylesheet" href="css/style.css">

            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">

            <link rel="preconnect" href="https://fonts.gstatic.com">
			
            <link href="https://fonts.googleapis.com/css2?family=Yanone+Kaffeesatz:wght@300&display=swap" rel="stylesheet">

            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

            <style>
            
                body {

                    font-family: "Yanone Kaffeesatz", sans-serif;
                    font-size: 25px;
                }
            
            </style>

        </head>

        <body>

            <div class="strisciaColorata"></div>

            <nav class="navbar navbar-expand-lg navbar-dark bg-primary">

                <a class="navbar-brand" href="index.php"> LORVINCFRALMA </a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">

                    <span class="navbar-toggler-icon"></span>

                </button>

                <div class="collapse navbar-collapse" id="navbarScroll">

                    <ul id="headerList" class="navbar-nav mr-auto my-2 my-lg-0 navbar-nav-scroll">

                        <li class="nav-item active">

                            <a class="nav-link" href="index.php"> Home <span class="sr-only">(current)</span></a>

                        </li>

                        <li class="nav-item dropdown">';
                            
                            if(isset($_SESSION["Email"])) { // se l'utente è loggato (variabile globale settata) allora stampa il suo nome
                                
                                echo 
                                "<a class=\"nav-link dropdown-toggle\" href=\"#\" id=\"navbarScrollingDropdown\" role=\"button\" data-toggle=\"dropdown\" aria-expanded=\"false\"> 
                                    Ciao " . $_SESSION["Nome"] . 
                                "</a>";
                            }
                            
                            else { // altirmenti se l'utente non è loggato stampa stringa generica
                                
                                echo 
                                "<a class=\"nav-link dropdown-toggle\" href=\"#\" id=\"navbarScrollingDropdown\" role=\"button\" data-toggle=\"dropdown\" aria-expanded=\"false\"> 
                                    Utente 
                                </a>";
                            }

                                echo '<ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">';
                                        
                                    if (isset($_SESSION["Email"])) { // se l'utente è loggato si personalizza il menu dropdown

                                        echo "<li> <a href=\"profile.php?viewGif=true\" class=\"dropdown-item\"> Profilo utente </a> </li>";
                                        echo '<li> <hr class="dropdown-divider"> </li>';
                                        echo "<li> <a href=\"includes/PHP/logout.inc.php\" class=\"dropdown-item\"> Esci </a> </li>";
                                    } 
                                    
                                    else { // altrimenti nel menu dropdown si inseriscono i link alle pagine di accesso/registrazione

                                        echo "<li> <a class=\"dropdown-item\" href=\"login.php\"> Accesso </a> </li>";
                                        echo "<li> <a class=\"dropdown-item\" href=\"signup.php\"> Registrazione </a> </li>";                                    
                                    }

                                echo '</ul>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="chisiamo.php" tabindex="-1" aria-disabled="true"> Chi siamo </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#contatti"> Contattaci </a>
                        </li>
                        
                        <li class="nav-item">
                            <a id="searchSectionLink" style="visibility: visible" class="nav-link" href="search.php"> Sezione ricerca </a>
                        </li>';

                        if (isset($_SESSION["Email"])) { // se l'utente è loggato può visualizzare il carrello degli acquisti
                           
                            echo
                            '<li class="nav-item">

                                <a id="cartIcon" class="nav-link" href="cart.php"> 
                                    
                                    <div class="badge badge-pill badge-success">
                                        <i class="bi bi-cart mr-2"> </i> 
                                        <span class="cartNumber"> </span>
                                    </div>
                                
                                </a>
                                                        
                                
                            </li>';
                        } 
                    
                    echo
                    '</ul>

                    <div id="searchArea" style="visibility: hidden;" class="d-flex">

                        <input required id="searchField" name="searchfield" class="form-control mr-2" type="search" placeholder="Cerca"
                            aria-label="Search">

                        <button id="searchbtn" class="btn btn-success" type="submit"> Cerca </button>

                    </div>

                </div>

            </nav>';