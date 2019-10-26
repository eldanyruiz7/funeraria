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
        require_once "../php/funcionesVarias.php";
        $xml = $_GET['xml'];
        $xml = validarFormulario("i",$xml);
        $xml = str_pad($xml, 10, "0", STR_PAD_LEFT);
        header('Content-disposition: attachment; filename="'.$xml.'.xml"');
        header('Content-type: "text/xml"; charset="utf8"');
        readfile('XML/'.$xml.'.xml');
http://localhost/funeraria/ace-master/assets/ws/descargarXML.php?xml=0000000032
    }
?>
