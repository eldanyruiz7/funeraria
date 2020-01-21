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
        $permiso = $usuario->permiso("listarClientes",$mysqli);
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
            $response['mensaje'] = "El formato del id del cliente no es el correcto";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $sql = "SELECT clientes.*,
                    cat_sucursales.nombre       AS nombreSucursal,
                    cat_sucursales.direccion2   AS direccionSucursal
                FROM clientes
                INNER JOIN cat_sucursales
                ON clientes.idSucursal = cat_sucursales.id
                WHERE clientes.id = ? AND clientes.activo = 1 LIMIT 1";
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
            $response['mensaje'] = "No existe informaci&oacute;n para este cliente. Posiblemente ya ha sido eliminado.";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $row                            = $res->fetch_assoc();
        $id                             = $row['id'];
        $nombre                         = $row['nombres'];
        $apellidop                      = $row['apellidop'];
        $apellidom                      = $row['apellidom'];
        $domicilio1                     = $row['domicilio1'];
        $domicilio2                     = $row['domicilio2'];
        $cp                             = $row['cp'];
        $idEstado                       = $row['idEstado'];
        $rfc                            = $row['rfc'];
        $fechaNac                       = $row['fechaNac'];
        $telefono                       = $row['tel'];
        $celular                        = $row['cel'];
        $email                          = $row['email'];
        $nombreSucursal                 = $row['nombreSucursal'];
        $direccionSucursal              = $row['direccionSucursal'];
        $response['id']                 = $id;
        $response['nombre']             = $nombre;
        $response['apellidop']          = $apellidop;
        $response['apellidom']          = $apellidom;
        $response['domicilio1']         = $domicilio1;
        $response['domicilio2']         = $domicilio2;
        $response['cp']                 = $cp;
        $response['idEstado']           = $idEstado;
        $response['rfc']                = $rfc;
        $response['fechaNac']           = $fechaNac;
        $response['telefono']           = $telefono;
        $response['celular']            = $celular;
        $response['email']              = $email;
        $response['nombreSucursal']     = $nombreSucursal." ".$direccionSucursal;
        $response['status']             = 1;
        $prepare                        ->close();
        responder($response, $mysqli);
    }
?>
