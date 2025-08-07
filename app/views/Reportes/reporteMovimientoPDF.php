<?php
// Limpiar buffer de salida para evitar errores en PDF
ob_start();
ob_clean();

session_start();

require_once("../../config/configuracion.php");
require_once("../../models/GestionarMovimientos.php");
require_once("../../../includes/vendor/setasign/fpdf/fpdf.php");

$movimientos = new GestionarMovimientos();
$idMovimiento = $_GET['id'] ?? null;

if (!$idMovimiento) {
    die("ID de movimiento no proporcionado");
}

try {
    $detalles = $movimientos->obtenerDetallesMovimiento($idMovimiento);
    $cabecera = $movimientos->obtenerCabeceraMovimiento($idMovimiento);

    // Asegurarnos de que tenemos el primer registro de la cabecera
    if (is_array($cabecera) && !empty($cabecera)) {
        $cabecera = $cabecera[0];
    }

    // Obtener los tipos de movimiento desde la base de datos
    $tiposMovimiento = $movimientos->obtenerTiposMovimiento();

    // Obtener nombres de responsables y autorizadores
    $responsableOrigen = $movimientos->obtenerEmpleado($cabecera['responsableOrigen']);
    $responsableDestino = $movimientos->obtenerEmpleado($cabecera['responsableDestino']);

    $cabecera['responsableOrigen'] = $responsableOrigen['NombreTrabajador'] ?? '';
    $cabecera['responsableDestino'] = $responsableDestino['NombreTrabajador'] ?? '';
} catch (Exception $e) {
    die("Error al obtener datos: " . $e->getMessage());
}

class PDF extends FPDF
{
    private $cabecera;

    public function setCabecera($cabecera)
    {
        $this->cabecera = $cabecera;
    }

    function Header()
    {
        // Header con logo y información de empresa
        $logoPath = '../../../public/img/Logo-Lubriseng.png';
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 15, 15, 20);
        }

        // Información de la empresa (lado izquierdo)
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(40, 167, 69); // Verde corporativo
        $this->SetXY(40, 18);
        $this->Cell(0, 6, $this->convertToLatin1($this->cabecera['empresaOrigen'] ?? ''), 0, 1);

        $this->SetFont('Arial', 'B', 12);
        $this->SetXY(40, 25);
        $this->Cell(0, 5, $this->convertToLatin1('Gestión de Activos'), 0, 1);

        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(102, 102, 102); // Gris
        $this->SetXY(40, 32);
        $this->Cell(0, 4, $this->convertToLatin1('Dirección fiscal: ' . ($this->cabecera['DireccionOrigen'] ?? '')), 0, 1);
        $this->SetXY(40, 36);
        $this->Cell(0, 4, $this->convertToLatin1('Sucursal: ' . ($this->cabecera['sucursalOrigen'] ?? '')), 0, 1);

        // Cuadro de información del documento (lado derecho)
        $this->SetDrawColor(40, 167, 69);
        $this->SetLineWidth(0.5);
        $this->Rect(145, 15, 50, 25);

        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(40, 167, 69);
        $this->SetXY(147, 18);
        $this->Cell(46, 4, 'R.U.C. ' . ($this->cabecera['RucOrigen'] ?? ''), 0, 1, 'C');

        $this->SetFont('Arial', 'B', 8);
        $this->SetXY(147, 23);
        $this->MultiCell(46, 3, $this->convertToLatin1("GESTION DE ALMACEN\nVALE DE SALIDA DE ACTIVOS"), 0, 'C');

        $this->SetFont('Arial', 'B', 9);
        $this->SetXY(147, 33);
        $this->Cell(46, 4, $this->convertToLatin1('N° ' . ($this->cabecera['codigoMovimiento'] ?? '')), 0, 1, 'C');

        // Línea separadora verde
        $this->SetDrawColor(40, 167, 69);
        $this->Line(15, 45, 195, 45);

        $this->SetTextColor(0, 0, 0); // Volver a negro
        $this->Ln(15);
    }

    function Footer()
    {
        // Footer con fecha y hora de generación
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(102, 102, 102);
        $this->Cell(0, 10, $this->convertToLatin1('Documento generado el ' . date('d/m/Y') . ' a las ' . date('H:i:s')), 0, 0, 'C');
    }

    function DrawCheckbox($x, $y, $checked = false, $size = 3)
    {
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.3);
        $this->Rect($x, $y, $size, $size);
        if ($checked) {
            $this->SetFont('Arial', 'B', 8);
            $this->SetXY($x + 0.3, $y - 0.3);
            $this->Cell($size - 0.6, $size, 'X', 0, 0, 'C');
        }
    }

    function convertToLatin1($text)
    {
        return iconv('UTF-8', 'ISO-8859-1//IGNORE', $text);
    }
    
    // Función para crear líneas divisorias
    function DrawDivider()
    {
        $this->SetDrawColor(221, 221, 221);
        $this->Line(15, $this->GetY(), 195, $this->GetY());
        $this->Ln(5);
    }
    
    // Función para crear secciones de datos
    function CreateDataSection($leftData, $rightData, $y_start)
    {
        $y_left = $y_start;
        $y_right = $y_start;
        
        // Columna izquierda
        foreach ($leftData as $item) {
            $this->SetFont('Arial', 'B', 9);
            $this->SetTextColor(40, 167, 69);
            $this->SetXY(15, $y_left);
            $this->Cell(50, 5, $this->convertToLatin1($item['label']), 0, 0);
            $this->SetFont('Arial', '', 9);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(45, 5, $this->convertToLatin1($item['value']), 0, 0); // Limitar ancho para no solapar
            $y_left += 5;
        }
        
        // Columna derecha
        foreach ($rightData as $item) {
            $this->SetFont('Arial', 'B', 9);
            $this->SetTextColor(40, 167, 69);
            $this->SetXY(110, $y_right);
            $this->Cell(50, 5, $this->convertToLatin1($item['label']), 0, 0);
            $y_right += 5;
            
            $this->SetFont('Arial', '', 9);
            $this->SetTextColor(0, 0, 0);
            if (is_array($item['value'])) {
                foreach ($item['value'] as $line) {
                    $this->SetXY(110, $y_right);
                    $this->Cell(80, 4, $this->convertToLatin1($line), 0, 0); // Ancho fijo para evitar solapamiento
                    $y_right += 4;
                }
            } else {
                $this->SetXY(110, $y_right);
                $this->Cell(80, 4, $this->convertToLatin1($item['value']), 0, 0);
                $y_right += 4;
            }
        }
        
        // Establecer la posición Y al final de la sección más larga
        $this->SetY(max($y_left, $y_right));
    }
}

// Limpiar buffer antes de generar PDF
ob_end_clean();

// Crear PDF
$pdf = new PDF();
$pdf->setCabecera($cabecera);
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);

// ===== SECCIÓN: DETALLES DE TRANSFERENCIA =====
$y_start = $pdf->GetY();

// Datos de la columna izquierda
$leftData = [
    ['label' => 'Fecha de inicio de traslado:', 'value' => date('d/m/Y', strtotime($cabecera['fechaMovimiento'] ?? ''))],
    ['label' => 'Destinatario:', 'value' => $cabecera['empresaDestino'] ?? ''],
    ['label' => 'RUC:', 'value' => $cabecera['RucDestino'] ?? '']
];

// Datos de la columna derecha
$rightData = [
    ['label' => 'Punto de partida:', 'value' => [$cabecera['sucursalOrigen'] ?? '', $cabecera['DireccionOrigen'] ?? '']],
    ['label' => 'Punto de llegada:', 'value' => [$cabecera['sucursalDestino'] ?? '', $cabecera['DireccionDestino'] ?? '']]
];

$pdf->CreateDataSection($leftData, $rightData, $y_start);
$pdf->Ln(3);
$pdf->DrawDivider();

// ===== SECCIÓN: MOTIVO DE TRASLADO =====
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(40, 167, 69);
$pdf->Cell(0, 5, 'Motivo de traslado:', 0, 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(2);

$tipoMov = $cabecera['tipoMovimiento'] ?? '';
$y_start = $pdf->GetY();
$x_start = 15;
$col_width = 60;

// Checkboxes organizados en 3 columnas
foreach ($tiposMovimiento as $index => $tipo) {
    $x = $x_start + ($index % 3) * $col_width;
    $y = $y_start + floor($index / 3) * 6;

    $pdf->DrawCheckbox($x, $y, $tipoMov == $tipo['nombre'], 3);
    $pdf->SetXY($x + 6, $y);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell($col_width - 6, 5, $pdf->convertToLatin1($tipo['nombre']), 0, 0);
}

$pdf->Ln(12);
$pdf->DrawDivider();

// ===== SECCIÓN: TABLA DE BIENES TRANSPORTADOS =====
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(40, 167, 69);
$pdf->Cell(0, 6, $pdf->convertToLatin1('DATOS DEL BIEN TRANSPORTADO'), 0, 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(1);

// Encabezados de tabla con colores corporativos
$pdf->SetFillColor(40, 167, 69);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetDrawColor(40, 167, 69);

$pdf->Cell(20, 8, $pdf->convertToLatin1('N°'), 1, 0, 'C', true);
$pdf->Cell(35, 8, $pdf->convertToLatin1('CÓDIGO'), 1, 0, 'C', true);
$pdf->Cell(85, 8, $pdf->convertToLatin1('DESCRIPCIÓN'), 1, 0, 'C', true);
$pdf->Cell(25, 8, 'CANTIDAD', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'UM', 1, 1, 'C', true);

// Datos de la tabla
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 8);
$pdf->SetDrawColor(221, 221, 221);

if (!empty($detalles)) {
    $i = 1;
    foreach ($detalles as $detalle) {
        $pdf->Cell(20, 6, $i++, 1, 0, 'C');
        $pdf->Cell(35, 6, $detalle['codigoActivo'] ?? '', 1, 0, 'C');
        $pdf->Cell(85, 6, $pdf->convertToLatin1($detalle['nombreActivo'] ?? ''), 1, 0, 'L');
        $pdf->Cell(25, 6, $detalle['Cantidad'] ?? '1', 1, 0, 'C');
        $pdf->Cell(25, 6, 'UM', 1, 1, 'C');
    }
} else {
    $pdf->Cell(190, 6, 'No hay detalles de movimiento para mostrar.', 1, 1, 'C');
}

$pdf->Ln(8);

// ===== SECCIÓN: OBSERVACIONES =====
$pdf->SetDrawColor(221, 221, 221);
$pdf->SetLineWidth(0.5);
$obs_height = 20;
$pdf->Rect(15, $pdf->GetY(), 180, $obs_height);

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(40, 167, 69);
$pdf->SetXY(20, $pdf->GetY() + 2);
$pdf->Cell(0, 5, 'Observaciones:', 0, 1);
$pdf->SetTextColor(0, 0, 0);

$pdf->SetFont('Arial', '', 8);
$pdf->SetX(20);
$pdf->Cell(0, 4, 'Doc. Referencia:', 0, 1);
$pdf->SetX(20);
$pdf->MultiCell(170, 4, $pdf->convertToLatin1($cabecera['observaciones'] ?? ''));

$pdf->Ln(15); // Más espacio antes de las firmas

// ===== SECCIÓN: FIRMAS CON MÁS ESPACIO =====
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0, 6, 'CONFORMIDAD', 0, 1, 'C');
$pdf->Ln(25); // Espacio generoso para firmas

// Líneas de firma más largas y mejor posicionadas
$line_y = $pdf->GetY();
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0.5);
$pdf->Line(25, $line_y, 95, $line_y);   // Línea izquierda más larga
$pdf->Line(115, $line_y, 185, $line_y); // Línea derecha más larga

$pdf->Ln(5); // Espacio entre línea y nombres

// Nombres con mejor espaciado
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetX(25);
$pdf->Cell(70, 5, $pdf->convertToLatin1($cabecera['nombreAutorizador'] ?? ''), 0, 0, 'C');
$pdf->SetX(115);
$pdf->Cell(70, 5, $pdf->convertToLatin1($cabecera['nombreReceptor'] ?? ''), 0, 1, 'C');

$pdf->Ln(1); // Pequeño espacio entre nombre y DNI

// DNI
$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(102, 102, 102); // Gris para DNI
$pdf->SetX(25);
$pdf->Cell(70, 4, 'DNI: ' . ($cabecera['dniAutorizador'] ?? ''), 0, 0, 'C');
$pdf->SetX(115);
$pdf->Cell(70, 4, 'DNI: ' . ($cabecera['dniReceptor'] ?? ''), 0, 1, 'C');

$pdf->Ln(1); // Espacio entre DNI y títulos

// Títulos de cargo
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetX(25);
$pdf->Cell(70, 4, 'Emisor', 0, 0, 'C');
$pdf->SetX(115);
$pdf->Cell(70, 4, 'Receptor', 0, 1, 'C');

// Generar PDF - 'I' para visualizar en navegador
$filename = 'movimiento_' . ($cabecera['codigoMovimiento'] ?? 'reporte') . '.pdf';
$pdf->Output('I', $filename);
