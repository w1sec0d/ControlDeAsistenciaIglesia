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
    } else if ($hora >= 13) {
        $hora -= 12;
        return $hora . ":00p.m";
    } else if ($hora == 12) {
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.js"></script>
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
                    <img src="../assets/img/logo.png" width="40" height="30" class="d-inline-block align-top" alt="" loading="lazy">
                    IPUC
                </a>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <form action="" method="POST">
                            <input type="submit" value="Cerrar Sesión" id="logoutButton" name="borrarCookies" class="btn">
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
            $numeroCuposReservados = $arrayCuposReservados["OCUPADOS"];
        ?>
            <div class="row justify-content-center align-items-center">
                <h2 class="w-100 text-center">Culto <?php echo formatoDia($arrayCultos["DIA"]) ?> <?php echo formato($arrayCultos["HORA_INICIO"]) ?>-<?php echo formato($arrayCultos["HORA_FIN"]) ?></h2>
                <h2 class="w-100 text-center">Cupos Reservados: <?php echo $arrayCultos["CUPOS"] ?> / <?php echo $arrayCultos["CUPOS"] + $numeroCuposReservados ?></h2>
                <div class="row">
                    <div class="col text-center">
                        <a href="excel.php?SEDE=<?php echo $SEDE ?>&ID_CULTO=<?php echo $ID_CULTO ?>" onclick="" class="button" style="background:#28a745">
                            <span style="color: White;">
                                Descargar excel
                            </span>
                        </a>
                    </div>
                </div>
                <div class="row" style="width: 90%;">
                    <table class="table table-light table-striped table-hover table-responsive-md text-center" id="tableUser<?php echo $ID_CULTO ?>" style="margin-top:10px;margin-bottom:10px">
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
                            <?php
                            }
                            ?>
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
            $('#tableUser').DataTable({
                "language": spanishTable
            });
        });
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
    </script>
</body>

</html>