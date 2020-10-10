<?php
session_start();
require_once("../model/database.php");

if (!isset($_SESSION["ID"]) or !isset($_SESSION["CONTRASENA"]) or !isset($_SESSION["SEDE"])) {
    header("Location:./error404.php");
} else {
    $ID = $_SESSION["ID"];
    $CONTRASENA = $_SESSION["CONTRASENA"];
    $SEDE = $_SESSION["SEDE"];
    $loginPastor = "SELECT * FROM USUARIO WHERE ID = $ID AND CONTRASENA = '$CONTRASENA' AND ROL = 'PASTOR' AND SEDE ='$SEDE'";
    $resultadoLoginPastor = mysqli_query($conection, $loginPastor);
    if ($resultadoLoginPastor) {
        $rowsResultadoLoginPastor = mysqli_num_rows($resultadoLoginPastor);
        if (empty($rowsResultadoLoginPastor)) {
            echo "
            <script>
            Swal.fire({
                icon: 'error',
                title: 'Usuario o Contraseña incorrecto',
                text: 'Porfavor inténtalo de nuevo',
                timer: 10000,
                showCloseButton: true
            });
            </script>
            ";
        }
    }
}

if (isset($_REQUEST["borrarCookies"])) {
    header("location: ../index.php?x=" . $_SESSION["CODIGO"]);
    //session_destroy();
    //unset($_COOKIE);
}
function formato($hora) //AGREGA AM,M,PM segun el entero de la fecha y adecua la hora
{
    if ($hora < 12) {
        return $hora . ":00a.m";
    } elseif ($hora >= 13) {
        $hora -= 12;
        return $hora . ":00p.m";
    } elseif ($hora == 12) {
        return $hora . ":00m";
    }
}
function formatoDia($dia)
{
    switch ($dia) {
        case 1:
            return "Domingo";
            break;
        case 2:
            return "Lunes";
            break;
        case 3:
            return "Martes";
            break;
        case 4:
            return "Miercoles";
            break;
        case 5:
            return "Jueves";
            break;
        case 6:
            return "Viernes";
            break;
        case 7:
            return "Sábado";
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
        integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
        integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"
        integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.22/b-1.6.5/b-flash-1.6.5/b-html5-1.6.5/b-print-1.6.5/cr-1.5.2/r-2.2.6/datatables.min.css" />

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript"
        src="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.22/b-1.6.5/b-flash-1.6.5/b-html5-1.6.5/b-print-1.6.5/cr-1.5.2/r-2.2.6/datatables.min.js">
    </script>
    <script src="https://kit.fontawesome.com/482fb72b25.js" crossorigin="anonymous"></script>

    <link href="https://fonts.googleapis.com/css2?family=Lato&family=Roboto:wght@500&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../assets/img/logo.ico" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/style2.css">
    <title>Módulo Pastores</title>
</head>

<body>
    <div class="container-fluid">
        <div class="row justify-content-center align-content-center text-center">
            <nav class="navbar navbar-light bg-light w-100">
                <a class="navbar-brand" href="#">
                    <img src="../assets/img/logo.png" width="40" height="30" class="d-inline-block align-top" alt=""
                        loading="lazy">
                    IPUC
                </a>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <form action="" method="POST">
                            <input type="submit" value="Cerrar Sesión" id="logoutButton" name="borrarCookies"
                                class="btn">
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="row">
            <h1 class="welcome text-center w-100">Bienvenido Pastor</h1>
        </div>

        <?php
        $cultos = "SELECT * FROM CULTOS_CUPOS WHERE SEDE = '$SEDE'";
        $resultadoCultos = mysqli_query($conection, $cultos);

        while ($arrayCultos = mysqli_fetch_array($resultadoCultos)) {
            $ID_CULTO = $arrayCultos['ID'];
            $cuposReservados = "SELECT SUM(CUPOS) as OCUPADOS FROM USUARIO_CULTO WHERE SEDE = '$SEDE' AND ID_CULTO = $ID_CULTO";
            $resultadoCuposReservados = mysqli_query($conection, $cuposReservados);
            $arrayCuposReservados = mysqli_fetch_array($resultadoCuposReservados);
            $numeroCuposReservados = $arrayCuposReservados["OCUPADOS"]; ?>
        <div class="row justify-content-center align-items-center">
            <h2 class="w-100 text-center">Culto <?php echo formatoDia($arrayCultos["DIA"]) ?>
                <?php echo formato($arrayCultos["HORA_INICIO"]) ?>-<?php echo formato($arrayCultos["HORA_FIN"]) ?></h2>
            <h2 class="w-100 text-center">Cupos Reservados: <?php echo $arrayCultos["CUPOS"] ?> /
                <?php echo $arrayCultos["CUPOS"] + $numeroCuposReservados ?></h2>
            <div class="row justify-content-center align-items-center">
                <table class="table table-light table-striped table-hover table-responsive text-center"
                    id="tableUser<?php echo $ID_CULTO ?>" style="margin-top:10px;margin-bottom:10px">
                    <thead class="thead-dark">
                        <th scope="col">DOCUMENTO</th>
                        <th scope="col">NOMBRE</th>
                        <th scope="col">TELEFONO</th>
                        <th scope="col">CUPOS RESERVADOS</th>
                    </thead>
                    <tbody>
                        <?php
                            $usuarios = "SELECT * FROM USUARIO_CULTO WHERE SEDE = '$SEDE' AND ID_CULTO = $ID_CULTO";
            $resultadoUsuarios = mysqli_query($conection, $usuarios);

            while ($mostrar = mysqli_fetch_array($resultadoUsuarios)) {
                ?>
                        <tr>
                            <td><?php echo $mostrar["ID_USUARIO"] ?></td>
                            <td><?php echo $mostrar["NOMBRE_USUARIO"] ?></td>
                            <td><?php echo $mostrar["TELEFONO_USUARIO"] ?></td>
                            <td><?php echo $mostrar["CUPOS"] ?></td>
                        </tr>
                        <script>

                        </script>
                        <?php
            } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        }
        ?>
    </div>
    <?php
    if (isset($_REQUEST["alertSesion"])) {
        echo
            "
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        })
        Toast.fire({
            icon: 'success',
            title: 'Inicio de sesión correcto'
        })
    </script>
    ";
    }
    ?>
    <script>
    $(document).ready(function() {
        var spanishTable = {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Registros del _START_ al _END_ de un total de _TOTAL_ ",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            },
            "buttons": {
                "copy": "Copiar",
                "colvis": "Visibilidad"
            }
        }
        <?php
        $query = "SELECT ID FROM CULTO";
        $resultadoQuery = mysqli_query($conection, $query);
        while ($mostrar = mysqli_fetch_array($resultadoQuery)) {
            ?>
        $('#tableUser<?php echo $mostrar["ID"] ?>').DataTable({
            language: spanishTable,
            responsive: true,
            dom: 'fBtp', // Establece los elementos a mostrar en la tabla
            buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Exportar Excel ',
                    titleAttr: 'Exportar a Excel',
                    className: 'excel'
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Imprimir',
                    titleAttr: 'Imprimir',
                    className: 'imprimir'
                }
            ]
        });
        <?php
        }
        ?>


    });
    </script>
</body>

</html>