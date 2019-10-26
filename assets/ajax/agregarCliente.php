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
        require "../php/responderJSON.php";
        require ("../php/usuario.class.php");
        $usuario = new usuario($idUsuario,$mysqli);
        $permiso = $usuario->permiso("agregarCliente",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo guardar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }

        $nombres                        = $_POST['nombres'];
        $apellidop                      = $_POST['apellidop'];
        $apellidom                      = $_POST['apellidom'];
        $domicilio1                     = $_POST['domicilio1'];
        $domicilio2                     = $_POST['domicilio2'];
        $cp                             = $_POST['cp'];
        $idEstado                       = $_POST['estado'];
        $rfc                            = $_POST['rfc'];
        $fechaNac                       = $_POST['fechaNac'];
        $telefono                       = $_POST['telefono'];
        $celular                        = $_POST['celular'];
        $email                          = $_POST['email'];
        $response = array(
            "status"                    => 1
        );

        if (!$nombres = validarFormulario('s',$nombres,0))
        {
            $response['mensaje'] = "El campo Nombre no cumple con el formato esperado y no puede estar en blanco";
            $response['status'] = 0;
            $response['focus'] = 'nombres';
            responder($response, $mysqli);
        }
        if (!$apellidop = validarFormulario('s',$apellidop,0))
        {
            $response['mensaje'] = "El campo Apellido paterno no puede estar en blanco";
            $response['status'] = 0;
            $response['focus'] = 'apellidop';
            responder($response, $mysqli);
        }
        $apellidom = validarFormulario('s', $apellidom, FALSE);
        if (!$domicilio1 = validarFormulario('s',$domicilio1,0))
        {
            $response['mensaje'] = "El campo domicilio no puede estar en blanco";
            $response['status'] = 0;
            $response['focus'] = 'domicilio1';
            responder($response, $mysqli);
        }
        if (!$domicilio2 = validarFormulario('s',$domicilio2,0))
        {
            $response['mensaje'] = "El campo domicilio no puede estar en blanco";
            $response['status'] = 0;
            $response['focus'] = 'domicilio2';
            responder($response, $mysqli);
        }
        if (!$cp = validarFormulario('s',$cp,0))
        {
            $response['mensaje'] = "El campo código postal no puede estar en blanco";
            $response['status'] = 0;
            $response['focus'] = 'cp';
            responder($response, $mysqli);
        }
        if (!$idEstado = validarFormulario('i',$idEstado))
        {
            $response['mensaje'] = "El formato del campo estado no es el correcto";
            $response['status'] = 0;
            $response['focus'] = 'estado';
            responder($response, $mysqli);
        }
        $rfc = validarFormulario('s',$rfc);
        if (!$fechaNac = validarFormulario('d',$fechaNac))
        {
            $response['mensaje'] = "Elige una fecha válida. El formato de la fecha no es el correcto.";
            $response['status'] = 0;
            $response['focus'] = 'fechaNac';
            responder($response, $mysqli);
        }
        $telefono = validarFormulario('s',$telefono, FALSE);
        $celular = validarFormulario('s',$celular, FALSE);
        $email = validarFormulario('s', $email, FALSE);

        $idUsuario      = $sesion->get('id');
        $sql            = "SELECT idSucursal FROM cat_usuarios WHERE id = $idUsuario LIMIT 1";
        $res_noSucursal = $mysqli->query($sql);
        $row_noSucursal = $res_noSucursal->fetch_assoc();
        $idSucursal     = $row_noSucursal['idSucursal'];
        if (strlen($rfc) > 0)
        {    
            $sql = "SELECT id FROM clientes WHERE rfc = '$rfc' AND activo = 1 AND idSucursal = $idSucursal";
            $res_rfc = $mysqli->query($sql);
            if ($res_rfc->num_rows > 0)
            {
                $response['mensaje'] = "No se puede guardar este nuevo registro porque ya existe un cliente en esta sucursal con el mismo RFC";
                $response['status'] = 0;
                $response['focus'] = 'rfc';
                responder($response, $mysqli);
            }
        }
        $sql            = "INSERT INTO clientes
                                (nombres, apellidop, apellidom, domicilio1, domicilio2, cp, idEstado, rfc, fechaNac, tel, cel, email, idSucursal, usuario)
                            VALUES
                                (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        if($prepare     = $mysqli->prepare($sql))
        {
            if(!$prepare->bind_param('ssssssisssssii', $nombres, $apellidop, $apellidom, $domicilio1, $domicilio2, $cp, $idEstado, $rfc, $fechaNac, $telefono, $celular, $email, $idSucursal, $idUsuario))
            {
                $response['mensaje'] = "Error. No se pudo guardar la información. Falló el la vinculación de parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if(!$prepare->execute())
            {
                $response['mensaje'] = "Error. No se pudo guardar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if($prepare->affected_rows == 0)
            {
                $response['mensaje']        = "No se modificó nada, no se pudo guardar el registro, inténtalo nuevamente";
                $response['status']         = 0;
                responder($response, $mysqli);
            }
            $insert_id                  = $mysqli->insert_id;
            $response['mensaje']        = "$nombres $apellidop $apellidom";
            $response['status']         = 1;
            responder($response, $mysqli);
        }
        else
        {
            $response['mensaje']        = "Error. No se pudo modificar. Falló la preparación de los datos. Vuelve a intentarlo nuevamente";
            $response['status']         = 0;
            responder($response, $mysqli);
        }
    }
?>
