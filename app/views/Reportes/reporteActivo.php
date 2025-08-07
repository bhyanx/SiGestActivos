<?php
session_start();
require_once("../../config/configuracion.php");
require_once("../../models/GestionarActivos.php");

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
} catch (Exception $e) {
    die("Error al obtener datos del activo: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha Técnica de Activo</title>
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

        .ficha-title {
            font-weight: bold;
            margin: 5px 0;
            color: #28A745;
        }

        .ficha-number {
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

        .activo-details {
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

        .activo-info-title {
            color: #28A745;
            font-weight: bold;
            margin: 15px 0 10px 0;
            text-transform: uppercase;
        }

        .activo-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .activo-table th,
        .activo-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .activo-table th {
            background-color: #28A745;
            color: white;
            font-weight: bold;
            font-size: 11px;
            width: 30%;
        }

        .activo-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .aditional-info {
            margin: 15px 0;
            border: 1px solid #ddd;
            padding: 15px;
        }

        .aditional-title {
            color: #28A745;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        input[type="text"] {
            width: 100%;
            padding: 5px;
            border: 1px solid #ddd;
        }

        .description {
            margin: 15px 0;
            border: 1px solid #ddd;
            padding: 15px;
        }

        .description-title {
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
                    <div class="company-name">LUBRISENG</div>
                    <div class="company-subname">Gestión de Activos</div>
                    <!-- <div class="address">
                        Dirección fiscal: [DIRECCIÓN FISCAL]<br>
                        Sucursal: [SUCURSAL]
                    </div> -->
                </div>
            </div>
            <div class="document-info">
                <div class="ruc"></div>
                <div class="ficha-title">FICHA TÉCNICA DE ACTIVO</div>
                <div class="ficha-number">N° <?php echo $detalleActivo['codigo'] ?? ''; ?></div>
            </div>
        </div>
        <!-- Activo Details -->
        <div class="activo-details">
            <div>
                <div class="detail-row">
                    <div class="label">Código:</div>
                    <div class="value"><?php echo $detalleActivo['codigo'] ?? ''; ?></div>
                </div>
                <div class="detail-row">
                    <div class="label">Nombre:</div>
                    <div class="value"><?php echo $detalleActivo['NombreActivo'] ?? ''; ?></div>
                </div>
                <div class="detail-row">
                    <div class="label">Categoría:</div>
                    <div class="value"><?php echo $detalleActivo['Categoria'] ?? ''; ?></div>
                </div>
            </div>
            <div>
                <div class="detail-row">
                    <div class="label">Fecha de Adquisición:</div>
                    <div class="value"><?php echo date('d/m/Y', strtotime($detalleActivo['fechaAdquisicion'] ?? '')); ?></div>
                </div>
                <div class="detail-row">
                    <div class="label">Estado:</div>
                    <div class="value"><?php echo $detalleActivo['Estado'] ?? ''; ?></div>
                </div>
                <div class="detail-row">
                    <div class="label">Ubicación:</div>
                    <div class="value"><?php echo $detalleActivo['Sucursal']. ' - ' . $detalleActivo['Ambiente']; ?></div>
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- Activo Information -->
        <div class="activo-info">
            <div class="activo-info-title">INFORMACIÓN TÉCNICA DEL ACTIVO</div>
            <table class="activo-table">
                <tbody>
                    <tr>
                        <th>Marca:</th>
                        <td><?php echo $detalleActivo['Marca'] ?? ''; ?></td>
                    </tr>
                    <tr>
                        <th>Número de Serie:</th>
                        <td><?php echo $detalleActivo['Serie'] ?? ''; ?></td>
                    </tr>
                    <tr>
                        <th>Valor de Adquisición:</th>
                        <td><?php echo $detalleActivo['valorAdquisicion'] ?? ''; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Additional Information -->
        <div class="aditional-info">
            <div class="aditional-title">INFORMACIÓN ADICIONAL</div>
            <div class="detail-row">
                <div class="label">Proveedor:</div>
                <div class="value"><?php echo $detalleActivo['RazonSocial'] ?? ''; ?></div>
            </div>
            <!-- <div class="detail-row">
                <div class="label">Garantía (meses):</div>
                <div class="value"><?php //echo $detalleActivo['Garantia'] ?? ''; ?></div>
            </div> -->
        </div>

        <!-- Components -->
        <div class="activo-info">
            <div class="activo-info-title">COMPONENTES DEL ACTIVO</div>
            <table class="activo-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($componentes)): ?>
                        <?php foreach ($componentes as $componente): ?>
                            <tr>
                                <td><?php echo $componente['CodigoComponente'] ?? ''; ?></td>
                                <td><?php echo $componente['NombreComponente'] ?? ''; ?></td>
                                <td><?php echo $componente['Observaciones'] ?? ''; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center;">No hay componentes asociados a este activo.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Description -->
        <!-- <div class="description">
            <div class="description-title">Descripción:</div>
            <div><?php //echo $detalleActivo['descripcion'] ?? ''; ?></div>
        </div> -->

        <!-- Conformidad -->
        <div class="section">
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-name"><?php echo $detalleActivo['NombreResponsable'] ?? ''; ?></div>
                    <div class="signature-dni">DNI: <?php echo $detalleActivo['idResponsable'] ?></div>
                    <div class="title-signature">Responsable</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    <div style="text-align: center; margin-top: 20px; padding: 20px;">
        <button onclick="window.print()" class="btn btn-primary" style="margin-right: 10px; padding: 10px 20px; border: none; border-radius: 5px; background: #0074da; color: white; cursor: pointer;">
            <i class="fas fa-print"></i> Imprimir
        </button>
        <button onclick="descargarPDF()" class="btn btn-success" style="margin-right: 10px; padding: 10px 20px; border: none; border-radius: 5px; background: #dc3545; color: white; cursor: pointer;">
            <i class="fas fa-file-pdf"></i> Descargar PDF (HTML)
        </button>
        <button onclick="descargarPDFNativo()" class="btn btn-success" style="padding: 10px 20px; border: none; border-radius: 5px; background: #28a745; color: white; cursor: pointer;">
            <i class="fas fa-file-pdf"></i> Descargar PDF (Nativo)
        </button>
    </div>

    <!-- Script para descargar PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function descargarPDF() {
            const element = document.querySelector('.document');
            const opt = {
                margin: 1,
                filename: 'ficha_tecnica_<?php echo $detalleActivo['idActivo'] ?? ''; ?>.pdf',
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

        function descargarPDFNativo() {
            const idActivo = <?php echo json_encode($idActivo); ?>;
            window.open('reporteActivoPDF.php?idActivo=' + idActivo, '_blank');
        }
    </script>
</body>

</html>
