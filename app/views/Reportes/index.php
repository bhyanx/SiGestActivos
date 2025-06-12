<?php
// Asegurarnos de que no haya salida antes del PDF
ob_start();

// Definir la ruta de las fuentes y cargar las clases necesarias
define('FPDF_FONTPATH', __DIR__ . '/../../../public/plugins/fpdf/font/');
require_once(__DIR__ . '/../../../public/plugins/fpdf/fpdf.php');
require_once(__DIR__ . '/../../models/GestionarActivos.php');

// Verificar que no haya errores de PHP
error_reporting(E_ALL);
ini_set('display_errors', 0);

class PDF extends FPDF
{
    public $show_footer = true;
    protected $skipFooter = false;
    protected $widths;
    protected $aligns;

    function __construct()
    {
        parent::__construct();
        // No need to add fonts as Helvetica is a core font
    }

    function SetWidths($w)
    {
        $this->widths = $w;
    }

    function SetAligns($a)
    {
        $this->aligns = $a;
    }

    function Row($data)
    {
        $nb = 0;
        for($i=0; $i<count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5*$nb;
        $this->CheckPageBreak($h);
        for($i=0; $i<count($data); $i++)
        {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, $w, $h);
            $this->MultiCell($w, 5, $data[$i], 0, $a);
            $this->SetXY($x+$w, $y);
        }
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt)
    {
        if(!isset($this->CurrentFont))
            $this->Error('No font has been set');
        $cw = &$this->CurrentFont['cw'];
        if($w==0)
            $w = $this->w-$this->rMargin-$this->x;
        $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
        $s = str_replace("\r",'',$txt);
        $nb = strlen($s);
        if($nb>0 && $s[$nb-1]=="\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while($i<$nb)
        {
            $c = $s[$i];
            if($c=="\n")
            {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep = $i;
            $l += $cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i = $sep+1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }

    function Header()
    {
        $this->SetFont('Helvetica','B',12);
        $this->Cell(0,10,utf8_decode('REPORTE DE ACTIVO'),0,1,'C');
        $this->Ln(5);
    }

    function Footer()
    {
        if($this->show_footer && !$this->skipFooter) {
            $this->SetY(-15);
            $this->SetFont('Helvetica','I',8);
            $this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
        }
    }

    function CuerpoActivo($datos)
    {
        $this->SetFont('Helvetica','B',10);
        $this->Cell(0,10,utf8_decode('INFORMACIÓN DEL ACTIVO'),0,1,'L');
        $this->Ln(2);

        // Datos del activo
        $this->SetFont('Helvetica','',10);
        $this->SetWidths(array(50,140));
        $this->SetAligns(array('L','L'));

        $this->Row(array(utf8_decode('Código:'), utf8_decode($datos['CodigoActivo'])));
        $this->Row(array(utf8_decode('Nombre:'), utf8_decode($datos['NombreArticulo'])));
        $this->Row(array(utf8_decode('Marca:'), utf8_decode($datos['MarcaArticulo'])));
        $this->Row(array(utf8_decode('Serie:'), utf8_decode($datos['NumeroSerie'])));
        $this->Row(array(utf8_decode('Sucursal:'), utf8_decode($datos['Sucursal'])));
        $this->Row(array(utf8_decode('Ambiente:'), utf8_decode($datos['Ambiente'])));
        $this->Row(array(utf8_decode('Estado:'), utf8_decode($datos['Estado'])));
        $this->Row(array(utf8_decode('Categoría:'), utf8_decode($datos['Categoria'])));
        
        if(!empty($datos['FechaAdquisicion'])) {
            $this->Row(array(utf8_decode('Fecha de Adquisición:'), utf8_decode($datos['FechaAdquisicion'])));
        }
        if(!empty($datos['ValorAdquisicion'])) {
            $this->Row(array(utf8_decode('Valor de Adquisición:'), utf8_decode('S/ ' . number_format($datos['ValorAdquisicion'], 2))));
        }
        if(!empty($datos['Observaciones'])) {
            $this->Row(array(utf8_decode('Observaciones:'), utf8_decode($datos['Observaciones'])));
        }
    }

    function CuerpoMovimiento($datos)
    {
        $this->SetFont('Arial','',10);

        // Información del movimiento
        $this->Cell(0,10,'FECHA: ' . date('d/m/Y', strtotime($datos['FechaMovimiento'])), 0, 1);
        $this->Cell(0,10,'MOVIMIENTO: ' . $datos['idMovimiento'], 0, 1);
        $this->MultiCell(0, 7, 'TIPO DE MOVIMIENTO: ' . $datos['tipoMovimiento']);

        // Datos del responsable y destinatario
        $this->Ln(5);
        $this->Cell(0,10,'DATOS DEL RESPONSABLE Y DESTINATARIO',0,1);
        $this->Cell(95,10,'Nombre (Entrega): ' . $datos['responsableOrigen'], 0, 0);
        $this->Cell(0,10,'Nombre (Recibe): ' . $datos['responsableDestino'], 0, 1);
        $this->Cell(95,10,'Sucursal Origen: ' . $datos['sucursalOrigen'], 0, 0);
        $this->Cell(0,10,'Sucursal Destino: ' . $datos['sucursalDestino'], 0, 1);
        $this->Cell(95,10,'Ambiente Origen: ' . $datos['ambienteOrigen'], 0, 0);
        $this->Cell(0,10,'Ambiente Destino: ' . $datos['ambienteDestino'], 0, 1);

        // Información de los activos
        $this->Ln(5);
        $this->Cell(0,10,'INFORMACION BASICA DE LOS ACTIVOS',0,1);
        $this->SetFillColor(200,200,200);
        $this->Cell(30,10,'Codigo',1,0,'C',true);
        $this->Cell(80,10,'Descripcion',1,0,'C',true);
        $this->Cell(50,10,'Marca',1,0,'C',true);
        $this->Cell(30,10,'No. Serial',1,1,'C',true);

        foreach($datos['activos'] as $activo){
            $this->Cell(30,10,$activo['CodigoActivo'],1);
            $this->Cell(80,10,$activo['NombreArticulo'],1);
            $this->Cell(50,10,$activo['MarcaArticulo'],1);
            $this->Cell(30,10,$activo['NumeroSerie'],1,1);
        }

        // Clausula y observaciones
        $this->Ln(8);
        $this->MultiCell(0,7,"CLAUSULA DE COMPROMISO:\nComo funcionario de la empresa declaro que los activos relacionados en el presente formato están bajo mi responsabilidad y me comprometo a darles el uso adecuado y a reportar cualquier novedad que se presente con los mismos.");

        $this->Ln(5);
        $this->MultiCell(0,7,"OBSERVACIONES: " . ($datos['observaciones'] ?? 'Sin observaciones'));

        // Firmas
        $this->Ln(15);
        $this->Cell(60,10,'Vo.Bo. Activos Fijos',0,0,'C');
        $this->Cell(60,10,'Firma Responsable del Activo',0,0,'C');
        $this->Cell(60,10,'Firma Quien Recibe',0,1,'C');
        $this->Cell(60,10,'','T',0,'C');
        $this->Cell(60,10,'','T',0,'C');
        $this->Cell(60,10,'','T',1,'C');
    }
}

// Función para generar el PDF de un activo
function generarPDFActivo($idActivo) {
    try {
        // Obtener datos del activo
        $gestionarActivos = new GestionarActivos();
        $datosActivo = $gestionarActivos->obtenerActivoPorId($idActivo);
        
        if (empty($datosActivo)) {
            throw new Exception("No se encontró el activo especificado");
        }

        // Crear y generar el PDF
        $pdf = new PDF();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->CuerpoActivo($datosActivo);
        
        // Generar nombre del archivo
        $nombreArchivo = 'activo_' . $idActivo . '_' . date('YmdHis') . '.pdf';
        
        // Guardar el PDF
        $pdf->Output('F', __DIR__ . '/../../../public/reports/' . $nombreArchivo);
        
        return $nombreArchivo;
    } catch (Exception $e) {
        error_log("Error al generar PDF: " . $e->getMessage());
        throw $e;
    }
}

// Función para generar el PDF
function generarPDFMovimiento($idMovimiento) {
    try {
        // Obtener datos del movimiento
        $gestionarMovimientos = new GestionarMovimientos();
        $datosMovimiento = $gestionarMovimientos->obtenerHistorialMovimiento($idMovimiento);
        
        if (empty($datosMovimiento)) {
            throw new Exception("No se encontró el movimiento especificado");
        }

        // Preparar datos para el PDF
        $datos = $datosMovimiento[0]; // Tomamos el primer registro ya que contiene la información principal
        
        // Crear y generar el PDF
        $pdf = new PDF();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->CuerpoMovimiento($datos);
        
        // Generar nombre del archivo
        $nombreArchivo = 'movimiento_' . $idMovimiento . '_' . date('YmdHis') . '.pdf';
        
        // Guardar el PDF
        $pdf->Output('F', __DIR__ . '/../../../public/reports/' . $nombreArchivo);
        
        return $nombreArchivo;
    } catch (Exception $e) {
        error_log("Error al generar PDF: " . $e->getMessage());
        throw $e;
    }
}

// Endpoint para generar el reporte
if (isset($_GET['idActivo'])) {
    try {
        $nombreArchivo = generarPDFActivo($_GET['idActivo']);
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $nombreArchivo . '"');
        readfile(__DIR__ . '/../../../public/reports/' . $nombreArchivo);
    } catch (Exception $e) {
        echo "Error al generar el reporte: " . $e->getMessage();
    }
} elseif (isset($_GET['idMovimiento'])) {
    try {
        $nombreArchivo = generarPDFMovimiento($_GET['idMovimiento']);
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $nombreArchivo . '"');
        readfile(__DIR__ . '/../../../public/reports/' . $nombreArchivo);
    } catch (Exception $e) {
        echo "Error al generar el reporte: " . $e->getMessage();
    }
}

try {
    // Obtener datos del activo
    $objActivo = new GestionarActivos();
    
    // Determinar el parámetro a usar
    $codigoActivo = null;
    if (isset($_GET['codigo'])) {
        $codigoActivo = $_GET['codigo'];
    } elseif (isset($_GET['idActivo'])) {
        // Si tenemos idActivo, primero obtenemos el código
        $filtros = [
            'pCodigo' => null,
            'pIdEmpresa' => null,
            'pIdSucursal' => null,
            'pIdCategoria' => null,
            'pIdEstado' => null
        ];
        $resultados = $objActivo->consultarActivos($filtros);
        $activo = array_filter($resultados, function($item) {
            return $item['idActivo'] == $_GET['idActivo'];
        });
        $activo = array_values($activo)[0] ?? null;
        if ($activo) {
            $codigoActivo = $activo['CodigoActivo'];
        }
    }

    if (empty($codigoActivo)) {
        throw new Exception('No se proporcionó un código de activo válido');
    }

    // Consultar el activo usando el código
    $filtros = [
        'pCodigo' => $codigoActivo,
        'pIdEmpresa' => null,
        'pIdSucursal' => null,
        'pIdCategoria' => null,
        'pIdEstado' => null
    ];

    $resultados = $objActivo->consultarActivos($filtros);
    $datos = $resultados[0] ?? null;

    if (!$datos) {
        throw new Exception('Activo no encontrado');
    }

    // Limpiar cualquier salida anterior
    ob_clean();

    // Generar PDF
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->CuerpoActivo($datos);
    
    // Limpiar buffer y enviar PDF
    ob_end_clean();
    $pdf->Output('I', 'Reporte_Activo_' . $datos['CodigoActivo'] . '.pdf');

} catch (Exception $e) {
    // Limpiar cualquier salida anterior
    ob_clean();
    
    // Enviar error como JSON
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
    exit;
}
?>
