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
        $permiso = $usuario->permiso("listarVentas",$mysqli);
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
		require ("../php/query.class.php");
		$query = new Query();
        $fInicio = $_GET['fechaInicio'];
        $fInicio_e = explode('-',$fInicio);
        $Y_ini = intval($fInicio_e[0]);
        $m_ini = intval($fInicio_e[1]);
        $d_ini = intval($fInicio_e[2]);
        // var_dump($_GET);
        $fFin = $_GET['fechaFin'];
        $fFin_e = explode('-',$fFin);
        $Y_fin = intval($fFin_e[0]);
        $m_fin = intval($fFin_e[1]);
        $d_fin = intval($fFin_e[2]);
        // var_dump($fInicio_e);
        if(checkdate($m_ini,$d_ini,$Y_ini) == FALSE || checkdate($m_fin,$d_fin,$Y_fin) == FALSE)
        {
            $json_data = [
                "data"   => 0
            ];
            echo json_encode($json_data);
            die;
        }
        $fInicio       .= " 00:00:00";
        $fFin          .= " 23:59:59";
		$res_nominas = $query ->table("cat_periodos_nominas")->select("id, fechaInicio, fechaFin, fechaCreacion")
							->where("fechaInicio", "BETWEEN", "'$fInicio' AND '$fFin'", "ss")->and()->where("activo", "=", 1 ,"i")->execute();
        if ($query->num_rows() == 0)
        {
            $json_data = [
                "data"   => 0
            ];
        }
        else
        {
            foreach ($res_nominas as $row)
            {
                $fechaInicio = date_create($row['fechaInicio']);
                $fechaInicio = date_format($fechaInicio, 'd-m-Y');
				$fechaFin = date_create($row['fechaFin']);
                $fechaFin = date_format($fechaFin, 'd-m-Y');
				$fechaCreacion = date_create($row['fechaCreacion']);
                $fechaCreacion = date_format($fechaCreacion, 'd-m-Y');
                $htmlBtns = '<div class="action-buttons">';
                $htmlBtns.= 	'<a class="green pointer" id="'.$row['id'].'" target="_blank" href="assets/pdf/facturaPDF.php?idFactura='.$row['id'].'" data-rel="tooltip" title="Mostrar lista">
                                    <i class="ace-icon fa fa-file-pdf-o bigger-130"></i>
                                </a>
                                <a class="btnEliminar pointer red" idCliente='.$row['id'].' data-rel="tooltip" title="Eliminar">
                                    <i class="ace-icon fa fa-ban bigger-130"></i>
                                </a>
                            </div>';
                $InfoData[] = array(
                    'id'                => str_pad($row['id'], 7, "0", STR_PAD_LEFT),
					'fechaInicio'     => $fechaInicio,
					'fechaFin'     => $fechaFin,
                    'fechaCreacion'     => $fechaCreacion,
                    'btns'              => $htmlBtns);
            }
            $json_data = [
                "data"   => $InfoData
            ];
        }
        echo json_encode($json_data);
    }
?>
