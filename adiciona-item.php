<?php
ob_start();
session_start();
include_once "header.php";
require_once 'Database.class.php';
$db = Database::conexao();
$title = "Adiciona item";


$idusuario = $_SESSION['idusuario'];
$idproduto = $_GET['idproduto'];

$sql = $db->prepare("UPDATE e2.carrinho SET qtdcompra = qtdcompra + 1 WHERE idusuario = :idusuario AND idproduto = :idproduto");
$sql->bindParam(':idusuario', $idusuario);
$sql->bindParam(':idproduto', $idproduto);
$sql->execute();

if ($sql->rowCount() >0){
    header('location:carrinho.php');
	ob_end_flush();
}else {
    echo "Ops! Houve um erro ao atualizar a quantidade do produto no carrinho de compras.";
}
pg_close($con);
?>