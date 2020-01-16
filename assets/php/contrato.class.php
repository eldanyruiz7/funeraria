<?php
require_once "funcionesVarias.php";
require_once "query.class.php";
class Contrato
{
    function __construct($idC, $query)
    {
		$this->Query = new Query();
        $idC = validarFormulario("i",$idC);
		$Contrato = $query->table("contratos")->select("
			contratos.id						AS id,						contratos.fechaCreacion 				AS fechaCreacion,
			contratos.fechaPrimerAportacion		AS fechaPrimerAportacion,	contratos.precio						AS precio,
			contratos.primerAnticipo			AS primerAnticipo,			contratos.precioAportacion				AS precioAportacion,
			contratos.direccion1				AS direccion1,				contratos.direccion2					AS direccion2,
			contratos.cp						AS cp,						contratos.idEstado						AS idEstado,
			contratos.referencias				AS referencias,				contratos.frecuenciaPago				AS frecuenciaPago,
			contratos.idTitular					AS idTitular,				contratos.idPlan						AS idPlan,
			contratos.idSucursal				AS idSucursal,				contratos.observaciones					AS observaciones,
			contratos.tasaComision				AS tasaComision,			contratos.idVendedor					AS idVendedor,
			contratos.idFallecido				AS idFallecido,				contratos.folio							AS folio,
			contratos.idFactura					AS idFactura,				contratos.activo						AS activo,
			contratos.descuentoCambioFuneraria	AS descuentoCambioFuneraria,contratos.descuentoAdicional			AS descuentoAdicional,
			contratos.motivoCancelado			AS motivoCancelado,			contratos.idNomina						AS idNomina,
			contratos.enCurso					AS enCurso,					contratos.descuentoDuplicacionInversion	AS descuentoDuplicacionInversion,
			cat_estados.estado					AS estado,					clientes.nombres						AS nombreCliente,
			clientes.apellidop					AS apellidopCliente,		clientes.apellidom						AS apellidomCliente,
			clientes.rfc						AS rfcCliente,				clientes.tel							AS telCliente,
			clientes.cel						AS celCliente,				cat_planes.nombre						AS nombrePlan,
			clientes.domicilio1					AS domicilio1Cliente,		clientes.domicilio2						AS domicilio2Cliente,
			clientes.idEstado					AS idEstadoCliente,			clientes.email							AS emailCliente,
			cat_sucursales.nombre				AS nombreSucursal, 			cat_sucursales.lema						AS lemaSucursal,
			cat_sucursales.direccion1			AS direccion1Sucursal,		cat_sucursales.direccion2				AS direccion2Sucursal,
			cat_sucursales.telefono1			AS tel1Sucursal,			cat_sucursales.telefono2				AS tel2Sucursal,
			cat_sucursales.celular				AS celSucursal,				cat_sucursales.clausulasContrato		AS clausulas,
			cat_sucursales.rfc					AS rfcSucursal,				cat_sucursales.curp						AS curpSucursal,
			cat_sucursales.correo				AS correoSucursal,			cat_sucursales.representante			AS representanteSucursal,
			cat_usuarios.nombres				AS nombreUsuario,			cat_usuarios.apellidop					AS apellidopUsuario,
			cat_usuarios.apellidom				AS apellidomUsuario,		cat_regimenes_fiscales.c_RegimenFiscal	AS idRegimenFiscalSucursal,
			cat_regimenes_fiscales.nombre		AS regimenFiscalSucursal,	cat_sucursales.cp						AS cpSucursal,
			cat_sucursales.estado				AS idEstadoSucursal,		clientes.cp								AS cpCliente,
			usr_v.nombres						AS nombreVendedor,			usr_v.apellidop							AS apellidopVendedor,
			usr_v.apellidom						AS apellidomVendedor" )
		->innerJoin("cat_estados", 				"contratos.idEstado", 		"=", "cat_estados.id")
		->innerJoin("clientes", 				"contratos.idTitular", 		"=", "clientes.id")
		->innerJoin("cat_planes", 				"contratos.idPlan", 		"=", "cat_planes.id")
		->innerJoin("cat_sucursales", 			"contratos.idSucursal", 	"=", "cat_sucursales.id")
		->innerJoin("cat_usuarios", 			"contratos.usuario", 		"=", "cat_usuarios.id")
		->innerJoin("cat_regimenes_fiscales", 	"cat_sucursales.idRegimenFiscal","=","cat_regimenes_fiscales.id")
		->innerJoin("cat_usuarios AS usr_v",	"usr_v.id",					"=", "contratos.idVendedor")
		->where("contratos.id", "=", $idC, "i")->and()
		->where("contratos.activo", "=", 1, "i")->limit()->execute(FALSE, OBJ);

		if($query ->num_rows())
		{
			$this ->id                      = $Contrato->id;
			$this ->fechaCreacion           = $Contrato->fechaCreacion;
			$this ->fechaPrimerAportacion   = $Contrato->fechaPrimerAportacion;
			$this ->precio                  = $Contrato->precio;
			$this ->anticipo                = $Contrato->primerAnticipo;
			$this ->aportacion              = $Contrato->precioAportacion;
			$this ->domicilio1              = $Contrato->direccion1;
			$this ->domicilio2              = $Contrato->direccion2;
			$this ->cp                      = $Contrato->cp;
			$this ->idEstado                = $Contrato->idEstado;
			$this ->domicilio               = $Contrato->direccion1.", ".$Contrato->direccion2.", ".$Contrato->estado.", CP: ".$Contrato->cp;
			$this ->referencias             = $Contrato->referencias;
			$this ->frecuenciaPago          = $Contrato->frecuenciaPago;
			$this ->idCliente               = $Contrato->idTitular;
			$this ->idPlan                  = $Contrato->idPlan;
			$this ->idSucursal              = $Contrato->idSucursal;
			$this ->observaciones           = $Contrato->observaciones;
			$this ->primerNombreCliente     = $Contrato->nombreCliente;
			$this ->apellidopCliente        = $Contrato->apellidopCliente;
			$this ->apellidomCliente        = $Contrato->apellidomCliente;
			$this ->nombreCliente           = $Contrato->nombreCliente." ".$Contrato->apellidopCliente." ".$Contrato->apellidomCliente;
			$this ->rfcCliente              = $Contrato->rfcCliente;
			$this ->telCliente              = $Contrato->telCliente;
			$this ->celCliente              = $Contrato->celCliente;
			$this ->cpCliente               = $Contrato->cpCliente;
			$this ->domicilioCliente        = $Contrato->domicilio1Cliente.", ".$Contrato->domicilio2Cliente.". CP: ".$Contrato->cpCliente;
			$this ->idEstadoCliente         = $Contrato->idEstadoCliente;
			$this ->nombrePlan              = $Contrato->nombrePlan;
			$this ->nombreSucursal          = $Contrato->nombreSucursal;
			$this ->lemaSucursal            = $Contrato->lemaSucursal;
			$this ->domicilioSucursal       = $Contrato->direccion1Sucursal.", ".$Contrato->direccion2Sucursal.", CP: ".$Contrato->cpSucursal;
			$this ->cpSucursal              = $Contrato->cpSucursal;
			$this ->direccion1Sucursal      = $Contrato->direccion1Sucursal;
			$this ->direccion2Sucursal      = $Contrato->direccion2Sucursal;
			$this ->tel1Sucursal            = $Contrato->tel1Sucursal;
			$this ->tel2Sucursal            = $Contrato->tel2Sucursal;
			$this ->celSucursal             = $Contrato->celSucursal;
			$this ->clausulas               = $Contrato->clausulas;
			$this ->rfcSucursal             = $Contrato->rfcSucursal;
			$this ->curpSucursal            = $Contrato->curpSucursal;
			$this ->correoSucursal          = $Contrato->correoSucursal;
			$this ->representanteSucursal   = $Contrato->representanteSucursal;
			$this ->nombreUsuario           = $Contrato->nombreUsuario." ".$Contrato->apellidopUsuario." ".$Contrato->apellidomUsuario;
			$this ->enCurso                 = $Contrato->enCurso;
			$this ->tasaComision            = $Contrato->tasaComision;
			$this ->idVendedor              = $Contrato->idVendedor;
			$this ->idDifunto               = $Contrato->idFallecido;
			$this ->folio                   = $Contrato->folio;
			$this ->idFactura               = $Contrato->idFactura;
			$this ->activo                  = $Contrato->activo;
			$this ->c_RegimenFiscal         = $Contrato->idRegimenFiscalSucursal;
			$this ->regimenFiscal           = $Contrato->regimenFiscalSucursal;
			$this ->emailCliente            = $Contrato->emailCliente;
			$this ->idEstadoSucursal        = $Contrato->idEstadoSucursal;
			$this ->descuentoDuplicacionInversion = $Contrato->descuentoDuplicacionInversion;
			$this ->descuentoCambioFuneraria= $Contrato->descuentoCambioFuneraria;
			$this ->descuentoAdicional      = $Contrato->descuentoAdicional;
			$this ->motivoCancelado         = $Contrato->motivoCancelado;
			$this ->idNomina         		= $Contrato->idNomina;

			$this ->costoTotal              = $Contrato->precio - $Contrato->descuentoDuplicacionInversion - $Contrato->descuentoCambioFuneraria - $Contrato->descuentoAdicional;
			$this ->nombresVendedor         = $Contrato->nombreVendedor;
			$this ->nombreVendedor          = $Contrato->nombreVendedor." ".$Contrato->apellidopVendedor." ".$Contrato->apellidomVendedor;

			$query->table("detalle_pagos_contratos")->select()->where("idContrato", "=", $this->id, "i")->and()->where("activo", "=", 1, "i")->execute();
			$this ->pagosEfectuados         = $query->num_rows();
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
	public function total_pagado_vendedor($query)
	{
		$totalAnticipo = $this->idNomina ? $this ->anticipo : 0;
		$res =$query->table("detalle_pagos_contratos AS dpc")->select("dpc.monto, dpc.tasaComisionCobranza")
					->innerJoin("contratos AS con","dpc.idContrato", "=", "con.id")
					->where("con.idVendedor", "=", $this ->idVendedor, "i")->and()
					->where("dpc.idNominaVenta", ">", 0, "i")->execute();
		$totalPagado = 0;
		$totalCobranza = 0;
		foreach ($res as $row)
		{
			$factorTasa = $row['tasaComisionCobranza'] / 100;
			$montoCobranza = $row['monto'] * $factorTasa;
			$totalCobranza += $row['monto'] - $montoCobranza;
		}
		$totalPagado = $totalAnticipo + $totalCobranza;
		if ($totalPagado > $this->comision_vendedor())
		{
			$totalPagado = $this->comision_vendedor();
		}
		return $totalPagado;
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
