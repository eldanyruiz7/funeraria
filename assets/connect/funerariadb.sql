-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 29-10-2019 a las 04:21:56
-- Versión del servidor: 10.3.18-MariaDB
-- Versión de PHP: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `funerariadb`
--
CREATE DATABASE IF NOT EXISTS `funerariadb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `funerariadb`;

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `agregarEvento` (IN `idUsuario` INT(11), IN `ip` VARCHAR(30), IN `pantalla` VARCHAR(100), IN `descripcion` VARCHAR(500), IN `idSucursal` INT(11))  INSERT INTO bitacora_eventos (idUsuario, ip, pantalla, descripcion, idSucursal) VALUES (idUsuario, ip, pantalla, descripcion, idSucursal)$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora_eventos`
--

CREATE TABLE `bitacora_eventos` (
  `id` bigint(20) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `ip` varchar(30) NOT NULL,
  `pantalla` varchar(100) NOT NULL,
  `descripcion` varchar(500) NOT NULL,
  `idSucursal` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_causasdecesos`
--

CREATE TABLE `cat_causasdecesos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `cat_causasdecesos`
--

INSERT INTO `cat_causasdecesos` (`id`, `nombre`, `fechaCreacion`, `usuario`, `activo`) VALUES
(1, 'Enfisema pulmonar', '2019-05-17 18:58:00', 1, 1),
(2, 'Paro respiratorio', '2019-05-17 18:58:00', 1, 1),
(3, 'Paro cardiaco', '2019-05-17 18:58:12', 1, 1),
(4, 'Diabetes mellitus', '2019-05-17 20:03:15', 1, 1),
(5, 'Accidente vial', '2019-05-17 21:43:46', 1, 1),
(6, 'Diabetes infantil', '2019-05-17 21:48:54', 1, 1),
(7, 'Cardiopatía Isquémica', '2019-05-18 18:46:55', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_difuntos`
--

CREATE TABLE `cat_difuntos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `idCliente` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `idContrato` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `idVenta` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `nombres` varchar(50) NOT NULL,
  `apellidop` varchar(50) NOT NULL,
  `apellidom` varchar(50) DEFAULT NULL,
  `domicilio1_part` varchar(200) NOT NULL,
  `domicilio2_part` varchar(200) NOT NULL,
  `cp_part` varchar(20) NOT NULL,
  `idEstado_part` int(11) NOT NULL,
  `rfc` varchar(20) NOT NULL,
  `fechaNac` date NOT NULL,
  `fechaHrDefuncion` datetime NOT NULL,
  `idLugarDefuncion` int(11) NOT NULL COMMENT '0 = Dom defuncion abierto',
  `nombreLugarDefuncion` varchar(50) DEFAULT NULL,
  `domicilioLugarDefuncion` varchar(300) DEFAULT NULL,
  `domicilioParticularDefuncion` varchar(300) NOT NULL,
  `noCertificadoDefuncion` varchar(100) DEFAULT NULL,
  `noActaDefuncion` varchar(100) DEFAULT NULL,
  `fechaRegistro` timestamp NOT NULL DEFAULT current_timestamp(),
  `idSucursal` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_estados`
--

CREATE TABLE `cat_estados` (
  `id` int(11) NOT NULL,
  `estado` varchar(100) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `cat_estados`
--

INSERT INTO `cat_estados` (`id`, `estado`, `activo`) VALUES
(1, 'Aguascalientes', 1),
(2, 'Baja California', 1),
(3, 'Baja California Sur', 1),
(4, 'Campeche', 1),
(5, 'Coahuila de Zaragoza', 1),
(6, 'Colima', 1),
(7, 'Chiapas', 1),
(8, 'Chihuahua', 1),
(9, 'Distrito Federal', 1),
(10, 'Durango', 1),
(11, 'Guanajuato', 1),
(12, 'Guerrero', 1),
(13, 'Hidalgo', 1),
(14, 'Jalisco', 1),
(15, 'México', 1),
(16, 'Michoacán de Ocampo', 1),
(17, 'Morelos', 1),
(18, 'Nayarit', 1),
(19, 'Nuevo León', 1),
(20, 'Oaxaca de Juárez', 1),
(21, 'Puebla', 1),
(22, 'Querétaro', 1),
(23, 'Quintana Roo', 1),
(24, 'San Luis Potosí', 1),
(25, 'Sinaloa', 1),
(26, 'Sonora', 1),
(27, 'Tabasco', 1),
(28, 'Tamaulipas', 1),
(29, 'Tlaxcala', 1),
(30, 'Veracruz de Ignacio de la Llave', 1),
(31, 'Yucatán', 1),
(32, 'Zacatecas', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_formas_pago`
--

CREATE TABLE `cat_formas_pago` (
  `id` int(11) NOT NULL,
  `c_FormaPago` varchar(5) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `bancarizado` int(11) NOT NULL COMMENT '1=si; 2=no',
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `cat_formas_pago`
--

INSERT INTO `cat_formas_pago` (`id`, `c_FormaPago`, `nombre`, `bancarizado`, `activo`) VALUES
(1, '01', 'Efectivo', 0, 1),
(2, '02', 'Cheque', 1, 1),
(3, '03', 'Transferencia', 1, 1),
(4, '04', 'Tarjeta de crédito', 1, 1),
(5, '05', 'Monedero electrónico', 1, 1),
(6, '06', 'Dinero electrónico', 1, 1),
(7, '08', 'Vales de despensa', 0, 1),
(8, '12', 'Dación en pago', 0, 1),
(9, '13', 'Pago por subrogación', 0, 1),
(10, '14', 'Pago por consignación', 0, 1),
(11, '15', 'Condonación', 0, 1),
(12, '17', 'Compensación', 0, 1),
(13, '23', 'Novación', 0, 1),
(14, '24', 'Confusión', 0, 1),
(15, '25', 'Remisión de deuda', 0, 1),
(16, '26', 'Prescripción o caducidad', 0, 1),
(17, '27', 'A satisfacción del acreedor', 0, 1),
(18, '28', 'Tarjeta de débito', 1, 1),
(19, '29', 'Tarjeta de servicios', 1, 1),
(20, '30', 'Aplicación de anticipos', 0, 1),
(21, '99', 'Por definir', 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_lugares_defuncion`
--

CREATE TABLE `cat_lugares_defuncion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `domicilio` varchar(300) NOT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_metodos_pago`
--

CREATE TABLE `cat_metodos_pago` (
  `id` int(11) NOT NULL,
  `c_MetodoPago` varchar(10) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `cat_metodos_pago`
--

INSERT INTO `cat_metodos_pago` (`id`, `c_MetodoPago`, `nombre`, `activo`) VALUES
(1, 'PUE', 'Pago en una sola exhibición', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_motivosCancelacionContratos`
--

CREATE TABLE `cat_motivosCancelacionContratos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1,
  `idUsuario` int(11) NOT NULL DEFAULT 1,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `cat_motivosCancelacionContratos`
--

INSERT INTO `cat_motivosCancelacionContratos` (`id`, `nombre`, `activo`, `idUsuario`, `fechaCreacion`) VALUES
(1, 'Problemas económicos', 1, 1, '2019-10-27 20:47:52'),
(2, 'Desempleo', 1, 1, '2019-10-27 20:47:52'),
(3, 'No localizable', 1, 1, '2019-10-27 20:47:52'),
(4, 'Pérdida de interés', 1, 1, '2019-10-27 20:47:52'),
(5, 'Transferencia de contrato', 1, 1, '2019-10-27 20:47:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_permisos`
--

CREATE TABLE `cat_permisos` (
  `id` bigint(20) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `listarContratos` int(11) NOT NULL,
  `agregarContrato` int(11) NOT NULL,
  `modificarContrato` int(11) NOT NULL,
  `eliminarContrato` int(11) NOT NULL,
  `listarVentas` int(11) NOT NULL,
  `agregarVenta` int(11) NOT NULL,
  `modificarVenta` int(11) NOT NULL,
  `eliminarVenta` int(11) NOT NULL,
  `listarProveedores` int(11) NOT NULL,
  `agregarProveedor` int(11) NOT NULL,
  `modificarProveedor` int(11) NOT NULL,
  `eliminarProveedor` int(11) NOT NULL,
  `listarClientes` int(11) NOT NULL,
  `agregarCliente` int(11) NOT NULL,
  `modificarCliente` int(11) NOT NULL,
  `eliminarCliente` int(11) NOT NULL,
  `listarDifuntos` int(11) NOT NULL,
  `agregarDifunto` int(11) NOT NULL,
  `modificarDifunto` int(11) NOT NULL,
  `eliminarDifunto` int(11) NOT NULL,
  `listarProductos` int(11) NOT NULL,
  `agregarProducto` int(11) NOT NULL,
  `modificarProducto` int(11) NOT NULL,
  `eliminarProducto` int(11) NOT NULL,
  `listarServicios` int(11) NOT NULL,
  `agregarServicio` int(11) NOT NULL,
  `modificarServicio` int(11) NOT NULL,
  `eliminarServicio` int(11) NOT NULL,
  `listarCompras` int(11) NOT NULL,
  `agregarCompra` int(11) NOT NULL,
  `modificarCompra` int(11) NOT NULL,
  `eliminarCompra` int(11) NOT NULL,
  `listarPlanes` int(11) NOT NULL,
  `agregarPlan` int(11) NOT NULL,
  `modificarPlan` int(11) NOT NULL,
  `eliminarPlan` int(11) NOT NULL,
  `listarUsuarios` int(11) NOT NULL,
  `agregarUsuario` int(11) NOT NULL,
  `modificarUsuario` int(11) NOT NULL,
  `eliminarUsuario` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_planes`
--

CREATE TABLE `cat_planes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(500) NOT NULL,
  `precio` float NOT NULL,
  `idSucursal` int(11) NOT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `imagen` varchar(25) DEFAULT NULL,
  `usuario` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `cat_planes`
--

INSERT INTO `cat_planes` (`id`, `nombre`, `descripcion`, `precio`, `idSucursal`, `fechaCreacion`, `imagen`, `usuario`, `activo`) VALUES

(1, 'Plan funerario 1', 'Descripción del pman funerari No. 2', 6500, 1, '2019-05-14 00:01:12', '0000000002', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_productos`
--

CREATE TABLE `cat_productos` (
  `id` int(11) NOT NULL,
  `claveSat` varchar(10) DEFAULT NULL,
  `unidadVenta` int(11) NOT NULL DEFAULT 0,
  `nombre` varchar(200) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `precio` float NOT NULL,
  `precioCompra` float NOT NULL DEFAULT 1,
  `imagen` varchar(20) DEFAULT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `idSucursal` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_proveedores`
--

CREATE TABLE `cat_proveedores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rsocial` varchar(50) NOT NULL,
  `representante` varchar(50) NOT NULL,
  `telefono` varchar(25) NOT NULL,
  `celular` varchar(25) NOT NULL,
  `domicilio1` varchar(100) NOT NULL,
  `domicilio2` varchar(100) NOT NULL,
  `cp` varchar(10) NOT NULL,
  `idEstado` int(11) NOT NULL,
  `rfc` varchar(25) NOT NULL,
  `email` varchar(50) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1,
  `usuario` bigint(20) UNSIGNED NOT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `cat_proveedores`
--

INSERT INTO `cat_proveedores` (`id`, `rsocial`, `representante`, `telefono`, `celular`, `domicilio1`, `domicilio2`, `cp`, `idEstado`, `rfc`, `email`, `activo`, `usuario`, `fechaCreacion`) VALUES
(1, 'Proveedor no especificado', 'Sin representante_m', '(314) 111-1111', '(314) 122-2222', 'Domicilio no_m', 'especificado_m', '288700', 1, 'ABCD010102', 'proveedor@example.mx', 1, 1, '2019-05-10 10:47:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_regimenes_fiscales`
--

CREATE TABLE `cat_regimenes_fiscales` (
  `id` int(11) NOT NULL,
  `c_RegimenFiscal` varchar(10) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `cat_regimenes_fiscales`
--

INSERT INTO `cat_regimenes_fiscales` (`id`, `c_RegimenFiscal`, `nombre`, `activo`) VALUES
(1, '601', 'General de Ley Personas Morales', 1),
(2, '603', 'Personas Morales con Fines no Lucrativos\r\n', 1),
(3, '605', 'Sueldos y Salarios e Ingresos Asimilados a Salarios\r\n', 1),
(4, '606', 'Arrendamiento\r\n', 1),
(5, '608', 'Demás ingresos\r\n', 1),
(6, '609', 'Consolidación', 1),
(7, '610', 'Residentes en el Extranjero sin Establecimiento Permanente en México\r\n', 1),
(8, '611', 'Ingresos por Dividendos (socios y accionistas)\r\n', 1),
(9, '612', 'Personas Físicas con Actividades Empresariales y Profesionales', 1),
(10, '614', 'Ingresos por intereses\r\n', 1),
(11, '616', 'Sin obligaciones fiscales\r\n', 1),
(12, '620', 'Sociedades Cooperativas de Producción que optan por diferir sus ingresos\r\n', 1),
(13, '621', 'Incorporación Fiscal\r\n', 1),
(14, '622', 'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras\r\n', 1),
(15, '623', 'Opcional para Grupos de Sociedades\r\n', 1),
(16, '628', 'Hidrocarburos', 1),
(17, '607', 'Régimen de Enajenación o Adquisición de Bienes', 1),
(18, '629', 'De los Regímenes Fiscales Preferentes y de las Empresas Multinacionales\r\n', 1),
(19, '630', 'Enajenación de acciones en bolsa de valores\r\n', 1),
(20, '615', 'Régimen de los ingresos por obtención de premios\r\n', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_servicios`
--

CREATE TABLE `cat_servicios` (
  `id` int(11) NOT NULL,
  `claveSat` varchar(10) NOT NULL,
  `unidadVenta` int(11) NOT NULL DEFAULT 0,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `precio` float NOT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_sucursales`
--

CREATE TABLE `cat_sucursales` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `representante` varchar(300) NOT NULL,
  `lema` varchar(200) NOT NULL,
  `direccion1` varchar(200) NOT NULL,
  `direccion2` varchar(200) NOT NULL,
  `cp` varchar(20) NOT NULL,
  `estado` int(11) NOT NULL,
  `telefono1` varchar(50) NOT NULL,
  `telefono2` varchar(50) NOT NULL,
  `celular` varchar(50) NOT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `clausulasContrato` mediumtext NOT NULL,
  `rfc` varchar(50) NOT NULL,
  `curp` varchar(50) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `idRegimenFiscal` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `cat_sucursales`
--

INSERT INTO `cat_sucursales` (`id`, `nombre`, `representante`, `lema`, `direccion1`, `direccion2`, `cp`, `estado`, `telefono1`, `telefono2`, `celular`, `fechaCreacion`, `clausulasContrato`, `rfc`, `curp`, `correo`, `idRegimenFiscal`, `usuario`, `activo`) VALUES
(1, 'Nombre funeraria', 'Nombre del propietario', 'El tiempo y el suceso imprevisto les acaecen a todos...', 'Domicilio Sucursal', 'Col. Centro', '00000', 6, '(555)12 3 45 67', '01 800 00 00 0000', '', '2019-04-11 18:21:50', '<div class=\"wysiwyg-editor\" id=\"editor1\" contenteditable=\"true\">DECLARACIONES DEL AFILIADO: 1.- Que es una persona física que tiene plena capacidad para la celebración de este contrato y sus datos personales figuran al frente de este contrato.<div>DECLARACIONES DE LA EMPRESA: 1.- Que es una persona física, representada NOMBRE SUCURSAL, en calidad de propietaria y que tiene plena capacidad para la celebración de este contrato, así como para obligarse en todos y cada uno de los términos del presente contrato.</div><div>2.- Que tiene su domicilio ubicado en Domicilio de la sucursal. con número de teléfono 01 (555) 12 3 33 45, mismos que señala para los fines y efectos legales a que haya lugar.</div><div style=\"text-align: center;\"><b><font size=\"5\">CLÁUSULAS:</font></b></div><div style=\"text-align: left;\">1.- NOMBRE SUCURSAL se obliga a proporcionar los servicios funerarios contratados en este convenio mutuo, con oportunidad, eficacia y calidad representado por NOMBRE PROPIETARIO, en calidad de propietario.<br>2.- El titular deberá entregar el presente contrato al momento de solicitar el servicio estando al corriente con sus aportaciones y firmando la solicitud de servicio cualquiera de sus familiares, aceptando así lo estipulado en dicho contrato.<br>3.- La falta de aportaciones en 90 días será factor de cancelación automática, quedando como depósito la cantidad aportada misma que podrá hacer uso en un servicio futuro de uso inmediato al precio actual al momento de solicitarlo.<br>4.- Una vez que NOMBRE SUCURSAL otorgue el servicio pactado en el presente contrato terminará su obligación para con el titular.<br>5.- En caso de fallecimiento del titular y el contrato tenga 3,000.00 (tres mil pesos 00/100 M.N.) de las aportaciones del costo total NOMBRE SUCURSAL otorgará cabalmente un servicio funerario plan 2, quedando exenta de pago, se requiere estar al corriente con sus aportaciones y el titular no debe ser mayor de 60 años.<br>6.- Los servicios del presente contrato son TRANSFERIBLES a quien el titular lo determine.<br>7.- Una vez cumplido con las aportaciones establecidas en dicho contrato de manera puntual, EL PRECIO QUEDARÁ CONGELADO Y SIN FECHA DE CADUCIDAD.<br>8.- Cualquier caso no previsto en la celebración de este contrato, será sometido a diálogo entre ambas partes a efecto de llegar a un acuerdo, en caso de ser así las partes se someten a los tribunales correspondientes en la ciudad de Ciudad de la sucursal.</div><div style=\"text-align: left;\">9.- En caso de la cancelación voluntaria o automática del contrato, lo aportado en dicho contrato se quedará como depósito expidiendo un bono mismo que podrá hacer uso en un servicio funerario en NOMBRE FUNERARIA.</div><div style=\"text-align: left;\">10.- Este contrato entra en vigor a partir de la fecha de contratación y podrá utilizarse desde el primer día, no obstante los solicitantes deberán hacer la aportación total del valor del servicio con un plazo de 60 días naturales después de ser utilizado.<br>11.- El servicio aquí estipulado además podrá ser otorgado por NOMBRE FUNERARIA, en la ciudad de Ciudad de la matriz. Con domicilio en Domicilio matriz.<br></div></div>', 'RFCEJEM', 'CURPEJEM', 'funeralez@ejemplo.com', 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_tipo_usuarios`
--

CREATE TABLE `cat_tipo_usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `idSucursal` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_unidades_venta`
--

CREATE TABLE `cat_unidades_venta` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `c_ClaveUnidad` varchar(25) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `cat_unidades_venta`
--

INSERT INTO `cat_unidades_venta` (`id`, `nombre`, `c_ClaveUnidad`, `activo`) VALUES
(1, 'Kg', 'KGM', 1),
(2, 'Botella', 'XBO', 1),
(3, 'Paquete', 'XPK', 1),
(4, 'Pieza', 'H87', 1),
(5, 'Lata', 'XCX', 1),
(6, 'Bolsa', 'X44', 1),
(7, 'Caja', 'XBX', 1),
(8, 'Bote', 'XPR', 1),
(9, 'Frasco', 'XCI', 1),
(10, 'Manojo', 'XBH', 1),
(11, 'Galon', 'XGL', 1),
(12, 'Bot. Protegida', 'XBQ', 1),
(13, 'Unidad de servicio', 'E48', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_usos_cfdi`
--

CREATE TABLE `cat_usos_cfdi` (
  `id` int(11) NOT NULL,
  `nombre` varchar(300) NOT NULL,
  `c_UsoCFDI` varchar(25) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `cat_usos_cfdi`
--

INSERT INTO `cat_usos_cfdi` (`id`, `nombre`, `c_UsoCFDI`, `activo`) VALUES
(1, 'Adquisicion mercancias', 'G01', 1),
(2, 'Devoluciones, descuentos o bonificaciones', 'G02', 1),
(3, 'Gastos en general', 'G03', 1),
(4, 'Construcciones', 'I01', 1),
(5, 'Mobiliario y equipo de oficina por inversiones', 'I02', 1),
(6, 'Equipo de transporte', 'I03', 1),
(7, 'Equipo de cómputo y accesorios', 'I04', 1),
(8, 'Dados, troqueles, moldes, matrices y herramental', 'I05', 1),
(9, 'Comunicaciones telefónicas', 'I06', 1),
(10, 'Comunicaciones satelitales', 'I07', 1),
(11, 'Otra maquinaria y equipo', 'I08', 1),
(12, 'Honorarios médicos, dentales y gastos hospitalarios.', 'D01', 1),
(13, 'Gastos médicos por incapacidad o discapacidad', 'D02', 1),
(14, 'Gastos funerales', 'D03', 1),
(15, 'Donativos', 'D04', 1),
(16, 'Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación).', 'D05', 1),
(17, 'Aportaciones voluntarias al SAR.', 'D06', 1),
(18, 'Primas por seguros de gastos médicos.', 'D07', 1),
(19, 'Gastos de transportación escolar obligatoria.', 'D08', 1),
(20, 'Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones.', 'D09', 1),
(21, 'Pagos por servicios educativos (colegiaturas)', 'D10', 1),
(22, 'Por definir', 'P01', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cat_usuarios`
--

CREATE TABLE `cat_usuarios` (
  `id` int(11) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidop` varchar(100) NOT NULL,
  `apellidom` varchar(100) NOT NULL,
  `direccion1` varchar(200) NOT NULL,
  `direccion2` varchar(200) NOT NULL,
  `estado` int(11) NOT NULL,
  `nickName` varchar(50) NOT NULL,
  `cntrsn` varchar(100) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `celular` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `tipo` int(11) NOT NULL COMMENT '1=Admin,2=Secretario,3=Visitante',
  `tasaComision` float NOT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `idSucursal` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `cat_usuarios`
--

INSERT INTO `cat_usuarios` (`id`, `nombres`, `apellidop`, `apellidom`, `direccion1`, `direccion2`, `estado`, `nickName`, `cntrsn`, `telefono`, `celular`, `email`, `tipo`, `tasaComision`, `fechaCreacion`, `idSucursal`, `usuario`, `activo`) VALUES
(1, 'root', 'admin', '', 'Domicilio 1', 'Domicilio 2', 6, 'system', '$2y$15$VpxKMvAom2bDT56olaCCCeyPcuoPifK0VvO2CaDvo7v3kjGdO8txm', '', '', 'jhon_doe@ejemplo.com', 0, 10, '2019-04-10 12:24:00', 1, 1, 1);


--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombres` varchar(50) NOT NULL,
  `apellidop` varchar(50) NOT NULL,
  `apellidom` varchar(50) DEFAULT NULL,
  `domicilio1` varchar(200) NOT NULL,
  `domicilio2` varchar(200) NOT NULL,
  `cp` varchar(10) NOT NULL,
  `idEstado` int(11) NOT NULL,
  `rfc` varchar(20) DEFAULT NULL,
  `fechaNac` date NOT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `cel` varchar(20) DEFAULT NULL,
  `email` varchar(40) NOT NULL,
  `fechaRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  `idVenta` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `idContrato` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `idSucursal` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `idProveedor` bigint(20) UNSIGNED NOT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `idSucursal` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contratos`
--

CREATE TABLE `contratos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `folio` varchar(20) NOT NULL DEFAULT '0',
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fechaPrimerAportacion` date NOT NULL,
  `enCurso` int(11) NOT NULL DEFAULT 1,
  `precio` float NOT NULL,
  `primerAnticipo` float NOT NULL,
  `precioAportacion` float NOT NULL,
  `noAportaciones` int(11) NOT NULL DEFAULT 0,
  `descuentoDuplicacionInversion` float NOT NULL DEFAULT 0,
  `descuentoCambioFuneraria` float NOT NULL DEFAULT 0,
  `descuentoAdicional` float NOT NULL DEFAULT 0,
  `direccion1` varchar(200) NOT NULL,
  `direccion2` varchar(200) NOT NULL,
  `cp` varchar(20) NOT NULL,
  `idEstado` int(11) NOT NULL,
  `referencias` varchar(200) DEFAULT NULL,
  `formaPago` varchar(5) NOT NULL,
  `frecuenciaPago` int(11) NOT NULL COMMENT '1=semanal;2=quincenal;3=mensual',
  `tasaComision` int(11) NOT NULL,
  `idTitular` bigint(20) UNSIGNED NOT NULL COMMENT 'id cliente',
  `idFallecido` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `idPlan` int(11) NOT NULL COMMENT '0 = Sin plan',
  `idFactura` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `idVendedor` int(11) NOT NULL,
  `idSucursal` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `motivoCancelado` int(11) NOT NULL DEFAULT 0 COMMENT '0 = No cancelado; 1 = Problemas económicos; 2 = Desempleo; 3 = No localizable; 4 = Pérdida de interés; 5= Transferencia de contrato',
  `activo` int(11) NOT NULL DEFAULT 1,
  `observaciones` varchar(400) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_cat_planes`
--

CREATE TABLE `detalle_cat_planes` (
  `id` int(11) NOT NULL,
  `idPlan` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `idServicio` int(11) NOT NULL,
  `idSucursal` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` float NOT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_causasdecesos`
--

CREATE TABLE `detalle_causasdecesos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `idDifunto` bigint(20) UNSIGNED NOT NULL,
  `idCausa` int(11) NOT NULL,
  `fechaRegistro` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compras`
--

CREATE TABLE `detalle_compras` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `idCompra` bigint(20) UNSIGNED NOT NULL,
  `idProducto` int(11) NOT NULL,
  `idServicio` int(11) NOT NULL,
  `precioCompra` float NOT NULL,
  `cantidad` int(11) NOT NULL,
  `idSucursal` int(11) NOT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_contrato`
--

CREATE TABLE `detalle_contrato` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `idContrato` bigint(20) UNSIGNED NOT NULL,
  `idProducto` int(11) NOT NULL,
  `idServicio` int(11) NOT NULL,
  `idSucursal` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_existenciasproductos`
--

CREATE TABLE `detalle_existenciasproductos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `idProducto` int(11) NOT NULL,
  `idSucursal` int(11) NOT NULL,
  `existencias` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_facturas`
--

CREATE TABLE `detalle_facturas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `idFactura` bigint(20) UNSIGNED NOT NULL,
  `idProducto` bigint(20) UNSIGNED NOT NULL,
  `idServicio` bigint(20) UNSIGNED NOT NULL,
  `claveSat` varchar(25) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `claveUnidad` varchar(10) NOT NULL,
  `nombreUnidad` varchar(50) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `precioU` float NOT NULL,
  `iva` float NOT NULL DEFAULT 0,
  `ieps` float NOT NULL DEFAULT 0,
  `importe` float NOT NULL,
  `descuento` float NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pagos_contratos`
--

CREATE TABLE `detalle_pagos_contratos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `idContrato` bigint(20) UNSIGNED NOT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `monto` float NOT NULL,
  `usuario_cobro` int(11) NOT NULL,
  `usuario_registro` int(11) NOT NULL,
  `formaPago` int(11) NOT NULL DEFAULT 1,
  `idFolio_cobranza` bigint(20) UNSIGNED NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ventas`
--

CREATE TABLE `detalle_ventas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `idVenta` bigint(20) UNSIGNED NOT NULL,
  `idProducto` int(11) NOT NULL,
  `idServicio` int(11) NOT NULL,
  `precioVenta` float NOT NULL,
  `cantidad` int(11) NOT NULL,
  `idSucursal` int(11) NOT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `usuario` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rfcEmisor` varchar(25) NOT NULL,
  `razonEmisor` varchar(500) NOT NULL,
  `regimenEmisor` varchar(20) NOT NULL,
  `domicilioEmisor` varchar(500) NOT NULL,
  `idEstadoEmisor` int(11) NOT NULL,
  `cpEmisor` varchar(20) NOT NULL,
  `idReceptor` bigint(20) NOT NULL,
  `rfcReceptor` varchar(25) NOT NULL,
  `razonReceptor` varchar(500) NOT NULL,
  `regimenReceptor` varchar(20) DEFAULT NULL,
  `domicilioReceptor` varchar(500) NOT NULL,
  `cpReceptor` varchar(20) NOT NULL,
  `emailReceptor` varchar(50) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `fechaEmision` varchar(50) NOT NULL,
  `usoCFDI` varchar(25) NOT NULL,
  `tipoCFDI` varchar(10) NOT NULL,
  `moneda` varchar(10) NOT NULL DEFAULT 'MXN',
  `formaPago` varchar(10) NOT NULL,
  `metodoPago` varchar(10) NOT NULL,
  `xml` mediumtext NOT NULL,
  `folioFiscal` varchar(100) NOT NULL,
  `noCertificado` varchar(60) NOT NULL,
  `noCertificadoSAT` varchar(60) DEFAULT NULL,
  `selloDigitalEmisor` text NOT NULL,
  `selloDigitalSAT` text NOT NULL,
  `cadenaOriginal` text NOT NULL,
  `cadenaOriginalCumplimiento` text NOT NULL,
  `codigoQR` varchar(200) NOT NULL,
  `fechaCertificacion` varchar(50) NOT NULL,
  `totalIVA` double NOT NULL,
  `totalIEPS` double NOT NULL,
  `subTotal` double NOT NULL,
  `total` double NOT NULL,
  `descuento` double NOT NULL DEFAULT 0,
  `version` varchar(10) NOT NULL DEFAULT '3.3',
  `usuario` bigint(20) NOT NULL DEFAULT 2,
  `pagado` int(11) NOT NULL DEFAULT 1,
  `idVentaRelacion` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `idContratoRelacion` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `idSucursal` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `folios_cobranza_asignados`
--

CREATE TABLE `folios_cobranza_asignados` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `idUsuario_asignado` int(11) NOT NULL,
  `folio` varchar(30) NOT NULL,
  `idSucursal` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1,
  `asignado` int(11) NOT NULL DEFAULT 0 COMMENT 'No. de recibo de cobro del sistema',
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `idUsuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesionescontrol`
--

CREATE TABLE `sesionescontrol` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `timestampentrada` datetime NOT NULL,
  `timestampsalida` datetime DEFAULT NULL,
  `estado` int(11) NOT NULL DEFAULT 1 COMMENT '0=cancelada;1=Abierta/en uso;2=cerrada',
  `usuario` int(11) NOT NULL,
  `activo` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `vendedor` int(11) NOT NULL,
  `idCliente` bigint(20) UNSIGNED NOT NULL,
  `tasaComision` int(11) NOT NULL,
  `idFactura` bigint(20) NOT NULL DEFAULT 0,
  `idSucursal` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bitacora_eventos`
--
ALTER TABLE `bitacora_eventos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_causasdecesos`
--
ALTER TABLE `cat_causasdecesos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_difuntos`
--
ALTER TABLE `cat_difuntos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_estados`
--
ALTER TABLE `cat_estados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_formas_pago`
--
ALTER TABLE `cat_formas_pago`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_lugares_defuncion`
--
ALTER TABLE `cat_lugares_defuncion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_metodos_pago`
--
ALTER TABLE `cat_metodos_pago`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_motivosCancelacionContratos`
--
ALTER TABLE `cat_motivosCancelacionContratos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_permisos`
--
ALTER TABLE `cat_permisos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_planes`
--
ALTER TABLE `cat_planes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_productos`
--
ALTER TABLE `cat_productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_proveedores`
--
ALTER TABLE `cat_proveedores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_regimenes_fiscales`
--
ALTER TABLE `cat_regimenes_fiscales`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_servicios`
--
ALTER TABLE `cat_servicios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_sucursales`
--
ALTER TABLE `cat_sucursales`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_tipo_usuarios`
--
ALTER TABLE `cat_tipo_usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_unidades_venta`
--
ALTER TABLE `cat_unidades_venta`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_usos_cfdi`
--
ALTER TABLE `cat_usos_cfdi`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cat_usuarios`
--
ALTER TABLE `cat_usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `contratos`
--
ALTER TABLE `contratos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_cat_planes`
--
ALTER TABLE `detalle_cat_planes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_causasdecesos`
--
ALTER TABLE `detalle_causasdecesos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_contrato`
--
ALTER TABLE `detalle_contrato`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_existenciasproductos`
--
ALTER TABLE `detalle_existenciasproductos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_facturas`
--
ALTER TABLE `detalle_facturas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_pagos_contratos`
--
ALTER TABLE `detalle_pagos_contratos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tipoCFDI` (`tipoCFDI`);

--
-- Indices de la tabla `folios_cobranza_asignados`
--
ALTER TABLE `folios_cobranza_asignados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sesionescontrol`
--
ALTER TABLE `sesionescontrol`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bitacora_eventos`
--
ALTER TABLE `bitacora_eventos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cat_causasdecesos`
--
ALTER TABLE `cat_causasdecesos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `cat_difuntos`
--
ALTER TABLE `cat_difuntos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cat_estados`
--
ALTER TABLE `cat_estados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `cat_formas_pago`
--
ALTER TABLE `cat_formas_pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `cat_lugares_defuncion`
--
ALTER TABLE `cat_lugares_defuncion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cat_metodos_pago`
--
ALTER TABLE `cat_metodos_pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cat_motivosCancelacionContratos`
--
ALTER TABLE `cat_motivosCancelacionContratos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `cat_permisos`
--
ALTER TABLE `cat_permisos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `cat_planes`
--
ALTER TABLE `cat_planes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `cat_productos`
--
ALTER TABLE `cat_productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cat_proveedores`
--
ALTER TABLE `cat_proveedores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `cat_regimenes_fiscales`
--
ALTER TABLE `cat_regimenes_fiscales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `cat_servicios`
--
ALTER TABLE `cat_servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cat_tipo_usuarios`
--
ALTER TABLE `cat_tipo_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cat_unidades_venta`
--
ALTER TABLE `cat_unidades_venta`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `cat_usos_cfdi`
--
ALTER TABLE `cat_usos_cfdi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `cat_usuarios`
--
ALTER TABLE `cat_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contratos`
--
ALTER TABLE `contratos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_cat_planes`
--
ALTER TABLE `detalle_cat_planes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_causasdecesos`
--
ALTER TABLE `detalle_causasdecesos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_compras`
--
ALTER TABLE `detalle_compras`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_contrato`
--
ALTER TABLE `detalle_contrato`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_existenciasproductos`
--
ALTER TABLE `detalle_existenciasproductos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_facturas`
--
ALTER TABLE `detalle_facturas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_pagos_contratos`
--
ALTER TABLE `detalle_pagos_contratos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_ventas`
--
ALTER TABLE `detalle_ventas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `folios_cobranza_asignados`
--
ALTER TABLE `folios_cobranza_asignados`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sesionescontrol`
--
ALTER TABLE `sesionescontrol`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
