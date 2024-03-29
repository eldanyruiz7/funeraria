<?php
    require_once ('../connect/bd.php');
    require_once ("../connect/sesion.class.php");
    $sesion = new sesion();
    require_once ("../connect/cerrarOtrasSesiones.php");
    require_once ("../connect/usuarioLogeado.php");
    require_once ("../php/funcionesVarias.php");
    if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
    {
		header("Location: ".dirname(__FILE__)."../../salir.php");
    }
    else
    {
        require "../php/responderJSON.php";
        require ("../php/usuario.class.php");
        $usuario = new usuario($idUsuario,$mysqli);
        $permiso = $usuario->permiso("modificarDifunto",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] = "No se pudo modificar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        function genRand($qtd)
        {
            //Under the string $Caracteres you write all the characters you want to be used to randomly generate the code.
            $Caracteres = 'ABCDEFGHIJKLMOPQRSTUVXWYZabcdefghijklmnopqrstuvwxyz0123456789';
            $QuantidadeCaracteres = strlen($Caracteres);
            $QuantidadeCaracteres--;

            $Hash=NULL;
            for($x=1;$x<=$qtd;$x++){
                $Posicao = rand(0,$QuantidadeCaracteres);
                $Hash .= substr($Caracteres,$Posicao,1);
            }

            return $Hash;
        }
        function borrarDirectorio($dir)
        {
            if(!$dh = @opendir($dir)) return;
            while (false !== ($current = readdir($dh)))
            {
                if($current != '.' && $current != '..')
                {
                    if (!@unlink($dir.'/'.$current))
                        deleteDirectory($dir.'/'.$current);
                }
            }
            closedir($dh);
            @rmdir($dir);
        }
        // var_dump($arrayImagenes);
        // die;
        $targetPath                     = "0";
        $idDifunto                      = $_POST['idDifunto'];
        $nombres                        = $_POST['nombres'];
        $apellidop                      = $_POST['apellidop'];
        $apellidom                      = $_POST['apellidom'];
        $domicilio1                     = $_POST['domicilio1'];
        $domicilio2                     = $_POST['domicilio2'];
        $cp                             = $_POST['cp'];
        $idEstado                       = $_POST['estado'];
        $rfc                            = $_POST['rfc'];
        $fechaNac                       = $_POST['fechaNac'];
        $fechaDef                       = $_POST['fechaDef'];
        $domPartDef                     = $_POST['domicilioParticularDefuncion'];
        $idLugarDefuncion               = $_POST['idLugarDefuncion'];
        $nombreLugarDef                 = $_POST['nombreLugarDefuncion'];
        $domicilioLugarDef              = $_POST['domicilioLugarDefuncion'];
        $noCausasDecesos                = isset($_POST['causasDecesos']) ? 1 : 0;
        $chkDomicilio                   = isset($_POST['chkDomicilio']) ? 1 : 0;
        $chkNuevoLugar                  = isset($_POST['checkNuevoLugar']) ? 1 : 0;
        $certificadoDef                 = $_POST['certificadoDefuncion'];
        $actaDef                        = $_POST['actaDefuncion'];
        $arrayImagenes                  = json_decode($_POST['arrayImagenes']);
        // echo $fechaDef;
        // die;
        // echo sizeof($arrayImagenes);
        // die;
        // $telefono                       = $_POST['telefono'];
        // $celular                        = $_POST['celular'];
        $response = array(
            "status"                    => 1,
            "warning"                   =>""
        );
        if (!$idDifunto = validarFormulario('i',$idDifunto))
        {
            $response['mensaje'] = "El formato del campo estado no es el correcto";
            $response['status'] = 0;
            $response['focus'] = 'estado';
            responder($response, $mysqli);
        }
        $sql = "SELECT id FROM cat_difuntos WHERE id = ? AND activo = 1 LIMIT 1";
        if ($prepare_activo = $mysqli->prepare($sql))
        {
            if (!$prepare_activo->bind_param("i",$idDifunto))
            {
                $response['mensaje'] = "Error. No se pudo consultar la información del id del registro. Falló el la vinculación de parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if (!$prepare_activo->execute())
            {
                $response['mensaje'] = "Error. No se pudo consultr la información del id del registro. Falló el la ejecución de parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
        }
        else
        {
            $response['mensaje'] = "Error. No se pudo consultr la información del id del registro. Falló el la preparción de parámetros. Inténtalo nuevamente";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
        $res_activo = $prepare_activo->get_result();
        if ($res_activo->num_rows == 0)
        {
            $response['mensaje'] = "Error. No puede modificarse este registro porque ya ha sido cancelado o eliminado";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
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
        if (!$rfc = validarFormulario('s',$rfc,0))
        {
            $response['mensaje'] = "El campo RFC no puede estar en blanco";
            $response['status'] = 0;
            $response['focus'] = 'rfc';
            responder($response, $mysqli);
        }
        // var_dump($fff);
        if (!$fechaNac = validarFormulario('d',$fechaNac))
        {
            $response['mensaje'] = "Elige una fecha válida. El formato de la fecha de nacimiento no es el correcto.";
            $response['status'] = 0;
            $response['focus'] = 'fechaNac';
            responder($response, $mysqli);
        }
        if (strlen($fechaDef) == 0)
        {

            $response['mensaje'] = "Elige una fecha válida. El formato de la fecha de defunción no es el correcto.";
            $response['status'] = 0;
            $response['focus'] = 'fechaDef';
            responder($response, $mysqli);
        }
        else
        {
            $explodeFechaDef    = explode('T',$fechaDef);
            $fechaDef           = $explodeFechaDef[0];
            $hrDef              = $explodeFechaDef[1];
        }
        if (!$fechaDef = validarFormulario('d',$fechaDef))
        {
            $response['mensaje'] = "Elige una fecha válida. El formato de la fecha de defunción no es el correcto.";
            $response['status'] = 0;
            $response['focus'] = 'fechaDef';
            responder($response, $mysqli);
        }
        if ($chkDomicilio)
        {
            $nombreLugarDef = "";
            $domicilioLugarDef = "";
            $idLugarDefuncion = 0;
            if (!$domPartDef = validarFormulario('s',$domPartDef,0))
            {
                $response['mensaje'] = "El campo Domicilio particular de defunción no puede estar en blanco";
                $response['status'] = 0;
                $response['focus'] = 'domicilioParticularDefuncion';
                responder($response, $mysqli);
            }
        }
        else
        {
            $domPartDef = "";
            if (!$chkNuevoLugar)
            {
                if (!$idLugarDefuncion = validarFormulario('i',$idLugarDefuncion, 0))
                {
                    $response['mensaje'] = "El formato del Id lugar de defunción no es el correcto. Elige un lugar de defunción";
                    $response['status'] = 0;
                    $response['focus'] = 'idLugarDefuncion';
                    responder($response, $mysqli);
                }
            }
            else
            {
                if (!$nombreLugarDef = validarFormulario('s',$nombreLugarDef, 0))
                {
                    $response['mensaje'] = "El campo Nombre del lugar de defunción no puede estar en blanco";
                    $response['status'] = 0;
                    $response['focus'] = 'nombreLugarDefuncion';
                    responder($response, $mysqli);
                }
                if (!$domicilioLugarDef = validarFormulario('s',$domicilioLugarDef, 0))
                {
                    $response['mensaje'] = "El campo Domicilio del lugar de defunción no puede estar en blanco";
                    $response['status'] = 0;
                    $response['focus'] = 'domicilioLugarDefuncion';
                    responder($response, $mysqli);
                }
            }
        }
        if (!$noCausasDecesos)
        {
            $response['mensaje'] = "Debes elegir al menos una causa de ceceso";
            $response['status'] = 0;
            $response['focus'] = 'causasDecesos';
            responder($response, $mysqli);
        }
        $certificadoDef = validarFormulario('s',$certificadoDef,FALSE);
        $actaDef = validarFormulario('s',$actaDef,FALSE);

        $idUsuario      = $sesion->get('id');
        $sql            = "SELECT idSucursal FROM cat_usuarios WHERE id = $idUsuario LIMIT 1";
        $res_noSucursal = $mysqli->query($sql);
        $row_noSucursal = $res_noSucursal->fetch_assoc();
        $idSucursal     = $row_noSucursal['idSucursal'];

        $fechaDef_sql = $fechaDef." ".$hrDef;
        $mysqli->autocommit(FALSE);
        $sql            = "UPDATE cat_difuntos
                            SET nombres = ?, apellidop = ?, apellidom = ?, domicilio1_part = ?, domicilio2_part = ?,
                                cp_part = ?, idEstado_part = ?, rfc = ?, fechaNac = ?, fechaHrDefuncion = ?, idLugarDefuncion = ?,
                                nombreLugarDefuncion = ?, domicilioLugarDefuncion = ?, domicilioParticularDefuncion = ?,
                                noCertificadoDefuncion = ?, noActaDefuncion = ?, idSucursal = ?, usuario = ?
                            WHERE id = ? LIMIT 1";
        if($prepare     = $mysqli->prepare($sql))
        {
            if(!$prepare->bind_param('ssssssisssisssssiii', $nombres, $apellidop, $apellidom, $domicilio1, $domicilio2,
                                                            $cp, $idEstado, $rfc, $fechaNac, $fechaDef_sql, $idLugarDefuncion,
                                                            $nombreLugarDef, $domicilioLugarDef, $domPartDef,
                                                            $certificadoDef, $actaDef, $idSucursal, $idUsuario, $idDifunto))
            {
                $response['mensaje'] = "Error. No se pudo actualizar la información. Falló el la vinculación de parámetros. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if(!$prepare->execute())
            {
                $response['mensaje'] = "Error. No se pudo actualizar la información. Falló el enlace a la base de datos. Inténtalo nuevamente";
                $response['status'] = 0;
                responder($response, $mysqli);
            }
            if ($chkDomicilio == 0 && $chkNuevoLugar == 1)
            {
                $sql = "INSERT INTO cat_lugares_defuncion (nombre, domicilio,usuario)
                        VALUES (?,?,?)";
                if ($prepare_nuevo_lugar_def = $mysqli->prepare($sql))
                {
                     $prepare_nuevo_lugar_def ->bind_param("ssi", $nombreLugarDef, $domicilioLugarDef, $idUsuario );
                     if($prepare_nuevo_lugar_def ->execute())
                     {
                         $idNuevoLugar                  = $mysqli->insert_id;
                         $sql = "UPDATE cat_difuntos
                                 SET idLugarDefuncion = ?, nombreLugarDefuncion = ?, domicilioLugarDefuncion = ?
                                 WHERE id = ? LIMIT 1";
                        if($prepare_actual_lugar_def = $mysqli->prepare($sql))
                        {
                            $a = "";
                            $b = "";
                            if(!$prepare_actual_lugar_def->bind_param("issi",$idNuevoLugar, $a, $b, $insert_id))
                            {
                                $mysqli->rollback();
                                $response['mensaje']        = "No se modificó nada, Error al enlazar los parámetros al intentar actualizar el id lugar de defunción";
                                $response['status']         = 0;
                                responder($response, $mysqli);
                            }
                            if(!$prepare_actual_lugar_def->execute())
                            {
                                $mysqli->rollback();
                                $response['mensaje']        = "No se modificó nada, Error al ejecutar los parámetros al intentar actualizar el id lugar de defunción";
                                $response['status']         = 0;
                                responder($response, $mysqli);
                            }
                        }
                        else
                        {
                            $mysqli->rollback();
                            $response['mensaje']        = "No se modificó nada, Error en la preparación de parámetros al intentar actualizar el id lugar de defunción";
                            $response['status']         = 0;
                            $response['idNuevolugar']   = $idNuevoLugar;
                            $response['idInsert']       = $insert_id;
                            responder($response, $mysqli);
                        }
                     }
                     else
                     {
                         $mysqli->rollback();
                         $response['mensaje']        = "No se modificó nada, Error en ejecución de parámetros del nuevo lugar de defunción";
                         $response['status']         = 0;
                         responder($response, $mysqli);
                     }
                }
                else
                {
                    $mysqli->rollback();
                    $response['mensaje']        = "No se modificó nada, Error en preparación del nuevo lugar de defunción";
                    $response['status']         = 0;
                    responder($response, $mysqli);
                }
            }
            $causasDecesos = $_POST['causasDecesos'];
            $sql = "UPDATE detalle_causasdecesos SET activo = 0 WHERE idDifunto = ? AND activo = 1";
            if ($prepare_actual_decesos = $mysqli->prepare($sql))
            {
                if (!$prepare_actual_decesos->bind_param('i',$idDifunto))
                {
                    $mysqli->rollback();
                    $response['mensaje']        = "No se modificó nada, Error al enlazar los parámetros al intentar actualizar la lista de causas de deceso";
                    $response['status'] = 0;
                    $response['focus'] = 'causasDecesos';
                    responder($response, $mysqli);
                }
                if (!$prepare_actual_decesos->execute())
                {
                    $mysqli->rollback();
                    $response['mensaje']        = "No se modificó nada, Error al ejecutar los parámetros al intentar actualizar la lista de causas de deceso";
                    $response['status']         = 0;
                    responder($response, $mysqli);
                }
            }
            else
            {
                $mysqli->rollback();
                $response['mensaje']        = "No se modificó nada, Error al preparar los parámetros al intentar actualizar la lista de causas de deceso";
                $response['status']         = 0;
                responder($response, $mysqli);
            }
            foreach ( $causasDecesos as $idDeceso)
            {
                if (!$idDeceso_ = validarFormulario('i',$idDeceso))
                {
                    $mysqli->rollback();
                    $response['mensaje'] = "El formato del id del deceso no es el correcto. Elige al menos uno correctamente";
                    $response['status'] = 0;
                    $response['focus'] = 'causasDecesos';
                    responder($response, $mysqli);
                }
                else
                {
                    $sql = "INSERT INTO detalle_causasdecesos (idDifunto, idCausa, usuario) VALUES (?,?,?)";
                    if ($prepare_decesos = $mysqli->prepare($sql))
                    {
                        if (!$prepare_decesos->bind_param('iii',$idDifunto, $idDeceso_, $idUsuario))
                        {
                            $mysqli->rollback();
                            $response['mensaje']        = "No se modificó nada, Error al enlazar los parámetros al intentar guardar la lista de causas de deceso";
                            $response['status']         = 0;
                            responder($response, $mysqli);
                        }
                        if (!$prepare_decesos->execute())
                        {
                            $mysqli->rollback();
                            $response['mensaje']        = "No se modificó nada, Error al ejecutar los parámetros al intentar guardar la lista de causas de deceso";
                            $response['status']         = 0;
                            responder($response, $mysqli);
                        }
                    }
                    else
                    {
                        $mysqli->rollback();
                        $response['mensaje']        = "No se modificó nada, Error al preparar los parámetros al intentar guardar la lista de causas de deceso";
                        $response['status']         = 0;
                        responder($response, $mysqli);
                    }
                }
            }
			// Agregar evento en la bitácora de eventos ///////
			$nombreDifuntoInsert	= "$nombres $apellidop $apellidom";
			$idUsuario 				= $sesion->get("id");
			$ipUsuario 				= $sesion->get("ip");
			$pantalla				= "Agregar/Modificar difunto";
			$descripcion			= "Se modificó el difunto=$nombreDifuntoInsert, id=$idDifunto al catálogo de difuntos.";
			$sql					= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
			$mysqli					->query($sql);
			//////////////////////////////////////////////////
            if ($mysqli->commit())
            {
                $ds          = DIRECTORY_SEPARATOR;  //1
                $insert_id                  = $idDifunto;
                $storeFolder = '../images/avatars/difuntos/'.$insert_id;   //2

                $targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;  //4
                borrarDirectorio($targetPath);
                if (file_exists($targetPath) === FALSE)
                {
                    mkdir($targetPath, 0777);
                }
                if (sizeof($arrayImagenes) > 0)
                {
                    foreach ($arrayImagenes as $estaImagen)
                    {
                        $imagenBinario = $estaImagen;
                        $nuevoNombre = genRand(30);
                        $targetFile =  $targetPath. $nuevoNombre.'.jpg';  //5
                        $imagen_d   = base64_decode($imagenBinario); // decode an image
                        $im         = imagecreatefromstring($imagen_d); // php function to create image from string
                        if ($im     !== false)
                        {
                            $resp   = imagejpeg($im, $targetFile);
                            imagedestroy($im);
							// Agregar evento en la bitácora de eventos ///////
							$descripcion			= "Se agregó una nueva imagen path=$targetFile al expediente del difunto=$nombreDifuntoInsert, id=$insert_id.";
							$sql					= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
							$mysqli					->query($sql);
							//////////////////////////////////////////////////
                        }
                        else
                        {
                            $response['warning'] = "Hubo uno o más errores al guardar las imágenes.";
                        }
                    }
                }
                $response['mensaje']        = "$nombres $apellidop $apellidom";
                $response['status']         = 1;
				$mysqli->commit();
                responder($response, $mysqli);
            }
            else
            {
                $mysqli->rollback();
                $response['mensaje']        = "Error en comit. No se guardó nada";
                $response['status']         = 0;
                responder($response, $mysqli);
            }
        }
        else
        {
            $mysqli->rollback();
            $response['mensaje']        = "Error. No se pudo modificar.".$prepare->error." Falló la preparación de los datos. Vuelve a intentarlo nuevamente";
            $response['status']         = 0;
            responder($response, $mysqli);
        }
    }
?>
