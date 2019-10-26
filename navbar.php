<?php
    require_once ("assets/php/usuario.class.php");
    $usuario_nav_bar = new usuario($idUsuario,$mysqli);
?>
<div id="navbar" class="navbar navbar-default navbar-fixed-top ace-save-state">
    <div class="navbar-container ace-save-state" id="navbar-container">
        <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
            <span class="sr-only">Toggle sidebar</span>

            <span class="icon-bar"></span>

            <span class="icon-bar"></span>

            <span class="icon-bar"></span>
        </button>

        <div class="navbar-header pull-left">
            <a href="index.php" class="navbar-brand" style="padding:0px;margin-left:0px">
                <small>
                    <img src="assets/images/icons/logo_s_mono.png" width="45px"/>
                    Funeraria
                </small>
            </a>
        </div>

        <div class="navbar-buttons navbar-header pull-right" role="navigation">
            <ul class="nav ace-nav">
                <li class="warning dropdown-modal">
                    <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                        <i class="ace-icon fa fa-user-o"></i>
                        <span class="user-info">
                            <small>Bienvenido(a),</small>
                            <?php echo $sesion->get("titulo")." ".$sesion->get("nombre");?>
                        </span>

                        <i class="ace-icon fa fa-caret-down"></i>
                    </a>
                    <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                        <li>
                            <a href="salir.php">
                                <i class="ace-icon fa fa-power-off"></i>
                                Cerrar sesi&oacute;n
                            </a>
                        </li>
                        <li>
                            <a href="cambiarPassword.php">
                                <i class="ace-icon fa fa-key"></i>
                                Cambiar mi contraseña
                            </a>
                        </li>
                <?php
                if ($usuario_nav_bar->tipo == 1 || $usuario_nav_bar->tipo == 0)
                {
                    ?>
                        <li>
                            <a href="realizarRespaldo.php">
                                <i class="ace-icon fa fa-database" aria-hidden="true"></i>
                                Realizar respaldo
                            </a>
                        </li>
                    <?php
                }
                 ?>

                    </ul>
                </li>
            </ul>
        </div>
    </div><!-- /.navbar-container -->
</div>
