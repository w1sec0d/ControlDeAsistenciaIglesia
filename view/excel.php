<?php
require_once("../model/database.php");
header('Content-type:application/xls');
header('Content-Disposition: attachment; filename=nombre_archivo.xls');
$SEDE = $_REQUEST["SEDE"];
$ID_CULTO = $_REQUEST["ID_CULTO"];
$cultos = "SELECT * FROM CULTOS_CUPOS WHERE SEDE = '$SEDE'";
$resultadoCultos = mysqli_query($conection, $cultos);
?>
<table class="table" id="tableUser<?php echo $ID_CULTO ?>"">
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