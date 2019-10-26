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
        $permiso = $usuario->permiso("listarProveedores",$mysqli);
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
            $response['mensaje'] = "El formato del id del proveedor no es el correcto";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $sql = "SELECT cat_proveedores.*,
                    cat_estados.estado
                FROM cat_proveedores
                INNER JOIN cat_estados
                ON cat_proveedores.idEstado = cat_estados.id
                WHERE cat_proveedores.id = ? AND cat_proveedores.activo = 1 LIMIT 1";
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
            $response['mensaje'] = "No existe informaci&oacute;n para este proveedor. Posiblemente ya ha sido eliminado.";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $row                            = $res->fetch_assoc();
        $id                             = $row['id'];
        $rsocial                        = $row['rsocial'];
        $representante                  = $row['representante'];
        $telefono                       = $row['telefono'];
        $celular                        = $row['celular'];
        $estado                         = $row['estado'];
        $idEstado                         = $row['idEstado'];
        $domicilio1                     = $row['domicilio1'];
        $domicilio2                     = $row['domicilio2'];
        $cp                             = $row['cp'];
        $rfc                            = $row['rfc'];
        $email                          = $row['email'];
        $response['id']                 = $id;
        $response['rsocial']             = $rsocial;
        $response['representante']          = $representante;
        $response['telefono']           = $telefono;
        $response['celular']            = $celular;
        $response['estado']          = $estado;
        $response['idEstado']          = $idEstado;
        $response['domicilio1']         = $domicilio1;
        $response['domicilio2']         = $domicilio2;
        $response['cp']                 = $cp;
        $response['rfc']                = $rfc;
        $response['email']           = $email;
        $response['status']             = 1;
        $prepare                        ->close();
        responder($response, $mysqli);
    }
?>
