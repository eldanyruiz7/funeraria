<?php
	require_once ('assets/connect/bd.php');
	require_once ("assets/connect/sesion.class.php");
	$sesion = new sesion();
	require_once ("assets/connect/cerrarOtrasSesiones.php");
	require_once ("assets/connect/usuarioLogeado.php");
	if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
	{
		header("Location: salir.php");
	}
	else
	{
		// require_once ("assets/php/usuario.class.php");
		// $usuario = new usuario($idUsuario,$mysqli);
		// $permiso = $usuario->permiso("agregarPlan",$mysqli);
		// if (!$permiso)
		// {
		// 	header("Location: listarPlanes.php");
		// }
		$facturar 			= FALSE;
		require_once ("assets/php/venta.class.php");
		require_once ("assets/php/contrato.class.php");
		$tipoFactura 		= FALSE;
		if (isset($_GET['idVenta']))
		{
			$venta 			= new venta($_GET['idVenta'],$mysqli);
			if ( $venta->id != 0 && $venta->activo)
			{
				$facturar 	= TRUE;
				$tipoFactura = 'venta';
			}

		}
		elseif (isset($_GET['idContrato']))
		{
			$contrato 			= new contrato($_GET['idContrato'],$mysqli);
			if ($contrato->id != 0 && $contrato->activo)
			{
				$facturar 	= TRUE;
				$tipoFactura = 'contrato';
			}
		}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>Agregar factura</title>

		<meta name="description" content="Static &amp; Dynamic Tables" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="assets/font-awesome/4.5.0/css/font-awesome.min.css" />
		<!-- text fonts -->
		<link rel="stylesheet" href="assets/css/fonts.googleapis.com.css" />
		<link rel="stylesheet" href="assets/css/jquery.gritter.min.css" />
		<!-- ace styles -->
		<link rel="stylesheet" href="assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
		<!--[if lte IE 9]>
			<link rel="stylesheet" href="assets/css/ace-part2.min.css" class="ace-main-stylesheet" />
		<![endif]-->
		<link rel="stylesheet" href="assets/css/ace-skins.min.css" />
		<link rel="stylesheet" href="assets/css/ace-rtl.min.css" />
		<!--[if lte IE 9]>
		  <link rel="stylesheet" href="assets/css/ace-ie.min.css" />
		<![endif]-->
		<!-- inline styles related to this page -->
		<!-- ace settings handler -->
		<script src="assets/js/ace-extra.min.js"></script>

		<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

		<!--[if lte IE 8]>
		<script src="assets/js/html5shiv.min.js"></script>
		<script src="assets/js/respond.min.js"></script>
		<![endif]-->
		<style>
			.pointer
			{
				cursor: pointer;
			}
			.oculto
			{
				display: none;
			}
			.oculto2
			{
				display: none;
			}
		</style>
	</head>

	<body class="skin-1">
		<?php include "navbar.php"; ?>

		<div class="main-container ace-save-state" id="main-container">
			<script type="text/javascript">
				try{ace.settings.loadState('main-container')}catch(e){}
			</script>
			<?php include "sidebar.php"; ?>
			<div class="main-content">
				<div class="main-content-inner">
					<div class="breadcrumbs ace-save-state" id="breadcrumbs">
						<ul class="breadcrumb">
							<li>
								<i class="ace-icon fa fa-home home-icon"></i>
								<a href="index.php">Inicio</a>
							</li>
							<li class="active">Facturar</li>
						</ul><!-- /.breadcrumb -->
					</div>
					<div class="page-content">
						<div class="page-header">
							<h1>
								<i class="fa fa-bank" aria-hidden="true"></i> Facturar
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									<?php if ($facturar == TRUE)
									{
										if ($tipoFactura == 'venta')
										{
											echo "Agregar factura para la venta No. $venta->id";
										}
										elseif ($tipoFactura == 'contrato')
										{
											echo "Agregar factura para el contrato No. $contrato->id";
										}
									}
									?>
								</small>
							</h1>
						</div><!-- /.page-header -->
						<div class="space-6"></div>
						<div class="row">
					<?php
					if ($facturar)
					{
					?>
							<div class="col-sm-10 col-sm-offset-1">
								<div class="widget-box transparent">
									<div class="widget-header widget-header-large">
										<h3 class="widget-title grey lighter">
											<i class="ace-icon fa fa-bank green"></i>
											Información de la factura
										</h3>

										<div class="widget-toolbar no-border invoice-info">
										<?php if ($tipoFactura == 'venta'): ?>
											<span class="invoice-info-label">Venta:</span>
											<span class="red">#<?php echo $venta->id;?></span>
										<?php else: ?>
											<span class="invoice-info-label">Contrato:</span>
											<span class="red">#<?php echo $contrato->id;?></span>
										<?php endif; ?>
									<?php
									 ?>


											<br />
											<span class="invoice-info-label">Fecha:</span>
											<?php if ($tipoFactura == 'venta'): ?>
												<span class="blue"><?php echo  date_format(new DateTime($venta->fechaCreacion),"d-m-Y")?></span>
											<?php else: ?>
												<span class="blue"><?php echo  date_format(new DateTime($contrato->fechaCreacion),"d-m-Y")?></span>
											<?php endif; ?>
										</div>

										<div class="widget-toolbar no-padding">
											&nbsp;
										</div>
									</div>

									<div class="widget-body">
										<div class="widget-main">
											<div class="row">
												<div class="col-sm-6 no-padding-left">
													<div class="widget-box widget-color-blue">
														<div class="widget-header widget-header-flat widget-header-small">
															<h5 class="widget-title">Información del emisor</h5>
														</div>

														<div class="widget-body">
															<div class="widget-main padding-6 no-padding-left no-padding-right">
																<form class="form-horizontal" role="form" style="margin-top:10px;padding-right:20px">
																	<div class="form-group no-margin-bottom">
																		<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> RFC: </label>
																		<label class="col-sm-8 blue bigger control-label" style="text-align:left;"><?php echo ($tipoFactura == 'venta') ? $venta->rfcSucursal : $contrato->rfcSucursal;?> </label>
																	</div>
																	<div class="form-group no-margin-bottom">
																		<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre: </label>
																		<label class="col-sm-8 blue bigger control-label" style="text-align:left;"><?php echo ($tipoFactura == 'venta') ? $venta->representanteSucursal : $contrato->representanteSucursal;?> </label>
																	</div>
																	<div class="form-group no-margin-bottom">
																		<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Lugar de expedición: </label>
																		<label class="col-sm-8 blue bigger control-label" style="text-align:left;"><?php echo ($tipoFactura == 'venta') ? $venta->direccionSucursal : $contrato->domicilioSucursal;?> </label>
																	</div>
																	<div class="form-group no-margin-bottom">
																		<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> E-mail: </label>
																		<label class="col-sm-8 blue bigger control-label" style="text-align:left;"><?php echo ($tipoFactura == 'venta') ? $venta->emailSucursal : $contrato->correoSucursal;?> </label>
																	</div>
																	<div class="form-group no-margin-bottom">
																		<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Régimen fiscal: </label>
																		<label class="col-sm-8 blue bigger control-label" style="text-align:left;"><?php echo ($tipoFactura == 'venta') ? $venta->c_RegimenFiscal." - ".$venta->regimenFiscal : $contrato->c_RegimenFiscal." - ".$contrato->regimenFiscal;?> </label>
																	</div>
																</form>
															</div>
														</div>
													</div>

												</div><!-- /.col -->

												<div class="col-sm-6 no-padding-right">
													<div class="widget-box widget-color-green">
														<div class="widget-header widget-header-flat widget-header-small">
															<h5 class="widget-title">Información del receptopr</h5>
														</div>

														<div class="widget-body">
															<div class="widget-main padding-6 no-padding-left no-padding-right">
																<form class="form-horizontal" role="form" style="margin-top:10px;padding-right:20px">
																	<div class="form-group">
																		<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> RFC: </label>
																		<div class="col-sm-8">
																			<input autocomplete="off" value="" type="text" id="rfcReceptor" placeholder="RFC del receptor" class="col-xs-12 controlable">
																		</div>
																	</div>
																	<div class="form-group">
																		<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre/Razón Social: </label>
																		<div class="col-sm-8">
																			<input autocomplete="off" value="" type="text" id="razonReceptor" placeholder="Nombre o razón social del receptor" class="col-xs-12 controlable">
																		</div>
																	</div>
																	<div class="form-group">
																		<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Forma de pago: </label>
																		<div class="col-sm-8">
																			<select id="formaPago" class="col-xs-12 input-sm controlable">
																		<?php
																				$sql = "SELECT * FROM cat_formas_pago WHERE activo = 1";
																				$res_formas = $mysqli->query($sql);
																				while ($row_formas = $res_formas->fetch_assoc())
																				{
																					echo '<option value="'.$row_formas['id'].'">'.$row_formas['c_FormaPago'].' - '.$row_formas['nombre'].'</option>';
																				}

																		 ?>
																			</select>
																			<!-- <input type="text" id="form-field-1" placeholder="Username" class="col-xs-12"> -->
																		</div>
																	</div>
																	<div class="form-group">
																		<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Método de pago: </label>
																		<div class="col-sm-8">
																			<select id="metodoPago" class="col-xs-12 input-sm">
																				<option value="1">PUE - Pago en una sola exhibición</option>
																			</select>
																			<!-- <input type="text" id="form-field-1" placeholder="Username" class="col-xs-12"> -->
																		</div>
																	</div>
																	<div class="form-group">
																		<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Uso CFDI: </label>
																		<div class="col-sm-8">
																			<select id="usoCfdi" class="col-xs-12 input-sm controlable">
																		<?php
																				$sql = "SELECT * FROM cat_usos_cfdi WHERE activo = 1";
																				$res_usos = $mysqli->query($sql);
																				while ($row_usos = $res_usos->fetch_assoc())
																				{
																					echo '<option value="'.$row_usos['id'].'">'.$row_usos['c_UsoCFDI'].' - '.$row_usos['nombre'].'</option>';
																				}

																		 ?>
																			</select>
																			<!-- <input type="text" id="form-field-1" placeholder="Username" class="col-xs-12"> -->
																		</div>
																	</div>
																	<div class="form-group">
																		<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> E-mail: </label>
																		<div class="col-sm-8">
																			<input autocomplete="off" value="<?php echo ($tipoFactura == 'venta') ? $venta->emailCliente : $contrato->emailCliente;?>" type="text" id="emailReceptor" placeholder="email del receptor" class="col-xs-12 controlable">
																		</div>
																	</div>
																	<div class="form-group no-margin-bottom">
																		<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Enviar CFDI: </label>
																		<div class="col-sm-8">
																			<label style="padding-top:6px;">
																				<input checked name="switch-field-1" id="enviarCfdi" class="ace ace-switch ace-switch-6 controlable" type="checkbox">
																				<span class="lbl"></span>
																			</label>
																		</div>
																	</div>
																</form>
															</div>
														</div>
													</div>


												</div><!-- /.col -->
											</div><!-- /.row -->

											<div class="space"></div>

											<div>
												<table class="table table-striped table-bordered">
													<thead>
														<tr>
															<th>Clave Sat</th>
															<th>Unidad de medida</th>
															<th>Descripción</th>
															<th class="">Preico Unitario</th>
															<th class="">Cantidad</th>
															<th>Sub total</th>
														</tr>
													</thead>

													<tbody>
								<?php
								$totalElementos = 0;
								$totalMonto = 0;
								$sql_med			= "SELECT * FROM cat_unidades_venta WHERE activo = 1";
								$res_med			= $mysqli->query($sql_med);
								$selectMedidas		= "<select class='medidaSat input-sm text-center controlable'>";
								while ($row_med = $res_med->fetch_assoc())
								{
									$selectMedidas .= "<option value='".$row_med['id']."'>".$row_med['c_ClaveUnidad']." - ".$row_med['nombre']."</option>";
								}
								$selectMedidas		.= "</select>";

					                echo $tipoFactura == 'venta' ? "<tr class='trConceptos'>
																		<td class='center'><input type='text' name='inputClaveSat' autocomplete='on' class='claveSat text-center input-sm controlable' value='' ></td>
																		<td class='center'>$selectMedidas</td>
					                                                    <td><input type='text' name='inputNombreConcepto' style='width:260px' autocomplete='on' class='nombreConcepto text-center input-sm controlable' value='' ></td>
					                                                    <td class='text-right'><input type='number' id-servicio='0' name='inputPrecio' autocomplete='on' class='inputPrecio text-right input-sm controlable' value='".$venta->totalVenta."' ></td>
					                                                    <td class='text-right'>1</td>
					                                                    <td class='text-right'>$<span id='spanSubTotal'>".$venta->totalVenta."</span></td>
					                                                </tr>"
																 : "<tr class='trConceptos'>
 																		<td class='center'><input type='text' name='inputClaveSat' autocomplete='on' class='claveSat text-center input-sm controlable' value='' ></td>
 																		<td class='center'>$selectMedidas</td>
 					                                                    <td><input type='text' name='inputNombreConcepto' style='width:260px' autocomplete='on' class='nombreConcepto text-center input-sm controlable' value='' ></td>
 					                                                    <td class='text-right'><input type='number' id-servicio='0' name='inputPrecio' autocomplete='on' class='inputPrecio text-right input-sm controlable' value='".$contrato->precio."' ></td>
 					                                                    <td class='text-right'>1</td>
 					                                                    <td class='text-right'>$<span id='spanSubTotal'>".$contrato->precio."</span></td>
 					                                                </tr>" ;
								 ?>
													</tbody>
												</table>
											</div>

											<div class="hr hr8 hr-double hr-dotted"></div>

											<div class="row">
												<div class="col-sm-5 pull-right">
													<h4 class="text-right">
														Sub total :
														<?php if ($tipoFactura == 'venta'): ?>
															<span class="blue">$<span id='spanSubTotalTotal'><?php echo number_format($venta->totalVenta - $venta->totalVenta * 0.16,2,".","");?></span></span>
														<?php else: ?>
															<span class="blue">$<span id='spanSubTotalTotal'><?php echo number_format($contrato->precio - $contrato->precio * 0.16,2,".","");?></span></span>
														<?php endif; ?>
													</h4>
													<h4 class="text-right">
														IVA :
														<?php if ($tipoFactura == 'venta'): ?>
															<span class="blue">$<span id='spanIVA'><?php echo number_format($venta->totalVenta * 0.16,2,".","");?></span></span>
														<?php else: ?>
															<span class="blue">$<span id='spanIVA'><?php echo number_format($contrato->precio * 0.16,2,".","");?></span></span>
														<?php endif; ?>
													</h4>
													<h4 class="text-right">
														Monto total :
														<?php if ($tipoFactura == 'venta'): ?>
															<span class="blue">$<span id='spanTotal'><?php echo number_format($venta->totalVenta,2,".","");?></span></span>
														<?php else: ?>
															<span class="blue">$<span id='spanTotal'><?php echo number_format($contrato->precio,2,".","");?></span></span>
														<?php endif; ?>
													</h4>
												</div>
												<!-- <div class="col-sm-7 pull-left"> Observaciones </div> -->
											</div>

											<div class="space-6"></div>
											<div class="row">
												<div class="col-xs-12">
            										<iframe id="ifram_monto" style="width: 100%; height: 65px; border-style: none; visibility:hidden;"></iframe>
        										</div>
												<div class="col-xs-4 col-xs-offset-4">
													<hr>
													<button class="btn btn-danger btn-white btn-bold btn-info btn-block btn-round controlable" id="btnGenerar">
														<i class="ace-icon fa fa-bolt bigger-120 blue"></i>
														Generar factura
													</button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
			<?php
			}
			else
			{
			?>
			<h3 class="red text-center bigger">
				<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> No existe la venta o contrato con el id seleccionado o ya ha sido cancelada y no se puede facturar.
			</h3>
			<?php
			}
			?>
								<!-- PAGE CONTENT ENDS -->
					</div><!-- /.col -->
				</div><!-- /.row -->

				<!--/////////////////////modal agregar factura ////////////////////////-->
				<div id="my-modal-confirmar" class="modal fade" tabindex="-1">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h3 class="smaller blue no-margin">
									<span class="smaller lighter blue no-margin">
										<i class="fa fa-plus" aria-hidden="true"></i>
									</span>
									Generar factura
								</h3>
							</div>

							<div class="modal-body">
								<form class="form-horizontal" role="form" onSubmit="return false">
									<div class="form-group">
										<label class="col-xs-12 text-left"><i class="fa fa-question-circle" aria-hidden="true"></i> ¿Deseas generar esta factura? <br/>Revisa que toda la información sea correcta y pulsa "Generar" para continuar</label>
									</div>
								</form>
							</div>

							<div class="modal-footer">
								<button class="btn btn-white btn-info btn-bold btn-round" id="btnGuardarModal">
									<i class="ace-icon fa fa-bolt"></i>
									Generar
								</button>
								<button class="btn btn-white btn-default btn-bold no-border btn-round" data-dismiss="modal">
									<i class="ace-icon fa fa-times"></i>
									Cancelar
								</button>
							</div>
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div>
				<!-- /////////////////// Modal visualizar imagen del plan ////////////////////////// -->
				<div id="modal-img" class="modal fade" role="dialog">
					<div class="modal-dialog">

					<!-- Modal content-->
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title">Vista previa de la imagen</h4>
							</div>
							<div class="modal-body">
								<div id="imgSrc" class="col-xs-12" style="text-align:center">
					<?php
						if ($modificar && strlen($imagen_) > 0)
						{
					?>
									<img class="vistaPrevia img-thumbnail" style="cursor:zoom-in" width="100%" height="100%" src="data:image/jpeg;base64,<?php echo $imdata;?>"/>
					<?php
						}
					?>
						        </div>
								<div class="caption">
						        	<p class="text-center">Vista previa de la imagen del plan funerario</p>
						        </div>
								<br>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
							</div>
						</div>
					</div>
				</div><!-- Modal -->
				<div id="modal-vista-previa" class="modal fade" role="dialog">
					<div class="modal-dialog">
					<!-- Modal content-->
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title"><i class="fa fa-search" aria-hidden="true"></i> Vista previa del nuevo plan funerario</h4>
							</div>
							<div class="modal-body">
								<div class="row">
									<div id="divVistaPrevia" class="col-xs-12">
							        </div>
								</div>
								<br>
							</div>
							<div class="modal-footer">
								<button type="button" id="btnGuardarVistPrevia" class="btn btn-white btn-info btn-bold btn-round" data-dismiss="modal"><i class="ace-icon fa fa-save"></i> Guardar este plan</button>
								<button type="button" class="btn btn-default btn-bold btn-white btn-round" data-dismiss="modal"><i class="ace-icon fa fa-times"></i> Cerrar</button>
							</div>
						</div>
					</div>
				</div><!-- Modal -->
			</div><!-- /.page-content -->
			<div class="footer">
				<?php require_once('pie.php'); ?>
			</div>

			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
			</a>
		</div>
	</div><!-- /.main-content -->

		<!-- basic scripts -->

		<!--[if !IE]> -->
		<script src="assets/js/jquery-2.1.4.min.js"></script>

		<!-- <![endif]-->

		<!--[if IE]>
<script src="assets/js/jquery-1.11.3.min.js"></script>
<![endif]-->
		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>
		<script src="assets/js/bootstrap.min.js"></script>

		<!-- page specific plugin scripts -->
		<script src="assets/js/bootbox.js"></script>
		<script src="assets/js/jquery.gritter.min.js"></script>
		<script src="assets/js/jquery.dataTables.min.js"></script>
		<script src="assets/js/jquery.dataTables.bootstrap.min.js"></script>
		<script src="assets/js/dataTables.buttons.min.js"></script>
		<!-- ace scripts -->
		<script src="assets/js/ace-elements.min.js"></script>
		<script src="assets/js/ace.min.js"></script>
		<script src="assets/js/jquery-upload-files/jquery.uploadfile.min.js"></script>
		<script src="assets/js/custom/primario.js"></script>
		<!-- inline scripts related to this page -->
		<script type="text/javascript">

			myTable = "";
			objetoItems	= [];
			htmlBtnGenerar = $("#btnGenerar").html();
			function deshabilitar(d)
			{
				if(d)
				{
					$(".controlable").prop("disabled",true);
					$("#btnGenerar").html(cargarSpinner+" Generando factura. Espere por favor...");
				}
				else
				{
					$(".controlable").prop("disabled",false);
					$("#btnGenerar").html(htmlBtnGenerar);

				}
			}
			function unshiftItem(claveSat, medida, cantidad, precio, concepto)
			{
				esteItem     				= new item(	claveSat, medida, cantidad, precio, concepto);
				objetoItems.unshift(esteItem);
			}
			function item(claveSat, medida, cantidad, precio, concepto)
			{
				this.claveSat 					= claveSat;
				this.medida 					= medida;
				this.cantidad 					= cantidad;
				this.precio 					= precio;
				this.concepto 					= concepto;
			}
			function adjuntarClavesSat()
			{
				completo = true;
				objetoItems.length = 0;
				$(".trConceptos").each(function()
				{
					esteTr 			= $(this);
					esteInputClave 	= esteTr.find(".claveSat");
					estaClave 		= esteInputClave.val();
					esteInputConcepto 	= esteTr.find(".nombreConcepto");
					esteConcepto 		= esteInputConcepto.val();
					if (estaClave.length < 8)
					{
						esteInputClave.parent().addClass("danger");
						esteInputClave.focus();
						objetoItems.length = 0;
						mensaje("error","Revisa las claves remarcadas, no cumple con la cantidad de caracteres");
						completo = false;
						return completo;
					}
					if (esteConcepto.length < 1)
					{
						esteInputConcepto.parent().addClass("danger");
						esteInputConcepto.focus();
						objetoItems.length = 0;
						mensaje("error","El campo concepto no pueden estar en blanco");
						completo = false;
						return completo;
					}
					else
					{
						estaMedida = esteTr.find(".medidaSat").val();
						estePrecio = esteTr.find(".inputPrecio").val();
						unshiftItem(estaClave, estaMedida, 1, estePrecio, esteConcepto);
					}
				});
				return completo;
			}
			$(document).ready(function()
			{
				abrirMenu();
				$("#btnGenerar").click(function()
				{
					$(".danger").removeClass("danger");
					if (adjuntarClavesSat())
					{
						$("#my-modal-confirmar").modal();
					}
				});
				$("#btnGuardarModal").click(function()
				{
					$("#my-modal-confirmar").modal('hide');
					if (adjuntarClavesSat())
					{
						deshabilitar(true);
					<?php if ($tipoFactura == 'venta'): ?>
						idVenta = <?php echo $venta->id;?>;
						idContrato = 0;
					<?php else: ?>
						idContrato = <?php echo $contrato->id;?>;
						idVenta = 0;
					<?php endif; ?>
						formaPago = $("#formaPago").val();
						metodoPago = $("#metodoPago").val();
						usoCfdi = $("#usoCfdi").val();
						rfcReceptor = $("#rfcReceptor").val();
						razonReceptor = $("#razonReceptor").val();
						emailReceptor = $("#emailReceptor").val();
						enviarCfdi = $("#enviarCfdi").prop("checked") ? 1 : 0;
						objetoItemsJSON = JSON.stringify(objetoItems);

						get = '?idVenta='+idVenta;
						get +='&idContrato='+idContrato;
						get +='&formaPago='+formaPago;
						get +='&metodoPago='+metodoPago;
						get +='&usoCfdi='+usoCfdi;
						get +='&rfcReceptor='+rfcReceptor;
						get +='&razonReceptor='+razonReceptor;
						get +='&emailReceptor='+emailReceptor;
						get +='&enviarCfdi='+enviarCfdi;
						get +='&objetoItemsJSON='+objetoItemsJSON;
						console.log(get);
						$("#ifram_monto").css("visibility","visible");
						$("#ifram_monto").attr('src','assets/ws/gen_factura.php'+get);
					}
				});
				$(".inputPrecio").change(function()
				{
					total = parseFloat($(this).val());
					iva = total * 0.16;
					subTotal = total - iva;
					$("#spanSubTotal").text(total);
					$("#spanSubTotalTotal").text(subTotal.toFixed(2));
					$("#spanTotal").text(total.toFixed(2));
					$("#spanIVA").text(iva.toFixed(2));
				});
			});
		</script>
	</body>
</html>
<?php
	}
 ?>
