<?php
session_start();

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home page</title>
</head>
<body>
    <h1>Hello , <?php 
    if(isset($_SESSION['userid'])){
        echo $_SESSION['userid']; 
     } ?>
 
    </h1>


    <h2 style="color:blue;">You are
<?php
    if(isset($_SESSION['usertype'])){
        echo $_SESSION['usertype']; 
     } ?>

    </h2>
    

    <a href="logout.php">LOGOUT</a>
</body>
</html>