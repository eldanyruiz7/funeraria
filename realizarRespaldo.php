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
		if (!$usuario->tipo == 0 && !$usuario->tipo == 1)
		{
			header("Location: index.php");
		}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>Realizar respaldo</title>

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
							<li class="active">Realizar respaldo</li>
						</ul><!-- /.breadcrumb -->
					</div>
					<div class="page-content">
						<div class="page-header">
							<h1>
								<i class="fa fa-database" aria-hidden="true"></i> Realizar respaldo
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									Enviar respaldo por e-mail
								</small>
							</h1>
						</div><!-- /.page-header -->
						<div class="row">
							<div class="col-xs-12">
							</div>
							<div class="col-xs-4 col-xs-offset-4">
								<hr>
								<button class="btn btn-danger btn-white btn-bold btn-info btn-block btn-round" id="btnGuardar">
									<i class="fa fa-paper-plane-o" aria-hidden="true"></i>
									Enviar por e-mail
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
										<i class="fa fa-paper-plane-o" aria-hidden="true"></i>
									</span>
									Enviar respaldo
								</h3>
							</div>

							<div class="modal-body">
								<form class="form-horizontal" role="form" onSubmit="return false">
									<div class="form-group">
										<label class="col-xs-12 text-left"><i class="fa fa-question-circle" aria-hidden="true"></i> ¿Deseas realizar y enviar respaldo por e-mail? <br/>Nota: Se enviará un respaldo completo al e-mail que tengas registrado en tu cuenta como administrador, en caso de no tener configurado una dirección de e-mail, por favor primero configuralo y despues vuelves aquí.</label>
									</div>
								</form>
							</div>
							<div class="modal-footer">
								<button class="btn btn-white btn-info btn-bold btn-round" id="btnGuardarModal">
									<i class="ace-icon fa fa-paper-plane"></i>
									Enviar respaldo
								</button>
								<button class="btn btn-white btn-success btn-bold btn-round" id="configurar">
									<i class="fa fa-envelope-o" aria-hidden="true"></i>
									Configurar dirección e-mail
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
				$("#configurar").click(function()
				{
					window.location.assign("agregarUsuario.php?idUsuario=<?php echo $usuario->id;?>");
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
					    message: '<p><i class="fa fa-spin fa-spinner fa-pulse"></i> Procesando la información... Espera. Esto puede tomar algunos segundos. No cierres ni salgas de esta ventana.</p>',
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
			            url:"assets/ajax/resp/enviarRespaldo.php"
			        })
			        .done(function(p)
			        {
						console.log(p);
						if (p.status == 1)
						{
							dialog.init(function()
							{
							 	dialog.find('.bootbox-body').html('<i class="fa fa-check" aria-hidden="true"></i> Respaldo enviado correctamente. <br> Revisa la bandeja de entrada y en correos no deseados en tu cuenta de e-mail');
								dialog.find('.oculto').removeClass('oculto');
								// dialog.find('button')
								// body > div.bootbox.modal.fade.in > div > div > div.modal-footer > button:nth-child(3)
							});
							mensaje('success','Respaldo enviado correctamente. <br> Revisa la bandeja de entrada y en correos no deseados en tu cuenta de e-mail<br><h5><a href="index.php" class="orange">Lista de contratos</a></h5>');
						}
						else
						{
							$("#"+p.focus).parent().parent().addClass('has-error');
							dialog.init(function()
							{
							 	dialog.find('.bootbox-body').html('<i class="fa fa-times" aria-hidden="true"></i> No se pudo respaldar. Consúltalo con el Administrador del sistema');
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
			});
		</script>
	</body>
</html>
<?php
	}
 ?>
