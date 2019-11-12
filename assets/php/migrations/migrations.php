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

	$query ->dropTable("bitacora_eventos");
	$query 	->createTable("bitacora_eventos", TRUE)
			->bigIncrements("id")
			->int("idUsuario", FALSE)
			->dateTimeCurrent("fecha", FALSE)
			->varChar("ip",30, FALSE)
			->varChar("pantalla",100, FALSE)
			->varChar("descripcion",500, FALSE)
			->int("idSucursal", FALSE)
			->execute();
	echo $query ->mensaje()."</br>";

	$query ->dropTable("cat_causasdecesos");
	$query 	->createTable("cat_causasdecesos", TRUE)
			->intIncrements("id")
			->varChar("nombre",100, FALSE)
			->dateTimeCurrent("fechaCreacion", FALSE)
			->int("usuario", FALSE)
			->int("activo", FALSE, '1')
			->execute();
			echo $query ->mensaje()."</br>";
	$query 	->table("cat_causasdecesos")
			->insert(array( "nombre" => "Enfisema pulmonar",
					 		"usuario" => 1), "si")
			->execute();
	$query 	->table("cat_causasdecesos")
			->insert(array( "nombre" => "Paro respiratorio",
					 		"usuario" => 1), "si")
			->execute();
	$query 	->table("cat_causasdecesos")
			->insert(array( "nombre" => "Paro cardiaco",
					 		"usuario" => 1), "si")
			->execute();

	// CAT DIFUNTOS

	// CAT ESTADOS

	$query ->dropTable("cat_formas_pago");
	$query 	->createTable("cat_formas_pago", TRUE)
			->intIncrements("id")
			->varChar("c_FormaPago",5, FALSE)
			->varChar("nombre",50, FALSE)
			->int("bancarizado", FALSE)
			->int("activo", FALSE, '1')
			->execute();
			echo $query ->mensaje()."</br>";

	$query 	->table("cat_formas_pago")
			->insert(array( "c_FormaPago" 	=> "01",
							"nombre" 		=> "Efectivo",
							"bancarizado" 	=> 0), "ssi")->execute();
	$query 	->table("cat_formas_pago")
			->insert(array( "c_FormaPago" 	=> "02",
							"nombre" 		=> "Cheque",
							"bancarizado" 	=> 0), "ssi")->execute();
	$query 	->table("cat_formas_pago")
			->insert(array( "c_FormaPago" 	=> "03",
							"nombre" 		=> "Transferencia",
							"bancarizado" 	=> 1), "ssi")->execute();
	$query 	->table("cat_formas_pago")
			->insert(array( "c_FormaPago" 	=> "04",
							"nombre" 		=> "Tarjeta de Crédito",
							"bancarizado" 	=> 1), "ssi")->execute();
	$query 	->table("cat_formas_pago")
			->insert(array( "c_FormaPago" 	=> "05",
							"nombre" 		=> "Monedero electrónico",
							"bancarizado" 	=> 1), "ssi")->execute();
	$query 	->table("cat_formas_pago")
			->insert(array( "c_FormaPago" 	=> "06",
							"nombre" 		=> "Dinero electrónico",
							"bancarizado" 	=> 1), "ssi")->execute();
	$query 	->table("cat_formas_pago")
			->insert(array( "c_FormaPago" 	=> "08",
							"nombre" 		=> "Vales de despensa",
							"bancarizado" 	=> 0), "ssi")->execute();
	$query 	->table("cat_formas_pago")
			->insert(array( "c_FormaPago" 	=> "12",
							"nombre" 		=> "Dación en pago",
							"bancarizado" 	=> 0), "ssi")->execute();
	$query 	->table("cat_formas_pago")
			->insert(array( "c_FormaPago" 	=> "13",
							"nombre" 		=> "Pago por subrogación",
							"bancarizado" 	=> 0), "ssi")->execute();
	$query 	->table("cat_formas_pago")
			->insert(array( "c_FormaPago" 	=> "14",
							"nombre" 		=> "Pago por consignación",
							"bancarizado" 	=> 0), "ssi")->execute();
	$query 	->table("cat_formas_pago")
			->insert(array( "c_FormaPago" 	=> "15",
							"nombre" 		=> "Condonación",
							"bancarizado" 	=> 0), "ssi")->execute();
	$query 	->table("cat_formas_pago")
			->insert(array( "c_FormaPago" 	=> "17",
							"nombre" 		=> "Compensación",
							"bancarizado" 	=> 0), "ssi")->execute();
	$query 	->table("cat_formas_pago")
			->insert(array( "c_FormaPago" 	=> "23",
							"nombre" 		=> "Novación",
							"bancarizado" 	=> 0), "ssi")->execute();
	$query 	->table("cat_formas_pago")
			->insert(array( "c_FormaPago" 	=> "24",
							"nombre" 		=> "Confusión",
							"bancarizado" 	=> 0), "ssi")->execute();
	$query 	->table("cat_formas_pago")
			->insert(array( "c_FormaPago" 	=> "25",
							"nombre" 		=> "Remisión de deuda",
							"bancarizado" 	=> 0), "ssi")->execute();
	$query 	->table("cat_formas_pago")
			->insert(array( "c_FormaPago" 	=> "26",
							"nombre" 		=> "Prescripción o caducidad",
							"bancarizado" 	=> 0), "ssi")->execute();
	$query 	->table("cat_formas_pago")
			->insert(array( "c_FormaPago" 	=> "27",
							"nombre" 		=> "A satisfacción del acreedor",
							"bancarizado" 	=> 0), "ssi")->execute();
	$query 	->table("cat_formas_pago")
			->insert(array( "c_FormaPago" 	=> "28",
							"nombre" 		=> "Tarjeta de débito",
							"bancarizado" 	=> 1), "ssi")->execute();
	$query 	->table("cat_formas_pago")
			->insert(array( "c_FormaPago" 	=> "29",
							"nombre" 		=> "Tarjeta de servicios",
							"bancarizado" 	=> 1), "ssi")->execute();
	$query 	->table("cat_formas_pago")
			->insert(array( "c_FormaPago" 	=> "30",
							"nombre" 		=> "Aplicación de anticipos",
							"bancarizado" 	=> 0), "ssi")->execute();
	$query 	->table("cat_formas_pago")
			->insert(array( "c_FormaPago" 	=> "99",
							"nombre" 		=> "Por definir",
							"bancarizado" 	=> 0), "ssi")->execute();
}
