<?php
require_once ('../../connect/bd.php');
require_once ("../../connect/sesion.class.php");
$sesion = new sesion();
require_once ("../../connect/cerrarOtrasSesiones.php");
require_once ("../../connect/usuarioLogeado.php");
require_once ("../../php/funcionesVarias.php");
if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
{
	header("Location: ../../../salir.php");
}
else
{
	function pa($arr)
	{
		echo $arr;
	}
	require ("../usuario.class.php");
	require ("../query.class.php");
	$usuario 	= new usuario($idUsuario,$mysqli);
	$query 		= new Query();

// 	$query ->dropTable("bitacora_eventos");
// 	$query 	->createTable("bitacora_eventos", TRUE)
// 			->bigIncrements("id")
// 			->int("idUsuario")
// 			->dateTimeCurrent("fecha")
// 			->varChar("ip",30)
// 			->varChar("pantalla",100)
// 			->varChar("descripcion",500)
// 			->int("idSucursal")
// 			->execute();
// 	echo $query ->mensaje()."</br>";
//
// 	$query ->dropTable("cat_causasdecesos");
// 	$query 	->createTable("cat_causasdecesos", TRUE)
// 			->intIncrements("id")
// 			->varChar("nombre",100)
// 			->dateTimeCurrent("fechaCreacion")
// 			->int("usuario")
// 			->int("activo", FALSE, '1')
// 			->execute();
// 			echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_causasdecesos")
// 			->insert(array( "nombre" => "Enfisema pulmonar",
// 					 		"usuario" => 1), "si")
// 			->execute();
// 			echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_causasdecesos")
// 			->insert(array( "nombre" => "Paro respiratorio",
// 					 		"usuario" => 1), "si")
// 			->execute();
// 			echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_causasdecesos")
// 			->insert(array( "nombre" => "Paro cardiaco",
// 					 		"usuario" => 1), "si")
// 			->execute();
// 			echo $query ->mensaje()."</br>";
//
// 	// CAT DIFUNTOS
//
// 	// CAT ESTADOS
//
// 	$query ->dropTable("cat_formas_pago");
// 	$query 	->createTable("cat_formas_pago", TRUE)
// 			->intIncrements("id")
// 			->varChar("c_FormaPago",5)
// 			->varChar("nombre",50)
// 			->int("bancarizado")
// 			->int("activo", FALSE, '1')
// 			->execute();
// 			echo $query ->mensaje()."</br>";
//
// 	$query 	->table("cat_formas_pago")
// 			->insert(array( "c_FormaPago" 	=> "01",
// 							"nombre" 		=> "Efectivo",
// 							"bancarizado" 	=> 0), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_formas_pago")
// 			->insert(array( "c_FormaPago" 	=> "02",
// 							"nombre" 		=> "Cheque",
// 							"bancarizado" 	=> 0), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_formas_pago")
// 			->insert(array( "c_FormaPago" 	=> "03",
// 							"nombre" 		=> "Transferencia",
// 							"bancarizado" 	=> 1), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_formas_pago")
// 			->insert(array( "c_FormaPago" 	=> "04",
// 							"nombre" 		=> "Tarjeta de Crédito",
// 							"bancarizado" 	=> 1), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_formas_pago")
// 			->insert(array( "c_FormaPago" 	=> "05",
// 							"nombre" 		=> "Monedero electrónico",
// 							"bancarizado" 	=> 1), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_formas_pago")
// 			->insert(array( "c_FormaPago" 	=> "06",
// 							"nombre" 		=> "Dinero electrónico",
// 							"bancarizado" 	=> 1), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_formas_pago")
// 			->insert(array( "c_FormaPago" 	=> "08",
// 							"nombre" 		=> "Vales de despensa",
// 							"bancarizado" 	=> 0), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_formas_pago")
// 			->insert(array( "c_FormaPago" 	=> "12",
// 							"nombre" 		=> "Dación en pago",
// 							"bancarizado" 	=> 0), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_formas_pago")
// 			->insert(array( "c_FormaPago" 	=> "13",
// 							"nombre" 		=> "Pago por subrogación",
// 							"bancarizado" 	=> 0), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_formas_pago")
// 			->insert(array( "c_FormaPago" 	=> "14",
// 							"nombre" 		=> "Pago por consignación",
// 							"bancarizado" 	=> 0), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_formas_pago")
// 			->insert(array( "c_FormaPago" 	=> "15",
// 							"nombre" 		=> "Condonación",
// 							"bancarizado" 	=> 0), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_formas_pago")
// 			->insert(array( "c_FormaPago" 	=> "17",
// 							"nombre" 		=> "Compensación",
// 							"bancarizado" 	=> 0), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_formas_pago")
// 			->insert(array( "c_FormaPago" 	=> "23",
// 							"nombre" 		=> "Novación",
// 							"bancarizado" 	=> 0), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_formas_pago")
// 			->insert(array( "c_FormaPago" 	=> "24",
// 							"nombre" 		=> "Confusión",
// 							"bancarizado" 	=> 0), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_formas_pago")
// 			->insert(array( "c_FormaPago" 	=> "25",
// 							"nombre" 		=> "Remisión de deuda",
// 							"bancarizado" 	=> 0), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_formas_pago")
// 			->insert(array( "c_FormaPago" 	=> "26",
// 							"nombre" 		=> "Prescripción o caducidad",
// 							"bancarizado" 	=> 0), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_formas_pago")
// 			->insert(array( "c_FormaPago" 	=> "27",
// 							"nombre" 		=> "A satisfacción del acreedor",
// 							"bancarizado" 	=> 0), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_formas_pago")
// 			->insert(array( "c_FormaPago" 	=> "28",
// 							"nombre" 		=> "Tarjeta de débito",
// 							"bancarizado" 	=> 1), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_formas_pago")
// 			->insert(array( "c_FormaPago" 	=> "29",
// 							"nombre" 		=> "Tarjeta de servicios",
// 							"bancarizado" 	=> 1), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_formas_pago")
// 			->insert(array( "c_FormaPago" 	=> "30",
// 							"nombre" 		=> "Aplicación de anticipos",
// 							"bancarizado" 	=> 0), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_formas_pago")
// 			->insert(array( "c_FormaPago" 	=> "99",
// 							"nombre" 		=> "Por definir",
// 							"bancarizado" 	=> 0), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
//
// /// CATÁLOGO LUGARES DE DEFUNCIÓN ///
//
// 	$query ->dropTable("cat_lugares_defuncion");
// 	$query 	->createTable("cat_lugares_defuncion", TRUE)
// 			->intIncrements("id")
// 			->varChar("nombre",30)
// 			->varChar("domicilio",300)
// 			->dateTimeCurrent("fechaCreacion")
// 			->int("usuario")
// 			->int("activo", FALSE, '1')
// 			->execute();
// 			echo $query ->mensaje()."</br>";
//
// 	$query 	->table("cat_lugares_defuncion")
// 			->insert(array( "nombre" 		=> "Clínica 10 Manzanillo",
// 							"domicilio" 	=> "Eulogia Serratos 6, Hospital IMSS, Manzanillo, Col.",
// 							"usuario" 		=> 1), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_lugares_defuncion")
// 			->insert(array( "nombre" 		=> "Hospital civil Manzanillo",
// 							"domicilio" 	=> "Av Elías Zamora Verduzco S/N, Nuevo Salahua, 28869 Manzanillo, Col.",
// 							"usuario" 		=> 1), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
// 	$query 	->table("cat_lugares_defuncion")
// 			->insert(array( "nombre" 		=> "Hospital Echauri Manzanillo",
// 							"domicilio" 	=> "Blvd. Miguel de la Madrid 1215, Playa Azul Salagua, 28869 Manzanillo, Col.",
// 							"usuario" 		=> 1), "ssi")->execute();
// 							echo $query ->mensaje()."</br>";
//
// 	/// CATÁLOGO MÉTODOS DE PAGO ///
//
// 	$query ->dropTable("cat_metodos_pago");
// 	$query 	->createTable("cat_metodos_pago", TRUE)
// 			->intIncrements("id")
// 			->varChar("c_MetodoPago",10)
// 			->varChar("nombre",100)
// 			->int("activo", FALSE, '1')
// 			->execute();
// 			echo $query ->mensaje()."</br>";
//
// 	$query 	->table("cat_metodos_pago")
// 			->insert(array( "c_MetodoPago" 	=> "PUE",
// 							"nombre" 	=> "Pago en una sola exhibición"), "ss")->execute();
// 							echo $query ->mensaje()."</br>";
//
// 	/// CATÁLOGO MOTIVOS DE CANCELACIÓN ///
//
// 	$query ->dropTable("cat_motivosCancelacion");
// 	$query 	->createTable("cat_motivosCancelacion", TRUE)
// 			->intIncrements("id")
// 			->varChar("nombre",50)
// 			->dateTimeCurrent("fechaCreacion")
// 			->int("idUsuario")
// 			->int("activo", FALSE, '1')
// 			->execute();
// 			echo $query ->mensaje()."</br>";


	// $query 	->table("cat_motivosCancelacion")
	// 		->insert(array( "nombre" 	=> "Problemas económicos",
	// 						"idUsuario" 	=> 1), "si")->execute();
	// 						echo $query ->mensaje()."</br>";
	// $query 	->table("cat_motivosCancelacion")
	// 		->insert(array( "nombre" 	=> "Desempleo",
	// 						"idUsuario" 	=> 1), "si")->execute();
	// 						echo $query ->mensaje()."</br>";
	// $query 	->table("cat_motivosCancelacion")
	// 		->insert(array( "nombre" 	=> "No localizable",
	// 						"idUsuario" 	=> 1), "si")->execute();
	// 						echo $query ->mensaje()."</br>";
	// $query 	->table("cat_motivosCancelacion")
	// 		->insert(array( "nombre" 	=> "Pérdida de interés",
	// 						"idUsuario" 	=> 1), "si")->execute();
	// 						echo $query ->mensaje()."</br>";
	// $query 	->table("cat_motivosCancelacion")
	// 		->insert(array( "nombre" 	=> "Transferencia de contrato",
	// 						"idUsuario" 	=> 1), "si")->execute();
	// 						echo $query ->mensaje()."</br>";

//////////////////////////////////////////////// tipos_usuarios /////////////////////////////////////////////////////////
	// $query 	->dropTable("tipos_usuarios");
	// $query ->createTable("tipos_usuarios", TRUE)
	// 		->intIncrements("id")
	// 		->varChar("nombre",50)
	// 		->int("idUsuario")
	// 		->int("activo", FALSE, '1')
	// 		->execute();
	// 		echo $query ->mensaje()."</br>";
	//
	// $query ->table("tipos_usuarios")->insert(array("nombre" 	=> "Administrador",
	// 												"idUsuario" => 1), "si")->execute();
	// 												echo $query ->mensaje()."</br>";
	//
	// $query ->table("tipos_usuarios")->insert(array("nombre" 	=> "Secretario",
	// 												"idUsuario" => 1), "si")->execute();
	// 												echo $query ->mensaje()."</br>";
	//
	// $query ->table("tipos_usuarios")->insert(array("nombre" 	=> "Vendedor",
	// 												"idUsuario" => 1), "si")->execute();
	// 												echo $query ->mensaje()."</br>";
	//
	// $query ->table("tipos_usuarios")->insert(array("nombre" 	=> "Cobrador",
	// 												"idUsuario" => 1), "si")->execute();
	// 												echo $query ->mensaje()."</br>";


	///////////////////////////////////////////// Nómina ////////////////////////////////////////////////////
	// $query ->dropTable("cat_periodos_nominas");
	// $query ->createTable("cat_periodos_nominas", TRUE)
	// 		->bigIncrements("id")
	// 		->date("fechaInicio")
	// 		->date("fechaFin")
	// 		->dateTimeCurrent("fechaCreacion")
	// 		->int("idUsuarioCreo")
	// 		->int("idSucursal")
	// 		->int("activo", FALSE, '1')
	// 		->execute();
	// $query ->table("cat_periodos_nominas")->insert(array("fechaInicio" => "2019-11-14", "fechaFin" => "2019-11-14", "idUsuarioCreo" => 1, "idSucursal" => 1 ), "ssii")->execute();
	// $query ->table("cat_periodos_nominas")->insert(array("fechaInicio" => "2019-11-15", "fechaFin" => "2019-11-15", "idUsuarioCreo" => 1, "idSucursal" => 1 ), "ssii")->execute();
	// $query ->table("cat_periodos_nominas")->insert(array("fechaInicio" => "2019-11-16", "fechaFin" => "2019-11-16", "idUsuarioCreo" => 1, "idSucursal" => 1 ), "ssii")->execute();
	//

	// $query ->dropTable("cat_nominas");
	// $query ->createTable("cat_nominas", TRUE)
	// 		->bigIncrements("id")
	// 		->bigInt("idPeriodo")
	// 		->int("idUsuario")
	// 		->int("activo", FALSE, '1')
	// 		->execute();
	//
	// $query ->dropTable("detalle_nomina");
	// $query ->createTable("detalle_nomina", TRUE)
	// 		->bigIncrements("id")
	// 		->bigInt("idNomina")
	// 		->bigInt("idConcepto")
	// 		->varChar("nombreConcepto", 50)
	// 		->int("cantidad")
	// 		->decimal("monto")
	// 		->dateTimeCurrent("fechaCreacion")
	// 		->int("idUsuario")
	// 		->int("idSucursal")
	// 		->int("activo", FALSE, '1')
	// 		->execute();
	// //
	// $query ->dropTable("cat_conceptos_nomina");
	// $query ->createTable("cat_conceptos_nomina", TRUE)
	// 		->bigIncrements("id")
	// 		->varChar("nombreConcepto", 50)
	// 		->int("tipo", FALSE, "1") //1 = Percepcion 2 = Deducción
	// 		->dateTimeCurrent("fechaCreacion")
	// 		->int("idUsuario")
	// 		->int("idSucursal")
	// 		->int("activo", FALSE, '1')
	// 		->execute();
	//
	// $query ->table("cat_conceptos_nomina")->insert(array("nombreConcepto" => "Comisión ventas",
	// 													 "idUsuario" => 1,
	// 												 	 "idSucursal" => 1), "sii")->execute();
	// $query ->table("cat_conceptos_nomina")->insert(array("nombreConcepto" => "Comisión cobranza",
 	// 													 "idUsuario" => 1,
 	// 												 	 "idSucursal" => 1), "sii")->execute();

}
