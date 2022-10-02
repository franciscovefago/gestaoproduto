<?php

require_once '../model/produto.php';
require_once '../model/venda.php';
require_once '../model/vendaproduto.php';

class vendaController
{
    //Nome da Pagina
    private $page = 'venda';

    function index()
    {
        $html = file_get_contents("../view/{$this->page}/index.phtml");

        $modelProduto = new ProdutoModel();

        $replace = array('page' => $this->page);

        $fetchlocador = $modelProduto->findAllProdutoSelect('Ativo');
        $replace['optioncategoria'] = util::replaceArrToOption($fetchlocador, array('value' => 'produto_id', 'label' => 'produto_nome'), '');
        //
        return Util::replaceArrtoString($html, $replace);
    }


    function cadastrar()
    {

        $modelProduto = new ProdutoModel();
        $modelVenda = new VendaModel();
        $modelVendaProduto = new VendaProdutoModel();
        $return = array('status' => '0', 'mensagem' => 'Ocorreu um Erro');
        if (util::isPost()) {
            $post = $_POST;
            unset($post['submit']);
            //Passa valores e instruções para validação do formulario
            $valid = util::validPost($post, null, array('produto_id'));

            if ($valid['status']) {

                $postnew = util::convertearrhtml($post);

                $arrproduto = $postnew['arr'];
                unset($postnew['arr']);
                $postnew['venda_data_emitida'] = date('Y-m-d H:i:s');

                $venda_id = $modelVenda->insertRow($postnew);

                foreach ($arrproduto as $value) {
                    $fetch = $modelProduto->findInfoProduto($value['produto_id']);
                 
                    $modelVendaProduto->insertRow(array(
                        'venda_id' => $venda_id,
                        'produto_id' => $value['produto_id'],
                        'venda_produto_qtd' => $value['produto_qtd'],
                        'venda_produto_valor' => $fetch['produto_valor'],
                        'venda_produto_percentual' => $fetch['categoria_produto_imposto']
                    ));
                }

                //Redireciona
                $return = array('status' => '1', 'mensagem' => 'Venda Realizada com Sucesso');
            } else {
                $return = array('status' => '0', 'mensagem' => 'Todos os Campos devem ser preenchidos');
            }
        }
        echo json_encode($return);exit;
    }



  
}
