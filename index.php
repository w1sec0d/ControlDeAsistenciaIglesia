<body>
    <?php
    require_once "./view/header.php";
    require_once "./model/database.php";
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
    ";
    session_start();
    function formato($hora)
    {
        if ($hora < 12) {
            return "a.m";
        } else if ($hora > 12) {
            return "p.m";
        } else if ($hora == 12) {
            return "m";
        }
    }

    if (isset($_REQUEST["x"])) {
        switch ($_REQUEST["x"]) {
            case '9xY0ltbVuGbG':
                $_SESSION["SEDE"] = "BRITALIA";
                break;

            case 'KsjJD4DJAv6q':
                $_SESSION["SEDE"] = "TINTAL";
                break;

            default:
                header("Location: ./view/error404.php");
                break;
        }
    } else {
        header("Location: ./view/error404.php");
    }
    if (isset($_REQUEST["submit"])) {
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
            $insertUsuario = "INSERT INTO USUARIO(ID,SEDE,NOMBRE,TELEFONO) VALUES ($ID,'$SEDE','$NOMBRE',$TELEFONO)";
            $resultadoInsertUsuario = mysqli_query($conection, $insertUsuario);
        }

        //VERIFICO QUE EL CULTO TENGA CUPOS
        $verificarCupos = "SELECT CUPOS FROM CULTOS_CUPOS WHERE ID = $CULTO";
        $resultadoVerificarCupos = mysqli_query($conection, $verificarCupos);
        $arrayResultadoVerificarCupos = $resultadoVerificarCupos->fetch_assoc();
        if ($arrayResultadoVerificarCupos["CUPOS"] - $PERSONAS < 0) {
            if ($arrayResultadoVerificarCupos["CUPOS"] == 0) {
                echo "
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'El culto al que deseas registrarte está lleno',
                    text: 'Porfavor considera registrarte en otro culto'
                })
            </script>
            ";
            } else {
                echo "
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'El culto al que deseas registrarte no tiene los cupos suficientes para ti',
                    text: 'Porfavor considera registrarte en otro culto, o reservar menos cupos'
                })
            </script>
        ";
            }
        } else {
            $registrarAsistencia = "INSERT INTO CULTO_USUARIO(IDCULTOFK,IDUSUARIOFK) VALUES ($CULTO,$ID)";
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
                  });
            </script>
            ";
            }
        }
    }
    ?>

    <img src="./assets/img/logoTransparente.png" id="logo">
    <div class="row container-fluid justify-content-center align-content-center text-center" id="main-container">
        <form action="" method="POST" height="100%">
            <h1 style="text-transform: uppercase;">Asistencia a cultos</h1>
            <h2 class="sede">
                SEDE <?php echo $_SESSION["SEDE"] ?>
            </h2>
            <h5>Este formulario es con el fin de organizar la asistencia presencial a los cultos, la cual está limitada por la reglamentación de distanciamiento social para evitar congestión y que usted no pueda ingresar al culto por falta de cupo.</h5>
            <div class="form-group row">
                <div class="col-md-6 form-space">
                    <label for="NOMBRE">
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-person-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                        </svg>
                        Ingrese su nombre
                    </label>
                    <input type="text" class="form-control" name="NOMBRE" required>
                </div>
                <div class="col-md-6 form-space">
                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-person-badge-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm4.5 0a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1h-3zM8 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm5 2.755C12.146 12.825 10.623 12 8 12s-4.146.826-5 1.755V14a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-.245z" />
                    </svg>
                    <label for="ID">Ingrese su número de documento</label>
                    <input type="number" class="form-control" name="ID" required>
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
                                echo "<option value='" . $arrayResultado["ID"] . "'> " . $arrayResultado["HORA_INICIO"] . ":00" . formato($arrayResultado["HORA_INICIO"]) . "-" . $arrayResultado["HORA_FIN"] . ":00" . formato($arrayResultado["HORA_FIN"]);
                                if ($arrayResultado["CUPOS"] > 0) {
                                    echo " | CUPOS:" . $arrayResultado["CUPOS"] . "</option>";
                                } else {
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
            <div class="form-group row justify-content-center align-items-center">
                <div class="col-md-6">
                    <div class="form-group">
                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-people-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z" />
                        </svg>
                        <label for="PERSONAS">¿Cuántas personas van a asistir? (Contándose a usted)</label>
                        <input type="number" name="PERSONAS" class="form-control" min="1" max="10" required>
                        <small id="peopleHelp" class="form-text text-muted">Porfavor verifique que alguien no haya registrado ya su cupo</small>
                    </div>
                </div>
            </div>
            <input type="submit" value="✔ Enviar" class="btn btn-primary" name="submit">
        </form>
    </div>
    <div class="ingreso">
        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-box-arrow-in-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z" />
            <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z" />
        </svg>
        INGRESO PASTORES
    </div>
</body>

</html>