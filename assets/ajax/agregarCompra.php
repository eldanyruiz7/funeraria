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
	$query 				= new Query();
	$usuario 			= new usuario($idUsuario,$mysqli);
	$response 			= array("status" => 0);
		$permiso = $usuario->permiso("agregarCompra",$mysqli);
		if (!$permiso)
		{
			$response['respuesta'] = "No se pudo guardar este registro. Usuario con permisos insuficientes para realizar esta acción";
			responder($response, $mysqli);
		}

		$idUsuario      = $sesion->get('id');
		$resultSuc 		= $query ->table('cat_usuarios')->select("idSucursal")
							->where("id", "=", $idUsuario, "i")->limit(1)
							->execute();
		$idSucursal     				= $resultSuc[0]['idSucursal'];

		if(!$idProveedor 	= validarFormulario('i', $_POST['proveedor']))
		{
			$response['respuesta'] = "El formato del id del proveedor no es el correcto. Inténtalo nuevamente.<br>Debes elegir un proveedor de la lista. <br> Error: El campo <b>'Proveedor'</b> no puede estar en blanco'";
			responder($response, $mysqli);
		}
		$rowProveedor = $query 	->table("cat_proveedores")
								->select("id") ->where("id", "=", $idProveedor, "i")
								->and() ->where("activo", "=", 1, "i") ->limit(1) ->execute();

		if($query->num_rows() == 0)
		{
			$response['respuesta'] = "Error. No existe el id <b>($idProveedor)</b> de proveedor en la Base de datos. Posiblemente ya fue eliminado. No se guardó nada";
			responder($response, $mysqli);
		}
		if ($query ->status())
		{
			$row_prov 		= $query->data();
			$idProveedor 	= $rowProveedor[0]['id'];
		}
		else
		{
			$response['respuesta'] = "Error en el id del proveedor. ".$query->mensaje().". Inténtalo nuevamente";
			responder($response, $mysqli);
		}
		$arrayProductos             = json_decode($_POST['arrayProductos']);
		if (sizeof($arrayProductos) == 0)
		{
			$response['respuesta']  = "La lista de productos no puede estar vacía. Agrega al menos un producto para poder guardar la compra";
			responder($response, $mysqli);
		}
		$usuario = $idUsuario;
		$query ->autocommit(FALSE);
		$query ->table("compras")->insert(compact("usuario", "idProveedor", "idSucursal"), "iii") ->execute();
		if ($query ->status())
		{
			if ($query ->affected_rows == 0)
			{
				$query ->rollback();
				$response['respuesta']        = "No se modificó nada, no se pudo registrar la compra, inténtalo nuevamente";
				responder($response, $mysqli);
			}
		}
		else
		{
			$query ->rollback();
			$response['respuesta'] 	= "Error al registrar la compra. No se pudo guardar la información. ".$query->mensaje().". Inténtalo nuevamente";
			responder($response, $mysqli);
		}

		$idCompra = $query->insert_id();
		foreach ($arrayProductos as $esteProducto)
		{
			$nombreProducto		=   $esteProducto    ->nombre;
			$codigoProducto		=   $esteProducto    ->codigo;
			if (!$idProducto 	= validarFormulario('i',$esteProducto->id, 0))
			{
				$query->rollback();
				$response['respuesta'] = "El formato del id <b>$nombreProducto</b> no es el correcto. Ocurrió un rollback. <br>No se guardó nada. <br>Por favor vuelve a intentarlo";
				responder($response, $mysqli);
				break;
			}
			if (!$existencias = validarFormulario('i',$esteProducto->cantidad, 0))
			{
				$query->rollback();
				$response['respuesta'] = "El formato del parámetro 'cantidad' del producto: <b>$nombreProducto</b> no es el correcto y no puede ser menor o igual que cero (0). Ocurrió un rollback. <br>No se guardó nada. <br>Por favor vuelve a intentarlo";
				responder($response, $mysqli);
				break;
			}
			if (!$precioCompra = validarFormulario('i',$esteProducto->precio, 0))
			{
				$query->rollback();
				$response['respuesta'] = "El formato del parámetro 'precio' del producto: <b>$nombreProducto</b> no es el correcto y no puede ser menor o igual que cero (0). Ocurrió un rollback. <br>No se guardó nada. <br>Por favor vuelve a intentarlo";
				responder($response, $mysqli);
				break;
			}
			$idServicio 		= 0;
			$cantidad = $existencias;
			$query ->table("detalle_compras")->insert(compact(	"idCompra", "idProducto", "idServicio", "precioCompra",
																"cantidad", "idSucursal", "usuario"), "iiidiii") ->execute();

			if (!$query ->status())
			{
				$query ->rollback();
				$response['respuesta'] = "Error en el detalle de la compra. No se pudo guardar la información. Falló el la preparación de parámetros. Inténtalo nuevamente";
				responder($response, $mysqli);
			}
			$query ->table("cat_productos")->update(compact("precioCompra"),"d")->where("id", "=", $idProducto, "i") ->limit(1) ->execute();

			if (!$query ->status())
			{
				$query ->rollback();
				$response['respuesta'] = "Error al registrar precio del producto. Error en el id de producto: <b>$idProducto</b>. No se pudo guardar la información. Falló la vinculación de parámetros. Inténtalo nuevamente";
				responder($response, $mysqli);
			}

			$rowDetalleExistencias = $query ->table("detalle_existenciasproductos")  ->select("id, existencias")
											->where("idProducto", "=", $idProducto, "i")
											->and()->where("idSucursal", "=", $idSucursal, "i")
											->and()->where("activo", "=", 1, "i")->limit(1)->execute();
			if ($query ->status())
			{
				if ($query->num_rows() == 0)
				{
					$query ->table("detalle_existenciasproductos") ->insert(compact("idProducto", "idSucursal", "existencias", "usuario"), "iiii")->execute();

					if (!$query ->status())
					{
						$query ->rollback();
						$response['respuesta'] = "Error al consultar la existencia del producto. ".$query->mensaje().". Error en el id de producto: <b>$idProducto</b>. Inténtalo nuevamente";
						responder($response, $mysqli);
					}
				}
				else
				{
					$esteExistencias = $rowDetalleExistencias[0]["existencias"];
					$existencias += $esteExistencias;
					$query->table("detalle_existenciasproductos")	->update(compact("existencias", "usuario"), "ii")
																	->where("idSucursal", "=", $idSucursal, "i")->and()->where("idProducto", "=", $idProducto, "i")
																	->limit(1)->execute();
					if (!$query ->status())
					{
						$query ->rollback();
						$response['respuesta'] = "Error al actualizar la existencia del producto. ".$query->mensaje().". Error en el id de producto: <b>$idProducto</b>. Inténtalo nuevamente";
						responder($response, $mysqli);
					}
				}
			}
			else
			{
				$query->rollback();
				$response['respuesta'] = "Error al preparar los parámetros de las existencias.".$query->mensaje().". Inténtalo nuevamente";
				responder($response, $mysqli);
			}
		}

		//////////////////////////////////////////////////
		if($query->commit())
		{
			// Agregar evento en la bitácora de eventos ///////
			$usuario 				= $sesion->get("id");
			$ipUsuario 				= $sesion->get("ip");
			$pantalla				= "Agregar compra";
			$descripcion			= "Se agregó una nueva compra con id=$idCompra al catálogo de compras";
			$sql					= "CALL agregarEvento($usuario, '$ipUsuario', '$pantalla', '$descripcion', $idSucursal);";
			$mysqli					->query($sql);

			$idTicket               = $idCompra;
			$response['status']     = 1;
			$response['respuesta']  = "La compra se ha generado correctamente. </br>No. de compra: <strong>$idTicket</strong>
			</br> <strong><a href='listarCompras.php' class='orange'>Lista de compras</a></strong>
			</br> <strong><a target='_blank' href='assets/pdf/comprobanteCompra.php?idCompra=$idTicket' class='orange'>Imprimir</a></strong>";
			responder($response, $mysqli);
		}
		else
		{
			$query->rollback();
			$response['respuesta']  = "Ocurrió un error. No se pudo guardar. Error en commit. Vuelve a intentarlo";
			responder($response, $mysqli);
		}
	}
