<!-- Sidebar menu-->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
    <div class="app-sidebar__user"><img class="app-sidebar__user-avatar" src="<?= media(); ?>images/uploads/logo-icon3.png" alt="User Image">
        <div>
            <p class="app-sidebar__user-name"><?= $_SESSION["userDATA"]["NOMBRE"]; ?></p>
            <p class="app-sidebar__user-designation"><?= $_SESSION["userDATA"]["CARGO_DESCRIPCION"]; ?></p>
        </div>
    </div>
    <ul class="app-menu">
        <li><a class="app-menu__item" href="<?= base_url(); ?>dashboard"><i class="app-menu__icon fa fa-home"></i><span class="app-menu__label">Dashboard</span></a></li>
        <li><a class="app-menu__item" href="<?= base_url(); ?>ventas"><i class="app-menu__icon fa fa-shopping-cart" aria-hidden="true"></i><span class="app-menu__label">Ventas</span></a></li>
        <!-- <li><a class="app-menu__item" href="<?//= base_url(); ?>compras"><i class="app-menu__icon fa fa-shopping-cart" aria-hidden="true"></i><span class="app-menu__label">Compras</span></a></li> -->
        <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-users" aria-hidden="true"></i><span class="app-menu__label">Personas</span><i class="treeview-indicator fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item" href="<?= base_url(); ?>cargos"><i class="icon fa fa-circle-o"></i> Cargos</a></li>
                <li><a class="treeview-item" href="<?= base_url(); ?>clientes"><i class="icon fa fa-circle-o"></i> Clientes</a></li>
                <li><a class="treeview-item" href="<?= base_url(); ?>empleados"><i class="icon fa fa-circle-o"></i> Empleados</a></li>
                <!-- <li><a class="treeview-item" href="<?//= base_url(); ?>proveedores"><i class="icon fa fa-circle-o"></i> Proveedores</a></li> -->
                <!-- <li><a class="treeview-item" href="<?//= base_url(); ?>sucursales"><i class="icon fa fa-circle-o"></i> Sucursales</a></li> -->
            </ul>
        </li>
        <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-archive" aria-hidden="true"></i><span class="app-menu__label">Inventario</span><i class="treeview-indicator fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item" href="<?= base_url(); ?>rubros"><i class="icon fa fa-circle-o"></i> Rubros</a></li>
                <li><a class="treeview-item" href="<?= base_url(); ?>udMedidas"><i class="icon fa fa-circle-o"></i> Unidades de Medidas</a></li>
                <li><a class="treeview-item" href="<?= base_url(); ?>productos"><i class="icon fa fa-circle-o"></i> Productos</a></li>
                <!-- <li><a class="treeview-item" href="<?//= base_url(); ?>productosVentas"><i class="icon fa fa-circle-o"></i> A la Venta</a></li> -->
                <!-- <li><a class="treeview-item" href="<?//= base_url(); ?>cargos"><i class="icon fa fa-circle-o"></i> Recetas</a></li> -->
            </ul>
        </li>
        <!-- <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-bar-chart" aria-hidden="true"></i><span class="app-menu__label">Informes</span><i class="treeview-indicator fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item" href="<?//= base_url(); ?>clientes"><i class="icon fa fa-circle-o"></i> Clientes</a></li>
            </ul>
        </li> -->
        <!-- <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-eye" aria-hidden="true"></i><span class="app-menu__label">Auditorias</span><i class="treeview-indicator fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item" href="<?//= base_url(); ?>clientes"><i class="icon fa fa-circle-o"></i> Clientes</a></li>
            </ul>
        </li> -->
        <!-- <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-money" aria-hidden="true"></i><span class="app-menu__label">Tesorería</span><i class="treeview-indicator fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item" href="<?//= base_url(); ?>clientes"><i class="icon fa fa-circle-o"></i> Clientes</a></li>
            </ul>
        </li> -->
        <!-- <li><a class="app-menu__item" href="<?//= base_url(); ?>dashboard"><i class="app-menu__icon fa fa-cog"></i><span class="app-menu__label">Opciones</span></a></li> -->
        <li><a class="app-menu__item" href="<?= base_url();?>logout"><i class="app-menu__icon fa fa-sign-out"></i><span class="app-menu__label"> Salir</span></a></li>
</aside>