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
		require_once ("../php/usuario.class.php");
        $usuario = new usuario($idUsuario,$mysqli);
		$permiso = $usuario->permiso("listarNominas",$mysqli);
        if (!$permiso)
        {
			echo "Usuario con permisos insuficientes para realizar esta acción.";
			die;
        }

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
										->leftJoin('cat_periodos_nominas AS cpn', "cn.idPeriodo", "=", 'cpn.id')
										->leftJoin('cat_usuarios AS usr', 'cpn.idUsuarioCreo', '=', 'usr.id')
										->leftJoin("cat_departamentos AS cd", "usr.departamento", "=", "cd.id")
										->leftJoin("tipos_usuarios AS tusr", "usr.tipo", "=", "tusr.id")
										->where("cn.id", "=", $idNomina, "i")->and()->where("cn.activo", "=", 1, "i")->limit()->execute(FALSE, OBJ);
			// echo $query->lastStatement();
			// die;
			if ($query->num_rows() == 0)
			{
				echo "No existe el periodo o la n&oacute;mina seleccionada.";
				die;
			}

			/**
			* Info de la sucursal
			*/
			$Sucursal			= $query->table("cat_sucursales")->select()->where('id', '=', $resPeriodo->idSucursal, 'i')->limit()->execute(FALSE, OBJ);
			if ($query->num_rows() == 0)
			{
				echo "El id de la sucursal no es el correcto.";
				die;
			}
			$margen				= 0;
			$pos_Y				= 7;
			$copia				= TRUE;
			$Usuario 			= new usuario($resPeriodo->idUsuario, $mysqli);
			$pdf				= new PDF_Code128('P','mm','Letter');
			$pdf->AliasNbPages();
			$pdf->AddPage();
			$pdf->SetMargins(8,16);
			/**
			 * ORIGINAL
			 */
			$pdf->Image('../images/avatars/sucursales/'.$resPeriodo->idSucursal.'/logo.jpg',11,$pos_Y,-780);
			$pdf->SetFont('times','',7);
			$code				= str_pad($idNomina, 10, "0", STR_PAD_LEFT);
			$pos_Y				+=2.5;
			$pdf->Code128(167,$pos_Y," ".$code,38,5);
			$pos_Y				+=4.5;
			$pdf->SetXY(172,$pos_Y);
			$pdf->SetFillColor(128, 0, 128);
			$pdf->SetFont('times','',6.3);
			$pdf->Cell(30,4,"No.- ".$code,$margen,1,'C');
			$pos_Y				-=8;
			$pdf->SetXY(37,$pos_Y);
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
			$pdf->Cell(200,1,utf8_decode(""),$margen,1,'C');

			$pdf->SetFont('Courier','B',7);
 			$pdf->Cell(16,3.5,utf8_decode("NOMINA:"),$margen,0,'L');
 			$pdf->SetFont('Courier','',6.5);
 			$pdf->Cell(20,3.5,utf8_decode($code),1,0,'C',true);

 			$pdf->SetFont('Courier','B',7);
 			$pdf->Cell(14,3.5,utf8_decode("NOMBRE:"),$margen,0,'L');
 			$pdf->SetFont('Courier','',6.5);
 			$pdf->Cell(82,3.5,utf8_decode($Usuario->nombres),1,0,'C',true);
 			$pdf->SetFont('Courier','B',7);
 			$pdf->Cell(15,3.5,utf8_decode('PERIODO:'),$margen,0,'L');
 			$pdf->SetFont('Courier','',6.5);
 			$pdf->Cell(50,3.5,utf8_decode(date_format(date_create($resPeriodo->fechaInicio), 'd/m/Y')." - ".date_format(date_create($resPeriodo->fechaFin), 'd/m/Y')),1,1,'C',true);
 			$pdf->SetFont('Courier','B',7);
 			$pdf->Cell(24,3.5,utf8_decode('FH. CREACIÓN:'),$margen,0,'L');
 			$pdf->SetFont('Courier','',6.5);
 			$pdf->Cell(38,3.5,utf8_decode(date_format(date_create($resPeriodo->fechaCreacion),'d/m/Y H:i:s')),1,0,'C',true);
			$pdf->SetFont('Courier','B',7);
 			$pdf->Cell(24,3.5,utf8_decode("DEPARTAMENTO:"),$margen,0,'L');
 			$pdf->SetFont('Courier','',6.5);
 			$pdf->Cell(30,3.5,utf8_decode($Usuario->nombreDepartamento),1,0,'C',true);
			$pdf->SetFont('Courier','B',7);
 			$pdf->Cell(15,3.5,utf8_decode("PUESTO:"),$margen,0,'L');
 			$pdf->SetFont('Courier','',6.5);
 			$pdf->Cell(66,3.5,utf8_decode($Usuario->tipoUsuario(FALSE)),1,1,'C',true);

			$pdf->Cell(100,3,"",0,1,'L',0);
			$pdf->SetFillColor(99, 99, 99);
			$pdf->SetTextColor(255);
			$pdf->SetFont('Courier','B',7);
			$pdf->Cell(70,3.5,utf8_decode('CONCEPTO'),1,0,'C',TRUE);
			$pdf->Cell(29,3.5,utf8_decode('$ IMPORTE'),1,0,'C',TRUE);
			$pdf->Cell(70,3.5,utf8_decode('CONCEPTO'),1,0,'C',TRUE);
			$pdf->Cell(29,3.5,utf8_decode('$ IMPORTE'),1,1,'C',TRUE);
			//$pdf->SetFillColor(255);
			$pdf->SetFont('times','',6.8);
			$pdf->SetTextColor(0);
			$pdf->SetFillColor(240);
			$pdf->SetDrawColor(0);

			/**
			 * Detalle de nómina
			 */
			$Nominas = $query->table("detalle_nomina AS dn")->select("dn.nombreConcepto AS nombreConcepto, dn.monto, dn.tipo, dn.idConcepto")
							->where("dn.idNomina","=", $idNomina, "i")->and()
							->where("dn.idUsuario","=", $Usuario->id, "i")->and()
							->where("dn.activo", "=", 1, "i")->orderBy("dn.tipo", "ASC")->execute(FALSE, RETURN_OBJECT);

			/**
			 * Juntar en un solo concepto la cobranza
			 */
			$percepciones_cobranza = 0;
			foreach ($Nominas as $nomina)
			{
				if ($nomina->idConcepto == 2) //Cobranza
				{
					$percepciones_cobranza += $nomina->monto;
				}
			}

			/**
			* Primer línea caja nómina
			*/
			$pdf->Cell(200,2.4,'',$margen,1,'C',0);

			/**
			 * Imprimir concepto de cobranza, si existe
			 */
			if ($percepciones_cobranza > 0)
			{
				$pdf->Cell(10,3.4,'',$margen,0,'C',0);
				$pdf->Cell(60,3.4,utf8_decode("Comisión por cobranza"),$margen,0,'L',0);
				$pdf->Cell(29,3.4,utf8_decode(number_format($percepciones_cobranza,2,".",",")),$margen,0,'R', 0);
			}

			$stripper 	= $percepciones_cobranza ? 0 : 1;
			$saltoLinea = $percepciones_cobranza ? 1 : 0;
			$contLinea 	= $percepciones_cobranza ? 2 : 1;
			$percepciones_ventas = 0;
			$deducciones = 0;
			foreach ($Nominas as $nomina)
			{
				if ($nomina->idConcepto == 2) //Cobranza
				{
					continue;
				}
				if ($contLinea%2)
					$saltoLinea = 0;
				else
				{
					$saltoLinea = 1;
					$stripper 	= $stripper ? 0 : 1;
				}
				$pdf->Cell(10,3.4,'',$margen,0,'C',$stripper);
				$pdf->Cell(60,3.4,utf8_decode($nomina->nombreConcepto),$margen,0,'L',$stripper);
				$pdf->Cell(29,3.4,utf8_decode($nomina->tipo == 1 ? $nomina->monto : '-'.$nomina->monto),$margen,$saltoLinea,'R', $stripper);

				if ($nomina->tipo == 1)
					$percepciones_ventas += $nomina->monto;
				else
					$deducciones += $nomina->monto;
				// $percepciones_ventas += $nomina->tipo ? $nomina->monto : 0;
				$contLinea++;
			}
			$percepciones = $percepciones_ventas + $percepciones_cobranza;
			$neto = $percepciones - $deducciones;

			// Marco conceptos nómina y totales
			$pdf->SetXY(8,$pos_Y);
			$pdf->SetLineWidth(0.2);
			$pdf->SetDrawColor(0);
			$pdf->Cell(198,21,utf8_decode(''),1,1,'C',FALSE);
			$pdf->Cell(198,3.5,utf8_decode(''),0,1,'C',FALSE);
			$pdf->Cell(99,85,utf8_decode(''),1,0,'C',FALSE);
			$pdf->Cell(99,85,utf8_decode(''),1,1,'C',FALSE);

			// Totales
			$pdf->SetFont('times','I',7);
			$pdf->Cell(99,3,utf8_decode('Recibí a mi entera conformidad de la Empresa arriba mencionada'),0,0,'L',FALSE);
			$pdf->SetFont('times','B',7);
			$pdf->Cell(45,3,utf8_decode('FIRMA'),0,0,'L',FALSE);
			$pdf->Cell(54,3,utf8_decode('TOTALES'),1,1,'C',FALSE);

			// Percepciones
			$pdf->SetFont('times','',7);
			$pdf->Cell(144,3,utf8_decode('la cantidad indicada en el renglón " NETO A PAGAR"'),0,0,'L',FALSE);
			$pdf->Cell(27,3,utf8_decode('PERCEPCIONES'),1,0,'L',FALSE);
			$pdf->Cell(27,3,utf8_decode('$'.number_format($percepciones,2,".",",")),1,1,'R',FALSE);

			// Deducciones
			$pdf->Cell(144,3,utf8_decode(''),0,0,'C',FALSE);

			$pdf->Cell(27,3,utf8_decode('DEDUCCIONES'),1,0,'L',FALSE);
			$pdf->Cell(27,3,utf8_decode('$'.number_format($deducciones,2,".",",")),1,1,'R',FALSE);

			// Neto
			$pdf->SetFont('times','BI',8.5);
			$pdf->Cell(70,3,utf8_decode('- ORIGINAL -'),0,0,'C',FALSE);
			$pdf->SetFont('times','B',7);
			$pdf->Cell(74,3,utf8_decode('_______________________________________________'),0,0,'C',FALSE);
			$pdf->Cell(27,3,utf8_decode('NETO A PAGAR'),1,0,'L',FALSE);
			$pdf->Cell(27,3,utf8_decode('$'.number_format($neto,2,".",",")),1,1,'R',FALSE);

			/**
			 * GENERAR COPIA
			 */
			if ($copia)
			{
				$pos_Y				+= 132;
				$pdf->Image('../images/avatars/sucursales/'.$resPeriodo->idSucursal.'/logo.jpg',11,$pos_Y,-780);
				$pdf->SetFont('times','',7);
				$code				= str_pad($idNomina, 10, "0", STR_PAD_LEFT);
				$pos_Y				+=2.5;
				$pdf->SetFillColor(0);
				$pdf->Code128(167,$pos_Y," ".$code,38,5);
				$pos_Y				+=4.5;
				$pdf->SetXY(172,$pos_Y);
				$pdf->SetDrawColor(0);
				$pdf->SetLineWidth(0.1);
				$pdf->SetFillColor(128, 0, 128);
				$pdf->SetFont('times','',6.3);
				$pdf->Cell(30,4,"No.- ".$code,$margen,1,'C');
				$pos_Y				-=8;
				$pdf->SetXY(37,$pos_Y);
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
				$pdf->Cell(200,1,utf8_decode(""),$margen,1,'C');

				$pdf->SetFont('Courier','B',7);
	 			$pdf->Cell(16,3.5,utf8_decode("NOMINA:"),$margen,0,'L');
	 			$pdf->SetFont('Courier','',6.5);
	 			$pdf->Cell(20,3.5,utf8_decode($code),1,0,'C',true);

	 			$pdf->SetFont('Courier','B',7);
	 			$pdf->Cell(14,3.5,utf8_decode("NOMBRE:"),$margen,0,'L');
	 			$pdf->SetFont('Courier','',6.5);
	 			$pdf->Cell(82,3.5,utf8_decode($Usuario->nombres),1,0,'C',true);
	 			$pdf->SetFont('Courier','B',7);
	 			$pdf->Cell(15,3.5,utf8_decode('PERIODO:'),$margen,0,'L');
	 			$pdf->SetFont('Courier','',6.5);
	 			$pdf->Cell(50,3.5,utf8_decode(date_format(date_create($resPeriodo->fechaInicio), 'd/m/Y')." - ".date_format(date_create($resPeriodo->fechaFin), 'd/m/Y')),1,1,'C',true);
	 			$pdf->SetFont('Courier','B',7);
	 			$pdf->Cell(24,3.5,utf8_decode('FH. CREACIÓN:'),$margen,0,'L');
	 			$pdf->SetFont('Courier','',6.5);
	 			$pdf->Cell(38,3.5,utf8_decode(date_format(date_create($resPeriodo->fechaCreacion),'d/m/Y H:i:s')),1,0,'C',true);
				$pdf->SetFont('Courier','B',7);
	 			$pdf->Cell(24,3.5,utf8_decode("DEPARTAMENTO:"),$margen,0,'L');
	 			$pdf->SetFont('Courier','',6.5);
	 			$pdf->Cell(30,3.5,utf8_decode($Usuario->nombreDepartamento),1,0,'C',true);
				$pdf->SetFont('Courier','B',7);
	 			$pdf->Cell(15,3.5,utf8_decode("PUESTO:"),$margen,0,'L');
	 			$pdf->SetFont('Courier','',6.5);
	 			$pdf->Cell(66,3.5,utf8_decode($Usuario->tipoUsuario(FALSE)),1,1,'C',true);

				$pdf->Cell(100,3,"",0,1,'L',0);
				$pdf->SetFillColor(99, 99, 99);
				$pdf->SetTextColor(255);
				$pdf->SetFont('Courier','B',7);
				$pdf->Cell(70,3.5,utf8_decode('CONCEPTO'),1,0,'C',TRUE);
				$pdf->Cell(29,3.5,utf8_decode('$ IMPORTE'),1,0,'C',TRUE);
				$pdf->Cell(70,3.5,utf8_decode('CONCEPTO'),1,0,'C',TRUE);
				$pdf->Cell(29,3.5,utf8_decode('$ IMPORTE'),1,1,'C',TRUE);
				//$pdf->SetFillColor(255);
				$pdf->SetFont('times','',6.8);
				$pdf->SetTextColor(0);
				$pdf->SetFillColor(240);
				$pdf->SetDrawColor(0);

				/**
				 * Detalle de nómina
				 */
				// $Nominas = $query->table("detalle_nomina AS dn")->select("dn.nombreConcepto AS nombreConcepto, dn.monto, dn.tipo, dn.idConcepto")
				// 				->where("dn.idNomina","=", $idNomina, "i")->and()
				// 				->where("dn.idUsuario","=", $Usuario->id, "i")->and()
				// 				->where("dn.activo", "=", 1, "i")->orderBy("dn.tipo", "ASC")->execute(FALSE, OBJ);
				//
				/**
				 * Juntar en un solo concepto la cobranza
				 */
				$percepciones_cobranza = 0;
				foreach ($Nominas as $nomina)
				{
					if ($nomina->idConcepto == 2) //Cobranza
					{
						$percepciones_cobranza += $nomina->monto;
					}
				}

				/**
				* Primer línea caja nómina
				*/
				$pdf->Cell(200,2.4,'',$margen,1,'C',0);

				/**
				 * Imprimir concepto de cobranza, si existe
				 */
				if ($percepciones_cobranza > 0)
				{
					$pdf->Cell(10,3.4,'',$margen,0,'C',0);
					$pdf->Cell(60,3.4,utf8_decode("Comisión por cobranza"),$margen,0,'L',0);
					$pdf->Cell(29,3.4,utf8_decode(number_format($percepciones_cobranza,2,".",",")),$margen,0,'R', 0);
				}

				$stripper 	= $percepciones_cobranza ? 0 : 1;
				$saltoLinea = $percepciones_cobranza ? 1 : 0;
				$contLinea 	= $percepciones_cobranza ? 2 : 1;
				$percepciones_ventas = 0;
				$deducciones = 0;
				foreach ($Nominas as $nomina)
				{
					if ($nomina->idConcepto == 2) //Cobranza
					{
						continue;
					}
					if ($contLinea%2)
						$saltoLinea = 0;
					else
					{
						$saltoLinea = 1;
						$stripper 	= $stripper ? 0 : 1;
					}
					$pdf->Cell(10,3.4,'',$margen,0,'C',$stripper);
					$pdf->Cell(60,3.4,utf8_decode($nomina->nombreConcepto),$margen,0,'L',$stripper);
					$pdf->Cell(29,3.4,utf8_decode($nomina->tipo == 1 ? $nomina->monto : '-'.$nomina->monto),$margen,$saltoLinea,'R', $stripper);

					if ($nomina->tipo == 1)
						$percepciones_ventas += $nomina->monto;
					else
						$deducciones += $nomina->monto;
					// $percepciones_ventas += $nomina->tipo ? $nomina->monto : 0;
					$contLinea++;
				}
				$percepciones = $percepciones_ventas + $percepciones_cobranza;
				$neto = $percepciones - $deducciones;

				// Marco conceptos nómina y totales
				$pdf->SetXY(8,$pos_Y);
				$pdf->SetLineWidth(0.2);
				$pdf->SetDrawColor(0);
				$pdf->Cell(198,21,utf8_decode(''),1,1,'C',FALSE);
				$pdf->Cell(198,3.5,utf8_decode(''),0,1,'C',FALSE);
				$pdf->Cell(99,85,utf8_decode(''),1,0,'C',FALSE);
				$pdf->Cell(99,85,utf8_decode(''),1,1,'C',FALSE);

				// Totales
				$pdf->SetFont('times','I',7);
				$pdf->Cell(99,3,utf8_decode('Recibí a mi entera conformidad de la Empresa arriba mencionada'),0,0,'L',FALSE);
				$pdf->SetFont('times','B',7);
				$pdf->Cell(45,3,utf8_decode('FIRMA'),0,0,'L',FALSE);
				$pdf->Cell(54,3,utf8_decode('TOTALES'),1,1,'C',FALSE);

				// Percepciones
				$pdf->SetFont('times','',7);
				$pdf->Cell(144,3,utf8_decode('la cantidad indicada en el renglón " NETO A PAGAR"'),0,0,'L',FALSE);
				$pdf->Cell(27,3,utf8_decode('PERCEPCIONES'),1,0,'L',FALSE);
				$pdf->Cell(27,3,utf8_decode('$'.number_format($percepciones,2,".",",")),1,1,'R',FALSE);

				// Deducciones
				$pdf->Cell(144,3,utf8_decode(''),0,0,'C',FALSE);

				$pdf->Cell(27,3,utf8_decode('DEDUCCIONES'),1,0,'L',FALSE);
				$pdf->Cell(27,3,utf8_decode('$'.number_format($deducciones,2,".",",")),1,1,'R',FALSE);

				// Neto
				$pdf->SetFont('times','BI',8.5);
				$pdf->Cell(70,3,utf8_decode('- COPIA -'),0,0,'C',FALSE);
				$pdf->SetFont('times','B',7);
				$pdf->Cell(74,3,utf8_decode('_______________________________________________'),0,0,'C',FALSE);
				$pdf->Cell(27,3,utf8_decode('NETO A PAGAR'),1,0,'L',FALSE);
				$pdf->Cell(27,3,utf8_decode('$'.number_format($neto,2,".",",")),1,1,'R',FALSE);
			}
			$pdf->Output();
		}
	}
