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
        usleep(1000000);
        require "../php/responderJSON.php";

        $idUsuarioAsignado               = $_POST['idUsuario'];
        $listaRango                      = $_POST['listaRango'];
		$inputPrefijo					= $_POST['inputPrefijo'];
        $response = array(
            "status"                    => 1
        );

        if (!$idUsuarioAsignado = validarFormulario('i',$idUsuarioAsignado))
        {
            $response['mensaje'] = "El formato del Id del usuario no es el correcto";
            $response['status'] = 0;
            responder($response, $mysqli);
        }
		$inputPrefijo = validarFormulario('s',$inputPrefijo, FALSE);
		$inputPrefijo = (!$inputPrefijo) ? "" : $inputPrefijo;
        $array = array(" ", "\\");
        $listaRango = str_replace($array, "", $listaRango);
        if (!$listaRango = validarFormulario('s',$listaRango,0))
        {
            $response['mensaje'] = "La lista de folios no puede estar en blanco";
            $response['status'] = 0;
            $response['focus'] = 'textAreaFolios';
            responder($response, $mysqli);
        }
        // $lista = strtoupper($lista);
        $idUsuario      = $sesion->get('id');
        $sql            = "SELECT idSucursal FROM cat_usuarios WHERE id = $idUsuario LIMIT 1";
        $res_noSucursal = $mysqli->query($sql);
        $row_noSucursal = $res_noSucursal->fetch_assoc();
        $idSucursal     = $row_noSucursal['idSucursal'];
        $sql = "SELECT
                    folios_cobranza_asignados.folio                 AS noFolio,
                    folios_cobranza_asignados.idUsuario_asignado    AS idUsuario_asignado,
                    cat_usuarios.nombres                            AS nombreUsuario_asignado,
                    cat_usuarios.apellidop                          AS apellidopUsuario_asignado,
                    cat_usuarios.apellidom                          AS apellidomUsuario_asignado
                FROM folios_cobranza_asignados
                INNER JOIN cat_usuarios
                ON folios_cobranza_asignados.idUsuario_asignado = cat_usuarios.id
                WHERE folios_cobranza_asignados.activo = 1 AND folios_cobranza_asignados.asignado = 0";
        $res_folios = $mysqli->query($sql);
        $listaMostrar = "<b>";
        $listaAgregar = array();
        while ($row_folios = $res_folios->fetch_assoc())
        {
            $foliosRegistrados[] = $row_folios;
        }
        // var_dump($foliosRegistrados);
        // die;
        $comma = substr_count($listaRango, '-'); // Cuenta el número de apariciones del caracter (-)
        if ($comma == 0)
        {
			$str_pad	= strlen($listaRango);
            for ($a=0; $a < sizeof($foliosRegistrados); $a++)
            {
                if ($inputPrefijo.$listaRango == $foliosRegistrados[$a]['noFolio'])
                {
                    $usuarioAsignoFolio = $foliosRegistrados[$a]['nombreUsuario_asignado']." ".$foliosRegistrados[$a]['apellidopUsuario_asignado']." ".$foliosRegistrados[$a]['apellidomUsuario_asignado'];
                    $response['mensaje'] = "No se puede guardar. El folio No. <b>$listaRango</b> ya existe y está asignado al usuario: <br><b>$usuarioAsignoFolio</b>";
                    $response['status'] = 0;
                    $response['focus'] = 'textAreaFolios';
                    responder($response, $mysqli);
                }
            }
            if (strlen($listaRango) > 0)
            {
				// $code               .=  $code_ = str_pad($factura->id, 10, "0", STR_PAD_LEFT);
                $listaAgregar[] = str_pad($listaRango, $str_pad, "0", STR_PAD_LEFT);
            }
        }
        elseif ($comma == 1)
        {
            $lista_explode = explode("-",$listaRango);
            if (!is_numeric($lista_explode[0]) || !is_numeric($lista_explode[1]))
            {
                $response['mensaje'] = "No se puede guardar. Los rangos de inicio y final deben ser numéricos";
                $response['status'] = 0;
                $response['focus'] = 'textAreaFolios';
                responder($response, $mysqli);
            }
            if ($lista_explode[0] >= $lista_explode[1])
            {
                $response['mensaje'] = "No es un rango válido, el rango de inicio (<b>".$lista_explode[0]."</b>) debe ser menor que el rango final (<b>".$lista_explode[1]."</b>)";
                $response['status'] = 0;
                $response['focus'] = 'textAreaFolios';
                responder($response, $mysqli);
            }
			$str_pad = strlen($lista_explode[0]);

            for ($x=$lista_explode[0]; $x <= $lista_explode[1] ; $x++)
            {
                for ($a=0; $a < sizeof($foliosRegistrados); $a++)
                {
                    if ($inputPrefijo.str_pad(strval($x), $str_pad, "0", STR_PAD_LEFT) == $foliosRegistrados[$a]['noFolio'])
                    {
                        $usuarioAsignoFolio = $foliosRegistrados[$a]['nombreUsuario_asignado']." ".$foliosRegistrados[$a]['apellidopUsuario_asignado']." ".$foliosRegistrados[$a]['apellidomUsuario_asignado'];
                        $response['mensaje'] = "No puede asignarse. El folio No. <b>".$inputPrefijo."".$x."</b> ya existe y está asignado al usuario: <br><b>$usuarioAsignoFolio</b>";
                        $response['status'] = 0;
                        $response['focus'] = 'textAreaFolios';
                        responder($response, $mysqli);
                    }
                }
                $listaAgregar[] = str_pad(strval($x), $str_pad, "0", STR_PAD_LEFT);
            }
        }
        else {
            $response['mensaje'] = "No es un rango válido";
            $response['status'] = 0;
            $response['focus'] = 'textAreaFolios';
            responder($response, $mysqli);
        }
        $mysqli->autocommit(FALSE);
        for ($i=0; $i < sizeof($listaAgregar) ; $i++)
        {
            $esteFolio = $inputPrefijo.$listaAgregar[$i];
            $sql = "INSERT INTO folios_cobranza_asignados (idUsuario_asignado, folio, idSucursal, idUsuario) VALUES (?,?,?,?)";
            $prepare = $mysqli->prepare($sql);
            if (!$prepare ||
                !$prepare->bind_param("isii",$idUsuarioAsignado, $esteFolio, $idSucursal, $idUsuario) ||
                !$prepare->execute() ||
                !$prepare->affected_rows > 0)
                {
                    $mysqli->rollback();
                    $response['mensaje'] = "Ocurrió un error al preparar la sentencia con el folio: <b>$esteFolio</b>";
                    $response['status'] = 0;
                    $response['focus'] = '';
                    responder($response, $mysqli);
                }
                else
                {
                    $listaMostrar .= $esteFolio." ";
                }
        }

		// Agregar evento en la bitácora de eventos ///////
		$idUsuario 					= $sesion->get("id");
		$ipUsuario 					= $sesion->get("ip");
		$pantalla					= "Listar usuarios";
		$descripcion				= "Se asignó folio(s) de cobranza($listaRango), prefijo=$inputPrefijo, al usuario con id=$idUsuarioAsignado.";
		$sql						= "CALL agregarEvento($idUsuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
		$mysqli						->query($sql);
        if ($mysqli->commit())
        {
            $response['mensaje'] = "Folios agregados correctamente.<br> Lista de folios: <b> $listaMostrar";
            $response['status'] = 1;
            responder($response, $mysqli);
        }
        else
        {
            $mysqli->rollback();
            $response['mensaje'] = "Ocurrió un error en commit, No se guardó nada. Inténtalo nuevamente";
            $response['status'] = 0;
            $response['focus'] = '';
            responder($response, $mysqli);
        }
    }
?>
