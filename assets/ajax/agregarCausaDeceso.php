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
        $permiso = $usuario->permiso("agregarDifunto",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo completar la información. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $nombre                        = $_POST['nombre'];
        $response = array(
            "status"                    => 1
        );
		$idUsuario      = $sesion->get('id');
		$sql            = "SELECT idSucursal FROM cat_usuarios WHERE id = $idUsuario LIMIT 1";
		$res_noSucursal = $mysqli->query($sql);
		$row_noSucursal = $res_noSucursal->fetch_assoc();
		$idSucursal     = $row_noSucursal['idSucursal'];

        if (!$nombre = validarFormulario('s',$nombre,0))
        {
            $response['mensaje'] = "El campo Nombre no cumple con el formato esperado y no puede estar en blanco";
            $response['status'] = 0;
            $response['focus'] = 'inputNuevaCausaDeceso';
            responder($response, $mysqli);
        }
        $sql = "SELECT nombre FROM cat_causasdecesos WHERE nombre = ? AND activo = 1";
        if ($prepare_select = $mysqli->prepare($sql))
        {
            if (!$prepare_select->bind_param('s',$nombre))
            {
                $response['mensaje'] = "No se puede consultar la información. Error al enlazar parámetros";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if (!$prepare_select->execute())
            {
                $response['mensaje'] = "No se puede consultar la información. Error al ejecutar los parámetros";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            $res_select = $prepare_select->get_result();
            if ($res_select->num_rows > 0)
            {
                $response['mensaje'] = "No se puede agregar esta causa de deceso. Ya existe registrada una con el mismo nombre. Elije otra distinta";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            else
            {
                $sql = "INSERT INTO cat_causasdecesos (nombre, usuario) VALUES (?,?)";
                if ($prepare_insert = $mysqli->prepare($sql))
                {
                    if (!$prepare_insert->bind_param("si",$nombre,$idUsuario))
                    {
                        $response['mensaje'] = "No se pudo guardar la información. Error al enlazar parámetros";
                        $response['status'] = 0;
                        responder($response, $mysqli);
                    }
                    if (!$prepare_insert->execute())
                    {
                        $response['mensaje'] = "No se pudo guardar la información. Error al ejecutar parámetros";
                        $response['status'] = 0;
                        responder($response, $mysqli);
                    }
                    else
                    {
						// Agregar evento en la bitácora de eventos
						$idUsuario 				= $sesion->get("id");
						$ipUsuario 				= $sesion->get("ip");
						$pantalla				= "Agregar/Modificar difunto";
						$descripcion			= "Se agregó una nueva causa de deceso ($nombre) al catálogo de causas de decesos";
						$sql					= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
						$mysqli					->query($sql);
                        $response['mensaje'] 	= "Nueva causa de deceso generada correctamente.";
                        $response['status'] 	= 1;
                        responder($response, $mysqli);
                    }
                }
                else
                {
                    $response['mensaje'] = "No se puede guardar la información. Error al preparar los parámetros";
                    $response['status']         = 0;
                    responder($response, $mysqli);
                }
            }
        }
        else
        {
            $response['mensaje'] = "No se puede consultar la información. Error al preparar los parámetros";
            $response['status']         = 0;
            responder($response, $mysqli);
        }
    }
?>