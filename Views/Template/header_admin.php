<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="description" content="SoftBoos - Sistema de Gestion de Comercio y Tienda Virtual.">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Leandro Boos">
    <meta name="theme-color" content="#009688">
    <title><?= $data['page_tag']; ?></title>
    <link rel="shortcut icon" href="<?= media();?>images/uploads/logo-icon3.png" type="image/x-icon">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="<?= media(); ?>css/bootstrap/dist/css/bootstrap.min.css">
    <!-- <link rel="stylesheet" type="text/css" href="<?//= media(); ?>js/puglins/DataTables/datatables.min.css"/> -->
    <link rel="stylesheet" type="text/css" href="<?= media(); ?>css/main.css">
    <link rel="stylesheet" type="text/css" href="<?= media(); ?>css/select-bootstrap/css/bootstrap-select.min.css">
    <link rel="stylesheet" type="text/css" href="<?= media(); ?>css/style.css">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css" href="<?= media(); ?>css/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body class="app sidebar-mini">
    <!-- Navbar-->
    <header class="app-header"><a class="app-header__logo" href="<?= base_url();?>dashboard">SoftBoos</a>
        <!-- Sidebar toggle button--><a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
        <!-- Navbar Right Menu-->
        <ul class="app-nav">
            <!-- <li class="app-search">
                <input class="app-search__input" type="search" placeholder="Buscar">
                <button class="app-search__button"><i class="fa fa-search"></i></button>
            </li> -->
            <!--Notification Menu-->
            <li class="dropdown"><a class="app-nav__item" href="#" data-toggle="dropdown" aria-label="Show notifications"><i class="fa fa-bell-o fa-lg"></i></a>
                <ul class="app-notification dropdown-menu dropdown-menu-right">
                    <li class="app-notification__title">Tienes 4 nuevas notificaciones.</li>
                    <div class="app-notification__content">
                        <li><a class="app-notification__item" href="javascript:;"><span class="app-notification__icon"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x text-primary"></i><i class="fa fa-envelope fa-stack-1x fa-inverse"></i></span></span>
                                <div>
                                    <p class="app-notification__message">Lisa te envió un mail</p>
                                    <p class="app-notification__meta">hace 2 min</p>
                                </div>
                            </a></li>
                        <li><a class="app-notification__item" href="javascript:;"><span class="app-notification__icon"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x text-danger"></i><i class="fa fa-hdd-o fa-stack-1x fa-inverse"></i></span></span>
                                <div>
                                    <p class="app-notification__message">El servior de correo no esta trabajando</p>
                                    <p class="app-notification__meta">hace 5 min</p>
                                </div>
                            </a></li>
                        <li><a class="app-notification__item" href="javascript:;"><span class="app-notification__icon"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x text-success"></i><i class="fa fa-money fa-stack-1x fa-inverse"></i></span></span>
                                <div>
                                    <p class="app-notification__message">Transaccion completa</p>
                                    <p class="app-notification__meta">hace 2 días</p>
                                </div>
                            </a></li>
                        <div class="app-notification__content">
                            <li><a class="app-notification__item" href="javascript:;"><span class="app-notification__icon"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x text-primary"></i><i class="fa fa-envelope fa-stack-1x fa-inverse"></i></span></span>
                                    <div>
                                        <p class="app-notification__message">Lisa te envió un mail</p>
                                        <p class="app-notification__meta">hace 2 min</p>
                                    </div>
                                </a></li>
                            <li><a class="app-notification__item" href="javascript:;"><span class="app-notification__icon"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x text-danger"></i><i class="fa fa-hdd-o fa-stack-1x fa-inverse"></i></span></span>
                                    <div>
                                        <p class="app-notification__message">El servior de correo no esta trabajando</p>
                                        <p class="app-notification__meta">hace 5 min</p>
                                    </div>
                                </a></li>
                            <li><a class="app-notification__item" href="javascript:;"><span class="app-notification__icon"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x text-success"></i><i class="fa fa-money fa-stack-1x fa-inverse"></i></span></span>
                                    <div>
                                        <p class="app-notification__message">Transaccion completa</p>
                                        <p class="app-notification__meta">hace 2 días</p>
                                    </div>
                                </a></li>
                        </div>
                    </div>
                    <li class="app-notification__footer"><a href="#">Ver todas las notificaciones.</a></li>
                </ul>
            </li>
            <!-- User Menu-->
            <li class="dropdown"><a class="app-nav__item" href="#" data-toggle="dropdown" aria-label="Open Profile Menu"><i class="fa fa-user fa-lg"></i></a>
                <ul class="dropdown-menu settings-menu dropdown-menu-right">
                    <li><a class="dropdown-item" href="<?= base_url();?>opciones"><i class="fa fa-cog fa-lg"></i> Configuracion</a></li>
                    <li><a class="dropdown-item" href="<?= base_url();?>empleados/perfil"><i class="fa fa-user fa-lg"></i> Perfil</a></li>
                    <li><a class="dropdown-item" href="<?= base_url();?>logout"><i class="fa fa-sign-out fa-lg"></i> Salir</a></li>
                </ul>
            </li>
        </ul>
    </header>
<?php require_once("nav_admin.php");