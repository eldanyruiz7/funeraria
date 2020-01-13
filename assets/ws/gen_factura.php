<?php
require_once ('../connect/bd.php');
require_once ("../connect/sesion.class.php");
$sesion = new sesion();
require_once ("../connect/cerrarOtrasSesiones.php");
require_once ("../connect/usuarioLogeado.php");
require_once ("../php/funcionesVarias.php");
require_once ("../php/venta.class.php");
require_once ("../php/contrato.class.php");
require_once '../php/factura.class.php';
require_once '../fpdf/code128.php';
require_once ("../fpdf/qrcode/qrcode.class.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../php/PHPMailer-master/src/Exception.php';
require '../php/PHPMailer-master/src/PHPMailer.php';
require '../php/PHPMailer-master/src/SMTP.php';
require_once 'genFacturaPDF.php';
if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
{
    header("Location: salir.php");
}
else
{
    // require '../startbootstrap/vendor/fpdf/qrcode/qrcode.class.php';
	require_once ("../php/query.class.php");
	$query = new Query();
    $pp = 0;
    function progreso($x, $status, $error)
    {
        global $pp;
        $pp += $x;
        $pp = ($pp > 100) ? 100 : $pp;
        if($error       == 0)
        {
            $script     =  "<script>";
            $script     .= "$('#progress-p').css('width','$pp%');";
            $script     .= "$('#span-completo').text('$pp% completado');";
            $script     .= "$('#strong-tarea').text('$status');";
            if($pp >= 100)
                $script .= "$('#progress-div').removeClass('active');";
            $script     .= '</script>';
            echo $script;
            flush();
            ob_flush();
        }
        else
        {
            $script     =  "<script>";
            $script     .= "$('#progress-p').removeClass('progress-bar-info');";
            $script     .= "$('#progress-p').addClass('progress-bar-danger');";
            $script     .= "$('#progress-div').removeClass('active');";
            $script     .= "$('#span-completo').text('$pp% completado');";
            $script     .= '$("#strong-tarea").text("No se pudo timbrar. Inténtalo nuevamente");';
            $script     .= '</script>';
            $respHTML   = str_replace("\"", "", $status);
            $script     .=  "<script>";
            $script     .= 'window.parent.mensaje("error","'.$respHTML.'");';
            $script     .= 'window.parent.deshabilitar(false);';
            $script     .= '</script>';
            echo $script;
            flush();
            ob_flush();
            exit(0);
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    	<head>
    		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    		<meta charset="utf-8" />
    		<title></title>

    		<meta name="description" content="Static &amp; Dynamic Tables" />
    		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    		<link rel="stylesheet" href="../css/bootstrap.min.css" />
    		<link rel="stylesheet" href="../font-awesome/4.5.0/css/font-awesome.min.css" />
    		<link rel="stylesheet" href="../css/fonts.googleapis.com.css" />
    		<link rel="stylesheet" href="../css/jquery.gritter.min.css" />
    		<link rel="stylesheet" href="../css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
    		<link rel="stylesheet" href="../css/jquery-ui.custom.min.css" />
    		<script src="../js/ace-extra.min.js"></script>
            <script src="../js/jquery-2.1.4.min.js"></script>

            <script src="../js/bootstrap.min.js"></script>
            <!-- page specific plugin scripts -->
            <script src="../js/jquery.gritter.min.js"></script>
            <!-- ace scripts -->
            <script src="../js/ace-elements.min.js"></script>
            <script src="../js/ace.min.js"></script>
            <script src="../js/custom/primario.js"></script>
    	</head>
        <body style="background-color:snow">
    <?php
    $html               = "<div>";
    $html               .="    <p>";
    $html               .="        <strong id='strong-tarea'>Inicializando...</strong>";
    $html               .="        <span class='pull-right text-muted' id='span-completo'>0% Completo</span>";
    $html               .="    </p>";
    $html               .='    <div id="progress-div" class="progress progress-striped active no-margin">';
    $html               .='        <div id="progress-p" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%">';
    $html               .='            <span class="sr-only">40% Complete (success)</span>';
    $html               .='        </div>';
    $html               .='    </div>';
    $html               .='</div>';

    echo $html;
    flush();
    ob_flush();
    progreso(10, "Recolectando datos", 0);
    usleep(500000);
    ////// Datos del emisor ///////////
    $coleccionFactura   = json_decode($_GET['objetoItemsJSON']);
    // var_dump($_GET);
    // die;
    if (! isset($_GET['idVenta']) && ! isset($_GET['idContrato']))
    {
        progreso(0, "Error. No se recibió ningún parámetro. Por favor vuelve a intentarlo", 1);
    }
    $idVenta = $_GET['idVenta'];
    $idContrato = $_GET['idContrato'];
    // if(!$idVenta        = validarFormulario("i",$_GET['idVenta'],0) && !$idContrato        = validarFormulario("i",$_GET['idContrato'],0))
    // {
    // }
    if ($idVenta != 0)
    {
        $tipoFactura = 'venta';
        $idContrato = 0;
    }
    elseif ($idContrato != 0)
    {
        $tipoFactura = 'contrato';
        $idVeta = 0;
    }
    // echo 'idVenta: '.$idVenta;
    // echo 'idContrato '.$idContrato;
    if(!$formaPago          = validarFormulario("i",$_GET['formaPago'],0))
    {
        progreso(0, "Error. El formato del id de la forma de pago no es el correcto", 1);
    }
    $sql = "SELECT c_FormaPago FROM cat_formas_pago WHERE id = ? AND activo = 1 LIMIT 1";
    $prepare = $mysqli->prepare($sql);
    if (!$prepare ||
        !$prepare->bind_param("i", $formaPago) ||
        !$prepare->execute() ||
        !$prepare->store_result() ||
        !$prepare->bind_result($formaPago) ||
        !$prepare->fetch() ||
        !$prepare->num_rows > 0)
        {
            progreso(0, "Error. No existe la forma de pago seleccionada. Elige otra distinta", 1);
        }
    if(!$metodoPago         = validarFormulario("s",$_GET['metodoPago'],0))
    {
        progreso(0, "Error. El formato del id del método de pago no es el correcto", 1);
    }
    $sql = "SELECT c_MetodoPago FROM cat_metodos_pago WHERE id = ? AND activo = 1 LIMIT 1";
    $prepare = $mysqli->prepare($sql);
    if (!$prepare ||
        !$prepare->bind_param("i", $metodoPago) ||
        !$prepare->execute() ||
        !$prepare->store_result() ||
        !$prepare->bind_result($metodoPago) ||
        !$prepare->fetch() ||
        !$prepare->num_rows > 0)
        {
            progreso(0, "Error. No existe el método de pago seleccionado. Elige otro distinto", 1);
        }
    if(!$usoCfdi            = validarFormulario("i",$_GET['usoCfdi'],0))
    {
        progreso(0, "Error. El formato del id del uso CFDI no es el correcto", 1);
    }
    $sql = "SELECT c_UsoCFDI FROM cat_usos_cfdi WHERE id = ? AND activo = 1 LIMIT 1";
    $prepare = $mysqli->prepare($sql);
    if (!$prepare ||
        !$prepare->bind_param("i", $usoCfdi) ||
        !$prepare->execute() ||
        !$prepare->store_result() ||
        !$prepare->bind_result($usoCfdi) ||
        !$prepare->fetch() ||
        !$prepare->num_rows > 0)
        {
            progreso(0, "Error. No existe el uso del CFDI seleccionado. Elige otro distinto", 1);
        }
    $emailReceptor      = validarFormulario("s",$_GET['emailReceptor']);
    $enviarCfdi         = $_GET['enviarCfdi'] == 1 ? 1 : 0;
    if ($tipoFactura == 'venta')
    {
        $venta              = new venta($idVenta,$mysqli);
    }
    else
    {
        $contrato           = new Contrato($idContrato,$query);
    }
    if ($tipoFactura == 'venta')
    {
        if (!$venta->activo)
        {
            progreso(0, "Error. No se puede facturar esta venta. Posiblemente haya sido borrada o cancelada", 1);
        }
        if ($venta->idFactura)
        {
            progreso(0, "No se puede volver a facturar. Esta venta ya ha sido facturada con anterioridad.<br>Id de la factura asociado: <b>$venta->idFactura</b><br><h5><a href=\"listarFacturas.php\" class=\"orange\">Ir a lista de facturas</a></h5>", 1);
        }
    }
    else
    {
        if (!$contrato->activo)
        {
            progreso(0, "Error. No se puede facturar este contrato. Posiblemente haya sido borrado o cancelado", 1);
        }
        if ($contrato->idFactura)
        {
            progreso(0, "No se puede volver a facturar. Este contrato ya ha sido facturado con anterioridad.<br>Id de la factura asociado: <b>$contrato->idFactura</b><br><h5><a href=\"listarFacturas.php\" class=\"orange\">Ir a lista de facturas</a></h5>", 1);
        }
    }
    $rfcEmisor          = "AAA010101AAA";
    // $rfcEmisor          = $venta->rfcSucursal;
    $razonEmisor        = "===FACTURA DE EJEMPLO===";

    // $razonEmisor        = "JORGE IVAN SALAZAR CHAVEZ";
    //$razonEmisor        = "YADIRA JANNETTE ARIAS ROJAS";
    $regimenEmisor      = $tipoFactura == 'venta' ? $venta->c_RegimenFiscal : $contrato->c_RegimenFiscal;
    // $regimenEmisor      = "612";
    $domicilioEmisor    = $tipoFactura == 'venta' ? $venta->direccionSucursal : $contrato->domicilioSucursal;
    $cpEmisor           = $tipoFactura == 'venta' ? $venta->cpSucursal : $contrato->cpSucursal;
    $idEntidadEmisor    = $tipoFactura == 'venta' ? $venta->idEstadoSucursal : $contrato->idEstadoSucursal;
    $idReceptor         = $tipoFactura == 'venta' ? $venta->idCliente : $contrato->idCliente;
    // $rfcReceptor        = $tipoFactura == 'venta' ? $venta->rfcCliente : $contrato->rfcCliente;
    // $razonReceptor      = $tipoFactura == 'venta' ? $venta->nombresCliente : $contrato->nombreCliente;
    $rfcReceptor        = validarFormulario("s",$_GET['rfcReceptor'],FALSE);
    $razonReceptor        = validarFormulario("s",$_GET['razonReceptor'],FALSE);
    $cpReceptor         = $tipoFactura == 'venta' ? $venta->cpCliente : $contrato->cpCliente;
    $regimenReceptor    = "";
    $domicilioReceptor  = $tipoFactura == 'venta' ? $venta->domicilioCliente : $contrato->domicilioCliente;
    $entidadReceptor    = $tipoFactura == 'venta' ? $venta->idEstadoCliente : $contrato->idEstadoCliente;
    $moneda             = "MXN";
    $fecha              = date('Y-m-d');
    $hora               = date('H:i:s');
    $fechaHora          = $fecha."T".$hora;
    $vigencia           = 'NULL';
    $version            = "3.3";
    $tipoComprobante    = "I";
    $porCientoIVA       = 16;
    //$noCertificado    = '30001000000300023707';
    // $noCertificado   = '0000100000040930875';

    progreso(10,"Creando XML", 0);
    usleep(500000);
    $impuestoIEPS       = "003";
    $tipoFactorIEPS     = 'Tasa';
    $tasaOCuotaIVA      = $porCientoIVA / 100;
    // $tasaOCuotaIEPS     = $porCientoIEPS / 100;
    $sumatoriaIVA           = 0;
    $sumatoriaSinIva        = 0;
    $x                      = 0;
    $xmlCFDI_c              = '<cfdi:Conceptos>';
    // echo $xmlCFDI_c;
    $itemEncontrado = FALSE;
    foreach ($coleccionFactura as $rowFactura)
    {

        $cantidad           = $rowFactura->cantidad;
        $descripcion        = $rowFactura->concepto;
        $valorUnitario      = round($rowFactura->precio - $rowFactura->precio * $tasaOCuotaIVA, 2);
        $esteSubTotal       = $cantidad * $valorUnitario;
        $tipoFactorIVA      = "Tasa";
        $impuestoIVA        = "002";
        $importe            = round($rowFactura->precio * $cantidad, 2);
        $importeImpuestoIVA = round($importe - $esteSubTotal, 2);
        $sumatoriaIVA       += number_format($importeImpuestoIVA,2,".","");
        $idUnidadMedida     = $rowFactura->medida;
        $claveProdServ      = $rowFactura->claveSat;
        $sumatoriaSinIva    += $esteSubTotal;
        $sql                    = "SELECT c_ClaveUnidad, nombre
                                    FROM cat_unidades_venta
                                    WHERE id = $idUnidadMedida AND activo = 1
                                    LIMIT 1";
        $res_med            = $mysqli->query($sql);
        $row_med            = $res_med->fetch_assoc();
        $claveUnidad        = $row_med['c_ClaveUnidad'];
        $unidad             = $row_med['nombre'];

        $xmlCFDI_c            .= '<cfdi:Concepto ClaveProdServ="'.$claveProdServ.'" ';
        $xmlCFDI_c            .= 'ClaveUnidad="'.$claveUnidad.'" Cantidad="'.number_format($cantidad,3,".","").'" Unidad="'.$unidad.'" Descripcion="'.$descripcion.'" ValorUnitario="'.number_format($valorUnitario,2,".","").'" Importe="'.number_format($esteSubTotal,2,".","").'" Descuento="0.00">';
        $xmlCFDI_c            .= '   <cfdi:Impuestos>';
        $xmlCFDI_c            .= '       <cfdi:Traslados>';
        $xmlCFDI_c            .= '           <cfdi:Traslado Base="'.number_format($importe * $cantidad,2,".","").'" Impuesto="'.$impuestoIVA.'" TipoFactor="'.$tipoFactorIVA.'" TasaOCuota="'.number_format($tasaOCuotaIVA,6,".","").'" Importe="'.number_format($importeImpuestoIVA,2,".","").'"/>';
        // $xmlCFDI_c            .= '           <cfdi:Traslado Base="'.number_format($importe,2,".","").'" Impuesto="'.$impuestoIEPS.'" TipoFactor="'.$tipoFactorIEPS.'" TasaOCuota="'.number_format($tasaOCuotaIEPS,2,".","").'" Importe="'.number_format($importeImpuestoIEPS,2,".","").'"/>';
        $xmlCFDI_c            .= '       </cfdi:Traslados>';
        $xmlCFDI_c            .= '   </cfdi:Impuestos>';
        $xmlCFDI_c            .= '</cfdi:Concepto>';

        $detalle_factura[$x]['claveSat']    = $claveProdServ;
        $detalle_factura[$x]['cantidad']    = $cantidad;
        $detalle_factura[$x]['claveUnidad'] = $claveUnidad;
        $detalle_factura[$x]['nombreUnidad']= $unidad;
        $detalle_factura[$x]['descripcion'] = $descripcion;
        $detalle_factura[$x]['precioU']     = $valorUnitario;
        $detalle_factura[$x]['iva']         = $porCientoIVA;
        // $detalle_factura[$x]['ieps']        = $porCientoIEPS;
        $detalle_factura[$x]['importe']     = $importe;
        $detalle_factura[$x]['descuento']   = 0;
        $x++;
    }

    $subTotal                   = $sumatoriaSinIva;
    $totalImpuestosTrasladados  = $sumatoriaIVA;
    $TOTAL                      = number_format($subTotal + $totalImpuestosTrasladados,2,".","");
    $xmlCFDI_c            .= '</cfdi:Conceptos>';
    $xmlCFDI_c            .= '<cfdi:Impuestos TotalImpuestosTrasladados="'.number_format($totalImpuestosTrasladados,2,".","").'">';
    $xmlCFDI_c            .= '   <cfdi:Traslados>';
    $xmlCFDI_c            .= '       <cfdi:Traslado Impuesto="'.$impuestoIVA.'" TipoFactor="'.$tipoFactorIVA.'" TasaOCuota="'.number_format($tasaOCuotaIVA,6,".","").'" Importe="'.number_format($sumatoriaIVA,2,".","").'"/>';
    // $xmlCFDI_c            .= '       <cfdi:Traslado Impuesto="'.$impuestoIEPS.'" TipoFactor="'.$tipoFactorIEPS.'" TasaOCuota="'.number_format($tasaOCuotaIEPSTotal,2,".","").'" Importe="'.number_format($sumatoriaIEPS,2,".","").'"/>';
    $xmlCFDI_c            .= '   </cfdi:Traslados>';
    $xmlCFDI_c            .= '</cfdi:Impuestos>';
    $xmlCFDI_c            .= '</cfdi:Comprobante>';
    //echo utf8_decode($xmlCFDI);
    $xmlCFDI            = '<?xml version="1.0" encoding="utf-8"?><cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd" ';
    $xmlCFDI            .= 'LugarExpedicion="'.$cpEmisor.'" MetodoPago="'.$metodoPago.'" TipoDeComprobante="'.$tipoComprobante.'" Total="'.$TOTAL.'" Descuento="0.00" Moneda="'.$moneda.'" Certificado="" SubTotal="'.number_format($subTotal,2,".","").'" NoCertificado="00000000000000000000" FormaPago="'.$formaPago.'" Sello="" Fecha="'.$fechaHora.'" Version="'.$version.'">';
    $xmlCFDI            .= '<cfdi:Emisor Rfc="'.$rfcEmisor.'" Nombre="'.$razonEmisor.'" RegimenFiscal="'.$regimenEmisor.'"></cfdi:Emisor>';
    $xmlCFDI            .= '<cfdi:Receptor Rfc="'.$rfcReceptor.'" Nombre="'.$razonReceptor.'" UsoCFDI="'.$usoCfdi.'"></cfdi:Receptor>';
    $xmlCFDI            .= $xmlCFDI_c;
    $archivoXML_     = fopen("error.xml", "w");
    if($archivoXML_ == false)
    {
        progreso(0, "Error al crear archivo XML. Error de escritura", 1);
    }
    fwrite($archivoXML_, $xmlCFDI);
    fclose($archivoXML_);
    // echo $xmlCFDI;
    progreso(10, "Conectando al SAT", 0);
    usleep(500000);
    $response           = '';
    // $user               = "AIRY820226UXA_10";
    // $userPassword       = "58996256596700145927542";
    // $user               = "AIRY820226UXA_10";
    // $userPassword       = "790306585201182366653207";
    // $user               = "AIRY820226UXA_3";
    // $userPassword       = "184829659849329157062420";
    $user               = "AIRY820226UXA_36";
    $userPassword       = "247727062153132477780930";
    // $llavePrivadaEmisorPassword = "supersalazar123";
    $llavePrivadaEmisorPassword = "12345678a";

    // $llavePrivadaEmisorPassword = "sole1982";
    if (!$certificadoEmisor  = file_get_contents("certEmisorPrueba/Certificados de pruebas/CSD09_AAA010101AAA.cer"))
    {
        progreso(0, "Error al acceder al certificado del emisor", 1);
    };
    if (!$llavePrivadaEmisor = file_get_contents("certEmisorPrueba/Certificados de pruebas/CSD09_AAA010101AAA.key"))
    {
        progreso(0, "Error al acceder a la llave privada del emisor", 1);
    }
    // $certificadoEmisor  = file_get_contents("sellos/nuevo/SACJ830819U62.cer");
    // $llavePrivadaEmisor = file_get_contents("sellos/nuevo/SACJ830819U62.key");
    //$certificadoEmisor  = file_get_contents("sellos/nuevo/SACJ830819U62.cer");
    //$llavePrivadaEmisor = file_get_contents("sellos/nuevo/SACJ830819U62.key");
    //$certificadoEmisor  = file_get_contents("sellos/airy/csd/AIRY820226UXA.cer");
    //$llavePrivadaEmisor = file_get_contents("sellos/airy/csd/AIRY820226UXA.key");
    //$certificadoEmisor  = file_get_contents("certEmisorPrueba/airy820226uxa.cer");
    //$llavePrivadaEmisor = file_get_contents("certEmisorPrueba/airy820226uxa.key");
    //$user = "AIRY820226UXA_10";
    //$userPassword = "58996256596700145927542";
    progreso(20, "Descargando información",0);
    usleep(500000);
    try {
            // $client     = new SoapClient("http://cmmlinux.from-la.net:8083/CMM_Pruebas/InterconectaWs?wsdl"); /////////////PRUEBAS
            $client     = new SoapClient("http://mieses.from-la.net:8083/CMM_Pruebas/InterconectaWs?wsdl"); /////////////PRUEBAS
            // $client     = new SoapClient("http://pacfdisat.com:8080/CMM/InterconectaWs?wsdl"); //////////////////PRODUCCION
    		// var_dump($client->__getFunctions());
            $params     = array(
        			'user'                       => $user,
        			'userPassword'               => $userPassword,
        			'certificadoEmisor'          => $certificadoEmisor,
        			'llavePrivadaEmisor'         => $llavePrivadaEmisor,
        			'llavePrivadaEmisorPassword' => $llavePrivadaEmisorPassword,
        			'xmlCFDI'	                 => $xmlCFDI,
        			'versionCFDI'                => '3.2'
    		);
            $response   = $client->__soapCall('sellaTimbraCFDI', array('parameters' => $params));
    } catch (SoapFault $fault) {
            echo "SOAPFault: ".$fault->faultcode."-".$fault->faultstring."\n";
            var_dump($fault);
            progreso(0, "Revisa tu conexión de internet y vuelve a intentarlo. ", 1);
//var_dump($ret);
            /*echo $script;
            exit(0);*/
    }
    progreso(15, "Generando factura", 0);
    usleep(500000);
    $ret                = $response->return;
    // var_dump($ret);
    if ($ret->isError)
    {
        $respuesta      = $ret->errorMessage;
        ///////////////////////////////////////////
        progreso(0, $respuesta, 1);
    }
    // var_dump($ret);
    // die;
    $folioFiscal        = $ret->folioUUID;
    $selloDigitalEmisor = $ret->selloDigitalEmisor;
    $selloDigitalSAT    = $ret->selloDigitalTimbreSAT;
    $cadenaOriginal     = $ret->cadenaOriginal;
    $fechaCertificacion = $ret->fechaHoraTimbrado;
    $xmlTIMBRE          = $ret->XML;
    /////////////////////////////////// Obtener NoCertificadoSAT //////////////////////////////////////////
    $delimiter_Cert     = "NoCertificadoSAT=";
    $divideXML_Cert     = explode($delimiter_Cert, $xmlTIMBRE);
    $delimiter_Dash     = "\"";
    $divideXML_Dash     = explode($delimiter_Dash, $divideXML_Cert[1]);
    $noCertificadoSAT   = $divideXML_Dash[1];
    /////////////////////////////////// Obtener NoCertificadoEmisor ////////////////////////////////////////
    $delimiter_Cert     = "NoCertificado=";
    $divideXML_Cert     = explode($delimiter_Cert, $xmlTIMBRE);
    $delimiter_Dash     = "\"";
    $divideXML_Dash     = explode($delimiter_Dash, $divideXML_Cert[1]);
    $noCertificado      = $divideXML_Dash[1];
    ///////////////// Obtener RfcProvCertif y generar cadenaOriginalCumplimiento V1.1 //////////////////////
    $delimiter_Cert     = "RfcProvCertif=";
    $divideXML_Cert     = explode($delimiter_Cert, $xmlTIMBRE);
    $delimiter_Dash     = "\"";
    $divideXML_Dash     = explode($delimiter_Dash, $divideXML_Cert[1]);
    $RfcProvCertif      = $divideXML_Dash[1];
    $cadOrig_cump_v11   = "||1.1|";
    $cadOrig_cump_v11   .= $folioFiscal."|";
    $cadOrig_cump_v11   .= $fechaCertificacion."|";
    $cadOrig_cump_v11   .= $RfcProvCertif."|";
    $cadOrig_cump_v11   .= $selloDigitalSAT."|";
    $cadOrig_cump_v11   .= $noCertificadoSAT."||";
    /////////////////////////////////////// Generar codigoQR ///////////////////////////////////////////////
    $url_QR =  "https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx";
    $url_QR .= "?id=".$folioFiscal;
    $url_QR .= "&re=".$rfcEmisor;
    $url_QR .= "&rr=".$rfcReceptor;
    $url_QR .= "&tt=".$TOTAL;
    $subSDig = substr($selloDigitalEmisor, -8);
    $url_QR .= "&fe=".$subSDig;
    ///////////////////////////////////////SUCURSAL////////////////////////////////////////////////////////////
    $idUsuario      = $sesion->get('id');
    $sql            = "SELECT idSucursal FROM cat_usuarios WHERE id = $idUsuario LIMIT 1";
    $res_noSucursal = $mysqli->query($sql);
    $row_noSucursal = $res_noSucursal->fetch_assoc();
    $idSucursal     = $row_noSucursal['idSucursal'];
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    $mysqli->autocommit(FALSE);
    $sql = "INSERT INTO facturas (
                rfcEmisor, razonEmisor, regimenEmisor, domicilioEmisor, idEstadoEmisor, cpEmisor, idReceptor,
                rfcReceptor, razonReceptor, regimenReceptor, domicilioReceptor, cpReceptor, emailReceptor, fechaEmision,
                usoCFDI, tipoCFDI, moneda, formaPago, metodoPago, xml, folioFiscal, noCertificado, noCertificadoSAT,
                selloDigitalEmisor, selloDigitalSAT, cadenaOriginal, cadenaOriginalCumplimiento, codigoQR,
                fechaCertificacion, totalIVA, totalIEPS, subTotal, total, descuento, version, usuario,
                idVentaRelacion, idContratoRelacion, idSucursal)
            VALUES(
                '$rfcEmisor', '$razonEmisor', '$regimenEmisor', '$domicilioEmisor', $idEntidadEmisor, $cpEmisor, $idReceptor,
                '$rfcReceptor', '$razonReceptor', '$regimenReceptor', '$domicilioReceptor', '$cpReceptor', '$emailReceptor', '$fechaHora',
                '$usoCfdi', '$tipoComprobante', '$moneda', '$formaPago', '$metodoPago', '$xmlTIMBRE', '$folioFiscal', '$noCertificado', '$noCertificadoSAT',
                '$selloDigitalEmisor', '$selloDigitalSAT', '$cadenaOriginal', '$cadOrig_cump_v11', '$url_QR',
                '$fechaCertificacion', $sumatoriaIVA, 0, $subTotal, $TOTAL, 0, $version, $idUsuario,
                $idVenta, $idContrato, $idSucursal)";
    if ($mysqli->query($sql))
    {
        $idFactura_ = $mysqli->insert_id;
        for ($y=0; $y < sizeof($detalle_factura); $y++)
        {
            $idProducto_        = 0;
            $claveProdServ_     = $detalle_factura[$y]['claveSat'];
            $cantidad_          = $detalle_factura[$y]['cantidad'];
            $claveUnidad_       = $detalle_factura[$y]['claveUnidad'];
            $nombreUnidad_      = $detalle_factura[$y]['nombreUnidad'];
            $descripcion_       = $detalle_factura[$y]['descripcion'];
            $valorUnitario_     = $detalle_factura[$y]['precioU'];
            $iva_               = $detalle_factura[$y]['iva'];
            // $ieps_              = $detalle_factura[$y]['ieps'];
            $importe_           = $detalle_factura[$y]['importe'];
            $descuento_         = $detalle_factura[$y]['descuento'];
            $sql_det = "INSERT INTO detalle_facturas (
                            idFactura, idProducto, claveSat, cantidad,
                            claveUnidad, nombreUnidad, descripcion, precioU,
                            iva, importe, descuento)
                        VALUES ( $idFactura_, $idProducto_, '$claveProdServ_', $cantidad_,
                            '$claveUnidad_', '$nombreUnidad_', '$descripcion_', '$valorUnitario_',
                            $iva_, $importe_, $descuento_)";
            if(!$mysqli->query($sql_det))
            {
                progreso(0, "Factura timbrada, pero no se pudo guardar en la base de datos. Error: ".$mysqli->error, 1);
                $mysqli->rollback();
            }
            else {
                $sql = $tipoFactura == 'venta' ? "UPDATE ventas SET idFactura = $idFactura_ WHERE id = $idVenta LIMIT 1" : "UPDATE contratos SET idFactura = $idFactura_ WHERE id = $idContrato LIMIT 1";
                if ($mysqli->query($sql))
                {
                    $mysqli->commit();
                }
                else
                {
                    progreso(0, "Factura timbrada, pero no se pudo guardar en la base de datos. Error: ".$mysqli->error, 1);
                    $mysqli->rollback();
                }
            }
        }

    }
    else
    {
        progreso(0, "Factura timbrada, pero no se pudo guardar en la base de datos. Error: ".$mysqli->error, 1);
        $mysqli->rollback();
    }
    ////////////////////////////////////////////// REGISTRAR PAGO ///////////////////////////////////////////////////////

    $archivoXML     = fopen("XML/".str_pad($idFactura_, 10, "0", STR_PAD_LEFT).".xml", "w");
    if($archivoXML == false)
        progreso(0, "Error al descargar el XML, pero la factura SÍ fue timbrada", 1);
    fwrite($archivoXML, $xmlTIMBRE);
    fclose($archivoXML);
    $factura = new factura($idFactura_, $mysqli);
    genFactura($factura,$mysqli,0);
    if($enviarCfdi == 1)
    {
        progreso(10, "Enviando correo-e al cliente", 0);
        usleep(1000000);

        $mail = new PHPMailer();
        $mail->isSMTP();
        //Set the hostname of the mail server
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        //Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = "gamb2006@gmail.com";
        //Password to use for SMTP authentication
        $mail->Password = "dvrselvsclpssasF";
        //Set who the message is to be sent from
        $mail->From='gamb2006@gmail.com';
        $mail->FromName = utf8_decode($factura->nombreSucursal); //A RELLENAR Nombre a mostrar del remitente.
        //Set an alternative reply-to address
        //$mail->addReplyTo('replyto@example.com', 'First Last');
        //Set who the message is to be sent to
        $mail->addAddress($emailReceptor);
        //Set the subject line
        $mail->Subject = 'Su factura, gracias!';
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        //$mail->msgHTML(file_get_contents('contents.html'), __DIR__);
        //Replace the plain text body with one created manually
        //$mail->AltBody = 'This is a plain-text message body';
        $msg = "<p>Envío del comprobante fiscal digital.</p></br>";

        $mail->IsHTML(true); // El correo se envía como HTML
        $mail->Body    = $msg;
        //Attach an image file
        $mail->addAttachment("XML/".str_pad($idFactura_, 10, "0", STR_PAD_LEFT).".xml");
        $mail->addAttachment("XML/".str_pad($idFactura_, 10, "0", STR_PAD_LEFT).".pdf");
        //send the message, check for errors
        if (!$mail->send())
        {
            progreso(100, "Factura creada correctamente. No se envió e-mail!", 0);
            $script         =  "<script>";
            $script         .= "window.parent.mensaje('info','Tu factura ha sido timbrada con éxito. (No se pudo enviar e-mail)<br><h5><a href=\"listarFacturas.php\" class=\"orange\">Lista de facturas</a></h5>');";
            $script         .= 'window.parent.deshabilitar(false);';
            $script         .= '</script>';
            echo $script;
        } else
        {
            progreso(100, "Factura creada correctamente. E-mail enviado correctamente!", 0);
            $script         =  "<script>";
            $script         .= "window.parent.mensaje('success','Tu factura ha sido timbrada y enviada con éxito!<br><h5><a href=\"listarFacturas.php\" class=\"orange\">Lista de facturas</a></h5>');";
            $script         .= 'window.parent.deshabilitar(false);';
            $script         .= '</script>';
            echo $script;
        }
    }
    else
    {
        progreso(50, "Factura creada correctamente. SIN envío de e-mail!", 0);
        $script         =  "<script>";
        $script         .= "window.parent.mensaje('success','Tu factura ha sido timbrada correctamente. NO se envió por e-mail.<br><h5><a href=\"listarFacturas.php\" class=\"orange\">Lista de facturas</a></h5>');";
        $script         .= '</script>';
        echo $script;
    }
?>
    </body>
<?php
}
?>
