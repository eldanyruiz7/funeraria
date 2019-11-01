<?php
/**
* CLASE Query
* Esta clase sirve como Modelo, funciona para comunicar
* direcatmente con la base de datos
*
*
**/
class Query
{
	/**
	*
	* Obtiene el tipo de query (Select, Insert, Update)
	*
	*
	**/
	private function obtenerTipoQuery()
	{
		if (stripos($this ->sql, 'select') !== false) {
	    	$tipo = "consultar";
		}
		elseif (stripos($this ->sql, 'insert') !== false) {
	    	$tipo = "guardar";
		}
		elseif (stripos($this ->sql, 'update') !== false) {
	    	$tipo = "actualizar";
		}
		else
			$tipo = "enlazar";

		return $tipo;
	}
	/**
	*
	*
	* @var string String con la consulta SQL
	* @var array Array con parámetros, tipos y variables. Ejem: ("is","id, nombre")
	*
	*
	* $params= array("ss","string_1","string_2");
	*
	**/
    public function sentence($sql, $params)
    {
		//connect
		global $mysqli;
		$response = array();
		$this ->mensaje = NULL;
		$this ->status = 0;
		$this ->num_rows = 0;
		$this ->affected_rows = 0;
		$this ->data = 0;
		$this ->sql = $sql;
		//prepare
		if($prepare_select = $mysqli->prepare($sql))
		{
			$tmp = array();

			$tipo = $this ->obtenerTipoQuery();
			foreach($params as $key => $value)
				$tmp[$key] = &$params[$key];
			call_user_func_array(array($prepare_select, 'bind_param'), $tmp);

			if(!$prepare_select->execute())
			{
				$this ->mensaje = "No se puede $tipo la información. Error al ejecutar los parámetros";
                return false;
			}
			else
			{
				$a_data = array();
				if ($tipo == 'guardar' || $tipo == 'actualizar')
					$this ->affected_rows = $prepare_select ->affected_rows;

				else
				{
					$res_select 	= $prepare_select->get_result();
					$this ->num_rows= $res_select ->num_rows;
					while($row 		= $res_select->fetch_array(MYSQLI_ASSOC))
						$a_data[]	=$row;
					$this ->data 	= $a_data;
				}
				$this ->mensaje 	= "Sentencia realizada con éxito";
				$this ->status 		= 1;
				$prepare_select 	->close();
				return true;
			}
		}
		else
		{
			$this ->mensaje 		= "No se puede $tipo la información. Error al preparar los parámetros";
			return false;
		}


    }
	public function mensaje()
	{
		return $this->mensaje;
	}
	public function num_rows()
	{
		return $this->num_rows;
	}
	public function affected_rows()
	{
		return $this->affected_rows;
	}
	public function data()
	{
		return $this->data;
	}
	public function status()
	{
		return $this->status;
	}
}
