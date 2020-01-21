<?php
    require_once ('../connect/bd.php');
    require_once ("../connect/sesion.class.php");
    $sesion = new sesion();
    require_once ("../connect/cerrarOtrasSesiones.php");
    require_once ("../connect/usuarioLogeado.php");
    if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
    {
		header("Location: ".dirname(__FILE__)."../../salir.php");
    }
    else
    {
        // require "../php/responderJSON.php";
        $nombre             = (strlen($_GET['nombre']) > 0) ? $_GET['nombre'] : '<i class="text-muted">"Elige un nombre"</i>';
        $descripcion        = (strlen($_GET['descripcion']) > 0) ? $_GET['descripcion'] : '<i class="text-muted">"Sin descripción"</i>';
        $precio             = (is_numeric($_GET['precio'])) ? number_format($_GET['precio'],0,".",",") : "0.00";
        $arrayProductos     = json_decode($_GET['arrayProductos']);
        ?>

                    <div class="profile-user-info profile-user-info-striped">
						<div class="profile-info-row">
							<div class="profile-info-name"> Nombre</div>

							<div class="profile-info-value">
								<span><?php echo $nombre;?></span>
							</div>
						</div>
                        <div class="profile-info-row">
							<div class="profile-info-name"> Descripción </div>

							<div class="profile-info-value">
								<span><?php echo $descripcion;?></span>
							</div>
						</div>
					</div>
                    <div class="space-12"></div>
                    <table class="table table-bordered table-striped">
                        <thead class="thin-border-bottom">
                            <tr>
                                <th>
                                    <i class="ace-icon fa fa-caret-right blue"></i>Nombre elemento
                                </th>
                                <th>
                                    <i class="ace-icon fa fa-caret-right blue"></i>Tipo
                                </th>
                                <th>
                                    <i class="ace-icon fa fa-caret-right blue"></i>Precio unitario
                                </th>
                                <th>
                                    <i class="ace-icon fa fa-caret-right blue"></i>Cantidad
                                </th>
                                <th>
                                    <i class="ace-icon fa fa-caret-right blue"></i>Sub total
                                </th>
                            </tr>
                        </thead>

                        <tbody>
        <?php
                        $total = 0;
                        if (sizeof($arrayProductos) == 0)
                        {
        ?>
                            <tr>
                                <td colspan="5">
                                    <span class="text-muted">
                                        Sin elementos
                                    </span>
                                </td>
        <?php
                        }
                        else
                        {
                            foreach ($arrayProductos as $esteProducto)
                            {
        ?>
                                <tr>
        <?php
                                $nombreProducto         =   $esteProducto    ->nombre;
                                $servicio               =   ($esteProducto    ->servicio == 0) ? 0 : 1;
                                $idProducto = $esteProducto->id;
                                $cantidadProducto = $esteProducto->cantidad;
                                $precioProducto = (is_numeric($esteProducto->precio)) ? number_format($esteProducto->precio,2,".",",") : "0.00";
                                $total += $subTotal = $cantidadProducto * $esteProducto->precio;
                                if ($servicio)
                                {
                                    $tipo = '<span class="label label-purple label-white">
												<i class="ace-icon fa fa-cube bigger-120"></i>
												Servicio
    										</span>';
                                }
                                else
                                {
                                    $tipo = '<span class="label label-info label-white">
                                                <i class="ace-icon fa fa-tag bigger-120"></i>
                                                Producto
                                            </span>';
                                }
        ?>
                                    <td><?php echo $nombreProducto;?></td>
                                    <td class="text-center"><?php echo $tipo;?></td>
                                    <td class="text-right green"><b><?php echo "$".$precioProducto;?></b></td>
                                    <td class="text-right green"><b><?php echo $cantidadProducto;?></b></td>
                                    <td class="text-right blue"><b><?php echo "$".number_format($subTotal,2,".",",");?></b></td>
                                </tr>
        <?php
                            }
                        }
        ?>
                        </tbody>
                    </table>
                    <div class="hr hr8 hr-double"></div>
                    <div class="clearfix">
                        <div class="grid3">
							<span class="grey">
								&nbsp;
							</span>
                            <br>
							<h4 class="bigger pull-right">&nbsp;</h4>
						</div>
						<div class="grid3">
							<span class="grey">
								<i class="ace-icon fa fa-money fa-2x blue"></i>
								&nbsp; Precio calculado
							</span>
                            <br>
                            <h3 class="bigger pull-right grey"><?php echo "$".number_format($total,2,".",",");?></h3>
						</div>

						<div class="grid3">
							<span class="grey">
								<i class="ace-icon fa fa-money fa-2x purple"></i>
								&nbsp; Precio asignado
							</span>
                            <br>
                            <h3 class="bigger pull-right blue"><?php echo "$".$precio;?></h3>
						</div>
					</div>
                    <div class="hr hr8 hr-double"></div>
        <?php
        // responder($response, $mysqli);
    }
?>
