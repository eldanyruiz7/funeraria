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
		$permiso = $usuario->permiso("agregarUsuario",$mysqli);
		if (!$permiso)
		{
			header("Location: listarUsuarios.php");
		}
		require_once ("assets/php/query.class.php");
		$query = new Query();
		$modificar = FALSE;
		$usuarioActivo = TRUE;

		if (isset($_GET['idUsuario']))
		{
			if (is_numeric($_GET['idUsuario']))
			{
				require_once ("assets/php/usuario.class.php");

				$idUsuario = $_GET['idUsuario'];
				$usuario_m = new usuario($idUsuario,$mysqli);
				if ($usuario_m->id)
				{
					$modificar = TRUE;
					$usuarioActivo = TRUE;
				}
			}
		}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title><?php echo $modificar ? 'Modificar usuario' : 'Agregar usuario';?></title>

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
							<li class="active"><?php echo $modificar ? 'Modificar usuario' : 'Agregar usuario';?></li>
						</ul><!-- /.breadcrumb -->
					</div>
					<div class="page-content">
						<div class="page-header">
							<h1>
								<i class="fa fa-users" aria-hidden="true"></i> <?php echo $modificar ? 'Modificar usuario' : 'Agregar usuario';?>
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									<?php echo $modificar ? 'Modificar usuario' : 'Agregar un nuevo usuario';?>
								</small>
							</h1>
						</div><!-- /.page-header -->
						<form class="form-horizontal" id="form" role="form">
							<div class="row">
								<div class="col-xs-12">
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right"> Nombres(*) </label>

											<div class="col-sm-8">
												<input type="text" autocomplete="off" id="nombre" name="nombre" value="<?php echo $modificar ? $usuario_m->nombre : '';?>" placeholder="Nombres" class="col-xs-6">
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right"> Apellido paterno(*) </label>

											<div class="col-sm-8">
												<input type="text" autocomplete="off" id="apellidop" name="apellidop" value="<?php echo $modificar ? $usuario_m->apellidop : '';?>" placeholder="Apellido paterno" class="col-xs-6">
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right"> Apellido materno </label>

											<div class="col-sm-8">
												<input type="text" autocomplete="off" id="apellidom" name="apellidom" value="<?php echo $modificar ? $usuario_m->apellidom : '';?>" placeholder="Apellido materno" class="col-xs-6">
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right"> Domicilio (Calle, No Ext, No Int) (*) </label>

											<div class="col-sm-8">
												<input type="text" autocomplete="off" id="direccion1" name="direccion1" value="<?php echo $modificar ? $usuario_m->direccion1 : '';?>" placeholder="Domicilio (Calle, No Ext, No Int)" class="col-xs-12">
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right"> Domicilio (Colonia, Población, Municipio, CP) (*) </label>

											<div class="col-sm-8">
												<input type="text" autocomplete="off" id="direccion2" name="direccion2" value="<?php echo $modificar ? $usuario_m->direccion2 : '';?>" placeholder="Domicilio (Colonia, Población, Municipio, CP)" class="col-xs-12">
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right"> Estado (*) </label>

											<div class="col-sm-8">
												<select id="estado" name="estado" class="col-xs-5">
													<?php
															$rowEstados = $query->table("cat_estados")->select("*")->where("activo", "=", 1, "i")->execute();
															foreach ($rowEstados as $rowEstado) {
																if ($modificar && $rowEstado['id'] == $usuario_m->idEstado) {
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
											<label class="col-sm-4 control-label no-padding-right"> Teléfono </label>

											<div class="col-sm-8">
												<input type="text" autocomplete="off" id="telefono" name="telefono" value="<?php echo $modificar ? $usuario_m->telefono : '';?>" class="col-xs-3 input-mask-phone">
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right"> Celular </label>

											<div class="col-sm-8">
												<input type="text" autocomplete="off" id="celular" name="celular" value="<?php echo $modificar ? $usuario_m->celular : '';?>" class="col-xs-3 input-mask-phone">
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right"> E mail </label>

											<div class="col-sm-8">
												<input type="text" autocomplete="off" id="email" name="email" value="<?php echo $modificar ? $usuario_m->email : '';?>" class="col-xs-5">
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right"> Tasa comisión por ventas (%)(*) </label>

											<div class="col-sm-8">
												<input type="number" id="tasaComision" name="tasaComision" min="1" max="99" value="<?php echo $modificar ? $usuario_m->tasaComision : '1';?>" class="col-xs-1"style="text-align:right">
											</div>
										</div>
										<div class="hr hr-24"></div>
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right"> Nick name(*) </label>

											<div class="col-xs-2 no-padding-right">
												<input type="text" autocomplete="off" id="nickname" name="nickname" value="<?php echo $modificar ? $usuario_m->nickName : '';?>" placeholder="Nick Name" class="col-xs-12">
											</div>
											<span style="padding-bottom:10px" class="btn btn-white btn-success btn-sm popover-success no-border" data-rel="popover" data-trigger="hover" data-placement="right" title="<i class='ace-icon fa fa-check green'></i> Información" data-content="El <b>nick name</b> es el nombre utilizado para iniciar sesión. También puede utilizarse el <b>correo electrónico</b> en su lugar."><i class="fa fa-question-circle" aria-hidden="true"></i></span>
										</div>
						<?php
							if (!$modificar)
							{
						?>
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right"> Contraseña(*) </label>

											<div class="col-sm-8">
												<input type="password" autocomplete="off" id="password1" name="password1"  placeholder="Contraseña" class="col-xs-4">
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right"> Repite la Contraseña(*) </label>

											<div class="col-sm-8">
												<input type="password" autocomplete="off" id="password2" name="password2"  placeholder="Repote la Contraseña" class="col-xs-4">
											</div>
										</div>

						<?php
							}
							if ($modificar)
							{
						?>
										<input type="hidden" name="hiddenIdProducto" id="hiddenIdProducto" value="<?php echo $usuario_m->id;?>">
						<?php
							}

						?>
								</div>
							</div>
							<div class="hr hr-24"></div>
							<div class="row">
								<div class="col-xs-8 col-xs-offset-2 widget-container-col" id="">
									<div class="widget-box transparent ui-sortable-handle" id="">
										<div class="widget-header">
											<h4 class="widget-title"><i class="fa fa-key" aria-hidden="true"></i> Permisos para este usuario</h4>
											<div class="widget-toolbar no-padding-right">
												<span style="" class="btn btn-white btn-success no-border" data-trigger="hover" data-rel="popover" data-placement="bottom" title="<i class='ace-icon fa fa-check green'></i> Información"
													data-content="El perfil <b>Administrador</b> tiene acceso a todos los módulos y tiene control total del sistema, puede ingresar, modificar, visualizar y eliminar cualquier información. Puede crear y eliminar usuarios, así como quitar o agregar privilegios a cada usuario.<br>El perfil <b>Secretaria/o</b> Es un perfil estandar y por defecto tiene acceso a ciertos módulos del sistema, así como a ciertos reportes y a modificar solo alguna información.<br>El perfil <b>Vendedor</b> tiene acceso solo a un conjunto limitado de módulos y por defecto no puede editar ni eliminar ninguna información.<br>Los permisos de los usuarios pueden ser modificados por cualquier perfil <b>Administrador</b> "><i class="fa fa-question-circle" aria-hidden="true"></i>
												</span>
												<label class="green">Perfil:</label>
												<select id="perfil" name="perfil" class="">
													<option <?php echo $modificar && $usuario_m->tipo == 3 ? "selected" : '';?> value="3">
														Vendedor
													</option>
													<option <?php echo $modificar && $usuario_m->tipo == 2 ? "selected" : '';?> value="2">
														Secreatria/o
													</option>
													<option <?php echo $modificar && $usuario_m->tipo == 1 ? "selected" : '';?> value="1">
														Administrador
													</option>
												 </select>
											</div>

										</div>

										<div class="widget-body">
											<div class="widget-main padding-6 no-padding-left no-padding-right">
												<table class="table table-striped table-bordered">
													<thead>
														<tr>
															<th class="center">Elemento</th>
															<th class="center">Listar</th>
															<th class="center">Agregar</th>
															<th class="center">Modificar</th>
															<th class="center">Eliminar</th>
														</tr>
													</thead>

													<tbody>
														<tr>
															<td>
																<i class="fa fa-file-text" aria-hidden="true"></i> Contratos
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("listarContratos",$mysqli) ? 'checked' : '';?> disabled id="listarContratos" name="listarContratos" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("agregarContrato",$mysqli) ? 'checked' : '';?> tipo="a" id="agregarContrato" name="agregarContrato" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("modificarContrato",$mysqli) ? 'checked' : '';?> tipo="m" id="modificarContrato" name="modificarContrato" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("eliminarContrato",$mysqli) ? 'checked' : '';?> tipo="e" id="eliminarContrato" name="eliminarContrato" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
														</tr>
														<tr>
															<td>
																<i class="fa fa-shopping-cart" aria-hidden="true"></i> Ventas
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("listarVentas",$mysqli) ? 'checked' : '';?> tipo="l" id="listarVentas" name="listarVentas" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("agregarVenta",$mysqli) ? 'checked' : '';?> tipo="a" id="agregarVenta" name="agregarVenta" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("modificarVenta",$mysqli) ? 'checked' : '';?> tipo="m" id="modificarVenta" name="modificarVenta" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("eliminarVenta",$mysqli) ? 'checked' : '';?> tipo="e" id="eliminarVenta" name="eliminarVenta" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
														</tr>
														<tr>
															<td>
																<i class="fa fa-truck" aria-hidden="true"></i> Proveedores
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("listarProveedores",$mysqli) ? 'checked' : '';?> tipo="l" id="listarProveedores" name="listarProveedores" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("agregarProveedor",$mysqli) ? 'checked' : '';?> tipo="a" id="agregarProveedor" name="agregarProveedor" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("modificarProveedor",$mysqli) ? 'checked' : '';?> tipo="m" id="modificarProveedor" name="modificarProveedor" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("eliminarProveedor",$mysqli) ? 'checked' : '';?> tipo="e" id="eliminarProveedor" name="eliminarProveedor" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
														</tr>
														<tr>
															<td>
																<i class="fa fa-users" aria-hidden="true"></i> Clientes
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("listarClientes",$mysqli) ? 'checked' : '';?> tipo="l" id="listarClientes" name="listarClientes" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("agregarCliente",$mysqli) ? 'checked' : '';?> tipo="a" id="agregarCliente" name="agregarCliente" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("modificarCliente",$mysqli) ? 'checked' : '';?> tipo="m" id="modificarCliente" name="modificarCliente" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("eliminarCliente",$mysqli) ? 'checked' : '';?> tipo="e" id="eliminarCliente" name="eliminarCliente" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
														</tr>
														<tr>
															<td>
																<i class="fa fa-user-circle-o" aria-hidden="true"></i> Difuntos
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("listarDifuntos",$mysqli) ? 'checked' : '';?> tipo="l" id="listarDifuntos" name="listarDifuntos" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("agregarDifunto",$mysqli) ? 'checked' : '';?> tipo="a" id="agregarDifunto" name="agregarDifunto" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("modificarDifunto",$mysqli) ? 'checked' : '';?> tipo="m" id="modificarDifunto" name="modificarDifunto" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("eliminarDifunto",$mysqli) ? 'checked' : '';?> tipo="e" id="eliminarDifunto" name="eliminarDifunto" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
														</tr>
														<tr>
															<td>
																<i class="fa fa-tags" aria-hidden="true"></i> Productos
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("listarProductos",$mysqli) ? 'checked' : '';?> tipo="l" id="listarProductos" name="listarProductos" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("agregarProducto",$mysqli) ? 'checked' : '';?> tipo="a" id="agregarProducto" name="agregarProducto" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("modificarProducto",$mysqli) ? 'checked' : '';?> tipo="m" id="modificarProducto" name="modificarProducto" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("eliminarProducto",$mysqli) ? 'checked' : '';?> tipo="e" id="eliminarProducto" name="eliminarProducto" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
														</tr>
														<tr>
															<td>
																<i class="fa fa-cubes" aria-hidden="true"></i> Servicios
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("listarServicios",$mysqli) ? 'checked' : '';?> tipo="l" id="listarServicios" name="listarServicios" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("agregarServicio",$mysqli) ? 'checked' : '';?> tipo="a" id="agregarServicio" name="agregarServicio" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("modificarServicio",$mysqli) ? 'checked' : '';?> tipo="m" id="modificarServicio" name="modificarServicio" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("eliminarServicio",$mysqli) ? 'checked' : '';?> tipo="e" id="eliminarServicio" name="eliminarServicio" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
														</tr>
														<tr>
															<td>
																<i class="fa fa-shopping-bag" aria-hidden="true"></i> Compras
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("listarCompras",$mysqli) ? 'checked' : '';?> tipo="l" id="listarCompras" name="listarCompras" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("agregarCompra",$mysqli) ? 'checked' : '';?> tipo="a" id="agregarCompra" name="agregarCompra" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("modificarCompra",$mysqli) ? 'checked' : '';?> tipo="m" id="modificarCompra" name="modificarCompra" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("eliminarCompra",$mysqli) ? 'checked' : '';?> tipo="e" id="eliminarCompra" name="eliminarCompra" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
														</tr>
														<tr>
															<td>
																<i class="fa fa-object-group" aria-hidden="true"></i> Planes funerarios
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("listarPlanes",$mysqli) ? 'checked' : '';?> tipo="l" id="listarPlanes" name="listarPlanes" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("agregarPlan",$mysqli) ? 'checked' : '';?> tipo="a" id="agregarPlan" name="agregarPlan" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("modificarPlan",$mysqli) ? 'checked' : '';?> tipo="m" id="modificarPlan" name="modificarPlan" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("eliminarPlan",$mysqli) ? 'checked' : '';?> tipo="e" id="eliminarPlan" name="eliminarPlan" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
														</tr>
														<tr>
															<td>
																<i class="fa fa-user-o" aria-hidden="true"></i> Usuarios
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("listarUsuarios",$mysqli) ? 'checked' : '';?> tipo="l" id="listarUsuarios" name="listarUsuarios" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("agregarUsuario",$mysqli) ? 'checked' : '';?> tipo="a" id="agregarUsuario" name="agregarUsuario" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("modificarUsuario",$mysqli) ? 'checked' : '';?> tipo="m" id="modificarUsuario" name="modificarUsuario" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("eliminarUsuario",$mysqli) ? 'checked' : '';?> tipo="e" id="eliminarUsuario" name="eliminarUsuario" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
														</tr>
														<tr>
															<td>
																<i class="fa fa-cogs" aria-hidden="true"></i> Variables del sistema
															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("listarVariablesSistema",$mysqli) ? 'checked' : '';?> tipo="l" id="listarVariablesSistema" name="listarVariablesSistema" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">

															</td>
															<td class="text-center">
																<label>
																	<input <?php echo $modificar && $usuario_m->permiso("modificarVariablesSistema",$mysqli) ? 'checked' : '';?> tipo="m" id="modificarVariablesSistema" name="modificarVariablesSistema" class="ace ace-switch ace-switch-6" type="checkbox">
																	<span class="lbl"></span>
																</label>
															</td>
															<td class="text-center">

															</td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
						</form>
						<div class="col-xs-4 col-xs-offset-4">
							<hr>
							<button class="btn btn-danger btn-white btn-bold btn-info btn-block btn-round" id="btnGuardar">
								<i class="ace-icon fa fa-floppy-o bigger-120 blue"></i>
								<?php echo $modificar ? "Modificar usuario" : "Guardar nuevo usuario";?>
							</button>
						</div>
								<!-- PAGE CONTENT ENDS -->
					</div><!-- /.col -->
				</div><!-- /.row -->

				<!--/////////////////////modal agregar usuario ////////////////////////-->
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
										<label class="col-xs-12 text-left"><i class="fa fa-question-circle" aria-hidden="true"></i> ¿Deseas modificar este usuario? <br/>Estás a punto de modificar este usuario</label>
					<?php
						}
						else
						{
					?>
										<label class="col-xs-12 text-left"><i class="fa fa-question-circle" aria-hidden="true"></i> ¿Deseas agregar este registro al sistema? <br/>Estás a punto de agregar un nuevo usuario</label>
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
		<script src="assets/js/jquery.maskedinput.min.js"></script>

		<!-- ace scripts -->
		<script src="assets/js/ace-elements.min.js"></script>
		<script src="assets/js/ace.min.js"></script>
		<script src="assets/js/custom/primario.js"></script>
		<!-- inline scripts related to this page -->
		<script type="text/javascript">
		myTable = "";
			function ajustarPermisos()
			{
				idPerfil = $("#perfil").val();
				switch (idPerfil)
				{
					case '1':
						$("input[tipo=l]").prop("checked",false);
						$("input[tipo=a]").prop("checked",false);
						$("input[tipo=m]").prop("checked",false);
						$("input[tipo=e]").prop("checked",false);
						$("input[tipo=e]").click();
						break;
					case '2':
						$("input[tipo=l]").prop("checked",false);
						$("input[tipo=a]").prop("checked",false);
						$("input[tipo=m]").prop("checked",false);
						$("input[tipo=e]").prop("checked",false);
						$("#modificarContrato").click();
						$("#agregarVenta").click();
						$("#eliminarProveedor").click();
						$("#agregarCliente").click();
						$("#modificarDifunto").click();
						$("#agregarProducto").click();
						$("#agregarServicio").click();
						$("#agregarCompra").click();
						$("#listarPlanes").click();
						break;
					case '3':
						$("input[tipo=l]").prop("checked",false);
						$("input[tipo=a]").prop("checked",false);
						$("input[tipo=m]").prop("checked",false);
						$("input[tipo=e]").prop("checked",false);
						$("#listarVentas").click();
						$("#listarProveedores").click();
						$("#listarClientes").click();
						$("#listarDifuntos").click();
						$("input#listarContratos").prop("checked",true);
						break;
				}
			}
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
				$("#perfil").change(function()
				{
					ajustarPermisos();
				})
				$(document).on('click, change','[tipo=l]',function()
				{
					if ($(this).prop("checked") == false)
					{
						$(this).parent().parent().parent().find("[tipo=a]").prop("checked",false);
						$(this).parent().parent().parent().find("[tipo=m]").prop("checked",false);
						$(this).parent().parent().parent().find("[tipo=e]").prop("checked",false);
					}
				});
				$(document).on("click, change",'[tipo=a]',function()
				{
					if ($(this).prop("checked") == false)
					{
						$(this).parent().parent().parent().find("[tipo=m]").prop("checked",false);
						$(this).parent().parent().parent().find("[tipo=e]").prop("checked",false);
					}
					else
					{
						$(this).parent().parent().parent().find("[tipo=l]").prop("checked",true);
					}
				});
				$(document).on("click, change",'[tipo=m]',function()
				{
					if ($(this).prop("checked") == false)
					{
						$(this).parent().parent().parent().find("[tipo=e]").prop("checked",false);
					}
					else
					{
						$(this).parent().parent().parent().find("[tipo=a]").prop("checked",true);
						$(this).parent().parent().parent().find("[tipo=l]").prop("checked",true);
					}
				});
				$(document).on("click, change",'[tipo=e]',function()
				{
					if ($(this).prop("checked") == false)
					{

					}
					else
					{
						$(this).parent().parent().parent().find("[tipo=a]").prop("checked",true);
						$(this).parent().parent().parent().find("[tipo=l]").prop("checked",true);
						$(this).parent().parent().parent().find("[tipo=m]").prop("checked",true);
					}
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
							"usuarios" :
							{
								"label" : '<i class="fa fa-user-o" aria-hidden="true"></i> Lista de usuarios',
								"className" : "btn-info btn-white btn-bold btn-round oculto",
								callback: function(result)
								{
									window.location.assign("listarUsuarios.php");
								}
							},
				<?php
					if (!$modificar)
					{
				?>
							"otro" :
							{
								"label" : '<i class="fa fa-user-o" aria-hidden="true"></i> Crear otro usuario',
								"className" : "btn-default btn-white btn-bold btn-round oculto",
								callback: function(result)
								{
									window.location.assign("agregarUsuario.php");
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
			            url:<?php echo $modificar ? "'assets/ajax/modificarUsuario.php'" : "'assets/ajax/agregarUsuario.php'";?>,
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
								dialog.find('.bootbox-body').html('<i class="fa fa-check" aria-hidden="true"></i> Usuario <b>'+p.mensaje+'</b> ha sido modificado exitosamente');
				<?php
					}
					else
					{
				?>
								dialog.find('.bootbox-body').html('<i class="fa fa-check" aria-hidden="true"></i> Usuario <b>'+p.mensaje+'</b> ha sido creado exitosamente');
				<?php
					}
				?>
								dialog.find('.oculto').removeClass('oculto');
							});
				<?php
					if ($modificar)
					{
				?>
								mensaje('success','Usuario '+p.mensaje+' ha sido modificado exitosamente<br><h5><a href="listarUsuarios.php" class="orange">Lista de usuarios</a></h5>');
				<?php
					}
					else
					{
				?>
								mensaje('success','Usuario '+p.mensaje+' ha sido guardado exitosamente<br><h5><a href="listarUsuarios.php" class="orange">Lista de usuarios</a></h5>');
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
				$('.input-mask-phone').mask('(999) 999-9999');
				$('[data-rel=popover]').popover({html:true});
		<?php
			if (!$modificar)
			{
		?>
				ajustarPermisos();
		<?php
			}
		 ?>
			});
		</script>
	</body>
</html>
<?php
	}
 ?>
