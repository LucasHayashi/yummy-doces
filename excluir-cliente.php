<?php
ob_start();
session_start();
$title = "Exclusão de cliente";
include_once "autenticacao.php";
require_once 'Database.class.php';
$db = Database::conexao();

$id = $_GET['id'];

$sql = $db->prepare("UPDATE e2.cadastros
            SET excluido = 'Sim',
		dt_exclusao = CURRENT_TIMESTAMP		
        WHERE idusuario = :id");

$sql->bindParam(':id',$id);

$sql->execute();

if ($sql->rowCount() > 0){
    header('location:usuarios.php?msg1=Cadastro excluído com sucesso');
    ob_end_flush();
}else {
    header('location:usuarios.php?msg2=Falha ao excluir o cadastro');
    ob_end_flush();
}
unset($db);
?>
