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
            require_once "../php/contrato.class.php";
			require_once ("../php/query.class.php");
			$query = new Query();
            $contrato = new Contrato($idContrato, $query);
            if ($contrato->id == 0)
            {
                echo "No se encontró el contrato con el ID indicado.";
                die;
            }

    /////////////////////////////////////////////////////////// ARMAR PDF ESTADO DE CUENTA DEL CONTRATO !!! ////////////////////////////////////////////////////////////////
            $margen                     = 0; //Margen de celdas
            $renglon                    = 4.4;
            $pdf                        = new PDF_Code128('P','mm','Letter');
            $pdf                        ->AliasNbPages();
            $pdf->AddPage();
            $pdf->SetMargins(8,20);
            $pdf->Image('../images/avatars/sucursales/'.$contrato->idSucursal.'/logo.jpg',7,5,-370);
            $pdf->SetFont('times','',10);
            $code           = str_pad($contrato->id, 10, "0", STR_PAD_LEFT);
            $pdf->Code128(167,11," ".$code,38,12);
            $pdf->SetXY(172,20.5);
            $pdf->SetFillColor(255, 140, 26);
            $pdf->Cell(30,9,"Id Contrato.- ".$code,$margen,1,'C');
            if ($contrato->motivoCancelado)
            {
              $pdf->SetXY(172,24.5);
              $pdf->SetTextColor(255,0,0);
              $pdf->SetFont('times','B',11);
              $pdf->Cell(30,9,"CANCELADO",0,0,'C',0);
              $pdf->SetTextColor(0);
              $pdf->SetFont('times','',10);
            }
            $pdf->SetXY(37,4);
            $pdf->Cell(168,5,utf8_decode(''),$margen,1,'C',true);
            $pdf->SetFont('times','B',27);
            $pdf->Cell(200,3,utf8_decode(""),$margen,1,'C');
            $pdf->Cell(200,7,utf8_decode($contrato->nombreSucursal),$margen,1,'C');
            $pdf->SetFont('times','I',8);
            $pdf->Cell(200,2,utf8_decode('"'.$contrato->lemaSucursal.'"'),$margen,1,'C');

            $pdf->SetFont('times','BU',17.5);
            $pdf->Cell(200,8,utf8_decode("ESTADO DE CUENTA"),$margen,1,'C');
            $pdf->SetFillColor(233);
            $pdf->SetDrawColor(255);
            $pdf->SetLineWidth(0.6);
            $pdf->Cell(200,3,utf8_decode(""),$margen,1,'C');
            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(17,$renglon,utf8_decode("ELABORÓ:"),$margen,0,'L');
            $pdf->SetFont('Courier','',9.5);
            $pdf->Cell(70,$renglon,utf8_decode($sesion->get("nombre")." ".$sesion->get("apellidop")." ".$sesion->get("apellidom")),1,0,'C',true);
            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(22,$renglon,utf8_decode('TITULAR:'),$margen,0,'L');
            $pdf->SetFont('Courier','',9.5);
            $pdf->Cell(88,$renglon,utf8_decode($contrato->nombreCliente),1,1,'C',true);
            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(21,$renglon,utf8_decode('FECHA/HR:'),$margen,0,'L');
            $pdf->SetFont('Courier','',9.5);
            $pdf->Cell(47,$renglon,utf8_decode(date("d-m-Y h:i:s a")),1,0,'C',true);
            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(22,$renglon,utf8_decode('DIRECCIÓN:'),$margen,0,'L');
            $pdf->SetFont('Times','',7.5);
            $pdf->Cell(107,$renglon,utf8_decode($contrato->domicilio),1,1,'C',true);
            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(13,$renglon,utf8_decode('PLAN:'),$margen,0,'L');
            $pdf->SetFont('Courier','',9.5);
            $pdf->Cell(45,$renglon,utf8_decode($contrato->nombrePlan),1,0,'C',true);
            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(36,$renglon,utf8_decode('COSTO DEL PLAN:'),$margen,0,'L');
            $pdf->SetFont('Courier','',9.5);
            $pdf->Cell(26,$renglon,utf8_decode(number_format($contrato->precio,2,".",",")),1,0,'C',true);
            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(38,$renglon,utf8_decode('FECHA DE INICIO:'),$margen,0,'L');
            $pdf->SetFont('Courier','',9.5);
            $pdf->Cell(39,$renglon,utf8_decode($contrato->fechaPrimerAportacion()),1,1,'C',true);

            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(24,$renglon,utf8_decode('INVERSIÓN:'),$margen,0,'L');
            $pdf->SetFont('Courier','',9.5);
            $pdf->Cell(26,$renglon,utf8_decode(number_format($contrato->anticipo,2,".",",")),1,0,'C',true);
            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(26,$renglon,utf8_decode('APORTACIÓN:'),$margen,0,'L');
            $pdf->SetFont('Courier','',9.5);
            $pdf->Cell(26,$renglon,utf8_decode(number_format($contrato->aportacion,2,".",",")),1,0,'C',true);
            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(28,$renglon,utf8_decode('COSTO TOTAL:'),$margen,0,'L');
            $pdf->SetFont('Courier','',9.5);
            $pdf->Cell(25,$renglon,utf8_decode(number_format($contrato->costoTotal,2,".",",")),1,0,'C',true);
            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(16,$renglon,utf8_decode('SALDO:'),$margen,0,'L');
            $pdf->SetFont('Courier','',9.5);
            $pdf->Cell(26,$renglon,utf8_decode(number_format($contrato->saldo($mysqli),2,".",",")),1,1,'C',true);
            $salto = FALSE;
            if ($contrato->descuentoDuplicacionInversion)
            {
                $pdf->SetFont('Courier','B',10.5);
                $pdf->Cell(42,$renglon,utf8_decode('DESC. DUPLIC. INV:'),$margen,0,'L');
                $pdf->SetFont('Courier','',9.5);
                $pdf->Cell(22,$renglon,utf8_decode(number_format($contrato->descuentoDuplicacionInversion,2,".",",")),1,0,'C',true);
                $salto = TRUE;
            }
            if ($contrato->descuentoCambioFuneraria)
            {
                $pdf->SetFont('Courier','B',10.5);
                $pdf->Cell(54,$renglon,utf8_decode('DESC. CAMBIO FUNERARIA:'),$margen,0,'L');
                $pdf->SetFont('Courier','',9.5);
                $pdf->Cell(22,$renglon,utf8_decode(number_format($contrato->descuentoCambioFuneraria,2,".",",")),1,0,'C',true);
                $salto = TRUE;
            }
            if ($contrato->descuentoAdicional)
            {
                $pdf->SetFont('Courier','B',10.5);
                $pdf->Cell(35,$renglon,utf8_decode('DESCUENTO ADIC:'),$margen,0,'L');
                $pdf->SetFont('Courier','',9.5);
                $pdf->Cell(22,$renglon,utf8_decode(number_format($contrato->descuentoAdicional,2,".",",")),1,0,'C',true);
                $salto = TRUE;
            }
            if ($salto)
            {
                $pdf->Cell(1,$renglon,"",$margen,1,'C');
            }

            $pdf->SetFont('Courier','B',10.5);
            $pdf->Cell(28,$renglon,utf8_decode('COMENTARIOS:'),$margen,0,'L');
            $pdf->SetFont('Times','',7.5);
            $pdf->Cell(22,$renglon,utf8_decode($contrato->comentarios_estatus($mysqli,FALSE)),1,1,'L');
            if (strlen($contrato->observaciones) > 0) {
                // code...
                $pdf->SetFont('Courier','B',10.5);
                $pdf->Cell(33,$renglon,utf8_decode('OBSERVACIONES:'),$margen,0,'L');
                $pdf->SetFont('Courier','',9.5);
                $pdf->Cell(22,$renglon,utf8_decode($contrato->observaciones),1,1,'L');
            }

            $pdf->Cell(100,1.8,"",0,1,'L',0);
            $pdf->SetFillColor(99, 99, 99);
            $pdf->SetTextColor(255);
            $pdf->SetFont('Courier','B',9);
            $pdf->Cell(20,5,utf8_decode('NO.'),1,0,'C',TRUE);
            $pdf->Cell(28,5,utf8_decode('NO. RECIBO'),1,0,'C',TRUE);
            $pdf->Cell(50,5,utf8_decode('FECHA'),1,0,'C',TRUE);
            $pdf->Cell(43,5,utf8_decode('COBRÓ'),1,0,'C',TRUE);
            $pdf->Cell(30,5,utf8_decode('$ APORTACIÓN'),1,0,'C',TRUE);
            $pdf->Cell(26,5,utf8_decode('$ SALDO'),1,1,'C',TRUE);
            //$pdf->SetFillColor(255);
            $pdf->SetTextColor(0);
            $pdf->SetFillColor(0);
            $pdf->SetDrawColor(0);
            $pdf->SetFont('Times','',8.5);

            $pdf->Cell(20,3,utf8_decode("Inversión"),$margen,0,'R',0);
            $pdf->Cell(28,3,utf8_decode("--"),$margen,0,'R',0);
            $pdf->Cell(50,3,utf8_decode($contrato->fechaCreacion()),$margen,0,'C',0);
            $pdf->Cell(43,3,utf8_decode($contrato->nombresVendedor),$margen,0,'L',0);
            $pdf->Cell(30,3,utf8_decode("$".number_format($contrato->anticipo,2,".",",")),$margen,0,'R',0);
            $saldo = $contrato->costoTotal - $contrato->anticipo;
            $pdf->Cell(26,3,utf8_decode("$".number_format($saldo,2,".",",")),$margen,1,'R',0);
            $sql = "SELECT
                        detalle_pagos_contratos.fechaCreacion   AS fechaPago,
                        detalle_pagos_contratos.monto           AS monto,
                        folios_cobranza_asignados.folio         AS folio,
                        cat_usuarios.nombres                    AS cobrador
                    FROM detalle_pagos_contratos
                    INNER JOIN folios_cobranza_asignados
                    ON detalle_pagos_contratos.idFolio_cobranza = folios_cobranza_asignados.id
                    INNER JOIN cat_usuarios
                    ON detalle_pagos_contratos.usuario_cobro = cat_usuarios.id
                    WHERE detalle_pagos_contratos.idContrato = $idContrato
                    AND detalle_pagos_contratos.activo = 1
                    AND folios_cobranza_asignados.activo = 1";
            $res_det    = $mysqli->query($sql);
            $cont       = 0;
            while ($row_det = $res_det->fetch_assoc())
            {
                $cont++;
                $saldo -= $row_det['monto'];
                $pdf->Cell(20,3,utf8_decode($cont),$margen,0,'R',0);
                $pdf->Cell(28,3,utf8_decode($row_det['folio']),$margen,0,'R',0);
                $pdf->Cell(50,3,utf8_decode(date_format(date_create($row_det['fechaPago']),'d-m-Y h:i:s a')),$margen,0,'C',0);
                $pdf->Cell(43,3,utf8_decode($row_det['cobrador']),$margen,0,'L',0);
                $pdf->Cell(30,3,utf8_decode("$".number_format($row_det['monto'],2,".",",")),$margen,0,'R',0);
                $pdf->Cell(26,3,utf8_decode("$".number_format($saldo,2,".",",")),$margen,1,'R',0);
            }
            $pdf->SetDrawColor(0);
            $pdf->SetLineWidth(0.2);

            $pdf->Cell(197,2,"",0,1,'L');
            $pdf->Cell(197,0,"",1,1,'L');
            //$totalVenta     = $rowVenta['totalventa'];
            //$totalVenta_f   = number_format($totalVenta, 2);
            $pdf->SetFont('Times','',10);
            $pdf->Cell(69,6,utf8_decode('TOTAL APORTACIONES:'),0,0,'R');
            $pdf->SetFont('Times','B',10);
            $pdf->Cell(18,6,utf8_decode($cont),0,0,'L');
            $pdf->SetFont('Times','',10);
            $pdf->Cell(25,6,utf8_decode('ABONADO:'),1,0,'R',0);
            $pdf->SetFont('Times','B',10);
            $pdf->Cell(30,6,utf8_decode('$'.number_format($contrato->totalAbonado($mysqli),2,".",",")),1,0,'R',0);
            $pdf->SetFont('Times','',10);
            $pdf->Cell(25,6,utf8_decode('SALDO:'),1,0,'R',0);
            $pdf->SetFont('Times','B',10);
            $pdf->Cell(30,6,utf8_decode('$'.number_format($contrato->saldo($mysqli),2,".",",")),1,1,'R',0);

            $pdf->Output();

        }
    }

?>
