<?php
    require_once ('../connect/bd.php');
    require_once ("../connect/sesion.class.php");
    $sesion = new sesion();
    require_once ("../connect/cerrarOtrasSesiones.php");
    require_once ("../connect/usuarioLogeado.php");
    if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
    {
        header("Location: salir.php");
    }
    else
    {
        require "../php/responderJSON.php";
        $response = array(
            "status"        => 1
        );

        $sql = "SELECT
                    clientes.id                 AS idCliente,
                    clientes.idVenta            AS idVenta,
                    clientes.idContrato         AS idContrato,
                    clientes.nombres            AS nombresCliente,
                    clientes.apellidop          AS apellidopCliente,
                    clientes.apellidom          AS apellidomCliente,
                    clientes.domicilio1         AS domicilio1,
                    clientes.domicilio2         AS domicilio2,
                    clientes.cp                 AS cpCliente,
                    clientes.rfc                AS rfcCliente,
                    clientes.fechaNac           AS fechaNacCliente,
                    clientes.fechaRegistro      AS fechaRegistro,
                    clientes.idSucursal         AS idSucursal,
                    clientes.idEstado           AS idEstado,
                    cat_estados.estado          AS nombreEstado
                FROM clientes
                INNER JOIN cat_estados
                ON clientes.idEstado = cat_estados.id
                WHERE clientes.activo = 1";

        $res_ = $mysqli->query($sql);
        $num = $res_->num_rows;
        if ($num == 0)
        {
            $json_data = [
                "data"   => 0
            ];
        }
        else
        {
            while ($row = $res_->fetch_assoc())
            {
                $fechaNac = date_create($row['fechaNacCliente']);
                $fechaNac = date_format($fechaNac, 'd/m/Y');
                $fechaReg = date_create($row['fechaRegistro']);
                $fechaReg = date_format($fechaReg, 'd/m/Y H:i:s');
                if($row['idContrato'] == 0)
                {
                    $btns = '<div class="action-buttons">
                                <a href="agregarContrato.php?idCliente='.$row['idCliente'].'" class="blue pointer aVenta" id="'.$row['idCliente'].'" data-rel="tooltip" title="Agregar una venta">
                                    <i class="ace-icon fa fa-file-text-o bigger-130"></i>
                                </a>
                                <a href="agregarVenta.php?idCliente='.$row['idCliente'].'" class="green pointer aVenta" id="'.$row['idCliente'].'" data-rel="tooltip" title="Agregar una venta">
                                    <i class="ace-icon fa fa-shopping-cart bigger-130"></i>
                                </a>
                                <a class="purple pointer aEdit" id="'.$row['idCliente'].'" data-rel="tooltip" title="Editar">
								    <i class="ace-icon fa fa-pencil-square-o bigger-130"></i>
								</a>
								<a class="btnEliminar pointer red" idCliente='.$row['idCliente'].' data-rel="tooltip" title="Eliminar">
									<i class="ace-icon fa fa-trash-o bigger-130"></i>
							    </a>
							</div>';
                }
                else
                {
                    $btns = '<div class="action-buttons">
                                <a href="agregarVenta.php?idCliente='.$row['idCliente'].'" class="green pointer aVenta" id="'.$row['idCliente'].'" data-rel="tooltip" title="Agregar una venta">
                                    <i class="ace-icon fa fa-shopping-cart bigger-130"></i>
                                </a>
                                <a class="purple pointer aEdit" id="'.$row['idCliente'].'" data-rel="tooltip" title="Editar">
								    <i class="ace-icon fa fa-pencil-square-o bigger-130"></i>
								</a>
								<a class="btnEliminar pointer red" idCliente='.$row['idCliente'].' data-rel="tooltip" title="Eliminar">
									<i class="ace-icon fa fa-trash-o bigger-130"></i>
							    </a>
							</div>';
                }

                $InfoData[] = array(
                    'id'                => $row['idCliente'],
                    'nombresCliente'    => $row['nombresCliente']." ".$row['apellidopCliente']." ".$row['apellidomCliente'],
                    'rfcCliente'        => $row['rfcCliente'],
                    'domicilio'         => $row['domicilio1']." ".$row['domicilio2']." ".$row['nombreEstado'].". CP: ".$row["cpCliente"],
                    'fechaNacCliente'   => $fechaNac,
                    'fechaRegCliente'   => $fechaReg,
                    'sucursal'          => $row['idSucursal'],
                    'btnAgregar'        => '<div class="action-buttons">
                                                <a class="blue pointer aAgregarCliente"
                                                    id="'.$row['idCliente'].'" nombre-cliente="'.$row['nombresCliente'].' '.$row['apellidopCliente'].' '.$row['apellidomCliente'].'"
                                                    domicilio1="'.$row['domicilio1'].'"
                                                    domicilio2="'.$row['domicilio2'].'"
                                                    id-estado="'.$row['idEstado'].'"
                                                    cp="'.$row["cpCliente"].'" data-rel="tooltip" title="Editar">
                								    <i class="ace-icon fa fa-2x fa-share-square-o bigger-130"></i>
                								</a>
                							</div>',
                    'btns'              => $btns);
            }
            //$data[] = $InfoData;
            $json_data = [
                "data"   => $InfoData
            ];
        }
        echo json_encode($json_data);
    }
?>
