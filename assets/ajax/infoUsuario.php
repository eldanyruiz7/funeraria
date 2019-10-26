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
        require ("../php/usuario.class.php");
        $usuario = new usuario($idUsuario,$mysqli);
        $permiso = $usuario->permiso("listarUsuarios",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo mostrar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }

        $idCliente = $_POST['idCliente'];

        $response = array(
            "status"        => 1
        );
        if(is_numeric($idCliente) == FALSE)
        {
            $response['mensaje'] = "El formato del id del usuario no es el correcto";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $sql = "SELECT
                    cat_usuarios.direccion1     AS direccion1,
                    cat_usuarios.direccion2     AS direccion2,
                    cat_usuarios.estado         AS idEstado,
                    cat_usuarios.telefono       AS telefono,
                    cat_usuarios.celular        AS celular,
                    cat_usuarios.email          AS email,
                    cat_sucursales.nombre       AS nombreSucursal,
                    cat_sucursales.direccion2   AS direccionSucursal,
                    cat_estados.estado          AS nombreEstado
                FROM cat_usuarios
                INNER JOIN cat_sucursales
                ON cat_usuarios.idSucursal = cat_sucursales.id
                INNER JOIN cat_estados
                ON cat_usuarios.estado = cat_estados.id
                WHERE cat_usuarios.id = ? AND cat_usuarios.activo = 1 LIMIT 1";
        if($prepare         = $mysqli->prepare($sql))
        {
            $prepare        ->bind_param('i',$idCliente);
            $prepare        ->execute();
            $res            = $prepare->get_result();
        }
        else
        {
            $response['mensaje'] = "Ocurrió un error en la consulta a la Base de datos.".$mysqli->errno . " " . $mysqli->error;
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        if($res->num_rows == 0)
        {
            $response['mensaje'] = "No existe informaci&oacute;n para este usuario. Posiblemente ya ha sido eliminado.";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $row                            = $res->fetch_assoc();
        $domicilio1                     = $row['direccion1'];
        $domicilio2                     = $row['direccion2'];
        $nombreEstado                   = $row['nombreEstado'];
        $telefono                       = $row['telefono'];
        $celular                        = $row['celular'];
        $email                          = $row['email'];
        $nombreSucursal                 = $row['nombreSucursal'];
        $direccionSucursal              = $row['direccionSucursal'];
        $response['domicilio']         = $domicilio1.", ".$domicilio2.", ".$nombreEstado;
        $response['telefono']           = $telefono;
        $response['celular']            = $celular;
        $response['email']              = $email;
        $response['sucursal']           = $nombreSucursal." ".$direccionSucursal;
        $response['status']             = 1;
        $sql                            = "SELECT
                                                folios_cobranza_asignados.folio         AS folio,
                                                folios_cobranza_asignados.fechaCreacion AS fechaCreacion,
                                                cat_usuarios.nombres                    AS nombreUsuario,
                                                cat_usuarios.apellidop                  AS apellidopUsuario,
                                                cat_usuarios.apellidom                  AS apellidomUsuario
                                            FROM folios_cobranza_asignados
                                            INNER JOIN cat_usuarios
                                            ON folios_cobranza_asignados.idUsuario = cat_usuarios.id
                                            WHERE folios_cobranza_asignados.idUsuario_asignado = $idCliente
                                            AND folios_cobranza_asignados.activo = 1
                                            AND folios_cobranza_asignados.asignado = 0";
        $res_det                        = $mysqli->query($sql);
        $response['htmlDetalle_hist']   = "";
        $cont = 0;
        while ($row_det = $res_det->fetch_assoc())
        {
            $cont++;
            $folio = $row_det['folio'];
            $fechaCreacion = date_create($row_det['fechaCreacion']);
            $fechaCreacion = date_format($fechaCreacion, "d-m-Y H:i:s");
            $nombresUsuario = $row_det['nombreUsuario']." ".$row_det['apellidopUsuario']." ".$row_det['apellidomUsuario'];
            $response['htmlDetalle_hist'].= "<tr>
                                                <td>$cont.-</td>
                                                <td class='text-right'>$folio</td>
                                                <td class='text-center'>$fechaCreacion</td>
                                                <td>$nombresUsuario</td>
                                            </tr>";
        }
        responder($response, $mysqli);
    }
?>
