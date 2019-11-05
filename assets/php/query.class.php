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
	private $mysqli = NULL;
	private $query = NULL;
	private $select = NULL;
	private $table = NULL;
	private $fields = NULL;
	private $params = array();
	private $types = "";
	// private $types = NULL;
	/**
	*
	* Obtiene el tipo de query (Select, Insert, Update)
	*
	*
	**/
	private function conectar()
	{
		date_default_timezone_set('America/Mexico_City');
	    $servidor       = 'localhost';
	    $usr            = 'root';
	    $contrasena     = 'FlorVenenosa9';
	    $bd             = 'funerariadb';

	    $mysqli         = new mysqli($servidor , $usr , $contrasena , $bd);
	    $mysqli->set_charset("utf8");

	    if($mysqli->connect_errno)
	    {
	        echo "Error de Base de datos\n";
	        echo "Errno: ". $mysqli->connect_errno . "\n";
	        echo "Error: ". $mysqli->connect_error . "\n";
	        exit;
	    }
		else {
			$this ->mysqli = $mysqli;
		}
	}
	public function table($tb)
	{
		//echo $tb;
		$this ->table = $tb;
		// return self::$tb;
		return $this;
	}
	public function select($fields)
	{
		if ($this ->mysqli === NULL)
		{
			$this ->conectar();
		}

		$this ->query = "SELECT $fields FROM ".$this ->table;
		return $this;
	}
	public function join($tableJoin, $fieldAjoin, $rule, $fieldBjoin)
	{
		$this->query .= " INNER JOIN $tableJoin ON $fieldAjoin $rule $fieldBjoin";

		return $this;
	}
	public function leftJoin($tableJoin, $fieldAjoin, $rule, $fieldBjoin)
	{
		$this->query .= " LEFT JOIN $tableJoin ON $fieldAjoin $rule $fieldBjoin";
		return $this;
	}
	public function rightJoin($tableJoin, $fieldAjoin, $rule, $fieldBjoin)
	{
		$this->query .= " RIGHT JOIN $tableJoin ON $fieldAjoin $rule $fieldBjoin";
		return $this;
	}
	public function where($fieldAwhere, $rule, $fieldBwhere, $type)
	{
		if (stripos($this ->query, 'WHERE') == true)
		{
			$this ->query .= " $fieldAwhere $rule ?";
		}
		else
		{
			$this ->query .= " WHERE $fieldAwhere $rule ?";
		}
		$this ->types .= $type;
		global $params;
		$params[] = $fieldBwhere;
		return $this;
	}
	public function and()
	{
		$this ->query .= " AND";
		return $this;
	}
	public function or()
	{
		$query .= " OR";
		return $this;
	}
	public function get($debug = FALSE)
	{
		//echo $this ->query;
		if($prepare_select = $this ->mysqli ->prepare($this ->query))
		{
			$tmp = array();

			// $tipo = $this ->obtenerTipoQuery();
			global $params;
			// var_dump( $params );
			$t = $this->types;
			array_unshift($params, $t);
			// var_dump($params);
			foreach($params as $key => $value)
			$tmp[$key] = &$params[$key];
			call_user_func_array(array($prepare_select, 'bind_param'), $tmp);

			if(!$prepare_select->execute())
			{
				$this ->mensaje = "No se puede consultar la información. Error al ejecutar los parámetros";
				if ($debug)
					$this ->mensaje .= "<br>$query";
				return false;
			}
			else
			{
				$a_data = array();
				$res_select 	= $prepare_select->get_result();
				$this ->num_rows= $res_select ->num_rows;
				while($row 		= $res_select->fetch_array(MYSQLI_ASSOC))
				$a_data[]	=$row;
				$this ->data 	= $a_data;
				$this ->mensaje 	= "Sentencia realizada con éxito";
				if ($debug)
					$this ->mensaje .= "<br>".$this ->query;
				$this ->status 		= 1;
				$prepare_select 	->close();
				return $this ->data;
			}
		}
		else
		{
			$this ->mensaje 		= "No se puede consultar la información. Error al preparar los parámetros";
			if ($debug)
				return $this ->mensaje .= "<br>".$this->query;
			return false;
		}
	}
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
		$this ->insert_id = 0;
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
				{
					$this ->affected_rows = $prepare_select ->affected_rows;
					$this ->insert_id  = $mysqli->insert_id;
				}
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
	public function insert_id()
	{
		return $this->insert_id;
	}
}
