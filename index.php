<?php
session_start();
require_once "./view/header.php";
require_once "./model/database.php";
?>

<body>
    <?php
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
    ";
    function formato($hora, $minutos) //AGREGA AM,M,PM segun el entero de la fecha y adecua la hora
    {
        if ($minutos == 0) {
            $minutos = "00";
        }
        if ($hora < 12) {
            return $hora . ":" . $minutos . "a.m";
        } elseif ($hora >= 13) {
            $hora -= 12;
            return $hora . ":" . $minutos . "p.m";
        } elseif ($hora == 12) {
            return $hora . ":$minutos" . "m";
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

    if (isset($_REQUEST["x"])) { //Verifica que tenga el código de iglesia correspondiente
        switch ($_REQUEST["x"]) {
            case '9xY0ltbVuGbG':
                $_SESSION["SEDE"] = "BRITALIA";
                $_SESSION["CODIGO"] = "9xY0ltbVuGbG";
                break;

            case 'KsjJD4DJAv6q':
                $_SESSION["SEDE"] = "TINTALITO";
                $_SESSION["CODIGO"] = "KsjJD4DJAv6q";
                break;

            default:
                echo "
                <script>
                    window.location.href = './view/error404.php';
                </script>
                ";
                break;
        }
    } else {
        echo "
                <script>
                    window.location.href = './view/error404.php';
                </script>
                ";
    }

    if (isset($_REQUEST["submit"])) { //Se ejecuta si hacen click al submit principal, registra la asistencia o da info de error
        $SEDE = $_SESSION["SEDE"];
        $ID = $_REQUEST["ID"];
        $NOMBRE = $_REQUEST["NOMBRE"];
        $CULTO = $_REQUEST["CULTO"];
        $TELEFONO = $_REQUEST["TELEFONO"];
        $PERSONAS = $_REQUEST["PERSONAS"];

        //VERIFICO SI EL USUARIO EXISTE, SI NO, LO CREO
        $verificarUsuario = "SELECT * FROM USUARIO WHERE ID=$ID;";
        $resultadoVerificarUsuario = mysqli_query($conection, $verificarUsuario);
        $rowsResultadoVerificarUsuario = mysqli_num_rows($resultadoVerificarUsuario);

        if (empty($rowsResultadoVerificarUsuario)) {
            $insertUsuario = "INSERT INTO USUARIO(ID,NOMBRE,TELEFONO) VALUES ($ID,'$NOMBRE','$TELEFONO')";
            $resultadoInsertUsuario = mysqli_query($conection, $insertUsuario);
        }

        //VERIFICO QUE EL CULTO TENGA CUPOS
        $verificarCupos = "SELECT CUPOS FROM CULTOS_CUPOS WHERE ID = $CULTO";
        $resultadoVerificarCupos = mysqli_query($conection, $verificarCupos);
        $arrayResultadoVerificarCupos = $resultadoVerificarCupos->fetch_assoc();

        $verificarRegistroCulto = "SELECT * FROM CULTO_USUARIO WHERE IDCULTOFK = $CULTO AND IDUSUARIOFK = $ID";
        $resultadoVerificarRegistroCulto = mysqli_query($conection, $verificarRegistroCulto);
        $rowsResultadoVerificarCupos = mysqli_num_rows($resultadoVerificarRegistroCulto);

        if ($CULTO == 0) {
            echo "
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'No has seleccionado ningún culto',
                    text: 'Porfavor selecciona un culto',
                    showCloseButton: true,
                    timer:10000
                })
            </script>

            ";
        } elseif ($arrayResultadoVerificarCupos["CUPOS"] - $PERSONAS < 0) {
            if ($arrayResultadoVerificarCupos["CUPOS"] == 0) {
                echo "
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'El culto al que deseas registrarte está lleno',
                    text: 'Porfavor considera registrarte en otro culto',
                    footer: 'Si necesitas ayuda, escribe un correo a: cadavid4003@gmail.com',
                    showCloseButton: true
                })
            </script>
            ";
            } else {
                echo "
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'El culto al que deseas registrarte no tiene los cupos suficientes para ti',
                    text: 'Porfavor considera registrarte en otro culto, o reservar menos cupos',
                    showCloseButton: true,
                    timer:10000
                })
            </script>
            ";
            }
        } elseif (!empty($rowsResultadoVerificarCupos)) {
            echo "
            <script>
            Swal.fire({
                icon: 'error',
                title: 'Ya te has registrado previamente a este culto',
                text: 'Porfavor considera registrarte en otro culto',
                showCloseButton: true,
                timer:10000
            })
            </script>
            ";
        } else {
            $registrarAsistencia = "INSERT INTO CULTO_USUARIO(IDCULTOFK,IDUSUARIOFK,CUPOS) VALUES ($CULTO,$ID,$PERSONAS)";
            $resultadoRegistrarAsistencia = mysqli_query($conection, $registrarAsistencia);

            $restarCupos = "CALL RESTAR_CUPOS($PERSONAS,$CULTO,'$SEDE')";
            $resultadoRestarCupos = mysqli_query($conection, $restarCupos);

            if ($resultadoRegistrarAsistencia and $resultadoRestarCupos) {
                echo "
            <script>
                Swal.fire({
                    icon: 'success',
                    title: '¡Te has registrado correctamente!',
                    text: 'Gracias por tu tiempo, Dios te bendiga',
                    showCloseButton: true,
                    timer:10000
                  });
            </script>
            ";
            }
        }
    }

    if (isset($_REQUEST["submitPastor"])) {
        $ID = $_REQUEST["idPastor"];
        $CONTRASENA = $_REQUEST["passPastor"];
        $SEDE = $_SESSION["SEDE"];

        $loginPastor = "SELECT * FROM USUARIO WHERE ID = $ID AND CONTRASENA = '$CONTRASENA' AND ROL = 'PASTOR' AND SEDE ='$SEDE'";
        $resultadoLoginPastor = mysqli_query($conection, $loginPastor);
        if ($resultadoLoginPastor) {
            $rowsResultadoLoginPastor = mysqli_num_rows($resultadoLoginPastor);
            if (!empty($rowsResultadoLoginPastor)) {
                $_SESSION["ID"] = $ID;
                $_SESSION["CONTRASENA"] = $CONTRASENA;
                echo "
                <script>
                    window.location.href = './view/moduloPastor.php?alertSesion=true';
                </script>
                ";
            } else {
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
        } else {
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
    ?>

    <img src="./assets/img/logoTransparente.png" id="logo">
    <div class="container-fluid">
        <div class="row justify-content-center align-content-center text-center">
            <form action="" method="POST" height="100%" id="main-form">
                <h1 style="text-transform: uppercase;">Asistencia a cultos</h1>
                <h2 class="sede">
                    IGLESIA <?php echo $_SESSION["SEDE"] ?>
                </h2>
                <h5>Este formulario es con el fin de organizar la asistencia presencial a los cultos, la cual está
                    limitada por la reglamentación de distanciamiento social para evitar congestión y que usted no pueda
                    ingresar al culto por falta de cupo.</h5>
                <div class="form-group row">
                    <div class="col-md-6 form-space">
                        <label for="NOMBRE">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-person-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                            </svg>
                            Ingrese su nombre
                        </label>
                        <input type="text" class="form-control" name="NOMBRE" maxlength="50" required>
                    </div>
                    <div class="col-md-6 form-space">
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-person-badge-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm4.5 0a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1h-3zM8 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm5 2.755C12.146 12.825 10.623 12 8 12s-4.146.826-5 1.755V14a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-.245z" />
                        </svg>
                        <label for="ID">Ingrese su número de documento</label>
                        <input type="number" class="form-control" name="ID" maxlength="999999999999999" required>
                    </div>
                </div>
                <div class="form-group row justify-content-center align-items-center">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="CULTO">
                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-clock-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z" />
                                </svg>
                                ¿A que hora desea asistir?
                            </label>
                            <select name="CULTO" class="form-control">
                                <option value="0">-TOCA AQUI PARA SELECCIONAR UN CULTO-</option>
                                <?php
                                $SEDE = $_SESSION["SEDE"];
                                $query = "SELECT * FROM CULTOS_CUPOS WHERE SEDE = '$SEDE'";
                                $resultado = mysqli_query($conection, $query);

                                while ($arrayResultado = mysqli_fetch_array($resultado)) {
                                    if ($arrayResultado["CUPOS"] > 0) {
                                        echo "<option value='" . $arrayResultado["ID"] . "'> " . formatoDia($arrayResultado["DIA"]) . " " . formato($arrayResultado["HORA_INICIO"], $arrayResultado["MINUTO_INICIO"]) . "-" . formato($arrayResultado["HORA_FIN"], $arrayResultado["MINUTO_FINAL"]);
                                        echo " | CUPOS:" . $arrayResultado["CUPOS"] . "</option>";
                                    } else {
                                        echo "<option value='" . $arrayResultado["ID"] . "'> " . formatoDia($arrayResultado["DIA"]) . " " . formato($arrayResultado["HORA_INICIO"], $arrayResultado["MINUTO_INICIO"]) . "-" . formato($arrayResultado["HORA_FIN"], $arrayResultado["MINUTO_FINAL"]);
                                        echo " | IGLESIA LLENA</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-telephone-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M2.267.98a1.636 1.636 0 0 1 2.448.152l1.681 2.162c.309.396.418.913.296 1.4l-.513 2.053a.636.636 0 0 0 .167.604L8.65 9.654a.636.636 0 0 0 .604.167l2.052-.513a1.636 1.636 0 0 1 1.401.296l2.162 1.681c.777.604.849 1.753.153 2.448l-.97.97c-.693.693-1.73.998-2.697.658a17.47 17.47 0 0 1-6.571-4.144A17.47 17.47 0 0 1 .639 4.646c-.34-.967-.035-2.004.658-2.698l.97-.969z" />
                            </svg>
                            <label for="TELEFONO">Telefono de contacto</label>
                            <input type="number" name="TELEFONO" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group row justify-content-center align-items-center" style="margin: 0;">
                    <div class="col-md-6">
                        <div class="form-group">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-people-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z" />
                            </svg>
                            <label for="PERSONAS">¿Cuántas personas van a asistir? (Contándose a usted)</label>
                            <input type="number" name="PERSONAS" class="form-control" min="1" max="10" required>
                            <small id="peopleHelp" class="form-text text-muted">Porfavor verifique que alguien no haya
                                registrado ya su cupo</small>
                        </div>
                    </div>
                </div>
                <input type="submit" value="✔ Enviar" class="btn btn-primary" name="submit">
            </form>
            <footer>
                <div class="row justify-content-center align-content-center text-center">
                    <div class="col">
                        <a href="https://www.freepik.es/vectores/fondo" target="_BLANK">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-brush" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M15.825.12a.5.5 0 0 1 .132.584c-1.53 3.43-4.743 8.17-7.095 10.64a6.067 6.067 0 0 1-2.373 1.534c-.018.227-.06.538-.16.868-.201.659-.667 1.479-1.708 1.74a8.117 8.117 0 0 1-3.078.132 3.658 3.658 0 0 1-.563-.135 1.382 1.382 0 0 1-.465-.247.714.714 0 0 1-.204-.288.622.622 0 0 1 .004-.443c.095-.245.316-.38.461-.452.393-.197.625-.453.867-.826.094-.144.184-.297.287-.472l.117-.198c.151-.255.326-.54.546-.848.528-.739 1.2-.925 1.746-.896.126.007.243.025.348.048.062-.172.142-.38.238-.608.261-.619.658-1.419 1.187-2.069 2.175-2.67 6.18-6.206 9.117-8.104a.5.5 0 0 1 .596.04zM4.705 11.912a1.23 1.23 0 0 0-.419-.1c-.247-.013-.574.05-.88.479a11.01 11.01 0 0 0-.5.777l-.104.177c-.107.181-.213.362-.32.528-.206.317-.438.61-.76.861a7.127 7.127 0 0 0 2.657-.12c.559-.139.843-.569.993-1.06a3.121 3.121 0 0 0 .126-.75l-.793-.792zm1.44.026c.12-.04.277-.1.458-.183a5.068 5.068 0 0 0 1.535-1.1c1.9-1.996 4.412-5.57 6.052-8.631-2.591 1.927-5.566 4.66-7.302 6.792-.442.543-.796 1.243-1.042 1.826a11.507 11.507 0 0 0-.276.721l.575.575zm-4.973 3.04l.007-.005a.031.031 0 0 1-.007.004zm3.582-3.043l.002.001h-.002z" />
                            </svg>
                            Fondo creado por starline - www.freepik.es
                        </a>
                    </div>
                    <div class="col">
                        <a href="https://github.com/w1sec0d" target="_BLANK">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-code-square" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M14 1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z" />
                                <path fill-rule="evenodd" d="M6.854 4.646a.5.5 0 0 1 0 .708L4.207 8l2.647 2.646a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 0 1 .708 0zm2.292 0a.5.5 0 0 0 0 .708L11.793 8l-2.647 2.646a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708 0z" />
                            </svg>
                            Desarrollado por Carlos Ramírez |
                        </a>
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-envelope-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555zM0 4.697v7.104l5.803-3.558L0 4.697zM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757zm3.436-.586L16 11.801V4.697l-5.803 3.546z" />
                        </svg>
                        Contacto: cadavid4003@gmail.com
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <a onclick="loginPastor()">
        <div class="ingreso">
            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-box-arrow-in-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z" />
                <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z" />
            </svg>
            INGRESO PASTORES
        </div>
    </a>
    <script>
        function loginPastor() {
            Swal.fire({
                title: 'Ingreso Pastores',
                html: '<form action="" method="POST">' +
                    '<div class="form-group">' +
                    '<label for="idPastor">Ingresa tu número de documento</label>' +
                    '<input type="number" class="form-control" name="idPastor" required>' +
                    '</div>' +
                    '<label for="idPastor">Ingresa tu contraseña</label>' +
                    '<div class="form-group">' +
                    '<input type="password" class="form-control" name="passPastor" id="password" required>' +
                    '<input type="checkbox" onclick="verPassword()"> Mostrar Contraseña' +
                    '</div>' +
                    '<input type="submit" class="btn btn-primary" value="Ingresar" name="submitPastor">' +
                    '</form>',
                showConfirmButton: false,
                showCloseButton: true
            });
        }

        function verPassword() {
            const inputPassword = document.getElementById("password");
            if (inputPassword.type === "password") {
                inputPassword.type = "text";
            } else {
                inputPassword.type = "password";
            }
        }
    </script>
</body>

</html>