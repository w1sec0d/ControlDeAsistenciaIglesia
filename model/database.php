<?php
    //    $conection = mysqli_connect("pdb52.awardspace.net","3575716_iglesia","Lg276c30","3575716_iglesia");
    $conection = mysqli_connect("localhost","root","","IGLESIA");
    if($conection->connect_error){
        die($conection->connect_errno);
    }
