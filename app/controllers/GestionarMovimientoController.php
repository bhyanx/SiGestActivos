<?php

require_once '../models/GestionarMovimientos.php';

class GestionarMovimientoController{

    private $movimientoModel;

    public function __construct(){
        $this->movimientoModel = new GestionarMovimientos();
    }

    public function registrarMovimiento(){
        $dataMovimiento = [
            'idTipoMovimiento' => $_POST['tipo'],
            'idAutorizador' => $_POST['autorizador'],
            'idSucursalOrigen' => $_POST['sucursal_origen'],
            'idSucursalDestino' => $_POST['sucursal_destino'],
            'observaciones' => $_POST['observacion']
        ];

        $idMovimiento = $this->movimientoModel->crearMovimiento($dataMovimiento);

        foreach($_POST['activos'] as $activo){
            $detalle = [
                'IdMovimiento' => $idMovimiento,
                'IdActivo' => $activo,
                'IdSucursal_Nueva' => $_POST['sucursal_destino'],
                'IdAmbiente_Nueva' => $_POST['ambiente_destino'],
                'IdResponsable_Nueva' => $_POST['responsable_destino'],
                'IdActivoPadreOrigen' => $_POST['activo_padre']
            ];
            $this->movimientoModel->crearDetalleMovimiento($detalle);
        }

        header('Location: ../views/movimiento_exito.php'); // Redirección después del registro
        exit;
    }

    public function listarMovimientos(){
        $idMovimiento = $_GET['idMovimiento'];
        $detalles = $this->movimientoModel->listarDetalleMovimientos($idMovimiento);
        return $detalles;
    }
}

?>
