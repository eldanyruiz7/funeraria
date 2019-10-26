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
		$permiso = $usuario->permiso("agregarProducto",$mysqli);
		if (!$permiso)
		{
			header("Location: listarProductos.php");
		}
		$modificar = FALSE;
		if (isset($_GET['idProducto']))
		{
			// echo "aaaaaaaaaaaaaaaaaaaaaa".$_GET['idProducto'];
			if (is_numeric($_GET['idProducto']))
			{
				$idProducto = $_GET['idProducto'];
				$sql = "SELECT
							id 				AS idProducto,
							nombre			AS nombre,
							descripcion		AS descripcion,
							precio			AS precio,
							imagen			AS imagen
						FROM cat_productos
						WHERE id = ? AND activo = 1 LIMIT 1";
				if ($res = $mysqli->prepare($sql))
				{
				    $res->bind_param("i", $idProducto);
				    $res->execute();
					$res->store_result();
				    if ($res->num_rows == 1)
					{
						$modificar = TRUE;
						$res->bind_result($idProducto_, $nombre_, $descripcion_, $precio_, $imagen_);
						$res->fetch();
					}
				}
			}
		}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title><?php echo $modificar ? 'Modificar producto' : 'Agregar producto';?></title>

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
							<li class="active"><?php echo $modificar ? 'Modificar producto' : 'Agregar producto';?></li>
						</ul><!-- /.breadcrumb -->
					</div>
					<div class="page-content">
						<div class="page-header">
							<h1>
								<i class="fa fa-users" aria-hidden="true"></i> <?php echo $modificar ? 'Modificar producto' : 'Agregar producto';?>
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									<?php echo $modificar ? 'Modificar producto' : 'Agregar un nuevo producto';?>
								</small>
							</h1>
						</div><!-- /.page-header -->
						<div class="row">
							<div class="col-xs-12">
								<div class="col-xs-2 center">
									<div>
										<span class="profile-picture" id="status" style="min-width:100%;color:darkgray">
						<?php
							if ($modificar && strlen($imagen_) > 0)
							{
								$im = file_get_contents("assets/images/avatars/productos/$imagen_.jpg");
								$imdata = base64_encode($im);
						?>
											<img class="vistaPrevia img-thumbnail" style="cursor:zoom-in" width="100%" height="100%" src="data:image/jpeg;base64,<?php echo $imdata;?>"/>
						<?php
							}
							else
							{
								$imdata = "";
						?>
											<br><i class="fa fa-file-image-o fa-5x"></i><br>&nbsp;
						<?php
							}
						 ?>
											<!--<img id="avatar" class="editable img-responsive editable-click editable-empty" src="assets/images/avatars/profile-pic.jpg" style="display: block;">-->
										</span>
									</div>
									<div class="space space-4"></div>
						<?php
							if ($modificar && strlen($imagen_) > 0)
							{
						?>
									<button class="btn btn-sm btn-block btn-success" id="btnEliminarImg">
										<i class="fa fa-times" aria-hidden="true"></i> Eliminar
									</button>
						<?php
							}
							else
							{
						?>
									<button class="btn btn-sm btn-block btn-info" id="btnAnadirImg">
										<i class="fa fa-plus-circle" aria-hidden="true"></i> Imagen
									</button>
						<?php
							}
						?>
									<div class="col-lg-12" style="margin-bottom:-1px;">
			                            <div class="progress progress-striped active" id="progressUpload" style="display:none">
											<!-- <div class="progress progress-striped active" id="progressUpload"> -->

			                                <div id="divfileupload" class="progress-bar progress-bar-info" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%">
			                                    <span class="sr-only">0% Completado</span>
			                                </div>
			                            </div>
			                        </div>
									<div class="col-lg-12" id="divRespuesta-Img" style="padding-left:0px;padding-right:0px;padding-top:10px;">
									</div>
								</div>
								<div class="col-xs-10">
									<form id="formfileupload" method="POST" action="assets/ajax/subirImg.php" enctype="multipart/form-data" style="margin: 0px; padding: 0px;">
										<input type="file" id="inputFileImagen" name="inputFileImagen" accept="image/*" style="display:none">
										<input type="submit" value="Subir" id="submitImagen" style="display:none">
										<input type="reset" id="inputReset" value="reset" style="display:none">
										<input type="hidden" id="hiddenImgTipo" value="">
									</form>
									<form class="form-horizontal" id="form" role="form">
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right"> Nombre(*) </label>

											<div class="col-sm-8">
												<input type="text" id="nombre" name="nombre" value="<?php echo $modificar == TRUE ? $nombre_ : '';?>" placeholder="Nombre del producto" class="col-xs-7">
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right"> Descripción </label>

											<div class="col-sm-8">
												<input type="text" id="descripcion" name="descripcion" value="<?php echo $modificar == TRUE ? $descripcion_ : '';?>" placeholder="Descripción del producto" class="col-xs-12">
											</div>
										</div>

										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right"> Precio al público $ (*) </label>

											<div class="col-sm-8">
												<input type="number" id="precio" name="precio" min="0" value="<?php echo $modificar == TRUE ? $precio_ : '0';?>" class="col-xs-3"style="text-align:right">
											</div>
										</div>
										<input type="hidden" name="hiddenImgBinario" id="hiddenImgBinario" value="<?php echo $modificar ? $imdata : '';?>">
						<?php
							if ($modificar)
							{
						?>
										<input type="hidden" name="hiddenIdProducto" id="hiddenIdProducto" value="<?php echo $idProducto_;?>">
						<?php
							}

						?>
									</form>
								</div>
							</div>
							<div class="col-xs-4 col-xs-offset-4">
								<hr>
								<button class="btn btn-danger btn-white btn-bold btn-info btn-block btn-round" id="btnGuardar">
									<i class="ace-icon fa fa-floppy-o bigger-120 blue"></i>
									<?php echo $modificar ? "Modificar" : "Guardar nuevo producto";?>
								</button>
							</div>
						</div>
								<!-- PAGE CONTENT ENDS -->
					</div><!-- /.col -->
				</div><!-- /.row -->

				<!--/////////////////////modal agregar producto ////////////////////////-->
				<div id="my-modal-confirmar" class="modal fade" tabindex="-1">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h3 class="smaller blue no-margin">
									<span class="smaller lighter blue no-margin">
										<i class="fa fa-user-plus" aria-hidden="true"></i>
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
										<label class="col-xs-12 text-left"><i class="fa fa-question-circle" aria-hidden="true"></i> ¿Deseas modificar este registro? <br/>Estás a punto de modificar este producto</label>
					<?php
						}
						else
						{
					?>
										<label class="col-xs-12 text-left"><i class="fa fa-question-circle" aria-hidden="true"></i> ¿Deseas agregar este registro al sistema? <br/>Estás a punto de agregar un nuevo producto</label>
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
				<!-- /////////////////// Modal visualizar imagen del producto ////////////////////////// -->
				<div id="modal-img" class="modal fade" role="dialog">
					<div class="modal-dialog">

					<!-- Modal content-->
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title">Vista previa</h4>
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
						        	<p class="text-center">Vista previa del producto</p>
						        </div>
								<br>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
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

		<!-- ace scripts -->
		<script src="assets/js/ace-elements.min.js"></script>
		<script src="assets/js/ace.min.js"></script>
		<script src="assets/js/jquery-upload-files/jquery.uploadfile.min.js"></script>
		<script src="assets/js/custom/primario.js"></script>
		<!-- inline scripts related to this page -->
		<script type="text/javascript">
		myTable = "";

			$(document).ready(function()
			{
				abrirMenu();
				$(document).on('click',"#btnAnadirImg",function()
		        {
		            $("#inputFileImagen").click();
		        });
				$("#inputFileImagen").change(function()
		        {
		            $("#submitImagen").click();
		        });
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
				(function() {

		            var bar 			= $('#divfileupload');
		            var percent 		= $('.sr-only');
		            var status 			= $('#status');
					var status2 		= $('#status2');
		            var progressUpload 	= $("#progressUpload");
		            var btnImg 			= $("#btnAnadirImg");
					var btnImg_pos		= $("#btnAnadirImg-pos");
		            var hidden 			= $("#hiddenImgBinario");
					var hiddenTipo		= $("#hiddenImgTipo");
		            $('#formfileupload').ajaxForm({
		                beforeSend: function() {
		                    //status.empty();
		                    var percentVal = '0%';
							$('#divfileupload').width(percentVal);
 	                       	$('.sr-only').html(percentVal);
 	                       	$("#progressUpload").show();
		                },
		                uploadProgress: function(event, position, total, percentComplete) {
		                    var percentVal = percentComplete + '%';
							$('#divfileupload').width(percentVal)
		                    $('.sr-only').html(percentVal);
		                },
		                success: function(xhr) {
		                    var percentVal = '100%';
		                    bar.width(percentVal)
		                    percent.html(percentVal);
		                    if(xhr.exito == 1)
		                    {
		                        $("#btnAnadirImg").addClass('btn-success');
		                        $("#btnAnadirImg").removeClass('btn-info');
		                        $("#btnAnadirImg").html('<i class="fa fa-times" aria-hidden="true"></i> Eliminar');
		                        $("#btnAnadirImg").prop('id','btnEliminarImg');
								// btnImg_pos.addClass('btn-success');
		                        // btnImg_pos.removeClass('btn-info');
		                        // btnImg_pos.html('<i class="fa fa-times" aria-hidden="true"></i> Eliminar');
		                        // btnImg_pos.prop('id','btnEliminarImg-pos');
		                        $("#imgSrc").html(xhr.respuesta);
		                        status.html(xhr.respuesta);
								// status2.html(xhr.respuesta);
								hidden.val(xhr.binario);
								hiddenTipo.val(xhr.tipo);
								// armarInfoGuardar();
		                    }
		                    else
		                    {

		                       // $("#divRespuesta").html(xhr.respuesta);
							   $.gritter.add(
							   {
			   						title: xhr.titulo,
			   						text: xhr.texto,
			   						class_name: 'gritter-error gritter-center'
		   						});
								//$("#divRespuesta-Img").html(xhr.respuesta);
		                        $("#inputReset").click();
		                    }
							// $("#progressUpload").hide();

		                },
		            	complete: function(xhr) {
		            		//status.html(xhr.responseText);
							$('#divfileupload').width(0);
							// $("#progressUpload").hide();
		                    console.log(xhr);
		                    progressUpload.hide();
		                    //$("#inputNombreCorto").focus();
		            	}
		            });
		        })();
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
							"productos" :
							{
								"label" : '<i class="fa fa-tags" aria-hidden="true"></i> Lista de productos',
								"className" : "btn-info btn-white btn-bold btn-round oculto",
								callback: function(result)
								{
									window.location.assign("listarProductos.php");
								}
							},
				<?php
					if (!$modificar)
					{
				?>
							"otro" :
							{
								"label" : '<i class="fa fa-tag" aria-hidden="true"></i> Capturar otro producto',
								"className" : "btn-default btn-white btn-bold btn-round oculto",
								callback: function(result)
								{
									window.location.assign("agregarProducto.php");
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
			            url:<?php echo $modificar ? "'assets/ajax/modificarProducto.php'" : "'assets/ajax/agregarProducto.php'";?>,
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
								dialog.find('.bootbox-body').html('<i class="fa fa-check" aria-hidden="true"></i> Producto <b>'+p.mensaje+'</b> ha sido modificado exitosamente');
				<?php
					}
					else
					{
				?>
								dialog.find('.bootbox-body').html('<i class="fa fa-check" aria-hidden="true"></i> Producto <b>'+p.mensaje+'</b> ha sido creado exitosamente');
				<?php
					}
				?>
								dialog.find('.oculto').removeClass('oculto');
							});
				<?php
					if ($modificar)
					{
				?>
								mensaje('success','Producto '+p.mensaje+' ha sido modificado exitosamente<br><h5><a href="listarProductos.php" class="orange">Lista de productos</a></h5>');
				<?php
					}
					else
					{
				?>
								mensaje('success','Producto '+p.mensaje+' ha sido guardado exitosamente<br><h5><a href="listarProductos.php" class="orange">Lista de productos</a></h5>');
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
