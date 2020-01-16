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
 ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>Lista contratos activos</title>

		<meta name="description" content="with draggable and editable events" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="assets/font-awesome/4.5.0/css/font-awesome.min.css" />

		<!-- page specific plugin styles -->
		<link rel="stylesheet" href="assets/css/jquery-ui.custom.min.css" />
		<!-- text fonts -->
		<link rel="stylesheet" href="assets/css/chosen.min.css" />
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
		.modal-dialog{
			overflow-y: initial !important
		}
		#modal-agregar{
			max-height: 60%;
			overflow-y: auto;
		}
		.oculto
		{
			display: none;
		}
		.pointer
		{
			cursor: pointer;
		}
		td.details-control {
			background: url('assets/images/icons/details_open.png') no-repeat center center;
			cursor: pointer;
		}
		tr.shown td.details-control {
			background: url('assets/images/icons/details_close.png') no-repeat center center;
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
							<li class="active"><i class="ace-icon fa fa-file-text home-icon"></i> Contratos activos</li>
						</ul><!-- /.breadcrumb -->
					</div>

					<div class="page-content">
						<div class="page-header">
							<div class="row">
								<div class="col-xs-11">
									<h1>
										<i class="fa fa-file-text" aria-hidden="true"></i>Lista de contratos activos
										<small>
											<i class="ace-icon fa fa-angle-double-right"></i>

										</small>
									</h1>
								</div>
							</div>
						</div><!-- /.page-header -->

						<div class="row">
							<div class="col-xs-12">

								<div class="clearfix">
									<div class="pull-right tableTools-container"></div>
								</div>
								<!-- div.table-responsive -->

								<!-- div.dataTables_borderWrap -->
								<div id="tabladiv">
									<table id="dynamic-table" class="table table-striped table-bordered table-hover" style='width:100%'>
										<thead>
											<tr>
												<th class="center">
												</th>
												<th>Id.</th>
												<th>Folio</th>
												<th>Titular</th>
												<th class="text-center">Difunto</th>
												<th>Frecuencia pago</th>
												<th class="text-center">Costo $</th>
												<th class="text-center">Abonado $</th>
												<th class="text-center">Resta $</th>
												<th class="text-center">Estado</th>

												<th></th>
											</tr>
										</thead>


									</table>
								</div>
							</div>
						</div>

						<div class="row">


								<!-- PAGE CONTENT ENDS -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->
					<!-- //////////////////////////// Modal agregar pago contrato ////////////////////////////////// -->
					<div id="modal-agregar-pago" class="modal fade" tabindex="-1" data-keyboard="false" data-backdrop="static">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h3 class="smaller lighter blue no-margin">
										<span class="smaller lighter purple no-margin">
											<i class="fa fa-money" aria-hidden="true"></i>
										</span>
										¿Deseas agregar pago?
									</h3>
								</div>

								<div class="modal-body">
									<form class="form-horizontal" role="form" onSubmit="return false">
										<div class="form-group divError">
											<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre del titular: </label>
											<div class="col-sm-8">
												<span class="input-icon" style="width: 100%;">
													<input class="form-control" readonly="readonly" type="text" id="inputNombreTitular">
													<i class="ace-icon fa fa-user-o"></i>
												</span>
											</div>
										</div>
										<div class="form-group divError">
											<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Monto del pago: </label>

											<div class="col-sm-8">
												<span class="input-icon" style="width: 100%;">
													<input class="form-control text-right" type="number" id="inputPagaCon">
													<i class="ace-icon fa fa-usd"></i>
												</span>
											</div>
										</div>
										<div class="form-group divError">
											<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Folio del recibo: </label>

											<div class="col-sm-8">
												<select class="chosen-select form-control" id="selectFolio" data-placeholder="Folio recibo de cobro" style="width:100%">
												</select>
											</div>
										</div>


										<div class="form-group divError">
											<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Forma de pago: </label>

											<div class="col-sm-8">
												<span class="input-icon" style="width: 100%;">
													<select class="form-control" id="selectFormaPago">
														<?php
															$sql_fPago = "SELECT id, nombre FROM cat_formas_pago WHERE activo = 1";
															$res_fPago = $mysqli->query($sql_fPago);
															while ($row_fPago = $res_fPago->fetch_assoc())
															{
																$idFPago = $row_fPago['id'];
																$nombreFPago = $row_fPago['nombre'];
																echo "<option value='$idFPago'>$nombreFPago</option>";
															}
														 ?>
													</select>
												</span>
											</div>
										</div>
									</form>
									<div class="hr hr8 hr-double"></div>
									<div class="clearfix">
										<div class="grid3">
											<span class="grey">
												&nbsp;
											</span>
											<h4 class="bigger pull-right">&nbsp;</h4>
										</div>
										<div class="grid3">
											<span class="grey">
												<i class="ace-icon fa fa-file-text-o fa-2x green"></i>
												&nbsp; No. Contrato:
											</span>
											<input type="hidden" id="hiddenIdContrato">
											<h4 class="bigger pull-right"><span id="spanContrato">0000000010</span></h4>
										</div>

										<div class="grid3">
											<span class="grey">
												<i class="ace-icon fa fa-money fa-2x blue"></i>
												&nbsp; Saldo
											</span>
											<h4 class="bigger pull-right">$<span id="spanSaldo">0</span></h4>
										</div>
									</div>
								</div>

								<div class="modal-footer">
									<button class="btn btn-white btn-info btn-bold btn-round" id="btnAgregarPago">
										<i class="ace-icon fa fa-floppy-o"></i>
										Agregar pago
									</button>
									<button class="btn btn-white btn-danger btn-bold no-border btn-round" id="btnCancelarPago" data-dismiss="modal">
										<i class="ace-icon fa fa-times"></i>
										Cancelar
									</button>
								</div>
							</div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div>
					<!-- //////////////////////////// Modalasignar difunto ////////////////////////////////// -->
					<div id="modal-asignar-difunto" class="modal fade" tabindex="-1" data-keyboard="false" data-backdrop="static">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h3 class="smaller lighter blue no-margin">
										<span class="smaller lighter green no-margin">
											<i class="fa fa-user-circle" aria-hidden="true"></i>
										</span>
										Agregar difunto a este contrato
									</h3>
								</div>

								<div class="modal-body">
									<form class="form-horizontal" role="form" onSubmit="return false">
										<div class="form-group divError">
											<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre del titular: </label>
											<div class="col-sm-8">
												<span class="input-icon" style="width: 100%;">
													<input class="form-control" readonly="readonly" type="text" id="inputNombreTitularAsignar">
													<i class="ace-icon fa fa-user-o"></i>
												</span>
											</div>
										</div>
										<div class="form-group divError">
											<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Nombre del difunto: </label>
											<div class="col-sm-6 no-padding-right">
												<input type="text" readonly="true" id="nombreDifunto" name="nombreDifunto" value="" placeholder="" class="col-xs-12">
												<input type="hidden" name="idDifuntoHidden" id="idDifuntoHidden" value="0">
											</div>
											<div class="col-sm-2 no-padding-left">
												<button id="buttonBuscarDifunto" type="button" class="btn btn-white btn-info"><i class="fa fa-search green" aria-hidden="true"></i></button>
											</div>
										</div>

										<div class="form-group divError">
											<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Seleccionar cajón o urna: </label>

											<div class="col-sm-8">
												<span class="input-icon" style="width: 100%;">
													<select class="form-control" id="selectCajonUrna">
														<option value="0">Selecciona...</option>
														<?php
															$sql_fPago = "SELECT id, nombre FROM cat_productos WHERE activo = 1 ORDER BY nombre ASC";
															$res_fPago = $mysqli->query($sql_fPago);
															while ($row_fPago = $res_fPago->fetch_assoc())
															{
																$idFPago = $row_fPago['id'];
																$nombreFPago = $row_fPago['nombre'];
																echo "<option value='$idFPago'>$nombreFPago</option>";
															}
														 ?>
													</select>
												</span>
											</div>
										</div>
										<div class="form-group divError">
											<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Observaciones: </label>

											<div class="col-sm-8">
												<span class="input-icon" style="width: 100%;">
													<input class="form-control" type="text" id="observacionesAgregarDifunto">
													<i class="ace-icon fa fa-file-text"></i>
												</span>
											</div>
										</div>
									</form>
									<div class="hr hr8 hr-double"></div>
									<div class="clearfix">
										<div class="grid3">
											<span class="grey">
												&nbsp;
											</span>
											<h4 class="bigger pull-right">&nbsp;</h4>
										</div>
										<div class="grid3">
											<span class="grey">
												<i class="ace-icon fa fa-file-text-o fa-2x blue"></i>
												&nbsp; Folio:
											</span><br>
											<h4 class="bigger pull-right"><span id="spanFolioContratoDifunto"></span></h4>
										</div>
										<div class="grid3">
											<span class="grey">
												<i class="ace-icon fa fa-file-text-o fa-2x green"></i>
												&nbsp; Id. Contrato:
											</span>
											<h4 class="bigger pull-right"><span id="spanContratoDifunto"></span></h4>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<button class="btn btn-white btn-info btn-bold btn-round" id="btnAsignarDifunto">
										<i class="ace-icon fa fa-floppy-o"></i>
										Asignar difunto
									</button>
									<button class="btn btn-white btn-danger btn-bold no-border btn-round" id="btnAsignarDifuntoHidden" data-dismiss="modal">
										<i class="ace-icon fa fa-times"></i>
										Cancelar
									</button>
								</div>
							</div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div>
					<!--/////////////////////modal cancelar contrato ////////////////////////-->
					<div id="my-modal-eliminar" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h3 class="smaller red no-margin">
										<span class="smaller lighter red no-margin">
											<i class="fa fa-ban" aria-hidden="true"></i>
										</span>
										Cancelar este contrato
									</h3>
								</div>

								<div class="modal-body">
									<form class="form-horizontal" role="form" onSubmit="return false">
										<div class="form-group">
											<label class="col-xs-12 text-left"><i class="fa fa-question-circle" aria-hidden="true"></i> ¿Deseas cancelar este contrato? <br/>Elige un motivo por el que se cancelará este contrato y pulsa cancelar contrato</label>
										</div>
										<div class="form-group divError">
											<label class="col-sm-4 control-label no-padding-right" for="form-field-1"> Motivo: </label>

											<div class="col-sm-8">
												<span class="input-icon" style="width: 100%;">
													<select class="form-control" id="selectMotivo">
														<?php
															$sql = "SELECT id, nombre FROM cat_motivosCancelacionContratos WHERE activo = 1 ORDER BY nombre ASC";
															$res_motivos = $mysqli->query($sql);
															while ($row_motivos = $res_motivos->fetch_assoc())
															{
																$idMotivo 		= $row_motivos['id'];
																$nombreMotivo 	= $row_motivos['nombre'];
																echo "<option value='$idMotivo'>$nombreMotivo</option>";
															}
														 ?>
													</select>
												</span>
											</div>
										</div>
									</form>
									<input type="hidden" id="hiddenEliminar" />
								</div>

								<div class="modal-footer">
									<button class="btn btn-white btn-danger btn-bold btn-round" id="btnEliminarModal">
										<i class="ace-icon fa fa-ban"></i>
										Cancelar contrato
										</button>
									<button class="btn btn-white btn-default btn-bold no-border btn-round" id="btnEliminarModalCancelar" data-dismiss="modal">
										<i class="ace-icon fa fa-times"></i>
										Regresar
									</button>
								</div>
							</div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div>
					<!--/////////////////////modal reactivar contrato ////////////////////////-->
					<div id="my-modal-reactivar" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h3 class="smaller green no-margin">
										<span class="smaller lighter green no-margin">
											<i class="fa fa-level-up" aria-hidden="true"></i>
										</span>
										Reactivar este contrato
									</h3>
								</div>

								<div class="modal-body">
									<form class="form-horizontal" role="form" onSubmit="return false">
										<div class="form-group">
											<label class="col-xs-12 text-left"><i class="fa fa-question-circle" aria-hidden="true"></i> ¿Deseas reactivar este contrato? </label>
										</div>
									</form>
									<input type="hidden" id="hiddenEliminar" />
								</div>

								<div class="modal-footer">
									<button class="btn btn-white btn-success btn-bold btn-round" id="btnReactivarModal">
										<i class="ace-icon fa fa-ban"></i>
										Reactivar contrato
										</button>
									<button class="btn btn-white btn-default btn-bold no-border btn-round" id="btnReactivarModalCancelar" data-dismiss="modal">
										<i class="ace-icon fa fa-times"></i>
										Regresar
									</button>
								</div>
							</div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div>
					<!-- /////////////////// Modal buscar y asignar difunto ///////////////////// -->
					<div id="modal-buscar-difunto" class="modal fade" tabindex="-1" data-keyboard="false" data-backdrop="static">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header no-padding">
									<div class="table-header">
										<i class="fa fa-user-o" aria-hidden="true"></i> Asignar difunto al contrato
									</div>
								</div>

								<div class="modal-body no-padding">
									<table id="tabla-agregar-difunto" style="width:100%" class="table table-striped table-bordered table-hover no-margin-bottom no-border-top">
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
									<button id="btnCancelarAgregarDifunto" class="btn btn-white btn-default btn-bold no-border btn-round" data-dismiss="modal">
										<i class="ace-icon fa fa-times"></i>
										Cancelar
									</button>
								</div>
							</div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div>

				</div>
			</div><!-- /.main-content -->

			<div class="footer">
				<?php require_once('pie.php'); ?>
			</div>

			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
			</a>
		</div><!-- /.main-container -->

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
		<script src="assets/js/jquery-ui.custom.min.js"></script>
		<script src="assets/js/bootbox.js"></script>
		<script src="assets/js/jquery.gritter.min.js"></script>
		<script src="assets/js/jquery.dataTables.min.js"></script>
		<script src="assets/js/jquery.dataTables.bootstrap.min.js"></script>
		<!-- <script src="assets/js/dataTables.select.min.js"></script> -->

		<script src="assets/js/dataTables.buttons.min.js"></script>
		<script src="assets/js/buttons.flash.min.js"></script>
		<script src="assets/js/buttons.html5.min.js"></script>
		<script src="assets/js/buttons.print.min.js"></script>
		<script src="assets/js/buttons.colVis.min.js"></script>
		<script src="assets/js/ace-elements.min.js"></script>
		<script src="assets/js/chosen.jquery.min.js"></script>
		<script src="assets/js/ace.min.js"></script>
		<script src="assets/js/jquery-ui-blind.min.js"></script>
		<script src="assets/js/custom/primario.js"></script>

		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			function cancelarContrato()
			{
				icon = $("#btnEliminarModal");
				iconOriginal = icon.html();
				icon.html(cargarSpinner+" Espera...");
				icon.attr("disabled",true);
				$("#btnEliminarModalCancelar").attr("disabled", true);
				$("#selectMotivo").attr("disabled",true);
				idPac_ = $("#hiddenEliminar").val();
				motivo = $("#selectMotivo").val();
			 $.ajax(
			 {
				 method: "POST",
				 url:"assets/ajax/cancelarContrato.php",
				 data: {idCliente:idPac_,motivo:motivo}
			 })
			 .done(function(p)
			 {
				 console.log(p);
				 if (p.status == 1)
				 {
					 $('#my-modal-eliminar').modal('hide');
					 myTable.ajax.reload( null, false );
					 dialog.init(function()
					 {
						 dialog.find('.bootbox-body').html('<i class="fa fa-check" aria-hidden="true"></i> Este contrato ha sido cancelado correctamente');
						 dialog.find('.oculto').removeClass('oculto');
					 });
					 mensaje("success",p.mensaje);
				 }
				 else
				 {
					 dialog.init(function()
					 {
						 dialog.find('.bootbox-body').html('<i class="fa fa-times" aria-hidden="true"></i> No se pudo cancelar este contrato');
						 dialog.find('.oculto').removeClass('oculto');
					 });
					 mensaje("error",p.mensaje);
				 }
			 })
			 .always(function(p)
			 {
				 icon.html(iconOriginal);
				 $("#selectMotivo").attr("disabled",false);
				 $("#btnEliminarModal").attr("disabled",false);
				 $("#btnEliminarModalCancelar").attr("disabled",false);
				 console.log(p);
			 })
			 .error(function()
			 {
				 mensaje("error", "No hay conexión con el servidor. Revisa tu conexión a internet y vuelve a intentarlo");
			 });
			}
			function reactivarContrato()
			{
				icon = $("#btnReactivarModal");
				iconOriginal = icon.html();
				icon.html(cargarSpinner+" Espera...");
				icon.attr("disabled",true);
				$("#btnReactivarModalCancelar").attr("disabled", true);
				idPac_ = $("#hiddenEliminar").val();
			 $.ajax(
			 {
				 method: "POST",
				 url:"assets/ajax/reactivarContrato.php",
				 data: {idCliente:idPac_}
			 })
			 .done(function(p)
			 {
				 console.log(p);
				 if (p.status == 1)
				 {
					 $('#my-modal-reactivar').modal('hide');
					 myTable.ajax.reload( null, false );
					 dialog.init(function()
					 {
						 dialog.find('.bootbox-body').html('<i class="fa fa-check" aria-hidden="true"></i> Este contrato ha sido reactivado correctamente');
						 dialog.find('.oculto').removeClass('oculto');
					 });
					 mensaje("success",p.mensaje);
				 }
				 else
				 {
					 dialog.init(function()
					 {
						 dialog.find('.bootbox-body').html('<i class="fa fa-times" aria-hidden="true"></i> No se pudo reactivar este contrato');
						 dialog.find('.oculto').removeClass('oculto');
					 });
					 mensaje("error",p.mensaje);
				 }
				})
				.always(function(p)
				{
					icon.html(iconOriginal);
					$("#btnReactivarModal").attr("disabled",false);
					$("#btnReactivarModalCancelar").attr("disabled",false);
					console.log(p);
				})
				.error(function()
				{
					mensaje("error", "No hay conexión con el servidor. Revisa tu conexión a internet y vuelve a intentarlo");
				});
			}
			function recargarPagos()
			{
				$.ajax(
				{
					method: "POST",
					url:"assets/ajax/obtenerRowDet_pagos.php",
				})
				.done(function(p)
				{
					$("#selectFolio").html(p);
					$('.chosen-select').trigger('chosen:updated');
					if(!ace.vars['touch'])
					{
						$('.chosen-select').chosen({allow_single_deselect:true});
						//resize the chosen on window resize
						$(window)
						.off('resize.chosen')
						.on('resize.chosen', function() {
							$('.chosen-select').each(function() {
								 var $this = $(this);
								 $this.next().css({'width': $this.parent().width()});
							})
						}).trigger('resize.chosen');
						//resize chosen on sidebar collapse/expand
						$(document).on('settings.ace.chosen', function(e, event_name, event_val) {
							if(event_name != 'sidebar_collapsed') return;
							$('.chosen-select').each(function() {
								 var $this = $(this);
								 $this.next().css({'width': $this.parent().width()});
							});
						});
					}
					$('.chosen-select').each(function() {
						 var $this = $(this);
						 $this.next().css({'width': $this.parent().width()});
					})
				})
				.always(function(p)
				{
					console.log(p);
				})
				.error(function()
				{
					mensaje("error","No hay conexión con el servidor, vuelve a itentarlo");
				});
			}
			function format ( d )
			{
				console.log(d);
				html = '<div class="col-xs-12">';
				html +='    <div class="table-responsive">';
				html +='       <table class="table table-striped table-bordered table-hover">';
				html +='			<tr>';
				html +='				<td>';
				html +='					<b>Comentarios:</b><br> '+d.estadoContrato;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Fecha creación:</b><br> '+d.fechaCreacion;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Fecha primer aportación:</b><br> '+d.fechaPrimerAportacion;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Sucursal:</b><br> '+d.sucursal;
				html +='				</td>';
				html +='			</tr>';
				html +='			<tr>';
				html +='				<td>';
				html +='					<b>Domicilio del contrato:</b><br> '+d.domicilio;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Referencias del domicilio:</b><br> '+d.referencias;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Rfc Cliente:</b><br> '+d.rfc;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Total aportaciones:</b><br> '+d.noAportaciones;
				html +='				</td>';
				html +='			</tr>';
				html +='			<tr>';
				html +='				<td>';
				html +='					<b>Teléfono:</b><br> '+d.telefono;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Celular:</b><br> '+d.celular;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Plan contratado:</b><br> '+d.nombrePlan;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Aportaciones registradas:</b><br> '+d.aportacionesReg;
				html +='				</td>';
				html +='			</tr>';
				html +='			<tr>';
				html +='				<td>';
				html +='					<b>Observaciones del contrato:</b><br> '+d.observaciones;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Id Vendedor:</b><br> '+d.idVendedor;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Nombre vendedor:</b><br> '+d.nombreVendedor;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Tasa comisión vendedor:</b><br> '+d.tasaComision+'%';
				html +='				</td>';
				html +='			</tr>';
				html +='			<tr>';
				html +='				<td>';
				html +='					<b>Costo del plan:</b><br> '+d.precio;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Primer aportación:</b><br> '+d.primerAportacion;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Aportaciónes de:</b><br> '+d.aportacion;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Monto abonado:</b><br> '+d.abonado;
				html +='				</td>';
				html +='			</tr>';
				html +='			<tr>';
				html +='				<td>';
				html +='					<b>Costo total:</b><br> '+d.costoTotal;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Descuento por duplicación de inversión:</b><br> '+d.descuentoDuplicacionInversion;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Descuento por cambio de funeraria:</b><br> '+d.descuentoCambioFuneraria;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Descuento adicional:</b><br> '+d.descuentoAdicional;
				html +='				</td>';
				html +='			</tr>';
				html +='		</table>';
				html +='	</div>';
				html +='</div>';
				html +='<div class="col-xs-8">';
				html +='<div class="col-xs-12 widget-container-col ui-sortable" id="widget-container-col-12">'+
							'<div class="widget-box collapsed transparent ui-sortable-handle" id="widget-box-12">'+
								'<div class="widget-header text-center">'+
									'<h4 class="widget-title lighter pointer">Mostrar historial de pagos</h4>'+
									'<div class="widget-toolbar no-border">'+
										'<a class="blue" href="#" data-action="collapse">'+
											'<i class="ace-icon fa fa-search-plus" data-icon-show="fa-search-plus" data-icon-hide="fa-search-minus""></i>'+
										'</a>'+
									'</div>'+
								'</div>'+
								'<div class="widget-body"> '+
									'<table class="table table-striped table-bordered table-hover" style="width:100%">'+
									'	<th>Id Pago</th>'+
									'	<th>Folio físico</th>'+
									'	<th>Fecha</th>'+
									'	<th>Aportación</th>'+
									'	<th>Saldo</th>'+
									'	<th>F. pago</th>'+
									'	<th></th>'+
									d.html_hist+
								'	</table>'+
								'</div>'+
							'</div>'+
						'</div>';
				html +='</div>';
				return html;
			}

			$(document).ready(function()
			{
				abrirMenu();
				$(document).on('click','.aCancelar',function()
				{
					idEliminar = $(this).attr('name');
					$("#hiddenEliminar").val(idEliminar);
					$("#my-modal-eliminar").modal();
				});
				$(document).on('click','#btnEliminarModal',function()
				{
					$(".has-error").removeClass('has-error');
					dialog = bootbox.dialog(
					{
					    title: '¿Seguro que quieres cancelar este contrato?',
					    message: '<i class="fa fa-question-circle-o" aria-hidden="true"></i> ¿Estás seguro que quieres cancelar este contrato? Presiona "Continuar" para continuar o "cancelar" si quieres regresar',
						closeButton: false,
						buttons:
						{
							"Sí" :
							{
								"label" : '<i class="fa fa-check" aria-hidden="true"></i> Continuar',
								"className" : "btn btn-primary",

								callback: function()
								{
									cancelarContrato();
								}
							},
							"No" :
							{
								"label" : '<i class="fa fa-times" aria-hidden="true"></i> Cancelar',
								"className" : "btn btn-light",

								callback: function(result)
								{
								}
							},
						}
					});
				});

				$(document).on('click','.aReactivar',function()
				{
					idEliminar = $(this).attr('name');
					$("#hiddenEliminar").val(idEliminar);
					$("#my-modal-reactivar").modal();
				});
				$(document).on('click','#btnReactivarModal',function()
				{
					$(".has-error").removeClass('has-error');
					dialog = bootbox.dialog(
					{
					    title: '¿Seguro que quieres reactivar este contrato?',
					    message: '<i class="fa fa-question-circle-o" aria-hidden="true"></i> ¿Estás seguro que quieres reactivar este contrato? Presiona "Continuar" para continuar o "cancelar" si quieres regresar',
						closeButton: false,
						buttons:
						{
							"Sí" :
							{
								"label" : '<i class="fa fa-check" aria-hidden="true"></i> Continuar',
								"className" : "btn btn-primary",

								callback: function()
								{
									reactivarContrato();
								}
							},
							"No" :
							{
								"label" : '<i class="fa fa-times" aria-hidden="true"></i> Cancelar',
								"className" : "btn btn-light",

								callback: function(result)
								{
								}
							},
						}
					});
				});

				tablaDifuntos =
				$('#tabla-agregar-difunto')
				//.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
				.DataTable( {
					"ajax":			"assets/ajax/listarDifuntosSinAsignar.JSON.php",
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
						{ "data": "nombreDifunto" },
						{ "data": "rfc" },
						{ "data": "direccion" },
						{
							"data": "btnAgregar",
							"className": 'text-center',
						 }
					],
					"order": 		[[1, 'asc']]
				} );
				myTable =
				$('#dynamic-table')
				//.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
				.DataTable( {
					"ajax":
					{
						"url":"assets/ajax/listarContratosActivos.JSON.php",
						"complete": function()
						{
							myTable.columns.adjust().draw();
							$("#tabladiv").show();
						}
					},
					"aLengthMenu":[
						[10, 20, 30, 50],
						[10, 20, 30, 50]
					],
					"processing": 	true,
					"initComplete": function(settings, json)
					{
					    //armarHorario();
					},
					"language":
		            {
		                 "url": "assets/js/custom/Spanish.json"
		             },
			        "columns":		[
						{
 			                "className":      	'details-control',
 			                "orderable":      	false,
 			                "data":           	null,
 			                "defaultContent": 	'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
 			            },
						{ "data": "id" },
						{ "data": "folio" },
						{
							"className":      	'text-left',
							"data": "nombresTitular"
						},
						{
							"className":      	'text-center',
							"data": 			"nombreDifunto"
						},
			            {
							"className":      	'text-center',
							"data": "frecuenciaPago"
						},
						{
							"className":      	'text-center',
							"data": "precio"
						},
						{
							"className":      	'text-right',
							"data": "abonado"
						},
			            {
							"className":      	'text-right',
							"data": "resta"
						},
						{
							"className":      	'text-center',
							"data": "estatus_cobranza"
						},
			            // { "data": "direccion" },
			            {
							"className":      	'text-right',
							"data": 			"btns",
						 	"orderable":      	false
						}
			        ],
					'createdRow': function( row, data, dataIndex ) {
					     $(row).attr('id', data.id);
					 },
			        "order": 		[[1, 'desc']]
			    } );

				$.fn.dataTable.Buttons.defaults.dom.container.className = 'dt-buttons btn-overlap btn-group btn-overlap';

				new $.fn.dataTable.Buttons( myTable, {
					buttons: [
					{
						"extend": "copy",
						"text": "<i class='fa fa-copy bigger-110 pink'></i> <span class='hidden'>Copar al portapapele</span>",
						"className": "btn btn-white btn-primary btn-bold"
					},
					{
						"extend": "csv",
						"text": "<i class='fa fa-database bigger-110 orange'></i> <span class='hidden'>Exportar a Excel</span>",
						"className": "btn btn-white btn-primary btn-bold"
					},
					{
						"extend": "excel",
						"text": "<i class='fa fa-file-excel-o bigger-110 green'></i> <span class='hidden'>Exportar a Excel</span>",
						"className": "btn btn-white btn-primary btn-bold"
					},
					{
						"extend": "pdf",
						"text": "<i class='fa fa-file-pdf-o bigger-110 red'></i> <span class='hidden'>Exportar a PDF</span>",
						"className": "btn btn-white btn-primary btn-bold"
					},
					{
						"extend": "print",
						"text": "<i class='fa fa-print bigger-110 grey'></i> <span class='hidden'>Imprimir</span>",
						"className": "btn btn-white btn-primary btn-bold",
						autoPrint: true,
						message: 'Reporte de registros activos'
					}
					]
				} );
				myTable.buttons().container().appendTo( $('.tableTools-container') );
				setTimeout(function() {
					$($('.tableTools-container')).find('a.dt-button').each(function() {
						var div = $(this).find(' > div').first();
						if(div.length == 1) div.tooltip({container: 'body', title: div.parent().text()});
						else $(this).tooltip({container: 'body', title: $(this).text()});
					});
				}, 500);

				/********************************/
				//add tooltip for small view action buttons in dropdown menu
				$('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});

				//tooltip placement on right or left
				function tooltip_placement(context, source)
				{
					var $source = $(source);
					var $parent = $source.closest('table')
					var off1 = $parent.offset();
					var w1 = $parent.width();

					var off2 = $source.offset();
					//var w2 = $source.width();

					if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) return 'right';
					return 'left';
				}
				/***************/
				$('.show-details-btn').on('click', function(e) {
					e.preventDefault();
					$(this).closest('tr').next().toggleClass('open');
					$(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
				});
				$(document).on('click', 'td.details-control', function ()
				{
			        var tr = $(this).closest('tr');
			        var row = myTable.row( tr );
					if ( row.child.isShown() )
					{
						row.child.hide();
						tr.removeClass('shown');
					}
					else
					{
						idCliente = tr.attr("id");
						$(this).html(cargarSpinner);
						infoRow = $.ajax(
						{
							method: "POST",
							url:"assets/ajax/obtenerRowDet_contrato.php",
							dataType:'JSON',
							data: {idCliente:idCliente}
						})
						.always(function(p)
						{
							console.log(p);
							$("td.details-control").empty();
						})
						.error(function()
						{
							mensaje("error","No hay conexión con el servidor, vuelve a itentarlo");
						});
						$.when(infoRow).done(function(dataRow)
						{
							if (dataRow.status == 0)
							{
								mensaje("error",dataRow.mensaje);
								return false;
							}
				            row.child( format(dataRow) ).show();
				            tr.addClass('shown');
						});
					}
			    } );
				$('[data-rel=tooltip]').tooltip();
				function agregarPago(idContrato, monto, formaPago, idFolio)
				{
					icon = $("#btnAgregarPago");
					iconOriginal = icon.html();
					icon.html(cargarSpinner+" Espera...");
					icon.attr("disabled",true);
					$("#selectFormaPago").attr("disabled",true);
					$("#inputPagaCon").attr("disabled",true);
					$("#btnCancelarPago").attr("disabled",true);
					// $("#selectFolio").attr("disabled",true);
					$.ajax(
			        {
			            method: "POST",
			            url:"assets/ajax/agregarPagoContrato.php",
			            data: {idContrato:idContrato,monto:monto,formaPago:formaPago,idFolio:idFolio}
			        })
			        .done(function(p)
			        {
						console.log(p);
						if (p.status == 0)
						{
							$("#"+p.focus).parent().parent().addClass('has-error');
							mensaje("error",p.mensaje);
						}
						else if (p.status == 1)
						{
							mensaje("success",p.mensaje);
							$('#modal-agregar-pago').modal('hide');
							myTable.ajax.reload( null, false );
						}
						else
						{
							mensaje("success",p.mensaje);
							mensaje("info",p.mensaje2);
							$('#modal-agregar-pago').modal('hide');
							myTable.ajax.reload( null, false );
						}
			        })
			        .always(function(p)
			        {
						icon.html(iconOriginal);
						$("#selectFormaPago").attr("disabled",false);
						$("#inputPagaCon").attr("disabled",false);
						$("#btnCancelarPago").attr("disabled",false);
						icon.attr("disabled",false);

						console.log(p);
			        })
					.error(function()
					{
						mensaje("error", "Error al intentar conectar al servidor. No hay conexión");
					});
				}
				function asignarDifunto(idContrato,idDifunto,observaciones,selectCajonUrna)
				{
					console.log(idContrato);
					console.log(idDifunto);
					$("#btnAsignarDifuntoHidden").attr("disabled",true);
					icon = $("#btnAsignarDifunto");
					iconOriginal = icon.html();
					icon.html(cargarSpinner+" Espera...");
					icon.attr("disabled",true);
					$.ajax(
			        {
			            method: "POST",
			            url:"assets/ajax/asignarDifuntoContrato.php",
			            data: {idContrato:idContrato,idDifunto:idDifunto,observaciones:observaciones,selectCajonUrna:selectCajonUrna}
			        })
			        .done(function(p)
			        {
						console.log(p);
						if (p.status == 0)
						{
							mensaje("error",p.mensaje);
						}
						else
						{

							mensaje("success",p.mensaje);
							$('#modal-asignar-difunto').modal('hide');
							myTable.ajax.reload( null, false );
						}
			        })
			        .always(function(p)
			        {
						icon.html(iconOriginal);
						icon.attr("disabled",false);
						$("#btnAsignarDifuntoHidden").attr("disabled",false);
						console.log(p);
			        })
					.error(function()
					{
						mensaje("error", "Error al intentar conectar al servidor. No hay conexión. Por favor vuelve aintentarlo");
					});
				}
				$(document).on("click","#buttonBuscarDifunto",function()
				{
					tablaDifuntos.ajax.reload();
					// $("#hiddenIdContrato").val($(this).attr("name"));
					// $("#spanContratoDifunto").text($(this).attr("name"));
					$("#modal-buscar-difunto").modal();
					// $("#modal-buscar-difunto").modal();
				});
				$(document).on("click",".btnAgregar",function()
				{
					$("#modal-aServicio").modal();
				});
				$(document).on("click","h4.widget-title",function()
				{
					$(this).next().find(".blue").click();
				});
				$("#btnAgregarPago").click(function()
				{
					$(".has-error").removeClass('has-error');
					dialog = bootbox.dialog(
					{
					    title: '¿Seguro que quieres agregar este pago?',
					    message: '<i class="fa fa-question-circle-o" aria-hidden="true"></i> ¿Estás seguro que quieres agregar este pago? Presiona "Agregar" para continuar o "cancelar" si quieres regresar',
						closeButton: false,
						buttons:
						{
							"Sí" :
							{
								"label" : '<i class="fa fa-check" aria-hidden="true"></i> Agregar',
								"className" : "btn btn-primary",

								callback: function()
								{

									idContrato = $("#hiddenIdContrato").val();
									monto = $("#inputPagaCon").val();
									formaPago = $("#selectFormaPago").val();
									idFolio = $("#selectFolio").val();
									agregarPago(idContrato,monto,formaPago,idFolio);
								}
							},
							"No" :
							{
								"label" : '<i class="fa fa-times" aria-hidden="true"></i> Cancelar',
								"className" : "btn btn-light",

								callback: function(result)
								{
								}
							},
						}

					});
				});
				$(document).on("click",".aAgregarDifunto",function()
				{
					nombreDifunto 	= $(this).attr("nombre-difunto");
					idDifunto		= $(this).attr("id");
					$("#idDifuntoHidden").val(idDifunto);
					$("#nombreDifunto").val(nombreDifunto);
					$("#modal-buscar-difunto").modal("hide");
				});
				$(document).on("click","#btnAsignarDifunto",function()
				{
					idContrato 		= $("#hiddenIdContrato").val();
					observaciones 	= $("#observacionesAgregarDifunto").val();
					nombreDifunto 	= $("#nombreDifunto").val();
					idDifunto 		= $("#idDifuntoHidden").val();
					selectCajonUrna = $("#selectCajonUrna").val();
					$(".has-error").removeClass('has-error');
					dialog = bootbox.dialog(
					{
					    title: '¿Seguro que quieres asignar este difunto?',
					    message: '<i class="fa fa-question-circle-o" aria-hidden="true"></i> ¿Estás seguro que quieres asignar el difunto <b>'+nombreDifunto+'</b> a este contrato? Presiona "Agregar" para continuar o "cancelar" si quieres regresar',
						closeButton: false,
						buttons:
						{
							"Sí" :
							{
								"label" : '<i class="fa fa-check" aria-hidden="true"></i> Agregar',
								"className" : "btn btn-primary",

								callback: function()
								{
									asignarDifunto(idContrato,idDifunto,observaciones,selectCajonUrna);
								}
							},
							"No" :
							{
								"label" : '<i class="fa fa-times" aria-hidden="true"></i> Cancelar',
								"className" : "btn btn-light",

								callback: function(result)
								{
								}
							},
						}

					});
				});
				$(document).on("click",".aModalPago",function()
				{
					icon = $(this);
					idContrato = icon.attr('name');
					iconOriginal = icon.html();
					icon.html(cargarSpinner);
					$.ajax(
			        {
			            method: "POST",
			            url:"assets/ajax/infoContrato.php",
			            data: {idContrato:idContrato}
			        })
			        .done(function(p)
			        {
						console.log(p);
						if (p.status == 0)
						{
							mensaje("error",p.mensaje);
						}
						else
						{
							$("#hiddenIdContrato").val(p.id);
							$("#spanContrato").text(p.id);
							$("#spanSaldo").text(p.saldo);
							$("#inputPagaCon").val(p.aportacion);
							$("#inputNombreTitular").val(p.titular);
							$('#modal-agregar-pago').modal();
						}
			        })
			        .always(function(p)
			        {
						console.log(p);
						icon.html(iconOriginal);
			        })
					.error(function()
					{
						mensaje("error", "Error al intentar conectar al servidor. No hay conexión");
					});
				});
				$(document).on("click",".aAsignarDifunto",function()
				{
					icon = $(this);
					idContrato = icon.attr('name');
					iconOriginal = icon.html();
					icon.html(cargarSpinner);
					$.ajax(
			        {
			            method: "POST",
			            url:"assets/ajax/infoContrato.php",
			            data: {idContrato:idContrato}
			        })
			        .done(function(p)
			        {
						console.log(p);
						if (p.status == 0)
						{
							mensaje("error",p.mensaje);
						}
						else
						{
							$("#hiddenIdContrato").val(p.id);
							$("#spanContratoDifunto").text(p.id);
							$("#observacionesAgregarDifunto").val(p.observaciones);
							$("#spanFolioContratoDifunto").text(p.folio);
							$("#inputNombreTitularAsignar").val(p.titular);
							$("#nombreDifunto").val("");
							$("#idDifuntoHidden").val(0);
							$("#selectCajonUrna").val(0);
							$('#modal-asignar-difunto').modal();
						}
			        })
			        .always(function(p)
			        {
						console.log(p);
						icon.html(iconOriginal);
			        })
					.error(function()
					{
						mensaje("error", "Error al intentar conectar al servidor. No hay conexión");
					});
				});
				$("#modal-table-prod").on('show.bs.modal', function()
				{
					objetoAgregarProd.length = 0;
					tablaProd.ajax.reload();
					html  = '<tr>';
					html +=	'	<td colspan="4">';
					html +=	'		<span class="text-muted">';
					html +=	'			<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>';
					html +=	'			No hay ningún producto en esta lista';
					html +=	'		</span>';
					html +=	'	</td>';
					html += '</tr>';
					$("#tbodyListaProductos").empty();
					$("#tbodyListaProductos").prepend(html);
				});
				$('#modal-agregar-pago').on("shown.bs.modal",function()
				{
					recargarPagos();
					$("#selectFormaPago").val(1);
					$("#inputPagaCon").select().focus();

				});
			});

		</script>
	</body>
</html>
<?php
}
 ?>
