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
        header("Location: salir.php");
    }
    else
    {
        require "../php/responderJSON.php";
        require "../php/contrato.class.php";
        require_once "../php/funcionesVarias.php";
        $response = array(
            "status"        => 1
        );

        $sql = "SELECT
                    contratos.id                AS idContrato
                FROM contratos
                WHERE contratos.enCurso = 1 AND contratos.activo = 1";
        $res_ = $mysqli->query($sql);
        $num = $res_->num_rows;
        if ($num == 0)
        {
			$json_data["Records"] = 0;

        }
        else
        {
            while ($row = $res_->fetch_assoc())
            {
                $idContrato = $row['idContrato'];
                //$fechaCreacion = date_format(date_create($row['fechaCreacion']), 'd-m-Y');
                $contrato = new contrato($idContrato,$mysqli);
                $nombreDifunto = $contrato->nombreDifunto($mysqli);
                $InfoData[] = array(
                    'id'                =>$contrato->id,
                    'folio'             => $contrato->folio,
                    'fechaCreacion'     => $contrato->fechaCreacion(),
                    'precio'            => "$".number_format($contrato->costoTotal,2,".",","));
            }
        //$data[] = $InfoData;
			$json_data["Result"] = "OK";
            $json_data["Records"] = $InfoData;
        }
        echo json_encode($json_data);
    }
?>
