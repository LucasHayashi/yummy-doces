<?php
ob_start();
session_start();
require_once 'Database.class.php';
$title = "Adiciona Item";
$db = Database::conexao();

$idusuario = $_SESSION['idusuario'];
$idproduto = $_GET['idproduto'];

$sql = $db->prepare("UPDATE e2.carrinho
                        SET qtdcompra = qtdcompra - 1
                        WHERE idusuario = :idusuario
                        AND idproduto = :idproduto
                        AND concluido = 'Não'");

$sql->execute(array('idusuario' => $idusuario, 'idproduto' => $idproduto));

if ($sql->rowCount() > 0){
    $sql = "SELECT qtdcompra \"qtd\"
            FROM e2.carrinho
                WHERE idusuario = '$idusuario'
            AND idproduto = $idproduto
            AND concluido = 'Não'";

    $rs     = $db->query($sql);

    #retorna exatamente o valor da consulta, seja número, string ou booleano.
    $return = $rs->fetch(PDO::FETCH_ASSOC);

    if ($return['qtd'] == 0){
        header("location: excluir-item.php?idproduto=$idproduto");
    }else {
        header('location:carrinho.php');
    }
        ob_end_flush();
}else {
    echo "Ops! Houve um erro ao atualizar a quantidade do produto no carrinho de compras.";
}
unset($db);
?>