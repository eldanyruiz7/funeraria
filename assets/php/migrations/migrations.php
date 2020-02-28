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
	echo $query->backup();
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
	//
	//
	//
	//
	//
	//
	//
	//
	//
	//
	//
	//
	//
	//
	//
	//
	//
	//
	//
	//
	//
	//
	//
	//
	//
	//
	//
	//////////////////////////////////////////////// cat_departamentos /////////////////////////////////////////////////////////
		// $query 	->dropTable("cat_departamentos");
		// $query ->createTable("cat_departamentos", TRUE)
		// 		->intIncrements("id")
		// 		->varChar("nombre",50)
		// 		->int("idUsuario")
		// 		->int("activo", FALSE, '1')
		// 		->execute();
		// 		echo $query ->mensaje()."</br>";
		//
		// $query ->table("cat_departamentos")->insert(array("nombre" 	=> "Administrativo",
		// 												"idUsuario" => 1), "si")->execute();
		// 												echo $query ->mensaje()."</br>";
		//
		// $query ->table("cat_departamentos")->insert(array("nombre" 	=> "Ventas",
		// 												"idUsuario" => 1), "si")->execute();
		// 												echo $query ->mensaje()."</br>";
		//
		// $query ->table("cat_departamentos")->insert(array("nombre" 	=> "Cobranza",
		// 												"idUsuario" => 1), "si")->execute();
		// 												echo $query ->mensaje()."</br>";

/**
 * MÓDULO DE NÓMINAS
 */

//////////////////////////////////////////////// tipos_periodos_nominas /////////////////////////////////////////////////////////
// 			$query 	->dropTable("tipos_periodos_nominas", 0);
// 			$query ->createTable("tipos_periodos_nominas", TRUE)
// 					->intIncrements("id")
// 					->varChar("nombre",50)
// 					// ->int("cuantosDias")
// 					->int("idUsuario")
// 					->int("activo", FALSE, '1')
// 					->execute();
// 					echo $query ->mensaje()."</br>";
//
// 			$query ->table("tipos_periodos_nominas")->insert(array( "nombre" 	=> "Semanal",
// 																	"idUsuario" => 1), "si")->execute();
// 																	echo $query ->mensaje()."</br>";
//
// 			$query ->table("tipos_periodos_nominas")->insert(array( "nombre" 	=> "Quincenal",
// 																	"idUsuario" => 1), "si")->execute();
// 																	echo $query ->mensaje()."</br>";
//
// 			$query ->table("tipos_periodos_nominas")->insert(array( "nombre" 	=> "Mensual",
// 																	"idUsuario" => 1), "si")->execute();
// 																	echo $query ->mensaje()."</br>";
//
// 		//////////////////////////////////////////////// periodos_nomina /////////////////////////////////////////////////////////
// 			$query 	->dropTable("periodos_nomina", 0);
// 			$query ->createTable("periodos_nomina", TRUE)
// 					->intIncrements("id")
// 					->varChar("nombre",50)
// 					->int("idUsuario")
// 					->int("activo", FALSE, '1')
// 					->execute();
// 					echo $query ->mensaje()."</br>";
//
// 			$query ->table("periodos_nomina")->insert(array("nombre" 	=> "Semanal",
// 															"idUsuario" => 1), "si")->execute();
// 															echo $query ->mensaje()."</br>";
//
// 			$query ->table("periodos_nomina")->insert(array("nombre" 	=> "Quincenal",
// 															"idUsuario" => 1), "si")->execute();
// 															echo $query ->mensaje()."</br>";
//
// 			$query ->table("periodos_nomina")->insert(array("nombre" 	=> "Mensual",
// 															"idUsuario" => 1), "si")->execute();
// 															echo $query ->mensaje()."</br>";
//
//
// /////////////////////////////////////////////////// cat_nominas /////////////////////////////////////////////////////////
// 			$query 	->dropTable("cat_periodos_nominas",0);
// 			$query 	->createTable("cat_periodos_nominas", TRUE)
// 					->bigIncrements("id")
// 					->int("tipoPeriodo", FALSE, "1")
// 					->date("fechaInicio")
// 					->date("fechaFin")
// 					->dateTimeCurrent("fechaCreacion")
// 					->int("idUsuarioCreo")
// 					->int("idSucursal")
// 					->int("activo", FALSE, '1')
// 					->foreignKey("fk_tipo_periodo_nomina", "tipoPeriodo", "periodos_nomina", "id")
// 					->execute();
// 					echo $query ->mensaje()."</br>";
// 			// $query ->table("cat_periodos_nominas")->insert(array("fechaInicio" => "2019-11-14", "fechaFin" => "2019-11-14", "idUsuarioCreo" => 1, "idSucursal" => 1 ), "ssii")->execute();
// 			// $query ->table("cat_periodos_nominas")->insert(array("fechaInicio" => "2019-11-15", "fechaFin" => "2019-11-15", "idUsuarioCreo" => 1, "idSucursal" => 1 ), "ssii")->execute();
// 			// $query ->table("cat_periodos_nominas")->insert(array("fechaInicio" => "2019-11-16", "fechaFin" => "2019-11-16", "idUsuarioCreo" => 1, "idSucursal" => 1 ), "ssii")->execute();
//
//
// 			$query ->dropTable("cat_nominas", 0);
// 			$query ->createTable("cat_nominas", TRUE)
// 					->bigIncrements("id")
// 					->bigInt("idPeriodo")
// 					->int("idUsuario")
// 					->int("activo", FALSE, '1')
// 					->foreignKey("fk_id_periodo_nomina", "idPeriodo", "cat_periodos_nominas", "id")
// 					->execute();
// 					echo $query ->mensaje()."</br>";
//
//
// // //////////////////////////////////////////////// cat_conceptos_nominas /////////////////////////////////////////////////////////
// 			$query ->dropTable("cat_conceptos_nominas", 0);
// 			$query ->createTable("cat_conceptos_nominas", TRUE)
// 					->bigIncrements("id")
// 					->varChar("nombreConcepto", 50)
// 					->int("tipo", FALSE, "1") //1 = Percepcion 2 = Deducción
// 					->dateTimeCurrent("fechaCreacion")
// 					->int("idUsuario")
// 					->int("idSucursal")
// 					->int("activo", FALSE, '1')
// 					->execute();
// 					echo $query ->mensaje()."</br>";
//
// 			$query ->table("cat_conceptos_nominas")->insert(array("nombreConcepto" => "Comisión ventas",
// 																 "idUsuario" => 1,
// 															 	 "idSucursal" => 1), "sii")->execute();
// 																 echo $query ->mensaje()."</br>";
// 			$query ->table("cat_conceptos_nominas")->insert(array("nombreConcepto" => "Comisión cobranza",
// 		 														 "idUsuario" => 1,
// 		 													 	 "idSucursal" => 1), "sii")->execute();
// 																 echo $query ->mensaje()."</br>";
// 			$query ->table("cat_conceptos_nominas")->insert(array("nombreConcepto" => "Otros",
// 																 "idUsuario" => 1,
// 															 	 "idSucursal" => 1), "sii")->execute();
// 																 echo $query ->mensaje()."</br>";
//
// /////////////////////////////////////////// detalle_nomina ////////////////////////////////////////////////////
// 			$query 	->dropTable("tipos_detalle_nomina", 0); //Percepción, deducción
// 			$query	->createTable("tipos_detalle_nomina", TRUE)
// 					->intIncrements("id")
// 					->varChar("nombre", 20)
// 					->int("activo", FALSE, '1')
// 					->execute();
// 					echo $query ->mensaje()."</br>";
//
// 			$query ->table("tipos_detalle_nomina")->insert(array("nombre" => "Percepción"), "s")->execute();
// 			$query ->table("tipos_detalle_nomina")->insert(array("nombre" => "Deducción"), "s")->execute();
//
// 			$query	->dropTable("detalle_nomina", 0);
// 			$query 	->createTable("detalle_nomina", TRUE)
// 					->bigIncrements("id")
// 					->bigInt("idNomina")
// 					->bigInt("idConcepto")
// 					->int("tipo", FALSE, "1")
// 					->varChar("nombreConcepto", 100)
// 					->int("cantidad")
// 					->decimal("monto")
// 					->dateTimeCurrent("fechaCreacion")
// 					->int("idUsuario")
// 					->int("idSucursal")
// 					->int("pagado", FALSE, '0')
// 					->int("activo", FALSE, '1')
// 					->foreignKey("fk_id_nomina", "idNomina", "cat_nominas", "id")
// 					->foreignKey("fk_id_cat_concepto_nomina", "idConcepto", "cat_conceptos_nominas", "id")
// 					->foreignKey("fk_tipo_detalle_nomina", "tipo", "tipos_detalle_nomina", "id")
// 					->execute();
// 					echo $query ->mensaje()."</br>";
//
//
// 	// Limpiar nomina //
// 	$query->table("detalle_pagos_contratos")->update(array("idNominaVenta" => 0), "i")->execute();
// 	$query->table("detalle_pagos_contratos")->update(array("idNominaCobranza" => 0), "i")->execute();
// 	$query->table("contratos")->update(array("idNomina" => 0), "i")->execute();
//
// // //////////////////////////////////////////////// tipos_usuarios /////////////////////////////////////////////////////////
// 	$query 	->dropTable("tipos_usuarios");
// 	$query ->createTable("tipos_usuarios", TRUE)
// 			->intIncrements("id")
// 			->varChar("nombre",50)
// 			->int("idUsuario")
// 			->int("activo", FALSE, '1')
// 			->execute();
// 			echo $query ->mensaje()."</br>";
//
// 	$query ->table("tipos_usuarios")->insert(array("nombre" 	=> "Administrador",
// 													"idUsuario" => 1), "si")->execute();
// 													echo $query ->mensaje()."</br>";
//
// 	$query ->table("tipos_usuarios")->insert(array("nombre" 	=> "Secretario",
// 													"idUsuario" => 1), "si")->execute();
// 													echo $query ->mensaje()."</br>";
//
// 	$query ->table("tipos_usuarios")->insert(array("nombre" 	=> "Vendedor",
// 													"idUsuario" => 1), "si")->execute();
// 													echo $query ->mensaje()."</br>";
//
// 	$query ->table("tipos_usuarios")->insert(array("nombre" 	=> "Cobrador",
// 													"idUsuario" => 1), "si")->execute();
// 													echo $query ->mensaje()."</br>";
//
//
//
//
//
// /**
//  * CATÁLOGO FRECUENCIAS DE PAGO
//  *
//  */
//
// // $query	->dropTable("cat_frecuencias_pago", 0);
// // $query 	->createTable("cat_frecuencias_pago", TRUE)
// // 		->bigIncrements("id")
// // 		->varChar("nombre", 20)
// // 		->varChar("clase", 200)
// // 		->int("idUsuario")
// // 		->int("idSucursal")
// // 		->int("activo", FALSE, '1')
// // 		->execute();
// // 		echo $query ->mensaje()."</br>";
// // $query ->table("cat_frecuencias_pago")->insert(array("nombre" => "Semanal",
// // 													 "clase" => '<span class="label label-success label-white middle">Semanal</span>',
// // 													 "idUsuario" => 1,
// // 												 	 "idSucursal" => 1), "ssii")->execute();
// // 													 echo $query ->mensaje()."</br>";
// // $query ->table("cat_frecuencias_pago")->insert(array("nombre" => "Quincenal",
// // 													 "clase" => '<span class="label label-info label-white middle">Quincenal</span>',
// // 													 "idUsuario" => 1,
// // 												 	 "idSucursal" => 1), "ssii")->execute();
// // 													 echo $query ->mensaje()."</br>";
// // $query ->table("cat_frecuencias_pago")->insert(array("nombre" => "Mensual",
// // 													 "clase" => '<span class="label label-purple label-white middle">Mensual</span>',
// // 													 "idUsuario" => 1,
// // 												 	 "idSucursal" => 1), "ssii")->execute();
// // 													 echo $query ->mensaje()."</br>";
//
//
//
//
													 }
