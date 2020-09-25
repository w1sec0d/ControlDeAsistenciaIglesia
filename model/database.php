<?php
    $conection = mysqli_connect("localhost","root","","IGLESIA");
    if($conection->connect_error){
        die($conection->connect_errno);
    }
?>