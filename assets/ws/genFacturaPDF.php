<?php
    function genFactura($factura,$mysqli,$vista)
    {
        $pdf                = new PDF_Code128('P','mm','Letter');
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetMargins(8,20);
        $pdf->SetFont('Courier','B',9);
        //$pdf->SetFillColor(225);
        $idSucursal = $factura->idSucursal;
        $pdf->Image("../images/avatars/sucursales/$idSucursal/logo.png",9,8,-300);
        //$pdf->Image('certEmisorPrueba/codigoqr.png',173,25,-285);
        $qrcode = new QRcode($factura->codigoQR, 'H'); // error level : L, M, Q, H
        $qrcode->disableBorder();
        $qrcode->displayFPDF($pdf, 173, 25, 30);
        //$idFactura = $_GET['idFactura'];
        $mB                 = 0; // Mostrar ocultar bordes cell
        $code               = "Folio: ";
        $code               .=  $code_ = str_pad($factura->id, 10, "0", STR_PAD_LEFT);
        $pdf->Code128(169,10," ".$code_,38,12);
        $pdf->SetXY(174,20);
        $pdf->Cell(30,7,$code,0,1,'C');
        //$pdf->Write(5,$code);
        $pdf->SetXY(8,10);
        $pdf->SetFont('Arial','B',15);
        $pdf->SetTextColor(0,0,100);
        $pdf->Cell(200,7,utf8_decode('"'.$factura->nombreSucursal.'"'),$mB,1,'C');
        $pdf->SetFont('Arial','',10);
        $pdf->SetTextColor(0,0,0);
        $pdf->Cell(200,4,utf8_decode($factura->razonEmisor),$mB,1,'C');
        $pdf->Cell(200,4,utf8_decode($factura->domicilioEmisor),$mB,1,'C');
        $pdf->Cell(200,4,utf8_decode(""),$mB,1,'C');
        $pdf->SetFont('Arial','B',12);
        $pdf->SetTextColor(0,0,100);
        $pdf->Cell(200,4,utf8_decode("RFC: ". $factura->rfcEmisor),$mB,1,'C');
        $pdf->SetFont('Arial','B',10);
        //$pdf->Cell(200,4,utf8_decode($regimenEmisor),$mB,1,'C');
        $pdf->Cell(200,4,utf8_decode($factura->nombreRegimenFiscal),$mB,1,'C');
        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial','B',7.5);
        $pdf->Cell(30,3,utf8_decode('Nombre Receptor: '),$mB,0,'L');
        $pdf->SetFont('Arial','',7.5);
        $pdf->Cell(70,3,utf8_decode($factura->razonReceptor),$mB,0,'L');

        $pdf->SetFont('Arial','B',7.5);
        $pdf->Cell(30,3,utf8_decode('Método de pago: '),$mB,0,'L');
        $pdf->SetFont('Arial','',7.5);
        $pdf->Cell(70,3,utf8_decode($factura->metodoPago),$mB,1,'L');

        $pdf->SetFont('Arial','B',7.5);
        $pdf->Cell(30,3,utf8_decode('RFC Receptor: '),$mB,0,'L');
        $pdf->SetFont('Arial','',7.5);
        $pdf->Cell(70,3,utf8_decode($factura->rfcReceptor ),$mB,0,'L');

        $pdf->SetFont('Arial','B',7.5);
        $pdf->Cell(30,3,utf8_decode('Forma de pago: '),$mB,0,'L');
        $pdf->SetFont('Arial','',7.5);
        $pdf->Cell(70,3,utf8_decode($factura->formaPago." ".$factura->nombreFormaPago),$mB,1,'L');

        $pdf->SetFont('Arial','B',7.5);
        $pdf->Cell(30,3,utf8_decode('Domicilio Receptor:'),$mB,0,'L');
        $pdf->SetFont('Arial','',7.5);
        $pdf->MultiCell(70,3,utf8_decode($factura->domicilioReceptor),$mB,'L');

        $pdf->SetXY(8,43);
        $pdf->SetFont('Arial','B',7.5);
        $pdf->Cell(100,3,utf8_decode(''),$mB,0,'L');
        $pdf->Cell(30,3,utf8_decode('Tipo comprobante:'),$mB,0,'L');
        $pdf->SetFont('Arial','',7.5);
        $pdf->Cell(70,3,utf8_decode($factura->tipoCFDI),$mB,1,'L');
        $pdf->SetFont('Arial','B',7.5);
        $pdf->Cell(100,3,utf8_decode(''),$mB,0,'L');
        $pdf->Cell(30,3,utf8_decode('Moneda:'),$mB,0,'L');
        $pdf->SetFont('Arial','',7.5);
        $pdf->Cell(70,3,utf8_decode($factura->moneda),$mB,1,'L');
        $pdf->SetFont('Arial','B',7.5);
        $pdf->Cell(100,3,utf8_decode(''),$mB,0,'L');

        $pdf->Cell(30,3,utf8_decode('Fecha-hr emisión:'),$mB,0,'L');
        $pdf->SetFont('Arial','',7.5);
        $pdf->Cell(70,3,utf8_decode($factura->fechaEmision),$mB,1,'L');
        $pdf->SetFont('Arial','B',7.5);
        $pdf->Cell(200,3,utf8_decode(''),$mB,1,'L');
        $pdf->Cell(200,2,utf8_decode(''),$mB,1,'L');
        $pdf->Cell(16,3.5,utf8_decode('CLAVE'),1,0,'L');
        $pdf->Cell(8,3.5,utf8_decode('CANT'),1,0,'C');
        $pdf->Cell(28,3.5,utf8_decode('UNIDAD'),1,0,'C');
        $pdf->Cell(89,3.5,utf8_decode('DESCRIPCIÓN'),1,0,'C');
        $pdf->Cell(17,3.5,utf8_decode('PRECIOU'),1,0,'C');
        $pdf->Cell(11,3.5,utf8_decode('IVA'),1,0,'C');
        $pdf->Cell(11,3.5,utf8_decode('IEPS'),1,0,'C');
        $pdf->Cell(19,3.5,utf8_decode('IMPORTE'),1,1,'C');

        $pdf->SetFont('Arial','',7.5);
        $detalle = $factura->detalle($mysqli);
        // print_r($detalle);

        for ($i=0; $i < sizeof($detalle); $i++)
        {
        // foreach ($detalle as $det) {
        //     // code...
            $pdf->Cell(16,3.5,utf8_decode($detalle[$i]['claveSat']),$mB,0,'L');
            $pdf->Cell(8,3.5,utf8_decode(number_format($detalle[$i]['cantidad'],1,".","")),$mB,0,'R');
            $pdf->Cell(28,3.5,utf8_decode($detalle[$i]['claveUnidad']."-".$detalle[$i]['nombreUnidad']),$mB,0,'L');
            $pdf->Cell(89,3.5,utf8_decode($detalle[$i]['descripcion']),$mB,0,'L');
            $pdf->Cell(17,3.5,utf8_decode(number_format($detalle[$i]['precioU'],2,".",",")),$mB,0,'R');
            $pdf->Cell(11,3.5,utf8_decode(number_format($detalle[$i]['iva'],2,".","")),$mB,0,'C');
            $pdf->Cell(11,3.5,utf8_decode(number_format($detalle[$i]['ieps'],2,".","")),$mB,0,'C');
            $pdf->Cell(19,3.5,utf8_decode(number_format($detalle[$i]['importe'],2,".",",")),$mB,1,'R');
        }
        $pdf->Cell(200,1.5,utf8_decode(''),$mB,1,'R');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(60,3.5,utf8_decode('SERIE DEL CERTIFICADO DEL EMISOR:'),$mB,0,'R');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(92,3.5,utf8_decode($factura->noCertificado),$mB,0,'L');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(24,3.5,utf8_decode('SUB TOTAL:'),$mB,0,'R');
        $pdf->Cell(24,3.5,utf8_decode($factura->subTotal()),$mB,1,'R');

        $pdf->Cell(60,3.5,utf8_decode('FOLIO FISCAL:'),$mB,0,'R');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(92,3.5,utf8_decode($factura->folioFiscal),$mB,0,'L');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(24,3.5,utf8_decode('IVA:'),$mB,0,'R');
        $pdf->Cell(24,3.5,utf8_decode($factura->totalIVA()),$mB,1,'R');

        $pdf->Cell(60,3.5,utf8_decode('NO. DE SERIE DEL CERTIFICADO DEL SAT:'),$mB,0,'R');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(92,3.5,utf8_decode($factura->noCertificadoSAT),$mB,0,'L');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(24,3.5,utf8_decode('IEPS:'),$mB,0,'R');
        $pdf->Cell(24,3.5,utf8_decode($factura->totalIEPS()),$mB,1,'R');

        $pdf->Cell(60,3.5,utf8_decode('FECHA Y HORA DE CERTIFICACIÓN:'),$mB,0,'R');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(92,3.5,utf8_decode($factura->fechaCertificacion),$mB,0,'L');
        $pdf->SetFont('Arial','B',9.5);
        $pdf->Cell(24,3.5,utf8_decode('TOTAL:'),$mB,0,'R');
        $pdf->Cell(24,3.5,utf8_decode("$".$factura->total()),$mB,1,'R');
        $pdf->Cell(200,8,utf8_decode(''),$mB,1,'R');
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(200,3.5,utf8_decode('ESTE DOCUMENTO ES UNA REPRESENTACIÓN IMPRESA DE UN CFDI'),$mB,1,'C');
        $pdf->SetFont('Arial','',7);
        $pdf->Cell(200,3.5,utf8_decode("VERSIÓN $factura->version"),$mB,1,'C');
        $pdf->Cell(200,3.5,utf8_decode("USOCFDI: $factura->usoCFDI $factura->nombreUsoCFDI"),$mB,1,'C');
        $pdf->Cell(200,1.5,utf8_decode(''),$mB,1,'R');
        $pdf->SetFont('Arial','B',6.5);
        $pdf->Cell(200,3.5,utf8_decode('SELLO DIGITAL DEL CFDI'),$mB,1,'C');
        $pdf->SetFont('Arial','',5.5);
        $pdf->MultiCell(200,2.5,$factura->selloDigitalEmisor,$mB,'L');
        $pdf->SetFont('Arial','B',6.5);
        $pdf->Cell(200,3.5,utf8_decode('SELLO DIGITAL DEL SAT'),$mB,1,'C');
        $pdf->SetFont('Arial','',5.5);
        $pdf->MultiCell(200,2.2,$factura->selloDigitalSAT,$mB,'L');
        $pdf->SetFont('Arial','B',6.5);
        $pdf->Cell(200,3.5,utf8_decode('CADENA ORIGINAL'),$mB,1,'C');
        $pdf->SetFont('Arial','',5.5);
        $pdf->MultiCell(200,2.2,utf8_decode($factura->cadenaOriginal),$mB,'L');
        $pdf->SetFont('Arial','B',6.5);
        $pdf->Cell(200,3.5,utf8_decode('CADENA ORIGINAL DEL COMPLEMENTO DE CERTIFICACIÓN DIGITAL DEL SAT'),$mB,1,'C');
        $pdf->SetFont('Arial','',5.5);
        $pdf->MultiCell(200,2.2,utf8_decode($factura->cadenaOriginalCumplimiento),$mB,'L');
        if ($vista == 1)
            $pdf->Output();
        else
            $pdf->Output('F',"XML/".str_pad($factura->id, 10, "0", STR_PAD_LEFT).".pdf");
    }

?>
