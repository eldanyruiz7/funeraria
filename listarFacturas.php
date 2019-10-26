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
		// $permiso = $usuario->permiso("listarVentas",$mysqli);
		// if (!$permiso)
		// {
		// 	header("Location: index.php");
		// }
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>Lista de facturas</title>

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
							<li class="active">Listar facturas</li>
						</ul><!-- /.breadcrumb -->

					</div>

					<div class="page-content">
						<div class="page-header">
							<h1>
								<i class="fa fa-bolt" aria-hidden="true"></i> Facturas
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									Lista de facturas emitidas, por rango de fechas
								</small>
							</h1>
						</div><!-- /.page-header -->
						<div class="row">
							<div class="col-xs-12">
								<input type="hidden" id="hiddenFInicio" value="<?php echo date("Y-m-d");?>"/>
								<input type="hidden" id="hiddenFFin" value="<?php echo date("Y-m-d");?>"/>
								<div class="clearfix">
									<div class="pull-right tableTools-container"></div>
								</div>
								<div>
									<table id="dynamic-table" class="display table table-striped table-bordered table-hover" style="width:100%">
								        <thead>
								            <tr>
												<th></th>
												<th>Id</th>
												<th>Fecha creación</th>
												<th>Rfc Receptor</th>
												<th>Nombre Receptor</th>
												<th>Sub Total</th>
												<th>IVA</th>
								                <th>Total</th>
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
				<!--/////////////////////modal cancelar ////////////////////////-->
				<div id="my-modal-eliminar" class="modal fade" tabindex="-1">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h3 class="smaller red no-margin">
									<span class="smaller lighter red no-margin">
										<i class="fa fa-ban" aria-hidden="true"></i>
									</span>
									Cancelar este registro del sistema
								</h3>
							</div>

							<div class="modal-body">
								<form class="form-horizontal" role="form" onSubmit="return false">
									<div class="form-group">
										<label class="col-xs-12 text-left"><i class="fa fa-question-circle" aria-hidden="true"></i> ¿Deseas cancelar esta venta del sistema? <br/> Esta acción no puede deshacerse.<br/>El sistema intentará regresar el inventario a su estado antes de efectuar esta venta.</label>
									</div>
								</form>
								<input type="hidden" id="hiddenEliminar" />
							</div>

							<div class="modal-footer">
								<button class="btn btn-white btn-danger btn-bold btn-round" id="btnEliminarModal">
									<i class="ace-icon fa fa-ban"></i>
									Cancelar factura
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
		<script src="assets/js/moment.min.js"></script>
		<script src="assets/js/daterangepicker.min.js"></script>
		<script src="assets/js/jquery.gritter.min.js"></script>
		<!-- ace scripts -->
		<script src="assets/js/ace-elements.min.js"></script>
		<script src="assets/js/ace.min.js"></script>
		<script src="assets/js/custom/primario.js"></script>
		<!-- inline scripts related to this page -->
		<script type="text/javascript">
			myTable = "";
			function format ( d )
			{
				console.log(d);
				html ='<div class="col-xs-12">';
				html +='    <div class="table-responsive">';
				html +='       <table class="table table-striped table-bordered table-hover">';
				html +='			<tr>';
				html +='				<td>';
				html +='					<b>Clave SAT</b>';
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Cantidad</b>';
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Unidad</b>';
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Descripción</b>';
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Precio U.</b>';
				html +='				</td>';
				html +='				<td>';
				html +='					<b>IVA</b>';
				html +='				</td>';
				html +='				<td>';
				html +='					<b>IEPS</b>';
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Importe</b>';
				html +='				</td>';
				html +='			</tr>'+d.html_hist;
				html +='		</table>';
				html +='	</div>';
				html +='</div>';
				html += '<div class="col-xs-12">';
				html +='    <div class="table-responsive">';
				html +='       <table class="table table-striped table-bordered table-hover">';
				html +='			<tr>';
				html +='				<td>';
				html +='					<b>Fecha creación:</b><br> '+d.fechaCreacion;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Fecha emisión:</b><br> '+d.fechaEmision;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Fecha certificación:</b><br> '+d.fechaCertificacion;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Sucursal:</b><br> '+d.sucursal;
				html +='				</td>';
				html +='			</tr>';
				html +='			<tr>';
				html +='				<td>';
				html +='					<b>Rfc Emisor:</b><br> '+d.rfcEmisor;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>R. Social Emisor:</b><br> '+d.razonEmisor;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Régimen Emisor:</b><br> '+d.regimenEmisor;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Domicilio Emisor:</b><br> '+d.domicilioEmisor;
				html +='				</td>';
				html +='			</tr>';
				html +='			<tr>';
				html +='				<td>';
				html +='					<b>rfc Receptor:</b><br> '+d.rfcReceptor;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>R. Social Receptor:</b><br> '+d.razonReceptor;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Domicilio receptor:</b><br> '+d.domicilioReceptor;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>E-mail receptor:</b><br> '+d.emailReceptor;
				html +='				</td>';
				html +='			</tr>';
				html +='			<tr>';
				html +='				<td>';
				html +='					<b>Uso CFDI:</b><br> '+d.usoCFDI;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Tipo CFDI:</b><br> '+d.tipoCFDI;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Moneda:</b><br> '+d.moneda;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Forma de pago:</b><br> '+d.formaPago;
				html +='				</td>';
				html +='			</tr>';
				html +='			<tr>';
				html +='				<td colspan=2>';
				html +='					<b>Folio fiscal:</b><br> '+d.folioFiscal;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>No. Certificado:</b><br> '+d.noCertificado;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>No. Certificado SAT:</b><br> '+d.noCertificadoSAT;
				html +='				</td>';
				html +='			</tr>';
				html +='			<tr>';
				html +='				<td colspan=2>';
				html +='					<b>Generó:</b><br> '+d.nombresUsuario;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Versión CFDI:</b><br> '+d.version;
				html +='				</td>';
				html +='				<td>';
				html +='					<b>Método pago:</b><br> '+d.metodoPago;
				html +='				</td>';
				html +='			</tr>';
				html +='			<tr>';
				html +='				<td colspan=4>';
				html +='					<b>Validar este CFDI ante el Sat (Código QR):</b><br> '+d.codigoQR;
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
					    title: 'Cancelar',
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
									setTimeout(function()
									{
										$("body").css("padding-right",0);
								    }, 500);
								}
							}
						}

					});
					idPac_ = $("#hiddenEliminar").val();
					$.ajax(
			        {
			            method: "POST",
			            url:"assets/ajax/eliminarVenta.php",
			            data: {idCliente:idPac_}
			        })
			        .done(function(p)
			        {
						console.log(p);
						if (p.status == 1)
						{
							dialog.init(function()
							{
							 	dialog.find('.bootbox-body').html('<i class="fa fa-check" aria-hidden="true"></i> Esta venta ha sido cancelada correctamente');
								dialog.find('.oculto').removeClass('oculto');
								// dialog.find('button')
								// body > div.bootbox.modal.fade.in > div > div > div.modal-footer > button:nth-child(3)
							});
							mensaje("success",p.mensaje);
						}
						else
						{
							dialog.init(function()
							{
							 	dialog.find('.bootbox-body').html('<i class="fa fa-times" aria-hidden="true"></i> No se pudo cancelar este registro');
								dialog.find('.oculto').removeClass('oculto');
								// dialog.find('button')
								// body > div.bootbox.modal.fade.in > div > div > div.modal-footer > button:nth-child(3)
							});
							mensaje("error",p.mensaje);
						}
			        })
			        .always(function(p)
			        {
						console.log(p);
			        });

				});
				$(document).on("click","h4.widget-title",function()
				{
					$(this).next().find(".blue").click();
				});
			});
			jQuery(function($) {

				myTable = $('#dynamic-table').DataTable( {

			        "ajax":
					{
						"url":"assets/ajax/listarFacturas.JSON.php",
						"type": "GET",
						"data": function(d)
						{
							d.fechaInicio =  $("#hiddenFInicio").val();
							d.fechaFin = $("#hiddenFFin").val();
						},
						"complete": function()
						{
							myTable.columns.adjust().draw();
						}
					},
					"processing": 	true,
					"initComplete": function(settings, json)
					{
						$('.dataTables_length').prepend('Rango de fechas: <div class="input-group"><input class="form-control input-sm" type="text" name="date-range-picker" id="id-date-range-picker-1" /><span class="input-group-addon"><i class="fa fa-calendar bigger-110"></i></span></div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
						$('input[name=date-range-picker]').daterangepicker({
							'applyClass' : 'btn-sm btn-success btn-white btn-bold',
							'cancelClass' : 'btn-sm btn-default btn-white btn-bold',
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
						    }
							// locale: {
							//
							// }
						},	function(start, end, label)
							{
								$("#hiddenFInicio").val(start.format('YYYY-MM-DD'));
								$("#hiddenFFin").val(end.format('YYYY-MM-DD'));
								myTable.ajax.reload();
						    	console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
						  	})
						.next().on(ace.click_event, function(){
							$(this).prev().focus();
						});

					},
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
						{ "data": "fechaCreacion" },
						{ "data": "rfcReceptor"},
						{ "data": "razonReceptor"},
						{
							"data": "subTotal",
						 	"className":"text-right"
						},
						{
							"data": "iva",
						 	"className":"text-right"
						},
						{
							"data": "total",
						 	"className":"text-right"
						},
						{ "data": "nombreSucursal"},
			            {
							"data": 			"btns",
						 	"orderable":      	false
						}
			        ],
					'createdRow': function( row, data, dataIndex ) {
					     $(row).attr('id', data.id);
					 },
			        "order": 		[[1, 'desc']]
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
							url:"assets/ajax/obtenerRowDet_factura.php",
							dataType:'JSON',
							data: {idCliente:idCliente}
						})
						.always(function(p)
						{
							$("td.details-control").empty();
							console.log(p);
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
