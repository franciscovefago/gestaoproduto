<?php

require_once '../model/categoriaproduto.php';
require_once '../model/produto.php';

class categoriaprodutoController
{
    //Nome da Pagina
    private $page = 'categoriaproduto';

    function index()
    {
        $html = file_get_contents("../view/{$this->page}/index.phtml");

        $model = new CategoriaProdutoModel();

        $replace = array('page' => $this->page);

        //Pega parametros
        $pgn = Util::getParam('pgn');
        $busca['nome'] = Util::getPost('nome');

        //Substituir os campos de filtro
        $replace = array_merge($replace, $busca);

        //Busca informações e paginação
        $rtn = $model->findAll($busca, $pgn);

        $trs = '';
        foreach ($rtn['dados'] as $value) {
            $acoes = '<a href="?page=' . $this->page . '&action=cadastrar&codigo=' . $value['categoria_produto_id'] . '" class="text-warning" title="Editar"><i class="fa fa-pencil fa-2x " aria-hidden="true"></i></a>
            <a href="?page=' . $this->page . '&action=deletar&codigo=' . $value['categoria_produto_id'] . '" class="text-danger icon-remove-table deleteConfirm" title="Excluir"><i class="fa fa-trash fa-2x" aria-hidden="true"></i></a>';
            $trs .= util::replaceArrToTr(null, array(
                $value['categoria_produto_nome'],
                $value['categoria_produto_imposto'],
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

        $model = new CategoriaProdutoModel();

        if (util::isPost()) {
            $post = $_POST;
            unset($post['submit']);
            //Passa valores e instruções para validação do formulario
            $valid = util::validPost($post, null, array('categoria_produto_id'));

            if ($valid['status']) {

                if (!is_numeric($_POST['categoria_produto_id'])) {
                    $model->insertRow($post);
                } else {
                    $model->updateRow($post, array('categoria_produto_id'));
                }

                //Redireciona
                header("Location: ?page={$this->page}");
            } else {
                $replace['displayvalid'] = 'block';
                
                $replace = array_merge($replace, $post);
            }
        } else if (is_numeric($codigo)) {
            $fetch = $model->fetchRowArr(array('categoria_produto_id' => $codigo));

            $fetch['categoria_produto_imposto'] = $fetch['categoria_produto_imposto'] * 100;
            //Redireciona caso não encontre o Registro para Preencher o Formulario
            if (!$fetch) {
                header("Location: ?page={$this->page}");
            }

            $replace = array_merge($replace, $fetch);
        }


        return Util::replaceArrtoString($html, $replace);
    }



    function deletar()
    {
        $html = file_get_contents("../view/{$this->page}/deletar.phtml");

        $replace = array('page' => $this->page);

        $codigo = util::getParam('codigo');
        $confirmado = util::getParam('confirmado');
        $model = new CategoriaProdutoModel();
        $modelProduto = new ProdutoModel();

        $fetch = $modelProduto->fetchRowArr(array('categoria_produto_id' => $codigo));

        if (!$fetch) {
            $model->deleteRow(array('categoria_produto_id' => $codigo));
            header("Location: ?page={$this->page}");
        } else {
            return Util::replaceArrtoString($html, $replace);
        }
        
    }
}
