<?php
ob_start();
session_start();
$title = "Exclusão de produto";
include_once "autenticacao.php";
require_once 'Database.class.php';
$db = Database::conexao();

$id = $_GET['id'];

$sql = $db->prepare("UPDATE e2.produto
            SET disponivel = 'Não'
        WHERE idproduto = > :id");

$sql->bindParam(':id', $id);

$sql->execute();

if ($sql->rowCount() > 0){
    header('location:lista-produtos.php?msg1=Produto excluído com sucesso');
    ob_end_flush();
}else {
    header('location:lista-produtos.php?msg2=Falha ao excluir o produto');
    ob_end_flush();
}
unset($db);
