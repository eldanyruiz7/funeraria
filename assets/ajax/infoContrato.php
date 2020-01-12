<?php
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
        require "../php/contrato.class.php";
        require_once "../php/funcionesVarias.php";
        require "../php/responderJSON.php";
		require_once ("../php/query.class.php");
		$query = new Query();

        $idContrato = $_POST['idContrato'];
        $response = array(
            "status"        => 1
        );
        if(is_numeric($idContrato) == FALSE)
        {
            $response['mensaje'] = "El formato del id del contrato no es el correcto";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $contrato = new Contrato($idContrato,$query);
        if ($contrato->id == 0)
        {
            $response['mensaje'] = "No existe informaci&oacute;n para este contrato. Posiblemente ya ha sido eliminado.";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $response['aportacion']     = $contrato->aportacion;
        $response['titular']        = $contrato->nombreCliente;
        $response['folio']          = $contrato->folio;
        $response['observaciones']  = $contrato->observaciones;
        $response['id']             = str_pad($contrato->id, 10, "0", STR_PAD_LEFT);
        $response['saldo']          = number_format($contrato->saldo($mysqli),2,".",",");
        $response['status']         = 1;
        responder($response, $mysqli);
    }
?>
