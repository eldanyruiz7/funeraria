<?php
	// error_reporting(E_ALL);
	ini_set('display_errors', '0');
    require_once ('../connect/bd.php');
    require_once ("../connect/sesion.class.php");
    $sesion = new sesion();
    require_once ("../connect/cerrarOtrasSesiones.php");
    require_once ("../connect/usuarioLogeado.php");
    require_once ("../php/funcionesVarias.php");
    require_once ("../php/factura.class.php");
    require_once ("../ws/genFacturaPDF.php");
    require_once ("../fpdf/qrcode/qrcode.class.php");
    require_once ('../fpdf/code128.php');
    if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
    {
        header("Location: salir.php");
    }
    else
    {
        if (isset($_GET['idFactura']))
        {
            $idFactura = $_GET['idFactura'];
        }
        else
        {
            die;
        }
        if (!$idFactura = validarFormulario('i',$idFactura,0))
        {
            echo "El formato del Id de la factura no es el esperado.";
            die;
        }
        else
        {
            $factura = new factura($idFactura,$mysqli);
            genFactura($factura,$mysqli,1);
        }
    }

?>
