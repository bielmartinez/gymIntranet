<?php
/**
 * Servicio para generar PDFs de rutinas utilizando TCPDF
 */

require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

class PDFGenerator {
    /**
     * Genera un PDF con los detalles de una rutina
     * 
     * @param object $routine Datos de la rutina
     * @param array $exercises Ejercicios de la rutina
     * @param string $outputPath Ruta donde guardar el PDF (opcional)
     * @return string|bool Ruta al archivo generado o false en caso de error
     */
    public function generateRoutinePDF($routine, $exercises, $outputPath = null) {
        try {
            // Crear una instancia de TCPDF
            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            
            // Establecer información del documento
            $pdf->SetCreator('GymIntranet');
            $pdf->SetAuthor('GymIntranet');
            $pdf->SetTitle('Rutina: ' . $routine->nom);
            $pdf->SetSubject('Rutina de ejercicios');
            
            // Eliminar cabecera y pie de página predeterminados
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            
            // Establecer márgenes
            $pdf->SetMargins(15, 15, 15);
            $pdf->SetAutoPageBreak(true, 15);
            
            // Añadir una nueva página
            $pdf->AddPage();
            
            // Establecer fuente
            $pdf->SetFont('helvetica', 'B', 20);
            
            // Logo y cabecera
            $pdf->Image(dirname(dirname(__DIR__)) . '/public/img/logo.png', 15, 10, 30, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
            $pdf->SetXY(50, 15);
            $pdf->Cell(0, 10, 'GymIntranet', 0, false, 'L', 0, '', 0, false, 'M', 'M');
            
            // Título de la rutina
            $pdf->Ln(20);
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->Cell(0, 10, 'Rutina: ' . $routine->nom, 0, 1, 'C');
            
            // Descripción de la rutina
            $pdf->SetFont('helvetica', '', 12);
            $pdf->writeHTML('<p><strong>Descripción:</strong> ' . $routine->descripcio . '</p>', true, false, true, false, '');
            $pdf->Ln(5);
            
            // Fecha de creación
            $pdf->SetFont('helvetica', 'I', 10);
            $pdf->Cell(0, 10, 'Fecha de creación: ' . date('d/m/Y', strtotime($routine->creat_el)), 0, 1, 'R');
            $pdf->Ln(5);
            
            // Tabla de ejercicios
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->Cell(0, 10, 'Listado de Ejercicios', 0, 1, 'L');
            $pdf->Ln(2);
            
            if (empty($exercises)) {
                $pdf->SetFont('helvetica', 'I', 12);
                $pdf->Cell(0, 10, 'No hay ejercicios asignados a esta rutina.', 0, 1, 'C');
            } else {
                // Cabeceras de la tabla
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->SetFillColor(230, 230, 230);
                $pdf->Cell(10, 10, '#', 1, 0, 'C', 1);
                $pdf->Cell(60, 10, 'Ejercicio', 1, 0, 'C', 1);
                $pdf->Cell(20, 10, 'Series', 1, 0, 'C', 1);
                $pdf->Cell(20, 10, 'Reps', 1, 0, 'C', 1);
                $pdf->Cell(20, 10, 'Descanso', 1, 0, 'C', 1);
                $pdf->Cell(50, 10, 'Notas', 1, 1, 'C', 1);
                
                // Datos de los ejercicios
                $pdf->SetFont('helvetica', '', 11);
                $count = 1;
                $backgroundAlt = false;
                
                foreach ($exercises as $exercise) {
                    // Alternar colores para facilitar lectura
                    $backgroundColor = $backgroundAlt ? array(240, 240, 240) : array(255, 255, 255);
                    $pdf->SetFillColor($backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
                    
                    $pdf->Cell(10, 10, $count, 1, 0, 'C', 1);
                    $pdf->Cell(60, 10, $exercise->nom, 1, 0, 'L', 1);
                    $pdf->Cell(20, 10, $exercise->series, 1, 0, 'C', 1);
                    $pdf->Cell(20, 10, $exercise->repeticions, 1, 0, 'C', 1);
                    $pdf->Cell(20, 10, $exercise->descans . 's', 1, 0, 'C', 1);
                    
                    // Limitar texto de descripción
                    $desc = strlen($exercise->descripcio) > 50 ? 
                            substr($exercise->descripcio, 0, 50) . '...' : 
                            $exercise->descripcio;
                    $pdf->Cell(50, 10, $desc, 1, 1, 'L', 1);
                    
                    $count++;
                    $backgroundAlt = !$backgroundAlt;
                    
                    // Descripción del ejercicio
                    $pdf->Ln(5);
                    $pdf->SetFont('freesans', '', 10);
                    
                    // Convertir saltos de línea en la descripción
                    $description = str_replace("\n", "<br>", htmlspecialchars($exercise->descripcio));
                    $pdf->writeHTML("<strong>Descripción:</strong><br>" . $description, true, false, true, false, '');
                    
                    // Detalles del ejercicio (series, repeticiones, descanso)
                    $pdf->Ln(5);
                    $pdf->SetFont('freesans', 'B', 10);
                    $pdf->Cell(0, 10, 'Detalles:', 0, 1);
                    
                    $pdf->SetFont('freesans', '', 10);
                    $pdf->Cell(60, 7, 'Series: ' . $exercise->series, 0, 0);
                    $pdf->Cell(60, 7, 'Repeticiones: ' . $exercise->repeticions, 0, 0);
                    $pdf->Cell(60, 7, 'Descanso: ' . $exercise->descans . ' segundos', 0, 1);
                    
                    // Información adicional si existe
                    if (isset($exercise->info_adicional) && !empty($exercise->info_adicional)) {
                        $info = json_decode($exercise->info_adicional);
                        if ($info) {
                            $pdf->Ln(3);
                            $pdf->SetFont('freesans', 'B', 10);
                            $pdf->Cell(0, 7, 'Información adicional:', 0, 1);
                            
                            $pdf->SetFont('freesans', '', 10);
                            if (!empty($info->muscle)) {
                                $pdf->Cell(0, 7, 'Grupo muscular: ' . $info->muscle, 0, 1);
                            }
                            if (!empty($info->equipment)) {
                                $pdf->Cell(0, 7, 'Equipamiento: ' . $info->equipment, 0, 1);
                            }
                            if (!empty($info->difficulty)) {
                                $pdf->Cell(0, 7, 'Dificultad: ' . $info->difficulty, 0, 1);
                            }
                        }
                    }
                    
                    $pdf->Ln(10);
                    
                    // Separador
                    $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
                    $pdf->Ln(10);
                }
            }
            
            // Pie de página
            $pdf->Ln(10);
            $pdf->SetFont('helvetica', 'I', 10);
            $pdf->Cell(0, 10, 'Documento generado automáticamente por GymIntranet.', 0, 1, 'C');
            $pdf->Cell(0, 10, 'Fecha de impresión: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
            
            // Determinar el nombre del archivo
            if ($outputPath === null) {
                $filename = 'rutina_' . $routine->rutina_id . '_' . time() . '.pdf';
                $outputPath = dirname(dirname(__DIR__)) . '/public/uploads/routines/' . $filename;
                
                // Asegurarse de que el directorio existe
                if (!is_dir(dirname($outputPath))) {
                    mkdir(dirname($outputPath), 0777, true);
                }
            }
            
            // Guardar el PDF
            $pdf->Output($outputPath, 'F');
            
            // Devolver la ruta relativa para guardar en la base de datos
            $relativePath = 'uploads/routines/' . basename($outputPath);
            return $relativePath;
            
        } catch (Exception $e) {
            if (class_exists('Logger')) {
                Logger::log('ERROR', 'Error al generar PDF de rutina: ' . $e->getMessage());
            }
            return false;
        }
    }
}