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
		$permiso = $usuario->permiso("agregarCompra",$mysqli);
		if (!$permiso)
		{
			header("Location: listarCompras.php");
		}
		require_once ("assets/php/query.class.php");
		$query = new Query();
		$modificar = FALSE;
		$compraActiva = TRUE;
		if (isset($_GET['idCompra']))
		{
			if (is_numeric($_GET['idCompra']))
			{
				$idCompra = $_GET['idCompra'];
				$resCompra = $query	->table("compras")->select("id AS idCompra, activo AS activo, idProveedor AS idProveedor")
									->where("id", "=", $idCompra, "i") ->limit(1) ->execute();

				if ($query->status() && $query->num_rows())
				{
					$idCompra_ = $resCompra[0]['idCompra'];
					$activo = $resCompra[0]['activo'];
					$idProveedor_ = $resCompra[0]['idProveedor'];
					$modificar = TRUE;
					$compraActiva = ($activo == 1) ? TRUE : FALSE;
					$res_detalle = $query 	->table("detalle_compras") ->select("precioCompra, cantidad")
											->where("idCompra", "=", $idCompra_, "i") ->and()
											->where("activo", "=", 1, "i")->execute();
	                $totalCompra = 0;
					foreach ($res_detalle as $row_detalle)
					{
						$precioCompra = $row_detalle['precioCompra'];
	                    $cantidad = $row_detalle['cantidad'];
	                    $totalCompra += $precioCompra * $cantidad;
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
		<title><?php echo $modificar ? 'Modificar compra' : 'Agregar compra';?></title>

		<meta name="description" content="Static &amp; Dynamic Tables" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="assets/font-awesome/4.5.0/css/font-awesome.min.css" />

		<!-- page specific plugin styles -->

		<!-- text fonts -->
		<link rel="stylesheet" href="assets/css/fonts.googleapis.com.css" />

		<!-- ace styles -->
		<link rel="stylesheet" href="assets/css/jquery.gritter.min.css" />
		<link rel="stylesheet" href="assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
		<!--[if lte IE 9]>
			<link rel="stylesheet" href="assets/css/ace-part2.min.css" class="ace-main-stylesheet" />
		<![endif]-->
		<link rel="stylesheet" href="assets/css/jquery-ui.custom.min.css" />
		<link rel="stylesheet" href="assets/css/chosen.min.css" />
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
			/* td.details-control {
			    background: url('assets/images/icons/details_open.png') no-repeat center center;
			    cursor: pointer;
			}
			tr.shown td.details-control {
			    background: url('assets/images/icons/details_close.png') no-repeat center center;
			} */
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
							<li class="active"><?php echo $modificar ? 'Modificar compra' : 'Agregar una compra';?></li>
						</ul><!-- /.breadcrumb -->

					</div>

					<div class="page-content">
						<div class="page-header">
							<h1>
								<i class="fa fa-shopping-bag" aria-hidden="true"></i> <?php echo $modificar ? 'Modificar compra' : 'Agregar compra';?>
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									<?php echo $modificar ? 'Modificar compra No: <b>'.$idCompra_.'</b>' : 'Agregar una nueva compra';?>
								</small>
							</h1>
				<?php
					if (!$compraActiva)
					{
				?>
							<h1 class="red text-center bigger">
								<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Atención: Esta compra no se puede modificar porque ya ha sido cancelada
							</h1>
				<?php
					}
				 ?>
						</div><!-- /.page-header -->
						<div class="row">
							<div class="col-xs-12">
								<div class="clearfix">
									<div class="pull-right tableTools-container"></div>
								</div>
								<div>
									<table id="tabla-agregar-prod" style="width:100%" class="table table-striped table-bordered table-hover no-margin-bottom no-border-top">
										<thead>
											<tr>
												<th>Id</th>
												<th>Nombre</th>
												<th>Precio de venta</th>
												<th>Último precio compra</th>
												<th>Existencias</th>
												<th>Agregar</th>
											</tr>
										</thead>
									</table>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								<div class="widget-box <?php echo $modificar ? 'widget-color-orange' : 'widget-color-green2';?>">
									<div class="widget-header widget-header-flat">
										<h4 class="widget-title lighter">
											<i class="ace-icon fa fa-tags"></i>
											<?php echo $modificar ? 'Modificar compra No. <b>'.$idCompra_.'</b>': 'Productos que se agregar&aacute;n a la compra';?>
										</h4>
										<div class="widget-toolbar">
											<button class="btn btn-white btn-success btn-bold btn-xs" <?php echo $modificar ? '' : 'disabled="disabled"';?> id="btnGuardarCompra" style="background-color: transparent!important;border-color:#FFF!important;color:#FFF!important">
												<i class="ace-icon fa fa-floppy-o bigger-110"></i>
												<?php echo $modificar ? 'Actualizar compra' : 'Guardar compra';?>
											</button>
										</div>
										<div class="widget-toolbar">
											Total $: <strong id="spanTotal">0.00</strong>
										</div>
										<div class="widget-toolbar">
											Total art&iacute;culos: <strong id="spanTotArts">0</strong>
										</div>
										<div class="widget-toolbar">
											<!-- <div> -->
												<select class="chosen-select form-control" id="proveedor" data-placeholder="Proveedor...">
													<option value="">  </option>
										<?php
											$res_prov = $query->table("cat_proveedores")->select("id, rsocial")->where("activo", "=", 1, "i")->execute();
											foreach ($res_prov as $row_prov)
											{
												$idProv = $row_prov['id'];
												$nombreProv = $row_prov['rsocial'];
												if ($modificar && $idProv == $idProveedor_)
													echo "<option selected value='$idProv'>$nombreProv</option>";
												else
													echo "<option value='$idProv'>$nombreProv</option>";
											}
										 ?>
												</select>
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
														<th>Precio de Compra</th>
														<th>Cantidad</th>
														<th>Sub total</th>
														<th>Quitar</th>
													</tr>
												</thead>
												<tbody id="tbodyListaProductos">
										<?php
											if ($modificar)
											{
												$res_det_compra = $query->table("cat_productos AS cp")->select("cp.id 			AS idProducto,
																												cp.nombre 		AS nombreProducto,
																												dc.precioCompra AS precioCompra,
																												dc.cantidad 	AS cantidad")
																		 ->innerJoin("detalle_compras AS dc", "cp.id", "dc.idProducto")->where("dc.idCompra", "=", $idCompra_, "i")
																		 ->and()->where("dc.activo", "=", 1, "i")->execute();
												foreach ($res_det_compra as $row_det_compra)
												{
													$idEsteProducto = $row_det_compra['idProducto'];
													$nombreEsteProducto = $row_det_compra['nombreProducto'];
													$precioEsteProducto = $row_det_compra['precioCompra'];
													$cantidadEsteProducto = $row_det_compra['cantidad'];
													$subTotalEsteProducto = $precioEsteProducto * $cantidadEsteProducto;
													echo "	<tr class='trProductoAgregar' name='$idEsteProducto' nombre='$nombreEsteProducto' precio='$precioEsteProducto' cantidad='$cantidadEsteProducto' subTotal='$subTotalEsteProducto'>
																<td> $idEsteProducto</td>
																<td> $nombreEsteProducto</td>
																<td class='text-right'> $<input type='number' class='text-right inputP_Compra' style='width:80px;border-style:hidden' min='1' step='1' value='$precioEsteProducto'/></td>
																<td class='text-right'>
																	<input type='number' class='text-right inputCantidad' min='1' value='$cantidadEsteProducto' style='width:80px;border-style:hidden'>
																</td>
																<td class='text-right'> $<span class='spanSubTotal'>".$subTotalEsteProducto."</span></td>
																<td class='text-center pointer tdEliminarProd' data-rel='tooltip' title='Quitar de esta lista' idProd='$idEsteProducto'> <i class='fa fa-times red bigger-160' aria-hidden='true'></i></td>
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
																No hay ningún producto en esta lista
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
								<!-- PAGE CONTENT ENDS -->
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.page-content -->
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
		<script src="assets/js/jquery.dataTables.min.js"></script>
		<script src="assets/js/jquery.dataTables.bootstrap.min.js"></script>
		<script src="assets/js/dataTables.buttons.min.js"></script>
		<script src="assets/js/jquery.gritter.min.js"></script>
		<script src="assets/js/vfs_fonts.js"></script>
		<script src="assets/js/buttons.colVis.min.js"></script>
		<script src="assets/js/dataTables.select.min.js"></script>
		<script src="assets/js/chosen.jquery.min.js"></script>
		<!-- ace scripts -->
		<script src="assets/js/ace-elements.min.js"></script>
		<script src="assets/js/ace.min.js"></script>
		<script src="assets/js/bootbox.js"></script>
		<script src="assets/js/custom/primario.js"></script>
		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			objetoAgregarProd	= [];
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
			function actualizarEsteProducto(esteId, esteCantidad, estePrecio)
			{
				romper = false;
				for (var i = 0; i < objetoAgregarProd.length; i++)
				{
					if (objetoAgregarProd[i]['id'] == esteId)
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
							if ($(this).attr("name") == esteId)
							{
								$(this).find(".spanSubTotal").text(subTotal.toFixed(2));
							}
						});
						actualizarTotal();
						return false;
					}
				}
			}
			function guardarCompra()
			{
				var proveedor = $("#proveedor").val();
				var arrayProductosJSON 	= JSON.stringify(objetoAgregarProd);
				$.ajax(
				{
					method: "POST",
					url:<?php echo $modificar ? '"assets/ajax/editarCompra.php"' : '"assets/ajax/agregarCompra.php"';?>,
					data: {arrayProductos:arrayProductosJSON,proveedor:proveedor<?php echo $modificar ? ",idCompra:$idCompra_" : "";?>}
				})
				.done(function(p)
				{
					if (p.status == 1)
					{
						mensaje("success",p.respuesta);
						objetoAgregarProd.length = 0;
						html  = '<tr>';
						html +=	'	<td colspan="7">';
						html +=	'		<span class="text-muted">';
						html +=	'			<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>';
						html +=	'			No hay ningún producto en esta lista';
						html +=	'		</span>';
						html +=	'	</td>';
						html += '</tr>';
						<?php echo $modificar ? "" : '$("#tbodyListaProductos").empty();$("#tbodyListaProductos").prepend(html);';?>

						$("#btnGuardarCompra").attr("disabled", true);
						$("#spanTotArts").text(objetoAgregarProd.length);
						$("#spanTotal").text("0.00");
						tablaProd.ajax.reload(null,false);
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
			}
			function unshiftObjetoAgregarProd(id,nombre,codigo,precio,cantidad)
			{
				esteProd     				= new producto(
					id,
						nombre,
							codigo,
								precio,
									cantidad);
				objetoAgregarProd.unshift(esteProd);
			}
			function agregarProductoTbody(p)
			{
				for (var i = 0; i < objetoAgregarProd.length; i++)
				{
					if (objetoAgregarProd[i]['id'] == p.idProducto)
					{
						return false;
					}
				}
				if (objetoAgregarProd.length == 0)
				{
					$("#tbodyListaProductos").empty();
				}
				id 		= p.idProducto;
				nombre 	= p.nombreProd;
				codigo 	= 0;
				precio 	= p.precioCompra;
				cantidad= 1;
				unshiftObjetoAgregarProd(id, nombre, codigo, precio, cantidad);
				$("#tbodyListaProductos").prepend(p.rowProd);
				mensaje('info','Se agregó '+p.nombreProd+' a la lista');
				actualizarTotal();
				$("#btnGuardarCompra").attr("disabled", false);

			}
			function producto(id, nombre, codigo, precio, cantidad)
			{
				this.id 					= id;
				this.nombre 				= nombre;
				this.codigo 				= codigo;
				this.precio 				= precio;
				this.cantidad				= cantidad;
			}
			function actualizarTodo()
			{
				$(".trProductoAgregar").each(function()
				{
					id = $(this).attr("name");
					nombre = $(this).attr("nombre");
					precio = $(this).attr("precio");
					cantidad = $(this).attr("cantidad");
					codigo = 0;
					unshiftObjetoAgregarProd(id, nombre, codigo, precio, cantidad);
				});
				actualizarTotal();
			}
			$(document).ready(function()
			{
				abrirMenu();
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
						})
					});
				}
				$(document).on("click",".aAgregarProd", function()
				{
					idProducto = $(this).attr('name');
					$.ajax(
			        {
			            method: "POST",
			            url:"assets/ajax/obtenerRowProd_compra.php",
			            data: {idProducto:idProducto}
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
					estePrecio 		= $(this).parent().parent().find(".inputP_Compra").val();
					estePrecio		= parseFloat(estePrecio);
					esteCantidad 	= $(this).parent().parent().find(".inputCantidad").val();
					esteCantidad	= parseInt(esteCantidad);
					esteId 			= $(this).parent().parent().attr("name");

					// estePrecio = parseFloat(estePrecio);
					// estePrecio = parseFloat($(this).val());
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
					esteCantidad = parseInt($(this).parent().parent().find(".inputCantidad").val());
					actualizarEsteProducto(esteId, esteCantidad, estePrecio);
				});
				$(document).on("click",".tdEliminarProd", function()
				{
					idProd = $(this).attr('idProd');
					for (var i = 0; i < objetoAgregarProd.length; i++)
					{
						if(objetoAgregarProd[i]['id'] == idProd)
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
									html +=	'			No hay ningún producto en esta lista';
									html +=	'		</span>';
									html +=	'	</td>';
									html += '</tr>';
									$("#tbodyListaProductos").empty();
									$("#tbodyListaProductos").prepend(html);
									$("#btnGuardarCompra").attr("disabled", true);
								}
								else
								{
									$("#btnGuardarCompra").attr("disabled", false);
								}
								mensaje('info','El producto "'+eliminado[0]['nombre']+'" se ha eliminado de la lista de compras');
							}
						}
					}
					actualizarTotal();
				});
				$("#btnGuardarCompra").click(function()
				{
					dialog = bootbox.dialog(
					{
						title: '¿Guardar compra?',
						message: '<i class="fa fa-question-circle-o" aria-hidden="true"></i> ¿Estás seguro que quieres guardar esta compra?',
						closeButton: false,
						buttons:
						{
							"Guardar" :
							{
								"label" : '<i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar',
								"className" : "btn btn-white btn-info btn-bold",

								callback: function(result)
								{
									guardarCompra();
								}
							},
							"Cancelar" :
							{
								"label" : '<i class="fa fa-times" aria-hidden="true"></i> Cancelar',
								"className" : "btn btn-white btn-bold",

								callback: function(result)
								{
								}
							},
						}

					});
				});
				actualizarTodo();
			});
			tablaProd =
			$('#tabla-agregar-prod')
			//.wrap("<div class='dataTables_borderWrap' />")   //if you are applying horizontal scrolling (sScrollX)
			.DataTable( {
				"ajax":			"assets/ajax/listarProductosIndex.JSON.php",
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
						"className":      	'text-right',
						"data": "precioVenta"
					},
					{
						"className":      	'text-right',
						"data": "precioCompra"
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
				"order": 		[[0, 'asc']]
			} );
			jQuery(function($) {

				$('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});
				//tooltip placement on right or left
				function tooltip_placement(context, source) {
					var $source = $(source);
					var $parent = $source.closest('table')
					var off1 = $parent.offset();
					var w1 = $parent.width();

					var off2 = $source.offset();
					//var w2 = $source.width();

					if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) return 'right';
					return 'left';
				}
				$('.show-details-btn').on('click', function(e) {
					e.preventDefault();
					$(this).closest('tr').next().toggleClass('open');
					$(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
				});
				$('[data-rel=tooltip]').tooltip();
			});
		</script>
	</body>
</html>
<?php
	}
 ?>
