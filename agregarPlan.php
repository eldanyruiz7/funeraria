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
		$permiso = $usuario->permiso("agregarPlan",$mysqli);
		if (!$permiso)
		{
			header("Location: listarPlanes.php");
		}
		$modificar = FALSE;
		if (isset($_GET['idPlan']))
		{
			if (is_numeric($_GET['idPlan']))
			{
				$idPlan = $_GET['idPlan'];
				$sql = "SELECT
							id 				AS idPlan,
							nombre			AS nombre,
							descripcion		AS descripcion,
							precio			AS precio,
							imagen			AS imagen,
							activo			AS activo,
							idSucursal		AS idSucursal
						FROM cat_planes
						WHERE id = ? LIMIT 1";
				if ($res = $mysqli->prepare($sql))
				{
				    $res->bind_param("i", $idPlan);
				    $res->execute();
					$res->store_result();
				    if ($res->num_rows == 1)
					{
						$modificar = TRUE;
						$res->bind_result($idPlan_, $nombre_, $descripcion_, $precio_, $imagen_, $activo_, $idSucursal_);
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
		<title><?php echo $modificar ? 'Modificar plan funerario' : 'Agregar plan funerario';?></title>

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
							<li class="active"><?php echo $modificar ? 'Modificar plan funerario' : 'Agregar plan funerario';?></li>
						</ul><!-- /.breadcrumb -->
					</div>
					<div class="page-content">
						<div class="page-header">
							<h1>
								<i class="fa fa-object-group" aria-hidden="true"></i> <?php echo $modificar ? 'Modificar plan funerario' : 'Agregar plan funerario';?>
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									<?php echo $modificar ? 'Modificar plan funerario' : 'Agregar un nuevo plan funerario';?>
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
								$im = file_get_contents("assets/images/avatars/planes/$imagen_.jpg");
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
												<input type="text" id="nombre" autocomplete="off" name="nombre" value="<?php echo $modificar == TRUE ? $nombre_ : '';?>" placeholder="Nombre del nuevo plan funerario" class="col-xs-7">
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right"> Descripción </label>

											<div class="col-sm-8">
												<input type="text" id="descripcion" autocomplete="off" name="descripcion" value="<?php echo $modificar == TRUE ? $descripcion_ : '';?>" placeholder="Descripción del nuevo plan funerario" class="col-xs-12">
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
										<input type="hidden" name="hiddenIdProducto" id="hiddenIdProducto" value="<?php echo $idPlan_;?>">
						<?php
							}

						?>
									</form>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<div class="widget-box ui-sortable-handle widget-color-blue3">
										<div class="widget-header">
											<h4 class="widget-title lighter"><i class="ace-icon fa fa-tags"></i> <i class="ace-icon fa fa-cubes"></i> Lista de productos y servicios disponibles</h5>
										</div>

										<div class="widget-body">
											<div class="widget-main">
												<div>
													<table id="tabla-agregar-prod" style="width:100%" class="table table-responsive table-striped table-bordered table-hover no-margin-bottom">
														<thead>
															<tr>
																<th>Id</th>
																<th>Nombre</th>
																<th>Tipo</th>
																<th>Precio de venta</th>
																<th>Existencias</th>
																<th>Agregar</th>
															</tr>
														</thead>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<div class="widget-box <?php echo $modificar ? 'widget-color-orange' : 'widget-color-green';?>">
										<div class="widget-header widget-header-flat">
											<h4 class="widget-title lighter">
												<i class="ace-icon fa fa-tags"></i> <i class="ace-icon fa fa-cubes"></i>
												<?php echo $modificar ? 'Modificar plan funerario No. <b>'.$idPlan_.'</b>': 'Elementos que se agregar&aacute;n al plan funerario';?>
											</h4>
											<div class="widget-toolbar">
												<div class="btn-group">
													<button data-toggle="dropdown" class="btn btn-info btn-white btn-xs dropdown-toggle" aria-expanded="false">
														<b>Acciones</b>
														<i class="ace-icon fa fa-angle-down icon-on-right"></i>
													</button>

													<ul class="dropdown-menu dropdown-menu-right">
														<li id="btnVistaPrevia" class="pointer">
															<a><i class="ace-icon fa fa-search"></i> Vista previa</a>
														</li>
														<li id="btnGuardarCompra" class="disabled pointer">
															<a><i class="ace-icon fa fa-save"></i> Guardar</a>
														</li>
													</ul>
												</div>
												<!-- <button class="btn btn-white btn-success btn-bold btn-xs" <?php echo $modificar ? '' : 'disabled="disabled"';?> id="btnGuardarCompra" style="background-color: transparent!important;border-color:#FFF!important;color:#FFF!important">
													<i class="ace-icon fa fa-floppy-o bigger-110"></i>
													<?php echo $modificar ? 'Actualizar plan funerario' : 'Guardar plan funerario';?>
												</button> -->
											</div>
											<div class="widget-toolbar">
												Costo del plan $: <strong id="spanTotalPlan"><?php echo $modificar ? $precio_ : '0.00';?></strong>
											</div>
											<div class="widget-toolbar">
												Calculado $: <strong id="spanTotal">0.00</strong>
											</div>
											<div class="widget-toolbar">
												Total elementos: <strong id="spanTotArts">0</strong>
											</div>
											<div class="widget-toolbar">
											</div>
										</div>
										<input type="hidden" id="hiddenIdTicketProd" value="21">
										<div class="widget-body">
											<div class="widget-main no-padding">
												<table class="table table-bordered table-striped">
													<thead class="thin-border-bottom">
														<tr>
															<th>Id</th>
															<th>Nombre</th>
															<th>Precio de Venta</th>
															<th>Cantidad</th>
															<th>Sub total</th>
															<th>Quitar</th>
														</tr>
													</thead>
													<tbody id="tbodyListaProductos">
											<?php
												if ($modificar)
												{
													////////////////////////////// Listar productos ///////////////////////////
													$sql = "SELECT
											                    cat_productos.id                    AS idProducto,
											                    cat_productos.nombre                AS nombreProducto,
																detalle_cat_planes.precio			AS precio,
																detalle_cat_planes.cantidad			AS cantidad
											                FROM cat_productos
															INNER JOIN detalle_cat_planes
															ON cat_productos.id = detalle_cat_planes.idProducto
											                WHERE detalle_cat_planes.idPlan = $idPlan_ AND detalle_cat_planes.activo = 1 AND detalle_cat_planes.idProducto <> 0";
													$res_det_plan 									= $mysqli->query($sql);
													while ($row_det_plan 							= $res_det_plan->fetch_assoc())
													{
														$idEsteProducto 							= $row_det_plan['idProducto'];
														$nombreEsteProducto 						= $row_det_plan['nombreProducto'];
														$precioEsteProducto 						= $row_det_plan['precio'];
														$cantidadEsteProducto 						= $row_det_plan['cantidad'];
														$subTotalEsteProducto 						= $precioEsteProducto * $cantidadEsteProducto;
														echo "	<tr class='trProductoAgregar' servicio='0' name='$idEsteProducto' nombre='$nombreEsteProducto' precio='$precioEsteProducto' cantidad='$cantidadEsteProducto' subTotal='$subTotalEsteProducto'>
					                                                <td> $idEsteProducto</td>
																	<td> $nombreEsteProducto</td>
					                                                <td class='text-right'> $<input type='number' class='text-right inputP_Compra' style='width:80px;border-style:hidden' min='1' step='1' value='$precioEsteProducto'/></td>
					                                                <td class='text-right'>
					                                                    <input type='number' class='text-right inputCantidad' min='1' value='$cantidadEsteProducto' style='width:80px;border-style:hidden'>
					                                                </td>
					                                                <td class='text-right'> $<span class='spanSubTotal'>".$subTotalEsteProducto."</span></td>

					                                                <td class='text-center pointer tdEliminarProd' servicio ='0' data-rel='tooltip' title='Quitar de esta lista' idProd='$idEsteProducto'> <i class='fa fa-times red bigger-160' aria-hidden='true'></i></td>
					                                            </tr>";
													}
													////////////////////////////// Listar servicios ///////////////////////////
													$sql = "SELECT
											                    cat_servicios.id                    AS idProducto,
											                    cat_servicios.nombre                AS nombreProducto,
																detalle_cat_planes.precio			AS precio,
																detalle_cat_planes.cantidad			AS cantidad
											                FROM cat_servicios
															INNER JOIN detalle_cat_planes
															ON cat_servicios.id = detalle_cat_planes.idServicio
											                WHERE detalle_cat_planes.idPlan = $idPlan_ AND detalle_cat_planes.activo = 1 AND detalle_cat_planes.idServicio <> 0";
													$res_det_plan 									= $mysqli->query($sql);
													while ($row_det_plan 							= $res_det_plan->fetch_assoc())
													{
														$idEsteProducto 							= $row_det_plan['idProducto'];
														$nombreEsteProducto 						= $row_det_plan['nombreProducto'];
														$precioEsteProducto 						= $row_det_plan['precio'];
														$cantidadEsteProducto 						= $row_det_plan['cantidad'];
														$subTotalEsteProducto 						= $precioEsteProducto * $cantidadEsteProducto;
														echo "	<tr class='trProductoAgregar' servicio='1' name='$idEsteProducto' nombre='$nombreEsteProducto' precio='$precioEsteProducto' cantidad='$cantidadEsteProducto' subTotal='$subTotalEsteProducto'>
					                                                <td> $idEsteProducto</td>
																	<td> $nombreEsteProducto</td>
					                                                <td class='text-right'> $<input type='number' class='text-right inputP_Compra' style='width:80px;border-style:hidden' min='1' step='1' value='$precioEsteProducto'/></td>
					                                                <td class='text-right'>
					                                                    <input type='number' class='text-right inputCantidad' min='1' value='$cantidadEsteProducto' style='width:80px;border-style:hidden'>
					                                                </td>
					                                                <td class='text-right'> $<span class='spanSubTotal'>".$subTotalEsteProducto."</span></td>

					                                                <td class='text-center pointer tdEliminarProd' servicio='1' data-rel='tooltip' title='Quitar de esta lista' idProd='$idEsteProducto'> <i class='fa fa-times red bigger-160' aria-hidden='true'></i></td>
					                                            </tr>";
													}
												}
												else
												{
											?>
														<tr>
															<td colspan="7">
																<span class="text-muted">
																	<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
																	No hay ningún producto o servicio en esta lista
																</span>
															</td>
														</tr>
											<?php
												}
											 ?>
													</tbody>
												</table>
											</div><!-- /.widget-main -->
										</div><!-- /.widget-body -->
									</div>
								</div>
							</div>
						</div>
								<!-- PAGE CONTENT ENDS -->
					</div><!-- /.col -->
				</div><!-- /.row -->

				<!--/////////////////////modal agregar plan ////////////////////////-->
				<div id="my-modal-confirmar" class="modal fade" tabindex="-1">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h3 class="smaller blue no-margin">
									<span class="smaller lighter blue no-margin">
										<i class="fa fa-plus" aria-hidden="true"></i>
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
										<label class="col-xs-12 text-left"><i class="fa fa-question-circle" aria-hidden="true"></i> ¿Deseas modificar este registro? <br/>Estás a punto de modificar este plan funerario</label>
					<?php
						}
						else
						{
					?>
										<label class="col-xs-12 text-left"><i class="fa fa-question-circle" aria-hidden="true"></i> ¿Deseas agregar este registro al sistema? <br/>Estás a punto de agregar un nuevo plan funerario</label>
					<?php
						}
					 ?>
									</div>
								</form>
							</div>

							<div class="modal-footer">
								<button class="btn btn-white btn-purple btn-bold btn-round" id="btnVistaPreviaModalGuardar" data-dismiss="modal">
									<i class="ace-icon fa fa-search"></i>
									Vista previa
								</button>
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
			objetoAgregarProd	= [];
			function agregarProductoTbody(p)
			{
				for (var i = 0; i < objetoAgregarProd.length; i++)
				{
					if (objetoAgregarProd[i]['id'] == p.idProducto && objetoAgregarProd[i]['servicio'] == p.servicio)
					{
						mensaje("error", objetoAgregarProd[i]['nombre'] + " ya ha sido agregado con anterioridad a la lista");
						return false;
					}
				}
				if (objetoAgregarProd.length == 0)
				{
					$("#tbodyListaProductos").empty();
				}
				id 		= p.idProducto;
				nombre 	= p.nombreProd;
				servicio 	= p.servicio;
				precio 	= p.precioVenta;
				cantidad= 1;
				unshiftObjetoAgregarProd(id, nombre, servicio, precio, cantidad);
				$("#tbodyListaProductos").prepend(p.rowProd);
				mensaje('info','Se agregó '+p.nombreProd+' a la lista');
				actualizarTotal();
				$("#btnGuardarCompra").removeClass("disabled");

			}
			function unshiftObjetoAgregarProd(id,nombre,servicio,precio,cantidad)
			{
				esteProd     				= new producto(
					id,
						nombre,
							servicio,
								precio,
									cantidad);
				objetoAgregarProd.unshift(esteProd);
			}
			function producto(id, nombre, servicio, precio, cantidad)
			{
				this.id 					= id;
				this.nombre 				= nombre;
				this.servicio 				= servicio;
				this.precio 				= precio;
				this.cantidad				= cantidad;
			}
			function actualizarTodo()
			{
				$(".trProductoAgregar").each(function()
				{
					id = $(this).attr("name");
					nombre = $(this).attr("nombre");
					servicio = $(this).attr("servicio");
					precio = $(this).attr("precio");
					cantidad = $(this).attr("cantidad");
					unshiftObjetoAgregarProd(id, nombre, servicio, precio, cantidad);
				});
				actualizarTotal();
			}
			function actualizarTotal()
			{
				var total = 0;
				for (var i = 0; i < objetoAgregarProd.length; i++)
				{
					total += objetoAgregarProd[i]['precio'] * objetoAgregarProd[i]['cantidad'];
				}
				total = parseFloat(total);
				total = total.toFixed(2);
				$("#spanTotal").text(total);
				$("#spanTotArts").text(objetoAgregarProd.length);
			}
			function actualizarEsteProducto(esteId, servicio, esteCantidad, estePrecio)
			{
				romper = false;
				for (var i = 0; i < objetoAgregarProd.length; i++)
				{
					if (objetoAgregarProd[i]['id'] == esteId && objetoAgregarProd[i]['servicio'] == servicio)
					{
						objetoAgregarProd[i]['precio'] = estePrecio;
						objetoAgregarProd[i]['cantidad'] = esteCantidad;
						romper = true;
					}
					if (romper)
					{
						subTotal = parseFloat(esteCantidad * estePrecio);
						$(".trProductoAgregar").each(function()
						{
							if ($(this).attr("name") == esteId && $(this).attr("servicio") == servicio)
							{
								$(this).find(".spanSubTotal").text(subTotal.toFixed(2));
							}
						});
						actualizarTotal();
						return false;
					}
				}
			}
			$(document).on("click",".tdEliminarProd", function()
			{
				idProd = $(this).attr('idprod');
				servicio = $(this).attr('servicio');
				// alert(idProd+" "+servicio);
				for (var i = 0; i < objetoAgregarProd.length; i++)
				{
					if(objetoAgregarProd[i]['id'] == idProd && objetoAgregarProd[i]['servicio'] == servicio)
					{
						var eliminado = objetoAgregarProd.splice(i,1);
						console.log(eliminado);
						if (eliminado.length == 1)
						{
							$(this).parent().remove();
							if (objetoAgregarProd.length == 0)
							{
								html  = '<tr>';
								html +=	'	<td colspan="7">';
								html +=	'		<span class="text-muted">';
								html +=	'			<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>';
								html +=	'			No hay ningún producto o servicio en esta lista';
								html +=	'		</span>';
								html +=	'	</td>';
								html += '</tr>';
								$("#tbodyListaProductos").empty();
								$("#tbodyListaProductos").prepend(html);
								$("#btnGuardarCompra").addClass("disabled");
							}
							else
							{
								$("#btnGuardarCompra").removeClass("disabled");
							}
							mensaje('info','El producto "'+eliminado[0]['nombre']+'" se ha eliminado de la lista de compras');
						}
					}
				}
				actualizarTotal();
			});
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
				$("#btnGuardarCompra, #btnGuardarVistPrevia").click(function()
				{
					if ($(this).hasClass("pointer") && $(this).hasClass("disabled") )
						return false;
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
				$(document).on("click","#btnVistaPrevia, #btnVistaPreviaModalGuardar",function()
				{
					$("#modal-vista-previa").modal();
					var arrayProductosJSON 	= JSON.stringify(objetoAgregarProd);
					nombre 					= $("#nombre").val();
					descripcion 			= $("#descripcion").val();
					precio 					= $("#precio").val();
					// hiddenImgBinario 		= $("#hiddenImgBinario").val();
					$.ajax(
			        {
			            method: "GET",
			            url:"assets/ajax/vistaPreviaPlan.php",
			            data: {nombre:nombre,descripcion:descripcion,precio:precio,arrayProductos:arrayProductosJSON}
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
				tablaProd =
				$('#tabla-agregar-prod')
				//.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
				.DataTable( {
					"ajax":
					{
						"url":"assets/ajax/listarProductosIndex.JSON.php",
						"type": "POST",
						"data": function(d)
						{
							d.listarServicios =  '1';
						}
					},
					"aLengthMenu":[
						[5, 10, -1],
						[5, 10, "Todos"]
					],
					"bInfo" : false,
					"processing": 	false,
					"language":
					{
						 "url": "assets/js/custom/Spanish.json"
					 },
					"columns":		[
						{ "data": "id" },
						{ "data": "nombreProducto" },
						{
							"className":      	'text-center',
							"data": "productoOserv"
						},
						{
							"className":      	'text-right',
							"data": "precioVenta"
						},
						{
							"className":      	'text-right',
							"data": "existencias"
						},
						{
							"className":      	'text-center',
							"data": 			"btns",
							"orderable":      	false
						}
					],
					'createdRow': function( row, data, dataIndex ) {
					     $(row).attr('servicio', data.servicio);
					 },
					"order": 		[[0, 'asc']]
				} );
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
							"planes" :
							{
								"label" : '<i class="fa fa-tags" aria-hidden="true"></i> Lista de planes',
								"className" : "btn-info btn-white btn-bold btn-round oculto",
								callback: function(result)
								{
									window.location.assign("listarPlanes.php");
								}
							},
				<?php
					if (!$modificar)
					{
				?>
							"otro" :
							{
								"label" : '<i class="fa fa-tag" aria-hidden="true"></i> Capturar otro plan',
								"className" : "btn-default btn-white btn-bold btn-round oculto",
								callback: function(result)
								{
									window.location.assign("agregarPlan.php");
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
					var arrayProductosJSON 	= JSON.stringify(objetoAgregarProd);
					nombre 					= $("#nombre").val();
					descripcion 			= $("#descripcion").val();
					precio 					= $("#precio").val();
					hiddenImgBinario 		= $("#hiddenImgBinario").val();

					$.ajax(
			        {
			            method: "POST",
			            url:<?php echo $modificar ? "'assets/ajax/editarPlan.php'" : "'assets/ajax/agregarPlan.php'";?>,
			            data: {nombre:nombre,descripcion:descripcion,precio:precio,hiddenImgBinario:hiddenImgBinario,arrayProductos:arrayProductosJSON<?php echo $modificar ? ",idPlan:$idPlan_" : "";?>}
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
								dialog.find('.bootbox-body').html('<i class="fa fa-check" aria-hidden="true"></i> Plan funerario <b>'+p.mensaje+'</b> ha sido modificado exitosamente');
				<?php
					}
					else
					{
				?>
								dialog.find('.bootbox-body').html('<i class="fa fa-check" aria-hidden="true"></i> Plan funerario <b>'+p.mensaje+'</b> ha sido creado exitosamente');
				<?php
					}
				?>
								dialog.find('.oculto').removeClass('oculto');
							});
				<?php
					if ($modificar)
					{
				?>
								mensaje('success','Plan funerario '+p.mensaje+' ha sido modificado exitosamente<br><h5><a href="listarPlanes.php" class="orange">Lista de planes</a></h5>');
				<?php
					}
					else
					{
				?>
								mensaje('success','Plan funerario '+p.mensaje+' ha sido guardado exitosamente<br><h5><a href="listarPlanes.php" class="orange">Lista de planes</a></h5>');
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
				$(document).on("click",".aAgregarProd", function()
				{
					idProducto = $(this).attr('name');
					servicio = $(this).attr("servicio");
					$.ajax(
			        {
			            method: "POST",
			            url:"assets/ajax/obtenerRowProd_compra.php",
			            data: {idProducto:idProducto,servicio:servicio,agregarPlan:1}
			        })
			        .done(function(p)
			        {
						if (p.status == 1)
						{
							agregarProductoTbody(p);
						}
						else
						{
							mensaje('error',p.respuesta);

						}
			        })
			        .always(function(p)
			        {
			            console.log(p);
			        })
			        .fail(function()
			        {
			            mensaje("error",'No se puede acceder al servidor en este momento, vuelve a intentarlo o consúltalo con el administrador del sistema.');
			        });
				});
				$(document).on("focusout, change",".inputP_Compra, .inputCantidad",function()
				{
					if ($(this).hasClass("inputP_Compra"))
					{
						estePrecio 		= $(this).val();
						estePrecio		= parseFloat(estePrecio);
						esteCantidad 	= $(this).parent().next().find(".inputCantidad").val();
						esteCantidad	= parseInt(esteCantidad);
					}
					if ($(this).hasClass("inputCantidad"))
					{
						estePrecio 		= $(this).parent().prev().find(".inputP_Compra").val();
						estePrecio		= parseFloat(estePrecio);
						esteCantidad 	= $(this).val();
						esteCantidad	= parseInt(esteCantidad);

					}
					esteId 				= $(this).parent().parent().attr("name");
					servicio			= $(this).parent().parent().attr("servicio");
					if (isNaN(estePrecio) || estePrecio < 0.1 || isNaN(esteCantidad) || esteCantidad < 1)
					{
						$(this).focus();
						mensaje("error","El valor introducido debe ser numérico, mayor que cero (0)");
						return false;
					}
					if($(this).hasClass("inputP_Compra"))
					{
						$(this).val(estePrecio.toFixed(2));
					}
					// esteCantidad = parseInt($(this).parent().parent().find(".inputCantidad").val());
					actualizarEsteProducto(esteId, servicio, esteCantidad, estePrecio);
				});
				$(document).on("focusout, change","#precio",function()
				{
					costoPlan = parseFloat($(this).val());
					$("#spanTotalPlan").text(costoPlan.toFixed(2));
				});
				$("#divVistaPrevia").html(cargarSpinner+" Generando, espera por favor...");
				actualizarTodo();
			});
		</script>
	</body>
</html>
<?php
	}
 ?>
