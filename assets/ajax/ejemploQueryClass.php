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
	require ("../php/query.class.php");
	$usuario 	= new usuario($idUsuario,$mysqli);
	$query 		= new Query();
	$a = $query ->table('bitacora_eventos AS be')
				->select('be.id AS idRegistro, descripcion, cu.nombres AS nombreUsuario, cu.apellidop AS apellidopUsuario')
				->innerJoin("cat_usuarios AS cu", "be.idUsuario", "=", 'cu.id')
				->where("be.id",">", "2", "i")
				->and()
				->where("be.id", "<", "10", "i")
				->orderBy("descripcion", "ASC")
				// ->limit(20)
				->execute(TRUE);

foreach ($a as $key) {
	echo $key["idRegistro"]." => ".$key['descripcion']." - Usuario: ".$key['nombreUsuario']." ".$key['apellidopUsuario']."<br>";
}	echo "<br>";
	echo $query ->num_rows();
	echo $query ->lastStatement();
	echo $query ->mensaje();

	// $query	->table('bitacora_eventos')
	// 		->insert(array("idUsuario" 		=> 1,
	// 						"ip" 			=> "192.168.1.100",
	// 						"pantalla" 		=> "pantallaEjem2",
	// 						"descripcion" 	=> "Descripción ejemplo2",
	// 						"idSucursal" 	=> 1 ), "isssi")
	// 		->execute();
	//
	// echo $query ->mensaje();
	// echo $query ->insert_id();

	// $query	->table("bitacora_eventos")
	// 		->update(array("descripcion" => "Descripcion modiicada"), "s")
	// 		->where("id", "=", 127, "i")
	// 		->limit()
	// 		->execute();
	// $query ->table("tablaEjemplo")->insert(array("numero" => 1, "nombre" =>"alfa", "fechaCreacion" => "2019-03-03"), "iss")->execute();
	$query ->dropTable("tablaEjemplo");
	$crearTabla = $query->createTable("tablaEjemplo", TRUE)
						->bigIncrements("id")
						->int("numero", TRUE)
						->varChar("nombre",100, TRUE)
						->date("fechaCreacion")
						->execute(TRUE);
	if ($crearTabla)
		echo "Tabla creada con éxito";
	else
		echo $query ->mensaje();
	echo $query ->lastStatement();
	echo $query ->mensaje();
	echo "affected rows: ".$query ->affected_rows();

}
