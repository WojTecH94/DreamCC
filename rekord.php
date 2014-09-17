<?php
//inicjalizacja sesji
session_start();
session_register("zalogowany");

if(empty($_SESSION["zalogowany"]))$_SESSION["zalogowany"]=0;


//funkcja generująca formularz logowania
function ShowLogin($komunikat=""){
	echo "<form class='navbar-form navbar-right' action='index.php' method=post>";
        echo    "<div class='form-group'>";
        if($komunikat) {echo        "<span class='alert alert-danger'>" . $komunikat . "</span>";};
        echo    "</div>";
        echo    "<div class='form-group'>";
        echo      "<input type='text' placeholder='login' class='form-control' name='login'>";
        echo    "</div>";
        echo    "<div class='form-group'>";
        echo      "<input type='password' placeholder='Password' class='form-control' name='haslo'>";
        echo    "</div>";
        echo    "<button type='submit' value='Submit' class='btn btn-success'>Zaloguj</button>";
        echo  "</form>";
}

?>

<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="../../docs-assets/ico/favicon.png">

    <title>Call Center</title>

    <!-- Bootstrap core CSS -->
    <link href="dist/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="jumbotron.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../docs-assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php">Call Center</a>
        </div>
        <div class="navbar-collapse collapse">
            
          <?php
            //wylogowywanie
            if($_GET["wyloguj"]=="tak"){$_SESSION["zalogowany"]=0;$_SESSION["user"]="";}
            //logowanie
            if($_SESSION["zalogowany"]!=1){
                    if(!empty($_POST["login"]) && !empty($_POST["haslo"])){
                            if($_POST["login"]=="test" && $_POST["haslo"]=="test"){
                                    echo "<div class='navbar-form navbar-right'>";
                                    echo "<span class='alert alert-success'>Zalogowano poprawnie.<span> <a href='index.php?wyloguj=tak' class='btn btn-success'>Wyloguj</a>";
                                    echo "</div>";
                                    $_SESSION["zalogowany"]=1;
                                    $_SESSION["user"]=$_POST["login"];
                                    }
                            else echo ShowLogin("Podano złe dane!!!");
                            }
                    else ShowLogin();
            }
            //jeżeli użytkownik jest zalogowany:
            else{
                echo "<div class='navbar-form navbar-right'>";
                echo "<a href='index.php?wyloguj=tak' class='btn btn-success'>Wyloguj</a>";
                echo "</div>";
            }
            ?>
            
        </div><!--/.navbar-collapse -->
      </div>
    </div>
      
       <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron">
      <div class="container">
        
           <?php
           //jeśli zalogowany pokaż treść
                    if($_SESSION["zalogowany"]==1){
                        include_once 'view/v_rekord.php';
                    }
            ?>
        
    </div>

    <div class="container">
      <!-- Example row of columns -->
      <div class="row">
        <div class="col-md-4">

            <p></p>
        </div>
        <div class="col-md-4">

       </div>
        <div class="col-md-4">

        </div>
      </div>


      <hr>

      <footer>
        <p>Stowarzyszenie WIOSNA</p>
      </footer>
    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="dist/js/bootstrap.min.js"></script>
  </body>
