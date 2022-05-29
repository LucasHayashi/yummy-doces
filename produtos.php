<?php
session_start();
$title = "Produtos";
include_once "header.php";
include_once "verificaSessao.php";
require_once 'QtdCarrinho.class.php';
require_once 'Database.class.php';
$db = Database::conexao();


$idusuario = $_SESSION['idusuario'];

#Instancia a classe responsável por mostrar a quantidade de itens no carrinho
$ativaQtdCarrinho = new qtdCarrinho();
$ativaQtdCarrinho->retornaQtd($idusuario);

#Verifica se existe produtos disponíveis na tabela produto

$sql = "SELECT
            p.idproduto     \"id_produto\",
            p.nome          \"nome\",
            p.preco_venda   \"preco_venda\",
            p.quantidade    \"qtd\",
            p.imgname       \"imgname\",
            t.nome          \"tamanho\"
        FROM e2.produto p, e2.tamanho t
            WHERE p.idtamanho = t.idtamanho
            AND p.disponivel  = 'Sim'
            ORDER BY p.nome asc";

$consulta = $db->query($sql);

echo "<div class='grid-container'>";

while ($data = $consulta->fetch(PDO::FETCH_ASSOC)){
    $nome_produto = $data['nome'];
    $preco_venda  = str_replace('.',',',$data['preco_venda']);
    $tamanho      = substr($data['tamanho'],0,1);
    $quantidade   = $data['qtd'];
    $imagem_nome  = $data['imgname'];
    $id_produto   = $data['id_produto'];

    echo "
    <div class='grid-item'>
        <div>
            <img src='../ecommerce/img/produtos/$imagem_nome'>
        </div>
        <div>
            <h3>$nome_produto</h3>
        </div>
        <div>
            <span class='preco'>R$ $preco_venda</span>
        </div>
        <div>
            <button><a href='adicionar-ao-carrinho.php?idproduto=$id_produto'>Adicionar ao carrinho</a></button>
        </div>
        <div class='info'>
            <div class='info-item'>
                <div class='title'>
                    <span>$tamanho</span>
                </div>
                <div class='subtext'>
                    <small>TAMANHO</small>
                </div>
            </div>
            <div class='info-item'>
                    <div class='title'>
                        <span>$quantidade</span>
                    </div>
                    <div class='subtext'>
                        <small>RESTANTE</small>
                    </div>
            </div>
        </div>
    </div>";
}

echo "</div>";

unset($db);

if (isset($_GET['msg'])){
    $msg = $_GET['msg'];
    echo "
    <div class='aviso sucesso'>
        <p>$msg</p>
    </div>
 ";
} else if (isset($_GET['msg2'])){
    $msg = $_GET['msg2'];
    echo "
    <div class='aviso sucesso'>
        <p>$msg</p>
    </div>
 ";
}

include "footer.php";
?>


<script>
    /*
     * Script para adicionar a classe menu-ativo na li produtos do menu
     */
    let item = document.getElementById('menu-produtos');
    item.classList.add('menu-ativo');
</script>