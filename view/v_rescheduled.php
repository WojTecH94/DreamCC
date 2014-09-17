<?php

include 'config.php';
include 'contact.php';
$connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE) or die('mysqli_connect_error()');
$connection->set_charset("utf8");
?>
<h1>Rozmowy przełożone</h1>
<?php






$query = "SELECT firstname, lastname, token, operator, 
IF(reservation_date IS NULL OR TIMESTAMPDIFF(MINUTE, reservation_date,CURRENT_TIMESTAMP())>=15, 'wolny', 'zarezerwowany') AS reserved
, status, attempt, contact_date, consultant, reschedule_date, notes FROM v_contacts  WHERE consultant = '". $_SESSION['user'] ."' AND status = 'Inny termin' ORDER BY reschedule_date ASC";
$result = $connection->query($query);
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
        <th>Umówiony termin</th>
        <th>Ankieta &raquo;</th>
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
                            echo '<td>' . $row['reschedule_date'] . '</td>';
                            echo '<td><a href="reserve.php?tok=' . $rekord->token . '">Ankieta &raquo;</a></td>'; // link do reserve przekazujący tokena w GECIE
                        echo '</tr>';
                    }
            }
            else {
                    echo 'Brak wyników';	
            }

       ?>     
</table>

