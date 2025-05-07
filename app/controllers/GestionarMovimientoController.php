<?php

//? SE USAN FUNCIONES Y NO UN CASE, YA QUE TIENE UNA SOLA FUNCIONALIDAD EN DONDE LA VAMOS A LLAMAR DIRECTAMENTE DESDE EL FORMULARIO
//? Y NO SE VA A HACER UNA PETICION DESDE UN BOTON DE ACCION, POR LO QUE NO SE NECESITA UN SWITCH CASE

require_once '../models/GestionarMovimientos.php';

class GestionarMovimientoController{

    private $movimientoModel;

    public function __construct(){
        $this->movimientoModel = new GestionarMovimientos();
    }

    //* FUNCION DEL CONTROLADOR QUE SE ENCARGA DE REGISTRAR UN MOVIMIENTO EN LA BASE DE DATOS 

    public function registrarMovimiento(){
        //? EN ESTA FUNCIÓN SE CREA PRINCIPALMENTE EL MOVIMIENTO Y LUEGO DE ESO, PASAMOS A CREAR LOS DETALLES QUE VIENE A SER LOS ATRIBUTOS DEL MOVIMIENTO Y SE ACTUALIZA AUTOMÁTICAMENTE EL ACTIVO EN LA BASE DE DATOS
        //? SE DEBE ENVIAR EL ID DEL MOVIMIENTO A REGISTRAR

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

    //* FUNCION DEL CONTROLADOR QUE SE ENCARGA DE LISTAR LOS DETALLES DE UN MOVIMIENTO ENCONTRADO EN LA BASE DE DATOS
    public function listarMovimientos(){
        $idMovimiento = $_GET['idMovimiento'];
        $detalles = $this->movimientoModel->listarDetalleMovimientos($idMovimiento);
        return $detalles;
    }
}

?>
