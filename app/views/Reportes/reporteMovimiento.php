<?php
session_start();
require_once("../../config/configuracion.php");
require_once("../../models/GestionarMovimientos.php");
//require_once("../../models/Combos.php");

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
        $cabecera = $cabecera[0]; // Tomar el primer registro
    }

    // Obtener los tipos de movimiento desde la base de datos
    $tiposMovimiento = $movimientos->obtenerTiposMovimiento();

    // Obtener nombres de responsables y autorizadores
    $responsableOrigen = $movimientos->obtenerEmpleado($cabecera['responsableOrigen']);
    $responsableDestino = $movimientos->obtenerEmpleado($cabecera['responsableDestino']);
    $autorizadorDestino = $movimientos->obtenerEmpleado($cabecera['autorizadorDestino']);

    $cabecera['responsableOrigen'] = $responsableOrigen['NombreTrabajador'] ?? '';
    $cabecera['responsableDestino'] = $responsableDestino['NombreTrabajador'] ?? '';
    $cabecera['autorizadorDestino'] = $autorizadorDestino['NombreTrabajador'] ?? '';
} catch (Exception $e) {
    die("Error al obtener datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guía de Remisión Electrónica</title>
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
                    <img src="/public/img/Logo-Lubriseng.png" alt="Logo de Lubriseng" style="width: 100px; height: auto;">
                </div>
                <div class="company-details">
                    <div class="company-name"><?php echo $cabecera['empresaOrigen'] ?? ''; ?></div>
                    <div class="company-subname"><?php echo $cabecera['empresaOrigen'] ?? ''; ?></div>
                    <div class="address">
                        Dirección fiscal: <?php echo $cabecera['DireccionOrigen'] ?? ''; ?><br>
                        Sucursal: <?php echo $cabecera['sucursalOrigen'] ?? ''; ?>
                    </div>
                </div>
            </div>
            <div class="document-info">
                <div class="ruc">R.U.C. <?php echo $cabecera['RucOrigen'] ?? ''; ?></div>
                <div class="guide-title">GUÍA DE REMISIÓN<br>ELECTRÓNICA REMITENTE</div>
                <div class="guide-number">N° <?php echo $cabecera['CodMovimiento'] ?? ''; ?></div>
            </div>
        </div>
        <!-- Transfer Details -->
        <div class="transfer-details">
            <div>
                <div class="detail-row">
                    <div class="label">Fecha de inicio de traslado:</div>
                    <div class="value"><?php echo date('d/m/Y', strtotime($cabecera['fechaMovimiento'] ?? '')); ?></div>
                </div>
                <div class="detail-row">
                    <div class="label">Destinatario:</div>
                    <div class="value"><?php echo $cabecera['empresaDestino'] ?? ''; ?></div>
                </div>
                <div class="detail-row">
                    <div class="label">RUC:</div>
                    <div class="value"><?php echo $cabecera['RucDestino'] ?? ''; ?></div>
                </div>
            </div>
            <div>
                <div class="detail-row">
                    <div class="label">Punto de partida:</div>
                    <div class="value"><?php echo $cabecera['sucursalOrigen'] ?? ''; ?><br><?php echo $cabecera['DireccionOrigen'] ?? ''; ?></div>
                </div>
                <div class="detail-row">
                    <div class="label">Punto de llegada:</div>
                    <div class="value"><?php echo $cabecera['sucursalDestino'] ?? ''; ?><br><?php echo $cabecera['DireccionDestino'] ?? ''; ?></div>
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- Transfer Type -->
        <div class="transfer-type">
            <div class="transfer-type-title">Motivo de traslado:</div>
            <div class="checkbox-grid">
                <?php
                $tipoMov = $cabecera['tipoMovimiento'] ?? '';
                foreach ($tiposMovimiento as $tipo): ?>
                    <div class="checkbox-item">
                        <input type="checkbox" id="<?php echo strtolower(str_replace(' ', '_', $tipo['nombre'])); ?>"
                            <?php echo ($tipoMov == $tipo['nombre']) ? 'checked' : ''; ?>>
                        <label for="<?php echo strtolower(str_replace(' ', '_', $tipo['nombre'])); ?>"><?php echo $tipo['nombre']; ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="section-divider"></div>

        <!-- Goods Table -->
        <div class="goods-section">
            <div class="goods-section-title">DATOS DEL BIEN TRANSPORTADO</div>
            <table class="goods-table">
                <thead>
                    <tr>
                        <th class="number-col">N°</th>
                        <th class="code-col">CÓDIGO</th>
                        <th class="description-col">DESCRIPCIÓN</th>
                        <th class="qty-col">CANTIDAD</th>
                        <th class="unit-col">UNIDAD DE DESPACHO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($detalles)): ?>
                        <?php $i = 1;
                        foreach ($detalles as $detalle): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo $detalle['Codigo'] ?? ''; ?></td>
                                <td class="description-col"><?php echo $detalle['nombreActivo'] ?? ''; ?></td>
                                <td>[CANTIDAD]</td>
                                <td>[UNIDAD DE DESPACHO]</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">No hay detalles de movimiento para mostrar.</td>
                        </tr>
                    <?php endif; ?>
                    <?php for ($k = 0; $k < (4 - count($detalles)); $k++): // Asumiendo un mínimo de 4 filas de ejemplo 
                    ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>


        <!-- Observations -->
        <div class="observations">
            <div class="observations-title">Observaciones:</div>
            <div><?php echo $cabecera['observaciones'] ?? ''; ?></div>
            <div>Doc. Referencia: [DOC. REFERENCIA]</div>
        </div>

        <!-- Conformidad -->
        <div class="section">
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-name"><?php echo $cabecera['nombreAutorizador'] ?? ''; ?></div>
                    <div class="signature-dni">DNI: <?php echo $cabecera['dniAutorizador'] ?? ''; ?></div>
                    <div class="title-signature">Emisor</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div class="signature-name"><?php echo $cabecera['autorizadorNombre'] ?? ''; ?></div>
                    <div class="signature-dni">DNI: <?php echo $cabecera['autorizadorDni'] ?? ''; ?></div>
                    <div class="title-signature">Receptor</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    <div style="text-align: center; margin-top: 20px; padding: 20px;">
        <button onclick="window.print()" class="btn btn-primary" style="margin-right: 10px;">
            <i class="fas fa-print"></i> Imprimir
        </button>
        <button onclick="descargarPDF()" class="btn btn-success">
            <i class="fas fa-file-pdf"></i> Descargar PDF
        </button>
    </div>

    <!-- Script para descargar PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function descargarPDF() {
            const element = document.querySelector('.document');
            const opt = {
                margin: 1,
                filename: 'movimiento_<?php echo $cabecera['CodMovimiento'] ?? 'reporte'; ?>.pdf',
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
</body>

</html>