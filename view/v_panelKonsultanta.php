<?php

//wybieranie danych do liczników
//
//
//połączenie z bazą
//include_once 'DB.php';
include 'config.php';
include 'contact.php';
$connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE) or die('mysqli_connect_error()');
$connection->set_charset("utf8");
// pobieranie ilośći wybranych rekordów w ostatniej godzinie przez obecnie zalogowanego użytkownika
$query = "SELECT tries FROM no_of_tries WHERE operator ='". $_SESSION["user"] . "'";
$result1 = $connection->query($query);
	if($result1->num_rows > 0) {
		$row = $result1->fetch_assoc();
                $tries = $row["tries"]; 
	}
	else {
		$tries = 0;	
	}
// pobieranie ilości przeprowadzonych rozmów w ciągu ostatniej godziny
$query = "SELECT succeeded FROM no_of_succeeded WHERE operator ='". $_SESSION["user"] . "'";
$result2 = $connection->query($query);
	if($result2->num_rows > 0) {
		$row = $result2->fetch_assoc();
                $succeeded = $row["succeeded"]; 
	}
	else {
		$succeeded = 0;	
	}
// pobieranie ilości rekordów przypisanych do konsultanta
$query = "SELECT COUNT(1) AS `contacts` FROM v_contacts WHERE operator = '". $_SESSION["user"] . "' GROUP BY operator";
$result3 = $connection->query($query);
	if($result3->num_rows > 0) {
		$row = $result3->fetch_assoc();
                $contacts = $row["contacts"]; 
	}
	else {
		$contacts = 0;	
	}
// pobieranie ilości rekordów nieprzedzwonionych przez konsultanta
$query = "SELECT COUNT(1) AS `left` FROM v_left_contacts WHERE operator = '". $_SESSION["user"] . "' GROUP BY operator";
$result4 = $connection->query($query);
	if($result4->num_rows > 0) {
		$row = $result4->fetch_assoc();
                $left = $row["left"]; 
	}
	else {
		$left = 0;	
	}
// pobieranie średniego czasu potrzebnego do przeprowadzenia skutecznej rozmowy
$query = "SELECT avg_time FROM v_avg_timings WHERE operator = '". $_SESSION["user"] . "'";
$result5 = $connection->query($query);
	if($result5->num_rows > 0) {
		$row = $result5->fetch_assoc();
                $avgTime = $row["avg_time"]; 
	}
	else {
		$avgTime = 0;	
	}     
?>
<!-- rysowanie wykresów start -->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["gauge"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        // rozmowy przeprowadzone/h
        var succeeded = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['', 0]
        ]);
        
        var succeeded_options = {
          width: 250, height: 250,
          redFrom: 18, redTo: 20,
          yellowFrom: 15, yellowTo: 18,
          minorTicks: 5, max: 20
        };
        
        // wybranych rekordów/h
        var tries = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['', 0]
        ]);

        var tries_options = {
            width: 250, height: 250,
          redFrom: 35, redTo: 40,
          yellowFrom:30, yellowTo: 35,
          minorTicks: 10, max: 40
        };
        
        // pozostało do przedzwonienia
        var left = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['', 0]
        ]);

        var left_options = {
            width: 250, height: 250,
         // redFrom: 26, redTo: 30,
         // yellowFrom:20, yellowTo: 26,
         minorTicks: <?php echo ceil($contacts/4); ?>, //obliczenie ile powinno być kreseczek
          max: <?php echo $contacts; ?> //wstawienie ilości kontaktów do przedzwonienia
        };

        // średni czas rozmowy
        var time = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['', 0]
        ]);

        var time_options = {
            width: 250, height: 250,
          greenFrom: 0, greenTo: 10,
          minorTicks: 15, max: 30
        };
        
        var succeeded_chart = new google.visualization.Gauge(document.getElementById('succeeded_div'));
        succeeded_chart.draw(succeeded, succeeded_options);
        var tries_chart = new google.visualization.Gauge(document.getElementById('tries_div'));
        tries_chart.draw(tries, tries_options);
        var left_chart = new google.visualization.Gauge(document.getElementById('left_div'));
        left_chart.draw(left, left_options);
        var time_chart = new google.visualization.Gauge(document.getElementById('time_div'));
        time_chart.draw(time, time_options);
        
        // ustawianie wartości wykresów po sekundzie - dzięki temu widać animację
        setTimeout(function() {
          succeeded.setValue(0, 1, <?php echo $succeeded; ?>); //wstawienie wartości pobranej z bazy
          succeeded_chart.draw(succeeded, succeeded_options);
        }, 500);
        setTimeout(function() {
          tries.setValue(0, 1, <?php echo $tries; ?>); //wstawienie wartości pobranej z bazy
          tries_chart.draw(tries, tries_options);
        }, 500);
        setTimeout(function() {
          left.setValue(0, 1, <?php echo $contacts-$left ?>); //obliczenie ile kontaktów przedzwoniono
          left_chart.draw(left, left_options);
        }, 500);
        setTimeout(function() {
          time.setValue(0, 1, <?php echo $avgTime; ?>); // średni czas
          time_chart.draw(time, time_options);
        }, 500);
      }
      
    </script>
<!-- rysowanie wykresów end -->
        <h1>Witaj <?php echo $_SESSION["user"] ?></h1>
        <div style="display: inline-block; text-align: center">
            <div id="succeeded_div" ></div>
            <p>Przeprowadzonych/h</p>   
        </div>
        <div style="display: inline-block; text-align: center">
            <div id="tries_div" ></div>
            <p syle="text-align: center">Wybranych/h</p>
        </div>
        <div style="display: inline-block; text-align: center">
            <div id="left_div" ></div>
            <p>Przedzwoniono</p>
        </div>
        <div style="display: inline-block; text-align: center">
            <div id="time_div" ></div>
            <p>Śr. czas rozmowy (m)</p>
        </div>
        
        
        <p style="clear: both">
            <a class="btn btn-primary btn-lg" role="button" href="rekord.php">Pobierz rekord &raquo;</a>
            <a class="btn btn-primary btn-lg" role="button" href="search.php">Wyszukaj rekord &raquo;</a>
            <a class="btn btn-primary btn-lg" role="button" href="rescheduled.php">Przełożone rozmowy &raquo;</a>
            <a class="btn btn-primary btn-lg" role="button" href="left.php">Nieprzedzwonione kontakty &raquo;</a>
        </p>
        </div>

        