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
            $pdf->SetXY(15, 15);
            $pdf->Cell(0, 10, 'GymIntranet', 0, false, 'C', 0, '', 0, false, 'M', 'M');
            
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
                $pdf->Cell(90, 10, 'Ejercicio', 1, 0, 'C', 1);
                $pdf->Cell(25, 10, 'Series', 1, 0, 'C', 1);
                $pdf->Cell(25, 10, 'Reps', 1, 0, 'C', 1);
                $pdf->Cell(30, 10, 'Descanso', 1, 1, 'C', 1);
                
                // Datos de los ejercicios
                $pdf->SetFont('helvetica', '', 11);
                $count = 1;
                $backgroundAlt = false;
                
                foreach ($exercises as $exercise) {
                    // Alternar colores para facilitar lectura
                    $backgroundColor = $backgroundAlt ? array(240, 240, 240) : array(255, 255, 255);
                    $pdf->SetFillColor($backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
                    
                    $pdf->Cell(10, 10, $count, 1, 0, 'C', 1);
                    $pdf->Cell(90, 10, $exercise->nom, 1, 0, 'L', 1);
                    $pdf->Cell(25, 10, $exercise->series, 1, 0, 'C', 1);
                    $pdf->Cell(25, 10, $exercise->repeticions, 1, 0, 'C', 1);
                    $pdf->Cell(30, 10, $exercise->descans . ' seg', 1, 1, 'C', 1);
                    
                    $count++;
                    $backgroundAlt = !$backgroundAlt;
                    
                    // Solo mostrar la descripción completa si existe
                    if (!empty($exercise->descripcio)) {
                        $pdf->Ln(2);
                        $pdf->SetFont('helvetica', 'B', 11);
                        $pdf->Cell(0, 8, $exercise->nom . ' - Descripción:', 0, 1);
                        
                        $pdf->SetFont('helvetica', '', 10);
                        // Convertir saltos de línea en la descripción
                        $description = str_replace("\n", "<br>", htmlspecialchars($exercise->descripcio));
                        $pdf->writeHTML($description, true, false, true, false, '');
                        
                        $pdf->Ln(3);
                        // Separador sutil
                        $pdf->SetDrawColor(200, 200, 200);
                        $pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
                        $pdf->SetDrawColor(0, 0, 0); // Restaurar color de línea por defecto
                        $pdf->Ln(5);
                    }
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

    /**
     * Genera un PDF con los detalles de una rutina y lo envía directamente al navegador
     * 
     * @param object $routine Datos de la rutina
     * @param array $exercises Ejercicios de la rutina
     * @param string $downloadName Nombre del archivo que se descargará
     * @return bool True si se generó correctamente, false en caso contrario
     */
    public function downloadRoutinePDF($routine, $exercises, $downloadName = null) {
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
            $pdf->SetXY(15, 15);
            $pdf->Cell(0, 10, 'GymIntranet', 0, false, 'C', 0, '', 0, false, 'M', 'M');
            
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
                $pdf->Cell(90, 10, 'Ejercicio', 1, 0, 'C', 1);
                $pdf->Cell(25, 10, 'Series', 1, 0, 'C', 1);
                $pdf->Cell(25, 10, 'Reps', 1, 0, 'C', 1);
                $pdf->Cell(30, 10, 'Descanso', 1, 1, 'C', 1);
                
                // Datos de los ejercicios
                $pdf->SetFont('helvetica', '', 11);
                $count = 1;
                $backgroundAlt = false;
                
                foreach ($exercises as $exercise) {
                    // Alternar colores para facilitar lectura
                    $backgroundColor = $backgroundAlt ? array(240, 240, 240) : array(255, 255, 255);
                    $pdf->SetFillColor($backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
                    
                    $pdf->Cell(10, 10, $count, 1, 0, 'C', 1);
                    $pdf->Cell(90, 10, $exercise->nom, 1, 0, 'L', 1);
                    $pdf->Cell(25, 10, $exercise->series, 1, 0, 'C', 1);
                    $pdf->Cell(25, 10, $exercise->repeticions, 1, 0, 'C', 1);
                    $pdf->Cell(30, 10, $exercise->descans . ' seg', 1, 1, 'C', 1);
                    
                    $count++;
                    $backgroundAlt = !$backgroundAlt;
                    
                    // Solo mostrar la descripción completa si existe
                    if (!empty($exercise->descripcio)) {
                        $pdf->Ln(2);
                        $pdf->SetFont('helvetica', 'B', 11);
                        $pdf->Cell(0, 8, $exercise->nom . ' - Descripción:', 0, 1);
                        
                        $pdf->SetFont('helvetica', '', 10);
                        // Convertir saltos de línea en la descripción
                        $description = str_replace("\n", "<br>", htmlspecialchars($exercise->descripcio));
                        $pdf->writeHTML($description, true, false, true, false, '');
                        
                        $pdf->Ln(3);
                        // Separador sutil
                        $pdf->SetDrawColor(200, 200, 200);
                        $pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
                        $pdf->SetDrawColor(0, 0, 0); // Restaurar color de línea por defecto
                        $pdf->Ln(5);
                    }
                }
            }
            
            // Pie de página
            $pdf->Ln(10);
            $pdf->SetFont('helvetica', 'I', 10);
            $pdf->Cell(0, 10, 'Documento generado automáticamente por GymIntranet.', 0, 1, 'C');
            $pdf->Cell(0, 10, 'Fecha de impresión: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
            
            // Determinar el nombre de descarga
            if ($downloadName === null) {
                $downloadName = 'Rutina_' . $routine->nom . '_' . date('Y-m-d') . '.pdf';
            }
            
            // Enviar el PDF directamente al navegador para descarga
            $pdf->Output($downloadName, 'D'); // 'D' significa descarga forzada
            
            return true;
            
        } catch (Exception $e) {
            if (class_exists('Logger')) {
                Logger::log('ERROR', 'Error al generar PDF de rutina para descarga: ' . $e->getMessage());
            }
            return false;
        }
    }
}