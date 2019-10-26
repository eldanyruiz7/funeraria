<?php
    require_once ('../connect/bd.php');
    require_once ("../connect/sesion.class.php");
    $sesion = new sesion();
    require_once ("../connect/cerrarOtrasSesiones.php");
    require_once ("../connect/usuarioLogeado.php");
    require_once ("../php/funcionesVarias.php");
    if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
    {
        header("Location: salir.php");
    }
    else
    {
        // var_dump($_GET);
        // die;
        if (!$idPlan = validarFormulario('i',$_GET['idPlan']))
        {
    ?>
            </div class="col-xs-12">
                <div class="alert alert-warning">
                    <strong><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Alerta. </strong>
                    Para poder realizar una corrida financiera primero debes elegir un plan
                    <br>
                </div>
            </div>
    <?php
            die;
        }
        $sql = "SELECT id FROM cat_planes WHERE id = ? AND activo = 1 LIMIT 1";
        $prepare_plan = $mysqli->prepare($sql);
        if (
        	$prepare_plan &&
        	$prepare_plan -> bind_param('i', $idPlan) &&
        	$prepare_plan -> execute() &&
        	$prepare_plan -> store_result() &&
        	$prepare_plan -> bind_result($idPlan_) &&
        	$prepare_plan -> fetch())
        {
        	if($prepare_plan->num_rows == 0)
            {
                ?>
                    </div class="col-xs-12">
                        <div class="alert alert-warning">
                            <strong><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Alerta. </strong>
                            El plan elegido ya no se encuentra disponible, posiblemente fue eliminado
                            <br>
                        </div>
                    </div>
                <?php
                die;
            }
        }
        else
        {
            ?>
                </div class="col-xs-12">
                    <div class="alert alert-warning">
                        <strong><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Alerta. </strong>
                        El plan elegido ya no se encuentra disponible, posiblemente fue eliminado. Elige otro distinto
                        <br>
                    </div>
                </div>
            <?php
            die;
        }
        $precio         = validarFormulario('i',$_GET['precio'],0);
        $anticipo       = validarFormulario('i',$_GET['anticipo'],0);
        $aportacion     = validarFormulario('i',$_GET['aportacion'],0);
        $descuentoDup   = validarFormulario('i',$_GET['descuentoDuplicacionInversion']);
        $descuentoCam   = validarFormulario('i',$_GET['descuentoCambioFuneraria']);
        $descuentoAdi   = validarFormulario('i',$_GET['descuentoAdicional']);
        if (!$precio || !$anticipo || !$aportacion)
        {
            ?>
                </div class="col-xs-12">
                    <div class="alert alert-warning">
                        <strong><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Alerta. </strong>
                        El formato de los campos <strong>Costo total</strong>, <strong>anticipo</strong> y <strong>Aportación</strong>
                        deben ser numéricos y mayores que 0 (cero)
                        <br>
                    </div>
                </div>
            <?php
            die;
        }
        if ($descuentoDup === FALSE || $descuentoCam === FALSE || $descuentoAdi === FALSE)
        {
            ?>
                </div class="col-xs-12">
                    <div class="alert alert-warning">
                        <strong><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Alerta. </strong>
                        <?php var_dump($descuentoDup) ?>
                        El formato de los campos <strong>descuento por duplicación de inversión</strong>, <strong>descuento por cambio de funeraria</strong> y <strong>descuento adicional</strong>
                        deben ser numéricos o iguales o mayores que 0 (cero)
                        <br>
                    </div>
                </div>
            <?php
            die;
        }
        $saldo              = $_GET['precio'];
        if ($saldo < ($descuentoDup + $descuentoCam + $descuentoAdi)) {
            ?>
                </div class="col-xs-12">
                    <div class="alert alert-warning">
                        <strong><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Alerta. </strong>
                        La suma total de los descuentos no puede ser mayor o igual que el costo total del plan.
                        <br>
                    </div>
                </div>
            <?php
            die;
        }
        $nombre             = (strlen($_GET['nombre']) > 0) ? $_GET['nombre'] : '<i class="text-muted">"Elige un plan funerario"</i>';
        $fechaInicio        = (strlen($_GET['fechaAportacion']) > 0) ? $_GET['fechaAportacion'] : date('Y-m-d');
        $fechaInicioFormat  = date_create($fechaInicio);
        $fechaInicioFormat  = date_format($fechaInicioFormat,"d-m-Y");
        $anticipo           = $_GET['anticipo'];
        $frecuencia         = intval($_GET['frecuencia']);
        // $aportacion         = $_GET['aportacion'];
        $precio             = (is_numeric($_GET['precio'])) ? number_format($_GET['precio'],2,".",",") : "0.00";
        switch ($frecuencia)
        {
                case 1:
                $frecuenciaPago     = "Semanal";
                break;
                case 2:
                $frecuenciaPago     = "Quincenal";
                break;
                case 3:
                $frecuenciaPago     = "Mensual";
                break;
                default:
                $frecuenciaPago     = "Semanal";
                break;
        }
        $pagoNo = 0;

        ?>
                    <div class="profile-user-info profile-user-info-striped">
						<div class="profile-info-row">
							<div class="profile-info-name" style="width:170px"> Nombre</div>

							<div class="profile-info-value">
								<span><?php echo $nombre;?></span>
							</div>
						</div>
                        <div class="profile-info-row">
							<div class="profile-info-name"> Fecha inicio </div>

							<div class="profile-info-value">
								<span><?php echo $fechaInicioFormat;?></span>
							</div>
						</div>
                        <div class="profile-info-row">
                            <div class="profile-info-name"> Costo plan </div>
                            <div class="profile-info-value">
                                <span>$<?php echo $precio;?></span>
                            </div>
                        </div>


                        <div class="profile-info-row">
                            <div class="profile-info-name"> Desc. Duplic. Inv. </div>
                            <div class="profile-info-value">
                                <span>$<?php echo number_format($descuentoDup,2,".",",");?></span>
                            </div>
                        </div>
                        <div class="profile-info-row">
                            <div class="profile-info-name"> Desc. Cambio Funeraria </div>
                            <div class="profile-info-value">
                                <span>$<?php echo number_format($descuentoCam,2,".",",");?></span>
                            </div>
                        </div>
                        <div class="profile-info-row">
                            <div class="profile-info-name"> Desc. Adicional </div>
                            <div class="profile-info-value">
                                <span>$<?php echo number_format($descuentoAdi,2,".",",");?></span>
                            </div>
                        </div>
                        <?php
                        $saldo  = $saldo- $descuentoDup - $descuentoCam - $descuentoAdi;
                         ?>
                        <div class="profile-info-row">
                            <div class="profile-info-name"> Costo total </div>
                            <div class="profile-info-value">
                                <span>$<?php echo number_format($saldo,2,".",",");?></span>
                            </div>
                        </div>

                        <div class="profile-info-row">
							<div class="profile-info-name"> Anticipo </div>
							<div class="profile-info-value">
								<span>$<?php echo number_format($anticipo,2,".",",");?></span>
							</div>
						</div>
                        <div class="profile-info-row">
							<div class="profile-info-name"> Apotacion </div>
							<div class="profile-info-value">
								<span>$<?php echo number_format($aportacion,2,".",",");?></span>
							</div>
						</div>
                        <div class="profile-info-row">
							<div class="profile-info-name"> Frecuencia </div>
							<div class="profile-info-value">
								<span><?php echo $frecuenciaPago;?></span>
							</div>
						</div>
					</div>
                    <div class="space-12"></div>
                    <table class="table table-bordered table-striped">
                        <thead class="thin-border-bottom">
                            <tr>
                                <th>
                                    <i class="ace-icon fa fa-ticket grey"></i> No. Pago
                                </th>
                                <th>
                                    <i class="ace-icon fa fa-calendar grey"></i> Fecha
                                </th>
                                <th>
                                    <i class="ace-icon fa fa-dollar gray"></i> Aportación
                                </th>
                                <th>
                                    <i class="ace-icon fa fa-money grey"></i> Saldo
                                </th>
                            </tr>
                        </thead>
                        <tr>
                            <td class="text-right grey">Inversión</td>
                            <td class="text-center green"><b><?php echo date('d-m-Y');?></b></td>
                            <td class="text-center grey"><b><?php echo "$".number_format($anticipo,2,".",",");?></b></td>
                            <td class="text-right blue"><b><?php echo "$".number_format($saldo - $anticipo,2,".",",");?></b></td>
                        </tr>

                        <tbody>
        <?php
                        $total = 0;
                        $fechaModificar                         = new DateTime($fechaInicio);
                        $saldo -= $anticipo;
                        while ($saldo > 0)
                        {
                            $pagoNo++;
                            $saldo -= $aportacion;

        ?>
                                <tr>
                                    <td class="text-right grey"><?php echo $pagoNo;?></td>
                                    <td class="text-center green"><b><?php echo date_format($fechaModificar, 'd-m-Y');?></b></td>
                                    <td class="text-center grey"><b><?php echo "$".number_format($aportacion,2,".",",");?></b></td>
                                    <td class="text-right blue"><b><?php echo "$".number_format($saldo,2,".",",");?></b></td>
                                </tr>
        <?php
                            if ($saldo < $aportacion && $saldo > 0)
                                $aportacion = $saldo;
                            if ($saldo == 0)
                                break;
                            switch ($frecuencia)
                            {
                                case 1:
                                $fechaModificar->modify('+1 week');
                                    break;
                                case 2:
                                $fechaModificar->modify('+2 week');
                                    break;
                                case 3:
                                $fechaModificar->modify('+1 month');
                                    break;

                                default:
                                $fechaModificar->modify('+1 week');
                                    break;
                            }
                        }
        ?>
                        </tbody>
                    </table>
                    <div class="hr hr8 hr-double"></div>
                    <div class="clearfix">
                        <div class="grid3">
                            <span class="grey">
								<i class="ace-icon fa fa-ticket fa-2x purple"></i>
								&nbsp; Total pagos
							</span>
                            <br>
                            <h3 class="bigger pull-right grey"><?php echo $pagoNo;?></h3>
						</div>
						<div class="grid3">
							<span class="grey">
								<i class="ace-icon fa fa-calendar fa-2x purple"></i>
								&nbsp; Fecha de término
							</span>
                            <br>
                            <h3 class="bigger pull-right green"><?php echo date_format($fechaModificar, 'd-m-Y');?></h3>
						</div>

						<div class="grid3">
							<span class="grey">
								<i class="ace-icon fa fa-money fa-2x purple"></i>
								&nbsp; Total pagado
							</span>
                            <br>
                            <h3 class="bigger pull-right blue"><?php echo "$".$precio;?></h3>
						</div>
					</div>
                    <div class="hr hr8 hr-double"></div>
        <?php
    }
?>
