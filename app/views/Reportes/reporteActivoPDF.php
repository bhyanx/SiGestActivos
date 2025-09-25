<?php
// Limpiar buffer de salida para evitar errores en PDF
ob_start();
ob_clean();

session_start();
require_once("../../config/configuracion.php");
require_once("../../models/Mantenimientos.php");
require_once("../../models/GestionarActivos.php");
require_once("../../../public/plugins/fpdf/fpdfRequerimiento.php");

$mantenimiento = new Mantenimientos();
$activos = new GestionarActivos();
$idActivo = $_GET['idActivo'] ?? null;

if (!$idActivo) {
    die("ID de Activo no proporcionado");
}

try {
    $detalleActivo = $activos->obtenerActivoPorId($idActivo);
    if (!$detalleActivo) {
        die("No se encontró el activo con el ID proporcionado");
    }
    $componentes = $activos->obtenerComponente($idActivo);

    // Obtener información de la empresa y sucursal desde la sesión
    $idEmpresa = $_SESSION['cod_empresa'] ?? 1;
    $idSucursal = $_SESSION['cod_UnidadNeg'] ?? 1;

    $empresaInfo = $mantenimiento->obtenerEmpresaInfo($idEmpresa);
    $sucursalInfo = $mantenimiento->obtenerSucursalInfo($idSucursal);

    // Preparar información de la empresa para el reporte
    $companyInfo = [
        'nombre' => $empresaInfo['nombre'] ?? 'LUBRISENG E.I.R.L',
        'ruc' => $empresaInfo['ruc'] ?? '20399129614',
        'direccion' => $sucursalInfo['direccion'] ,
        'sucursal' => $sucursalInfo['nombre'] ?? 'Autocentro Lubriseng'
    ];
    
} catch (Exception $e) {
    die("Error al obtener datos del activo: " . $e->getMessage());
}

class PDF extends FPDF
{
    private $detalleActivo;
    private $companyInfo;

    public function setDetalleActivo($detalleActivo)
    {
        $this->detalleActivo = $detalleActivo;
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
        $this->MultiCell(46, 3, $this->convertToLatin1("GESTION DE ALMACEN\nFICHA TÉCNICA DE ACTIVO"), 0, 'C');

        $this->SetFont('Arial', 'B', 9);
        $this->SetXY(147, 33);
        $this->Cell(46, 4, $this->convertToLatin1('N° ' . ($this->detalleActivo['codigo'] ?? '')), 0, 1, 'C');

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

    // Función para crear secciones de datos del activo
    function CreateAssetDataSection($leftData, $rightData, $y_start)
    {
        $y_left = $y_start;
        $y_right = $y_start;

        // Columna izquierda
        foreach ($leftData as $item) {
            $this->SetFont('Arial', 'B', 9);
            $this->SetTextColor(40, 167, 69);
            $this->SetXY(15, $y_left);
            $this->Cell(30, 5, $this->convertToLatin1($item['label']), 0, 0);
            $this->SetFont('Arial', '', 9);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(60, 5, $this->convertToLatin1($item['value']), 0, 0);
            $y_left += 5;
        }

        // Columna derecha
        foreach ($rightData as $item) {
            $this->SetFont('Arial', 'B', 9);
            $this->SetTextColor(40, 167, 69);
            $this->SetXY(110, $y_right);
            $this->Cell(40, 5, $this->convertToLatin1($item['label']), 0, 0);
            $this->SetFont('Arial', '', 9);
            $this->SetTextColor(0, 0, 0);
            $this->SetXY(150, $y_right);
            $this->Cell(40, 5, $this->convertToLatin1($item['value']), 0, 0);
            $y_right += 5;
        }

        // Establecer la posición Y al final de la sección más larga
        $this->SetY(max($y_left, $y_right));
    }
}

// Información de la empresa
$companyInfo = [
    'nombre' => 'LUBRISENG E.I.R.L',
    'ruc' => '20399129614',
    'direccion' => 'Av Bolognesi - 80-Talara',
    'telefono' => '073-381234',
    'sucursal' => $detalleActivo['Sucursal'] ?? 'Autocentro Lubriseng'
];

// Limpiar buffer antes de generar PDF
ob_end_clean();

// Crear PDF
$pdf = new PDF();
$pdf->setDetalleActivo($detalleActivo);
$pdf->setCompanyInfo($companyInfo);
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);

// ===== SECCIÓN: DETALLES DEL ACTIVO =====
$y_start = $pdf->GetY();

// Datos de la columna izquierda
$leftData = [
    ['label' => 'Código:', 'value' => $detalleActivo['codigo'] ?? ''],
    ['label' => 'Nombre:', 'value' => $detalleActivo['NombreActivo'] ?? ''],
    ['label' => 'Categoría:', 'value' => $detalleActivo['Categoria'] ?? '']
];

// Datos de la columna derecha
$rightData = [
    ['label' => 'Fecha de Adquisición:', 'value' => date('d/m/Y', strtotime($detalleActivo['fechaAdquisicion'] ?? ''))],
    ['label' => 'Estado:', 'value' => $detalleActivo['Estado'] ?? ''],
    ['label' => 'Ubicación:', 'value' => ($detalleActivo['Sucursal'] ?? '') . ' - ' . ($detalleActivo['Ambiente'] ?? '')]
];

$pdf->CreateAssetDataSection($leftData, $rightData, $y_start);
$pdf->Ln(3);
$pdf->DrawDivider();

// ===== SECCIÓN: INFORMACIÓN TÉCNICA DEL ACTIVO =====
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(40, 167, 69);
$pdf->Cell(0, 6, $pdf->convertToLatin1('INFORMACIÓN TÉCNICA DEL ACTIVO'), 0, 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(1);

// Tabla de información técnica
$pdf->SetFillColor(40, 167, 69);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetDrawColor(40, 167, 69);

// Fila 1: Marca
$pdf->Cell(60, 8, 'Marca:', 1, 0, 'L', true);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(130, 8, $pdf->convertToLatin1($detalleActivo['Marca'] ?? ''), 1, 1, 'L');

// Fila 2: Número de Serie
$pdf->SetFillColor(40, 167, 69);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(60, 8, $pdf->convertToLatin1('Número de Serie:'), 1, 0, 'L', true);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(130, 8, $pdf->convertToLatin1($detalleActivo['Serie'] ?? ''), 1, 1, 'L');

// Fila 3: Valor de Adquisición
$pdf->SetFillColor(40, 167, 69);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(60, 8, $pdf->convertToLatin1('Valor de Adquisición:'), 1, 0, 'L', true);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(130, 8, $pdf->convertToLatin1($detalleActivo['valorAdquisicion'] ?? ''), 1, 1, 'L');

$pdf->Ln(5);

// ===== SECCIÓN: INFORMACIÓN ADICIONAL =====
$pdf->SetDrawColor(221, 221, 221);
$pdf->SetLineWidth(0.5);
$info_height = 15;
$pdf->Rect(15, $pdf->GetY(), 180, $info_height);

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(40, 167, 69);
$pdf->SetXY(20, $pdf->GetY() + 2);
$pdf->Cell(0, 5, $pdf->convertToLatin1('INFORMACIÓN ADICIONAL'), 0, 1);
$pdf->SetTextColor(0, 0, 0);

$pdf->SetFont('Arial', 'B', 8);
$pdf->SetTextColor(40, 167, 69);
$pdf->SetX(20);
$pdf->Cell(30, 5, 'Proveedor:', 0, 0);
$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0, 5, $pdf->convertToLatin1($detalleActivo['RazonSocial'] ?? ''), 0, 1);

$pdf->Ln(8);

// ===== SECCIÓN: COMPONENTES DEL ACTIVO =====
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(40, 167, 69);
$pdf->Cell(0, 6, $pdf->convertToLatin1('COMPONENTES DEL ACTIVO'), 0, 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(1);

// Configurar anchos y alineaciones para la tabla de componentes
$pdf->SetWidths(array(40, 80, 70));
$pdf->SetAligns(array('C', 'L', 'L'));
$pdf->SetFont('Arial', 'B', 9);

// Encabezados de tabla de componentes
$pdf->SetFillColor(40, 167, 69);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetDrawColor(40, 167, 69);

$pdf->Row(array(
    $pdf->convertToLatin1('Código'),
    'Nombre',
    'Observaciones'
), true);

// Datos de la tabla de componentes
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 8);
$pdf->SetDrawColor(221, 221, 221);

if (!empty($componentes)) {
    foreach ($componentes as $componente) {
        $pdf->Row(array(
            $componente['CodigoComponente'] ?? '',
            $pdf->convertToLatin1($componente['NombreComponente'] ?? ''),
            $pdf->convertToLatin1($componente['Observaciones'] ?? '')
        ));
    }
} else {
    $pdf->SetWidths(array(190));
    $pdf->SetAligns(array('C'));
    $pdf->Row(array('No hay componentes asociados a este activo.'));
}

$pdf->Ln(25); // Más espacio antes de las firmas

// ===== SECCIÓN: FIRMA DEL RESPONSABLE =====
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(0, 0, 0);
$pdf->Ln(20); // Espacio generoso para firmas

// Línea de firma centrada
$line_y = $pdf->GetY();
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0.5);
$pdf->Line(70, $line_y, 140, $line_y); // Línea centrada

$pdf->Ln(1); // Espacio entre línea y nombre

// Nombre del responsable
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetX(70);
$pdf->Cell(70, 5, $pdf->convertToLatin1($detalleActivo['NombreResponsable'] ?? ''), 0, 1, 'C');

$pdf->Ln(1); // Pequeño espacio

// DNI del responsable
$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(102, 102, 102);
$pdf->SetX(70);
$pdf->Cell(70, 4, 'DNI: ' . ($detalleActivo['idResponsable'] ?? ''), 0, 1, 'C');

$pdf->Ln(1); // Pequeño espacio

// Título del cargo
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetX(70);
$pdf->Cell(70, 4, 'Responsable', 0, 1, 'C');

// Generar PDF - 'I' para visualizar en navegador
$filename = 'ficha_tecnica_' . ($detalleActivo['codigo'] ?? 'activo') . '.pdf';
$pdf->Output('I', $filename);
