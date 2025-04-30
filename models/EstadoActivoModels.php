<?php
class EstadoActivoModels extends Conectar{
    public function get_EstadoActivo(){
        $conectar = parent::ConexionBdPracticante();

        $sql = "SELECT * FROM tEstadoActivo";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_EstadoActivo_id($IdEstadoActivo){
        $conectar = parent::ConexionBdPracticante();

        $sql = "SELECT *  FROM tEstadoActivo WHERE IdEstadoActivo = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $IdEstadoActivo);
        $sql->execute();
        return $resultado = $sql->fetchObject();
    }

    
}
?>