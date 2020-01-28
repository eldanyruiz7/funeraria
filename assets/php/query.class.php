<?php
/**
* CLASE Query
* Esta clase sirve como Modelo, funciona para comunicar
* direcatmente con la base de datos
*
*
**/
define("FILECFG","cnn.ini");
define("OBJ","obj");
define("RETURN_OBJECT","obj_always");
define("ARR","arr");
class Query
{
	//  Data base
	private $host;
	private $dataBase;
	private $user;
	private $pass;
	private $timeZone;
	// Objeto que representa la conexión actual abierta
	private static $mysqli = NULL;
	private $query = NULL;
	private $queryFields = NULL;
	private $queryForeign = NULL;
	private $select = NULL;
	private $table = "";
	private $fields = NULL;
	private $params = array();
	private $types = "";
	private $mensaje = NULL;
	private $status = 0;
	private $num_rows = 0;
	private $affected_rows = 0;
	private $insert_id = 0;
	/**
	*
	* Obtiene el tipo de query (Select, Insert, Update)
	*
	*
	**/
	function __construct()
	{
	    $this->loadConfig();
		$this ->conectar();
	}
	function __destruct()
	{
		if(!empty(self::$mysqli))
		   return;
		self::$mysqli->close();
	}
	private function loadConfig()
	{
		$FichCfg = __DIR__.DIRECTORY_SEPARATOR.FILECFG;
		if(!file_exists($FichCfg))
		{
		   $this ->mensaje = "Fallo al cargar las configuraciones. No se ha localizado el archivo: $FichCfg";
		   exit();
		}
		$cfg_db = parse_ini_file($FichCfg,true);
		if(!isset($cfg_db['DataBase'])) {
	       $this ->mensaje = "Fallo en el fichero de configuraciones. No se ha localizado la sección [DataBase]";
	       exit();
    	}
		$this->host = $cfg_db['DataBase']['host'];
	    $this->dataBase = $cfg_db['DataBase']['db'];
	    $this->user = $cfg_db['DataBase']['user'];
	    $this->pass = $cfg_db['DataBase']['pass'];
		$this->timeZone = $cfg_db['DataBase']['timeZone'];
		$this->charCode = $cfg_db['DataBase']['charCode'];
	}
	private function restartParam()
	{
		$this ->mensaje = NULL;
		$this ->status = 0;
		$this ->num_rows = 0;
		$this ->affected_rows = 0;
		$this ->insert_id = 0;
		$this ->data = 0;
		$this ->types = "";
		$this ->queryFields = "";
		$this ->queryTablesKeys = "";
		$this ->queryForeign = "";
		$this ->table = "";
	}
	private function conectar()
	{
		if(!empty(self::$mysqli))
		   return;
		date_default_timezone_set($this ->timeZone);
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	    self::$mysqli = new mysqli($this->host, $this->user, $this->pass, $this->dataBase);
	    self::$mysqli ->set_charset($this ->charCode);
	    if(self::$mysqli->connect_errno)
	    {
			$this ->mensaje = "Fallo en la conexión. Errno: ". self::$mysqli->connect_errno ."Error: ". self::$mysqli->connect_error;
			exit();
	    }
	}
	private function to_object(array $array, $class = 'stdClass')
	{
		$object = new $class;
		foreach ($array as $key => $value)
		{
			if (is_array($value))
			{
				// Convert the array to an object
				$value = $this->to_object($value, $class);
			}
			// Add the value to the object
			$object->{$key} = $value;
		}
		return $object;
	}
	public function table($tb)
	{
		$this ->table = $tb;
		return $this;
	}
	public function select($fields = '*')
	{
		global $params;
		$params = array();
		$this ->query = "SELECT $fields FROM ".$this ->table;
		return $this;
	}
	public function insert($arrayValues, $ty)
	{
		$fieldList = "";
		$typesList = "";
		$cont = 1;
		global $params;
		$params = $arrayValues;
		foreach ($arrayValues as $key => $value)
		{
			if ($cont == 1)
			{
				$fieldList = $key;
				$typesList = "?";
			}
			else
			{
				$fieldList .= ", ".$key;
				$typesList .= ", ?";
			}
			$cont++;
		}
		$this ->types = $ty;
		$this ->query = "INSERT INTO ".$this ->table." ($fieldList) VALUES ($typesList)";
		return $this;
	}
	public function update($arrayValues, $ty)
	{
		$fieldList = "";
		// $typesList = "";
		$cont = 1;
		global $params;
		$params = $arrayValues;
		foreach ($arrayValues as $key => $value)
		{
			if ($cont == 1)
				$fieldList = $key." = ?";
			else
				$fieldList .= ", ".$key." = ?";
			$cont++;
		}
		$this ->types = $ty;
		$this ->query = "UPDATE ".$this ->table." SET $fieldList";
		return $this;
	}
	public function innerJoin($tableJoin, $fieldAjoin, $rule, $fieldBjoin)
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
		global $params;
		if ($rule == "BETWEEN")
		{
			if (stripos($this ->query, 'WHERE') == true)
				$this ->query .= " $fieldAwhere BETWEEN ? AND ?";
			else
				$this ->query .= " WHERE $fieldAwhere BETWEEN ? AND ?";
			$chars = array("'");
			$str = str_ireplace($chars, "", $fieldBwhere);
			$chars = array(" AND ");
			$str = str_ireplace($chars, " AND ", $str);
			$explode = explode(" AND ", $str);
			$params[] = $explode[0];
			$params[] = $explode[1];
		}
		else
		{
			if (stripos($this ->query, 'WHERE') == true)
				$this ->query .= " $fieldAwhere $rule ?";
			else
				$this ->query .= " WHERE $fieldAwhere $rule ?";
			$params[] = $fieldBwhere;
		}
		$this ->types .= $type;
		return $this;
	}
	public function and(...$argsJoin)
	{
		$args = func_num_args();
		$this ->query .= " AND";
		if($args == 3)
		{
			foreach ($argsJoin as $arg_join)
			{
				$this ->query.= " $arg_join";
			}
		}
		return $this;
	}
	public function or(...$argsJoin)
	{
		$args = func_num_args();
		$this ->query .= " OR";
		if($args == 3)
		{
			foreach ($argsJoin as $arg_join)
			{
				$this ->query.= " $arg_join";
			}
		}
		return $this;
	}
	public function limit(...$argsLimit)
	{
		$args = func_num_args();
		if ($args == 0)
		{
			$this ->query .= " LIMIT 1";
		}
		elseif ($args == 1)
		{
			$this ->query .= " LIMIT $argsLimit[0]";
		}
		elseif ($args == 2)
		{
			$this ->query .= " LIMIT $argsLimit[0] OFFSET $argsLimit[1]";
		}
		return $this;
	}
	public function orderBy(...$argsFieldOrder)
	{
		$args = func_num_args();
		if ($args == 1)
		{
			$this ->query .= " ORDER BY $argsFieldOrder[0] ASC";
		}
		elseif ($args > 1)
		{
			$this ->query .= " ORDER BY";
			$x = 0;
			foreach ($argsFieldOrder as $arg_order)
			{
				$x++;
				$this ->query .= " $arg_order";
				if ($x%2 == 0 && $x < $args)
				{
					$this ->query .=",";
				}

			}
		}
		return $this;
	}
	public function groupBy($fieldsGroupBy)
	{
		$this ->query .= " GROUP BY $fieldsGroupBy";
		return $this;
	}
	public function execute($debug = FALSE, $return = 'arr')
	{
		$tipo = $this ->obtenerTipoQuery();
		if ($tipo == "crearTabla")
		{
			$this ->query .= "(".$this ->queryFields."".$this ->queryTablesKeys."".$this ->queryForeign.");";
			// echo $this ->query;
			if($prepare_select = self::$mysqli ->query($this ->query))
			{
				$table = $this->table;
				$this ->restartParam();
				$this ->mensaje = "Tabla <b>".$table."</b> creada con éxito";
				if ($debug)
					$this ->mensaje .= "<br>".$this ->query;
				$this ->status = 1;
				return true;
			}
			else
			{
				$this ->mensaje = "No se puede crear la tabla. Error al preparar los parámetros";
				if ($debug)
					return $this ->mensaje .= "<br>".$this->query;
				return false;
			}
		}
		// echo $this ->query;
		if($prepare_select = self::$mysqli ->prepare($this ->query))
		{
			$tmp = array();
			global $params;
			if ($this ->types)
			{
				$t = $this->types;
				@array_unshift($params, $t);
				foreach($params as $key => $value)
				$tmp[$key] = &$params[$key];
				@call_user_func_array(array($prepare_select, 'bind_param'), $tmp);
			}
			if(!$prepare_select->execute())
			{
				$this ->mensaje = "No se puede ejecutar la sentencia. Error al ejecutar los parámetros";
				if ($debug)
					$this ->mensaje .= "<br>$query";
				return false;
			}
			else
			{
				$this ->restartParam();
				if ($tipo == 'guardar' || $tipo == 'actualizar')
				{
					$this ->affected_rows = $prepare_select ->affected_rows;
					$this ->insert_id  = self::$mysqli->insert_id;
				}
				elseif($tipo == 'consultar')
				{
					$a_data = array();
					$res_select = $prepare_select->get_result();
					$this ->num_rows= $res_select ->num_rows;
					if ($return == 'arr')
					{
						$a_data = $res_select ->fetch_all(MYSQLI_ASSOC);
					}
					elseif ($return == 'obj')
					{
						// $user = (object)$user;
						// echo "Num_rows: ".$this->num_rows."-";
						if($this->num_rows() > 1)
							$a_data = $this->to_object($res_select ->fetch_all(MYSQLI_ASSOC));
						else
							$a_data = $res_select ->fetch_object();
					}
					elseif ($return == 'obj_always')
					{
						$a_data = $this->to_object($res_select ->fetch_all(MYSQLI_ASSOC));
					}
					$this ->data = $a_data;
				}
				$this ->mensaje = "Sentencia realizada con éxito";
				if ($debug)
					$this ->mensaje .= "<br>".$this ->query;
				$this ->status = 1;
				$prepare_select ->close();
				return $this ->data;
			}
		}
		else
		{
			$this ->mensaje = "No se puede ejecutar la sentencia. Error al preparar los parámetros";
			if ($debug)
				return $this ->mensaje .= "<br>".$this->query;
			return false;
		}
	}
	/**
	 * DB Backup
	 */
	public function backup($tables = "*")
	{
		//ENTER THE RELEVANT INFO BELOW
    // $mysqlUserName      = "Your Username";
    // $mysqlPassword      = "Your Password";
    // $mysqlHostName      = "Your Host";
    // $DbName             = "Your Database Name here";
    // $backup_name        = "mybackup.sql";
    // $tables             = "Your tables";

   //or add 5th parameter(array) of specific tables:    array("mytable1","mytable2","mytable3") for multiple tables

    // Export_Database($mysqlHostName,$mysqlUserName,$mysqlPassword,$DbName,  $tables=false, $backup_name=false );

    // function Export_Database($host,$user,$pass,$name,  $tables=false, $backup_name=false )
    // {
    	// $this ->query()
        // $mysqli = new mysqli($host,$user,$pass,$name);
        // $mysqli->select_db($name);
        // $mysqli->query("SET NAMES 'utf8'");
        $backup_name = FALSE;
        $this ->restartParam();
		self::$mysqli->query("SET NAMES 'utf8'");

		$queryTables = self::$mysqli->query('SHOW TABLES FROM '.$this->dataBase);
		// print_r($queryTables);
		while($row = $queryTables->fetch_row())
		{
			$target_tables[] = $row[0];
		}
		// var_dump($target_tables);
		$tables = false;
		if($tables !== false)
		{
			$target_tables = array_intersect( $target_tables, $tables);
		}
		$content = "SET FOREIGN_KEY_CHECKS=0;\n";
		foreach($target_tables as $table)
		{
			$result = self::$mysqli->query('SELECT * FROM '.$table);
			$fields_amount = $result->field_count;
			$rows_num = self::$mysqli->affected_rows;
			$res = self::$mysqli->query('SHOW CREATE TABLE '.$table);
			// self::$mysqli->query("SET FOREIGN_KEY_CHECKS=0;");
			$TableMLine = $res->fetch_row();
			$content = (!isset($content) ?  '' : $content) . "\n\n".$TableMLine[1].";\n\n";

			for ($i = 0, $st_counter = 0; $i < $fields_amount;   $i++, $st_counter=0)
			{
				while($row = $result->fetch_row())
				{
					if ($st_counter%100 == 0 || $st_counter == 0)
					{
						$content .= "\nINSERT INTO ".$table." VALUES";
					}
					$content .= "\n(";
					for($j=0; $j<$fields_amount; $j++)
					{
						$row[$j] = str_replace("\n","\\n", addslashes($row[$j]) );
						if (isset($row[$j]))
						{
							$content .= '"'.$row[$j].'"' ;
						}
						else
						{
							$content .= '""';
						}
						if ($j<($fields_amount-1))
						{
							$content.= ',';
						}
					}
					$content .=")";

					if ( (($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num)
					{
						$content .= ";";
					}
					else
					{
						$content .= ",";
					}
					$st_counter=$st_counter+1;
				}
			} $content .="\n\n\n";
		}
		// self::$mysqli->query("SET FOREIGN_KEY_CHECKS=1;");

		$backup_name = $backup_name ? $backup_name : $this->dataBase."___(".date('H-i-s')."_".date('d-m-Y').")__rand".rand(1,11111111).".sql";
		$backup_name = $backup_name ? $backup_name : $this->dataBase.".sql";
		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary");
		header("Content-disposition: attachment; filename=\"".$backup_name."\"");
		return $content;
	}
	//////////////////////// MIGRATIONS ///////////////////////
	public function createTable($table, $replace = TRUE)
	{
		// $this ->conectar();
		if ($replace)
			$this ->query = "CREATE TABLE IF NOT EXISTS $table";
		else
			$this ->query = "CREATE TABLE $table";
		$this ->table = $table;
		return $this;
	}
	public function dropTable($table, $foreignKeyChecks = 1)
	{
		$queryKeys = $foreignKeyChecks ? "SET FOREIGN_KEY_CHECKS=1;" : "SET FOREIGN_KEY_CHECKS=0;";
		$this ->query = "DROP TABLE IF EXISTS $table;";
		// echo $this->query;
		self::$mysqli ->query($queryKeys);
		if($prepare_select = self::$mysqli ->query($this ->query))
		{
			return TRUE;
		}
		else {
			return FALSE;
		}
	}
	public function bigIncrements($name)
	{
		if (strlen($this ->queryFields))
			$this ->queryFields .= ", ";
		$this ->queryFields .= " $name BIGINT UNSIGNED NOT NULL AUTO_INCREMENT";
		//if (strlen($this ->queryTablesKeys))
			$this ->queryTablesKeys = ", ";
		$this ->queryTablesKeys .= " PRIMARY KEY ($name)";
		return $this;
	}
	public function intIncrements($name)
	{
		if (strlen($this ->queryFields))
			$this ->queryFields .= ", ";
		$this ->queryFields .= "$name INT UNSIGNED NOT NULL AUTO_INCREMENT";
		//if (strlen($this ->queryTablesKeys))
			$this ->queryTablesKeys = ", ";
		$this ->queryTablesKeys .= "PRIMARY KEY ($name)";
		return $this;
	}
	public function bigInt($name, $null = FALSE)
	{
		$nullable = $null ? "NULL" : "NOT NULL";
		if (strlen($this ->queryFields))
		$this ->queryFields .= ", ";
		$this ->queryFields .= "$name BIGINT UNSIGNED $nullable";
		return $this;
	}
	public function int($name, $null = FALSE, $defaultVal = FALSE)
	{
		$defaultValue = $defaultVal === FALSE ? "" : "DEFAULT '$defaultVal'";
		$nullable = $null ? "NULL" : "NOT NULL";
		if (strlen($this ->queryFields))
			$this ->queryFields .= ", ";
		$this ->queryFields .= "$name INT UNSIGNED $nullable $defaultValue";
		return $this;
	}
	public function varChar($name, $size = 250, $null = FALSE)
	{
		$nullable = $null ? "NULL" : "NOT NULL";
		if (strlen($this ->queryFields))
			$this ->queryFields .= ", ";
		$this ->queryFields .= "$name VARCHAR($size) $nullable";
		return $this;
	}
	public function date($name, $null = FALSE)
	{
		$nullable = $null ? "NULL" : "NOT NULL";
		if (strlen($this ->queryFields))
			$this ->queryFields .= ", ";
		$this ->queryFields .= "$name DATE $nullable";
		return $this;
	}
	public function dateTime($name, $null = FALSE)
	{
		$nullable = $null ? "NULL" : "NOT NULL";
		if (strlen($this ->queryFields))
			$this ->queryFields .= ", ";
		$this ->queryFields .= "$name DATETIME $nullable";
		return $this;
	}
	public function dateCurrent($name, $null = FALSE)
	{
		$nullable = $null ? "NULL" : "NOT NULL";
		if (strlen($this ->queryFields))
			$this ->queryFields .= ", ";
		$this ->queryFields .= "$name DATE $nullable DEFAULT CURRENT_TIMESTAMP";
		return $this;
	}
	public function dateTimeCurrent($name, $null = FALSE)
	{
		$nullable = $null ? "NULL" : "NOT NULL";
		if (strlen($this ->queryFields))
			$this ->queryFields .= ", ";
		$this ->queryFields .= "$name DATETIME $nullable DEFAULT CURRENT_TIMESTAMP";
		return $this;
	}
	public function decimal($name, $digits = "(10,2)", $null = FALSE, $defaultVal = FALSE)
	{
		$defaultValue = $defaultVal === FALSE ? "" : "DEFAULT '$defaultVal'";
		$nullable = $null ? "NULL" : "NOT NULL";
		if (strlen($this ->queryFields))
			$this ->queryFields .= ", ";
		$this ->queryFields .= "$name DECIMAL $digits $nullable $defaultValue";
		return $this;
	}
	public function foreignKey($constraint, $field, $tableForeign, $fieldForeign)
	{
		$this ->queryForeign .= ", CONSTRAINT $constraint FOREIGN KEY ($field) REFERENCES $tableForeign ($fieldForeign)";
		return $this;
	}
	private function obtenerTipoQuery()
	{
		if (stripos($this ->query, 'select') !== false) {
			$tipo = "consultar";
		}
		elseif (stripos($this ->query, 'insert') !== false) {
			$tipo = "guardar";
		}
		elseif (stripos($this ->query, 'update') !== false) {
			$tipo = "actualizar";
		}
		elseif (stripos($this ->query, 'create table') !== false) {
			$tipo = "crearTabla";
		}
		else
			$tipo = "enlazar";
		return $tipo;
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
	public function lastStatement()
	{
		return $this->query;
	}
	// Soporte para transacciones
	public function autocommit($bool)
	{
		// $this ->conectar();
		self::$mysqli ->autocommit($bool);
	}
	public function commit()
	{
		$resp = self::$mysqli ->commit();
		return $resp;
	}
	public function rollback()
	{
		self::$mysqli ->rollback();
	}
}
