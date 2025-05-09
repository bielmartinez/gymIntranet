# GymIntranet

Sistema de gestión interna para gimnasios.

## Reestructuración del Código

Se ha reestructurado el código siguiendo el patrón MVC (Modelo-Vista-Controlador) para mejorar la legibilidad, mantenibilidad y organización del proyecto.

### Estructura de Carpetas

```
gymIntranet/
├── app/
│   ├── config/        # Configuración de la aplicación
│   ├── controllers/   # Controladores que manejan la lógica de negocio
│   ├── libraries/     # Bibliotecas y clases de utilidad
│   ├── models/        # Modelos para interactuar con la base de datos
│   ├── utils/         # Utilidades y funciones auxiliares
│   └── views/         # Vistas (presentación HTML)
│       ├── admin/     # Vistas para administradores
│       ├── auth/      # Vistas de autenticación
│       ├── shared/    # Componentes compartidos (header, footer)
│       ├── staff/     # Vistas para personal del gimnasio
│       └── users/     # Vistas para usuarios regulares
│
└── public/            # Archivos públicos accesibles desde el navegador
    ├── css/           # Archivos CSS organizados por secciones
    │   ├── admin/     # Estilos específicos para admin
    │   ├── shared/    # Estilos compartidos
    │   └── user/      # Estilos específicos para usuarios
    ├── js/            # Archivos JavaScript organizados por secciones
    │   ├── admin/     # Scripts específicos para admin
    │   ├── shared/    # Scripts compartidos
    │   └── user/      # Scripts específicos para usuarios
    └── uploads/       # Archivos subidos por usuarios
```

## Cambios Realizados

### Separación de Código

- **PHP**: Los archivos PHP se mantienen en sus ubicaciones originales bajo la carpeta `app/`, siguiendo la estructura MVC.
- **CSS**: Los estilos se han movido a `public/css/` y se han organizado por sección.
- **JavaScript**: El código JavaScript se ha trasladado a `public/js/` y se ha organizado por sección.

### Beneficios

1. **Mantenibilidad**: Es más fácil encontrar y modificar código específico.
2. **Rendimiento**: Los navegadores pueden cachear mejor los archivos estáticos (CSS y JS) separados.
3. **Organización**: Clara separación de responsabilidades.
4. **Escalabilidad**: Estructura preparada para crecer sin volverse caótica.

### Convenciones de Nomenclatura

- Los archivos CSS siguen el patrón: `seccion.css` o `funcionalidad.css`
- Los archivos JS siguen el patrón: `seccion.js` o `funcionalidad.js`
- Los nombres de archivo utilizan minúsculas y guiones (`-`) para separar palabras

## Uso

Para incluir archivos CSS y JS en las vistas:

```php
<!-- Incluir CSS -->
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/[carpeta]/[archivo].css">

<!-- Incluir JavaScript -->
<script src="<?php echo URLROOT; ?>/js/[carpeta]/[archivo].js"></script>
```

## Variables Globales en JavaScript

Para acceder a variables PHP desde JavaScript:

```php
<script>
  // Variable global para el controlador
  const URLROOT = '<?php echo URLROOT; ?>';
</script>
```