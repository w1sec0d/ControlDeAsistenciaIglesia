<?php
    $conection = mysqli_connect("localhost","root","","iglesia"); 
    if ($conection->connect_error) {
        die($conection->connect_errno);
    }     
?>