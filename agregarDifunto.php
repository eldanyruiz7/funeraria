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
		$permiso = $usuario->permiso("agregarDifunto",$mysqli);
		if (!$permiso)
		{
			header("Location: listarDifuntos.php");
		}
		$modificar = FALSE;
		$difuntoActivo = TRUE;
		if (isset($_GET['idDifunto']))
		{
			if (is_numeric($_GET['idDifunto']))
			{
				$idDifunto = $_GET['idDifunto'];
				$sql = "SELECT
							id, idCliente, idContrato, idVenta, nombres, apellidop, apellidom, domicilio1_part, domicilio2_part, cp_part,
							idEstado_part, rfc, fechaNac, fechaHrDefuncion, idLugarDefuncion, nombreLugarDefuncion,	domicilioLugarDefuncion,
							domicilioParticularDefuncion, noCertificadoDefuncion, noActaDefuncion, idSucursal, usuario, activo
						FROM cat_difuntos
						WHERE id = ? LIMIT 1";
				if ($res = $mysqli->prepare($sql))
				{
				    if($res->bind_param("i", $idDifunto) && $res->execute())
					{
						$res->store_result();
						if ($res->num_rows == 1)
						{
							$modificar = TRUE;
							$res->bind_result($idDifunto_, $idCliente_, $idContrato_, $idVenta_, $nombres_, $apellidop_, $apellidom_,
												$domicilio1_part_, $domicilio2_part, $cp_part_, $idEstado_part_, $rfc_, $fechNac_,
												$fechaHrDefuncion_, $idLugarDefuncion_, $nombreLugarDefuncion_, $domicilioLugarDefuncion_,
												$domicilioParticularDefuncion_, $noCertificadoDefuncion_, $noActaDefuncion_, $idSucursal_, $usuario_, $activo_);
							$res->fetch();
							$difuntoActivo = ($activo_ == 1) ? TRUE : FALSE;
						}
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
		<title><?php echo $modificar ? "Modificar difunto" : "Agregar difunto";?></title>

		<meta name="description" content="Static &amp; Dynamic Tables" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="assets/font-awesome/4.5.0/css/font-awesome.min.css" />
		<!-- text fonts -->
		<link rel="stylesheet" href="assets/css/fonts.googleapis.com.css" />
		<link rel="stylesheet" href="assets/css/jquery.gritter.min.css" />
		<link rel="stylesheet" href="assets/css/chosen.min.css" />

		<!-- ace styles -->
		<link rel="stylesheet" href="assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
		<!--[if lte IE 9]>
			<link rel="stylesheet" href="assets/css/ace-part2.min.css" class="ace-main-stylesheet" />
		<![endif]-->
		<link rel="stylesheet" href="assets/css/ace-skins.min.css" />
		<link rel="stylesheet" href="assets/css/ace-rtl.min.css" />
		<link rel="stylesheet" href="assets/css/dropzone.min.css" />
		<link rel="stylesheet" href="assets/css/jquery.fancybox.min.css" />
		<link rel="stylesheet" href="assets/css/bootstrap-multiselect.min.css" />


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
							<li class="active"><?php echo $modificar ? "Modificar difunto" : "Agregar difunto";?></li>
						</ul><!-- /.breadcrumb -->
					</div>
					<div class="page-content">
						<div class="page-header">
							<h1>
								 <?php echo $modificar ? "<i class='fa fa-user-circle' aria-hidden='true'></i> Modificar difunto" : "<i class='fa fa-user-circle-o' aria-hidden='true'></i> Agregar difunto";?>
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									<?php echo $modificar ? $nombres_." ".$apellidop_." ".$apellidom_ : "Agregar un nuevo difunto";?>
								</small>
							</h1>
							<?php
				if (!$difuntoActivo)
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
										<label class="col-sm-4 control-label no-padding-right"> Nombre(s)(*) </label>

										<div class="col-sm-8">
											<input value="<?php echo $modificar ? $nombres_ : '';?>" type="text" id="nombres" autocomplete="off" name="nombres" placeholder="Nombres" class="col-xs-5">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Apellido paterno(*) </label>

										<div class="col-sm-8">
											<input value="<?php echo $modificar ? $apellidop_ : '';?>" type="text" id="apellidop" autocomplete="off" name="apellidop" placeholder="Apellido paterno" class="col-xs-5">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Apellido materno </label>

										<div class="col-sm-8">
											<input value="<?php echo $modificar ? $apellidom_ : '';?>" type="text" id="apellidom" autocomplete="off" name="apellidom" placeholder="Apellido materno" class="col-xs-5">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Domicilio particular (Calle, No Ext, No Int) </label>

										<div class="col-sm-8">
											<input value="<?php echo $modificar ? $domicilio1_part_ : '';?>" type="text" id="domicilio1" autocomplete="off" name="domicilio1" placeholder="Domicilio (Calle, No Ext, No Int)" class="col-xs-9">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Domicilio particular(Colonia, Población, Municipio) </label>

										<div class="col-sm-8">
											<input value="<?php echo $modificar ? $domicilio2_part : '';?>" type="text" id="domicilio2" autocomplete="off" name="domicilio2" placeholder="Domicilio (Colonia, Población, Municipio)" class="col-xs-9">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Código postal particular(*) </label>

										<div class="col-sm-8">
											<input value="<?php echo $modificar ? $cp_part_ : '';?>" type="text" id="cp" autocomplete="off" name="cp" placeholder="Código postal" class="col-xs-">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Estado (*) </label>

										<div class="col-sm-8">
											<select id="estado" name="estado" class="col-xs-5">
												<?php
														$sql = "SELECT * FROM cat_estados WHERE activo = 1";
														$res_estado = $mysqli->query($sql);
														while ($row_estado = $res_estado->fetch_assoc())
															if ($modificar && $row_estado['id'] == $idEstado_part_)
																echo "<option selected value=".$row_estado['id'].">".$row_estado['estado']."</option>";
															else
																echo "<option value=".$row_estado['id'].">".$row_estado['estado']."</option>";
												 ?>
											 </select>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> RFC </label>

										<div class="col-sm-8">
											<input value="<?php echo $modificar ? $rfc_ : '';?>" type="text" id="rfc"  autocomplete="off" name="rfc" placeholder="RFC" class="col-xs-3">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Fecha de nacimiento (*) </label>

										<div class="col-sm-8">
											<input value="<?php echo $modificar ? $fechNac_ : '';?>" type="date" id="fechaNac" autocomplete="off" name="fechaNac" class="col-xs-4">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> Fecha/hr de defunción (*) </label>

										<div class="col-sm-8">
								<?php
								if ($modificar)
								{
									$fechaDef_part = explode(" ",$fechaHrDefuncion_);
								}
								 ?>
											<input value="<?php echo $modificar ? $fechaDef_part[0]."T".$fechaDef_part[1] : '';?>" type="datetime-local" autocomplete="off" id="fechaDef" name="fechaDef" class="col-xs-4">
										</div>
									</div>
									<hr>
									<div class="form-group">
										<label style="margin:0px;padding-top:8px;padding-left:8px;">
											<input <?php echo ($modificar && $idLugarDefuncion_ == 0) ? "checked" : '';?> name="chkDomicilio" autocomplete="off" id="chkDomicilio" class="ace ace-switch ace-switch-6" type="checkbox">
											<span class="lbl"></span>
										</label>
										<label class="col-sm-4 control-label no-padding-right"> El difunto falleció en domicilio particular </label>
									</div>
								<?php
								 	if ($modificar)
									{
								 		if ($idLugarDefuncion_ != 0)
										{
								 			$dsp = "display:none";
								 		}
										else
										{
											$dsp = "display:block";
										}
								 	}
									else
									{
										$dsp = "display:none";
									}
								 ?>
									<div id="divDomParticular" style="<?php echo $dsp;?>">
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right"> Domicilio particular de defunción (*)</label>

											<div class="col-sm-8">
												<input value="<?php echo $modificar ? $domicilioParticularDefuncion_ : '';?>" type="text" autocomplete="off" id="domicilioParticularDefuncion" name="domicilioParticularDefuncion" placeholder="Domicilio particular de defunción" class="col-xs-11">
											</div>
										</div>
									</div>
								<?php
								 	if ($modificar)
									{
								 		if ($idLugarDefuncion_ != 0)
										{
								 			$dsp = "display:block";
								 		}
										else {
											$dsp = "display:none";
										}
								 	}
									else
									{
										$dsp = "display:block";
									}
								 ?>
									<div id="divDomInstitucion" style="<?php echo $dsp;?>">
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right"> Lugar de defunción (*) </label>
											<div class="col-sm-8">
												<select class="chosen-select col-xs-10" name="idLugarDefuncion" id="idLugarDefuncion" data-placeholder="Elige un lugar...">
													<option value="">  </option>
									<?php
										$nombreLugarDefuncion_select = "";
										$domicilioLugarDefuncion_select = "";
										$sql = "SELECT id, nombre, domicilio FROM cat_lugares_defuncion WHERE activo = 1";
										$res_lugares_def = $mysqli->query($sql);
										while ($row_lugares_def = $res_lugares_def->fetch_assoc())
											if ($modificar && $row_lugares_def['id'] == $idLugarDefuncion_)
											{
												echo '<option selected nombre-institucion="'.$row_lugares_def['nombre'].'" domicilio-institucion="'.$row_lugares_def['domicilio'].'" value="'.$row_lugares_def['id'].'">'.$row_lugares_def['nombre'].' - '.$row_lugares_def['domicilio'].'  </option>';
												$nombreLugarDefuncion_select = $row_lugares_def['nombre'];
												$domicilioLugarDefuncion_select = $row_lugares_def['domicilio'];
											}
											else
												echo '<option nombre-institucion="'.$row_lugares_def['nombre'].'" domicilio-institucion="'.$row_lugares_def['domicilio'].'" value="'.$row_lugares_def['id'].'">'.$row_lugares_def['nombre'].' - '.$row_lugares_def['domicilio'].'  </option>';
									?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right"> Agregar un nuevo lugar </label>

											<div class="col-sm-8 checkbox">
												<label>
													<input id="checkNuevoLugar" autocomplete="off" name="checkNuevoLugar" class="" type="checkbox" style="width:20px;height:20px">
												</label>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right"> Nombre del lugar o institución </label>

											<div class="col-sm-8">
												<input value="<?php echo $nombreLugarDefuncion_select;?>" type="text" id="nombreLugarDefuncion" autocomplete="off" readonly="true" name="nombreLugarDefuncion" placeholder="Nombre del lugar" class="col-xs-5">
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-4 control-label no-padding-right"> Domicilio de la institución donde falleció </label>

											<div class="col-sm-8">
												<input value="<?php echo $domicilioLugarDefuncion_select;?>" type="text" id="domicilioLugarDefuncion" autocomplete="off" readonly="true" name="domicilioLugarDefuncion" placeholder="Domicilio de la institución de defunción" class="col-xs-11">
											</div>
										</div>
										<hr>
									</div>
									<div class="form-group">
										<label class="control-label col-sm-4 no-padding-right">Causa(s) del deceso</label>

										<div class="col-sm-8">
											<select id="causasDecesos" name="causasDecesos[]" class="multiselect" multiple="">
											</select>
											<span class="btn btn-white btn-success btn-bold" id="btnAgregarCausa">
												<i class="ace-icon fa fa-plus-square bigger-120 green"></i>
												Agregar nueva
											</span>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> No. certificado defunción</label>
										<div class="col-sm-8">
											<input value="<?php echo $modificar ? $noCertificadoDefuncion_ : '';?>" type="text" autocomplete="off" id="certificadoDefuncion" name="certificadoDefuncion" placeholder="Número de certificado de defunción" class="col-xs-7">
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-4 control-label no-padding-right"> No. acta de defunción</label>
										<div class="col-sm-8">
											<input value="<?php echo $modificar ? $noActaDefuncion_ : '';?>" type="text" autocomplete="off" id="actaDefuncion" name="actaDefuncion" placeholder="Número de acta de defunción" class="col-xs-7">
										</div>
									</div>
								</form>
							</div>
							<hr>
							<div id="" class="col-xs-12 search-area well">
								<b class="text-primary"><?php echo $modificar ? "Imágenes dentro del expediente:" : "Imágenes que se agregarán al expediente:";?></b>
								<ul id="divImagenesDifunto" class="ace-thumbnails clearfix">
						<?php
								if ($modificar)
								{
									// $idDifunto = $_POST['idDifunto'];
							        $directorio = 'assets/images/avatars/difuntos/'.$idDifunto_;
							        $total_imagenes = count(glob($directorio."/{*.jpg,*.gif,*.png,*.BMP,*.JPG,*.GIF,*.PNG,*.bmp}",GLOB_BRACE));
							        // echo "total_imagenes = ".$total_imagenes;
							        //var_dump(glob($directorio));
									if (is_dir($directorio) != FALSE && $total_imagenes >= 1)
									{
										$dir = opendir($directorio);
								        while($file=readdir($dir)){
								            if(!is_dir($file))
								            {
								                $data[] = array($file);//, date("Y-m-d H:i:s",strtotime($fechaCreacion)));
								            }
								        }
								        closedir($dir);
										foreach ($data as $file)
								        {
								            $im = file_get_contents('assets/images/avatars/difuntos/'.$idDifunto.'/'.$file[0]);
								            $imdata = base64_encode($im);
								            $partes = explode(".",$file[0]);
								            $extImg = $partes[1];
								            if (strtolower($extImg) 	== 'jpg' || strtolower($extImg) == 'jpeg')
								                $tipoImg = 'image/jpeg';
								            elseif (strtolower($extImg) 	== 'png')
								                $tipoImg = 'image/png';
						?>
									<li>
				   	                     <a href='data:image/jpeg;base64,<?php echo $imdata;?>' data='<?php echo $imdata;?>' data-fancybox='images' data-caption='<?php echo $file[0];?>' class='cboxElement'>
				   	                         <img height='180px' alt='150x150' src='data:image/jpeg;base64,<?php echo $imdata;?>'/>
				   	                         <div class='text'>
				   	                             <div class='inner'></div>
				   	                         </div>
				   	                     </a>

				   	                     <div class='tools tools-bottom'>
				   	                         <a class='timesEliminarImg pointer'>
				   	                             <i class='ace-icon fa fa-times red'></i>
				   	                         </a>
				   	                     </div>
				   	                 </li>
						<?php
										}
									}
								}
						?>
						</ul>
						<?php
									// if (is_dir($directorio) == FALSE || $total_imagenes < 1)
									// {
						?>
									<span style="<?php echo ($total_imagenes < 1 && $modificar) ? 'display:block' : 'display:none';?>" id="spanInfoImagenes" class="gray text-muted"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> No hay imágenes</span>
									 &nbsp;
						<?php
									// }
						?>
							</div>
							<div class="col-xs-12">
								<form action="assets/ajax/subirImg.php" class="dropzone well" id="dropzone">
									<div class="fallback">
										<input name="file" type="file" multiple="" />
										<input type="hidden" value="001" name="inputFileImagen" id="inputFileImagen" />
									</div>
								</form>
							</div>

							<div id="preview-template" class="hide">
								<div class="dz-preview dz-file-preview">
									<div class="dz-image">
										<img data-dz-thumbnail="" />
									</div>
									<div class="dz-details">
										<div class="dz-size">
											<span data-dz-size=""></span>
										</div>
										<div class="dz-filename">
											<span data-dz-name=""></span>
										</div>
									</div>
									<div class="dz-progress">
										<span class="dz-upload" data-dz-uploadprogress=""></span>
									</div>
									<div class="dz-error-message">
										<span data-dz-errormessage=""></span>
									</div>
									<div class="dz-success-mark">
										<span class="fa-stack fa-lg bigger-150">
											<i class="fa fa-circle fa-stack-2x white"></i>
											<i class="fa fa-check fa-stack-1x fa-inverse green"></i>
										</span>
									</div>

									<div class="dz-error-mark">
										<span class="fa-stack fa-lg bigger-150">
											<i class="fa fa-circle fa-stack-2x white"></i>
											<i class="fa fa-remove fa-stack-1x fa-inverse red"></i>
										</span>
									</div>
								</div>
							</div>
							<div class="col-xs-4 col-xs-offset-4">
								<button class="btn btn-danger btn-white btn-bold btn-info btn-block btn-round" id="btnGuardar">
									<i class="ace-icon fa fa-floppy-o bigger-120 blue"></i>
									<?php echo $modificar ? "Actualizar este difunto" : "Guardar nuevo difunto";?>
								</button>
							</div>
						</div>
								<!-- PAGE CONTENT ENDS -->
					</div><!-- /.col -->
				</div><!-- /.row -->

				<!--/////////////////////modal agregar difunto ////////////////////////-->
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
										<label class="col-xs-12 text-left"><i class="fa fa-question-circle" aria-hidden="true"></i> <?php echo $modificar ? "¿Deseas modificar este registro? <br/>Estás a punto de modificar la información de este registro" : "¿Deseas agregar este registro al sistema? <br/>Estás a punto de agregar un nuevo difunto";?></label>
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
				<!--/////////////////////modal agregar difunto ////////////////////////-->
				<div id="modal-agregar-causa-deceso" class="modal fade" tabindex="-1">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h5 class="green bigger no-margin">
									<span class="lighter green no-margin">
										<i class="fa fa-plus-square" aria-hidden="true"></i>
									</span>
									Registra una nueva causa de deceso
								</h4>
							</div>

							<div class="modal-body">
								<div class="row">
									<div class="col-xs-12">
										<form class="form-horizontal" onsubmit="return(false)">
											<div class="form-group" style="margin-bottom:0px">
												<label class="col-sm-4 control-label no-padding"> Nombre de la nueva causa de deceso: </label>
												<div class="col-sm-8">
													<input type="text" id="inputNuevaCausaDeceso" autocomplete="off" name="inputNuevaCausaDeceso" placeholder="Ingresa el nuevo nombre de la causa de deceso" class="col-xs-12">
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button class="btn btn-white btn-success btn-bold" id="btnGuardarNuevaCausa">
									<i class="ace-icon fa fa-save"></i>
									Agregar
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
		<script src="assets/js/chosen.jquery.min.js"></script>
		<script src="assets/js/dropzone.min.js"></script>
		<script src="assets/js/ace-elements.min.js"></script>
		<script src="assets/js/ace.min.js"></script>
		<script src="assets/js/jquery-ui.min.js"></script>
		<script src="assets/js/jquery.fancybox.min.js"></script>
		<script src="assets/js/bootstrap-multiselect.min.js"></script>
		<script src="assets/js/custom/primario.js"></script>
		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			myTable = "";
			objetoInfoImagenes = [];
			function colorBox()
			{
				$('.cboxElement').fancybox(
				{
					// selector : ,
					helpers : {
				        title: {
				            type: 'inside',
				            position: 'top'
				        }
				    },
				    nextEffect: 'fade',
				    prevEffect: 'fade'
				});
			}
			function recargarCausasDecesos()
			{
				$('#causasDecesos').multiselect('destroy');
				$.ajax(
				{
					method: "POST",
					url:"assets/ajax/obtenerRowCausas_decesos.php",
					data: {idDifunto:<?php echo $modificar ? $idDifunto_ : '0';?>}
				})
				.done(function(e)
				{
					$("#causasDecesos").html(e);
					$('#causasDecesos').multiselect(
					{
						enableFiltering: true,
						enableHTML: true,
						buttonClass: 'btn btn-white btn-primary',
						templates:
						{
							button: '<button type="button" class="multiselect dropdown-toggle" data-toggle="dropdown"><span class="multiselect-selected-text"></span> &nbsp;<b class="fa fa-caret-down"></b></button>',
							ul: '<ul class="multiselect-container dropdown-menu"></ul>',
							filter: '<li class="multiselect-item filter"><div class="input-group"><span class="input-group-addon"><i class="fa fa-search"></i></span><input class="form-control multiselect-search" type="text"></div></li>',
							filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default btn-white btn-grey multiselect-clear-filter" type="button"><i class="fa fa-times-circle red2"></i></button></span>',
							li: '<li><a tabindex="0"><label></label></a></li>',
							divider: '<li class="multiselect-item divider"></li>',
							liGroup: '<li class="multiselect-item multiselect-group"><label></label></li>'
						}
					});
				});
			}
			function imagen(imgBinario)
			{
				this.imgBinario = imgBinario;
			}
			function adjuntarImagenes()
			{
				objetoInfoImagenes.length = 0;
				$(".cboxElement").each(function()
				{
					imgBinario = $(this).attr("data");
					esteImagen     	= new imagen(imgBinario);
					objetoInfoImagenes.push(imgBinario);
				});
			}
			$("#btnAgregarCausa").click(function(e)
			{
				e.preventDefault();
				$("#modal-agregar-causa-deceso").modal();
			});
			// $("form")submit(function(e)
			// {
			// 	e.preventDefault();
			// 	return false;
			// 	var self = this;
			// 	window.setTimeout(function() {
			// 		self.submit();
			// 	}, 2000);
			// });
			$("#modal-agregar-causa-deceso").on('hidden.bs.modal', function()
			{
				$("button.multiselect").click();
			});
			$("#btnGuardarNuevaCausa").click(function()
			{
				$(".has-error").removeClass("has-error");
				nombre = $("#inputNuevaCausaDeceso").val();
				$.ajax(
				{
					method: "POST",
					url:"assets/ajax/agregarCausaDeceso.php",
					data: {nombre:nombre}
				})
				.done(function(p)
				{
					if (p.status == 1)
					{
						$("#modal-agregar-causa-deceso").modal('hide');
						mensaje("info",p.mensaje);
						recargarCausasDecesos();
						// $("#"+p.focus).parent().parent().addClass('has-error');
					}
					if (p.status == 0)
					{
						mensaje("error",p.mensaje);
						$("#"+p.focus).parent().parent().addClass('has-error');
					}
				})
				.error(function()
				{
					mensaje("error", "No hay conexión con el servidor, vuelve a intentarlo");
				})
				.always(function(p)
				{
					console.log(p);
				});
			});
			$(document).one('ajaxloadstart.page', function(e) {
				//in ajax mode, remove remaining elements before leaving page
				try {
					dropzone.destroy();
				} catch(e) {}
				try {
					$('.editable').editable('destroy');
				} catch(e) {}
				$('[class*=select2]').remove();
			});
			function recargarImagenes(xhr)
			{
				$("#spanInfoImagenes").hide();
				$("#divImagenesDifunto").append(xhr.htmlImagen);
				console.log(xhr);
			}
			try {
			  Dropzone.autoDiscover = false;

			  myDropzone = new Dropzone('#dropzone', {
				previewTemplate: $('#preview-template').html(),
				thumbnailHeight: 120,
				thumbnailWidth: 120,
				maxFilesize: 50,

				addRemoveLinks : true,
				acceptedFiles: 'image/jpg,image/jpeg',
				dictRemoveFile: 'Aceptar',
				dictDefaultMessage :
				'<span class=""><i class="ace-icon fa fa-caret-right red"></i> Arrastra imágenes aquí</span> para agregarlas \
				<span class="smaller-70 grey">(o click)</span> <br /> \
				<i class="upload-icon ace-icon fa fa-cloud-upload blue fa-2x"></i>'
			,
				init: function()
				{
					this.on("sending", function(file, xhr, formData){
					formData.append("idPaciente", 0)
				}),
					this.on("success", function(file, xhr) { recargarImagenes(xhr); });
				},

				thumbnail: function(file, dataUrl) {
				  if (file.previewElement) {
					$(file.previewElement).removeClass("dz-file-preview");
					var images = $(file.previewElement).find("[data-dz-thumbnail]").each(function() {
						var thumbnailElement = this;
						thumbnailElement.alt = file.name;
						thumbnailElement.src = dataUrl;
					});
					setTimeout(function() { $(file.previewElement).addClass("dz-image-preview"); }, 100);
				  }
				}
			  });

			} catch(e) {
			  alert('Dropzone.js does not support older browsers!');
			}
			$(document).on("click",".timesEliminarImg", function()
			{
				$(this).parent().parent().remove();
				if ($(".cboxElement").length == 0)
				{
					$("#spanInfoImagenes").show();
				}
			});
			$(document).ready(function()
			{
				abrirMenu();

				$("#chkDomicilio").change(function()
				{
					$(".has-error").removeClass('has-error');
					if ($(this).prop("checked"))
					{
						$( "#divDomInstitucion" ).hide( 'fade', { direction: "down" } , 250, function()
						{
							$( "#divDomParticular" ).show( 'fade', { direction: "down" } , 250);
						});
					}
					else
					{
						$( "#divDomParticular" ).hide( 'fade', { direction: "down" } , 250, function()
						{
							$( "#divDomInstitucion" ).show( 'fade', { direction: "down" } , 250, function()
							{
								$('.chosen-select').each(function() {
									 var $this = $(this);
									 $this.next().css({'width': $this.parent().width()});
								});
							});
						});
					}
				});
				$("#checkNuevoLugar").change(function()
				{
					$(".has-error").removeClass('has-error');
					if ($(this).prop("checked"))
					{
						$('.chosen-select option:selected').removeAttr('selected');
						// $('.chosen-select option').attr("readonly",true);
						// // $('.chosen-select').attr("readonly",true);
						$('.chosen-select').trigger('chosen:updated');
						$("#nombreLugarDefuncion").val('');
						$("#domicilioLugarDefuncion").val('');
						// $('.chosen-select>option:selected').prop('selected', false);
						// $('.chosen-select').prop('selected', false);
						// $('.chosen-select').val("1");
        				// $('chosen-select').trigger("chosen:updated");
						$("#nombreLugarDefuncion").attr("readonly",false);
						$("#domicilioLugarDefuncion").attr("readonly",false);
						$("#nombreLugarDefuncion").focus();


					}
					else
					{
						$("#nombreLugarDefuncion").attr("readonly",true);
						$("#domicilioLugarDefuncion").attr("readonly",true);


					}
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
					    message: '<p><i class="fa fa-spin fa-spinner"></i> Procesando... Espera</p>',
						closeButton: false,
						buttons:
						{
							"difuntos" :
							{
								"label" : '<i class="fa fa-list" aria-hidden="true"></i> Lista de difuntos',
								"className" : "btn-info btn-white btn-bold btn-round oculto",
								callback: function(result)
								{
									window.location.assign("listarDifuntos.php");
								}
							},
							"otro" :
							{
								"label" : '<i class="fa fa-user-plus" aria-hidden="true"></i> Capturar otro difunto',
								"className" : "btn-default btn-white btn-bold btn-round oculto",
								callback: function(result)
								{
									window.location.assign("agregarDifunto.php");
								}
							},
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
								// "className" : "btn oculto2",
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
					adjuntarImagenes();
					var imagenesJSON 		= JSON.stringify(objetoInfoImagenes);
					var data =
					{
					  'arrayImagenes' : imagenesJSON
					  <?php echo $modificar ? ", 'idDifunto' : $idDifunto" : "";?>
					};

					data = $("form#form").serialize() + '&' + $.param(data);
					$.ajax(
			        {
			            method: "POST",
			            url:"assets/ajax/<?php echo $modificar ? "modificarDifunto.php" : "agregarDifunto.php";?>",
			            data: data
			        })
			        .done(function(p)
			        {
						console.log(p);
						if (p.status == 1)
						{
							dialog.init(function()
							{
							 	dialog.find('.bootbox-body').html('<i class="fa fa-check" aria-hidden="true"></i> Difunto <b>'+p.mensaje+'</b> <?php echo $modificar ? "ha sido modificado exitosamente" : "ha sido creado exitosamente";?>');
								dialog.find('.oculto').removeClass('oculto');
								// dialog.find('button')
								// body > div.bootbox.modal.fade.in > div > div > div.modal-footer > button:nth-child(3)
							});
							mensaje('success','Difunto '+p.mensaje+' ha sido guardado exitosamente<br><h5><a href="listarDifuntos.php" class="orange">Lista de difuntos</a></h5>');
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
						dialog.find('.bootbox-body').html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> No se pudo guardar');
						mensaje("error", "No hay conexión con el servidor. Revisa la conexión a internet y vuelve a intentarlo")
						dialog.find('.oculto2').removeClass('oculto2');
					})
			        .always(function(p)
			        {
						console.log(p);
			        });

				});
				if(!ace.vars['touch']) {
					$('.chosen-select').chosen({allow_single_deselect:true}).change(function()
					{
						var nombre = $('.chosen-select>option:selected').attr('nombre-institucion');
						var domicilio = $('.chosen-select>option:selected').attr('domicilio-institucion');
						$("#checkNuevoLugar").attr("checked",false);
						$("#nombreLugarDefuncion").val(nombre);
						$("#domicilioLugarDefuncion").val(domicilio);
					});

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
				colorBox();
				recargarCausasDecesos();
			});
		</script>
	</body>
</html>
<?php
	}
 ?>
