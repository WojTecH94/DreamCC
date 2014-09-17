<?php

include 'config.php';
include 'contact.php';
$connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE) or die('mysqli_connect_error()');
$connection->set_charset("utf8");
?>
<h1>Kontakty nieprzedzwonione</h1>

<?php



//wyszukanie rekordu
$query = "SELECT * FROM v_left_contacts";
$result = $connection->query($query) or die("query error: " . $connection->error);
?>
<table class="table">
    <tr>
        <th>token</th>
        <th>Imię</th>
        <th>Nazwisko</th>
        <th>Status</th>
        <th>Próba dotarcia</th>
        <th>Uwagi po rozmowie</th>
        <th>Rezerwacja</th>
        <th>Ankieta&raquo;</th>
    </tr>
    <?php
    // wyświetlanie
            if($result->num_rows > 0) { //zarezerwowano dostępny rekord
                  while ($row = $result->fetch_assoc()) {
                      $rekord = new contact($row["token"], $row["firstname"], $row["lastname"], $row["status"], $row["attempt"]);
                        echo '<tr>';
                            echo '<td>' . $rekord->token . '</td>';
                            echo '<td>' . $rekord->firstname . '</td>';
                            echo '<td>' . $rekord->lastname . '</td>';
                            echo '<td>' . $rekord->status . '</td>';
                            echo '<td>' . $rekord->attempt . '</td>';
                            echo '<td>' . $row['notes'] . '</td>';
                            echo '<td>' . $row['reserved'] . '</td>';
                            echo '<td><a href="reserve.php?tok=' . $rekord->token . '">Ankieta&raquo;</a></td>'; // link do reserve przekazujący tokena w GECIE
                        echo '</tr>';
                    }
            }
            else {
                    echo 'Brak wyników';	
            }

       ?>     
</table>

