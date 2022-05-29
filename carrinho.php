<?php
session_start();
include_once "verificaSessao.php";
include_once "header.php";
require_once 'QtdCarrinho.class.php';
require_once 'Database.class.php';
$db = Database::conexao();
$title = "Carrinho";
$usuario = $_SESSION['usuario'];
$idusuario = $_SESSION['idusuario'];

#Instancia a classe responsável por mostrar a quantidade de itens no carrinho
$ativaQtdCarrinho = new qtdCarrinho();
$ativaQtdCarrinho->retornaQtd($idusuario);

echo
"<div id='div-cart'>
        <table>
            <tr>
                <th>Produto</th>
                <th>Tamanho</th>
                <th>Quantidade</th>
                <th>Preço</th>
                <th>Subtotal</th>
                <th>Excluir</th>
            </tr>
            ";

$sql = "SELECT 
            p.idproduto   \"idproduto\",
            p.nome        \"nome\",
            t.nome        \"tamanho\",
            c.qtdcompra   \"qtd\",
            p.preco_venda \"preco_venda\"
        FROM e2.carrinho c, e2.produto p, e2.tamanho t
        WHERE c.idusuario = '$idusuario'
            AND c.idproduto = p.idproduto
            AND p.idtamanho = t.idtamanho
            AND c.concluido = 'Não'
        ORDER BY p.nome";

$rs = $db->query($sql);

$total = 0;
$soma  = 0;

while ($data = $rs->fetch(PDO::FETCH_ASSOC)) {
    $subtotal    = str_replace('.', ',', $data['preco_venda'] * $data['qtd']);
    $preco_venda = str_replace('.', ',', $data['preco_venda']);
    $soma       += $data['preco_venda'] * $data['qtd'];
    echo
    "<tr>
            <td>{$data['nome']}</td>
            <td>{$data['tamanho']}</td>
            <td>
               <ul class='qtd-carrinho'>
                    <li> <a href='remove-item.php?idproduto={$data['idproduto']}'><i class='material-icons'>remove</i></a></li>
                    <li>{$data['qtd']}</li>
                    <li><a href='adiciona-item.php?idproduto={$data['idproduto']}'><i class='material-icons'>add</i></a></li>
               </ul>
            </td>
            <td>R$ {$preco_venda}</td>
            <td>R$ {$subtotal}</td>
            <td><a href='excluir-item.php?idproduto={$data['idproduto']}' title='excluir'><i class='material-icons'>clear</i></a></td>
        </tr>";
}

$total = str_replace('.', ',', $soma);

echo "<tfoot>
            <tr>
                <td>Total:</td>
                <td>R$ $total</td>
                <td colspan='4'><button id='btn-comprar'><a href='finalizar-pedido.php'>Finalizar a compra</a></button></td>
            </tr>
        </tfoot>
        </table> </div>";
unset($db);
include_once "footer.php";
?>

<script>
    let item = document.getElementById('menu-carrinho');
    item.classList.add('menu-ativo');
</script>