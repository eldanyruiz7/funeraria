<?php
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
    require_once ('../connect/bd.php');
    require_once ("../connect/sesion.class.php");
    $sesion = new sesion();
    require_once ("../connect/cerrarOtrasSesiones.php");
    require_once ("../connect/usuarioLogeado.php");
    if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
    {
		header("Location: ".dirname(__FILE__)."../../salir.php");
    }
    else
    {
        require "../php/responderJSON.php";
        $response = array(
            "status"        => 1
        );
        $array_lista = array();
        $consultarDecesos = FALSE;
        if (isset($_POST['idDifunto']) && is_numeric($_POST['idDifunto']))
        {
            $idDifunto = $_POST['idDifunto'];
            $sql = "SELECT idCausa FROM detalle_causasdecesos WHERE idDifunto = ? AND activo = 1";
            $prepare_lista = $mysqli->prepare($sql);
            $prepare_lista->bind_param('i',$idDifunto);
            $prepare_lista->execute();
            $res_lista = $prepare_lista->get_result();
            if ($res_lista->num_rows > 0)
            {
                $consultarDecesos = TRUE;
                while($row_lista = $res_lista->fetch_assoc())
                {
                    $array_lista[] = $row_lista['idCausa'];
                }
            }
            // var_dump($array_lista);
            // die;

        }
        $sql = "SELECT id, nombre FROM cat_causasdecesos WHERE activo = ? ORDER BY nombre ASC";
        if ($prepare = $mysqli->prepare($sql))
        {
            $activo = 1;
            if ($prepare->bind_param('i',$activo))
            {
                if ($prepare->execute())
                {
                    $res = $prepare->get_result();
                    while ($row = $res->fetch_assoc())
                    {
                        if ($consultarDecesos)
                        {
                            for ($i=0; $i < sizeof($array_lista); $i++)
                            {
                                if ($array_lista[$i] == $row['id'])
                                {
                                    echo "<option selected value='".$row['id']."'>".$row['nombre']."</option>";
                                    continue(2);
                                }
                            }
                        }
                        echo "<option value='".$row['id']."'>".$row['nombre']."</option>";
                    }
                }
            }
        }
    }
?>
