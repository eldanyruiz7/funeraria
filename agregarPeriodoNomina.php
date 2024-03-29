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
		require_once "assets/php/query.class.php";
		$query 		= new Query();
		$permiso = $usuario->permiso("listarNominas",$mysqli);
		if (!$permiso)
		{
			header("Location: index.php");
		}
		$editar = FALSE;
		if ($_GET)
		{
			$idPeriodo = $_GET['id'];
			$Periodo = $query->table("cat_periodos_nominas")->select()->where("id", "=", $idPeriodo, "i")->and()->where("activo", "=", 1 , "i")->execute(FALSE, OBJ);
			if ($query->num_rows())
			{
				$editar = TRUE;
			}
		}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title><?php echo $editar ? 'Editar' : 'Agregar';?> periodo de nómina</title>

		<meta name="description" content="Static &amp; Dynamic Tables" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="assets/font-awesome/4.5.0/css/font-awesome.min.css" />
		<!-- text fonts -->
		<link rel="stylesheet" href="assets/css/fonts.googleapis.com.css" />
		<link rel="stylesheet" href="assets/css/jquery.gritter.min.css" />
		<link rel="stylesheet" href="assets/css/daterangepicker.min.css" />

		<!-- ace styles -->
		<link rel="stylesheet" href="assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
		<!--[if lte IE 9]>
			<link rel="stylesheet" href="assets/css/ace-part2.min.css" class="ace-main-stylesheet" />
		<![endif]-->
		<link rel="stylesheet" href="assets/css/ace-skins.min.css" />
		<link rel="stylesheet" href="assets/css/ace-rtl.min.css" />

		<link rel="stylesheet" href="assets/css/jquery-ui.min.css" />
		<link rel="stylesheet" href="assets/css/jquery-ui.theme.min.css" />

		<link rel="stylesheet" href="assets/js/jtable.2.4.0/themes/metro/blue/jtable.min.css" />

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
			.img-prev
			{
				cursor: zoom-in;
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
							<li>
								<i class="ace-icon fa fa-home home-icon"></i>
								<a href="index.php">Inicio</a>
							</li>
							<li class="active"><?php echo $editar ? 'Editar' : 'Agregar';?> periodo de nómina</li>
						</ul><!-- /.breadcrumb -->

					</div>

					<div class="page-content">
						<div class="page-header">
							<h1>
								<i class="fa fa-columns" aria-hidden="true"></i> <?php echo $editar ? 'Editar' : 'Agregar';?> periodo de nómina
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									<?php echo $editar ? 'Editar el periodo de n&oacute;mina # <b>'.str_pad($Periodo->id, 7, "0", STR_PAD_LEFT).'</b>' : 'Agregar un nuevo periodo de n&oacute;mina';?>

								</small>
							</h1>
						</div><!-- /.page-header -->
						<div class="row">
							<div class="col-xs-12">
								<?php if (!$editar): ?>

									<h3 class="header smaller lighter blue">
										Periodo:
										<small>Selecciona un periodo y presiona en Generar</small>
									</h3>
									<div class="input-group col-xs-5">

									</div>
									<div class="col-lg-5 col-md-6 col-sm-8 col-xs-12">
										<div class="input-group">
											<input class="form-control" type="text" name="date-range-picker" id="id-date-range-picker-1" />
											<span class="input-group-addon">
												<i class="fa fa-calendar bigger-110"></i>
											</span>
											<span class="input-group-btn">
												<button id="btnGenerarNomina" class="btn btn-white btn-info btn-bold" onclick='$("#my-modal-agregar-periodo").modal();'>
													<i class="ace-icon fa fa-calculator bigger-130 blue"></i>
													Generar!
												</button>
											</span>
										</div>
									</div>
								<?php endif; ?>

								<div class="col-xs-12">
									&nbsp;
								</div>
								<div class="col-xs-12">
									<div id="PersonTableContainer"></div>
								</div>
								<input type="hidden" id="hiddenFInicio" value="<?php echo date("Y-m-01");?>"/>
								<input type="hidden" id="hiddenFFin" value="<?php echo date("Y-m-d");?>"/>

							</div>
						</div>
								<!-- PAGE CONTENT ENDS -->
					</div><!-- /.col -->
				</div><!-- /.row -->
				<!--/////////////////////modal cancelar ////////////////////////-->
				<?php if (!$editar): ?>

					<div id="my-modal-agregar-periodo" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h3 class="smaller blue no-margin">
										<span class="smaller lighter blue no-margin">
											<i class="fa fa-ban" aria-hidden="true"></i>
										</span>
										Agregar un nuevo periodo
									</h3>
								</div>

								<div class="modal-body">
									<form class="form-horizontal" role="form" onSubmit="return false">
										<div class="form-group">
											<label class="col-xs-12 text-left">
												<i class="fa fa-question-circle" aria-hidden="true"></i>
												¿Deseas generar un nuevo periodo de n&oacute;mina dentro de las fechas seleccionadas?
											</label>
										</div>
									</form>
								</div>

								<div class="modal-footer">
									<button class="btn btn-white btn-primary btn-bold btn-round" data-dismiss="modal" onclick="recargarTabla(); "id="btnAgregarPeriodoModal">
										<i class="ace-icon fa fa-columns"></i>
										Agregar periodo
									</button>
									<button class="btn btn-white btn-default btn-bold no-border btn-round" data-dismiss="modal">
										<i class="ace-icon fa fa-times"></i>
										Cancelar
									</button>
								</div>
							</div><!-- /.modal-content -->
						</div><!-- /.modal-dialog -->
					</div>
				<?php endif; ?>

			</div><!-- /.page-content -->
			<div class="footer">
				<?php require_once('pie.php'); ?>
			</div>

			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
			</a>
		</div>
		<!-- basic scripts -->
		<!--[if !IE]> -->
		<script src="assets/js/jquery-2.1.4.min.js"></script>

		<!-- <![endif]-->

		<!--[if IE]>
<script src="assets/js/jquery-1.11.3.min.js"></script>
<![endif]-->
		<script src="assets/js/jquery-ui.min.js"></script>
		<script src="assets/js/jtable.2.4.0/jquery.jtable.min.js"></script>
		<script src="assets/js/jtable.2.4.0/localization/jquery.jtable.es.js"></script>
		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>
		<script src="assets/js/bootstrap.min.js"></script>
		<!-- page specific plugin scripts -->
		<script src="assets/js/bootbox.js"></script>
		<script src="assets/js/moment.min.js"></script>
		<script src="assets/js/daterangepicker.min.js"></script>
		<script src="assets/js/jquery.gritter.min.js"></script>
		<!-- ace scripts -->
		<script src="assets/js/ace-elements.min.js"></script>
		<script src="assets/js/ace.min.js"></script>
		<script src="assets/js/custom/primario.js"></script>
		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			$(document).ready(function()
			{
				abrirMenu();
				// $('.dataTables_length').prepend('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
				$('input[name=date-range-picker]').daterangepicker({
					'applyClass' : 'btn-sm btn-success btn-white btn-bold',
					"showWeekNumbers": true,
					"linkedCalendars": false,
					'cancelClass' : 'btn-sm btn-default btn-white btn-bold',
					"showWeekNumbers": true,
					"autoApply": false,
					// "minDate": "10/11/2019",
					//'showDropdowns': true,
					"locale": {
						"format": "DD/MM/YYYY",
						"separator": " - ",
						"applyLabel": "Apply",
						"cancelLabel": "Cancel",
						"fromLabel": "From",
						"toLabel": "To",
						"customRangeLabel": "Custom",
						"daysOfWeek": ["Dom","Lun","Mar","Mie","Jue","Vie","Sab"],
						"monthNames": ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Deciembre"],
						"firstDay": 0,
						applyLabel: 'Aplicar',
						cancelLabel: 'Cancelar'
					},
					startDate: moment($("#hiddenFInicio").val(), 'YYYY-MM-DD').format('DD-MM-YYYY'),
					endDate: moment($("#hiddenFFin").val(), 'YYYY-MM-DD').format('DD-MM-YYYY')
					// locale: {
					//
					// }
				},	function(start, end, label)
					{
						$("#hiddenFInicio").val(start.format('YYYY-MM-DD'));
						$("#hiddenFFin").val(end.format('YYYY-MM-DD'));
						console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
					})
				.next().on(ace.click_event, function(){
					$(this).prev().focus();
				});
			});
		</script>
		<script type="text/javascript">
		<?php if (!$editar): ?>
			function recargarTabla()
			{
				console.log($("#hiddenFInicio").val());
				console.log($("#hiddenFFin").val());
				$('#PersonTableContainer').jtable('load',{ fechaInicio: $("#hiddenFInicio").val(), fechaFin: $("#hiddenFFin").val() }, function()
				{
					$("#btnGenerarNomina").attr("disabled", true);
					mensaje("success","N&oacute;minas creadas correctamente");

				});
			}
		<?php else: ?>
			function cargarTabla()
			{
				$('#PersonTableContainer').jtable('load',{ idPeriodo: <?php echo $Periodo->id;?> });
			}
		<?php endif; ?>
		    $(document).ready(function ()
			{
				$('#PersonTableContainer').jtable({
					title: 'Lista de nóminas',
					actions:
					{
						<?php if (!$editar): ?>
							listAction: 'assets/ajax/listarRegistrosNominas.JSON.php'
						<?php else: ?>
							listAction: 'assets/ajax/listarRegistrosNominasEditar.JSON.php'
						<?php endif; ?>
					},
					fields:
					{
		                Conceptos:
						{
							title: '',
							width: '10%',
							sorting: false,
							edit: true,
							create: true,
		                    display: function (studentData) {
		                        //Create an image that will be used to open child table
		                        var $img = $('<img src="assets/js/jtable.2.4.0/themes/list_metro.png" title="Mostrar conceptos" />');
		                        //Open child table when user clicks the image
		                        $img.click(function () {
		                            $('#PersonTableContainer').jtable('openChildTable',
		                                    $img.closest('tr'),
		                                    {
		                                        title: studentData.record.nombres + ' - Detalle de conceptos',
												recordAdded: function (event, data)
												{
											        if (data.record) {
											            //$('#PersonTableContainer').jtable('load');
											        }
											    },
		                                        actions: {
		                                            listAction: 'assets/ajax/listarConceptosRegistrosNominas.JSON.php?idNomina=' + studentData.record.idNomina,
													createAction: 'assets/ajax/agregarConceptosRegistrosNominas.php?idNomina=' + studentData.record.idNomina + '&idUsuario=' + studentData.record.idUsuario + '&idSucursal=' + studentData.record.idSucursal,
													updateAction: 'assets/ajax/editarConceptosRegistrosNominas.php',
		                                            deleteAction: 'assets/ajax/eliminarConceptosRegistrosNominas.php'
		                                        },
		                                        fields: {
													idDetalle: {
														key: true
		                                            },
		                                            cantidad: {
		                                                title: 'Cantidad',
		                                                width: '10%',
														input: function (data) {
													        if (studentData.record) {
																console.log(studentData);
													            return '<input type="number" disabled name="cantidad" min="1" style="width:200px" value="1" />';
													        } else {
													            return '<input type="number" disabled name="cantidad" min="1" style="width:200px" value="1" />';
													        }
													    }
		                                            },
		                                            concepto: {
		                                                title: 'Concepto',
		                                                width: '50%'
		                                            },
													monto: {
		                                                title: 'Monto',
		                                                width: '20%',
		                                            },
													tipo: {
														title: "tipo",
														width: "20%",
														options:
														{
															<?php
																$rowTipoDetalleNomina = $query ->table("tipos_detalle_nomina")->select("*")->where("activo", "=", 1, "i")->execute();
																$jsonTipos = "";
																$cont = 0;
																foreach ($rowTipoDetalleNomina as $tipo)
																{
																	if ($cont == 0)
																		$jsonTipos = "'".$tipo['id']."':'".$tipo['nombre']."'";
																	else
																		$jsonTipos .= ",'".$tipo['id']."':'".$tipo['nombre']."'";
																	$cont++;
																}
																echo $jsonTipos;
															 ?>
															// '1': 'Percepción', '2': 'Deducción'
														}
													}
		                                        }
		                                    }, function (data) { //opened handler
		                                        data.childTable.jtable('load');
		                                    });
		                        });
		                        //Return image to show on the person row
		                        return $img;
		                    }
		                },
						idNomina:
						{
							key: true,
							list: false
						},
						nombres: {
							title: 'Nombres',
							width: '100%'
						}
					}
				});
				<?php if ($editar): ?>
					cargarTabla();
				<?php endif; ?>
		    });
		</script>
	</body>
</html>
<?php
	}
 ?>
