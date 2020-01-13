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
        require_once "../php/responderJSON.php";
        require_once ("../php/usuario.class.php");
		require_once ("../php/query.class.php");
        $usuario 	= new usuario($idUsuario,$mysqli);
		$query		= new Query();
        $permiso 	= $usuario->permiso("listarNominas",$mysqli);
		$response 	= array(
			"status"        => 0
		);
		if (!$permiso)
		{
			$response['mensaje'] = "No se pudo mostrar este registro. Usuario con permisos insuficientes para realizar esta acción";
			responder($response, $mysqli);
		}

		$idPeriodo = $_POST['idCliente'];

		if(is_numeric($idPeriodo) == FALSE && $idPeriodo <= 0)
		{
			$response['mensaje'] = "El formato del id del registro de la n&oacute;mina no es el correcto";
			$response['status'] = 0;
			responder($response, $mysqli);
		}
		$resPeriodo = $query->table('cat_periodos_nominas')->select('*')->where("id", "=", $idPeriodo, "i")->and()
							->where("activo", "=", 1, "i")->execute();
		if (!$query->num_rows())
		{
			$response['mensaje'] = "No existe informaci&oacute;n para este periodo. Posiblemente ya ha sido eliminado o cancelado.";
			responder($response, $mysqli);
		}
		$resNomina = $query	->table("cat_nominas AS cn")->select( "CONCAT(cu.nombres, ' ', cu.apellidop, ' ', cu.apellidom) AS nombreUsuario, cn.id AS idNomina")
							->innerJoin("cat_usuarios AS cu", "cn.idUsuario", "=", "cu.id")
							->where("cn.idPeriodo", "=", $resPeriodo[0]['id'], "i")->and()->where("cn.activo", "=", 1, "i")->execute();

        $response['htmlDetalle']        = "";
		$globalPercep = 0;
		$globalDeducc = 0;
		foreach ($resNomina as $nomina)
		{
			/**
			 * Obtener el monto total de percepciones y deducciones
			 *
			 */
			$resDetallePercep = $query	->table("detalle_nomina")->select("cantidad, monto, tipo")->where("idNomina", "=", $nomina['idNomina'], "i")->and()
										->where("activo", "=", 1, "i")->execute();
			$totalPercep = 0;
			$totalDeducc = 0;
			foreach ($resDetallePercep as $percep)
			{
				if ($percep['tipo'] == 1)
					$totalPercep += $percep['cantidad'] * $percep['monto'];
				else
					$totalDeducc += $percep['cantidad'] * $percep['monto'];
			}
			$globalPercep += $totalPercep;
			$globalDeducc += $totalDeducc;
			$response['htmlDetalle']    .= "<tr>
												<td>".str_pad($nomina['idNomina'], 9, "0", STR_PAD_LEFT)."</td>
												<td>".$nomina['nombreUsuario']."</td>
												<td class='text-right'>$".number_format($totalPercep,2,".",",")."</td>
												<td class='text-right'>$".number_format($totalDeducc,2,".",",")."</td>
												<td class='text-right'>$".number_format($totalPercep - $totalDeducc,2,".",",")."</td>
												<td>
													<a class='orange pointer' target='_blank' href='assets/pdf/comprobanteNomina.php?idNomina=".$nomina['idNomina']."' data-rel='tooltip' title='Imprimir nómina'>
														<i class='ace-icon fa fa-file-pdf-o bigger-130'></i>
													</a>
												</td>
											</tr>";
		}
		$response['htmlDetalle'] .= "<tr>
											<td colspan = '2' class='text-right'><b>TOTAL PERIODO:</b></td>
											<td class='text-right'><b>$".number_format($globalPercep,2,".",",")."</b></td>
											<td class='text-right'><b>$".number_format($globalDeducc,2,".",",")."</b></td>
											<td class='text-right'><b>$".number_format($globalPercep - $globalDeducc,2,".",",")."</b></td>
											<td></td>
										</tr>";
		// var_dump($response['htmlDetalle']);die;
        $response['htmlDetalle_hist']   = "";
        // $total = 0;
        // while ($row_prod = $res_prod->fetch_assoc())
        // {
        //     $activo             = ($row_prod['activo']) ? TRUE : FALSE;
        //     $fechaReg           = date_create($row_prod['fechaCreacion']);
        //     $fechaReg           = date_format($fechaReg, 'd/m/Y h:i:s p');
        //     $idProducto         = $row_prod['idProducto'];
        //     $cantidad           = $row_prod['cantidad'];
        //     $precio             = $row_prod['precio'];
        //     $subTotal           = $cantidad * $precio;
        //     $nombre             = $row_prod['nombreProducto'];
        //     $nombreUsuario      = $row_prod['nombreUsuario']." ".$row_prod['apellidopUsuario'];
		//
        //     $tipo               = ' <span class="label label-info label-white">
        //                                 <i class="ace-icon fa fa-tag bigger-120"></i>
        //                                 Producto
        //                             </span>';
		//
        //     if ($activo)
        //     {
        //         $response['htmlDetalle']    .= "<tr>
        //                                             <td>$idProducto</td>
        //                                             <td>$nombre</td>
        //                                             <td class='text-center'>$tipo</td>
        //                                             <td class='text-right'>$".number_format($precio,2,".",",")."</td>
        //                                             <td class='text-right'>$cantidad</td>
        //                                             <td class='text-right'>$".number_format($subTotal,2,".",",")."</td>
        //                                         </tr>";
        //     }
        //     else
        //     {
        //         $response['htmlDetalle_hist'] .= "<tr>
        //                                             <td>$idProducto</td>
        //                                             <td>$nombre</td>
        //                                             <td class='text-center'>$tipo</td>
        //                                             <td class='text-right'>$".number_format($precio,2,".",",")."</td>
        //                                             <td class='text-right'>$cantidad</td>
        //                                             <td class='text-right'>$".number_format($subTotal,2,".",",")."</td>
        //                                             <td class='text-center'>$fechaReg</td>
        //                                             <td>$nombreUsuario</td>
        //                                         </tr>";
        //     }
        // }
        // while ($row_serv = $res_serv->fetch_assoc())
        // {
        //     $activo             = ($row_serv['activo']) ? TRUE : FALSE;
        //     $fechaReg           = date_create($row_serv['fechaCreacion']);
        //     $fechaReg           = date_format($fechaReg, 'd/m/Y h:i:s p');
        //     $idProducto         = $row_serv['idProducto'];
        //     $cantidad           = $row_serv['cantidad'];
        //     $precio             = $row_serv['precio'];
        //     $subTotal           = $cantidad * $precio;
        //     $nombre             = $row_serv['nombreProducto'];
        //     $nombreUsuario      = $row_serv['nombreUsuario']." ".$row_serv['apellidopUsuario'];
		//
        //     $tipo               = ' <span class="label label-purple label-white">
		// 								<i class="ace-icon fa fa-cube bigger-120"></i>
		// 								Servicio
		// 							</span>';
		//
        //     if ($activo)
        //     {
        //         $response['htmlDetalle']    .= "<tr>
        //                                             <td>$idProducto</td>
        //                                             <td>$nombre</td>
        //                                             <td class='text-center'>$tipo</td>
        //                                             <td class='text-right'>$".number_format($precio,2,".",",")."</td>
        //                                             <td class='text-right'>$cantidad</td>
        //                                             <td class='text-right'>$".number_format($subTotal,2,".",",")."</td>
        //                                         </tr>";
        //     }
        //     else
        //     {
        //         $response['htmlDetalle_hist'] .= "<tr>
        //                                             <td>$idProducto</td>
        //                                             <td>$nombre</td>
        //                                             <td class='text-center'>$tipo</td>
        //                                             <td class='text-right'>$".number_format($precio,2,".",",")."</td>
        //                                             <td class='text-right'>$cantidad</td>
        //                                             <td class='text-right'>$".number_format($subTotal,2,".",",")."</td>
        //                                             <td class='text-center'>$fechaReg</td>
        //                                             <td>$nombreUsuario</td>
        //                                         </tr>";
        //     }
        // }
        $response['status']                 = 1;
        // $response['id']                 = $row_venta['id'];
        responder($response, $mysqli);
    }
?>
