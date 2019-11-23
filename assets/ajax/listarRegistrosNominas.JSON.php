<?php
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
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
		$fInicio = $_POST['fechaInicio'];
        $fInicio_e = explode('-',$fInicio);
        $Y_ini = intval($fInicio_e[0]);
        $m_ini = intval($fInicio_e[1]);
        $d_ini = intval($fInicio_e[2]);
        // var_dump($_GET);
        $fFin = $_POST['fechaFin'];
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
		$response = array(
			"status"        => 1
		);
        require "../php/responderJSON.php";
        require_once "../php/funcionesVarias.php";
		require "../php/query.class.php";
		$query 		= new Query();
		/**
		 * Obtener totales por primeras aportaciones,
		 * por rango de fechas
		 */
		$totalNominas = $query ->table("cat_usuarios") ->select("id AS idUsuario, CONCAT(nombres, ' ', apellidop, ' ', apellidom) AS nombres")
								->where("activo", "=", 1, "i")->and()->where("id", "<>", 1, "i")->execute();
        // $sql = "SELECT
        //             contratos.id                AS idContrato
        //         FROM contratos
        //         WHERE contratos.enCurso = 1 AND contratos.activo = 1";
        // $res_ = $mysqli->query($sql);
        $num = $query->num_rows();
        if ($num == 0)
        {
			$json_data["Records"] = 0;
        }
        else
        {
            foreach ($totalNominas as $nomina)
			{
				$totalAportaciones = $query ->table("contratos")->select("IFNULL(SUM(primerAnticipo),0) AS suma")
											->where("fechaCreacion", "BETWEEN", "'$fInicio' AND '$fFin'", "ss")->and()
											->where("idVendedor", "=", $nomina['idUsuario'], "i")->and()
											->where("activo", "=", 1, "i")->execute();
				// echo $query->lastStatement();
				// echo "<br>".$fInicio;
				// echo "<br>".$fFin;
				// $totalCobranzaVenta
                $InfoData[] = array(
					'idUsuario'         => $nomina['idUsuario'],
                    'nombres'           => $nomina['nombres'],
                    'aportaciones'      => $totalAportaciones[0]['suma']);
                    //'precio'            => "$".number_format($contrato->costoTotal,2,".",","));
            }
        //$data[] = $InfoData;
			$json_data["Result"] = "OK";
            $json_data["Records"] = $InfoData;
        }
        echo json_encode($json_data);
    }
?>
