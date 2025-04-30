<?php
class UnidadNegocioModels extends Conectar
{
    public function get_UnidadNegocio($cod_empresa){
        $conectar = parent::ConexionBdPracticante();

        $sql = "SELECT * FROM vUnidadesEmpresa WHERE estadoFuncionamiento = 1 AND cod_Empresa LIKE ? ORDER BY Nombre_local ASC";
        $sql = $conectar->prepare($sql);
        $sql-> bindValue(1, "%".$cod_empresa."%");
        $sql-> execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_UnidadNegocio_id($cod_UnidadNeg){
        $conectar = parent::ConexionBdPracticante();

        $sql = "SELECT * FROM vUnidadesEmpresa WHERE cod_UnidadNegocio = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $cod_UnidadNeg);
        $sql->execute();
        return $resultado = $sql->fetchObject();
    }
        
}
?>