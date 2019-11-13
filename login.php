<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
require ('assets/connect/bd.php');
require ("assets/connect/sesion.class.php");
$sesion = new sesion();
require ('assets/connect/validarUsuario.php');
$validar = TRUE;
function getUserIpAddress()
{

    foreach ( [ 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR' ] as $key )
	{

        // Comprobamos si existe la clave solicitada en el array de la variable $_SERVER
        if ( array_key_exists( $key, $_SERVER ) )
		{

            // Eliminamos los espacios blancos del inicio y final para cada clave que existe en la variable $_SERVER
            foreach ( array_map( 'trim', explode( ',', $_SERVER[ $key ] ) ) as $ip )
			{

                // Filtramos* la variable y retorna el primero que pase el filtro
                if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false ) {
                    return $ip;
                }
            }
        }
    }

    return '?'; // Retornamos '?' si no hay ninguna IP o no pase el filtro
}
if( isset($_POST["btnIniciar"]) )
{
    require_once "assets/php/funcionesVarias.php";
    $usuario                = validarFormulario('s',$_POST["usuario"]);
    $password               = validarFormulario('s',$_POST["password"]);
    $validar                = validarUsuario($usuario, $password, $mysqli);
    if($validar             != FALSE)
    {
        $idUsuario          = $validar['id'];
        $fechaLogin         = date("Y-m-d H:i:s");
        $sql                = "SELECT id FROM sesionescontrol WHERE usuario = $idUsuario AND timestampsalida IS NULL AND activo = 1 LIMIT 1";
        $result             = $mysqli->query($sql);
        $sesionCambiar      = 0;
        if($result->num_rows > 0)
        {
            $rowUsr         = $result->fetch_assoc();
            $sesionCambiar  = $rowUsr['id'];
            $sql            = "UPDATE sesionescontrol SET timestampsalida = '$fechaLogin', activo = 0, estado = 0 WHERE usuario = $idUsuario AND activo = 1";
            $mysqli->query($sql);
        }
        $sql                = "INSERT INTO sesionescontrol (timestampentrada, usuario, activo)
                                VALUES ('$fechaLogin', $idUsuario, 1)";
        $mysqli->query($sql);
        $idSesion           =       $mysqli->insert_id;
        /*if($sesionCambiar != 0)
        {*/

        // $sql        = "UPDATE retiros SET sesion = $idSesion WHERE usuario = $idUsuario AND corte = 0";
        // $mysqli->query($sql);
        // $sql        = "UPDATE compras SET sesion = $idSesion WHERE usuario = $idUsuario AND corte = 0";
        // $mysqli->query($sql);
        // $sql        = "UPDATE tickets SET sesionIngreso = $idSesion WHERE usuarioIngreso = $idUsuario AND corte = 0";
        // $mysqli->query($sql);
        // $sql        = "UPDATE tickets SET sesionSalida = $idSesion WHERE usuarioIngreso = $idUsuario AND corte = 0 AND sesionSalida > 0";
        // $mysqli->query($sql);
        // $sql        = "UPDATE pagosrecibidos SET sesion = $idSesion WHERE usuario = $idUsuario AND corte = 0";
        // $mysqli->query($sql);
        // $sql        = "UPDATE pagosemitidos SET sesion = $idSesion WHERE usuario = $idUsuario AND corte = 0";
        // $mysqli->query($sql);
        // $sql        = "UPDATE notacredito SET sesion = $idSesion WHERE usuario = $idUsuario AND corte = 0";
        // $mysqli->query($sql);
        // $sql        = "UPDATE notadesalida SET sesion = $idSesion WHERE usuario = $idUsuario AND corte = 0";
        // $mysqli->query($sql);

        //}
        $sesion->set("id",          $idUsuario);
        $sesion->set("idsesion",    $idSesion);
        $sesion->set("nombre",      $validar['nombres']);
        $sesion->set("apellidop",   $validar['apellidop']);
        $sesion->set("apellidom",   $validar['apellidom']);
        $sesion->set("tipousuario", $validar['tipo']);
        $sesion->set("nick",        $validar['nick']);
        $sesion->set("email",       $validar['email']);
		$sesion->set("celular",     $validar['cel']);
		$sesion->set("titulo",     	$validar['titulo']);
        $sesion->set("ip",     	getUserIpAddress());

        header("location: index.php");
    }
}
if ($sesion->get("nick")!=false)
    header("location: index.php");
 ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>Iniciar sesi&oacute;n</title>

		<meta name="description" content="User login page" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<!-- bootstrap & fontawesome -->
		<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="assets/font-awesome/4.5.0/css/font-awesome.min.css" />

		<!-- text fonts -->
		<link rel="stylesheet" href="assets/css/fonts.googleapis.com.css" />

		<!-- ace styles -->
		<link rel="stylesheet" href="assets/css/ace.min.css" />

		<!--[if lte IE 9]>
			<link rel="stylesheet" href="assets/css/ace-part2.min.css" />
		<![endif]-->
		<link rel="stylesheet" href="assets/css/ace-rtl.min.css" />

		<!--[if lte IE 9]>
		  <link rel="stylesheet" href="assets/css/ace-ie.min.css" />
		<![endif]-->

		<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

		<!--[if lte IE 8]>
		<script src="assets/js/html5shiv.min.js"></script>
		<script src="assets/js/respond.min.js"></script>
		<![endif]-->
        <style>
            body
            {
                /* background-image: url('assets/images/icons/fondo1-funeraria.jpg'); */

                background-size: contain;
            }

        </style>
	</head>

	<body class="login-layout light-login" style="background-image:url('assets/images/icons/fondo1-funeraria.jpg');background-repeat: no-repeat;background-size: cover;">
		<div class="main-container">
			<div class="main-content">
				<div class="row">
					<div class="col-sm-10 col-sm-offset-1">
						<div class="login-container">

                            <div class="space-20"></div>
							<div class="position-relative">
								<div id="login-box" class="login-box visible widget-box no-border">
									<div class="widget-body">
										<div class="widget-main">
                                            <div class="center">
                								<h1>
                                                    <span class="blue">Portal</span>
                									<span class="green">funeraria</span>
                								</h1>
                                                <img src="assets/images/icons/logo.png" class="img-responsive">
                							</div>
<?php
if ($validar)
{
?>
                                            <h4 class="header blue lighter bigger">
                                                <i class="ace-icon fa fa-key green"></i>
                                                Iniciar sesi&oacute;n
                                            </h4>
<?php
}
else
{
?>
                                            <h4 class="header red lighter bigger">
                                                <i class="ace-icon fa fa-key red"></i>
                                                Datos incorrectos
                                            </h4>
<?php
}
 ?>

											<div class="space-6"></div>

											<form onsubmit="javascript:submitt();" method="POST" id="form">
												<fieldset>
													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="text" autofocus name="usuario" autocomplete="off" class="form-control" placeholder="Nombre o e-mail" />
															<i class="ace-icon fa fa-user"></i>
														</span>
													</label>

													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="password" name="password" class="form-control" placeholder="Contraseña" />
															<i class="ace-icon fa fa-lock"></i>
														</span>
													</label>

													<div class="space"></div>

													<div class="clearfix" id="divBtn">
														<button id="btnIniciar" name="btnIniciar" class="width-35 pull-right btn btn-sm btn-success btn-app">
															<i class="ace-icon fa fa-sign-in"></i>
															<span class="bigger-110">Ingresar</span>
														</button>
													</div>
                                                    <div class="clearfix" id="divBtnLoad" style="display:none">
														<button id="" name="" disabled class="width-35 pull-right btn btn-sm btn-success btn-app">
                                                            <i class="ace-icon fa fa-spinner fa-pulse"></i>
                                                            <span class="bigger-110">Iniciando...</span>
                                                        </button>
													</div>

													<div class="space-4"></div>
												</fieldset>
											</form>
										</div><!-- /.widget-main -->

										<div class="toolbar clearfix">

										</div>
									</div><!-- /.widget-body -->
								</div><!-- /.login-box -->

							</div><!-- /.position-relative -->
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.main-content -->
		</div><!-- /.main-container -->

		<!-- basic scripts -->
		<script src="assets/js/jquery-2.1.4.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/jquery-ui.custom.min.js"></script>
		<script type="text/javascript">
			if('ontouchstart' in document.documentElement) document.write("<script src='assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
		</script>
		<!-- inline scripts related to this page -->
		<script type="text/javascript">
            function submitt()
            {
                $("#divBtn").hide();
                $("#divBtnLoad").show();
            }
		</script>
	</body>
</html>
