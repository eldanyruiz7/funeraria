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
		$permiso = TRUE;
        $permiso = $usuario->permiso("eliminarNomina",$mysqli);
        if (!$permiso)
        {
            $response['mensaje'] 		= "No se pudo eliminar este registro. Usuario con permisos insuficientes para realizar esta acción";
            $response['status'] 		= 0;
            responder($response, $mysqli);
        }
		require ("../php/query.class.php");
		$query 		= new Query();

        $idPeriodo_el					= $_POST['idCliente'];
		$idUsuario      				= $sesion->get('id');
		$resSucursal = $query ->table("cat_usuarios")->select("idSucursal AS id")->where("id", "=", $idUsuario, "i")->limit()->execute(FALSE, OBJ);

		$idSucursal						= $resSucursal->id;
        $response = array(
            "status"                    => 0
        );
        if(!$idPeriodo_el = validarFormulario('i', $idPeriodo_el))
        {
            $response['mensaje']        = "El ID del usuario no cumple con el formato establecido";
            $response['focus']          = '';
            responder($response, $mysqli);
        }
		$Periodo = $query 				->table("cat_periodos_nominas")->select()->where("id", "=", $idPeriodo_el, "i")->and()
										->where("activo", "=", 1, "i")->limit()->execute();
		if ($query->num_rows() == 0)
		{
			$response['mensaje']        = "No existe el registro seleccionado";
            $response['focus']          = '';
            responder($response, $mysqli);
		}
		/**
		 * Eliminar Periodo
		 */
		$query->autocommit(FALSE);
		$query->table("cat_periodos_nominas")		->update(array("activo" => 0),"i")			->where("id", "=", $idPeriodo_el, "i")->limit(1)->execute();
		$query->table("cat_nominas")				->update(array("activo" => 0), "i")			->where("idPeriodo", "=", $idPeriodo_el, "i")	->execute();
		$resNominas = $query->table("cat_nominas")	->select("id")								->where("idPeriodo", "=", $idPeriodo_el, "i")	->execute(FALSE, OBJ);
		foreach ($resNominas as $nomina)
		{
			$query->table("detalle_nomina")			->update(array("activo" => 0), "i")			->where("idNomina", "=", $nomina->id, "i")		->execute();
			$query->table("contratos")				->update(array("idNomina" => 0),"i")		->where("idNomina", "=", $nomina->id, "i")		->execute();
			$query->table("detalle_pagos_contratos")->update(array("idNominaVenta" => 0), "i")	->where("idNominaVenta", "=", $nomina->id, "i")	->execute();
			$query->table("detalle_pagos_contratos")->update(array("idNominaCobranza" => 0),"i")->where("idNominaCobranza","=", $nomina->id,"i")->execute();
		}
		if ($query->commit())
		{
			$response['mensaje']        = "Periodo eliminado exitosamente";
			$response['status']         = 1;
			responder($response, $mysqli);
		}
		else
		{
			$query->rollback();
		}
    }
