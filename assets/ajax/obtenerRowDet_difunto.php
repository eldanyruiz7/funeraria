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
        require "../php/responderJSON.php";
        require ("../php/usuario.class.php");
        $usuario = new usuario($idUsuario,$mysqli);
        $permiso = $usuario->permiso("listarDifuntos",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo mostrar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $response = array(
            "status"        => 1
        );
        $idDifunto = $_POST['idCliente'];
        $sql = "SELECT
                    cat_difuntos.id                             AS idDifunto,
                    cat_difuntos.domicilio1_part                AS domicilio1_part,
                    cat_difuntos.domicilio2_part                AS domicilio2_part,
                    cat_difuntos.cp_part                        AS cp_part,
                    cat_difuntos.idEstado_part                  AS idEstado_part,
                    cat_difuntos.fechaNac                       AS fechaNac,
                    cat_difuntos.idLugarDefuncion               AS idLugarDefuncion,
                    cat_difuntos.nombreLugarDefuncion           AS nombreLugarDefuncion,
                    cat_difuntos.domicilioLugarDefuncion        AS domicilioLugarDefuncion,
                    cat_difuntos.domicilioParticularDefuncion   AS domicilioParticularDefuncion,
                    cat_difuntos.noCertificadoDefuncion         AS noCertificadoDefuncion,
                    cat_difuntos.noActaDefuncion                AS noActaDefuncion,
                    cat_difuntos.fechaRegistro                  AS fechaRegistro,
                    cat_estados.estado                          AS estado_part,
                    cat_usuarios.nombres                        AS nombreUsuario,
                    cat_usuarios.apellidop                      AS apellidopUsuario
                FROM cat_difuntos
                INNER JOIN cat_estados
                ON cat_difuntos.idEstado_part = cat_estados.id
                INNER JOIN cat_usuarios
                ON cat_difuntos.usuario = cat_usuarios.id
                WHERE cat_difuntos.id = ? AND cat_difuntos.activo = 1 LIMIT 1";
        if ($prepare = $mysqli->prepare($sql))
        {
            if (!$prepare->bind_param('i',$idDifunto))
            {
                $response['mensaje'] = "Error. No se pudo consultar la información. Falló el la vinculación de parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if (!$prepare->execute())
            {
                $response['mensaje'] = "Error. No se pudo consultar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            $res                    = $prepare->get_result();
            if ($res->num_rows == 0)
            {
                $response['idCompra'] = $idCompra;
                $response['status'] = 0;
                $response['mensaje'] = "No existe información para este registro. Posiblemente ya haya sido eliminada del sistema";
                responder($response, $mysqli);
            }
            else
            {
                // $response['htmlDetalle']        = "";
                // $response['htmlDetalle_hist']   = "";

                $row                      = $res->fetch_assoc();
                // $activo                     = ($row['activo']) ? TRUE : FALSE;
                $fechaNac                   = date_create($row['fechaNac']);
                $fechaNac                   = date_format($fechaNac,"Y-m-d");
                $domicilioPart              = $row['domicilio1_part']." ".$row['domicilio2_part'].", CP: ".$row['cp_part'].", ".$row['estado_part'];
                if ($row['idLugarDefuncion'] == 0)
                {
                    $lugarDefuncion = $row['domicilioParticularDefuncion'];
                }
                else
                {
                    $idLugarDef = $row['idLugarDefuncion'];
                    $sql = "SELECT nombre, domicilio FROM cat_lugares_defuncion WHERE id = $idLugarDef LIMIT 1";
                    $res_lugar = $mysqli->query($sql);
                    $row_lugar = $res_lugar->fetch_assoc();
                    $lugarDefuncion = $row_lugar['nombre'];
                }
                $noCertificado              = $row['noCertificadoDefuncion'];
                $noActa                     = $row['noActaDefuncion'];
                $fechaReg                   = date_create($row['fechaRegistro']);
                $fechaReg                   = date_format($fechaReg,"d-m-Y");
                $usuario                    = $row['nombreUsuario']." ".$row['apellidopUsuario'];
                // if ($activo)
                // {
                //     $response['htmlDetalle']    .= "<tr>
                //                                         <td>$idProducto</td>
                //                                         <td>$nombreProducto</td>
                //                                         <td class='text-right'>$$precioCompra</td>
                //                                         <td class='text-right'>$cantidad</td>
                //                                         <td class='text-right'>$$subTotal</td>
                //                                     </tr>";
                // }
                // else
                // {
                //     $response['htmlDetalle_hist'] .= "<tr>
                //                                         <td>$idProducto</td>
                //                                         <td>$nombreProducto</td>
                //                                         <td class='text-right'>$$precioCompra</td>
                //                                         <td class='text-right'>$cantidad</td>
                //                                         <td class='text-right'>$$subTotal</td>
                //                                         <td class='text-center'>$fechaReg</td>
                //                                         <td>$usuario</td>
                //                                     </tr>";
                // }

                $response['fechaNac']           = $fechaNac;
                $response['domicilioPart']      = $domicilioPart;
                $response['lugarDefuncion']     = $lugarDefuncion;
                $response['noCertificado']      = $noCertificado;
                $response['noActa']             = $noActa;
                $response['fechaReg']           = $fechaReg;
                $response['usuario']            = $usuario;
                $response['idDifunto']            = $row['idDifunto'];
                $response['galeria']            = '<a class="blue pointer aGaleria" id-difunto="'.$row['idDifunto'].'" directorio="assets/images/avatars/difuntos/'.$row['idDifunto'].'" data-rel="tooltip" title="Mostrar galería">
                                                    <i class="ace-icon fa fa-image bigger-130"></i>
                                                </a>';
                $response['status']             = 1;
                responder($response, $mysqli);
            }
        }
        else
        {
            $response['mensaje'] = "Error. No se pudo consultar la información. Error en la consulta. Inténtalo nuevamente".$mysqli->errno . " " . $mysqli->error;
            $response['status'] = 0;
            responder($response, $mysqli);
        }
    }
?>
