<?php
ob_start();
session_start();
$title = "Exclui item do carrinho";
require_once 'Database.class.php';
$db = Database::conexao();

$idusuario = $_SESSION['idusuario'];

$idproduto = $_GET['idproduto'];

$sql = $db->prepare("DELETE FROM e2.carrinho
            WHERE idproduto = :idproduto
            AND idusuario = :idusuario");

$sql->bindParam(':idproduto', $idproduto);
$sql->bindParam(':idusuario', $idusuario);

$sql->execute();

if ($sql->rowCount() > 0){
    header('location:carrinho.php');
    ob_end_flush();
}else {
    echo "Ops! Houve um erro ao excluir o produto do carrinho de compras.";
}
unset($db);
?>