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
		if (isset($_GET['idNomina']))
			$idNomina 			= $_GET['idNomina'];
		else
			die;
		if (!$idNomina 			= validarFormulario('i',$idNomina,0))
		{
			echo "El formato del Id de la nómina no es el esperado.";
			die;
		}
		else
		{
			require_once ('../php/query.class.php');
			require_once ('../php/usuario.class.php');
			$query 				= new Query();
			$resPeriodo 		= $query->table("cat_nominas AS cn")
										->select( " cn.idUsuario, cn.activo, cpn.idSucursal, cpn.fechaInicio, cpn.fechaFin, cpn.fechaCreacion,
													cd.nombre AS departamento, tusr.nombre AS puestoUsuario,
													CONCAT(usr.nombres, ' ', usr.apellidop, ' ', usr.apellidom) AS usuarioCreo")
										->innerJoin('cat_periodos_nominas AS cpn', "cn.idPeriodo", "=", 'cpn.id')
										->innerJoin('cat_usuarios AS usr', 'cpn.idUsuarioCreo', '=', 'usr.id')
										->innerJoin("cat_departamentos AS cd", "usr.departamento", "=", "cd.id")
										->leftJoin("tipos_usuarios AS tusr", "usr.tipo", "=", "tusr.id")
										->where("cn.id", "=", $idNomina, "i")->limit()->execute(FALSE, OBJ);
			/**
			* Info de la sucursal
			*/
			$Sucursal			= $query->table("cat_sucursales")->select()->where('id', '=', $resPeriodo->idSucursal, 'i')->limit()->execute(FALSE, OBJ);
			// var_dump($resPeriodo);
			// die;
			$margen				= 0;
			$Usuario 			= new usuario($resPeriodo->idUsuario, $mysqli);
			$pdf				= new PDF_Code128('P','mm','Letter');
			$pdf->AliasNbPages();
			$pdf->AddPage();
			$pdf->SetMargins(8,20);
			$pdf->Image('../images/avatars/sucursales/'.$resPeriodo->idSucursal.'/logo.jpg',11,5,-780);
			$pdf->SetFont('times','',10);
			$code				= str_pad($idNomina, 10, "0", STR_PAD_LEFT);
			$pdf->Code128(167,8," ".$code,38,6);
			$pdf->SetXY(172,12);
			$pdf->SetFillColor(128, 0, 128);
			$pdf->Cell(30,7,"No.- ".$code,$margen,1,'C');
			$pdf->SetXY(37,4);
			$pdf->Cell(168,3,utf8_decode(''),$margen,1,'C',true);
			$pdf->Cell(200,1,utf8_decode(""),$margen,1,'L');

			$pdf->SetFont('times','B',10);
			$pdf->Cell(80,4,utf8_decode("COMPROBANTE DE PAGO"),$margen,0,'R');
			$pdf->Cell(80,4,utf8_decode($Sucursal->nombre),$margen,1,'C');

			$pdf->SetFont('times','I',8);
			$pdf->Cell(80,4,utf8_decode(""),$margen,0,'C');
			$pdf->Cell(80,2,utf8_decode('"'.$Sucursal->lema.'"'),$margen,1,'C');

			/**
			 * Información de cabecera
			 */
			$pdf->SetFillColor(246);
 			$pdf->SetDrawColor(255);
 			$pdf->SetLineWidth(0.6);
			$pdf->Cell(200,3,utf8_decode(""),$margen,1,'C');

			$pdf->SetFont('Courier','B',8);
 			$pdf->Cell(16,4,utf8_decode("NOMINA:"),$margen,0,'L');
 			$pdf->SetFont('Courier','',7.5);
 			$pdf->Cell(20,4,utf8_decode($code),1,0,'C',true);

 			$pdf->SetFont('Courier','B',8);
 			$pdf->Cell(14,4,utf8_decode("NOMBRE:"),$margen,0,'L');
 			$pdf->SetFont('Courier','',7.5);
 			$pdf->Cell(82,4,utf8_decode($Usuario->nombres),1,0,'C',true);
 			$pdf->SetFont('Courier','B',8);
 			$pdf->Cell(15,4,utf8_decode('PERIODO:'),$margen,0,'L');
 			$pdf->SetFont('Courier','',7.5);
 			$pdf->Cell(50,4,utf8_decode(date_format(date_create($resPeriodo->fechaInicio), 'd/m/Y')." - ".date_format(date_create($resPeriodo->fechaFin), 'd/m/Y')),1,1,'C',true);
 			$pdf->SetFont('Courier','B',8);
 			$pdf->Cell(24,4,utf8_decode('FH. CREACIÓN:'),$margen,0,'L');
 			$pdf->SetFont('Courier','',7.5);
 			$pdf->Cell(38,4,utf8_decode(date_format(date_create($resPeriodo->fechaCreacion),'d/m/Y H:i:s')),1,0,'C',true);
			$pdf->SetFont('Courier','B',8);
 			$pdf->Cell(24,4,utf8_decode("DEPARTAMENTO:"),$margen,0,'L');
 			$pdf->SetFont('Courier','',7.5);
 			$pdf->Cell(30,4,utf8_decode($resPeriodo->departamento),1,0,'C',true);
			$pdf->SetFont('Courier','B',8);
 			$pdf->Cell(15,4,utf8_decode("PUESTO:"),$margen,0,'L');
 			$pdf->SetFont('Courier','',7.5);
 			$pdf->Cell(66,4,utf8_decode($resPeriodo->puestoUsuario),1,1,'C',true);

			$pdf->Cell(100,1,"",0,1,'L',0);
			$pdf->SetFillColor(99, 99, 99);
			$pdf->SetTextColor(255);
			$pdf->SetFont('Courier','B',8);
			$pdf->Cell(70,4,utf8_decode('CONCEPTO'),1,0,'C',TRUE);
			$pdf->Cell(29,4,utf8_decode('$ IMPORTE'),1,0,'C',TRUE);
			$pdf->Cell(70,4,utf8_decode('CONCEPTO'),1,0,'C',TRUE);
			$pdf->Cell(29,4,utf8_decode('$ IMPORTE'),1,1,'C',TRUE);
			//$pdf->SetFillColor(255);
			$pdf->SetFont('times','',7);
			$pdf->SetTextColor(0);
			$pdf->SetFillColor(230);
			$pdf->SetDrawColor(0);

			/**
			 * Detalle de nómina
			 */
			$Nominas = $query->table("detalle_nomina AS dn")->select("dn.nombreConcepto AS concepto, dn.monto, dn.tipo")
							->where("dn.idNomina","=", $idNomina, "i")->and()
							->where("dn.idUsuario","=", $Usuario->id, "i")->and()
							->where("dn.activo", "=", 1, "i")->orderBy("dn.tipo", "ASC")->execute(FALSE, OBJ);
			// var_dump($Nomina);die;
			$stripper 	= 0;
			$saltoLinea = 0;
			$contLinea 	= 1;
			$contStr 	= 0;
			foreach ($Nominas as $nomina)
			{
				$stripper 	= $contStr%4 == 0 ? 1 : 0;
				$saltoLinea = $contLinea%2 == 0 ? 1 : 0;
				$pdf->Cell(10,3.5,'',$margen,0,'C',$stripper);
				$pdf->Cell(60,3.5,utf8_decode($nomina->concepto),$margen,0,'L',$stripper);
				$pdf->Cell(29,3.5,utf8_decode($nomina->tipo == 1 ? $nomina->monto : '-'.$nomina->monto),$margen,TRUE,'R', $stripper);
				$contLinea++;
				$contStr+=2;
			}
			$pdf->Output();
die;


                        $pdf->Cell(100,1.8,"",0,1,'L',0);
                        $pdf->SetFillColor(99, 99, 99);
                        $pdf->SetTextColor(255);
                        $pdf->SetFont('Courier','B',11);
                        $pdf->Cell(25,5.5,utf8_decode('ID ART.'),1,0,'C',TRUE);
                        $pdf->Cell(87,5.5,utf8_decode('NOMBRE DEL ARTÍCULO'),1,0,'C',TRUE);
                        $pdf->Cell(30,5.5,utf8_decode('$ UNITARIO'),1,0,'C',TRUE);
                        $pdf->Cell(15,5.5,utf8_decode('CANT.'),1,0,'C',TRUE);
                        $pdf->Cell(40,5.5,utf8_decode('SUB TOTAL'),1,1,'C',TRUE);
                        //$pdf->SetFillColor(255);
                        $pdf->SetTextColor(0);
                        $pdf->SetFillColor(0);
                        $pdf->SetDrawColor(0);
                        $sql = "SELECT
                                    detalle_ventas.idServicio AS idServicio,
                                    detalle_ventas.idProducto AS idProducto,
                                    detalle_ventas.precioVenta AS precioVenta,
                                    detalle_ventas.cantidad AS cantidad,
                                    cat_productos.nombre AS nombreProducto
                                FROM detalle_ventas
                                INNER JOIN cat_productos
                                ON detalle_ventas.idProducto = cat_productos.id
                                WHERE detalle_ventas.idVenta = ? AND detalle_ventas.activo = 1";
                        $totalVenta = 0;
                        if ($prepare_det = $mysqli->prepare($sql))
                        {
                            if ($prepare_det->bind_param('i', $idNomina) && $prepare_det->execute())
                            {
                                $res_det = $prepare_det->get_result();
                                $totalArts = $res_det->num_rows;
                                $pdf->SetFont('Times','',11.5);
                                while ($row_det = $res_det->fetch_assoc())
                                {
                                    $esteId = str_pad($row_det['idProducto'], 7, "0", STR_PAD_LEFT);
                                    $esteNombreProducto = $row_det['nombreProducto'];
                                    $estePrecio = $row_det['precioVenta'];
                                    $esteCantidad = $row_det['cantidad'];
                                    $totalVenta += $esteSubTotal = $estePrecio * $esteCantidad;
                                    $pdf->Cell(25,5.5,utf8_decode($esteId),$margen,0,'R',0);
                                    $pdf->Cell(87,5.5,utf8_decode($esteNombreProducto),$margen,0,'L',0);
                                    $pdf->Cell(30,5.5,utf8_decode("$".number_format($estePrecio,2,".",",")),$margen,0,'R',0);
                                    $pdf->Cell(15,5.5,utf8_decode($esteCantidad),$margen,0,'R',0);
                                    $pdf->Cell(40,5.5,utf8_decode("$".number_format($esteSubTotal,2,".",",")),$margen,1,'R',0);

                                }
                            }
                            else
                            {
                                echo "No se puede generar la consulta. Error al enlazar y/o ejecutar los parámetros del detalle de la venta.";
                                die;
                            }
                        }
                        $sql = "SELECT
                                    detalle_ventas.idServicio AS idServicio,
                                    detalle_ventas.idProducto AS idProducto,
                                    detalle_ventas.precioVenta AS precioVenta,
                                    detalle_ventas.cantidad AS cantidad,
                                    cat_servicios.nombre AS nombreProducto
                                FROM detalle_ventas
                                INNER JOIN cat_servicios
                                ON detalle_ventas.idServicio = cat_servicios.id
                                WHERE detalle_ventas.idVenta = ? AND detalle_ventas.activo = 1";
                        if ($prepare_det = $mysqli->prepare($sql))
                        {
                            if ($prepare_det->bind_param('i', $idNomina) && $prepare_det->execute())
                            {
                                $res_det = $prepare_det->get_result();
                                $totalArts = $res_det->num_rows;
                                $pdf->SetFont('Times','',11.5);
                                while ($row_det = $res_det->fetch_assoc())
                                {
                                    $esteId = str_pad($row_det['idServicio'], 7, "0", STR_PAD_LEFT);
                                    $esteNombreProducto = $row_det['nombreProducto'];
                                    $estePrecio = $row_det['precioVenta'];
                                    $esteCantidad = $row_det['cantidad'];
                                    $totalVenta += $esteSubTotal = $estePrecio * $esteCantidad;
                                    $pdf->Cell(25,5.5,utf8_decode($esteId),$margen,0,'R',0);
                                    $pdf->Cell(87,5.5,utf8_decode($esteNombreProducto),$margen,0,'L',0);
                                    $pdf->Cell(30,5.5,utf8_decode("$".number_format($estePrecio,2,".",",")),$margen,0,'R',0);
                                    $pdf->Cell(15,5.5,utf8_decode($esteCantidad),$margen,0,'R',0);
                                    $pdf->Cell(40,5.5,utf8_decode("$".number_format($esteSubTotal,2,".",",")),$margen,1,'R',0);

                                }
                            }
                            else
                            {
                                echo "No se puede generar la consulta. Error al enlazar y/o ejecutar los parámetros del detalle de la venta.";
                                die;
                            }
                        }
                        $pdf->SetDrawColor(0);
                        $pdf->SetLineWidth(0.2);

                        $pdf->Cell(197,2,"",0,1,'L');
                        $pdf->Cell(197,0,"",1,1,'L');
                        //$totalVenta     = $rowVenta['totalventa'];
                        //$totalVenta_f   = number_format($totalVenta, 2);
                        $pdf->SetFont('Times','',10);
                        $pdf->Cell(59,6,utf8_decode('TOTAL NO. ARTÍCULOS.:'),0,0,'R');
                        $pdf->SetFont('Times','B',10);
                        $pdf->Cell(10,6,utf8_decode($totalArts),0,0,'L');
                        $pdf->Cell(57,6,utf8_decode(''),0,0,'R');
                        $pdf->SetFont('Times','',10);
                        $pdf->Cell(33,6,utf8_decode('TOTAL:'),1,0,'R',0);
                        $pdf->SetFont('Times','B',12);
                        $pdf->Cell(38,6,utf8_decode('$ '.number_format($totalVenta,2,".",",")),1,1,'R',0);
                        $pdf->Output();

            }
		}
            // else
            // {
            //     echo "No se puede generar la consulta. Error en la preparación de los parámetros de la venta.";
            //     die;
            // }


?>
