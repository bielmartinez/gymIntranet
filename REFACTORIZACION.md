# Plan de Refactorización

Este documento contiene los siguientes pasos para continuar con la refactorización del código de GymIntranet.

## Estado Actual
✅ Refactorización de Vistas: Completado  
✅ Refactorización de Modelos: Completado  
✅ Refactorización de Controladores: Completado  
⬜ Optimización de Frontend: Pendiente  

## Trabajo Realizado

### Refactorización de Vistas y Assets
Se ha completado la refactorización de:

1. ✅ Página de gestión de notificaciones para el administrador
   - Separación de HTML, CSS y JavaScript
   - Creación de archivos específicos para cada tipo de contenido

2. ✅ Página de notificaciones para usuarios
   - Separación de HTML, CSS y JavaScript
   - Creación de archivos específicos para cada tipo de contenido

3. ✅ Componentes compartidos
   - Estilos CSS base en `/public/css/shared/`
   - Scripts JavaScript de notificaciones toast en `/public/js/shared/`

4. ✅ Vistas de administración de usuarios
   - Separación de HTML, CSS y JavaScript de `app/views/admin/users.php`
   - Creación de archivos CSS específicos en `/public/css/admin/users.css`
   - Creación de archivos JS específicos en `/public/js/admin/users.js`

5. ✅ Vistas de administración de clases
   - Separación de HTML, CSS y JavaScript de `app/views/admin/classes.php`
   - Creación de archivos CSS específicos en `/public/css/admin/classes.css`
   - Creación de archivos JS específicos en `/public/js/admin/classes.js`

6. ✅ Vistas de usuario para rutinas
   - Separación de HTML, CSS y JavaScript de `app/views/users/routines.php`
   - Creación de archivos CSS específicos en `/public/css/user/routines.css`
   - Creación de archivos JS específicos en `/public/js/user/routines.js`

7. ✅ Vistas de usuario para clases
   - Separación de HTML, CSS y JavaScript de `app/views/users/classes.php`
   - Creación de archivos CSS específicos en `/public/css/user/classes.css`
   - Creación de archivos JS específicos en `/public/js/user/classes.js`

8. ✅ Vistas de usuario para mis reservas
   - Separación de HTML, CSS y JavaScript de `app/views/users/my_reservations.php`
   - Creación de archivos CSS específicos en `/public/css/user/my_reservations.css`
   - Creación de archivos JS específicos en `/public/js/user/my_reservations.js`

9. ✅ Vistas de usuario para seguimiento físico
   - Separación de HTML, CSS y JavaScript de `app/views/users/tracking.php`
   - Creación de archivos CSS específicos en `/public/css/user/tracking.css`
   - Creación de archivos JS específicos en `/public/js/user/tracking.js`

10. ✅ Vistas de usuario para vista de rutina
   - Separación de HTML, CSS y JavaScript de `app/views/users/view_routine.php`
   - Creación de archivos CSS específicos en `/public/css/user/view_routine.css`
   - Creación de archivos JS específicos en `/public/js/user/view_routine.js`

11. ✅ Vistas de usuario para dashboard
   - Separación de HTML, CSS y JavaScript de `app/views/users/dashboard.php`
   - Creación de archivos CSS específicos en `/public/css/user/dashboard.css`
   - Creación de archivos JS específicos en `/public/js/user/dashboard.js`

12. ✅ Vistas de autenticación
   - Separación de HTML, CSS y JavaScript de `app/views/auth/login.php`, `app/views/auth/forgotPassword.php` y `app/views/auth/resetPassword.php`
   - Creación de archivos CSS específicos en `/public/css/auth/login.css` y `/public/css/auth/password-recovery.css`
   - Creación de archivos JS específicos en `/public/js/auth/login.js` y `/public/js/auth/reset-password.js`

13. ✅ Vistas de administrador para registro
   - Separación de HTML, CSS y JavaScript de `app/views/admin/register.php`
   - Creación de archivos CSS específicos en `/public/css/admin/register.css`
   - Creación de archivos JS específicos en `/public/js/admin/register.js`

14. ✅ Vistas de staff para rutinas
   - Separación de HTML, CSS y JavaScript de `app/views/staff/routines.php`
   - Creación de archivos CSS específicos en `/public/css/staff/routines.css`
   - Creación de archivos JS específicos en `/public/js/staff/routines.js`
   - Separación de HTML, CSS y JavaScript de `app/views/users/view_routine.php`
   - Creación de archivos CSS específicos en `/public/css/user/view_routine.css`
   - Creación de archivos JS específicos en `/public/js/user/view_routine.js`

15. ✅ Dashboards de administrador y staff
   - Corrección de referencias a archivos CSS y JS en `app/views/admin/dashboard.php`
   - Corrección de referencias a archivos CSS y JS en `app/views/staff/dashboard.php`
   - Verificación de estructuras comunes entre dashboards

16. ✅ Vistas de staff para creación de rutinas 
   - Separación de HTML, CSS y JavaScript de `app/views/staff/create_routine.php`
   - Creación de archivos CSS específicos en `/public/css/staff/create_routine.css`
   - Creación de archivos JS específicos en `/public/js/staff/create_routine.js`

17. ✅ Vistas de staff para edición de rutinas
   - Separación de HTML, CSS y JavaScript de `app/views/staff/edit_routine.php` 
   - Creación de archivos CSS específicos en `/public/css/staff/edit_routine.css`
   - Creación de archivos JS específicos en `/public/js/staff/edit_routine.js`

18. ✅ Vistas de staff para edición de ejercicios
   - Separación de HTML, CSS y JavaScript de `app/views/staff/edit_exercise.php` 
   - Creación de archivos CSS específicos en `/public/css/staff/edit_exercise.css`
   - Creación de archivos JS específicos en `/public/js/staff/edit_exercise.js`

19. ✅ Vistas de staff para búsqueda de ejercicios
   - Separación de HTML, CSS y JavaScript de `app/views/staff/search_exercises.php` 
   - Creación de archivos CSS específicos en `/public/css/staff/search_exercises.css`
   - Creación de archivos JS específicos en `/public/js/staff/search_exercises.js`

20. ✅ Vistas de staff para envío de notificaciones
   - Separación de HTML, CSS y JavaScript de `app/views/staff/send_notification.php` 
   - Creación de archivos CSS específicos en `/public/css/staff/send_notification.css`
   - Creación de archivos JS específicos en `/public/js/staff/send_notification.js`

21. ✅ Vistas de staff para seguimiento de usuarios
   - Separación de HTML, CSS y JavaScript de `app/views/staff/user_tracking.php`
   - Creación de archivos CSS específicos en `/public/css/staff/user_tracking.css`
   - Creación de archivos JS específicos en `/public/js/staff/user_tracking.js`
   
22. ✅ Vistas de administración para edición de usuarios
   - Separación de HTML, CSS y JavaScript de `app/views/admin/edit_user.php`
   - Creación de archivos CSS específicos en `/public/css/admin/edit_user.css`
   - Creación de archivos JS específicos en `/public/js/admin/edit_user.js`

## Resumen de Refactorización de Vistas y Assets

Hemos completado la refactorización de todos los archivos principales del sistema GymIntranet, separando el código HTML, CSS y JavaScript siguiendo el patrón MVC. Los principales logros incluyen:

1. **Separación de responsabilidades**: Todo el código CSS y JavaScript ha sido extraído de los archivos PHP y colocado en archivos específicos.
2. **Organización por módulos**: Los archivos CSS y JS están organizados por módulos (admin, auth, staff, user).
3. **Mejora en el mantenimiento**: Ahora es mucho más fácil modificar los estilos o la funcionalidad sin tener que editar los archivos de vista.
4. **Reutilización de código**: Se han identificado patrones comunes que podrían ser reutilizados en futuras mejoras.

## Refactorización de Controladores

Ahora que hemos completado la refactorización de los modelos, procedemos a refactorizar los controladores del sistema para aprovechar las mejoras implementadas. El objetivo es crear controladores más eficientes, con mejor manejo de errores y una estructura más coherente.

### Plan para la Refactorización de Controladores

1. ✅ Creación de un BaseController
   - Implementados métodos comunes como loadView(), redirect(), handleError(), handleSuccess(), isPost() e isGet()
   - Implementación de requireRole() para autorización consistente
   - Sistema unificado de gestión de errores y redirecciones
   - Métodos utilitarios para la validación de solicitudes HTTP

2. ⬜ Refactorización del AdminController
   - Adaptar para trabajar con los modelos refactorizados
   - Mejorar la validación de datos y manejo de errores
   - Optimizar consultas y operaciones

3. ⬜ Refactorización del AuthController
   - Implementar mejores prácticas de seguridad
   - Mejorar el sistema de autenticación y autorización
   - Optimizar el manejo de sesiones

4. ⬜ Refactorización del UserController
   - Adaptar para trabajar con los modelos refactorizados
   - Mejorar la experiencia del usuario
   - Implementar un mejor sistema de notificaciones

5. ⬜ Refactorización del StaffController
   - Adaptar para trabajar con los modelos refactorizados
   - Mejorar la gestión de clases y reservas
   - Optimizar las operaciones relacionadas con el seguimiento de usuarios
   
6. ✅ Refactorización del StaffRoutineController
   - Extendido de BaseController
   - Reemplazo de código para autorización con requireRole del BaseController
   - Sustitución de manipulación de sesiones con handleError y handleSuccess
   - Reemplazo de redirecciones con el método redirect
   - Sustitución de verificaciones de método HTTP con isPost e isGet
   - Refactorización de los métodos: index, createRoutine, editRoutine, deleteRoutine, updateExercise, searchExercises, apiSearchExercises, downloadPDF, addExercise
   - Mejoras en el manejo de errores y validaciones
   
7. 🟨 Refactorización parcial del UserRoutineController
   - Métodos viewRoutine y downloadRoutine refactorizados para usar BaseController
   - Reemplazo de código para autorización con requireRole del BaseController
   - Sustitución de manipulación de sesiones con handleError y handleSuccess
   
6. ✅ Refactorización del StaffRoutineController
   - Extendido de BaseController
   - Reemplazo de código para autorización con requireRole del BaseController
   - Sustitución de manipulación de sesiones con handleError y handleSuccess
   - Reemplazo de redirecciones con el método redirect
   - Sustitución de verificaciones de método HTTP con isPost e isGet
   - Refactorización de los métodos: index, createRoutine, editRoutine, deleteRoutine, updateExercise, searchExercises, apiSearchExercises, downloadPDF, addExercise
   - Mejoras en el manejo de errores y validaciones
   
7. ✅ Refactorización parcial del UserRoutineController
   - Métodos viewRoutine y downloadRoutine refactorizados para usar BaseController
   - Reemplazo de código para autorización con requireRole del BaseController
   - Sustitución de manipulación de sesiones con handleError y handleSuccess

## Refactorización de Modelos

Actualmente estamos en proceso de refactorizar los modelos del sistema para seguir un enfoque más orientado a objetos y basado en una clase base común. Los avances incluyen:

1. ✅ Creación de la clase BaseModel
   - Implementación de operaciones CRUD genéricas (getById, getAll, create, update, delete)
   - Métodos utilitarios genéricos (validate, findBy, findOneBy, count)
   - Propiedades para tabla y clave primaria

2. ✅ Refactorización del modelo ActivityTracking
   - Extensión de BaseModel
   - Implementación del método validate
   - Mejora en la organización del código con métodos específicos

3. ✅ Refactorización del modelo PhysicalTracking
   - Extensión de BaseModel
   - Implementación del método validate
   - Adaptación de métodos específicos

4. ✅ Refactorización del modelo Class
   - Extensión de BaseModel
   - Implementación del método validate
   - Mejora en los métodos específicos para clases
   
5. ✅ Refactorización del modelo User
   - Extensión de BaseModel
   - Implementación del método validate
   - Mapeo de campos en inglés a español
   - Optimización de métodos y reducción de código duplicado
   
6. ✅ Refactorización del modelo Notification
   - Extensión de BaseModel
   - Implementación del método validate
   - Adaptación de métodos para trabajar con diferentes formatos de datos
   
7. ✅ Refactorización del modelo Reservation
   - Extensión de BaseModel
   - Implementación del método validate
   
8. 🟨 Actualización del modelo Routine
   - Implementación del método getLastError para compatibilidad con el nuevo BaseController
   - Delegación a la clase Database para manejo de errores
   - Extensión de BaseModel
   - Implementación del método validate para gestionar tanto campos en español como en inglés
   - Mejora en el manejo de errores y validación de datos
   - Optimización de métodos de creación, actualización y cancelación de reservas
   - Adición de nuevos métodos para estadísticas
   
8. ✅ Refactorización del modelo Routine
   - Extensión de BaseModel
   - Implementación de mapeo de campos para compatibilidad entre español e inglés
   - Mejora en la gestión de rutinas y ejercicios
   - Adición de métodos para estadísticas de rutinas populares
   - Mantenimiento de compatibilidad con código existente a través de alias de métodos
   
9. ✅ Refactorización del modelo TypeClass
   - Extensión de BaseModel
   - Implementación del método validate
   - Adición de métodos adicionales para verificar uso en clases
   - Mantenimiento de compatibilidad con código existente
   - Mejora en la gestión de tipos de clase con estadísticas

### Beneficios de la Refactorización de Modelos
1. **Reducción de código duplicado**: Al mover la lógica común a la clase base.
2. **Mayor coherencia**: Todos los modelos siguen la misma estructura y patrones.
3. **Facilidad de mantenimiento**: Los cambios en la funcionalidad básica solo requieren modificaciones en un lugar.
4. **Mejor validación de datos**: Cada modelo implementa su propia lógica de validación.

## Próximos Pasos

### Prioridad Alta
1. ✅ Completar la refactorización de los modelos restantes
   - ✅ Implementar la extensión de BaseModel
   - ✅ Adaptar métodos específicos
   - ✅ Implementar validación de datos

2. ✅ Refactorizar los controladores para trabajar con los nuevos modelos
   - ✅ Implementar una clase BaseController para funcionalidades comunes
   - ✅ Mejorar la estructuración y normalización de respuestas
   - ✅ Optimizar la gestión de errores y validaciones
   - ✅ Refactorizar completamente StaffRoutineController
   - ✅ Completar refactorización de UserRoutineController
   - ✅ Refactorizar AdminController
   - ✅ Refactorizar AuthController
   - ✅ Refactorizar UserController 
   - ✅ Refactorizar StaffController

### Prioridad Baja

1. Optimización de archivos CSS
   - Evaluar la posibilidad de comprimir los archivos CSS para producción
   - Eliminar reglas duplicadas o no utilizadas

2. Optimización de archivos JS
   - Evaluar la posibilidad de minificar los archivos JavaScript para producción
   - Mejorar la modularización del código JavaScript

3. Implementación de preprocesadores
   - Considerar el uso de Sass o Less para una mejor organización de los estilos
   - Implementar un sistema de variables para colores, fuentes y otros valores comunes

## Buenas Prácticas a Seguir

1. **Consistencia**: Seguir siempre la misma estructura y convenciones de nomenclatura.
2. **Modularización**: Dividir el código en componentes pequeños y reutilizables.
3. **Comentarios**: Documentar adecuadamente el código, especialmente las funciones y clases.
4. **Optimización**: Minimizar archivos CSS y JS para producción cuando sea posible.
5. **Compatibilidad**: Asegurarse de que los cambios funcionen en diferentes navegadores.
6. **Pruebas**: Probar los cambios en diferentes dispositivos y resoluciones.

## Consideraciones Adicionales

- Evaluar la posibilidad de utilizar preprocesadores CSS como Sass o Less para futuras mejoras.
- Considerar la implementación de un sistema de componentes para elementos repetitivos de la interfaz.
- Implementar un sistema de gestión de dependencias frontend como npm o yarn para manejar bibliotecas externas.

## Resumen de Refactorización de Modelos

Hemos completado con éxito la refactorización de todos los modelos del sistema GymIntranet, transformándolos para seguir un enfoque más orientado a objetos y basado en una clase base común. Los principales logros incluyen:

1. **Estructura unificada**: Todos los modelos ahora extienden de la clase BaseModel, siguiendo una estructura y patrones consistentes.
2. **Mapeo de campos**: Implementación de mapeo entre campos en español e inglés para mayor flexibilidad y mantenimiento.
3. **Validación de datos**: Cada modelo implementa su propio método `validate()` para asegurar la integridad de los datos.
4. **Reducción de código duplicado**: Movimiento de operaciones CRUD comunes a la clase base.
5. **Ampliación de funcionalidades**: Adición de métodos para estadísticas y funciones avanzadas en varios modelos.
6. **Mantenimiento de compatibilidad**: Conservación de la compatibilidad con el código existente mediante alias de métodos.
7. **Documentación mejorada**: Mejor documentación de propiedades y métodos con PHPDoc.

## Resumen de Refactorización de Controladores

Hemos completado la refactorización de todos los controladores del sistema GymIntranet, adoptando un enfoque orientado a objetos y aprovechando la arquitectura MVC. Los logros alcanzados incluyen:

1. **Clase BaseController**: Implementación de una clase base con métodos comunes como loadView(), redirect(), handleError(), handleSuccess(), isPost(), isGet(), jsonResponse(), validateRequired(), validateEmail(), entre otros.
  
2. **Gestión unificada de errores**: Sustitución de manipulación directa de sesiones con métodos estructurados y estandarizados.

3. **Mejora en autenticación**: Implementación de los métodos requireAuth() y requireRole() en BaseController para un control de acceso consistente.

4. **Respuestas JSON estandarizadas**: Uso del método jsonResponse() para todas las respuestas API, logrando consistencia en el formato y los códigos HTTP.

5. **Controladores completamente refactorizados**:
   - AdminController: Mejora en la gestión de usuarios y clases
   - AuthController: Refuerzo en seguridad y manejo de sesiones
   - StaffController: Modernización del manejo de clases y asistencia
   - StaffRoutineController: Optimización de la gestión de rutinas
   - UserController: Mejora en la experiencia de usuario
   - UserRoutineController: Simplificación de la visualización y gestión de rutinas

Esta refactorización prepara el camino para futuros desarrollos y mejoras en el sistema, manteniendo un código más limpio, modular y fácil de mantener. Los beneficios clave incluyen:

1. **Reducción de código duplicado**: La funcionalidad común se ha movido al BaseController.
2. **Mayor coherencia**: Todos los controladores siguen la misma estructura y patrones.
3. **Mejor manejo de errores**: Sistema unificado para mostrar mensajes de error y éxito.
4. **Seguridad mejorada**: Validación de datos y control de acceso consistentes.
5. **Facilidad de mantenimiento**: Mayor organización y separación de responsabilidades.
