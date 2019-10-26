<?php
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
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
        require "../php/contrato.class.php";
        require_once "../php/funcionesVarias.php";
        $response = array(
            "status"        => 1
        );

        $sql = "SELECT
                    contratos.id                AS idContrato
                FROM contratos
                WHERE contratos.enCurso = 1 AND contratos.activo = 1";
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
                $idContrato = $row['idContrato'];
                //$fechaCreacion = date_format(date_create($row['fechaCreacion']), 'd-m-Y');
                $contrato = new contrato($idContrato,$mysqli);
                $nombreDifunto = $contrato->nombreDifunto($mysqli);
                $htmlBtns = '<div class="action-buttons">';
                if ($contrato->idFactura != 0)
                {
                    $htmlBtns.=             '<a class="green pointer fPdf" id="'.$contrato->idFactura.'" target="_blank" href="assets/pdf/facturaPDF.php?idFactura='.$contrato->idFactura.'" data-rel="tooltip" title="PDF factura">
                                                <i class="ace-icon fa fa-file-pdf-o bigger-130"></i>
                                            </a>
                                            <a class="green pointer aXml" id="" target="_blank" href="assets/ws/descargarXML.php?xml='.$contrato->idFactura.'" data-rel="tooltip" title="Descargar XML">
                                                <i class="ace-icon fa fa-file-code-o bigger-130"></i>
                                            </a>';
                }
                else
                {
                    if (!$contrato->enCurso)
                    {
                        $htmlBtns.=         '<a class="pink pointer crearFactura" id="'.$contrato->idFactura.'" href="agregarFactura_contrato.php?idVenta='.$contrato->idFactura.'" data-rel="tooltip" title="Agegar factura">
                                                <i class="ace-icon fa fa-bolt bigger-130"></i>
                                            </a>';
                    }
                }
                $htmlBtns.=                 '<a href="assets/pdf/contrato.php?idContrato='.$idContrato.'" target="_blank" class="blue pointer aPdf" name="'.$idContrato.'" data-rel="tooltip" title="Imprimir contrato">
                                                <i class="ace-icon fa fa-file-pdf-o bigger-130"></i>
                                            </a>
                                            <a class="orange pointer aEstadoCuenta" target="_blank" id="'.$idContrato.'" href="assets/pdf/estadoCuenta.php?idContrato='.$idContrato.'" data-rel="tooltip" title="Generar estado de cuenta">
                                                <i class="ace-icon fa fa-file-text-o bigger-130"></i>
                                            </a>
                                            <a class="green2 pointer aModalPago" name="'.$idContrato.'" data-toggle="modal" data-rel="tooltip" title="Agregar pago">
                                                <i class="ace-icon fa fa-money bigger-130"></i>
                                            </a>
                                            <a class="green pointer aAsignarDifunto" name="'.$idContrato.'" data-rel="tooltip" title="Asignar difunto">
                                                <i class="ace-icon fa fa-user-circle bigger-130"></i>
                                            </a>';
                if ($contrato->motivoCancelado)
                {
                    $htmlBtns.=             '<a class="grey pointer aReactivar" name="'.$idContrato.'" data-rel="tooltip" title="Reactivar contrato">
                                                <i class="ace-icon fa fa-level-up bigger-130"></i>
                                            </a>';
                }
                else {
                    $htmlBtns.=             '<a class="red pointer aCancelar" name="'.$idContrato.'" data-rel="tooltip" title="Cancelar contrato">
                                                <i class="ace-icon fa fa-ban bigger-130"></i>
                                            </a>';
                }
                $htmlBtns.=                 '<a class="purple pointer aEdit" id="'.$idContrato.'" href="agregarContrato.php?idContrato='.$idContrato.'" data-rel="tooltip" title="Editar contrato">
                                                <i class="ace-icon fa fa-pencil-square-o bigger-130"></i>
                                            </a>
                                        </div>';
                $InfoData[] = array(
                    'id'                =>str_pad($contrato->id, 9, "0", STR_PAD_LEFT),
                    'folio'             => $contrato->folio,
                    'fechaCreacion'     => $contrato->fechaCreacion(),
                    'precio'            => "$".number_format($contrato->costoTotal,2,".",","),
                    'precioAportacion'  => "$".number_format($contrato->aportacion,2,".",","),
                    'abonado'           => "$".number_format($contrato->totalAbonado($mysqli),2,".",","),
                    'resta'             => "$".number_format($contrato->saldo($mysqli),2,".",","),
                    'nombreDifunto'     => strlen($nombreDifunto) > 0 ? $nombreDifunto : '<span class="label label-white middle">No asignado</span>',
                    'nombresTitular'    => $contrato->nombreCliente,
                    'frecuenciaPago'    => $contrato->frecuenciaPago(TRUE),
                    'estatus_cobranza'  => $contrato->estatus_cobranza($mysqli),
                    'btns'              => $htmlBtns);
            }
        //$data[] = $InfoData;
            $json_data = [
                "data"   => $InfoData
            ];
        }
        echo json_encode($json_data);
    }
?>
