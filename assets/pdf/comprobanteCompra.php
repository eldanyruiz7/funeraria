<?php
	// error_reporting(E_ALL);
	ini_set('display_errors', '0');
    require_once ('../connect/bd.php');
    require_once ("../connect/sesion.class.php");
    $sesion = new sesion();
    require_once ("../connect/cerrarOtrasSesiones.php");
    require_once ("../connect/usuarioLogeado.php");
    require_once ("../php/funcionesVarias.php");
    require_once ('../fpdf/code128.php');
    if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
    {
        header("Location: salir.php");
    }
    else
    {
        if (isset($_GET['idCompra']))
        {
            $idCompra = $_GET['idCompra'];
        }
        else
        {
            die;
        }
        if (!$idCompra = validarFormulario('i',$idCompra,0))
        {
            echo "El formato del Id de la compra no es el esperado.";
            die;
        }
        else
        {
            $sql = "SELECT
                        compras.id                  AS idCompra,
                        compras.idProveedor         AS idProveedor,
                        compras.fechaCreacion       AS fechaCreacion,
                        compras.idSucursal          AS idSucursal,
                        compras.usuario             AS idUsuario,
                        compras.activo              AS compraActiva,
                        cat_proveedores.rsocial     AS nombreProveedor,
                        cat_sucursales.nombre       AS nombreSucursal,
                        cat_sucursales.lema         AS lemaSucursal,
                        cat_sucursales.direccion1   AS direccion1Sucursal,
                        cat_sucursales.direccion2   AS direccion2Sucursal,
                        cat_sucursales.cp           AS cpSucursal,
                        cat_sucursales.telefono1    AS tel1Sucursal,
                        cat_sucursales.telefono2    AS tel2Sucursal,
                        cat_sucursales.celular      AS celSucursal,
                        cat_estados.estado          AS nombreEstado,
                        cat_usuarios.nombres        AS nombreUsuario,
                        cat_usuarios.apellidop      AS apellidopUsuario,
                        cat_usuarios.apellidom      AS apellidomUsuario
                    FROM compras
                    INNER JOIN cat_proveedores
                    ON compras.idProveedor          = cat_proveedores.id
                    INNER JOIN cat_sucursales
                    ON compras.idSucursal           = cat_sucursales.id
                    INNER JOIN cat_estados
                    ON cat_sucursales.estado        = cat_estados.id
                    INNER JOIN cat_usuarios
                    ON compras.usuario              = cat_usuarios.id
                    WHERE compras.id                = ?
                    LIMIT 1";
            if ($prepare_compra = $mysqli->prepare($sql))
            {
                if ($prepare_compra->bind_param('i',$idCompra) && $prepare_compra->execute())
                {
                    $res_compra = $prepare_compra->get_result();
                    if ($res_compra->num_rows == 1)
                    {
    /////////////////////////////////////////////////////////// COMPRA!!! ////////////////////////////////////////////////////////////////
                        $margen                     = 0; //Margen de celdas
                        $row_compra                 = $res_compra->fetch_assoc();
                        $idCompra                   = $row_compra['idCompra'];
                        $compraActiva               = ($row_compra['compraActiva'] == 1) ? TRUE : FALSE;
                        $nombreSucursal             = $row_compra['nombreSucursal'];
                        $lemaSucursal               = $row_compra['lemaSucursal'];
                        $nombreUsuario              = $row_compra['nombreUsuario']." ".$row_compra['apellidopUsuario'];
                        $nombreProveedor            = $row_compra['nombreProveedor'];
                        $idSucursal					= $row_compra['idSucursal'];
                        $fechaCompra                = date_create($row_compra['fechaCreacion']);
                        $fechaCompra                = date_format($fechaCompra, 'd/m/Y h:i:s a');
                        $direccion                  = $row_compra['direccion1Sucursal']." ".$row_compra['direccion2Sucursal'].", ".$row_compra['nombreEstado'];
                        $pdf                        = new PDF_Code128('P','mm','Letter');
                        $pdf                        ->AliasNbPages();
                        $pdf->AddPage();
                        $pdf->SetMargins(8,20);
                        $pdf->SetFont('times','',10);

                        $code           = str_pad($idCompra, 10, "0", STR_PAD_LEFT);
                        $pdf->Code128(167,11," ".$code,38,12);
                        $pdf->SetXY(172,20.5);
                        $pdf->SetFillColor(120, 200, 80);
                        $pdf->Cell(30,9,"No.- ".$code,$margen,1,'C');
                        if (!$compraActiva)
                        {
                            // $pdf->Image('../images/icons/cancelada.png',30,33, 140);
                            $pdf->SetXY(172,24.5);
                            $pdf->SetTextColor(255,0,0);
                            $pdf->SetFont('times','B',11);
                            $pdf->Cell(30,9,"CANCELADA",0,0,'C',0);
                            $pdf->SetTextColor(0);
                            $pdf->SetFont('times','',10);
                        }
                        $pdf->SetXY(37,4);
                        $pdf->Cell(168,5,utf8_decode(''),$margen,1,'C',true);
                        $pdf->SetFont('times','B',27);
                        $pdf->Cell(200,3,utf8_decode(""),$margen,1,'C');
                        $pdf->Cell(200,7,utf8_decode($nombreSucursal),$margen,1,'C');
                        $pdf->SetFont('times','I',8);
                        $pdf->Cell(200,2,utf8_decode('"'.$lemaSucursal.'"'),$margen,1,'C');

                        $pdf->SetFont('times','BU',17.5);
                        $pdf->Cell(200,8,utf8_decode("COMPROBANTE DE COMPRA"),$margen,1,'C');
                        $pdf->SetFillColor(233);
                        $pdf->SetDrawColor(255);
                        $pdf->SetLineWidth(0.6);
                        $pdf->Cell(200,3,utf8_decode(""),$margen,1,'C');
                        $pdf->SetFont('Courier','B',10.5);
                        $pdf->Cell(17,5.5,utf8_decode("ELABORÓ:"),$margen,0,'L');
                        $pdf->SetFont('Courier','',9.5);
                        $pdf->Cell(70,5.5,utf8_decode($nombreUsuario),1,0,'C',true);
                        $pdf->SetFont('Courier','B',10.5);
                        $pdf->Cell(22,5.5,utf8_decode('PROVEEDOR:'),$margen,0,'L');
                        $pdf->SetFont('Courier','',9.5);
                        $pdf->Cell(88,5.5,utf8_decode($nombreProveedor),1,1,'C',true);
                        $pdf->SetFont('Courier','B',10.5);
                        $pdf->Cell(19,5.5,utf8_decode('FECHA/HR:'),$margen,0,'L');
                        $pdf->SetFont('Courier','',9.5);
                        $pdf->Cell(48,5.5,utf8_decode($fechaCompra),1,0,'C',true);
                        $pdf->SetFont('Courier','B',10.5);
                        $pdf->Cell(23,5.5,utf8_decode('DIRECCIÓN:'),$margen,0,'L');
                        $pdf->SetFont('Courier','',9.5);
                        $pdf->Cell(107,5.5,utf8_decode($direccion),1,1,'C',true);

                        $pdf->Cell(100,1.8,"",0,1,'L',0);
                        $pdf->SetFillColor(99, 99, 99);
                        $pdf->SetTextColor(255);
                        $pdf->SetFont('Courier','B',11);
                        $pdf->Cell(25,5.5,utf8_decode('ID PROD.'),1,0,'C',TRUE);
                        $pdf->Cell(87,5.5,utf8_decode('NOMBRE DEL ARTÍCULO'),1,0,'C',TRUE);
                        $pdf->Cell(30,5.5,utf8_decode('$ UNITARIO'),1,0,'C',TRUE);
                        $pdf->Cell(15,5.5,utf8_decode('CANT.'),1,0,'C',TRUE);
                        $pdf->Cell(40,5.5,utf8_decode('SUB TOTAL'),1,1,'C',TRUE);
                        //$pdf->SetFillColor(255);
                        $pdf->SetTextColor(0);
                        $pdf->SetFillColor(0);
                        $pdf->SetDrawColor(0);
                        $sql = "SELECT
                                    detalle_compras.idProducto AS idProducto,
                                    detalle_compras.precioCompra AS precioCompra,
                                    detalle_compras.cantidad AS cantidad,
                                    cat_productos.nombre AS nombreProducto
                                FROM detalle_compras
                                INNER JOIN cat_productos
                                ON detalle_compras.idProducto = cat_productos.id
                                WHERE detalle_compras.idCompra = ? AND detalle_compras.activo = 1";
                        if ($prepare_det = $mysqli->prepare($sql))
                        {
                            if ($prepare_det->bind_param('i', $idCompra) && $prepare_det->execute())
                            {
                                $res_det = $prepare_det->get_result();
                                $totalArts = $res_det->num_rows;
                                $totalCompra = 0;
                                $pdf->SetFont('Times','',11.5);
                                while ($row_det = $res_det->fetch_assoc())
                                {
                                    $esteId = str_pad($row_det['idProducto'], 7, "0", STR_PAD_LEFT);
                                    $esteNombreProducto = $row_det['nombreProducto'];
                                    $estePrecio = $row_det['precioCompra'];
                                    $esteCantidad = $row_det['cantidad'];
                                    $totalCompra += $esteSubTotal = $estePrecio * $esteCantidad;
                                    $pdf->Cell(25,5.5,utf8_decode($esteId),$margen,0,'R',0);
                                    $pdf->Cell(87,5.5,utf8_decode($esteNombreProducto),$margen,0,'L',0);
                                    $pdf->Cell(30,5.5,utf8_decode("$".number_format($estePrecio,2,".",",")),$margen,0,'R',0);
                                    $pdf->Cell(15,5.5,utf8_decode($esteCantidad),$margen,0,'R',0);
                                    $pdf->Cell(40,5.5,utf8_decode("$".number_format($esteSubTotal,2,".",",")),$margen,1,'R',0);

                                }
                                $pdf->SetDrawColor(0);
                                $pdf->SetLineWidth(0.2);

                                $pdf->Cell(197,2,"",0,1,'L');
                                $pdf->Cell(197,0,"",1,1,'L');
                                //$totalVenta     = $rowVenta['totalventa'];
                                //$totalVenta_f   = number_format($totalVenta, 2);
                                $pdf->SetFont('Times','',10);
                                $pdf->Cell(59,6,utf8_decode('TOTAL NO. ARTÍCULOS.:'),0,0,'R');
                                $pdf->SetFont('Times','B',10);
                                $pdf->Cell(10,6,utf8_decode($totalArts),0,0,'L');
                                $pdf->Cell(57,6,utf8_decode(''),0,0,'R');
                                $pdf->SetFont('Times','',10);
                                $pdf->Cell(33,6,utf8_decode('TOTAL:'),1,0,'R',0);
                                $pdf->SetFont('Times','B',10);
                                $pdf->Cell(38,6,utf8_decode('$ '.number_format($totalCompra,2,".",",")),1,1,'R',0);
                            }
                            else
                            {
                                echo "No se puede generar la consulta. Error al enlazar y/o ejecutar los parámetros del detalle de la compra.";
                                die;
                            }
                        }
                        // $pdf->Image('../images/icons/logo.jpg',7,5,-370);
                        $pdf->Image('../images/avatars/sucursales/'.$idSucursal.'/logo.jpg',7,5,-370);
                        $pdf->Output();
                    }
                    else
                    {
                        echo "El Número de Id al que deseas acceder no existe en la base de datos.";
                        die;
                    }
                }
                else
                {
                    echo "No se puede generar la consulta. Error al enlazar y/o ejecutar los parámetros de la compra.";
                    die;
                }
            }
            else
            {
                echo "No se puede generar la consulta. Error en la preparación de los parámetros de la compra.";
                die;
            }
        }
    }

?>
