<?php

class GestionarActivosController {
    private $model;
    public function __construct(){
        $this->model = new GestionarActivos();
    }
    public function crear($data){
        $data['userMod'] = $_SESSION['usuario'];
        $idActivo = $this->model->gestionActivos('INSERTAR', $data);
        header('Location: /GestionarActivos');
    }

    public function listar($filters){
        $data = [
            'idActivo' => $filters['idActivo'] ?? null,
            'idDocIngresoAlm' => $filters['idDocIngresoAlm'] ?? null,
            'idArticulo' => $filters['idArticulo'] ?? null,
            'codigo' => $filters['codigo'] ?? null,
            'idSucursal' => $filters['idSucursal'] ?? null,
            'idEstado' => $filters['idEstado'] ?? null,
            'enUso' => $filters['enUso'] ?? null,
            'fechaInicio' => $filters['fechaInicio'] ?? null,
            'fechaFin' => $filters['fechaFin'] ?? null,
            'userMod' => $_SESSION['usuario']
        ];

        $activos = $this->model->gestionActivos('CONSULTAR', $data);
        //require_once '../views/';
    }
        
}

?>