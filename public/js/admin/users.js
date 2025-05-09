/**
 * Funcionalidades específicas para la administración de usuarios
 * Este archivo maneja las interacciones en la página de gestión de usuarios
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTables para la tabla de usuarios
    if (document.getElementById('usersTable')) {
        $('#usersTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
            },
            order: [[0, 'desc']], // Ordenar por ID (columna 0) descendente por defecto
            responsive: true
        });
    }
    
    // Funcionalidad para confirmar eliminación/desactivación de usuario
    document.querySelectorAll('a[href*="/admin/deleteUser/"]').forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('¿Está seguro de que desea desactivar este usuario? El usuario perderá acceso al sistema pero podrá ser reactivado más adelante.')) {
                e.preventDefault();
            }
        });
    });
    
    // Funcionalidad para confirmar reactivación de usuario
    document.querySelectorAll('a[href*="/admin/reactivateUser/"]').forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('¿Está seguro de que desea reactivar este usuario?')) {
                e.preventDefault();
            }
        });
    });
    
    // Funcionalidad para filtrar usuarios por rol (si existe el control)
    const roleFilter = document.getElementById('roleFilter');
    if (roleFilter) {
        roleFilter.addEventListener('change', function() {
            const table = $('#usersTable').DataTable();
            table.column(3).search(this.value).draw();
        });
    }
    
    // Funcionalidad para filtrar usuarios por estado (si existe el control)
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            const table = $('#usersTable').DataTable();
            const searchTerm = this.value === 'active' ? 'Activo' : (this.value === 'inactive' ? 'Inactivo' : '');
            table.column(6).search(searchTerm).draw();
        });
    }
});
