<?php
	// error_reporting(E_ALL);
	ini_set('display_errors', '0');
    require_once ('../connect/bd.php');
    require_once ("../connect/sesion.class.php");
    $sesion = new sesion();
    require_once ("../connect/cerrarOtrasSesiones.php");
    require_once ("../connect/usuarioLogeado.php");
    require_once ("../php/funcionesVarias.php");
    require_once ('../fpdf/code128.php');
    if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
    {
        header("Location: salir.php");
    }
    else
    {
        if (isset($_GET['idContrato']))
        {
            $idContrato = $_GET['idContrato'];
        }
        else
        {
            die;
        }
        if (!$idContrato = validarFormulario('i',$idContrato,0))
        {
            echo "El formato del Id del contrato no es el esperado.";
            die;
        }
        else
        {
			require_once ("../php/query.class.php");
			$query = new Query();
            require "../php/contrato.class.php";
            $contrato = new Contrato($idContrato,$query);
            if (!$contrato->id)
            {
                echo "Error al consultar en la base de datos. No existe el contrato con el ID especificado";
                die;
            }
            $margen = 0;
            $pdf=new PDF_Code128('P','mm','Legal');
            $pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->SetFont('times','',10);
            $code = str_pad($contrato->id, 10, "0", STR_PAD_LEFT);
            $pdf->Code128(167,11,$code,38,12);
            $pdf->SetXY(172,20.5);
            $pdf->SetFillColor(51, 153, 255);
            $pdf->Cell(8,9,"No.- ",$margen,0,'C');
            $pdf->SetTextColor(204, 0, 0);
            $pdf->Cell(4,9,$code,$margen,1,'L');
			if ($contrato->motivoCancelado)
			{
				// $pdf->Image('../images/icons/cancelada.png',30,33, 140);
				$pdf->SetXY(172,24.5);
				$pdf->SetTextColor(255,0,0);
				$pdf->SetFont('times','B',11);
				$pdf->Cell(30,9,"CANCELADO",0,0,'C',0);
				$pdf->SetTextColor(0);
				$pdf->SetFont('times','',10);
			}
            $pdf->SetXY(37,4);
            $pdf->SetTextColor(0);

            $pdf->Cell(168,5,utf8_decode(''),$margen,1,'C',true);
            $pdf->SetFont('times','B',27);
            $pdf->Cell(200,1,utf8_decode(""),$margen,1,'C');
            $pdf->Cell(200,7,utf8_decode($contrato->nombreSucursal),$margen,1,'C');
            $pdf->SetFont('times','I',8);
            $pdf->Cell(200,2,utf8_decode('"'.$contrato->lemaSucursal.'"'),$margen,1,'C');
            $pdf->SetFont('times','',10);
            $pdf->Cell(200,4,utf8_decode($contrato->domicilioSucursal),$margen,1,'C');
            $pdf->Cell(200,4,utf8_decode($contrato->representanteSucursal),$margen,1,'C');
            $pdf->Cell(200,4,utf8_decode("RFC: ".$contrato->rfcSucursal." CURP: ".$contrato->curpSucursal),$margen,1,'C');
            $pdf->Cell(200,4,utf8_decode("Teléfonos: ".$contrato->tel1Sucursal ." / ". $contrato->tel2Sucursal),$margen,1,'C');

            $pdf->SetFont('times','BU',24);
            $pdf->Cell(200,8,utf8_decode("CONTRATO"),$margen,1,'C');
            $pdf->SetFillColor(233);
            $pdf->SetDrawColor(255);
            $pdf->SetLineWidth(0.6);
            $pdf->Cell(200,3,utf8_decode(""),$margen,1,'C');
            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(17,5.5,utf8_decode("NOMBRE:"),$margen,0,'L');
            $pdf->SetFont('Courier','',9.5);
            $pdf->Cell(105,5,utf8_decode($contrato->nombreCliente),1,0,'C',true);
            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(24,5.5,utf8_decode("CREADO EL:"),$margen,0,'C');
            $pdf->SetFont('Courier','',9.5);
            $pdf->Cell(49,5,utf8_decode($contrato->fechaCreacion()),1,1,'C',true);
            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(24,5.5,utf8_decode("DIRECCIÓN:"),$margen,0,'L');
            $pdf->SetFont('Courier','',9.5);
            $pdf->Cell(171,5,utf8_decode($contrato->domicilio),1,1,'C',true);
            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(11,5.5,utf8_decode("RFC:"),$margen,0,'L');
            $pdf->SetFont('Courier','',9.5);
            $pdf->Cell(49,5,utf8_decode($contrato->rfcCliente),1,0,'C',true);
            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(22,5.5,utf8_decode("TELÉFONO:"),$margen,0,'L');
            $pdf->SetFont('Courier','',9.5);
            $pdf->Cell(47,5,utf8_decode($contrato->telCliente),1,0,'C',true);
            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(19,5.5,utf8_decode("CELULAR:"),$margen,0,'L');
            $pdf->SetFont('Courier','',9.5);
            $pdf->Cell(47,5,utf8_decode($contrato->celCliente),1,1,'C',true);
            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(38,5.5,utf8_decode("NOMBRE DEL PLAN:"),$margen,0,'L');
            $pdf->SetFont('Courier','',9.5);
            $pdf->Cell(75,5,utf8_decode($contrato->nombrePlan),1,0,'C',true);
            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(47,5.5,utf8_decode("FECHA 1° APORTACIÓN:"),$margen,0,'L');
            $pdf->SetFont('Courier','',9.5);
            $pdf->Cell(35,5,utf8_decode($contrato->fechaPrimerAportacion()),1,1,'C',true);
            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(29,5.5,utf8_decode("COSTO TOTAL:"),$margen,0,'L');
            $pdf->SetFont('Courier','',9.5);
            $pdf->Cell(34,5,utf8_decode("$".number_format($contrato->precio,"2",".",",")),1,0,'C',true);
            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(33,5.5,utf8_decode("INVERSIÓN:"),$margen,0,'L');
            $pdf->SetFont('Courier','',9.5);
            $pdf->Cell(33,5,utf8_decode("$".number_format($contrato->anticipo,2,".",",")),1,0,'C',true);
            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(33,5.5,utf8_decode("APORTACIONES:"),$margen,0,'L');
            $pdf->SetFont('Courier','',9.5);
            $pdf->Cell(33,5,utf8_decode("$".number_format($contrato->aportacion,2,".",",")),1,1,'C',true);
            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(44,5.5,utf8_decode("FRECUENCIA DE PAGO:"),$margen,0,'L');
            $pdf->SetFont('Courier','',9.5);
            $pdf->Cell(34,5,utf8_decode($contrato->frecuenciaPago()),1,0,'C',true);
            // $pdf->Cell(22,5.5,utf8_decode('PROVEEDOR:'),$margen,0,'L');
            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(39,5.5,utf8_decode("NO. APORTACIONES:"),$margen,0,'L');
            $pdf->SetFont('Courier','',9.5);
            $pdf->Cell(20,5,utf8_decode($contrato->pagosCalculados()),1,1,'C',true);
            $pdf->SetFont('Times','',9.4);
            $pdf->WriteHTML(utf8_decode($contrato->clausulas));
            $pdf->SetFont('Times','',10);
            $pdf->Cell(200,3,utf8_decode(""),$margen,1,'C');
            $pdf->Cell(0,4, "El plan contratado es el plan: $contrato->nombrePlan, con un costo total de $".number_format($contrato->precio,2,".",",").", el cual consta de lo siguiente:",1,1,'L' );
            $sql = "SELECT
                        detalle_contrato.idProducto,
                        detalle_contrato.idServicio,
                        detalle_contrato.cantidad
                    FROM detalle_contrato
                    WHERE detalle_contrato.idContrato = ? AND detalle_contrato.activo = 1";
            $prepare_det = $mysqli->prepare($sql);
            if ($prepare_det &&
            	$prepare_det -> bind_param('i', $idContrato) &&
            	$prepare_det -> execute() &&
            	$prepare_det -> store_result() &&
            	$prepare_det -> bind_result($idProducto, $idServicio, $cantidad))
            {
                $pdf->SetFont('Times','',10);
                $pdf->SetLineWidth(0.2);
                $pdf->SetDrawColor(0);
                $pdf->Cell(200,3,utf8_decode(""),$margen,1,'C');
                $columna = 1;
                while ($prepare_det->fetch())
                {
                    if ($columna % 3 == 0)
                    {
                        $saltoLinea = 1;
                        $columna = 1;
                    }
                    else
                        $saltoLinea = 0;
                    if ($idProducto != 0)
                    {
                        $sql = "SELECT nombre FROM cat_productos WHERE id = ? LIMIT 1";
                        $prepare_prod = $mysqli->prepare($sql);
                        if ($prepare_prod &&
                        	$prepare_prod -> bind_param('i', $idProducto) &&
                        	$prepare_prod -> execute() &&
                        	$prepare_prod -> store_result() &&
                        	$prepare_prod -> bind_result($nombreProducto) &&
                            $prepare_prod -> fetch())
                        {
                            $pdf->Cell(64.5,5,chr(149)." ".utf8_decode("1 ".$nombreProducto),1,$saltoLinea,'L');
                        }
                    }
                    if ($idServicio != 0)
                    {
                        $sql = "SELECT nombre FROM cat_servicios WHERE id = ? LIMIT 1";
                        $prepare_serv = $mysqli->prepare($sql);
                        if ($prepare_serv &&
                        	$prepare_serv -> bind_param('i', $idServicio) &&
                        	$prepare_serv -> execute() &&
                        	$prepare_serv -> store_result() &&
                        	$prepare_serv -> bind_result($nombreServicio) &&
                            $prepare_serv -> fetch())
                        {
                            $pdf->Cell(64.5,5,chr(149)." ".utf8_decode("1 ".$nombreServicio),1,$saltoLinea,'L');
                        }
                    }
                    $columna++;
                }
            }
            if (strlen($contrato->observaciones) > 0)
            {
                $pdf->Cell(200,4,utf8_decode(""),$margen,1,'C');
                $pdf->SetFont('Courier','B',10.5);
                $pdf->Cell(64.5,5,"OBSERVACIONES:",0,1,'L');
                $pdf->SetFont('Courier','',9.5);
                $pdf->MultiCell(194,4,utf8_decode($contrato->observaciones),0,'L',true);
            }
            $pdf->SetLineWidth(0.4);
            $pdf->SetFont('Times','',11);
            $pdf->Cell(200,28,utf8_decode(""),$margen,1,'C');
            $pdf->Cell(8.5,0,utf8_decode(""),0,0,'C');
            $pdf->Cell(80,0,utf8_decode(""),1,0,'C');
            $pdf->Cell(20,0,utf8_decode(""),0,0,'C');
            $pdf->Cell(80,0,utf8_decode(""),1,1,'C');
            $pdf->Cell(8.5,4,utf8_decode(""),0,0,'C');
            $pdf->Cell(80,4,utf8_decode("C. ".$contrato->representanteSucursal),0,0,'C');
            $pdf->Cell(20,4,utf8_decode(""),0,0,'C');
            $pdf->Cell(80,4,utf8_decode("C. ".$contrato->nombreCliente),0,1,'C');
            $pdf->Cell(8.5,4,utf8_decode(""),0,0,'C');
            $pdf->Cell(80,4,utf8_decode("FIRMA DEL REPRESENTANTE"),0,0,'C');
            $pdf->Cell(20,4,utf8_decode(""),0,0,'C');
            $pdf->Cell(80,4,utf8_decode("FIRMA DEL TITULAR"),0,1,'C');
            $pdf->Image('../images/avatars/sucursales/'.$contrato->idSucursal.'/logo.jpg',7,5,-300);
            $pdf->Output();
            exit;
        }
    }

?>
