<?php
// Esta es la página de error 404 que se muestra cuando no se encuentra una página
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada - <?php echo SITENAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-container {
            max-width: 600px;
            margin: 100px auto;
            text-align: center;
        }
        .error-code {
            font-size: 120px;
            color: #dc3545;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 20px;
        }
        .error-message {
            font-size: 24px;
            margin-bottom: 30px;
        }
        .error-description {
            margin-bottom: 30px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container">
            <div class="error-code">404</div>
            <div class="error-message">Página no encontrada</div>
            <div class="error-description">
                Lo sentimos, la página que estás buscando no existe o ha sido movida.
            </div>
            <a href="<?php echo URLROOT; ?>" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Volver al inicio
            </a>
        </div>
    </div>
</body>
</html>