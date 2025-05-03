<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITENAME : SITENAME; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- FullCalendar (para vistas de calendario) -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <style>
        :root {
            /* Nueva paleta de colores más energética */
            --primary-color: #38B6FF;     /* Azul energético */
            --primary-dark: #0096E0;      /* Azul oscuro */
            --primary-darker: #0078B4;    /* Azul más oscuro */
            --primary-light: #6BCDFF;     /* Azul claro */
            --primary-lighter: #ADE0FF;   /* Azul muy claro */
            
            /* Colores complementarios */
            --accent-color: #FF5757;      /* Rojo energético para acentos */
            --secondary-color: #4F5E7B;   /* Azul grisáceo para elementos secundarios */
            --success-color: #32D583;     /* Verde para mensajes de éxito */
            --info-color: #38B6FF;        /* Azul informativo */
            --warning-color: #FFB020;     /* Ámbar para advertencias */
            --danger-color: #FF5757;      /* Rojo para errores */
            
            /* Colores neutros */
            --light-color: #F8FAFC;       /* Fondo claro */
            --dark-color: #1E293B;        /* Texto oscuro */
            --gray-light: #E2E8F0;        /* Gris claro para bordes */
            --gray-medium: #94A3B8;       /* Gris medio para textos secundarios */
        }
        
        body {
            font-family: 'Nunito', sans-serif;
            background-color: var(--light-color);
            color: var(--dark-color);
        }
        
        .navbar {
            background-color: var(--primary-color);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-dark .navbar-brand,
        .navbar-dark .nav-link {
            color: white !important;
            font-weight: 600;
        }
        
        .navbar-dark .nav-link:hover {
            color: var(--light-color) !important;
        }
        
        .sidebar .nav-item .nav-link {
            color: var(--secondary-color);
            font-weight: 500;
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
            margin-bottom: 0.25rem;
        }
        
        .sidebar .nav-item .nav-link:hover {
            color: var(--primary-darker);
            background-color: var(--primary-lighter);
        }
        
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 0.125rem 0.375rem rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.12);
        }
        
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid var(--gray-light);
            font-weight: 600;
            padding: 1.25rem 1.5rem;
            border-radius: 0.75rem 0.75rem 0 0 !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            font-weight: 600;
            border-radius: 0.5rem;
            padding: 0.5rem 1.25rem;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-darker);
            box-shadow: 0 4px 8px rgba(56, 182, 255, 0.3);
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #3A4A66;
            border-color: #2F3E59;
        }
        
        .btn-accent {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: white;
        }
        
        .btn-accent:hover {
            background-color: #E03F3F;
            border-color: #D03030;
        }
        
        .bg-primary {
            background-color: var(--primary-color) !important;
            color: white !important;
        }
        
        .text-primary {
            color: var(--primary-darker) !important;
        }
        
        .text-accent {
            color: var(--accent-color) !important;
        }
        
        .progress {
            height: 0.5rem;
            border-radius: 1rem;
            background-color: var(--gray-light);
        }
        
        .progress-bar {
            background-color: var(--primary-color);
            border-radius: 1rem;
        }
        
        .chart-area {
            height: 20rem;
        }
        
        /* Estilos para tarjetas de clases */
        .class-card {
            transition: all 0.3s ease;
        }
        
        .class-card:hover {
            transform: translateY(-5px);
        }
        
        .class-card .card-header {
            color: var(--dark-color);
            font-weight: 700;
        }
        
        /* Estilo para la tarjeta con borde izquierdo */
        .border-left-primary {
            border-left: 0.25rem solid var(--primary-color) !important;
        }
        .border-left-success {
            border-left: 0.25rem solid var(--success-color) !important;
        }
        .border-left-info {
            border-left: 0.25rem solid var(--info-color) !important;
        }
        .border-left-warning {
            border-left: 0.25rem solid var(--warning-color) !important;
        }
        .border-left-danger {
            border-left: 0.25rem solid var(--danger-color) !important;
        }
        .border-left-accent {
            border-left: 0.25rem solid var(--accent-color) !important;
        }
        
        /* Badges con la nueva paleta */
        .badge-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .badge-accent {
            background-color: var(--accent-color);
            color: white;
        }
        
        .badge-secondary {
            background-color: var(--secondary-color);
            color: white;
        }
        
        /* Estilos para notificaciones */
        .notification-item {
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
        }
        
        .notification-item:hover {
            background-color: var(--primary-lighter);
            border-left-color: var(--primary-color);
        }
        
        .notification-unread {
            border-left-color: var(--primary-color);
            background-color: rgba(56, 182, 255, 0.05);
        }
        
        /* Botón flotante para acciones rápidas */
        .floating-action-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 50%;
            background-color: var(--accent-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.2);
            z-index: 100;
            transition: all 0.3s ease;
        }
        
        .floating-action-btn:hover {
            background-color: var(--primary-color);
            transform: scale(1.1);
        }
        
        /* Personalización de tablas */
        .table {
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        .table thead th {
            background-color: rgba(56, 182, 255, 0.1);
            color: var(--primary-darker);
            border-top: none;
            border-bottom: 2px solid var(--primary-lighter);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(56, 182, 255, 0.05);
        }
    </style>
</head>
<body>
    <!-- Barra de navegación superior -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') ? URLROOT . '/admin/dashboard' : URLROOT . '/user/dashboard'; ?>"><?php echo SITENAME; ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                    <!-- Menú de Administrador -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/admin/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/admin/users">Gestionar Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/admin/registerForm">Añadir Usuario</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/admin/classes">Gestionar Clases</a>
                    </li>
                    <?php elseif(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'staff'): ?>
                    <!-- Menú de Staff -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/staff/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/staff/classes">Gestionar Clases</a>
                    </li>
                    <?php else: ?>
                    <!-- Menú de Usuario Normal -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/user/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/user/classes">Clases</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/user/tracking">Seguimiento</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/user/courts">Pistas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/user/notifications">
                            <i class="fas fa-bell me-1"></i> Notificaciones
                            <?php if(isset($unreadCount) && $unreadCount > 0): ?>
                                <span class="badge rounded-pill bg-danger"><?php echo $unreadCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                <!-- Usuario logueado -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            <?php 
                                // Inicializar UserController para obtener el conteo
                                $userController = new UserController();
                                $notificationCount = $userController->getUnreadNotificationsCount();
                                
                                if ($notificationCount > 0): 
                            ?>
                                <span class="badge rounded-pill bg-danger" id="notificationBadge"><?php echo $notificationCount; ?></span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <?php 
                                // Cargar modelo de notificaciones para mostrar las más recientes
                                require_once APPROOT . '/models/Notification.php';
                                $notificationModel = new Notification();
                                $recentNotifications = $notificationModel->getAllNotifications();
                                
                                if (empty($recentNotifications)): 
                            ?>
                                <li><a class="dropdown-item text-center" href="#">No hay notificaciones</a></li>
                            <?php else: 
                                // Mostrar hasta 3 notificaciones recientes
                                $count = 0;
                                foreach ($recentNotifications as $notification):
                                    if ($count >= 3) break;
                                    $count++;
                                    
                                    // Verificar si es un objeto o un array
                                    $title = is_object($notification) ? $notification->title : $notification['title'];
                            ?>
                                <li>
                                    <a class="dropdown-item notification-item" href="<?php echo URLROOT; ?>/user/notifications">
                                        <i class="fas fa-info-circle text-info me-2"></i>
                                        <?php echo htmlspecialchars($title); ?>
                                    </a>
                                </li>
                            <?php 
                                endforeach;
                            endif; 
                            ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center" href="<?php echo URLROOT; ?>/user/notifications"><i class="fas fa-bell me-2"></i> Ver todas las notificaciones</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Usuario'; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/user/profile"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i> Perfil</a></li>
                            <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/user/settings"><i class="fas fa-cogs fa-sm fa-fw me-2 text-gray-400"></i> Configuración</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo URLROOT; ?>/auth/logout"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i> Cerrar sesión</a></li>
                        </ul>
                    </li>
                </ul>
                <?php else: ?>
                <!-- Usuario no logueado -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/auth/login">
                            <i class="fas fa-sign-in-alt me-1"></i> Iniciar sesión
                        </a>
                    </li>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Contenido principal con espaciado para la navbar -->
    <div style="padding-top: 56px;"><?php // Este div se cierra en el footer ?>