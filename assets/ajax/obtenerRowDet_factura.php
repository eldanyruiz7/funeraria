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
        require "../php/factura.class.php";

        require_once "../php/funcionesVarias.php";
        require "../php/responderJSON.php";
        $response = array(
            "status"        => 1
        );
        $idFactura = $_POST['idCliente'];
        $factura = new factura($idFactura,$mysqli);
        if ($factura->id == 0)
        {
            $response['mensaje'] = "Error. No se pudo consultar la información. No existe el id del factura o posiblemente ya ha sido eliminado. Inténtalo nuevamente";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $idFactura = $factura->id;

        $response['rfcEmisor']                  = $factura->rfcEmisor;
        $response['razonEmisor']                = $factura->razonEmisor;
        $response['regimenEmisor']              = $factura->regimenEmisor."-".$factura->nombreRegimenFiscal;
        $response['domicilioEmisor']            = $factura->domicilioEmisor;
        $response['nombreEstadoEmisor']         = $factura->nombreEstadoEmisor;
        $response['cpEmisor']                   = $factura->cpEmisor;
        $response['idReceptor']                 = $factura->idReceptor;
        $response['rfcReceptor']                = $factura->rfcReceptor;
        $response['razonReceptor']              = $factura->razonReceptor;
        $response['domicilioReceptor']          = $factura->domicilioReceptor;
        $response['cpReceptor']                 = $factura->cpReceptor;
        $response['emailReceptor']              = $factura->emailReceptor;
        $response['fechaCreacion']              = $factura->fechaCreacion();
        $response['fechaEmision']               = $factura->fechaEmision;
        $response['usoCFDI']                    = $factura->usoCFDI."-".$factura->nombreUsoCFDI;
        $response['tipoCFDI']                   = $factura->tipoCFDI;
        $response['moneda']                     = $factura->moneda;
        $response['formaPago']                  = $factura->formaPago."-".$factura->nombreFormaPago;
        $response['metodoPago']                 = $factura->metodoPago;
        $response['folioFiscal']                = $factura->folioFiscal;
        $response['noCertificado']              = $factura->noCertificado;
        $response['noCertificadoSAT']           = $factura->noCertificadoSAT;
        $response['selloDigitalEmisor']         = $factura->selloDigitalEmisor;
        $response['selloDigitalSAT']            = $factura->selloDigitalSAT;
        $response['cadenaOriginal']             = $factura->cadenaOriginal;
        $response['cadenaOriginalCumplimiento'] = $factura->cadenaOriginalCumplimiento;
        $response['codigoQR']                   = $factura->linkCodigoQR();
        $response['fechaCertificacion']         = $factura->fechaCertificacion;
        $response['version']                    = $factura->version;
        $response['nombresUsuario']             = $factura->nombresUsuario;
        $response['sucursal']                   = $factura->nombreSucursal.",".$factura->direccion2Sucursal;
        // $response['status']                     = 1;
        $response['html_hist']                  = "";
        $detalle                                = $factura->detalle($mysqli);
        // print_r($response);
        for ($i=0; $i < sizeof($detalle); $i++)
        {
            $response['html_hist']             .= " <tr>";
            $response['html_hist']             .= "     <td>".$detalle[$i]['claveSat']."</td>";
            $response['html_hist']             .= "     <td>".$detalle[$i]['cantidad']."</td>";
            $response['html_hist']             .= "     <td class=''>".$detalle[$i]['claveUnidad']."-".$detalle[$i]['nombreUnidad']."</td>";
            $response['html_hist']             .= "     <td class=''>".$detalle[$i]['descripcion']."</td>";
            $response['html_hist']             .= "     <td class='text-right'>$".number_format($detalle[$i]['precioU'],2,".",",")."</td>";
            $response['html_hist']             .= "     <td class='text-right'>$".number_format($detalle[$i]['iva'],2,".",",")."</td>";
            $response['html_hist']             .= "     <td class='text-right'>$".number_format($detalle[$i]['ieps'],2,".",",")."</td>";
            $response['html_hist']             .= "     <td class='text-right'>$".number_format($detalle[$i]['importe'],2,".",",")."</td>";
            $response['html_hist']             .= " </tr>";
        }
        responder($response, $mysqli);
    }
?>
