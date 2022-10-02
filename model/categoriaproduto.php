<?php
require_once '../lib/db.php';
class CategoriaProdutoModel extends Db
{
    public $name = 'categoria_produto';
    public $page;

    public function __construct($page = 'categoria_produto')
    {   
        $this->page = $page;
        parent::__construct();
    }
    /**
     * Busca todos os registros para a Listagem
     */
    public function findAll($busca, $pgn)
    {
         $sql = '';

        if(!empty($busca['nome'])){
            $sql .= " WHERE categoria_produto_nome LIKE '%{$busca['nome']}%'";
        }

        return $this->querypgn("SELECT * FROM categoria_produto
            
            $sql
            ORDER BY categoria_produto_nome ASC", $pgn, 8, 4);
    }

}
