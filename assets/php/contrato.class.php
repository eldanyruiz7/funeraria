<?php
require_once "funcionesVarias.php";
require_once "query.class.php";
class contrato
{
    function __construct($idC, $mysqli)
    {
		$this->Query = new Query();
        $idC = validarFormulario("i",$idC);
        $sql = "SELECT
                    contratos.id,                       contratos.fechaCreacion,
                    contratos.fechaPrimerAportacion,    contratos.precio,
                    contratos.primerAnticipo,           contratos.precioAportacion,
                    contratos.direccion1,               contratos.direccion2,
                    contratos.cp,                       contratos.idEstado,
                    contratos.referencias,              contratos.frecuenciaPago,
                    contratos.idTitular,                contratos.idPlan,
                    contratos.idSucursal,               contratos.observaciones,
                    cat_estados.estado,                 clientes.nombres,
                    clientes.apellidop,                 clientes.apellidom,
                    clientes.rfc,                       clientes.tel,
                    clientes.cel,                       cat_planes.nombre,
                    cat_sucursales.nombre,              cat_sucursales.lema,
                    cat_sucursales.direccion1,          cat_sucursales.direccion2,
                    cat_sucursales.telefono1,           cat_sucursales.telefono2,
                    cat_sucursales.celular,             cat_sucursales.clausulasContrato,
                    cat_sucursales.rfc,                 cat_sucursales.curp,
                    cat_sucursales.correo,              cat_sucursales.representante,
                    cat_usuarios.nombres,               cat_usuarios.apellidop,
                    cat_usuarios.apellidom,             contratos.enCurso,
                    contratos.tasaComision,             contratos.idVendedor,
                    contratos.idFallecido,              contratos.folio,
                    contratos.idFactura,                contratos.activo,
                    cat_regimenes_fiscales.c_RegimenFiscal, cat_regimenes_fiscales.nombre,
                    clientes.email,                     cat_sucursales.cp,
                    cat_sucursales.estado,              clientes.cp,
                    clientes.domicilio1,			    clientes.domicilio2,
                    clientes.idEstado,                  contratos.descuentoDuplicacionInversion,
                    contratos.descuentoCambioFuneraria, contratos.descuentoAdicional,
                    contratos.motivoCancelado,			contratos.idNomina
                FROM contratos
                INNER JOIN cat_estados
                ON contratos.idEstado   = cat_estados.id
                INNER JOIN clientes
                ON contratos.idTitular  = clientes.id
                INNER JOIN cat_planes
                ON contratos.idPlan = cat_planes.id
                INNER JOIN cat_sucursales
                ON contratos.idSucursal = cat_sucursales.id
                INNER JOIN cat_usuarios
                ON contratos.usuario    = cat_usuarios.id
                INNER JOIN cat_regimenes_fiscales
                ON cat_sucursales.idRegimenFiscal = cat_regimenes_fiscales.id
                WHERE contratos.id      = ?
                AND contratos.activo    = 1 LIMIT 1";
        $prepare_contrato = $mysqli->prepare($sql);
        if ($prepare_contrato &&
            $prepare_contrato->bind_param("i",$idC) &&
            $prepare_contrato->execute() &&
            $prepare_contrato->store_result() &&
            $prepare_contrato->bind_result($idContrato, $fechaCreacion, $fechaPrimerAportacion, $precio, $anticipo,
                                        $aportacion, $domicilio1, $domicilio2, $cp, $idEstado, $referencias, $frecuenciaPago,
                                        $idCliente, $idPlan, $idSucursal, $observaciones, $nombreEstado, $nombreCliente,
                                        $apellidopCliente, $apellidomCliente, $rfcCliente, $telCliente, $celCliente, $nombrePlan, $nombreSucursal, $lemaSucursal,
                                        $domicilio1Sucursal, $domicilio2Sucursal, $tel1Sucursal, $tel2Sucursal, $celSucursal,
                                        $clausulas, $rfcSucursal, $curpSucursal, $correoSucursal, $representanteSucursal,
                                        $nombreUsuario, $apellidopUsuario, $apellidomUsuario, $enCurso, $tasaComision, $idVendedor,
                                        $idFallecido, $folio, $idFactura, $activo, $c_RegimenFiscal, $regimenFiscal, $emailCliente,
                                        $cpSucursal, $idEstadoSucursal, $cpCliente, $domicilio1Cliente, $domicilio2Cliente, $idEstadoCliente,
                                        $descuentoDuplicacionInversion, $descuentoCambioFuneraria, $descuentoAdicional,
										$motivoCancelado, $idNomina) &&
            $prepare_contrato->fetch() &&
            $prepare_contrato->num_rows > 0)
        {
            $this ->id                      = $idContrato;
            $this ->fechaCreacion           = $fechaCreacion;
            $this ->fechaPrimerAportacion   = $fechaPrimerAportacion;
            $this ->precio                  = $precio;
            $this ->anticipo                = $anticipo;
            $this ->aportacion              = $aportacion;
            $this ->domicilio1              = $domicilio1;
            $this ->domicilio2              = $domicilio2;
            $this ->cp                      = $cp;
            $this ->idEstado                = $idEstado;
            $this ->domicilio               = $domicilio1.", ".$domicilio2.", ".$nombreEstado.", CP: ".$cp;
            $this ->referencias             = $referencias;
            $this ->frecuenciaPago          = $frecuenciaPago;
            $this ->idCliente               = $idCliente;
            $this ->idPlan                  = $idPlan;
            $this ->idSucursal              = $idSucursal;
            $this ->observaciones           = $observaciones;
            $this ->primerNombreCliente     = $nombreCliente;
            $this ->apellidopCliente        = $apellidopCliente;
            $this ->apellidomCliente        = $apellidomCliente;
            $this ->nombreCliente           = $nombreCliente." ".$apellidopCliente." ".$apellidomCliente;
            $this ->rfcCliente              = $rfcCliente;
            $this ->telCliente              = $telCliente;
            $this ->celCliente              = $celCliente;
            $this ->cpCliente               = $cpCliente;
            $this ->domicilioCliente        = $domicilio1Cliente.", ".$domicilio2Cliente.". CP: ".$cpCliente;
            $this ->idEstadoCliente         = $idEstadoCliente;
            $this ->nombrePlan              = $nombrePlan;
            $this ->nombreSucursal          = $nombreSucursal;
            $this ->lemaSucursal            = $lemaSucursal;
            $this ->domicilioSucursal       = $domicilio1Sucursal.", ".$domicilio2Sucursal.", CP: ".$cpSucursal;
            $this ->cpSucursal              = $cpSucursal;
            $this ->domicilio1Sucursal      = $domicilio1Sucursal;
            $this ->domicilio2Sucursal      = $domicilio2Sucursal;
            $this ->tel1Sucursal            = $tel1Sucursal;
            $this ->tel2Sucursal            = $tel2Sucursal;
            $this ->celSucursal             = $celSucursal;
            $this ->clausulas               = $clausulas;
            $this ->rfcSucursal             = $rfcSucursal;
            $this ->curpSucursal            = $curpSucursal;
            $this ->correoSucursal          = $correoSucursal;
            $this ->representanteSucursal   = $representanteSucursal;
            $this ->nombreUsuario           = $nombreUsuario." ".$apellidopUsuario." ".$apellidomUsuario;
            $this ->enCurso                 = $enCurso;
            $this ->tasaComision            = $tasaComision;
            $this ->idVendedor              = $idVendedor;
            $this ->idDifunto               = $idFallecido;
            $this ->folio                   = $folio;
            $this ->idFactura               = $idFactura;
            $this ->activo                  = $activo;
            $this ->c_RegimenFiscal         = $c_RegimenFiscal;
            $this ->regimenFiscal           = $regimenFiscal;
            $this ->emailCliente            = $emailCliente;
            $this ->idEstadoSucursal        = $idEstadoSucursal;
            $this ->descuentoDuplicacionInversion = $descuentoDuplicacionInversion;
            $this ->descuentoCambioFuneraria= $descuentoCambioFuneraria;
            $this ->descuentoAdicional      = $descuentoAdicional;
			$this ->motivoCancelado         = $motivoCancelado;
            $this ->idNomina         		= $idNomina;

            $this ->costoTotal              = $precio - $descuentoDuplicacionInversion - $descuentoCambioFuneraria - $descuentoAdicional;
            $sql = "SELECT nombres, apellidop, apellidom FROM cat_usuarios WHERE id =$idVendedor LIMIT 1";
            $res_vende = $mysqli->query($sql);
            $row_vende = $res_vende->fetch_assoc();
            $this ->nombresVendedor         = $row_vende['nombres'];
            $this ->nombreVendedor          = $row_vende['nombres']." ".$row_vende['apellidop']." ".$row_vende['apellidom'];
            $sql = "SELECT fechaCreacion, monto FROM detalle_pagos_contratos WHERE idContrato =".$this->id." AND activo = 1";
            $res_pagos = $mysqli->query($sql);
            $this ->pagosEfectuados         = $res_pagos->num_rows;
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
    public function comision_vendedor()
    {
        $tasa = $this->tasaComision / 100;
        $comision = $this->costoTotal * $tasa;
        return $comision;
    }
    public function fechaPrimerAportacion()
    {
        $fecha = date_format(new DateTime($this->fechaPrimerAportacion),"d-m-Y");
        return $fecha;
    }
    public function frecuenciaPago($html = FALSE)
    {
        switch ($this->frecuenciaPago)
        {
            case 1:
                $frecuencia = ($html) ? '<span class="label label-success label-white middle">Semanal</span>' : "Semanal";
                break;
            case 2:
                $frecuencia = ($html) ? '<span class="label label-info label-white middle">Quincenal</span>' : "Quincenal";
                break;
            case 3:
                $frecuencia = ($html) ? '<span class="label label-purple label-white middle">Mensual</span>' : "Mensual";
                break;
            default:
                $frecuencia = ($html) ? '<span class="label label-success label-white middle">Semanal</span>' : "SemanaL";
                break;
        }
        return $frecuencia;
    }
    public function estatus_cobranza($mysqli, $html = FALSE)
    {
        if ($this ->motivoCancelado == 0)
        {
            $idContrato = $this->id;
            $sql = "SELECT fechaCreacion FROM detalle_pagos_contratos WHERE idContrato = $idContrato AND activo = 1 ORDER BY id DESC LIMIT 1";
            $res = $mysqli->query($sql);
            if ($res->num_rows == 0)
            {
                $fechaUltimoPago = new DateTime($this->fechaCreacion);
            }
            else
            {
                $row = $res->fetch_assoc();
                $fechaUltimoPago = new DateTime($row['fechaCreacion']);
            }
            $hoy = new DateTime("now");
            $diff = $hoy->diff($fechaUltimoPago);
            $diferenciaDias = $diff->days;
            $atrasado = FALSE;
            switch ($this->frecuenciaPago) {
                case 1:
                    if ($diferenciaDias > 14)
                    {
                        $atrasado = TRUE;
                    }
                    break;
                case 2:
                case 3:
                    if ($diferenciaDias > 30)
                    {
                        $atrasado = TRUE;
                    }
                    break;
                }
                if ($atrasado)
                {
                    $e = '<span class="label label-danger" style="margin-bottom:1px"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Atrasado</span>';
                }
                else
                {
                    $e = '<span class="label label-success" style="margin-bottom:1px"><i class="fa fa-check" aria-hidden="true"></i> Al corriente</span>';
                }
                $e .= '<br>';
                $e .= $this->estatus();
            }else {
                $e = $this->estatus();
                // $e = '';
        }
            return $e;
    }
    public function pagosCalculados()
    {
        $precio                 = validarFormulario('i',$this->precio,0);
        $anticipo               = validarFormulario('i',$this->anticipo,0);
        $aportacion             = validarFormulario('i',$this->aportacion,0);
        if (!$precio || !$anticipo || !$aportacion)
            return 0;
        else
        {
            $saldo              = $this->precio;
            $anticipo           = $this->anticipo;
            $frecuencia         = $this->frecuenciaPago;
            $precio             = $this->precio;
            $pagoNo             = 0;
            $saldo              -= $anticipo;
            while ($saldo > 0)
            {
                $pagoNo++;
                $saldo          -= $aportacion;
                if ($saldo < $aportacion && $saldo > 0)
                    $aportacion = $saldo;
                if ($saldo == 0)
                {
                    return $pagoNo;
                    break;
                }
            }
        }
    }
    public function fechaUltimoPago()
    {
        $precio                 = validarFormulario('i',$this->precio,0);
        $anticipo               = validarFormulario('i',$this->anticipo,0);
        $aportacion             = validarFormulario('i',$this->aportacion,0);
        if (!$precio || !$anticipo || !$aportacion)
            return 0;
        else
        {
            $saldo              = $this->precio;
            $anticipo           = $this->anticipo;
            $frecuencia         = $this->frecuenciaPago;
            $precio             = $this->precio;
            $pagoNo             = 0;
            $fechaModificar     = new DateTime($this->fechaPrimerAportacion);
            $saldo              -= $anticipo;
            while ($saldo > 0)
            {
                $pagoNo++;
                $saldo          -= $aportacion;
                if ($saldo < $aportacion && $saldo > 0)
                    $aportacion = $saldo;
                if ($saldo == 0)
                {
                    $fechaReturn = date_format($fechaModificar,"d-m-Y");
                    return $fechaReturn;
                    break;
                }
                switch ($this->frecuenciaPago)
                {
                    case 1:
                    $fechaModificar->modify('+1 week');
                        break;
                    case 2:
                    $fechaModificar->modify('+2 week');
                        break;
                    case 3:
                    $fechaModificar->modify('+1 month');
                        break;
                    default:
                    $fechaModificar->modify('+1 week');
                        break;
                }
            }
        }
    }
    public function saldo($mysqli)
    {
        $id = $this->id;
        $sql = "SELECT IFNULL(SUM(monto),0) AS totalPagos FROM detalle_pagos_contratos WHERE idContrato = $id AND activo = 1";
        $res = $mysqli->query($sql);
        $row = $res->fetch_assoc();
        $totalAbonado = $this->anticipo + $row['totalPagos'];
        $saldo = $this->costoTotal - $totalAbonado;
        return $saldo;
    }
    public function totalAbonado($mysqli)
    {
        $id = $this->id;
        $sql = "SELECT IFNULL(SUM(monto),0) AS totalPagos FROM detalle_pagos_contratos WHERE idContrato = $id AND activo = 1";
        $res = $mysqli->query($sql);
        $row = $res->fetch_assoc();
        $totalAbonado = $this->anticipo + $row['totalPagos'];
        return $totalAbonado;
    }

	/**
	 * [arrayPagosRecibidos]
	 * @param  [string] $fechaInicio [fecha en formato string (Y-m-d)]
	 * @param  [string] $fechaFinal    [fecha en formato string (Y-m-d)]
	 * @return [array]  $aray      [Array con el total de pagos o false si no hay registros]
	 */
	public function arrayPagosRecibidos($fechaInicio, $fechaFinal)
	{
		// global $query;
		$id = $this->id;
		$row = $this->Query ->table("detalle_pagos_contratos AS dpc")
							->select("dpc.monto AS monto,
								dpc.usuario_cobro AS idUsuarioCobro,
								dpc.usuario_registro AS idUsuarioRegistro,
								cuc.nombres AS nombreUsuarioCobro,
								cuc.apellidop AS apellidopUsuarioCobro,
								cuc.apellidom AS apellidomUsuarioCobro,
								cur.nombres AS nombreUsuarioRegistro,
								cur.apellidop AS apellidopUsuarioRegistro,
								cur.apellidom AS apellidomUsuarioRegistro,
								cli.nombres AS nombreTitular,
								cli.apellidop AS apellidopTitular,
								cli.apellidom AS apellidomTitular,
								con.folio AS folioContrato")
							->leftJoin("cat_usuarios AS cuc", "cuc.id", "=", "dpc.usuario_cobro")
							->leftJoin("cat_usuarios AS cur", "cur.id", "=", "dpc.usuario_registro")
							->leftJoin("contratos AS con", "dpc.idContrato", "=", "con.id")
							->leftJoin("clientes AS cli", "con.idTitular", "=", "cli.id")
							->where("dpc.idContrato", "=", $id, "i")->and()
							->where("dpc.activo", "=", 1, "i")->execute(TRUE);
							echo "---------------".$this ->Query->mensaje(1);
		return $row;
	}
    public function nombreDifunto($mysqli)
    {
        $idDifunto = $this->idDifunto;
        $sql = "SELECT nombres, apellidop, apellidom FROM cat_difuntos WHERE id = $idDifunto LIMIT 1";
        $res = $mysqli->query($sql);
        if ($res->num_rows > 0)
        {
            $row = $res->fetch_assoc();
            $nombreDifunto = $row['nombres']." ".$row['apellidop']." ".$row['apellidom'];
        }
        else
            $nombreDifunto = "";
        return $nombreDifunto;
    }
    public function estatus()
    {
        if ($this ->motivoCancelado){
            $estatus = '<span class="label"><i class="fa fa-ban" aria-hidden="true"></i> Cancelado</span>';
        }
        else {
            switch ($this->enCurso){
                case 1:
                    $estatus = '<span class="label label-info"><i class="fa fa-check-circle-o" aria-hidden="true"></i> En curso</span>';
                    break;

                default:
                    $estatus = '<span class="label label-inverse"><i class="fa fa-check-square" aria-hidden="true"></i> Pagado</span>';
                    break;
            }
        }
        return $estatus;
    }
    public function comentarios_estatus($mysqli, $html = TRUE)
    {
        if ($this ->motivoCancelado == 0)
        {
            $idContrato = $this->id;
            $sql = "SELECT fechaCreacion FROM detalle_pagos_contratos WHERE idContrato = $idContrato AND activo = 1 ORDER BY id DESC LIMIT 1";
            $res = $mysqli->query($sql);
            if ($res->num_rows == 0)
            {
                $fechaUltimoPago = new DateTime($this->fechaCreacion);
            }
            else
            {
                $row = $res->fetch_assoc();
                $fechaUltimoPago = new DateTime($row['fechaCreacion']);
            }
            $hoy = new DateTime("now");
            $diff = $hoy->diff($fechaUltimoPago);
            $diferenciaDias = $diff->days;
            $atrasado = FALSE;
            switch ($this->frecuenciaPago) {
                case 1:
                    if ($diferenciaDias > 14)
                    {
                        if ($html)
                            $comentario = "Este contrato presenta un atraso de <b>$diferenciaDias</b> días en total, por lo que su estado se encuentra como ATRASADO";
                        else
                            $comentario = "Este contrato presenta un atraso de $diferenciaDias días en total, por lo que su estado se encuentra como ATRASADO";
                    }
                    else {
                        $comentario = "Este contrato se encuentra al corriente";
                    }
                    break;
                case 2:
                case 3:
                    if ($diferenciaDias > 30)
                    {
                        if ($html)
                            $comentario = "Este contrato presenta un atraso de <b>$diferenciaDias</b> días en total, por lo que su estado se encuentra como ATRASADO";
                        else
                            $comentario = "Este contrato presenta un atraso de $diferenciaDias días en total, por lo que su estado se encuentra como ATRASADO";
                    }
                    else {
                        $comentario = "Este contrato se encuentra al corriente";
                    }
                    break;
                }
            }else {
                switch ($this ->motivoCancelado) {
                    case 1:
                        $motivo = "Problemas económicos";
                        break;
                    case 2:
                        $motivo = "Desempleo";
                        break;
                    case 3:
                        $motivo = "Cliente no localizable";
                        break;
                    case 4:
                        $motivo = "Pérdida de interés por parte del cliente";
                        break;
                    case 5:
                        $motivo = "Transferencia de contrato";
                        break;
                    default:
                    $motivo = "Problemas económicos";
                        break;
                }
                    if ($html)
                        $comentario = "Este contrato se encuentra cancelado por: <br><b>$motivo</b>";
                    else
                        $comentario = "Este contrato se encuentra cancelado por: $motivo";
            }
            return $comentario;
    }
} ?>
