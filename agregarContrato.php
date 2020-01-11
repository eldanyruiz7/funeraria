<?php // Ejemplo github
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
		require_once ("assets/php/usuario.class.php");
		$usuario = new usuario($idUsuario,$mysqli);
		$permiso = $usuario->permiso("agregarContrato",$mysqli);
		if (!$permiso)
		{
			header("Location: index.php");
		}
		require_once ("assets/php/query.class.php");
		$query = new Query();
		$modificar = FALSE;
		$difuntoActivo = TRUE;
		if (isset($_GET['idContrato']))
		{
			if (is_numeric($_GET['idContrato']))
			{
				require_once "assets/php/funcionesVarias.php";
				require "assets/php/contrato.class.php";
				$idContrato = $_GET['idContrato'];
				$contrato = new Contrato($idContrato,$query);
				if ($contrato->id)
				{
					$modificar = TRUE;
					$difuntoActivo = TRUE;
				}
				else
				{
					$modificar = TRUE;
					$difuntoActivo = FALSE;
				}
			}
		}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title><?php echo $modificar ? 'Modificar contrato' : 'Agregar contrato';?></title>

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
			.scrollable {
			    max-height: calc(100vh - 185px);
			    overflow-y: auto;
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
							<li class="active"><?php echo $modificar ? 'Modificar contrato' : 'Agregar contrato';?></li>
						</ul><!-- /.breadcrumb -->
					</div>
					<div class="page-content">
						<div class="page-header">
							<h1>
								<i class="fa fa-file-text-o" aria-hidden="true"></i> <?php echo $modificar ? 'Modificar contrato' : 'Agregar contrato';?>
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									<?php echo $modificar ? 'Modificar contrato' : 'Agregar un nuevo contrato';?>
								</small>
							</h1>
				<?php
					if ($difuntoActivo == FALSE && $modificar == TRUE)
					{
				?>
							<h1 class="red text-center bigger">
								<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Atención: Este registro no se puede modificar porque ya ha sido cancelado o eliminado
							</h1>
				<?php
					}
				 ?>
						</div><!-- /.page-header -->
						<div class="row">
							<div class="col-xs-12">
								<form class="form-horizontal" id="form" role="form">
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Folio </label>

										<div class="col-sm-8">
											<input type="text" autocomplete="off" value="<?php echo $modificar ? $contrato->folio : '';?>" id="folio" name="folio" placeholder="Folio físico" class="">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Cliente(*) </label>

										<div class="col-sm-4 no-padding-right">
											<input type="text" readonly="true" id="nombre" name="nombre" value="<?php echo $modificar ? $contrato->nombreCliente : '';?>" placeholder="" class="col-xs-12">
											<input type="hidden" name="idClienteHidden" id="idClienteHidden" value="<?php echo $modificar ? $contrato->idCliente : '0';?>">
										</div>
										<div class="col-sm-2 no-padding-left">
											<button id="buttonBuscarCliente" type="button" class="btn btn-white btn-info"><i class="fa fa-search green" aria-hidden="true"></i></button>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Domicilio (Calle, No Ext, No Int)(*) </label>

										<div class="col-sm-8">
											<input type="text" value="<?php echo $modificar ? $contrato->domicilio1 : '';?>" id="domicilio1" name="domicilio1" placeholder="Domicilio (Calle, No Ext, No Int)" class="col-xs-9">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Domicilio (Colonia, Población, Municipio)(*) </label>

										<div class="col-sm-8">
											<input type="text" value="<?php echo $modificar ? $contrato->domicilio2 : '';?>" id="domicilio2" name="domicilio2" placeholder="Domicilio (Colonia, Población, Municipio)" class="col-xs-9">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Código postal (*) </label>

										<div class="col-sm-8">
											<input type="text" value="<?php echo $modificar ? $contrato->cp : '';?>" id="cp" name="cp" placeholder="Código postal" class="">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Estado (*) </label>

										<div class="col-sm-8">
											<select id="estado" name="estado" class="col-xs-5">
												<?php
													$res_estados = $query->table("cat_estados")->select("*")->where("activo", "=", 1, "i")->execute();
														foreach ($res_estados as $row_estado)
														{
															if ($modificar && $row_estado['id'] == $contrato->idEstado)
															{
																echo "<option selected value=".$row_estado['id'].">".$row_estado['estado']."</option>";
																continue;
															}
															echo "<option value=".$row_estado['id'].">".$row_estado['estado']."</option>";

														}
												 ?>
											 </select>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Referencias del domicilio </label>

										<div class="col-sm-8">
											<input type="text" value="<?php echo $modificar ? $contrato->referencias : '';?>" id="referencias" name="referencias" placeholder="Referencias" class="col-xs-9">
										</div>
									</div>
									<hr>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Plan funerario(*) </label>

										<div class="col-sm-4 no-padding-right">
											<input type="text" readonly="true" id="plan" name="plan" value="<?php echo $modificar ? $contrato->nombrePlan : '';?>" placeholder="" class="col-xs-12">
											<input type="hidden" name="idPlanHidden" id="idPlanHidden" value="<?php echo $modificar ? $contrato->idPlan : '0';?>">
										</div>
										<div class="col-sm-2 no-padding-left">
											<button id="buttonBuscarPlan" type="button" class="btn btn-white btn-info"><i class="fa fa-search green" aria-hidden="true"></i></button>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Frecuencia de pago (*) </label>
										<div class="col-sm-6">
											<select id="frecuencia" name="frecuencia" class="col-xs-5">
												<option <?php echo ($modificar && $contrato->frecuenciaPago == 1) ? 'selected' : '';?> value="1">Semanal</option>
												<option <?php echo ($modificar && $contrato->frecuenciaPago == 2) ? 'selected' : '';?> value="2">Quincenal</option>
												<option <?php echo ($modificar && $contrato->frecuenciaPago == 3) ? 'selected' : '';?> value="3">Mensual</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Costo total $ (*)</label>

										<div class="col-sm-8">
											<input type="number" id="precio" min="0" name="precio" value="<?php echo $modificar ? $contrato->precio : '0';?>" class="col-xs-2 text-right">
										</div>
									</div>
									<hr>

									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Descuento por duplicación de inversión $</label>

										<div class="col-sm-8">
											<input type="number" id="descuentoDuplicacionInversion" min="0" name="descuentoDuplicacionInversion" value="<?php echo $modificar ? $contrato->descuentoDuplicacionInversion : '0';?>" class="col-xs-2 text-right">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Descuento por cambio de funeraria $</label>

										<div class="col-sm-8">
											<input type="number" id="descuentoCambioFuneraria" min="0" name="descuentoCambioFuneraria" value="<?php echo $modificar ? $contrato->descuentoCambioFuneraria : '0';?>" class="col-xs-2 text-right">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Descuento adicional $</label>

										<div class="col-sm-8">
											<input type="number" id="descuentoAdicional" min="0" name="descuentoAdicional" value="<?php echo $modificar ? $contrato->descuentoAdicional : '0';?>" class="col-xs-2 text-right">
										</div>
									</div>
									<hr>

									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Inversión $ (*)</label>

										<div class="col-sm-8">
											<input type="number" id="anticipo" name="anticipo" value="<?php echo $modificar ? $contrato->anticipo : '0';?>" class="col-xs-2 text-right">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Costo aportación $ (*) </label>

										<div class="col-sm-8">
											<input type="number" id="aportacion" name="aportacion" value="<?php echo $modificar ? $contrato->aportacion : '0';?>" class="col-xs-2 text-right">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Fecha de la primera aportación (*) </label>

										<div class="col-sm-2 no-padding-right">
											<input value="<?php echo $modificar ? $contrato->fechaPrimerAportacion : '';?>" type="date" id="fechaAportacion" autocomplete="off" name="fechaAportacion" class="col-xs-12">
										</div>
										<div class="col-sm-2 no-padding-left">
											<span id="buttonSimularCorrida" type="button" class="btn btn-link no-border"><i class="fa fa-2x fa-calculator green" aria-hidden="true"></i></span>
										</div>
									</div>
									<hr>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Vendedor (*) </label>

										<div class="col-sm-8">
											<select id="vendedor" name="vendedor" class="col-xs-5">
												<?php
													$res_vendedor = $query 	->table("cat_usuarios")->select("id, nombres, apellidop, apellidom")->where("activo", "=", 1, "i")
																			->and()->where("tipo", "<>", 0, "i")->orderBy("nombres")->execute();
													foreach ($res_vendedor as $row_vendedor)
													{
														if ($modificar && $row_vendedor['id'] == $contrato->idVendedor)
														{
															echo "<option selected value=".$row_vendedor['id'].">".$row_vendedor['nombres']." ".$row_vendedor['apellidop']." ".$row_vendedor['apellidom']."</option>";
															continue;
														}
														echo "<option value=".$row_vendedor['id'].">".$row_vendedor['nombres']." ".$row_vendedor['apellidop']." ".$row_vendedor['apellidom']."</option>";
													}
												 ?>
											 </select>
										</div>
									</div>
									<hr>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Observaciones </label>

										<div class="col-sm-8">
											<input type="text" value="<?php echo $modificar ? $contrato->observaciones : '';?>" id="observaciones" name="observaciones" placeholder="Observaciones" class="col-xs-12">
										</div>
									</div>
					<?php
						if ($modificar)
						{
					?>
									<input type="hidden" name="hiddenIdContrato" id="hiddenIdContrato" value="<?php echo $contrato->id;?>">
					<?php
						}

					?>
								</form>

							</div>
							<div class="col-xs-4 col-xs-offset-4">
								<hr>
								<button class="btn btn-danger btn-white btn-bold btn-info btn-block btn-round" id="btnGuardar">
									<i class="ace-icon fa fa-floppy-o bigger-120 blue"></i>
									<?php echo $modificar ? "Modificar contrato" : "Guardar nuevo contrato";?>
								</button>
							</div>
						</div>
								<!-- PAGE CONTENT ENDS -->
					</div><!-- /.col -->
				</div><!-- /.row -->
				<!-- ///////////////////////////////////// MODAL BUSCAR CLIENTE REGISTRADO ///////////////////////////////// -->
				<div id="modal-buscar-cliente" class="modal fade" tabindex="-1">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header no-padding">
								<div class="table-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
										<span class="white">&times;</span>
									</button>
									<i class="fa fa-user-o" aria-hidden="true"></i> Asignar cliente al contrato
								</div>
							</div>

							<div class="modal-body no-padding">
								<table id="tabla-agregar-cliente" style="width:100%" class="table table-striped table-bordered table-hover no-margin-bottom no-border-top">
									<thead>
										<tr>
											<th>Id</th>
											<th>Nombre</th>
											<th>RFC</th>
											<th>Dirección</th>
											<th>Seleccionar</th>
										</tr>
									</thead>
								</table>
							</div>
							<div class="modal-footer no-margin-top">
								<button class="btn btn-white btn-default btn-bold no-border btn-round" data-dismiss="modal">
									<i class="ace-icon fa fa-times"></i>
									Cancelar
								</button>
							</div>
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div>
				<!-- ///////////////////////////////////// MODAL BUSCAR CLIENTE REGISTRADO ///////////////////////////////// -->
				<div id="modal-buscar-plan" class="modal fade" tabindex="-1">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header no-padding">
								<div class="table-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
										<span class="white">&times;</span>
									</button>
									<i class="fa fa-object-group" aria-hidden="true"></i> Asignar un plan al contrato
								</div>
							</div>

							<div class="modal-body no-padding">
								<table id="tabla-agregar-plan" style="width:100%" class="table table-striped table-bordered table-hover no-margin-bottom no-border-top">
									<thead>
										<tr>
											<th>Nombre</th>
											<th>Descripción</th>
											<th>Precio</th>
											<th>Seleccionar</th>
										</tr>
									</thead>
								</table>
							</div>
							<div class="modal-footer no-margin-top">
								<button class="btn btn-white btn-default btn-bold no-border btn-round" data-dismiss="modal">
									<i class="ace-icon fa fa-times"></i>
									Cancelar
								</button>
							</div>
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div>
				<!--/////////////////////modal corrida financiera ////////////////////////-->
				<div id="modal-vista-previa" class="modal fade" role="dialog">
					<div class="modal-dialog">
					<!-- Modal content-->
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title"><i class="fa fa-search" aria-hidden="true"></i> Simulación de corrida financiera</h4>
							</div>
							<div class="modal-body scrollable">
								<div class="row">
									<div id="divVistaPrevia" class="col-xs-12">
							        </div>
								</div>
								<br>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default btn-bold btn-white btn-round" data-dismiss="modal"><i class="ace-icon fa fa-times"></i> Cerrar</button>
							</div>
						</div>
					</div>
				</div><!-- Modal -->
				<div id="my-modal-confirmar" class="modal fade" tabindex="-1">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h3 class="smaller blue no-margin">
									<span class="smaller lighter blue no-margin">
										<i class="fa fa-question" aria-hidden="true"></i>
									</span>
									<?php echo $modificar ? "Modificar este registro" : "Agregar este registro al sistema";?>
								</h3>
							</div>

							<div class="modal-body">
								<form class="form-horizontal" role="form" onSubmit="return false">
									<div class="form-group">
					<?php
						if ($modificar)
						{
					?>
										<label class="col-xs-12 text-left"><i class="fa fa-question-circle" aria-hidden="true"></i> ¿Deseas modificar este registro? <br/>Estás a punto de modificar este contrato</label>
					<?php
						}
						else
						{
					?>
										<label class="col-xs-12 text-left"><i class="fa fa-question-circle" aria-hidden="true"></i> ¿Deseas agregar este registro al sistema? <br/>Estás a punto de agregar un nuevo contrato</label>
					<?php
						}
					 ?>
									</div>
								</form>
							</div>

							<div class="modal-footer">
								<button class="btn btn-white btn-info btn-bold btn-round" id="btnGuardarModal">
									<i class="ace-icon fa fa-save"></i>
									Guardar
								</button>
								<button class="btn btn-white btn-default btn-bold no-border btn-round" data-dismiss="modal">
									<i class="ace-icon fa fa-times"></i>
									Cancelar
								</button>
							</div>
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div>
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
		<script src="assets/js/dataTables.select.min.js"></script>

		<script src="assets/js/dataTables.buttons.min.js"></script>
		<!-- ace scripts -->
		<script src="assets/js/ace-elements.min.js"></script>
		<script src="assets/js/ace.min.js"></script>
		<script src="assets/js/custom/primario.js"></script>
		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			myTable = "";
			tablaCliente =
			$('#tabla-agregar-cliente')
			//.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
			.DataTable( {
				"ajax":			"assets/ajax/listarClientes.JSON.php",
				"aLengthMenu":[
					[5, 10, -1],
					[5, 10, "Todos"]
				],
				"bInfo" : false,
				"processing": 	true,
				"language":
				{
					 "url": "assets/js/custom/Spanish.json"
				 },
				"columns":		[
					{ "data": "id" },
					{ "data": "nombresCliente" },
					{ "data": "rfcCliente" },
					{ "data": "domicilio" },
					{
						"data": "btnAgregar",
						"className": 'text-center',
					 }
				],
				"order": 		[[1, 'asc']]
			} );
			tablaPlanes =
			$('#tabla-agregar-plan')
			//.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
			.DataTable( {
				"ajax":			"assets/ajax/listarPlanes.JSON.php",
				"aLengthMenu":[
					[5, 10, -1],
					[5, 10, "Todos"]
				],
				"bInfo" : false,
				"processing": 	true,
				"language":
				{
					 "url": "assets/js/custom/Spanish.json"
				 },
				"columns":		[
					{ "data": "nombrePlan" },
					{ "data": "descripcion" },
					{
						"data": "precio",
						"className": 'text-right' },
					{
						"data": "btnAgregar",
						"className": 'text-center',
					 }
				],
				"order": 		[[0, 'asc']]
			} );
			$(document).ready(function()
			{
				abrirMenu();
				$("#buttonBuscarCliente").click(function()
				{
					tablaCliente.ajax.reload();
					$("#modal-buscar-cliente").modal();
				});
				$(document).on("click",".aAgregarCliente",function()
				{
					idCliente = $(this).attr("id");
					nombre = $(this).attr("nombre-cliente");
					domicilio1 = $(this).attr("domicilio1");
					domicilio2 = $(this).attr("domicilio2");
					idEstado = $(this).attr("id-estado");
					cp = $(this).attr("cp");
					$("#modal-buscar-cliente").modal("hide");
					$("#idClienteHidden").val(idCliente);
					$("#nombre").val(nombre);
					$("#domicilio1").val(domicilio1);
					$("#domicilio2").val(domicilio2);
					$("#estado").val(idEstado);
					$("#cp").val(cp);
				});
				$("#buttonBuscarPlan").click(function()
				{
					tablaPlanes.ajax.reload();
					$("#modal-buscar-plan").modal();
				});
				$(document).on("click",".aAgregarPlan",function()
				{
					idPlan = $(this).attr("id");
					nombre = $(this).attr("nombre-plan");
					precio = $(this).attr("precio");
					$("#modal-buscar-plan").modal("hide");
					$("#idPlanHidden").val(idPlan);
					$("#plan").val(nombre);
					$("#precio").val(precio);
				});
				$("#buttonSimularCorrida").click(function()
				{
					$("#modal-vista-previa").modal();
					//var arrayProductosJSON 	= JSON.stringify(objetoAgregarProd);
					idPlan					=$("#idPlanHidden").val();
					nombre 					= $("#plan").val();
					precio 					= $("#precio").val();
					descuentoDuplicacionInversion = $("#descuentoDuplicacionInversion").val();
					descuentoCambioFuneraria = $("#descuentoCambioFuneraria").val();
					descuentoAdicional 		= $("#descuentoAdicional").val();
					anticipo				= $("#anticipo").val();
					aportacion				= $("#aportacion").val();
					frecuencia				= $("#frecuencia").val();
					fechaAportacion			= $("#fechaAportacion").val();
					$.ajax(
			        {
			            method: "GET",
			            url:"assets/ajax/corridaFinancieraContrato.php",
			            data: {idPlan:idPlan,nombre:nombre,descuentoDuplicacionInversion:descuentoDuplicacionInversion,descuentoCambioFuneraria:descuentoCambioFuneraria,descuentoAdicional:descuentoAdicional,fechaAportacion:fechaAportacion,precio:precio,anticipo:anticipo,frecuencia:frecuencia,aportacion:aportacion}//,precio:precio,anticipo:anticipo,aportacion:aportacion,fechaAportacion:fechaAportacion,frecuencia:frecuencia}
			        })
			        .done(function(p)
			        {
						$("#divVistaPrevia").html(p);
					})
					.fail(function(p)
					{
						$("#divVistaPrevia").html('No hay conexión con el servidor. Por favor vuelve a intentarlo');
						mensaje('error','No hay conexión con el servidor. Por favor vuelve a intentarlo');
					})
					.always(function(p)
					{
						console.log(p);
					});
				});
				$("#modal-vista-previa").on('hidden.bs.modal', function()
				{
					$("#divVistaPrevia").html(cargarSpinner+" Generando, espera por favor...");
				});
				$("#divVistaPrevia").html(cargarSpinner+" Generando, espera por favor...");
				$("#btnGuardar").click(function()
				{
					$("#my-modal-confirmar").modal();
					$(".has-error").removeClass('has-error');

				});
				$(document).on('click',"#btnEliminarImg",function()
		        {
					btnImg 		= $(this);
					btnImg.removeClass('btn-success');
					btnImg.addClass('btn-info');
					btnImg.html('<i class="fa fa-plus-circle" aria-hidden="true"></i> Imagen');
					btnImg.prop('id','btnAnadirImg');
					$("#status").html('</br><i class="fa fa-file-image-o fa-5x"></i></br>&nbsp;');
					// $("#status2").html('</br><i class="fa fa-user-circle-o fa-5x"></i></br>&nbsp;');
					$("#inputReset").click();
					$("#hiddenImgBinario").val("");
					$("#hiddenImgTipo").val("");
		        });
				$(document).on('click',"img.vistaPrevia",function()
		        {
		           // $('#dialog-Img').dialog("open");
				   // alert("Img");
				   $("#modal-img").modal();
		        });
				$(document).on('click','#btnGuardarModal',function()
				{
					$("#my-modal-confirmar").modal('hide');
					dialog = bootbox.dialog(
					{
					    title: 'Guardar',
					    message: '<p><i class="fa fa-spin fa-spinner"></i> Procesando...</p>',
						closeButton: false,
						buttons:
						{
				<?php
					if (!$modificar)
					{
				?>
							"otro" :
							{
								"label" : '<i class="fa fa-file-text-o" aria-hidden="true"></i> Capturar otro contrato',
								"className" : "btn-default btn-white btn-bold btn-round oculto",
								callback: function(result)
								{
									window.location.assign("agregarContrato.php");
								}
							},
				<?php
					}
				 ?>

							"index" :
							{
								"label" : '<i class="fa fa-file-text" aria-hidden="true"></i> Lista de contratos',
								"className" : "btn-success btn-white btn-bold btn-round oculto",
								callback: function(result)
								{
									window.location.assign("index.php");

								}
							},
							"Cerrar" :
							{
								"label" : '<i class="fa fa-times" aria-hidden="true"></i> Cerrar',
								"className" : "btn oculto2",
								callback: function(result)
								{
									dialog.modal('hide');
									setTimeout(function()
									{
										$("body").css("padding-right",0);
								    }, 500);
								}
							},
				<?php
					if ($modificar)
					{
				?>
							"Cerrar2" :
							{
								"label" : '<i class="fa fa-times" aria-hidden="true"></i> Cerrar',
								"className" : "btn-default btn-white btn-bold btn-round oculto",
								callback: function(result)
								{
									dialog.modal('hide');
									setTimeout(function()
									{
										$("body").css("padding-right",0);
								    }, 500);
								}
							}
				<?php
					}
				?>
						}

					});
					$.ajax(
			        {
			            method: "POST",
			            url:<?php echo $modificar ? "'assets/ajax/modificarContrato.php'" : "'assets/ajax/agregarContrato.php'";?>,
			            data: $("form#form").serialize()
			        })
			        .done(function(p)
			        {
						console.log(p);
						if (p.status == 1)
						{
							dialog.init(function()
							{
				<?php
					if ($modificar)
					{
				?>
								dialog.find('.bootbox-body').html('<i class="fa fa-check" aria-hidden="true"></i> Contrato <b>'+p.mensaje+'</b> ha sido modificado exitosamente');
				<?php
					}
					else
					{
				?>
								dialog.find('.bootbox-body').html('<i class="fa fa-check" aria-hidden="true"></i> Contrato <b>'+p.mensaje+'</b> ha sido creado exitosamente');
				<?php
					}
				?>
								dialog.find('.oculto').removeClass('oculto');
							});
				<?php
					if ($modificar)
					{
				?>
								mensaje('success','Contrato '+p.mensaje+' ha sido modificado exitosamente<br><h5><a href="index.php" class="orange">Lista de contratos</a></h5><h5><a target="_blank" href="assets/pdf/contrato.php?idContrato='+p.mensaje+'" class="orange">Imprimr este contrato</a></h5>');
				<?php
					}
					else
					{
				?>
								mensaje('success','Contrato '+p.mensaje+' ha sido guardado exitosamente<br><h5><a href="index.php" class="orange">Lista de contratos</a></h5><h5><a target="_blank" href="assets/pdf/contrato.php?idContrato='+p.mensaje+'" class="orange">Imprimr este contrato</a></h5>');
				<?php
					}
				?>
						}
						else
						{
							$("#"+p.focus).parent().parent().addClass('has-error');
							dialog.init(function()
							{
							 	dialog.find('.bootbox-body').html('<i class="fa fa-times" aria-hidden="true"></i> No se pudo guardar este registro');
								dialog.find('.oculto2').removeClass('oculto2');
							});
							mensaje('error','No se pudo guardar, inténtalo nuevamente<br>'+p.mensaje);
						}
			        })
					.fail(function()
					{
						dialog.init(function()
						{
							dialog.find('.bootbox-body').html('<i class="fa fa-times" aria-hidden="true"></i> No hay conexión con el servidor');
							dialog.find('.oculto2').removeClass('oculto2');
						});
						mensaje('error','No hay conexión con el servidor, inténtalo nuevamente');

					})
			        .always(function(p)
			        {
						console.log(p);
			        });

				});
			});
		</script>
	</body>
</html>
<?php
	}
 ?>
