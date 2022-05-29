<?php
ob_start();
session_start();
include_once "verificaSessao.php";
$title = "Finalizar pedido";
include_once "header.php";
require_once 'Database.class.php';
require_once 'QtdCarrinho.class.php';
$db = Database::conexao();

$usuario = $_SESSION['usuario'];
$idusuario = $_SESSION['idusuario'];

#Instancia a classe responsável por mostrar a quantidade de itens no carrinho
$ativaQtdCarrinho = new qtdCarrinho();
$ativaQtdCarrinho->retornaQtd($idusuario);

echo "<div class='container-carrinho'>";

$sql = "SELECT
            nome       \"nome\",
            sobrenome  \"sobrenome\",
            cpf        \"cpf\",
            celular    \"celular\",
            logradouro \"rua\",
            bairro     \"bairro\",
            cidade     \"cidade\",
            estado     \"estado\",
            cep        \"cep\"
        FROM e2.Cadastros
        WHERE idusuario = $idusuario";

$rs = $db->query($sql);

while ($data = $rs->fetch(PDO::FETCH_ASSOC)) {

    echo "<div class='destino'>
        <h3>Destino</h3>
            <ul>
                <li>{$data['nome']} {$data['sobrenome']} - CPF {$data['cpf']}</li>
                <li>{$data['rua']}</li>
                <li>{$data['bairro']}, {$data['cidade']},{$data['estado']}</li>
                <li><b>{$data['cep']}</b></li>
                <li>Cel.: {$data['celular']}</li>
            </ul>
      </div>";
}

$sql2 = "SELECT 
            p.idproduto \"idproduto\",
            p.nome \"nome\",
            t.nome \"tamanho\",
            c.qtdcompra \"qtd\",
            p.preco_venda \"preco_venda\"
        FROM e2.carrinho c, e2.produto p, e2.tamanho t
        WHERE c.idusuario = '$idusuario'
            AND c.idproduto = p.idproduto
            AND p.idtamanho = t.idtamanho
            AND c.concluido = 'Não'";

$rs2 = $db->query($sql2);

$total = 0;
$soma  = 0;

echo "<div class='myorder'>
            <h3>Seu pedido</h3>
                <table>";

while ($data = $rs2->fetch(PDO::FETCH_ASSOC)) { 
    $preco_venda = str_replace('.', ',', $data['preco_venda']);
    $subtotal  = str_replace('.', ',', $data['preco_venda'] * $data['qtd']);
    $soma     += $data['preco_venda'] * $data['qtd'];
    echo
    "<tr>
        <td>{$data['nome']}</td>
        <td><b>Qtd.</b> {$data['qtd']}</td>
        <td><b>Sub. </b>R$ {$subtotal}</td>
    </tr>";
}
$total = str_replace('.', ',', $soma);
echo "<tr>
        <td colspan='3'><b>Total:</b> R$ $total</td>
     </tr>";
echo "</table></div>";
?>

<div class="payment-div">
    <h3>Forma de pagamento</h3>
    <form action="" method="POST" id='payment-methods'>
        <div class="payment-div-item">
            <label for="Cartão de crédito"><input type="radio" name="payment-id" value="Cartão de crédito" checked>Cartão de crédito</label>
            <ul>
                <li><img src="img/payment/elo.svg"></li>
                <li><img src="img/payment/hipercard.svg"></li>
                <li><img src="img/payment/mastercard.svg"></li>
                <li><img src="img/payment/visa.svg"></li>
            </ul>
        </div>
        <div class="payment-div-item">
            <label for="Boleto bancário"><input type="radio" name="payment-id" value="Boleto bancário">Boleto bancário</label>
            <ul>
                <li><img src="img/payment/boleto.svg"></li>
            </ul>
        </div>
        <div class="payment-div-item">
            <label for="Pix"><input type="radio" name="payment-id" value="Pix">Pix</label>
            <ul>
                <li><img src="img/payment/pix.svg"></li>
            </ul>
        </div>
        <div class="payment-div-item">
            <label for="Mercado Pago"><input type="radio" name="payment-id" value="Mercado Pago">Mercado Pago / PagSeguro</label>
            <ul>
                <li><img src="img/payment/mercadopago.svg"></li>
                <li><img src="img/payment/pagseguro.svg"></li>
            </ul>
        </div>
        <div class="payment-div-item">
            <label for="PayPal"><input type="radio" name="payment-id" value="PayPal">PayPal</label>
            <ul>
                <li><img src="img/payment/paypal.svg"></li>
            </ul>
        </div>

        <div class="payment-div-item">
            <input type="submit" name="submit" value="Confirmar compra" id="confirmar">
        </div>
    </form>
</div>

<?php
echo "</div>";



if (isset($_POST['submit'])) {
    $formaDePagamento = $_POST['payment-id'];

    $idvenda = date('Ymdis');

    $addVenda = $db->prepare("INSERT INTO e2.vendas (idvenda,idusuario,total,formaDePagamento,dataConfirmacao)
                    VALUES (:idvenda,:idusuario,:total,:formaDePagamento,CURRENT_TIMESTAMP)");

    $addVenda->execute(array(
        'idvenda' => $idvenda,
        'idusuario' => $idusuario,
        'total' => $total,
        'formaDePagamento' => $formaDePagamento
    ));

    if ($addVenda->rowCount() > 0) {

        $sqlCart = "UPDATE E2.carrinho 
                        SET concluido = 'Sim', 
                            idvenda = :idvenda 
                    WHERE idusuario = :idusuario 
                        AND concluido = 'Não'";

        $updateCart = $db->prepare($sqlCart);
        $updateCart->bindParam(':idvenda', $idvenda);
        $updateCart->bindParam(':idusuario', $idusuario);
        $updateCart->execute();

        if ($updateCart->rowCount() > 0) {
            $sql = "SELECT idproduto, qtdcompra FROM e2.carrinho
                        where idvenda = '$idvenda'
                    AND concluido = 'Sim'";

            $qtdItens = $db->query($sql);

            $rows = $qtdItens->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $key) {
                $idproduto = $key['idproduto'];
                $qtdcompra = $key['qtdcompra'];
                $atualizaEstoque = "UPDATE e2.produto
                                        SET quantidade = quantidade - :qtdcompra
                                    WHERE idproduto = :idproduto ";
                $baixaestoque = $db->prepare($atualizaEstoque);
                $baixaestoque->bindParam(':qtdcompra', $qtdcompra);
                $baixaestoque->bindParam(':idproduto', $idproduto);
                $baixaestoque->execute();
            }
        }

        $status = 'Não';
        $validaEstoque = $db->prepare("UPDATE e2.produto SET disponivel = :status WHERE e2.produto.quantidade <= '0'");
        $validaEstoque->bindParam(':status', $status);
        $validaEstoque->execute();
        header("location: pedido-concluido.php?idvenda=$idvenda");
        ob_end_flush();
    }else {
        echo "Ops, houve um erro ao concluir o seu pedido";
    }
}

unset($db);
include_once "footer.php";
?>