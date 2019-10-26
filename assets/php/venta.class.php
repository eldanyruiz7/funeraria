<?php
require_once "funcionesVarias.php";
class venta
{
    function __construct($idVenta, $mysqli)
    {
        $idVenta = validarFormulario("i",$idVenta,0);
        $sql = "SELECT
                    ventas.id, 						        ventas.fechaCreacion,
                    ventas.idCliente, 				        ventas.idSucursal,
                    ventas.usuario, 				        clientes.nombres,
                    clientes.apellidop,				        clientes.apellidom,
                    clientes.domicilio1,			        clientes.domicilio2,
                    clientes.cp,					        clientes.idEstado,
                    clientes.rfc,					        clientes.tel,
                    clientes.cel,					        cat_sucursales.nombre,
                    cat_sucursales.representante, 	        cat_sucursales.direccion1,
                    cat_sucursales.direccion2, 		        cat_sucursales.cp,
                    cat_sucursales.estado,			        cat_sucursales.telefono1,
                    cat_sucursales.telefono2, 		        cat_sucursales.rfc,
                    cat_sucursales.correo,                  cat_usuarios.nombres,
                    cat_usuarios.apellidop,                 cat_usuarios.apellidom,
                    clientes.email,                         ventas.activo,
                    cat_regimenes_fiscales.c_RegimenFiscal, cat_regimenes_fiscales.nombre,
                    ventas.idFactura
                FROM ventas
                INNER JOIN clientes
                ON ventas.idCliente = clientes.id
                INNER JOIN cat_sucursales
                ON ventas.idSucursal = cat_sucursales.id
                INNER JOIN cat_usuarios
                ON ventas.usuario = cat_usuarios.id
                INNER JOIN cat_regimenes_fiscales
                ON cat_sucursales.idRegimenFiscal = cat_regimenes_fiscales.id
                WHERE ventas.id = ? LIMIT 1";
        $prepare = $mysqli->prepare($sql);
        if ($prepare &&
            $prepare->bind_param("i", $idVenta) &&
            $prepare->execute() &&
            $prepare->store_result() &&
            $prepare->bind_result($idVenta, $fechaCreacion, $idCliente, $idSucursal, $idUsuario,
                                $nombreCliente, $apellidopCliente, $apellidomCliente, $domicilio1Cliente,
                                $domicilio2Cliente, $cpCliente, $idEstadoCliente, $rfcCliente, $telCliente,
                                $celCliente, $nombreSucursal, $representanteSucursal, $direccion1Sucursal,
                                $direccion2Sucursal, $cpSucursal, $idEstadoSucursal, $tel1Sucursal, $tel2Sucursal,
                                $rfcSucursal, $emailSucursal, $nombreUsuario, $apellidopUsuario,
                                $apellidomUsuario, $emailCliente, $activo, $c_RegimenFiscal, $regimenFiscal, $idFactura) &&
            $prepare->fetch() &&
            $prepare->num_rows > 0)
            {
            $this ->id                      = $idVenta;
            $this ->fechaCreacion           = $fechaCreacion;
            $this ->idCliente               = $idCliente;
            $this ->idSucursal              = $idSucursal;
            $this ->idUsuario               = $idUsuario;
            $this ->nombreCliente           = $nombreCliente;
            $this ->apellidopCliente        = $apellidopCliente;
            $this ->apellidomCliente        = $apellidomCliente;
            $this ->nombresCliente          = $nombreCliente." ".$apellidopCliente." ".$apellidomCliente;
            $this ->domicilio1Cliente       = $domicilio1Cliente;
            $this ->domicilio2Cliente       = $domicilio2Cliente;
            $this ->cpCliente               = $cpCliente;
            $this ->domicilioCliente        = $domicilio1Cliente.", ".$domicilio2Cliente.". CP: ".$cpCliente;
            $this ->idEstadoCliente         = $idEstadoCliente;
            $this ->rfcCliente              = $rfcCliente;
            $this ->telCliente              = $telCliente;
            $this ->celCliente              = $celCliente;
            $this ->nombreSucursal          = $nombreSucursal;
            $this ->representanteSucursal   = $representanteSucursal;
            $this ->direccionSucursal       = $direccion1Sucursal.", ".$direccion2Sucursal.", CP: ".$cpSucursal;
            $this ->direccion1Sucursal      = $direccion1Sucursal;
            $this ->direccion2Sucursal      = $direccion2Sucursal;
            $this ->cpSucursal              = $cpSucursal;
            $this ->idEstadoSucursal        = $idEstadoSucursal;
            $this ->tel1Sucursal            = $tel1Sucursal;
            $this ->tel2Sucursal            = $tel2Sucursal;
            $this ->rfcSucursal             = $rfcSucursal;
            $this ->emailSucursal           = $emailSucursal;
            $this ->nombreUsuario           = $nombreUsuario;
            $this ->apellidopUsuario        = $apellidopUsuario;
            $this ->apellidomUsuario        = $apellidomUsuario;
            $this ->emailCliente            = $emailCliente;
            $this ->activo                  = $activo;
            $this ->c_RegimenFiscal         = $c_RegimenFiscal;
            $this ->regimenFiscal           = $regimenFiscal;
            $sql = "SELECT precioVenta, cantidad FROM detalle_ventas WHERE idVenta = ".$this->id." AND activo = 1";
            $res_det = $mysqli->query($sql);
            $totalVenta = 0;
            while ($row_det = $res_det->fetch_assoc())
                $totalVenta += $row_det['precioVenta'] * $row_det['cantidad'];
            $this ->totalVenta              = $totalVenta;
            $this ->idFactura               = $idFactura;
        }
        else
        {
            $this->id = 0;
        }
    }
    public function fechaCreacion()
    {
        $fecha = date_format(new DateTime($this->fechaCreacion),"d-m-Y h:i:s a");
        return $fecha;
    }
    public function totalVenta()
    {
        return number_format($this->totalVenta,2,".",",");
    }
} ?>
