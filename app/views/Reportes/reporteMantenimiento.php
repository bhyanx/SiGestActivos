<?php
session_start();
require_once("../../config/configuracion.php");
require_once("../../models/Mantenimientos.php");

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
        'nombre' => $empresaInfo['nombre'] ?? 'EMPRESA',
        'ruc' => $empresaInfo['ruc'] ?? '00000000000',
        'direccion' => $empresaInfo['direccion'] ?? 'Dirección no disponible',
        'sucursal' => $sucursalInfo['nombre'] ?? 'Sucursal Principal'
    ];

} catch (Exception $e) {
    die("Error al obtener datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Mantenimiento</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Arial Narrow", Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            background: #f5f5f5;
            padding: 20px;
        }

        .document {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 40px;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #28A745;
        }

        .company-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-placeholder {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 12px;
            text-align: center;
        }

        .company-details {
            display: flex;
            flex-direction: column;
        }

        .company-name {
            color: #28A745;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .company-subname {
            color: #28A745;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .address {
            font-size: 10px;
            line-height: 1.3;
            color: #666;
        }

        .document-info {
            border: 1px solid #28A745;
            padding: 10px;
            text-align: center;
            min-width: 200px;
        }

        .ruc {
            font-weight: bold;
            margin-bottom: 5px;
            color: #28A745;
        }

        .guide-title {
            font-weight: bold;
            margin: 5px 0;
            color: #28A745;
        }

        .guide-number {
            font-weight: bold;
            color: #28A745;
        }

        .section {
            margin-bottom: 15px;
        }

        .section-divider {
            border-bottom: 1px solid #ddd;
            margin: 6px 0;
        }

        .transfer-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }

        .label {
            color: #28A745;
            font-weight: bold;
            min-width: 120px;
        }

        .value {
            flex: 1;
        }

        .transfer-type {
            margin: 15px 0;
        }

        .transfer-type-title {
            color: #28A745;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .checkbox-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .checkbox-item input[type="checkbox"] {
            margin: 0;
        }

        .goods-section-title {
            color: #28A745;
            font-weight: bold;
            margin: 15px 0 10px 0;
            text-transform: uppercase;
        }

        .goods-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .goods-table th,
        .goods-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .goods-table th {
            background-color: #28A745;
            color: white;
            font-weight: bold;
            font-size: 11px;
        }

        .goods-table .description-col {
            width: 40%;
            text-align: left;
        }

        .goods-table .code-col {
            width: 15%;
        }

        .goods-table .qty-col {
            width: 10%;
        }

        .goods-table .unit-col {
            width: 15%;
        }

        .goods-table .number-col {
            width: 8%;
            background-color: #28A745;
            color: white;
        }

        .goods-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .transport-section {
            margin: 15px 0;
            border: 1px solid #ddd;
            padding: 15px;
        }

        .transport-title {
            color: #28A745;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .transport-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .transport-detail {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .transport-label {
            color: #28A745;
            font-weight: bold;
            min-width: 120px;
        }

        .transport-value {
            flex: 1;
        }

        input[type="text"] {
            width: 100%;
            padding: 5px;
            border: 1px solid #ddd;
        }

        .observations {
            margin: 15px 0;
            border: 1px solid #ddd;
            padding: 15px;
        }

        .observations-title {
            color: #28A745;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .qr-section {
            display: flex;
            gap: 10px;
        }

        .qr-placeholder {
            width: 80px;
            height: 80px;
            border: 1px solid #ddd;
        }

        .qr-text {
            font-size: 9px;
            max-width: 200px;
        }

        .client-confirmation {
            border: 1px solid #ddd;
            padding: 15px;
            width: 300px;
        }

        .confirmation-title {
            color: #28A745;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
        }

        .signature-field {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .signature-label {
            color: #28A745;
            font-weight: bold;
        }

        .thanks-section {
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin: 0 auto 5px auto;
        }

        .signature-name {
            text-align: center;
            font-size: 10px;
            margin-top: 5px;
        }

        .signature-dni {
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .signature-section {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 10px;
        }


        /* Action Buttons */
        .action-buttons {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
        }

        .btn {
            padding: 10px 20px;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-print {
            background: #0074da;
            color: white;
        }

        .btn-print:hover {
            background: #0072c9;
        }

        .btn-pdf {
            background: #dc3545;
            color: white;
        }

        .btn-pdf:hover {
            background: #c82333;
        }


        @media print {
            .btn {
                display: none !important;
            }

            body {
                padding: 0;
                margin: 0;
            }

            .document {
                box-shadow: none;
                border: none;
            }

            /* Ocultar fecha, hora y URL en la impresión */
            @page {
                margin: 0;
            }

            body::before,
            body::after {
                display: none !important;
            }

            /* Ocultar encabezados y pies de página del navegador */
            @page :first {
                margin-top: 0;
            }

            @page :left {
                margin-left: 0;
            }

            @page :right {
                margin-right: 0;
            }
        }
    </style>
</head>

<body>
    <div class="document">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <div class="logo-placeholder">
                    <img src="../../../public/img/Logo-Lubriseng.png" alt="Logo de Lubriseng" style="width: 100px; height: auto;">
                </div>
                <div class="company-details">
                    <div class="company-name"><?php echo $companyInfo['nombre']; ?></div>
                    <div class="company-subname"><?php echo $companyInfo['nombre']; ?></div>
                    <div class="address">
                        Dirección fiscal: <?php echo $companyInfo['direccion'] ?? ''; ?><br>
                        Sucursal: <?php echo $companyInfo['sucursal'] ?? ''; ?>
                    </div>
                </div>
            </div>
            <div class="document-info">
                <div class="ruc">R.U.C. <?php echo $companyInfo['ruc'] ?? ''; ?></div>
                <div class="guide-title">ORDEN DE MANTENIMIENTO<br>EQUIPOS E INSTALACIONES</div>
                <div class="guide-number">N° <?php echo $cabecera['codigoMantenimiento'] ?? ''; ?></div>
            </div>
        </div>

        <!-- Maintenance Details -->
        <div class="maintenance-details">
            <div>
                <div class="detail-row">
                    <div class="label">Fecha Programada: </div>
                    <div class="value"><?php echo $cabecera['fechaProgramada'] ? date('d/m/Y', strtotime($cabecera['fechaProgramada'])) : 'No programada'; ?></div>
                </div>
                <div class="detail-row">
                    <div class="label">Responsable: </div>
                    <div class="value"><?php echo $cabecera['Proveedor'] ?? ''; ?></div>
                </div>
                <!-- <div class="detail-row">
                    <div class="label">Tipo de Mantenimiento: </div>
                    <div class="value"><?php //echo $detalle['tipoMantenimiento'] ?? ''; ?> </div>
                </div> -->
                <!-- <div class="detail-row">
                    <div class="label">Estado: </div>
                    <div class="value"><?php // echo $cabecera['estadoMantenimiento'] ?? ''; ?></div>
                </div> -->
            </div>
            <div>
                <div class="detail-row">
                    <div class="label">Costo Estimado: </div>
                    <div class="value">S/ <?php echo $cabecera['costoEstimado'] ? number_format($cabecera['costoEstimado'], 2) : '0.00'; ?></div>
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- Maintenance Description -->
        <div class="maintenance-description">
            <div class="description-title">DESCRIPCIÓN DEL MANTENIMIENTO</div>
            <div class="description-content">
                <?php echo !empty($cabecera['descripcion']) ? htmlspecialchars($cabecera['descripcion']) : 'No se ha especificado descripción.'; ?>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- Equipment Table -->
        <div class="equipment-section">
            <div class="equipment-section-title">EQUIPOS ENVIADOS</div>
            <table class="equipment-table">
                <thead>
                    <tr>
                        <th class="number-col">N°</th>
                        <th class="code-col">CÓDIGO</th>
                        <th class="description-col">DESCRIPCIÓN DEL EQUIPO</th>
                        <th class="type-col">TIPO MANT.</th>
                        <!-- <th class="observations-col">OBSERVACIONES</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($detalles)): ?>
                        <?php $i = 1;
                        foreach ($detalles as $detalle): ?>
                            <tr>
                                <td class="text-center"><?php echo $i++; ?></td>
                                <td class="code-text"><?php echo $detalle['codigoActivo'] ?? ''; ?></td>
                                <td class="description-col"><?php echo $detalle['nombreActivo'] ?? ''; ?></td>
                                <td class="type-text"><?php echo $detalle['tipoMantenimiento'] ?? ''; ?></td>
                                <!-- <td class="observations-text"><?php //echo $detalle['observaciones'] ?? ''; ?></td> -->
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">No hay equipos registrados para este mantenimiento.</td>
                        </tr>
                    <?php endif; ?>

                    <!-- Filas vacías para completar la tabla -->
                    <?php for ($k = 0; $k < (1 - count($detalles)); $k++): ?>
                        <tr>
                            <td>&nbsp;</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>

        <!-- General Observations -->
        <div class="observations">
            <div class="observations-title">OBSERVACIONES GENERALES:</div>
            <div class="observations-content">
                <?php echo $cabecera['observaciones'] ?? 'Sin observaciones adicionales.'; ?>
            </div>
        </div>

        <!-- Signatures -->
        <div class="signatures-section">
            <div class="signature-row">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-name"><?php echo $cabecera['responsable'] ?? ''; ?></div>
                    <div class="signature-title">Responsable de Mantenimiento</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="document-footer">
            <div class="footer-text">
                Documento generado el <?php echo date('d/m/Y'); ?> a las <?php echo date('H:i:s'); ?>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <button onclick="window.print()" class="btn btn-print">
            <i class="fas fa-print text-white"></i> Imprimir
        </button>
        <button onclick="descargarPDF()" class="btn btn-pdf">
            <i class="fas fa-file-pdf text-white"></i> Descargar PDF
        </button>
    </div>

    <!-- Script para descargar PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function descargarPDF() {
            const element = document.querySelector('.document');
            const opt = {
                margin: 1,
                filename: 'mantenimiento_<?php echo $cabecera['codigoMantenimiento'] ?? 'reporte'; ?>.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2
                },
                jsPDF: {
                    unit: 'cm',
                    format: 'a4',
                    orientation: 'portrait'
                }
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>

    <style>
        /* Estilos específicos para el reporte de mantenimiento */
        .maintenance-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 1.5rem;
        }

        .maintenance-description {
            margin-bottom: 1.5rem;
        }

        .description-title {
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 0.75rem;
            color: #333;
        }

        .description-content {
            background-color: #f8f9fa;
            padding: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            min-height: 60px;
            line-height: 1.5;
        }

        .equipment-section-title {
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 0.75rem;
            color: #333;
        }

        .equipment-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        .equipment-table th,
        .equipment-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        .equipment-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .number-col {
            width: 40px;
        }

        .code-col {
            width: 80px;
        }

        .type-col {
            width: 100px;
        }

        .observations-col {
            width: 200px;
        }

        .code-text {
            font-family: monospace;
            font-size: 0.9rem;
        }

        .type-text {
            font-size: 0.85rem;
        }

        .observations-text {
            font-size: 0.85rem;
            line-height: 1.3;
        }

        .status-badge {
            background-color: #e3f2fd;
            color: #1976d2;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.85rem;
            display: inline-block;
        }

        .signatures-section {
            margin-top: 3rem;
        }

        .signature-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 2rem;
        }

        .signature-box {
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 4rem;
            margin-bottom: 0.5rem;
        }

        .signature-name {
            font-weight: bold;
            margin-bottom: 0.25rem;
        }

        .signature-title {
            font-size: 0.85rem;
            color: #666;
        }

        .document-footer {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #ddd;
            text-align: center;
        }

        .footer-text {
            font-size: 0.75rem;
            color: #666;
        }

        .observations-content {
            background-color: #f8f9fa;
            padding: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            min-height: 80px;
            line-height: 1.5;
        }

        @media print {
            .action-buttons {
                display: none;
            }

            .document {
                box-shadow: none;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</body>

</html>