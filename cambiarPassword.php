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
		// $permiso = $usuario->permiso("agregarServicio",$mysqli);
		// if (!$permiso)
		// {
		// 	header("Location: listarServicios.php");
		// }
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>Cambiar mi contraseña</title>

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
							<li class="active">Cambiar mi contraseña</li>
						</ul><!-- /.breadcrumb -->
					</div>
					<div class="page-content">
						<div class="page-header">
							<h1>
								<i class="fa fa-key" aria-hidden="true"></i> Cambiar mi contraseña
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									Mi usuario: <?php echo $usuario->nickName;?>
								</small>
							</h1>
						</div><!-- /.page-header -->
						<div class="row">
							<div class="col-xs-8 col-xs-offset-2">
								<div class="profile-user-info profile-user-info-striped">
									<div class="profile-info-row">
										<div class="profile-info-name"> Nick Name </div>

										<div class="profile-info-value">
											<span><i class="menu-icon blue fa fa-user-o" aria-hidden="true"></i> <?php echo $usuario->nickName;?></span>
										</div>
									</div>
									<div class="profile-info-row">
										<div class="profile-info-name">E mail </div>

										<div class="profile-info-value">
											<span> <i class="menu-icon green fa fa-envelope-o" aria-hidden="true"></i> <?php echo strlen($usuario->email) > 0 ? $usuario->email : '<span class="text-muted">(No registrado)</span>';?></span>
										</div>
									</div>
									<div class="profile-info-row">
										<div class="profile-info-name"> Nombre </div>

										<div class="profile-info-value">
											<span> <i class="menu-icon purple fa fa-address-book-o" aria-hidden="true"></i> <?php echo $usuario->nombres;?></span>
										</div>
									</div>
									<div class="profile-info-row">
										<div class="profile-info-name"> Fecha alta </div>

										<div class="profile-info-value">
											<span> <i class="menu-icon orange fa fa-calendar-check-o" aria-hidden="true"></i> <?php echo $usuario->fechaCreacion();?></span>
										</div>
									</div>
								</div>
								<div class="space-12"></div>
							</div>
							<div class="col-xs-12">
								<form class="form-horizontal" id="form" role="form">
									<hr>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Contraseña actual (*) </label>

										<div class="col-xs-3 no-padding-right">
											<input type="password" autocomplete="off" id="contrasena-actual" name="contrasena-actual" placeholder="Contraseña actual" class="col-xs-12">
										</div>
										<span style="padding-bottom:10px" class="btn btn-white btn-success btn-sm popover-success no-border" data-rel="popover" data-trigger="hover" data-placement="right" title="<i class='ace-icon fa fa-check green'></i> Información" data-content="Para cambiar la contraseña es necesario ingresar tu contraseña actual y en seguida la nueva contraseña 2 veces.<br> Los 2 campos de la nueva contraseña deben coincidir, puedes ingresar letras y números y algunos caracteres especiales como: !,?,%,$,@, etc.<br> La nueva contraseña debe de contener al menos 6 caracteres. <br>La comprobación de caracteres distingue entre mayúsculas y minúsculas<br>Es recomendable guardar la nueva contraseña en un lugar seguro."><i class="fa fa-question-circle" aria-hidden="true"></i></span>
									</div>
									<hr>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Contraseña nueva (*) </label>

										<div class="col-xs-3 no-padding-right">
											<input type="password" autocomplete="off" id="contrasena-nueva1" name="contrasena-nueva1" placeholder="Contraseña nueva" class="col-xs-12">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Repite la contraseña nueva (*) </label>

										<div class="col-xs-3 no-padding-right">
											<input type="password" autocomplete="off" id="contrasena-nueva2" name="contrasena-nueva2" placeholder="Repite la contraseña nueva" class="col-xs-12">
										</div>
									</div>
								</form>
							</div>
							<div class="col-xs-4 col-xs-offset-4">
								<hr>
								<button class="btn btn-danger btn-white btn-bold btn-info btn-block btn-round" id="btnGuardar">
									<i class="ace-icon fa fa-key bigger-120 blue"></i>
									Cambiar mi contraseña
								</button>
							</div>
						</div>
								<!-- PAGE CONTENT ENDS -->
					</div><!-- /.col -->
				</div><!-- /.row -->

				<!--/////////////////////modal eliminar proveedor ////////////////////////-->
				<div id="my-modal-confirmar" class="modal fade" tabindex="-1">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h3 class="smaller blue no-margin">
									<span class="smaller lighter blue no-margin">
										<i class="fa fa-plus-circle" aria-hidden="true"></i>
									</span>
									Cambiar mi contraseña
								</h3>
							</div>

							<div class="modal-body">
								<form class="form-horizontal" role="form" onSubmit="return false">
									<div class="form-group">
										<label class="col-xs-12 text-left"><i class="fa fa-question-circle" aria-hidden="true"></i> ¿Deseas cambiar tu contraseña para acceder al sistema? <br/>Estás a punto de reemplazar tu contraseña</label>
									</div>
								</form>
							</div>

							<div class="modal-footer">
								<button class="btn btn-white btn-info btn-bold btn-round" id="btnGuardarModal">
									<i class="ace-icon fa fa-save"></i>
									Cambiar
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
		<script src="assets/js/jquery.maskedinput.min.js"></script>

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
					    message: '<p><i class="fa fa-spin fa-spinner fa-pulse"></i> Procesando la información... Espera. Esto puede tomar algunos segundos</p>',
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
			            url:"assets/ajax/cambiarPassword.php",
			            data: $("form#form").serialize()
			        })
			        .done(function(p)
			        {
						console.log(p);
						if (p.status == 1)
						{
							dialog.init(function()
							{
							 	dialog.find('.bootbox-body').html('<i class="fa fa-check" aria-hidden="true"></i> Tu contraseña ha sido cambiada exitosamente. <br>La próxima vez que inicies sesión, iníciala con tu nueva contraseña');
								dialog.find('.oculto').removeClass('oculto');
								// dialog.find('button')
								// body > div.bootbox.modal.fade.in > div > div > div.modal-footer > button:nth-child(3)
							});
							mensaje('success','Tu contraseña ha sido cambiada exitosamente. <br>La próxima vez que inicies sesión, iníciala con tu nueva contraseña<br><h5><a href="index.php" class="orange">Lista de contratos</a></h5>');
						}
						else
						{
							$("#"+p.focus).parent().parent().addClass('has-error');
							dialog.init(function()
							{
							 	dialog.find('.bootbox-body').html('<i class="fa fa-times" aria-hidden="true"></i> No se pudo guardar la información');
								dialog.find('.oculto2').removeClass('oculto2');
							});
							mensaje('error','No se pudo guardar, inténtalo nuevamente<br>'+p.mensaje);
						}
			        })
					.fail(function()
					{
						dialog.find('.bootbox-body').html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> No se pudo guardar');
						mensaje("error", "No hay conexión con el servidor. Revisa la conexión a internet y vuelve a intentarlo");
						dialog.find('.oculto2').removeClass('oculto2');

					})
			        .always(function(p)
			        {
						console.log(p);
			        });

				});
				$('.input-mask-phone').mask('(999) 999-9999');
				$('[data-rel=popover]').popover({html:true});

			});
		</script>
	</body>
</html>
<?php
	}
 ?>
