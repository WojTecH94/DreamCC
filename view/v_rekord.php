<?php


//połączenie z bazą
//include_once 'DB.php';
include 'config.php';
include 'contact.php';
$connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE) or die('mysqli_connect_error()');
$connection->set_charset("utf8");
//wywołanie procedury rezerwującej rekord
$query = "CALL reserve_token('". $_SESSION["user"] . "')";
$result = $connection->query($query);

// wyświetlanie
	if($result->num_rows > 0) { //zarezerwowano dostępny rekord
		$row = $result->fetch_assoc();
                $rekord = new contact($row["tok"], $row["firstname"], $row["lastname"], $row["status"], $row["attempt"]);
                echo $rekord->present();
                echo '<p><a target="_blank" class="btn btn-primary btn-lg" role="button" href="' . $rekord->gen_link($_SESSION["user"]) . '">Przejdź do ankiety &raquo;</a> ';
                echo ' <a class="btn btn-primary btn-lg" role="button" href="rekord.php">Pobierz kolejny &raquo;</a></p>';

	}
	else {
		echo 'W tym momencie brak wolnych rekordów. Poczekaj aż rekordy wrócą do bazy';	
	}
        
