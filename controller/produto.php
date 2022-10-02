<?php

require_once '../model/produto.php';
require_once '../model/categoriaproduto.php';

class produtoController
{
    //Nome da Pagina
    private $page = 'produto';

    function index()
    {
        $html = file_get_contents("../view/{$this->page}/index.phtml");

        $model = new ProdutoModel();

        $replace = array('page' => $this->page);

        //Pega parametros
        $pgn = Util::getParam('pgn');
        $busca['imovel_codigo'] = Util::getPost('imovel_codigo');

        //Substituir os campos de filtro
        $replace = array_merge($replace, $busca);

        //Busca informações e paginação
        $rtn = $model->findAll($busca, $pgn);

        $trs = '';
        foreach ($rtn['dados'] as $value) {
            $acoes = '<a href="?page=' . $this->page . '&action=cadastrar&codigo=' . $value['produto_id'] . '" class="text-warning" title="Editar"><i class="fa fa-pencil fa-2x " aria-hidden="true"></i></a>
            <a href="?page=' . $this->page . '&action=deletar&codigo=' . $value['produto_id'] . '" class="text-danger icon-remove-table deleteConfirm" title="Excluir"><i class="fa fa-trash fa-2x" aria-hidden="true"></i></a>';
            $trs .= util::replaceArrToTr(null, array(
                $value['categoria_produto_nome'],
                $value['produto_nome'],
                util::intToMoney($value['produto_valor'], 'grid'),
                $value['produto_status'],
                $acoes
            ));
        }

        //Inclui no replace conteudo de tabela e paginação
        $replace['table_dados'] = $trs;
        $replace['paginacao'] = $rtn['paginacao'];

        //
        return Util::replaceArrtoString($html, $replace);
    }


    function cadastrar()
    {
        $html = file_get_contents("../view/{$this->page}/cadastrar.phtml");

        $codigo = util::getParam('codigo');
        $replace = array('page' => $this->page, 'display' => 'none', 'displayvalid' => 'none');

        $selectop = array('categoria_produto_id' => '');

        $model = new ProdutoModel();
        $modelCatProduto = new CategoriaProdutoModel();

        if (util::isPost()) {
            $post = $_POST;
            unset($post['submit']);
            //Passa valores e instruções para validação do formulario
            $valid = util::validPost($post, null, array('produto_id'));

            if ($valid['status']) {

                $post['produto_valor'] = util::intToMoney($post['produto_valor'], 'db');

                if (!is_numeric($_POST['produto_id'])) {
                    $model->insertRow($post);
                } else {
                    $model->updateRow($post, array('produto_id'));
                }
                //Redireciona
                header("Location: ?page={$this->page}");
            } else {
                $replace['displayvalid'] = 'block';
                $replace['textvalid'] = $valid['msg'];
                $replace = array_merge($replace, $post);
                //Popular o select
                $selectop['categoria_produto_id'] = $post['categoria_produto_id'];
            }
        } else if (is_numeric($codigo)) {
            $fetch = $model->fetchRowArr(array('produto_id' => $codigo));

            //Redireciona caso não encontre o Registro para Preencher o Formulario
            if (!$fetch) {
                header("Location: ?page={$this->page}");
            }
            //Popular o select
            $selectop['categoria_produto_id'] = $fetch['categoria_produto_id'];

            $replace = array_merge($replace, $fetch);
        }

        //Popula select locador
        $fetchlocador = $modelCatProduto->fetchAll(null, ' ORDER BY categoria_produto_nome ASC');
        $replace['optioncategoria'] = util::replaceArrToOption($fetchlocador, array('value' => 'categoria_produto_id', 'label' => 'categoria_produto_nome'), $selectop['categoria_produto_id']);



        return Util::replaceArrtoString($html, $replace);
    }



    function deletar()
    {
        $html = file_get_contents("../view/{$this->page}/deletar.phtml");

        $replace = array('page' => $this->page);

        $codigo = util::getParam('codigo');
        $confirmado = util::getParam('confirmado');
        $model = new ContratoModel();
        $modelFinanceiro = new FinanceiroModel();

        if ($confirmado == 'true') {
            $modelFinanceiro->deleteRow(array('produto_id' => $codigo));
            $model->deleteRow(array('produto_id' => $codigo));
            header("Location: ?page={$this->page}");
        } else {
            $replace['codigo'] = $codigo;
        }
        return Util::replaceArrtoString($html, $replace);
    }
}
