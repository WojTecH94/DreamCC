<?php
session_start();
?>
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
