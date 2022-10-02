<?php

require_once '../model/usuario.php';

class usuarioController
{
    //Nome da Pagina
    private $page = 'usuario';

    function index()
    {
        $html = file_get_contents("../view/{$this->page}/index.phtml");

        $model = new UsuarioModel();

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
            $acoes = '<a href="?page=' . $this->page . '&action=cadastrar&codigo=' . $value['usuario_id'] . '" class="text-warning" title="Editar"><i class="fa fa-pencil fa-2x " aria-hidden="true"></i></a>
            <a href="?page=' . $this->page . '&action=deletar&codigo=' . $value['usuario_id'] . '" class="text-danger icon-remove-table deleteConfirm" title="Excluir"><i class="fa fa-trash fa-2x" aria-hidden="true"></i></a>';
            $trs .= util::replaceArrToTr(null, array(
                $value['usuario_nome'],
                $value['usuario_email'],
                $value['usuario_ativo'],
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

        $model = new UsuarioModel();

        if (util::isPost()) {
            $post = $_POST;
            unset($post['submit']);
            $valid = util::validPost($post, false, array('usuario_senha', 'usuario_id'));
            if ($valid['status']) {
                
                $email = $model->verificaEmail($post['usuario_email'], $post['usuario_id']);

                //Transforma senha em md5
                if (!empty($post['usuario_senha'])) {
                    $post['usuario_senha'] = md5($post['usuario_senha']);
                } else {
                    unset($post['usuario_senha']);
                }

                if ($email == false) {
                    //Caso tenha numero no USUARIO_ID  será update
                    if (!is_numeric($_POST['usuario_id'])) {
                        $model->insertRow($post);
                    } else {
                        $model->updateRow($post, array('usuario_id'));
                    }

                    //Redireciona
                    header("Location: ?page={$this->page}");
                } else {
                    //Caso já tenha email cadastrado mostra uma mensagem
                    $replace = array_merge($replace, $post);
                    $replace['display'] = 'block';
                }
            } else {
                $replace['displayvalid'] = 'block';
                $replace['textvalid'] = $valid['msg'];
                $replace = array_merge($replace, $post);
            }
        } else if (is_numeric($codigo)) {
            $fetch = $model->fetchRowArr(array('usuario_id' => $codigo));

            //Redireciona caso não encontre o Registro para Preencher o Formulario
            if (!$fetch) {
                header("Location: ?page={$this->page}");
            }

            $fetch[$fetch['usuario_ativo']] = 'selected';
            $replace = array_merge($replace, $fetch);
        }

        return Util::replaceArrtoString($html, $replace);
    }
    function deletar()
    {
        $codigo = util::getParam('codigo');
        $model = new UsuarioModel();
        if ($codigo != $_SESSION['dados']['usuario_id']) {
            $model->deleteRow(array('usuario_id' => $codigo));
        }
        header("Location: ?page={$this->page}");
    }
}
