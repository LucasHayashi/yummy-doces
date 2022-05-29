<?php
require_once 'Database.class.php';

class qtdCarrinho {
    public static function retornaQtd($idusuario){
        $db = Database::conexao();
        $qtdcarrinho = 0;
        $sql = "SELECT qtdcompra \"qtd\"
                FROM e2.carrinho 
            WHERE idusuario = '$idusuario'
                AND concluido = 'Não'";

        $countCart = $db->query($sql);

        if ($countCart->rowCount() > 0){
            while ($data = $countCart->fetch(PDO::FETCH_ASSOC)){
                $qtdcarrinho += $data['qtd'];
            }
        }else {
            $qtdcarrinho = 0;
        }

        echo "<span class='contador-carrinho'>$qtdcarrinho</span>";
    }
}

?>