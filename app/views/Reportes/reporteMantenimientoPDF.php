<?php
// Limpiar buffer de salida para evitar errores en PDF
ob_start();
ob_clean();

session_start();
require_once("../../config/configuracion.php");
require_once("../../models/Mantenimientos.php");
require_once("../../../public/plugins/fpdf/fpdfRequerimiento.php");

$mantenimiento = new Mantenimientos();
$idMantenimiento = $_GET['id'] ?? null;

if (!$idMantenimiento) {
    die("ID de mantenimiento no proporcionado");
}

try {
    // Obtener datos del mantenimiento
    $detalles = $mantenimiento->obtenerDetallesMantenimiento($idMantenimiento);
    $cabecera = $mantenimiento->obtenerCabeceraMantenimiento($idMantenimiento);

    if (!$cabecera) {
        die("No se encontró el mantenimiento especificado");
    }

    // Obtener información de la empresa y sucursal desde la sesión
    $idEmpresa = $_SESSION['cod_empresa'] ?? 1;
    $idSucursal = $_SESSION['cod_UnidadNeg'] ?? 1;

    $empresaInfo = $mantenimiento->obtenerEmpresaInfo($idEmpresa);
    $sucursalInfo = $mantenimiento->obtenerSucursalInfo($idSucursal);

    // Preparar información de la empresa para el reporte
    $companyInfo = [
        'nombre' => $empresaInfo['nombre'] ?? 'LUBRISENG E.I.R.L',
        'ruc' => $empresaInfo['ruc'] ?? '20399129614',
        'direccion' => $sucursalInfo['direccion'] ?? 'Av Bolognesi - 80-Talara',
        'sucursal' => $sucursalInfo['nombre'] ?? 'Autocentro Lubriseng'
    ];
} catch (Exception $e) {
    die("Error al obtener datos: " . $e->getMessage());
}

class PDF extends FPDF
{
    private $cabecera;
    private $companyInfo;

    public function setCabecera($cabecera)
    {
        $this->cabecera = $cabecera;
    }

    public function setCompanyInfo($companyInfo)
    {
        $this->companyInfo = $companyInfo;
    }

    function Header()
    {
        // Header con logo y información de empresa
        $logoPath = '../../../public/img/Logo-Lubriseng.png';
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 15, 25, 25);
        }

        // Información de la empresa (lado izquierdo)
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(40, 167, 69); // Verde corporativo
        $this->SetXY(40, 18);
        $this->Cell(0, 6, $this->convertToLatin1($this->companyInfo['nombre']), 0, 1);

        $this->SetFont('Arial', 'B', 12);
        $this->SetXY(40, 25);
        $this->Cell(0, 5, $this->convertToLatin1('Gestión de Activos'), 0, 1);

        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(102, 102, 102); // Gris
        $this->SetXY(40, 32);
        $this->MultiCell(100, 4, $this->convertToLatin1('Dirección fiscal: ' . $this->companyInfo['direccion']), 0, 'L');
        $this->SetXY(40, $this->GetY());
        $this->MultiCell(100, 4, $this->convertToLatin1('Sucursal: ' . $this->companyInfo['sucursal']), 0, 'L');

        // Cuadro de información del documento (lado derecho)
        $this->SetDrawColor(40, 167, 69);
        $this->SetLineWidth(0.5);
        $this->Rect(145, 15, 50, 25);

        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(40, 167, 69);
        $this->SetXY(147, 18);
        $this->Cell(46, 4, 'R.U.C. ' . $this->companyInfo['ruc'], 0, 1, 'C');

        $this->SetFont('Arial', 'B', 8);
        $this->SetXY(147, 23);
        $this->MultiCell(46, 3, $this->convertToLatin1("GESTION DE ALMACEN\nORDEN DE MANTENIMIENTO"), 0, 'C');

        $this->SetFont('Arial', 'B', 9);
        $this->SetXY(147, 33);
        $this->Cell(46, 4, $this->convertToLatin1('N° ' . ($this->cabecera['codigoMantenimiento'] ?? '')), 0, 1, 'C');

        // Línea separadora verde
        $this->SetDrawColor(40, 167, 69);
        $this->Line(15, 50, 195, 50);

        $this->SetTextColor(0, 0, 0); // Volver a negro
        $this->Ln(20);
    }

    function Footer()
    {
        // Footer con fecha y hora de generación
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(102, 102, 102);
        $this->Cell(0, 10, $this->convertToLatin1('Documento generado el ' . date('d/m/Y') . ' a las ' . date('H:i:s')), 0, 0, 'C');
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

    // Función para calcular la altura necesaria para MultiCell
    function GetMultiCellHeight($w, $h, $txt, $border = 0, $align = 'J')
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $ns = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $ns = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
                $ls = $l;
                $ns++;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else {
                    $i = $sep + 1;
                    $sep = -1;
                    $j = $i;
                    $l = 0;
                    $ns = 0;
                }
                $nl++;
            } else
                $i++;
        }
        return $nl * $h;
    }

    // Función para crear secciones de datos de mantenimiento
    function CreateMaintenanceDataSection($leftData, $rightData, $y_start)
    {
        $y_left = $y_start;
        $y_right = $y_start;

        // Columna izquierda
        foreach ($leftData as $item) {
            $this->SetFont('Arial', 'B', 9);
            $this->SetTextColor(40, 167, 69);
            $this->SetXY(15, $y_left);
            $this->Cell(35, 5, $this->convertToLatin1($item['label']), 0, 0);
            $this->SetFont('Arial', '', 9);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(45, 5, $this->convertToLatin1($item['value']), 0, 0);
            $y_left += 5;
        }

        // Columna derecha
        foreach ($rightData as $item) {
            $this->SetFont('Arial', 'B', 9);
            $this->SetTextColor(40, 167, 69);
            $this->SetXY(110, $y_right);
            $this->Cell(50, 5, $this->convertToLatin1($item['label']), 0, 0);
            $this->SetFont('Arial', '', 9);
            $this->SetTextColor(0, 0, 0);
            $this->SetXY(160, $y_right);
            $this->Cell(30, 5, $this->convertToLatin1($item['value']), 0, 0);
            $y_right += 5;
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
$pdf->setCompanyInfo($companyInfo);
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);

// ===== SECCIÓN: DETALLES DEL MANTENIMIENTO =====
$y_start = $pdf->GetY();

// Datos de la columna izquierda
$leftData = [
    [
        'label' => 'Fecha:',
        'value' => !empty($cabecera['fechaRegistro'])
            ? date('d/m/Y', strtotime($cabecera['fechaRegistro']))
            : 'No programada'
    ],
    [
        'label' => 'Responsable:',
        'value' => $cabecera['Proveedor'] ?? ''
    ]
];

// Datos de la columna derecha
$rightData = [];

// Mostrar costo estimado si el costo real no existe o es nulo/vacío
if (empty($cabecera['costoReal'])) {
    $rightData[] = [
        'label' => 'Costo Estimado:',
        'value' => 'S/ ' . number_format($cabecera['costoEstimado'] ?? 0, 2)
    ];
} else {
    $rightData[] = [
        'label' => 'Costo Real:',
        'value' => 'S/ ' . number_format($cabecera['costoReal'], 2)
    ];
}

// Render de la sección
$pdf->CreateMaintenanceDataSection($leftData, $rightData, $y_start);
$pdf->Ln(3);
$pdf->DrawDivider();

// ===== SECCIÓN: DESCRIPCIÓN DEL MANTENIMIENTO =====
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0, 6, $pdf->convertToLatin1('DESCRIPCIÓN DEL MANTENIMIENTO'), 0, 1);
$pdf->Ln(2);

// Rectángulo para la descripción
$pdf->SetDrawColor(221, 221, 221);
$pdf->SetLineWidth(0.5);
$desc_height = 25;
$pdf->Rect(15, $pdf->GetY(), 180, $desc_height);

$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetXY(20, $pdf->GetY() + 3);
$pdf->MultiCell(170, 5, $pdf->convertToLatin1($cabecera['descripcion'] ?? 'No se ha especificado descripción.'));

$pdf->SetY($pdf->GetY() + $desc_height - 15);
$pdf->Ln(10);

// ===== SECCIÓN: TABLA DE EQUIPOS =====
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0, 6, $pdf->convertToLatin1('EQUIPOS ENVIADOS'), 0, 1);
$pdf->Ln(1);

// Encabezados de tabla con colores corporativos
$pdf->SetFillColor(40, 167, 69);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetDrawColor(40, 167, 69);

$pdf->Cell(15, 8, $pdf->convertToLatin1('N°'), 1, 0, 'C', true);
$pdf->Cell(30, 8, $pdf->convertToLatin1('CÓDIGO'), 1, 0, 'C', true);
$pdf->Cell(105, 8, $pdf->convertToLatin1('DESCRIPCIÓN DEL EQUIPO'), 1, 0, 'C', true);
$pdf->Cell(40, 8, 'TIPO MANT.', 1, 1, 'C', true);

// Configurar anchos y alineaciones para la tabla
$pdf->SetWidths(array(15, 30, 105, 40));
$pdf->SetAligns(array('C', 'C', 'L', 'C'));
$pdf->SetFont('Arial', '', 7);

// Datos de la tabla
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(221, 221, 221);

if (!empty($detalles)) {
    $i = 1;
    foreach ($detalles as $detalle) {
        $pdf->Row(array(
            $i++,
            $detalle['codigoActivo'] ?? '',
            $pdf->convertToLatin1($detalle['NombreActivo'] ?? ''),
            $pdf->convertToLatin1($cabecera['tipoMantenimiento'] ?? '')
        ));
    }
} else {
    $pdf->SetWidths(array(190));
    $pdf->SetAligns(array('C'));
    $pdf->Row(array('No hay equipos registrados para este mantenimiento.'));
}

$pdf->Ln(8);

// ===== SECCIÓN: OBSERVACIONES GENERALES =====
$pdf->SetDrawColor(221, 221, 221);
$pdf->SetLineWidth(0.5);
$obs_height = 25;
$pdf->Rect(15, $pdf->GetY(), 180, $obs_height);

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(40, 167, 69);
$pdf->SetXY(20, $pdf->GetY() + 2);
$pdf->Cell(0, 5, 'OBSERVACIONES GENERALES:', 0, 1);
$pdf->SetTextColor(0, 0, 0);

$pdf->SetFont('Arial', '', 8);
$pdf->SetX(20);
$pdf->MultiCell(170, 4, $pdf->convertToLatin1($cabecera['observaciones'] ?? 'Sin observaciones adicionales.'));

$pdf->Ln(35); // Más espacio antes de las firmas

// ===== SECCIÓN: FIRMAS =====
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(10); // Espacio generoso para firmas

// Línea de firma centrada
$line_y = $pdf->GetY();
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0.5);
$pdf->Line(70, $line_y, 140, $line_y); // Línea centrada

$pdf->Ln(8); // Espacio entre línea y nombre

// Nombre del responsable
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetX(70);
$pdf->Cell(70, 5, $pdf->convertToLatin1($cabecera['responsable'] ?? ''), 0, 1, 'C');

$pdf->Ln(2); // Pequeño espacio

// Título del cargo
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(102, 102, 102);
$pdf->SetX(70);
$pdf->Cell(70, 4, 'Responsable de Mantenimiento', 0, 1, 'C');

// Generar PDF - 'I' para visualizar en navegador
$filename = 'mantenimiento_' . ($cabecera['codigoMantenimiento'] ?? 'reporte') . '.pdf';
$pdf->Output('I', $filename);
