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
        require ("../php/query.class.php");
        $usuario = new usuario($idUsuario,$mysqli);
		$query = new Query();
        $permiso = $usuario->permiso("listarUsuarios",$mysqli);
        if (!$permiso)
        {
            $json_data = [
                "data"   => 0
            ];
            echo json_encode($json_data);
            die;
        }
        $response = array(
            "status"        => 1
        );
		$res_usuario = $query->table("cat_usuarios")->select("id")->where("activo", "=", 1, "i")->and()->where("tipo", ">", 0, "i")->execute();


        if ($query->num_rows() == 0)
        {
            $json_data = [
                "data"   => 0
            ];
        }
        else
        {
            foreach ($res_usuario as $row)
			{
                $usuario = new usuario($row['id'], $mysqli);
                $InfoData[] = array(
                    'id'                => $usuario->id,//$row['id'],
                    'nombre'            => $usuario->nombres,//$row['nombre']." ".$row['apellidop']." ".$row['apellidom'],
					'tasaComision'      => $usuario->tasaComision." %",
                    'tasaComisionCobranza' => $usuario->tasaComisionCobranza." %",
                    'nickName'          => $usuario->nickName,
                    'fechaReg'          => $usuario->fechaCreacion(),
                    'tipo'              => $usuario->tipoUsuario(TRUE),
                    'idSucursal'        => $usuario->idSucursal,//$row['idSucursal'],
                    'btns'              => '<div class="hidden-sm hidden-xs action-buttons">
                                                <a id-usuario = "'.$usuario->id.'" nombre-usuario="'.$usuario->nombres.'" class="blue pointer aAsignarFolios" data-rel="tooltip" title="Asignar folios de cobranza">
                                                    <i class="ace-icon fa fa-ticket bigger-130"></i>
                                                </a>
                                                <a href="agregarUsuario.php?idUsuario='.$usuario->id.'" class="purple pointer aEdit" data-rel="tooltip" title="Editar">
            									    <i class="ace-icon fa fa-pencil-square-o bigger-130"></i>
            									</a>
            									<a class="btnEliminar pointer red" idCliente='.$usuario->id.' data-rel="tooltip" title="Eliminar">
            										<i class="ace-icon fa fa-trash-o bigger-130"></i>
            								    </a>
            								</div>');
            }
            //$data[] = $InfoData;
            $json_data = [
                "data"   => $InfoData
            ];
        }
        echo json_encode($json_data);
    }
?>
