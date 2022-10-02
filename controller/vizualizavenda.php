<?php

require_once '../model/venda.php';
require_once '../model/vendaproduto.php';

class vizualizavendaController
{
    //Nome da Pagina
    private $page = 'vizualizavenda';

    function index()
    {
        $html = file_get_contents("../view/{$this->page}/index.phtml");

        $model = new VendaModel();

        $replace = array('page' => $this->page);

        //Pega parametros
        $pgn = Util::getParam('pgn');
        $busca['de'] = Util::getPost('de');
        $busca['ate'] = Util::getPost('ate');

        //Substituir os campos de filtro
        $replace = array_merge($replace, $busca);

        //Busca informações e paginação
        $rtn = $model->findAll($busca, $pgn);

        $trs = '';
        foreach ($rtn['dados'] as $value) {
            $acoes = '<a href="?page=' . $this->page . '&action=info&codigo=' . $value['venda_id'] . '" class="text-warning" title="Editar"><i class="fa fa-search fa-2x " aria-hidden="true"></i></a>';
            $trs .= util::replaceArrToTr(null, array(
                util::datetimeFormat($value['venda_data_emitida'], 'grid'),
                $value['venda_obs'],
                util::intToMoney($value['total']),
                util::intToMoney($value['totalimpostos']),
                $acoes
            ));
        }

        //Inclui no replace conteudo de tabela e paginação
        $replace['table_dados'] = $trs;
        $replace['paginacao'] = $rtn['paginacao'];

        //
        return Util::replaceArrtoString($html, $replace);
    }
    function info()
    {
        $html = file_get_contents("../view/{$this->page}/info.phtml");

        $codigo = util::getParam('codigo');
        $replace = array('page' => $this->page, 'cardprodutos' => '');

        $modelVenda = new VendaModel();
        $modelProdutoVenda = new VendaProdutoModel();
        if (is_numeric($codigo)) {
            $corpocard = '<div class="col-6 p-3"><div class="card">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><b>Produto:</b> {produto_nome}</li>
                    <li class="list-group-item"><b>Quantidade:</b> {venda_produto_qtd}</li>
                    <li class="list-group-item"><b>Total:</b> {venda_produto_total}</li>
                    <li class="list-group-item"><b>Total Impostos:</b> {venda_produto_totalimposto}</li>
                    <li class="list-group-item"><b>SubTotal:</b> {venda_produto_subtotal}</li>
                </ul>
            </div></div>';

            $fetch = $modelVenda->fetchRowArr(array('venda_id' => $codigo));
            if ($fetch) {
                $fetchprodutos = $modelProdutoVenda->findAll(array('venda_id' => $codigo));

                $totais = array('totalprodutos' => 0, 'totalimpostos' => 0);
                foreach ($fetchprodutos as $value) {
                    $card = $corpocard;
                    $valorproduto = $value['venda_produto_valor'] * $value['venda_produto_qtd'];
                    $valorimposto = $valorproduto * ($value['venda_produto_percentual'] / 100);

                    $totais['totalprodutos'] += $valorproduto;
                    $totais['totalimpostos'] += $valorimposto;

                    $card = str_replace('{produto_nome}', $value['produto_nome'], $card);
                    $card = str_replace('{venda_produto_qtd}', $value['venda_produto_qtd'], $card);
                    $card = str_replace('{venda_produto_total}', util::intToMoney($valorproduto), $card);
                    $card = str_replace('{venda_produto_totalimposto}', util::intToMoney($valorimposto), $card);
                    $card = str_replace('{venda_produto_subtotal}', util::intToMoney($valorproduto - $valorimposto), $card);
                    $replace['cardprodutos'] .= $card;
                }

                $replace['venda_obs'] = $fetch['venda_obs'];
                $replace['totalprodutos'] = util::intToMoney($totais['totalprodutos']);
                $replace['totalimpostos'] = util::intToMoney($totais['totalimpostos']);
                $replace['subtotal'] = util::intToMoney($totais['totalprodutos'] - $totais['totalimpostos']);
            } else {
                exit;
            }
            
        }
        
        return Util::replaceArrtoString($html, $replace);
    }
}
