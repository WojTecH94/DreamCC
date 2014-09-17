<?php
session_start();
?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
<?php

if($_SESSION["zalogowany"]==0){echo "nie masz dostępu do tej części witryny. <a href='index.php'>Zaloguj się</a></body></html>;"; exit();}
else { 
    include('view/v_panelKonsultanta.php');
}
?>
    </body>
</html>
