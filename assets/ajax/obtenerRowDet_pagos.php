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
        // Obtener el último recibo de cobranza ingresado //
        $sql                = "SELECT idFolio_cobranza FROM detalle_pagos_contratos WHERE activo = 1 ORDER BY id DESC LIMIT 1";
        $res_ultimo         = $mysqli->query($sql);
        $row_ultimo         = $res_ultimo->fetch_assoc();
        $ultimoId           = $row_ultimo['idFolio_cobranza'];
        $sql 				= "SELECT id, folio FROM folios_cobranza_asignados WHERE activo = 1 AND asignado = 0";
        $res_prov 			= $mysqli->query($sql);
        while ($row_prov 	= $res_prov->fetch_assoc())
        {
            $idProv 		= $row_prov['id'];
            $nombreProv 	= $row_prov['folio'];
            if ($idProv == $ultimoId + 1)
            {
                echo "<option selected value='$idProv'>$nombreProv</option>";
            }
            else
            {
                echo "<option value='$idProv'>$nombreProv</option>";
            }
        }
    }
?>
