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
		require_once ("assets/php/usuario.class.php");
		$usuario = new usuario($idUsuario,$mysqli);
		$permiso = $usuario->permiso("listarVariablesSistema",$mysqli);
		if (!$permiso)
		{
			header("Location: index.php");
		}
		require_once ("assets/php/query.class.php");
		$query = new Query();
		$rowIdSucursal = $query ->table("cat_usuarios")	->select("idSucursal")
														->where("id", "=", $sesion->get('id'), "i")
														->limit(1)->execute();
		$idSucursal = $rowIdSucursal[0]["idSucursal"];

		$rowSucursal = $query->table("cat_sucursales")	->select("*")
														->where("id","=", $idSucursal, "i")
														->limit(1)->execute();
		$rowSucursal = $rowSucursal[0];
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>Variables del sistema</title>

		<meta name="description" content="Static &amp; Dynamic Tables" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="assets/font-awesome/4.5.0/css/font-awesome.min.css" />
		<!-- text fonts -->
		<link rel="stylesheet" href="assets/css/fonts.googleapis.com.css" />
		<link rel="stylesheet" href="assets/css/jquery.gritter.min.css" />
		<link rel="stylesheet" href="assets/css/jquery-ui.custom.min.css" />

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
							<li class="active">Variables del sistema</li>
						</ul><!-- /.breadcrumb -->
					</div>
					<div class="page-content">
						<div class="page-header">
							<h1>
								<i class="fa fa-cogs" aria-hidden="true"></i> Variables del sistema
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									Lista de las variables del sistema
								</small>
							</h1>
						</div><!-- /.page-header -->
						<div class="row">
							<div class="col-xs-12">
								<form class="form-horizontal" id="form" role="form">
									<h4 class="header blue clearfix">
										Informaci&oacute;n de la sucursal
									</h4>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Nombre de la sucursal(*) </label>

										<div class="col-sm-8">
											<input autocomplete="off" value="<?php echo $rowSucursal['nombre'];?>" type="text" id="nombreSucursal" name="nombreSucursal" placeholder="Nombre de la sucursal" class="col-xs-5">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Lema de la sucursal </label>

										<div class="col-sm-8">
											<input autocomplete="off" value="<?php echo $rowSucursal['lema'];?>" type="text" id="lema" name="lema" placeholder="Lema de la sucursal" class="col-xs-9">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Nombre del representante(*) </label>

										<div class="col-sm-8">
											<input autocomplete="off" value="<?php echo $rowSucursal['representante'];?>" type="text" id="nombreRepresentante" name="nombreRepresentante" placeholder="Nombre del representante" class="col-xs-5">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Domicilio (Calle, No Ext, No Int)(*) </label>

										<div class="col-sm-8">
											<input autocomplete="off" value="<?php echo $rowSucursal['direccion1'];?>" type="text" id="domicilio1" name="domicilio1" placeholder="Domicilio (Calle, No Ext, No Int)" class="col-xs-9">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Domicilio (Colonia, Población, Municipio)(*) </label>

										<div class="col-sm-8">
											<input autocomplete="off" value="<?php echo $rowSucursal['direccion2'];?>" type="text" id="domicilio2" name="domicilio2" placeholder="Domicilio (Colonia, Población, Municipio)" class="col-xs-9">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Código postal (*) </label>

										<div class="col-sm-8">
											<input autocomplete="off" value="<?php echo $rowSucursal['cp'];?>" type="text" id="cp" name="cp" placeholder="Código postal" class="col-xs-3">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Estado (*) </label>

										<div class="col-sm-8">
											<select id="estado" name="estado" class="col-xs-5">
												<?php
													$rowEstados = $query->table("cat_estados")->select("*")->where("activo", "=", 1, "i")->execute();
													foreach ($rowEstados as $rowEstado) {
														if ($rowEstado['id'] == $rowSucursal["estado"]) {
															echo "<option selected value=".$rowEstado['id'].">".$rowEstado['estado']."</option>";
															continue;
														}
														echo "<option value=".$rowEstado['id'].">".$rowEstado['estado']."</option>";
													}
												 ?>
											 </select>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Teléfono 1 </label>
										<div class="col-sm-8">
											<input autocomplete="off" value="<?php echo $rowSucursal['telefono1'];?>" class="col-xs-4 input-mask-phone1" type="text" id="telefono1" name="telefono1">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Teléfono 2 </label>
										<div class="col-sm-8">
											<input autocomplete="off" value="<?php echo $rowSucursal['telefono2'];?>" class="col-xs-4 input-mask-phone2" type="text" id="telefono2" name="telefono2">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Celular </label>
										<div class="col-sm-8">
											<input autocomplete="off" value="<?php echo $rowSucursal['celular'];?>" class="col-xs-4 input-mask-phone1" type="text" id="celular" name="celular">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> RFC </label>

										<div class="col-sm-8">
											<input autocomplete="off" value="<?php echo $rowSucursal['rfc'];?>" type="text" id="rfc" name="rfc" placeholder="RFC de la sucursal" class="col-xs-4">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> CURP </label>

										<div class="col-sm-8">
											<input autocomplete="off" value="<?php echo $rowSucursal['curp'];?>" type="text" id="curp" name="curp" placeholder="CURP" class="col-xs-4">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> E-mail </label>
										<div class="col-sm-8">
											<input autocomplete="off" value="<?php echo $rowSucursal['correo'];?>" class="col-xs-4" type="text" id="E-mail de la sucursal" name="email">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> R&eacute;gimen fiscal </label>

										<div class="col-sm-8">
											<select id="regimen" name="regimen" class="col-xs-9">
												<?php
													$rowRegimenes = $query->table("cat_regimenes_fiscales")->select("id, c_RegimenFiscal, nombre")->where("activo", "=", 1, "i")->execute();
													foreach ($rowRegimenes as $rowRegimen) {
														if ($rowRegimen['id'] == $rowSucursal["idRegimenFiscal"]) {
															echo "<option selected value=".$rowRegimen['id'].">".$rowRegimen['c_RegimenFiscal']." - ".$rowRegimen['nombre']."</option>";
															continue;
														}
														echo "<option value=".$rowRegimen['id'].">".$rowRegimen['c_RegimenFiscal']." - ".$rowRegimen['nombre']."</option>";
													}
												 ?>
											 </select>
										</div>
									</div>
									<div class="col-xs-10 col-xs-offset-1">
										<h5 class="header green clearfix">
											Cl&aacute;usulas del contrato
										</h5>
										<div class="wysiwyg-editor wysiwyg-style2" id="editor1"><?php echo $rowSucursal['clausulasContrato'];?></div>
										<div class="form-group">
											&nbsp;
										</div>
									</div>
									<div class="form-group">
										&nbsp;
									</div>
									<h4 class="header blue clearfix">
										Nómina
									</h4>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Periodo </label>

										<div class="col-sm-8">
											<select id="periodoNomina" name="periodoNomina" class="col-xs-5">
												<?php
													$rowPeriodosNominas = $query->table("periodos_nomina")->select("*")->where("activo", "=", 1, "i")->execute();
													foreach ($rowPeriodosNominas as $rowPeriodoNomina) {
														if ($rowPeriodoNomina['id'] == $rowSucursal["periodoNomina"]) {
															echo "<option selected value=".$rowPeriodoNomina['id'].">".$rowPeriodoNomina['nombre']."</option>";
															continue;
														}
														echo "<option value=".$rowPeriodoNomina['id'].">".$rowPeriodoNomina['nombre']."</option>";
													}
												 ?>
											 </select>
										</div>
									</div>
									<div class="form-group">
										&nbsp;
									</div>
									<h4 class="header blue clearfix">
										Comisiones
									</h4>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Tasa default comisión ventas (%)(*) </label>
										<div class="col-sm-8">
											<input type="number" value="<?php echo $rowSucursal['tasaVenta'];?>" id="tasaVentas" name="tasaVentas" min="1" max="99"class="col-xs-1" style="text-align:right">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Tasa default comisión cobranza (%)(*) </label>
										<div class="col-sm-8">
											<input type="number" value="<?php echo $rowSucursal['tasaCobranza'];?>" id="tasaCobranza" name="tasaCobranza" min="1" max="99" class="col-xs-1" style="text-align:right">
										</div>
									</div>
									<div class="form-group">
										&nbsp;
									</div>
								</form>


							</div>
							<div class="col-xs-4 col-xs-offset-4">
								<hr>
								<button class="btn btn-danger btn-white btn-bold btn-info btn-block btn-round" id="btnGuardar">
									<i class="ace-icon fa fa-floppy-o bigger-120 blue"></i>
									Actualizar Variables
								</button>
							</div>
						</div>
								<!-- PAGE CONTENT ENDS -->
					</div><!-- /.col -->
				</div><!-- /.row -->

				<!--/////////////////////modal eliminar cliente ////////////////////////-->
				<div id="my-modal-confirmar" class="modal fade" tabindex="-1">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h3 class="smaller blue no-margin">
									<span class="smaller lighter blue no-margin">
										<i class="fa fa-cogs" aria-hidden="true"></i>
									</span>
									Actualizar
								</h3>
							</div>

							<div class="modal-body">
								<form class="form-horizontal" role="form" onSubmit="return false">
									<div class="form-group">
										<label class="col-xs-12 text-left"><i class="fa fa-question-circle" aria-hidden="true"></i> ¿Deseas actualizar las variables del sistema? <br/>¡Cuidado! Una configuraci&oacute;n err&oacute;nea podr&iacute;a causar comportamientos inesperados del sistema</label>
									</div>
								</form>
							</div>

							<div class="modal-footer">
								<button class="btn btn-white btn-info btn-bold btn-round" id="btnGuardarModal">
									<i class="ace-icon fa fa-save"></i>
									Actualizar
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
		<script src="assets/js/jquery-ui.custom.min.js"></script>
		<script src="assets/js/bootbox.js"></script>
		<script src="assets/js/jquery.gritter.min.js"></script>
		<script src="assets/js/jquery.maskedinput.min.js"></script>
		<script src="assets/js/bootstrap-wysiwyg.min.js"></script>

		<!-- ace scripts -->
		<script src="assets/js/ace-elements.min.js"></script>
		<script src="assets/js/ace.min.js"></script>
		<script src="assets/js/custom/primario.js"></script>
		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			myTable = "";

			$(document).ready(function()
			{
				abrirMenu();
				////////////////////////////////////////////////////// FUNCIONALIDAD WYSIWYG ////////////////////////////////////////////////////////////////////
				jQuery(function($){
					$('#editor1').ace_wysiwyg({
						toolbar:
						[
							null,
							'fontSize',
							null,
							{name:'bold', className:'btn-info'},
							{name:'italic', className:'btn-info'},
							{name:'underline', className:'btn-info'},
							null,
							{name:'undo', className:'btn-grey'},
							{name:'redo', className:'btn-grey'}
						]
					}).prev().addClass('wysiwyg-style2');
					$("#editor1").attr("contenteditable","");
				});

				$("#btnGuardar").click(function()
				{
					$("#my-modal-confirmar").modal();
					$(".has-error").removeClass('has-error');

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
							}

						}

					});
					$.ajax(
			        {
			            method: "POST",
			            url:"assets/ajax/editarVariablesSistema.php",
			            data: $("form#form").serialize()+"&clausulas="+$("#editor1").html(),
			        })
			        .done(function(p)
			        {
						console.log(p);
						if (p.status == 1)
						{
							dialog.init(function()
							{
							 	dialog.find('.bootbox-body').html('<i class="fa fa-check" aria-hidden="true"></i> ¡&Eacute;xito!');
								dialog.find('.oculto').removeClass('oculto');
								// dialog.find('button')
								// body > div.bootbox.modal.fade.in > div > div > div.modal-footer > button:nth-child(3)
							});
							mensaje('success','La informaci&oacute;n ha sido actualizada exitosamente<br><h5><a href="index.php" class="orange">Lista de contratos</a></h5>');
						}
						else
						{
							$("#"+p.focus).parent().parent().addClass('has-error');
							dialog.init(function()
							{
							 	dialog.find('.bootbox-body').html('<i class="fa fa-times" aria-hidden="true"></i> No se pudo actualizar la informaci&oacute;n');
								dialog.find('.oculto2').removeClass('oculto2');
							});
							mensaje('error','No se pudo actualizar, inténtalo nuevamente<br>'+p.mensaje);
						}
			        })
					.fail(function()
					{
						dialog.find('.bootbox-body').html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> No se pudo actualizar');
						mensaje("error", "No hay conexión con el servidor. Revisa la conexión a internet y vuelve a intentarlo")
						dialog.find('.oculto2').removeClass('oculto2');
					})
			        .always(function(p)
			        {
						console.log(p);
			        });

				});
				$('.input-mask-phone1').mask('(999) 999-9999');
				$('.input-mask-phone2').mask('99(999) 999-9999');

			});
		</script>
	</body>
</html>
<?php
	}
 ?>
