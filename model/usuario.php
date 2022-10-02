<?php
require_once '../lib/db.php';
class UsuarioModel extends Db
{
    public $name = 'usuario';
    public $page;

    public function __construct($page = 'usuario')
    {   
        $this->page = $page;
        parent::__construct();
    }

    /**
     * Faz o Select para verificar o Login
     * $email = email do usuario
     * $password = senha do usuario
     */
    public function findLogin($email, $password)
    {
        $email = strip_tags(addslashes($email));
        $password = md5($password);

        $return = $this->query("SELECT * FROM usuario
             WHERE usuario.usuario_email = '{$email}'
             AND usuario.usuario_senha = '{$password}'
             AND usuario.usuario_ativo = 'Ativo'", 'load');

        return $return;
    }

    /**
     * Busca todos os registros para a Listagem
     */
    public function findAll($busca, $pgn)
    {
        $sql = '';

        if(!empty($busca['nome'])){
            $sql .= (!empty($sql) ? " AND " : " WHERE ") . "usuario_nome LIKE '%{$busca['nome']}%'";
        }

        return $this->querypgn("SELECT * FROM usuario 
            $sql
            ORDER BY usuario_nome ASC", $pgn, 8, 4);
    }

    /**
     * Verifica se Existe usuario com Email
     * $email = email a verificar
     * $id = caso seja um update, um id de usuario para ignorar
     */
    public function verificaEmail($email, $id = false){
        $sql = is_numeric($id) ? "AND usuario_id != '$id'": '';
        return $this->query("SELECT * FROM usuario 
                    WHERE usuario_email = '$email'
                    $sql",'load');
    }
}
