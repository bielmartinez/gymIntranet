<?php
// Este script sincroniza los usuarios con rol "staff" con la tabla "personal"
// Crea entradas en la tabla personal para cualquier usuario staff que no tenga registro

// Cargar configuración
require_once 'app/config/config.php';

// Cargar dependencias
require_once 'app/libraries/Database.php';
require_once 'app/utils/Logger.php';

// Crear una instancia de la base de datos
$db = new Database();

// 1. Obtener todos los usuarios con rol "staff"
$db->query('SELECT usuari_id, nom, cognoms FROM usuaris WHERE role = "staff"');
$staffUsers = $db->resultSet();

echo "<h1>Sincronizando usuarios staff con tabla personal</h1>";
echo "<p>Encontrados " . count($staffUsers) . " usuarios con rol staff</p>";

// 2. Para cada usuario staff, verificar si ya tiene entrada en personal
foreach ($staffUsers as $user) {
    $db->query('SELECT COUNT(*) as count FROM personal WHERE usuari_id = :usuari_id');
    $db->bind(':usuari_id', $user->usuari_id);
    $result = $db->single();
    
    if ($result->count == 0) {
        // No tiene entrada en personal, crearla
        $db->query('INSERT INTO personal (usuari_id, es_admin, data_contracte) VALUES (:usuari_id, :es_admin, CURDATE())');
        $db->bind(':usuari_id', $user->usuari_id);
        $db->bind(':es_admin', 0); // Por defecto no es admin
        
        if ($db->execute()) {
            echo "<p style='color:green'>✅ Usuario ID {$user->usuari_id} ({$user->nom} {$user->cognoms}) sincronizado correctamente.</p>";
        } else {
            echo "<p style='color:red'>❌ Error al sincronizar usuario ID {$user->usuari_id}.</p>";
        }
    } else {
        echo "<p>ℹ️ Usuario ID {$user->usuari_id} ({$user->nom} {$user->cognoms}) ya tiene registro en tabla personal.</p>";
    }
}

echo "<p><a href='" . URLROOT . "/admin/classes'>Volver a gestión de clases</a></p>";
?>