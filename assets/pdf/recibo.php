<?php
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
    require "../php/responderJSON.php";
    function mes_a_texto($fechaMySql)
    {
        $parts = explode('-',$fechaMySql);
        $mes = $parts[1];
        switch ($mes)
        {
            case '1':
                $mesStr = "Enero";
                break;
            case '2':
                $mesStr = "Febrero";
                break;
            case '3':
                $mesStr = "Marzo";
                break;
            case '4':
                $mesStr = "Abril";
                break;
            case '5':
                $mesStr = "Mayo";
                break;
            case '6':
                $mesStr = "Junio";
                break;
            case '7':
                $mesStr = "Julio";
                break;
            case '8':
                $mesStr = "Agosto";
                break;
            case '9':
                $mesStr = "Septiembre";
                break;
            case '10':
                $mesStr = "Octubre";
                break;
            case '11':
                $mesStr = "Noviemre";
                break;
            case '12':
                $mesStr = "Diciembre";
                break;
        }
        return $mesStr;

    }
    function numtoletras($xcifra)
    {
        $xarray = array(0 => "Cero",
            1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE",
            "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE",
            "VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA",
            100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
        );
    //
        $xcifra = trim($xcifra);
        $xlength = strlen($xcifra);
        $xpos_punto = strpos($xcifra, ".");
        $xaux_int = $xcifra;
        $xdecimales = "00";
        if (!($xpos_punto === false)) {
            if ($xpos_punto == 0) {
                $xcifra = "0" . $xcifra;
                $xpos_punto = strpos($xcifra, ".");
            }
            $xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
            $xdecimales = substr($xcifra . "00", $xpos_punto + 1, 2); // obtengo los valores decimales
        }

        $XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
        $xcadena = "";
        for ($xz = 0; $xz < 3; $xz++) {
            $xaux = substr($XAUX, $xz * 6, 6);
            $xi = 0;
            $xlimite = 6; // inicializo el contador de centenas xi y establezco el límite a 6 dígitos en la parte entera
            $xexit = true; // bandera para controlar el ciclo del While
            while ($xexit) {
                if ($xi == $xlimite) { // si ya llegó al límite máximo de enteros
                    break; // termina el ciclo
                }

                $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
                $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres dígitos)
                for ($xy = 1; $xy < 4; $xy++) { // ciclo para revisar centenas, decenas y unidades, en ese orden
                    switch ($xy) {
                        case 1: // checa las centenas
                            if (substr($xaux, 0, 3) < 100) { // si el grupo de tres dígitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas

                            } else {
                                $key = (int) substr($xaux, 0, 3);
                                if (TRUE === array_key_exists($key, $xarray)){  // busco si la centena es número redondo (100, 200, 300, 400, etc..)
                                    $xseek = $xarray[$key];
                                    $xsub = subfijo($xaux); // devuelve el subfijo correspondiente (Millón, Millones, Mil o nada)
                                    if (substr($xaux, 0, 3) == 100)
                                        $xcadena = " " . $xcadena . " CIEN " . $xsub;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                    $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
                                }
                                else { // entra aquí si la centena no fue numero redondo (101, 253, 120, 980, etc.)
                                    $key = (int) substr($xaux, 0, 1) * 100;
                                    $xseek = $xarray[$key]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
                                    $xcadena = " " . $xcadena . " " . $xseek;
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 0, 3) < 100)
                            break;
                        case 2: // checa las decenas (con la misma lógica que las centenas)
                            if (substr($xaux, 1, 2) < 10) {

                            } else {
                                $key = (int) substr($xaux, 1, 2);
                                if (TRUE === array_key_exists($key, $xarray)) {
                                    $xseek = $xarray[$key];
                                    $xsub = subfijo($xaux);
                                    if (substr($xaux, 1, 2) == 20)
                                        $xcadena = " " . $xcadena . " VEINTE " . $xsub;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                    $xy = 3;
                                }
                                else {
                                    $key = (int) substr($xaux, 1, 1) * 10;
                                    $xseek = $xarray[$key];
                                    if (20 == substr($xaux, 1, 1) * 10)
                                        $xcadena = " " . $xcadena . " " . $xseek;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " Y ";
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 1, 2) < 10)
                            break;
                        case 3: // checa las unidades
                            if (substr($xaux, 2, 1) < 1) { // si la unidad es cero, ya no hace nada

                            } else {
                                $key = (int) substr($xaux, 2, 1);
                                $xseek = $xarray[$key]; // obtengo directamente el valor de la unidad (del uno al nueve)
                                $xsub = subfijo($xaux);
                                $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                            } // ENDIF (substr($xaux, 2, 1) < 1)
                            break;
                    } // END SWITCH
                } // END FOR
                $xi = $xi + 3;
            } // ENDDO

            if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
                $xcadena.= " DE";

            if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
                $xcadena.= " DE";

            // ----------- esta línea la puedes cambiar de acuerdo a tus necesidades o a tu país -------
            if (trim($xaux) != "") {
                switch ($xz) {
                    case 0:
                        if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                            $xcadena.= "UN BILLON ";
                        else
                            $xcadena.= " BILLONES ";
                        break;
                    case 1:
                        if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                            $xcadena.= "UN MILLON ";
                        else
                            $xcadena.= " MILLONES ";
                        break;
                    case 2:
                        if ($xcifra < 1) {
                            $xcadena = "CERO PESOS $xdecimales/100 M.N.";
                        }
                        if ($xcifra >= 1 && $xcifra < 2) {
                            $xcadena = "UN PESO $xdecimales/100 M.N. ";
                        }
                        if ($xcifra >= 2) {
                            $xcadena.= " PESOS $xdecimales/100 M.N. "; //
                        }
                        break;
                } // endswitch ($xz)
            } // ENDIF (trim($xaux) != "")
            // ------------------      en este caso, para México se usa esta leyenda     ----------------
            $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
            $xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
            $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
        } // ENDFOR ($xz)
        return trim($xcadena);
    }

    // END FUNCTION

    function subfijo($xx)
    { // esta función regresa un subfijo para la cifra
        $xx = trim($xx);
        $xstrlen = strlen($xx);
        if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
            $xsub = "";
        //
        if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
            $xsub = "MIL";
        //
        return $xsub;
    }

    $pdf = new PDF_Code128('P','mm','Letter');
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetMargins(8,20);
    $pdf->SetFont('Courier','',9);

    if(!isset($_GET['idRecibo']))
    {
        echo "El formato del Id del recibo no es el correcto";
        die;
    }
    else
    {
        $idRecibo = $_GET['idRecibo'];
        if (!$idRecibo = validarFormulario("i",$idRecibo))
        {
            echo "El formato del Id del recibo no es el correcto";
            die;
        }
    }
    $sql = "SELECT
                detalle_pagos_contratos.id AS idC_,                 detalle_pagos_contratos.idContrato AS idCont_,
                detalle_pagos_contratos.fechaCreacion,              detalle_pagos_contratos.monto,
                detalle_pagos_contratos.usuario_cobro,              clientes.nombres,
                clientes.apellidop,                                 clientes.apellidom,
                cat_sucursales.id,                                  cat_sucursales.direccion2,
                cat_usuarios.nombres,                               cat_usuarios.apellidop,
                cat_usuarios.apellidom,
                (SELECT COUNT(id) FROM detalle_pagos_contratos
                 WHERE detalle_pagos_contratos.id <= idC_
                 AND detalle_pagos_contratos.idContrato = idCont_
                 AND detalle_pagos_contratos.activo = 1),
                detalle_pagos_contratos.activo
            FROM detalle_pagos_contratos
            INNER JOIN contratos
            ON detalle_pagos_contratos.idContrato = contratos.id
            INNER JOIN clientes
            ON contratos.idTitular = clientes.id
            INNER JOIN cat_sucursales
            ON contratos.idSucursal = cat_sucursales.id
            INNER JOIN cat_usuarios
            ON detalle_pagos_contratos.usuario_cobro = cat_usuarios.id
            WHERE detalle_pagos_contratos.id = ?";
            $prepare_recibo = $mysqli->prepare($sql);
            if ($prepare_recibo &&
                $prepare_recibo->bind_param("i",$idRecibo) &&
                $prepare_recibo->execute() &&
                $prepare_recibo->store_result() &&
                $prepare_recibo->bind_result($idRecibo, $idContrato, $fechaCreacion, $monto, $idUsuario,
                                            $nombreCliente, $apellidopCliente, $apellidomCliente, $idSucursal, $lugarExpedicion,
                                            $nombreUsuario, $apellidopUsuario, $apellidomUsuario, $noDePago, $activo) &&
                $prepare_recibo->fetch() &&
                $prepare_recibo->num_rows > 0)
            {
                require "../php/contrato.class.php";
                $contrato           = new contrato($idContrato, $mysqli);
                $totPagosCalculados = $contrato->pagosCalculados();
                $mesTexto           = mes_a_texto($fechaCreacion); //Fecha de pago
                $fecha_dia          = date('d',strtotime($fechaCreacion));
                $fecha_sql          = $fechaCreacion;
                $parts              = explode('-',$fechaCreacion);
                $listaVentas        = '';
                $contVentas         = 0;
                $reciboNo = str_pad($idRecibo, 10, "0", STR_PAD_LEFT);
                $anio = $parts[0];
                $pdf->SetFillColor(225);
                $pdf->SetFont('Times','I',14);
                $pdf->SetXY(15,20);
                $pdf->Cell(30,10,utf8_decode('Recibo No.'),0,0,'L');
                $pdf->SetFont('Times','B',14);
                $pdf->Cell(30,10,$reciboNo,0,0,'R',True);
                $pdf->Cell(30,10);
                $pdf->SetFont('Times','I',14);
                $pdf->Cell(50,10,'Bueno por:',0,0,'R');
                $pdf->SetFont('Times','B',14);
                $pdf->Cell(37,10,'$'.number_format($monto,2,".",","),0,0,'R',True);
                $pdf->Ln();
                $pdf->SetFont('Times','I',14);
                $pdf->Cell(40,10,utf8_decode('Recibimos de:'),0,0,'R');
                $pdf->SetFont('Times','B',14);
                $pdf->Cell(125,10,utf8_decode($nombreCliente." ".$apellidopCliente." ".$apellidomCliente),0,0,'C');
                $pdf->Ln();
                $pdf->SetFont('Times','I',14);
                $pdf->Cell(40,10,utf8_decode('La cantidad de:'),0,0,'R');
                $pdf->SetFont('Times','B',11);
                //$cantLetra = num2letras(3000.00);
                $pdf->Cell(145,10,numtoletras($monto),0,0,'C',TRUE);
                $pdf->Ln();
                $pdf->SetFont('Times','I',14);
                $pdf->Cell(40,10,utf8_decode('Por concepto de:'),0,0,'R');
                $pdf->SetFont('Times','',14);
                $noDePago = $noDePago < 1 ? 1 : $noDePago;
                $pdf->Cell(125,10,utf8_decode('Aportación servicio funerario. Pago '.$noDePago.' de '.$totPagosCalculados),0,1,'C');
                $pdf->SetFont('Times','B',14);
				$fraseNoContrato = "";
				// $fraseNoContrato .= $activo ? "" : "==CANCELADO== ";
				$fraseNoContrato .= $activo ? "" : " ==RECIBO CANCELADO== ";
				$fraseNoContrato .= "Contrato Número: ".str_pad($idContrato, 9, "0", STR_PAD_LEFT);

                $pdf->Cell(200,10,utf8_decode($fraseNoContrato),0,0,'C');
                //$pdf->Ln();
                $pdf->Cell(40,10);
                $pdf->SetFont('Times','',14);
                $pdf->Ln();
                $pdf->Cell(66,10);
                $pdf->SetFont('Times','I',14);
                $pdf->Cell(40,10,utf8_decode($lugarExpedicion),0,0,'R');
                $pdf->Cell(10,10);
                $fechaD     = $fechaCreacion;
                $fecha_r    = date('Y-m-d',strtotime($fechaD));
                $parts      = explode('-',$fecha_r);
                $pdf->SetFont('Times','B',14);
                $pdf->Cell(10,10,$parts[2],0,0,'R');
                $pdf->SetFont('Times','I',14);
                $pdf->Cell(10,10,utf8_decode('de'),0,0,'C');
                $pdf->SetFont('Times','B',14);
                $mesPago = mes_a_texto($fecha_r);
                $pdf->Cell(25,10,$mesPago,0,0,'C');
                $pdf->SetFont('Times','I',14);
                $pdf->Cell(10,10,utf8_decode('del'),0,0,'C');
                $pdf->SetFont('Times','B',14);
                $pdf->Cell(14,10,$parts[0],0,0,'R');
                $pdf->SetXY(106,97);
                $pdf->SetFont('Times','I',14);
                $pdf->Cell(20,10,utf8_decode('Recibió:'),0,0,'R');
                $pdf->SetFont('Times','IB',14);
                $pdf->Cell(60,10,utf8_decode($nombreUsuario.' '.$apellidopUsuario),0,0,'C');
                $pdf->Line(50,38,193,38);//Recibí de
                $pdf->Line(50,58,193,58);//Por concepto de
                $pdf->Line(50,68,193,68);//Por concepto de (2)
                 $pdf->Line(117,78,193,78);//Fecha
                // $pdf->Line(133,78,160,78);//Mes
                // $pdf->Line(169,78,190,78);//Anio
                $pdf->Line(110,97,193,97);//Firma
                $pdf->Rect(8,11,190,96);//Contorno
                $pdf->Image('../images/avatars/sucursales/'.$idSucursal.'/logo.jpg',12,73,-290);

                // $pdf->Image('../images/logo2.jpg',12,73,-490);
            //$pdf->Output('D','ReciboDinero-'.$reciboNo.'.pdf');
             $pdf->Output();
        }
    }

?>
