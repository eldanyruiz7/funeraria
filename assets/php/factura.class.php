<?php
require_once "funcionesVarias.php";
class factura
{
    function __construct($idFactura, $mysqli)
    {
        $idFactura = validarFormulario("i",$idFactura,0);
        $sql = "SELECT
                    facturas.id,                                    facturas.rfcEmisor,
                    facturas.razonEmisor,                           facturas.regimenEmisor,
                    facturas.domicilioEmisor, 				        facturas.idEstadoEmisor,
                    facturas.cpEmisor,                              facturas.idReceptor,
                    facturas.rfcReceptor,			                facturas.razonReceptor,
                    facturas.regimenReceptor,					    facturas.domicilioReceptor,
                    facturas.cpReceptor,					        facturas.emailReceptor,
                    facturas.timestamp,					            facturas.fechaEmision,
                    facturas.usoCFDI, 	                            facturas.tipoCFDI,
                    facturas.moneda, 		                        facturas.formaPago,
                    facturas.metodoPago,			                facturas.xml,
                    facturas.folioFiscal,                           facturas.noCertificado,
                    facturas.noCertificadoSAT,                      facturas.selloDigitalEmisor,
                    facturas.selloDigitalSAT,                       facturas.cadenaOriginal,
                    facturas.cadenaOriginalCumplimiento,            facturas.codigoQR,
                    facturas.fechaCertificacion,                    facturas.totalIVA,
                    facturas.totalIEPS,                             facturas.subTotal,
                    facturas.total,                                 facturas.descuento,
                    facturas.version,                               facturas.usuario,
                    facturas.idVentaRelacion,                       facturas.idContratoRelacion,
                    facturas.idSucursal,                            cat_estados.estado,
                    cat_usos_cfdi.nombre,                        cat_regimenes_fiscales.nombre,
                    cat_formas_pago.nombre,                         cat_usuarios.nombres,
                    cat_usuarios.apellidop,                         cat_usuarios.apellidom,
                    cat_sucursales.nombre,                          cat_sucursales.direccion2
                FROM facturas
                INNER JOIN cat_estados
                ON facturas.idEstadoEmisor = cat_estados.id
                INNER JOIN cat_usos_cfdi
                ON facturas.usoCFDI = cat_usos_cfdi.c_UsoCFDI
                INNER JOIN cat_regimenes_fiscales
                ON facturas.regimenEmisor = cat_regimenes_fiscales.c_RegimenFiscal
                INNER JOIN cat_formas_pago
                ON facturas.formaPago = cat_formas_pago.c_FormaPago
                INNER JOIN cat_usuarios
                ON facturas.usuario = cat_usuarios.id
                INNER JOIN cat_sucursales
                ON facturas.idSucursal = cat_sucursales.id
                WHERE facturas.id = ? LIMIT 1";
        $prepare = $mysqli->prepare($sql);
        if ($prepare &&
            $prepare->bind_param("i", $idFactura) &&
            $prepare->execute() &&
            $prepare->store_result() &&
            $prepare->bind_result($idFactura,                                    $rfcEmisor,
            $razonEmisor,                           $regimenEmisor,
            $domicilioEmisor, 				        $idEstadoEmisor,
            $cpEmisor,                              $idReceptor,
            $rfcReceptor,			                $razonReceptor,
            $regimenReceptor,					    $domicilioReceptor,
            $cpReceptor,					        $emailReceptor,
            $timestamp,					            $fechaEmision,
            $usoCFDI, 	                            $tipoCFDI,
            $moneda, 		                        $formaPago,
            $metodoPago,			                $xml,
            $folioFiscal,                           $noCertificado,
            $noCertificadoSAT,                      $selloDigitalEmisor,
            $selloDigitalSAT,                       $cadenaOriginal,
            $cadenaOriginalCumplimiento,            $codigoQR,
            $fechaCertificacion,                    $totalIVA,
            $totalIEPS,                             $subTotal,
            $total,                                 $descuento,
            $version,                               $idUsuario,
            $idVentaRelacion,                       $idContratoRelacion,
            $idSucursal,                            $nombreEstadoEmisor,
            $nombreUsoCFDI,                        $nombreRegimenFiscal,
            $nombreFormaPago,                         $nombreUsuario,
            $apellidopUsuario,                         $apellidomUsuario,
            $nombreSucursal,                          $direccion2Sucursal) &&
            $prepare->fetch() &&
            $prepare->num_rows > 0)
            {
                $this->id                   = $idFactura;
                $this->rfcEmisor            = $rfcEmisor;
                $this->razonEmisor          = $razonEmisor;
                $this->regimenEmisor        = $regimenEmisor;
                $this->domicilioEmisor      = $domicilioEmisor;
                $this->idEstadoEmisor       = $idEstadoEmisor;
                $this->cpEmisor             = $cpEmisor;
                $this->idReceptor           = $idReceptor;
                $this->rfcReceptor          = $rfcReceptor;
                $this->razonReceptor        = $razonReceptor;
                $this->regimenReceptor      = $regimenReceptor;
                $this->domicilioReceptor    = $domicilioReceptor;
                $this->cpReceptor           = $cpReceptor;
                $this->emailReceptor        = $emailReceptor;
                $this->timestamp            = $timestamp;
                $this->fechaEmision         = $fechaEmision;
                $this->usoCFDI              = $usoCFDI;
                $this->tipoCFDI             = $tipoCFDI;
                $this->moneda               = $moneda;
                $this->formaPago            = $formaPago;
                $this->metodoPago           = $metodoPago;
                $this->xml                  = $xml;
                $this->folioFiscal          = $folioFiscal;
                $this->noCertificado        = $noCertificado;
                $this->noCertificadoSAT     = $noCertificadoSAT;
                $this->selloDigitalEmisor   = $selloDigitalEmisor;
                $this->selloDigitalSAT      = $selloDigitalSAT;
                $this->cadenaOriginal       = $cadenaOriginal;
                $this->cadenaOriginalCumplimiento   = $cadenaOriginalCumplimiento;
                $this->codigoQR             = $codigoQR;
                $this->fechaCertificacion   = $fechaCertificacion;
                $this->totalIVA             = $totalIVA;
                $this->totalIEPS            = $totalIEPS;
                $this->subTotal             = $subTotal;
                $this->total                = $total;
                $this->descuento            = $descuento;
                $this->version              = $version;
                $this->idUsuario            = $idUsuario;
                $this->idVentaRelacion      = $idVentaRelacion;
                $this->idContratoRelacion   = $idContratoRelacion;
                $this->idSucursal           = $idSucursal;
                $this->nombreEstadoEmisor   = $nombreEstadoEmisor;
                $this->nombreUsoCFDI        = $nombreUsoCFDI;
                $this->nombreRegimenFiscal  = $nombreRegimenFiscal;
                $this->nombreFormaPago      = $nombreFormaPago;
                $this->nombreUsuario        = $nombreUsuario;
                $this->apellidopUsuario     = $apellidopUsuario;
                $this->apellidomUsuario     = $apellidomUsuario;
                $this->nombresUsuario       = $this->nombreUsuario." ".$this->apellidopUsuario." ".$this->apellidomUsuario;
                $this->nombreSucursal       = $nombreSucursal;
                $this->direccion2Sucursal   = $direccion2Sucursal;
        }
        else
        {
            $this->id = 0;
        }
    }
    public function fechaCreacion()
    {
        $fecha = date_format(new DateTime($this->timestamp),"d-m-Y h:i:s a");
        return $fecha;
    }
    public function totalIVA()
    {
        return number_format($this->totalIVA,2,".",",");
    }
    public function totalIEPS()
    {
        return number_format($this->totalIEPS,2,".",",");
    }
    public function subTotal()
    {
        return number_format($this->subTotal,2,".",",");
    }
    public function total()
    {
        return number_format($this->total,2,".",",");
    }
    public function detalle($mysqli)
    {
        $idFactura = $this->id;
        $sql = "SELECT * FROM detalle_facturas WHERE idFactura = ? AND activo = 1";
        $prepare = $mysqli->prepare($sql);
        if ($prepare &&
            $prepare->bind_param("i", $idFactura) &&
            $prepare->execute())
            {
                $result = $prepare->get_result();
                while ($row_lista = $result->fetch_assoc())
                {
                    $detalle_return [] = $row_lista;
                }
                return $detalle_return;
            }
    }
    public function linkCodigoQR()
    {
        return "<a class='btn btn-link' href='".$this->codigoQR."' target='_blank'>https://verificacfdi.facturaelectronica.sat.gob.mx/</a>";
    }
} ?>
