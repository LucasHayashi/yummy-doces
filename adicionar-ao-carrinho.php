<?php
ob_start();
session_start();
include_once "header.php";
include_once "verificaSessao.php";
require_once 'Database.class.php';
$db = Database::conexao();
$title = "Adiciona produto no carrinho";

$idusuario = $_SESSION['idusuario'];

$idproduto = $_GET['idproduto'];

$sql = "SELECT 1 
        FROM e2.carrinho
            WHERE idusuario = $idusuario
            AND idproduto   = $idproduto
            AND concluido   = 'Não'";

$rs = $db->query($sql);

$row = $rs->fetch(PDO::FETCH_ASSOC);

if ($rs->rowCount() == 0){
    $qtd = 1;
    $concluido = 'Não';
    $sql = $db->prepare("INSERT INTO e2.carrinho (idusuario, idproduto, qtdcompra, concluido) 
    VALUES (:idusuario, :idproduto, :qtdcompra, :concluido)");
    $sql->bindParam(':idusuario', $idusuario);
    $sql->bindParam(':idproduto', $idproduto);
    $sql->bindParam(':qtdcompra', $qtd);
    $sql->bindParam(':concluido', $concluido);
    $sql->execute();

    header('location:produtos.php?msg=Produto adicionado ao carrinho!');
    ob_end_flush();

}else if ($rs->rowCount() > 0) {

    $sql = $db->prepare("UPDATE e2.carrinho SET qtdcompra = qtdcompra + 1
            WHERE idusuario = :idusuario AND idproduto = :idproduto");
    $sql->bindParam(':idusuario', $idusuario);
    $sql->bindParam(':idproduto', $idproduto);
    $sql->execute();

    header('location:produtos.php?msg=Produto adicionado ao carrinho!');
    ob_end_flush();
}else {
    header('location:produtos.php?msg2="Erro ao adicionar o produto no carrinho!"');
    ob_end_flush();
}
pg_close($con);
?>