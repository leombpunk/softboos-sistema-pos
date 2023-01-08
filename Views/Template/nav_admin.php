<!-- Sidebar menu-->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
    <div class="app-sidebar__user"><img class="app-sidebar__user-avatar" src="<?= media(); ?>images/uploads/logo-icon3.png" alt="User Image">
        <div>
            <p class="app-sidebar__user-name"><?= $_SESSION["userDATA"]["APELLIDO"]." ".$_SESSION["userDATA"]["NOMBRE"]; ?></p>
            <p class="app-sidebar__user-designation"><?= $_SESSION["userDATA"]["CARGO_DESCRIPCION"]; ?></p>
        </div>
    </div>
    <ul class="app-menu">
        <li><a class="app-menu__item" href="<?= base_url(); ?>dashboard"><i class="app-menu__icon fa fa-home"></i><span class="app-menu__label">Dashboard</span></a></li>
        <li><a class="app-menu__item" href="#"><i class="app-menu__icon fa fa-shopping-cart" aria-hidden="true"></i><span class="app-menu__label">Ventas</span></a></li>
        <li><a class="app-menu__item" href="#"><i class="app-menu__icon fa fa-shopping-cart" aria-hidden="true"></i><span class="app-menu__label">Compras</span></a></li>
        <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-users" aria-hidden="true"></i><span class="app-menu__label">Personas</span><i class="treeview-indicator fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item" href="<?= base_url(); ?>cargos"><i class="icon fa fa-circle-o"></i> Cargos</a></li>
                <li><a class="treeview-item" href="<?= base_url(); ?>clientes"><i class="icon fa fa-circle-o"></i> Clientes</a></li>
                <li><a class="treeview-item" href="<?= base_url(); ?>empleados"><i class="icon fa fa-circle-o"></i> Empleados</a></li>
                <li><a class="treeview-item" href="<?= base_url(); ?>proveedores"><i class="icon fa fa-circle-o"></i> Proveedores</a></li>
                <li><a class="treeview-item" href="<?= base_url(); ?>sucursales"><i class="icon fa fa-circle-o"></i> Sucursales</a></li>
            </ul>
        </li>
        <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-shopping-cart" aria-hidden="true"></i><span class="app-menu__label">Inventario</span><i class="treeview-indicator fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item" href="<?= base_url(); ?>rubros"><i class="icon fa fa-circle-o"></i> Rubros</a></li>
                <li><a class="treeview-item" href="<?= base_url(); ?>udMedidas"><i class="icon fa fa-circle-o"></i> Unidades de Medidas</a></li>
                <li><a class="treeview-item" href="<?= base_url(); ?>productos"><i class="icon fa fa-circle-o"></i> Productos</a></li>
                <li><a class="treeview-item" href="<?= base_url(); ?>productosVentas"><i class="icon fa fa-circle-o"></i> A la Venta</a></li>
                <li><a class="treeview-item" href="<?= base_url(); ?>cargos"><i class="icon fa fa-circle-o"></i> Recetas</a></li>
            </ul>
        </li>
        <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-bar-chart" aria-hidden="true"></i><span class="app-menu__label">Informes</span><i class="treeview-indicator fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item" href="<?= base_url(); ?>clientes"><i class="icon fa fa-circle-o"></i> Clientes</a></li>
                <li><a class="treeview-item" href="<?= base_url(); ?>empleados"><i class="icon fa fa-circle-o"></i> Empleados</a></li>
                <li><a class="treeview-item" href="<?= base_url(); ?>proveedores"><i class="icon fa fa-circle-o"></i> Proveedores</a></li>
                <li><a class="treeview-item" href="<?= base_url(); ?>cargos"><i class="icon fa fa-circle-o"></i> Cargos</a></li>
            </ul>
        </li>
        <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-eye" aria-hidden="true"></i><span class="app-menu__label">Auditorias</span><i class="treeview-indicator fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item" href="<?= base_url(); ?>clientes"><i class="icon fa fa-circle-o"></i> Clientes</a></li>
                <li><a class="treeview-item" href="<?= base_url(); ?>empleados"><i class="icon fa fa-circle-o"></i> Empleados</a></li>
                <li><a class="treeview-item" href="<?= base_url(); ?>proveedores"><i class="icon fa fa-circle-o"></i> Proveedores</a></li>
                <li><a class="treeview-item" href="<?= base_url(); ?>cargos"><i class="icon fa fa-circle-o"></i> Cargos</a></li>
            </ul>
        </li>
        <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-money" aria-hidden="true"></i><span class="app-menu__label">Tesorería</span><i class="treeview-indicator fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item" href="<?= base_url(); ?>clientes"><i class="icon fa fa-circle-o"></i> Clientes</a></li>
                <li><a class="treeview-item" href="<?= base_url(); ?>empleados"><i class="icon fa fa-circle-o"></i> Empleados</a></li>
                <li><a class="treeview-item" href="<?= base_url(); ?>proveedores"><i class="icon fa fa-circle-o"></i> Proveedores</a></li>
                <li><a class="treeview-item" href="<?= base_url(); ?>cargos"><i class="icon fa fa-circle-o"></i> Cargos</a></li>
            </ul>
        </li>
        <li><a class="app-menu__item" href="<?= base_url(); ?>dashboard"><i class="app-menu__icon fa fa-cog"></i><span class="app-menu__label">Opciones</span></a></li>
        <!-- <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-laptop"></i><span class="app-menu__label">UI Elements</span><i class="treeview-indicator fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item" href="bootstrap-components.html"><i class="icon fa fa-circle-o"></i> Bootstrap Elements</a></li>
                <li><a class="treeview-item" href="https://fontawesome.com/v4.7.0/icons/" target="_blank" rel="noopener"><i class="icon fa fa-circle-o"></i> Font Icons</a></li>
                <li><a class="treeview-item" href="ui-cards.html"><i class="icon fa fa-circle-o"></i> Cards</a></li>
                <li><a class="treeview-item" href="widgets.html"><i class="icon fa fa-circle-o"></i> Widgets</a></li>
            </ul>
        </li>
        <li><a class="app-menu__item" href="charts.html"><i class="app-menu__icon fa fa-pie-chart"></i><span class="app-menu__label">Charts</span></a></li>
        <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-edit"></i><span class="app-menu__label">Forms</span><i class="treeview-indicator fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item" href="form-components.html"><i class="icon fa fa-circle-o"></i> Form Components</a></li>
                <li><a class="treeview-item" href="form-custom.html"><i class="icon fa fa-circle-o"></i> Custom Components</a></li>
                <li><a class="treeview-item" href="form-samples.html"><i class="icon fa fa-circle-o"></i> Form Samples</a></li>
                <li><a class="treeview-item" href="form-notifications.html"><i class="icon fa fa-circle-o"></i> Form Notifications</a></li>
            </ul>
        </li>
        <li class="treeview"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-th-list"></i><span class="app-menu__label">Tables</span><i class="treeview-indicator fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item" href="table-basic.html"><i class="icon fa fa-circle-o"></i> Basic Tables</a></li>
                <li><a class="treeview-item" href="table-data-table.html"><i class="icon fa fa-circle-o"></i> Data Tables</a></li>
            </ul>
        </li>
        <li class="treeview is-expanded"><a class="app-menu__item" href="#" data-toggle="treeview"><i class="app-menu__icon fa fa-file-text"></i><span class="app-menu__label">Pages</span><i class="treeview-indicator fa fa-angle-right"></i></a>
            <ul class="treeview-menu">
                <li><a class="treeview-item active" href="blank-page.html"><i class="icon fa fa-circle-o"></i> Blank Page</a></li>
                <li><a class="treeview-item" href="page-login.html"><i class="icon fa fa-circle-o"></i> Login Page</a></li>
                <li><a class="treeview-item" href="page-lockscreen.html"><i class="icon fa fa-circle-o"></i> Lockscreen Page</a></li>
                <li><a class="treeview-item" href="page-user.html"><i class="icon fa fa-circle-o"></i> User Page</a></li>
                <li><a class="treeview-item" href="page-invoice.html"><i class="icon fa fa-circle-o"></i> Invoice Page</a></li>
                <li><a class="treeview-item" href="page-calendar.html"><i class="icon fa fa-circle-o"></i> Calendar Page</a></li>
                <li><a class="treeview-item" href="page-mailbox.html"><i class="icon fa fa-circle-o"></i> Mailbox</a></li>
                <li><a class="treeview-item" href="page-error.html"><i class="icon fa fa-circle-o"></i> Error Page</a></li>
            </ul>
        </li> 
        <li><a class="app-menu__item" href="docs.html"><i class="app-menu__icon fa fa-file-code-o"></i><span class="app-menu__label">Docs</span></a></li>
    </ul> -->
</aside>