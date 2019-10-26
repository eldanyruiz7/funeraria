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
		$permiso = $usuario->permiso("listarClientes",$mysqli);
		if (!$permiso)
		{
			header("Location: index.php");
		}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>Lista de clientes</title>

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
							<li class="active">Listar clientes</li>
						</ul><!-- /.breadcrumb -->

					</div>

					<div class="page-content">
						<div class="page-header">
							<h1>
								<i class="fa fa-users" aria-hidden="true"></i> Clientes
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									Lista de clientes registrados
								</small>
							</h1>
						</div><!-- /.page-header -->
						<div class="row">
							<div class="col-xs-12">
								<div class="clearfix">
									<div class="pull-right tableTools-container"></div>
								</div>
								<div>
									<table id="dynamic-table" class="display table table-striped table-bordered table-hover" style="width:100%">
								        <thead>
								            <tr>
												<th></th>
												<th>Id</th>
								                <th>Nombre</th>
												<th>RFC</th>
												<th>Domicilio</th>
												<th>Fecha Nac</th>
												<th>Sucursal</th>
												<th></th>
								            </tr>
								        </thead>
								    </table>
								</div>
							</div>
						</div>
								<!-- PAGE CONTENT ENDS -->
					</div><!-- /.col -->
				</div><!-- /.row -->
				<!-- //////////////////////////// Modal modificar producto ////////////////////////////////// -->
				<div id="my-modal-edit" class="modal fade" tabindex="-1">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h3 class="smaller lighter blue no-margin">
									<span class="smaller lighter green no-margin">
										<i class="fa fa-pencil-square-o" aria-hidden="true"></i>
									</span>
									Editar cliente
								</h3>
							</div>

							<div class="modal-body">
								<form class="form-horizontal" role="form" id="form-editar-cliente" onSubmit="return false">
									<input type="hidden" id="inputIdEdit" name="inputIdEdit">
									<div class="form-group divError">
										<label class="col-sm-3 control-label no-padding-right"> Nombres (*) </label>

										<div class="col-sm-9">
											<input type="text" id="inputNombreEdit" autocomplete="off" name="inputNombreEdit" placeholder="Nombre del cliente" class="form-control">
										</div>
									</div>
									<div class="form-group divError">
										<label class="col-sm-3 control-label no-padding-right"> Apellido paterno (*) </label>

										<div class="col-sm-9">
											<input type="text" id="inputApellidopEdit" autocomplete="off" name="inputApellidopEdit" placeholder="Apellido paterno" class="form-control">
										</div>
									</div>
									<div class="form-group divError">
										<label class="col-sm-3 control-label no-padding-right"> Apellido materno </label>

										<div class="col-sm-9">
											<input type="text" id="inputApellidomEdit" autocomplete="off" name="inputApellidomEdit" placeholder="Apellido materno" class="form-control">
										</div>
									</div>
									<div class="form-group divError">
										<label class="col-sm-3 control-label no-padding-right"> Domicilio (Calle, No Ext, No Int) (*) </label>

										<div class="col-sm-9">
											<input type="text" id="inputDomicilio1Edit" autocomplete="off" name="inputDomicilio1Edit" placeholder="Domicilio del cliente" class="form-control">
										</div>
									</div>
									<div class="form-group divError">
										<label class="col-sm-3 control-label no-padding-right"> Domicilio (Colonia, Población, Municipio) (*) </label>

										<div class="col-sm-9">
											<input type="text" id="inputDomicilio2Edit" autocomplete="off" name="inputDomicilio2Edit" placeholder="Domicilio del cliente" class="form-control">
										</div>
									</div>
									<div class="form-group divError">
										<label class="col-sm-3 control-label no-padding-right"> Código Postal (*) </label>

										<div class="col-sm-9">
											<input type="text" id="inputCPEdit" autocomplete="off" name="inputCPEdit" placeholder="Código postal" class="form-control">
										</div>
									</div>
									<div class="form-group divError">
										<label class="col-sm-3 control-label no-padding-right"> Estado (*) </label>

										<div class="col-sm-9">
											<select class="form-control" id="inputEstadoEdit" name="inputEstadoEdit">
		<?php
				$sql = "SELECT * FROM cat_estados WHERE activo = 1";
				$res_estado = $mysqli->query($sql);
				while ($row_estado = $res_estado->fetch_assoc())
					echo "<option value=".$row_estado['id'].">".$row_estado['estado']."</option>";
		 ?>
											</select>
										</div>
									</div>
									<div class="form-group divError">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> RFC (*) </label>

										<div class="col-sm-9">
											<input type="text" id="inputRFCEdit" autocomplete="off" placeholder="RFC" name="inputRFCEdit" class="form-control">
										</div>
									</div>
									<div class="form-group divError">
										<label class="col-sm-3 control-label no-padding-right"> Fecha nac (*) </label>

										<div class="col-sm-9">
											<input type="date" id="inputFechaNacEdit" autocomplete="off" name="inputFechaNacEdit" class="form-control">
										</div>
									</div>
									<div class="form-group divError">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Teléfono </label>

										<div class="col-sm-9">
											<input type="text" id="inputTelefonoEdit" autocomplete="off" name="inputTelefonoEdit" class="form-control input-mask-phone">
										</div>
									</div>
									<div class="form-group divError">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Celular </label>

										<div class="col-sm-9">
											<input type="text" id="inputCelularEdit" autocomplete="off" name="inputCelularEdit" class="form-control input-mask-phone">
										</div>
									</div>
									<div class="form-group divError">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> E-mail </label>

										<div class="col-sm-9">
											<input type="text" id="inputEmailEdit" autocomplete="off" name="inputEmailEdit" class="form-control">
										</div>
									</div>
								</form>
							</div>

							<div class="modal-footer">
								<button class="btn btn-white btn-info btn-bold btn-round" id="btnEditarProducto">
									<span class="load">
										<i class="ace-icon fa fa-floppy-o"></i>
									</span>
									Actualizar
								</button>
								<button class="btn btn-white btn-danger btn-bold no-border btn-round" data-dismiss="modal">
									<i class="ace-icon fa fa-times"></i>
									Cancelar
								</button>
							</div>
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div>
				<!--/////////////////////modal eliminar cliente ////////////////////////-->
				<div id="my-modal-eliminar" class="modal fade" tabindex="-1">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h3 class="smaller red no-margin">
									<span class="smaller lighter red no-margin">
										<i class="fa fa-user-times" aria-hidden="true"></i>
									</span>
									Eliminar este registro del sistema
								</h3>
							</div>

							<div class="modal-body">
								<form class="form-horizontal" role="form" onSubmit="return false">
									<div class="form-group">
										<label class="col-xs-12 text-left"><i class="fa fa-question-circle" aria-hidden="true"></i> ¿Deseas eliminar este registro del sistema? <br/>Esta acci&oacute;n no se puede deshacer</label>
									</div>
								</form>
								<input type="hidden" id="hiddenEliminar" />
							</div>

							<div class="modal-footer">
								<button class="btn btn-white btn-danger btn-bold btn-round" id="btnEliminarModal">
									<i class="ace-icon fa fa-user-times"></i>
									Eliminar registro
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
		<script src="assets/js/buttons.flash.min.js"></script>
		<script src="assets/js/buttons.html5.min.js"></script>
		<script src="assets/js/buttons.print.min.js"></script>
		<script src="assets/js/pdfmake.min.js"></script>
		<script src="assets/js/vfs_fonts.js"></script>
		<script src="assets/js/buttons.colVis.min.js"></script>
		<script src="assets/js/dataTables.select.min.js"></script>
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
			function format ( d ) {
				idCliente 	= d.id;
				// alert("Logrado");
				console.log(d);
				telefono = d.telefono;
				celular = d.celular;
				nombreSucursal = d.nombreSucursal;
				email		= d.email;
				html = '<div class="col-xs-10">';
				html +='    <div class="table-responsive">';
				html +='       <table class="table table-striped table-bordered table-hover">';
				html +='			<tr>';
				html +='				<td>';
				html +='					<b>Teléfono:</b> '+telefono;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Celular:</b> '+celular;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Sucursal:</b> '+nombreSucursal;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>E-mail:</b> '+email;
				html +='				</td>';
				html +='			</tr>';
				html +='		</table>';
				html +='	</div>';
				html +='</div>';
				return html;
			}
			$(document).ready(function()
			{
				abrirMenu();
				$('.input-mask-phone').mask('(999) 999-9999');
				$(document).on("click",".aEdit", function()
				{
					icon = $(this);
					idCliente = icon.attr('id');
					iconOriginal = icon.html();
					icon.html(cargarSpinner);
					$.ajax(
			        {
			            method: "POST",
			            url:"assets/ajax/infoCliente.php",
			            data: {idCliente:idCliente}
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
							$("#inputNombreEdit").val(p.nombre);
							$("#inputApellidopEdit").val(p.apellidop);
							$("#inputApellidomEdit").val(p.apellidom);
							$("#inputDomicilio1Edit").val(p.domicilio1);
							$("#inputDomicilio2Edit").val(p.domicilio2);
							$("#inputCPEdit").val(p.cp);
							$("#inputEstadoEdit").val(p.idEstado);
							$("#inputRFCEdit").val(p.rfc);
							$("#inputFechaNacEdit").val(p.fechaNac);
							$("#inputTelefonoEdit").val(p.telefono);
							$("#inputCelularEdit").val(p.celular);
							$("#inputEmailEdit").val(p.email);
							$("#inputIdEdit").val(p.id);
							$('#my-modal-edit').modal();
						}
			        })
			        .always(function(p)
			        {
						console.log(p);
						icon.html(iconOriginal);
			        });

					//$("#my-modal-edit").modal();
				});
				$(document).on('click','.btnEliminar',function()
				{
					idEliminar = $(this).attr('idCliente');
					$("#hiddenEliminar").val(idEliminar);
					$("#my-modal-eliminar").modal();
				});
				$(document).on('click','#btnEliminarModal',function()
				{
					$("#my-modal-eliminar").modal('hide');
					dialog = bootbox.dialog(
					{
					    title: 'Eliminar',
					    message: '<p><i class="fa fa-spin fa-spinner"></i> Procesando...</p>',
						closeButton: true,
						buttons:
						{
							"aceptar" :
							{
								"label" : '<i class="fa fa-check-circle" aria-hidden="true"></i> Aceptar',
								"className" : "btn-white btn-info btn-bold btn-round oculto",
								callback: function(result)
								{
									myTable.ajax.reload( null, false );
								}
							}
						}

					});
					idPac_ = $("#hiddenEliminar").val();
					$.ajax(
			        {
			            method: "POST",
			            url:"assets/ajax/eliminarCliente.php",
			            data: {idCliente:idPac_}
			        })
			        .done(function(p)
			        {
						console.log(p);
						if (p.status == 1)
						{
							dialog.init(function()
							{
							 	dialog.find('.bootbox-body').html('<i class="fa fa-check" aria-hidden="true"></i> Este registro ha sido eliminado correctamente');
								dialog.find('.oculto').removeClass('oculto');
								// dialog.find('button')
								// body > div.bootbox.modal.fade.in > div > div > div.modal-footer > button:nth-child(3)
							});
							$.gritter.add(
						   	{
								title: "Éxito",
								text: p.mensaje,
								class_name: 'gritter-success'
							});
						}
						else
						{
							dialog.init(function()
							{
							 	dialog.find('.bootbox-body').html('<i class="fa fa-times" aria-hidden="true"></i> No se pudo eliminar este registro');
								dialog.find('.oculto').removeClass('oculto');
								// dialog.find('button')
								// body > div.bootbox.modal.fade.in > div > div > div.modal-footer > button:nth-child(3)
							});
							$.gritter.add(
							{
								title: "Error",
								text: p.mensaje,
								class_name: 'gritter-error'
							});
						}
			        })
			        .always(function(p)
			        {
						console.log(p);
			        });

				});
				$("#btnEditarProducto").click(function()
				{
					icon = $(this).find(".load");
					iconOriginal = icon.html();
					icon.html(cargarSpinner);
					$(".divError").removeClass('has-error');
					console.log($("#form-editar-cliente").serialize());
					$.ajax(
			        {
			            method: "POST",
			            url:"assets/ajax/editarCliente.php",
			            data: $("#form-editar-cliente").serialize()
			        })
			        .done(function(p)
			        {
						console.log(p);
						if (p.status == 0)
						{
							 mensaje("error",p.mensaje);
							 $("#"+p.focus).parent().parent().addClass('has-error');
						}
						else if (p.status == 1)
						{
							$('#my-modal-edit').modal('hide');
							mensaje("success",p.mensaje);
							myTable.ajax.reload( null, false );
						}
						else
						{
							mensaje("warning",p.mensaje);
							$('#my-modal-edit').modal('hide');

						}
			        })
			        .always(function(p)
			        {
						console.log(p);
						icon.html(iconOriginal);
			        });
				});
			} );
			jQuery(function($) {

				myTable = $('#dynamic-table').DataTable( {

			        "ajax":
					{
						"url":"assets/ajax/listarClientes.JSON.php",
						"complete": function()
						{
							myTable.columns.adjust().draw();
						}
					},
					"processing": 	true,
					"aLengthMenu":[
						[10, 25, 50, -1],
						[10, 25, 50, "Todos"]
					],
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
			            { "data": "nombresCliente" },
						{ "data": "rfcCliente" },
						{ "data": "domicilio" },
						{ "data": "fechaNacCliente" },
			            { "data": "sucursal" },
			            // { "data": "direccion" },
			            {
							"data": 			"btns",
						 	"orderable":      	false
						}
			        ],
					'createdRow': function( row, data, dataIndex ) {
					     $(row).attr('id', data.id);
					 },
			        "order": 		[[0, 'asc']]
			    } );
				$.fn.dataTable.Buttons.defaults.dom.container.className = 'dt-buttons btn-overlap btn-group btn-overlap';
				new $.fn.dataTable.Buttons( myTable, {
					buttons: [
					  {
						"extend": "colvis",
						"text": "<i class='fa fa-search bigger-110 blue'></i> <span class='hidden'>Mostrar/Ocultar columnas</span>",
						"className": "btn btn-white btn-primary btn-bold",
						columns: ':not(:first):not(:last)'
					  },
					  {
						"extend": "copy",
						"text": "<i class='fa fa-copy bigger-110 pink'></i> <span class='hidden'>Copiar al Portapapeles</span>",
						"className": "btn btn-white btn-primary btn-bold"
					  },
					  {
						"extend": "csv",
						"text": "<i class='fa fa-database bigger-110 orange'></i> <span class='hidden'>Exportar formato CSV</span>",
						"className": "btn btn-white btn-primary btn-bold"
					  },
					  {
						"extend": "excel",
						"text": "<i class='fa fa-file-excel-o bigger-110 green'></i> <span class='hidden'>Exportar formato Excel</span>",
						"className": "btn btn-white btn-primary btn-bold"
					  },
					  {
						"extend": "pdf",
						"text": "<i class='fa fa-file-pdf-o bigger-110 red'></i> <span class='hidden'>Exportar formato PDF</span>",
						"className": "btn btn-white btn-primary btn-bold",
						"message": "Reporte de lista de clientes"
					},
					 {
						"extend": "print",
						"text": "<i class='fa fa-print bigger-110 grey'></i> <span class='hidden'>Imprimir</span>",
						"className": "btn btn-white btn-primary btn-bold",
						autoPrint: true,
						message: 'Reporte de lista de clientes'
					  }
					]
				} );
				myTable.buttons().container().appendTo( $('.tableTools-container') );

				//style the message box
				var defaultCopyAction = myTable.button(2).action();
				myTable.button(2).action(function (e, dt, button, config) {
					defaultCopyAction(e, dt, button, config);
					$('.dt-button-info').addClass('gritter-item-wrapper gritter-info gritter-center white');
				});


				var defaultColvisAction = myTable.button(1).action();
				myTable.button(1).action(function (e, dt, button, config) {

					defaultColvisAction(e, dt, button, config);
					if($('.dt-button-collection > .dropdown-menu').length == 0) {
						$('.dt-button-collection')
						.wrapInner('<ul class="dropdown-menu dropdown-light dropdown-caret dropdown-caret" />')
						.find('a').attr('href', '#').wrap("<li />")
					}
					$('.dt-button-collection').appendTo('.tableTools-container .dt-buttons')
				});
				setTimeout(function() {
					$($('.tableTools-container')).find('a.dt-button').each(function() {
						var div = $(this).find(' > div').first();
						if(div.length == 1) div.tooltip({container: 'body', title: div.parent().text()});
						else $(this).tooltip({container: 'body', title: $(this).text()});
					});
				}, 500);
				myTable.on( 'select', function ( e, dt, type, index ) {
					if ( type === 'row' ) {
						$( myTable.row( index ).node() ).find('input:checkbox').prop('checked', true);
					}
				} );
				myTable.on( 'deselect', function ( e, dt, type, index ) {
					if ( type === 'row' ) {
						$( myTable.row( index ).node() ).find('input:checkbox').prop('checked', false);
					}
				} );
				$('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);

				//select/deselect all rows according to table header checkbox
				$('#dynamic-table > thead > tr > th input[type=checkbox], #dynamic-table_wrapper input[type=checkbox]').eq(0).on('click', function(){
					var th_checked = this.checked;//checkbox inside "TH" table header

					$('#dynamic-table').find('tbody > tr').each(function(){
						var row = this;
						if(th_checked) myTable.row(row).select();
						else  myTable.row(row).deselect();
					});
				});

				//select/deselect a row when the checkbox is checked/unchecked
				$('#dynamic-table').on('click', 'td input[type=checkbox]' , function(){
					var row = $(this).closest('tr').get(0);
					if(this.checked) myTable.row(row).deselect();
					else myTable.row(row).select();
				});



				$(document).on('click', '#dynamic-table .dropdown-toggle', function(e) {
					e.stopImmediatePropagation();
					e.stopPropagation();
					e.preventDefault();
				});

				//And for the first simple table, which doesn't have TableTools or dataTables
				//select/deselect all rows according to table header checkbox
				var active_class = 'active';
				$('#simple-table > thead > tr > th input[type=checkbox]').eq(0).on('click', function(){
					var th_checked = this.checked;//checkbox inside "TH" table header

					$(this).closest('table').find('tbody > tr').each(function(){
						var row = this;
						if(th_checked) $(row).addClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', true);
						else $(row).removeClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', false);
					});
				});

				//select/deselect a row when the checkbox is checked/unchecked
				$('#simple-table').on('click', 'td input[type=checkbox]' , function(){
					var $row = $(this).closest('tr');
					if($row.is('.detail-row ')) return;
					if(this.checked) $row.addClass(active_class);
					else $row.removeClass(active_class);
				});

				/********************************/
				//add tooltip for small view action buttons in dropdown menu
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
							url:"assets/ajax/infoCliente.php",
							dataType:'JSON',
							data: {idCliente:idCliente}
						})
						.always(function(p)
						{
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
			});
		</script>
	</body>
</html>
<?php
	}
 ?>
