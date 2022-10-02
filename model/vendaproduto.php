<?php
require_once '../lib/db.php';
class VendaProdutoModel extends Db
{
    public $name = 'venda_produto';
    public $page;

    public function __construct($page = 'venda_produto')
    {   
        $this->page = $page;
        parent::__construct();
    }
    /**
     * Busca todos os registros para a Listagem
     */
    public function findAll($busca)
    {
         $sql = '';

        if(!empty($busca['venda_id'])){
            $sql .= " AND venda_produto.venda_id = '{$busca['venda_id']}'";
        }

        return $this->query("SELECT * FROM venda_produto, venda, produto
            WHERE venda_produto.venda_id = venda.venda_id
            AND venda_produto.produto_id = produto.produto_id
            $sql
            ", 'list');
    }

}
