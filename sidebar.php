<div id="sidebar" class="sidebar                  responsive                    ace-save-state">
    <script type="text/javascript">
        try{ace.settings.loadState('sidebar')}catch(e){}
    </script>

    <div class="sidebar-shortcuts" id="sidebar-shortcuts">
        <div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
            <a href="index.php" class="btn btn-success">
                <i class="ace-icon fa fa-file-text"></i>
            </a>

            <a href="listarClientes.php" class="btn btn-info">
                <i class="ace-icon fa fa-users"></i>
            </a>

            <a href="listarProductos.php" class="btn btn-warning">
                <i class="ace-icon fa fa-tags"></i>
            </a>
        </div>

        <div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
            <span class="btn btn-success"></span>

            <span class="btn btn-info"></span>

            <span class="btn btn-warning"></span>

            <span class="btn btn-danger"></span>
        </div>
    </div><!-- /.sidebar-shortcuts -->
<?php
require_once "assets/php/usuario.class.php";
$usuario_side = new usuario($sesion->get("id"),$mysqli);
?>
    <ul class="nav nav-list" id="nav-list">
<?php if($usuario_side->permiso("listarContratos",$mysqli)){?>
        <li class="">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon fa fa-file-text"></i>
                <span class="menu-text"> Contratos </span>
                <b class="arrow fa fa-angle-down"></b>
            </a>

            <b class="arrow"></b>
            <ul class="submenu">
                <li class="">
                    <a href="index.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Listar activos
                    </a>

                    <b class="arrow"></b>
                </li>
                <li class="">
                    <a href="listarContratosTodos.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Listar todos (sin filtrar)
                    </a>

                    <b class="arrow"></b>
                </li>
    <?php if($usuario_side->permiso("agregarContrato",$mysqli)){?>
                <li class="">
                    <a href="agregarContrato.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Agregar
                    </a>
                    <b class="arrow"></b>
                </li>
        <?php } ?>
            </ul>
        </li>
<?php } ?>
<?php if($usuario_side->permiso("listarVentas",$mysqli)){?>
        <li class="">
            <a href="#" class="dropdown-toggle">
                <i class="fa fa-shopping-cart menu-icon" aria-hidden="true"></i>
                <span class="menu-text"> Ventas </span>

                <b class="arrow fa fa-angle-down"></b>
            </a>
            <b class="arrow"></b>
            <ul class="submenu">
                <li class="">
                    <a href="listarVentas.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Listar
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php if($usuario_side->permiso("agregarVenta",$mysqli)){?>
                <li class="">
                    <a href="agregarVenta.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Agregar
                    </a>
                    <b class="arrow"></b>
                </li>
            <?php } ?>
            </ul>
        </li>
<?php } ?>
<?php if($usuario_side->permiso("listarProveedores",$mysqli)){?>
        <li class="">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon fa fa-truck" aria-hidden="true"></i>
                <span class="menu-text"> Proveedores </span>

                <b class="arrow fa fa-angle-down"></b>
            </a>

            <b class="arrow"></b>

            <ul class="submenu">
                <li class="">
                    <a href="listarProveedores.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Listar
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php if($usuario_side->permiso("agregarProveedor",$mysqli)){?>
                <li class="">
                    <a href="agregarProveedor.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Agregar
                    </a>
                    <b class="arrow"></b>
                </li>
            <?php } ?>
            </ul>
        </li>
<?php } ?>
<?php if($usuario_side->permiso("listarClientes",$mysqli)){?>
        <li class="">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon fa fa-users" aria-hidden="true"></i>
                <span class="menu-text"> Clientes </span>

                <b class="arrow fa fa-angle-down"></b>
            </a>

            <b class="arrow"></b>

            <ul class="submenu">
                <li class="">
                    <a href="listarClientes.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Listar
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php if($usuario_side->permiso("agregarCliente",$mysqli)){?>
                <li class="">
                    <a href="agregarCliente.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Agregar
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php } ?>
            </ul>
        </li>
<?php } ?>
<?php if($usuario_side->permiso("listarDifuntos",$mysqli)){?>
        <li class="">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon fa fa-user-circle-o" aria-hidden="true"></i>
                <span class="menu-text"> Difuntos </span>

                <b class="arrow fa fa-angle-down"></b>
            </a>

            <b class="arrow"></b>

            <ul class="submenu">
                <li class="">
                    <a href="listarDifuntos.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Listar
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php if($usuario_side->permiso("agregarDifunto",$mysqli)){?>
                <li class="">
                    <a href="agregarDifunto.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Agregar
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php } ?>
            </ul>
        </li>
<?php } ?>

<?php if($usuario_side->permiso("listarProductos",$mysqli)){?>
        <li class="">
            <a href="#" class="dropdown-toggle">
                <i class="fa fa-tags menu-icon" aria-hidden="true"></i>
                <span class="menu-text"> Productos </span>

                <b class="arrow fa fa-angle-down"></b>
            </a>

            <b class="arrow"></b>

            <ul class="submenu">
                <li class="">
                    <a href="listarProductos.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Listar
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php if($usuario_side->permiso("agregarProducto",$mysqli)){?>
                <li class="">
                    <a href="agregarProducto.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Agregar
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php } ?>
            </ul>
        </li>
<?php } ?>
<?php if($usuario_side->permiso("listarServicios",$mysqli)){?>
        <li class="">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon fa fa-cubes" aria-hidden="true"></i>
                <span class="menu-text"> Servicios </span>

                <b class="arrow fa fa-angle-down"></b>
            </a>

            <b class="arrow"></b>
            <ul class="submenu">
                <li class="">
                    <a href="listarServicios.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Listar
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php if($usuario_side->permiso("agregarServicio",$mysqli)){?>
                <li class="">
                    <a href="agregarServicio.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Agregar
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php } ?>
            </ul>
        </li>
<?php } ?>
<?php if($usuario_side->permiso("listarCompras",$mysqli)){?>
        <li class="">
            <a href="#" class="dropdown-toggle">
                <i class="fa fa-shopping-bag menu-icon" aria-hidden="true"></i>
                <span class="menu-text"> Compras </span>

                <b class="arrow fa fa-angle-down"></b>
            </a>
            <b class="arrow"></b>
            <ul class="submenu">
                <li class="">
                    <a href="listarCompras.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Listar
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php if($usuario_side->permiso("agregarCompra",$mysqli)){?>
                <li class="">
                    <a href="agregarCompra.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Agregar
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php } ?>
            </ul>
        </li>
<?php } ?>
<?php if($usuario_side->permiso("listarPlanes",$mysqli)){?>
        <li class="">
            <a href="#" class="dropdown-toggle">
                <i class="fa fa-object-group menu-icon" aria-hidden="true"></i>
                <span class="menu-text"> Planes funerarios </span>

                <b class="arrow fa fa-angle-down"></b>
            </a>

            <b class="arrow"></b>

            <ul class="submenu">
                <li class="">
                    <a href="listarPlanes.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        listar
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php if($usuario_side->permiso("agregarPlan",$mysqli)){?>
                <li class="">
                    <a href="agregarPlan.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Agregar
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php } ?>
            </ul>
        </li>
<?php } ?>
        <li class="">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon fa fa-bolt" aria-hidden="true"></i>
                <span class="menu-text"> Facturas </span>

                <b class="arrow fa fa-angle-down"></b>
            </a>

            <b class="arrow"></b>

            <ul class="submenu">
                <li class="">
                    <a href="listarFacturas.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Listar
                    </a>
                    <b class="arrow"></b>
                </li>
            </ul>
        </li>
        <?php //} ?>
        <li class="">
            <a href="#" class="dropdown-toggle">
                <i class="fa fa-bar-chart menu-icon" aria-hidden="true"></i>
                <span class="menu-text"> Reportes </span>

                <b class="arrow fa fa-angle-down"></b>
            </a>

            <b class="arrow"></b>

            <ul class="submenu">
                <li class="">
                    <a href="reporteCobranza.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Cobranza
                    </a>

                    <b class="arrow"></b>
                </li>
                <li class="">
                    <a href="foliosCobranzaAsignados.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Folios de cobranza asignados
                    </a>

                    <b class="arrow"></b>
                </li>
                <li class="">
                    <a href="foliosCobranza.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Folios de cobranza (Sin filtrar)
                    </a>

                    <b class="arrow"></b>
                </li>
                <li class="">
                    <a href="reporteCobranza_cobradores.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Reporte de cobranza y comisión por cobrador
                    </a>

                    <b class="arrow"></b>
                </li>
                <li class="">
                    <a href="reporteCobranza_vendedores.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Reporte de cobranza y comisión por vendedor
                    </a>

                    <b class="arrow"></b>
                </li>
            </ul>
        </li>
<?php if($usuario_side->permiso("listarUsuarios",$mysqli)){?>
        <li class="">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon fa fa-user" aria-hidden="true"></i>
                <span class="menu-text"> Usuarios </span>

                <b class="arrow fa fa-angle-down"></b>
            </a>

            <b class="arrow"></b>

            <ul class="submenu">
                <li class="">
                    <a href="listarUsuarios.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Listar
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php if($usuario_side->permiso("agregarUsuario",$mysqli)){?>
                <li class="">
                    <a href="agregarUsuario.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Agregar
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php } ?>
            </ul>
        </li>
<?php } ?>

<?php if($usuario_side->permiso("listarNominas",$mysqli)){?>
		<li class="">
            <a href="#" class="dropdown-toggle">
                <i class="menu-icon fa fa-columns" aria-hidden="true"></i>
                <span class="menu-text"> N&oacute;minas </span>

                <b class="arrow fa fa-angle-down"></b>
            </a>

            <b class="arrow"></b>

            <ul class="submenu">
                <li class="">
                    <a href="listarPeriodosNominas.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Listar periodos
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php if($usuario_side->permiso("agregarNomina",$mysqli)){?>
                <li class="">
                    <a href="agregarPeriodoNomina.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Agregar periodo
                    </a>

                    <b class="arrow"></b>
                </li>
            <?php } ?>
            </ul>
        </li>

    <?php } ?>
	<?php if($usuario_side->permiso("listarVariablesSistema",$mysqli)){?>

	<li class="">
		<a href="#" class="dropdown-toggle">
			<i class="menu-icon fa fa-cogs" aria-hidden="true"></i>
			<span class="menu-text"> Configuraci&oacute;n </span>

			<b class="arrow fa fa-angle-down"></b>
		</a>

		<b class="arrow"></b>

		<ul class="submenu">
			<li class="">
				<a href="variablesSistema.php">
					<i class="menu-icon fa fa-caret-right"></i>
					Variables del sistema
				</a>

				<b class="arrow"></b>
			</li>
		</ul>
	</li>
<?php } ?>

        <!-- <li class="">
            <a href="#" class="dropdown-toggle">
                <i class="fa fa-cog menu-icon" aria-hidden="true"></i>
                <span class="menu-text"> Configuraci&oacute;n </span>

                <b class="arrow fa fa-angle-down"></b>
            </a>

            <b class="arrow"></b>

            <ul class="submenu">
                <li class="">
                    <a href="parametros.php">
                        <i class="menu-icon fa fa-caret-right"></i>
                        Parámetros
                    </a>

                    <b class="arrow"></b>
                </li>
            </ul>
        </li> -->
    </ul><!-- /.nav-list -->

    <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
        <i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
    </div>
</div>
