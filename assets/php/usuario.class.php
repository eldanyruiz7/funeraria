<?php
require_once "funcionesVarias.php";
class usuario
{
    function __construct($idUsuario, $mysqli)
    {
        $idUsuario = validarFormulario("i",$idUsuario);
        $sql = "SELECT
                    cat_usuarios.id,            cat_usuarios.nombres,
                    cat_usuarios.apellidop,     cat_usuarios.apellidom,
                    cat_usuarios.direccion1,    cat_usuarios.direccion2,
                    cat_usuarios.estado,        cat_usuarios.nickName,
                    cat_usuarios.telefono,      cat_usuarios.celular,
                    cat_usuarios.email,         cat_usuarios.tipo,
                    cat_usuarios.tasaComision,  cat_usuarios.fechaCreacion,
                    cat_usuarios.idSucursal,    cat_usuarios.usuario,
                    cat_estados.estado,			cat_usuarios.tasaComisionCobranza,
					tu.nombre,					cd.nombre,
					cat_usuarios.departamento
                FROM cat_usuarios
                INNER JOIN cat_estados
                ON cat_usuarios.estado      = cat_estados.id
				LEFT JOIN tipos_usuarios AS tu
				ON tu.id = cat_usuarios.tipo
				LEFT JOIN cat_departamentos AS cd
				ON cd.id = cat_usuarios.departamento
                WHERE cat_usuarios.id       = ?
                AND cat_usuarios.activo     = 1
                LIMIT 1";
        $prepare_usr = $mysqli->prepare($sql);
        if ($prepare_usr &&
            $prepare_usr->bind_param("i",$idUsuario) &&
            $prepare_usr->execute() &&
            $prepare_usr->store_result() &&
            $prepare_usr->bind_result($idUsuario, $nombre, $apellidop, $apellidom, $direccion1, $direccion2, $idEstado,
                                        $nickName, $telefono, $celular, $email, $tipo, $tasaComision, $fechaCreacion, $idSucursal,
                                        $usuarioRegistro, $nombreEstado, $tasaComisionCobranza, $tipoNombre, $nombreDepartamento, $idDepartamento) &&
            $prepare_usr->fetch() &&
            $prepare_usr->num_rows > 0)
        {
            $this ->id                      = $idUsuario;
            $this ->nombre                  = $nombre;
            $this ->apellidop               = $apellidop;
            $this ->apellidom                  = $apellidom;
            $this ->nombres                 = $nombre." ".$apellidop." ".$apellidom;
            $this ->direccion1              = $direccion1;
			$this ->direccion2              = $direccion2;
            $this ->idEstado              	= $idEstado;
            $this ->nombreEstado            = $nombreEstado;
            $this ->nickName                = $nickName;
            $this ->telefono                = $telefono;
            $this ->celular                 = $celular;
            $this ->email                   = $email;
            $this ->tipo                    = $tipo;
			$this ->tasaComision            = $tasaComision;
            $this ->tasaComisionCobranza    = $tasaComisionCobranza;
            $this ->fechaCreacion           = $fechaCreacion;
            $this ->idSucursal              = $idSucursal;
            $this ->idUsuarioRegistro       = $usuarioRegistro;
			$this ->tipoNombre				= $tipoNombre;
			$this ->nombreDepartamento		= $nombreDepartamento;
			$this ->idDepartamento			= $idDepartamento;
            $sql = "SELECT nombres, apellidop, apellidom FROM cat_usuarios WHERE id =$usuarioRegistro LIMIT 1";
            $res_registro = $mysqli->query($sql);
            $row_registro = $res_registro->fetch_assoc();
            $this ->nombreUsuarioRegistro          = $row_registro['nombres']." ".$row_registro['apellidop']." ".$row_registro['apellidom'];
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
    public function tipoUsuario($html = FALSE)
    {
		switch ($this->tipo)
		{
			case 1:
				$badge = 'badge-info';
				break;
			case 2:
				$badge = 'badge-success';
				break;
			case 3:
				$badge = 'badge-yellow';
				break;
			case 4:
				$badge = 'badge-purple';
				break;
            default:
				$badge = 'badge-info';
				break;
		}
		return ($html) ? "<span class='badge $badge'>$this->tipoNombre</span>" : $this->tipoNombre;
	}
    public function permiso($string,$mysqli)
    {
        if ($this->id == 1)
            return TRUE;
        switch ($string)
        {
            case 'listarContratos':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND listarContratos = 1 LIMIT 1";
                break;
            case 'agregarContrato':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND agregarContrato = 1 LIMIT 1";
                break;
            case 'modificarContrato':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND modificarContrato = 1 LIMIT 1";
                break;
            case 'eliminarContrato':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND eliminarContrato = 1 LIMIT 1";
                break;
            case 'listarVentas':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND listarVentas = 1 LIMIT 1";
                break;
            case 'agregarVenta':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND agregarVenta = 1 LIMIT 1";
                break;
            case 'modificarVenta':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND modificarVenta = 1 LIMIT 1";
                break;
            case 'eliminarVenta':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND eliminarVenta = 1 LIMIT 1";
                break;
            case 'listarProveedores':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND listarProveedores = 1 LIMIT 1";
                break;
            case 'agregarProveedor':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND agregarProveedor = 1 LIMIT 1";
                break;
            case 'modificarProveedor':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND modificarProveedor = 1 LIMIT 1";
                break;
            case 'eliminarProveedor':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND eliminarProveedor = 1 LIMIT 1";
                break;
            case 'listarClientes':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND listarClientes = 1 LIMIT 1";
                break;
            case 'agregarCliente':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND agregarCliente = 1 LIMIT 1";
                break;
            case 'modificarCliente':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND modificarCliente = 1 LIMIT 1";
                break;
            case 'eliminarCliente':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND eliminarCliente = 1 LIMIT 1";
                break;
            case 'listarDifuntos':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND listarDifuntos = 1 LIMIT 1";
                break;
            case 'agregarDifunto':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND agregarDifunto = 1 LIMIT 1";
                break;
            case 'modificarDifunto':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND modificarDifunto = 1 LIMIT 1";
                break;
            case 'eliminarDifunto':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND eliminarDifunto = 1 LIMIT 1";
                break;
            case 'listarProductos':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND listarProductos = 1 LIMIT 1";
                break;
            case 'agregarProducto':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND agregarProducto = 1 LIMIT 1";
                break;
            case 'modificarProducto':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND modificarProducto = 1 LIMIT 1";
                break;
            case 'eliminarProducto':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND eliminarProducto = 1 LIMIT 1";
                break;
            case 'listarServicios':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND listarServicios = 1 LIMIT 1";
                break;
            case 'agregarServicio':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND agregarServicio = 1 LIMIT 1";
                break;
            case 'modificarServicio':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND modificarServicio = 1 LIMIT 1";
                break;
            case 'eliminarServicio':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND eliminarServicio = 1 LIMIT 1";
                break;
            case 'listarCompras':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND listarCompras = 1 LIMIT 1";
                break;
            case 'agregarCompra':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND agregarCompra = 1 LIMIT 1";
                break;
            case 'modificarCompra':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND modificarCompra = 1 LIMIT 1";
                break;
            case 'eliminarCompra':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND eliminarCompra = 1 LIMIT 1";
                break;
            case 'listarPlanes':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND listarPlanes = 1 LIMIT 1";
                break;
            case 'agregarPlan':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND agregarPlan = 1 LIMIT 1";
                break;
            case 'modificarPlan':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND modificarPlan = 1 LIMIT 1";
                break;
            case 'eliminarPlan':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND eliminarPlan = 1 LIMIT 1";
                break;
            case 'listarUsuarios':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND listarUsuarios = 1 LIMIT 1";
                break;
            case 'agregarUsuario':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND agregarUsuario = 1 LIMIT 1";
                break;
            case 'modificarUsuario':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND modificarUsuario = 1 LIMIT 1";
                break;
            case 'eliminarUsuario':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND eliminarUsuario = 1 LIMIT 1";
                break;
			case 'listarNominas':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND listarNominas = 1 LIMIT 1";
                break;
            case 'agregarNomina':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND agregarNomina = 1 LIMIT 1";
                break;
            case 'modificarNomina':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND modificarNomina = 1 LIMIT 1";
                break;
            case 'eliminarNomina':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND eliminarNomina = 1 LIMIT 1";
                break;
			case 'listarVariablesSistema':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND listarVariablesSistema = 1 LIMIT 1";
                break;
			case 'modificarVariablesSistema':
                $sql = "SELECT id FROM cat_permisos WHERE idUsuario = ? AND activo = 1 AND modificarVariablesSistema = 1 LIMIT 1";
                break;
            default:
                return FALSE;
                break;
        }
        $id = $this->id;
        $prepare = $mysqli->prepare($sql);
        if ($prepare &&
            $prepare->bind_param("i",$id) &&
            $prepare->execute() &&
            $prepare->store_result() &&
            $prepare->num_rows > 0)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    /** Devuelve el total cobrado y la comisión **/
    public function obtener_cobranza_cobrador($mysqli, $comision, $fechaInicio = FALSE, $fechaFinal = FALSE)
    {
        $idUsuario      = $this->id;
        $comision       = $comision / 100;
        $fechaInicio    = $fechaInicio == FALSE ? date('Y-m-d') : $fechaInicio;
        $fechaFinal     = $fechaFinal == FALSE ? date('Y-m-d') : $fechaFinal;
        $fechaInicio    .= " 00:00:00";
        $fechaFinal     .= " 23:59:59";
        $sql = "SELECT  IFNULL(SUM(detalle_pagos_contratos.monto),0) AS monto
                FROM detalle_pagos_contratos
                INNER JOIN folios_cobranza_asignados
                ON detalle_pagos_contratos.idFolio_cobranza = folios_cobranza_asignados.id
                WHERE folios_cobranza_asignados.asignado <> 0
                AND folios_cobranza_asignados.idUsuario_asignado = $idUsuario
                AND detalle_pagos_contratos.activo = 1
                AND detalle_pagos_contratos.fechaCreacion BETWEEN '$fechaInicio' AND '$fechaFinal'";
        $res = $mysqli->query($sql);
        $row = $res->fetch_assoc();
        $array['cobrado'] = $row['monto'];
        $array['comision'] = $row['monto'] * $comision;
        return $array;
    }
    public function obtener_cobranza_vendedor($query, $fechaInicio = FALSE, $fechaFinal = FALSE)
    {
        require_once "../php/contrato.class.php";
        $totalCobranza  = 0;
        $comision_ganada = 0;
        $idUsuario      = $this->id;
        $fechaInicio    = $fechaInicio == FALSE ? date('Y-m-d') : $fechaInicio;
        $fechaFinal     = $fechaFinal == FALSE ? date('Y-m-d') : $fechaFinal;
        $fechaInicio    .= " 00:00:00";
        $fechaFinal     .= " 23:59:59";
        $sql = "SELECT contratos.id AS id
                FROM contratos
                WHERE contratos.activo = 1
                AND contratos.idVendedor = $idUsuario
                AND contratos.fechaCreacion BETWEEN '$fechaInicio' AND '$fechaFinal'";
        $res = $mysqli->query($sql);
        while ($row = $res->fetch_assoc())
        {
            $idContrato             = $row['id'];
            $contrato               = new Contrato($idContrato, $query);
            $abonado_contrato       = $contrato->totalAbonado($mysqli);
            $costo_total_contrato   = $contrato->costoTotal;
            $comision_vendedor      = $contrato->comision_vendedor();
            $inversion              = $contrato->anticipo;
            if ($inversion > $comision_vendedor) {
                $diferencia = $inversion - $comision_vendedor;
                $totalCobranza += $diferencia;
            }
            $abonado_por_cobranza   = $abonado_contrato - $inversion;
            $abonado_por_cobranza   = $abonado_por_cobranza * 0.86;
            $total_ganado_comision_este_contrato =  $abonado_por_cobranza + $inversion;
            $comision_ganada        += $comision_vendedor > $total_ganado_comision_este_contrato ? $total_ganado_comision_este_contrato : $comision_vendedor;
        }
        $array['totalCobranza'] = $totalCobranza;
        $array['totalComisionGanada'] = $comision_ganada;
        return $array;
    }
}
?>
