<?php
require_once '../lib/db.php';
class ProdutoModel extends Db
{
    public $name = 'produto';
    public $page;

    public function __construct($page = 'produto')
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

        if (!empty($busca['nome'])) {
            $sql .= " AND produto_nome LIKE '%{$busca['nome']}%'";
        }

        return $this->querypgn("SELECT * FROM produto, categoria_produto
            WHERE categoria_produto.categoria_produto_id = produto.categoria_produto_id
            $sql
            ORDER BY produto_nome ASC", $pgn, 8, 4);
    }

    public function findInfoProduto($produto_id)
    {
        return $this->query("SELECT * FROM produto, categoria_produto
            WHERE categoria_produto.categoria_produto_id = produto.categoria_produto_id
            AND produto.produto_id = '$produto_id'
            ORDER BY produto_nome ASC", 'load');
    }

    public function findAllProdutoSelect($status = 'Sim')
    {
        return $this->query("SELECT *, concat(concat(produto.produto_nome, '-'), categoria_produto.categoria_produto_nome) as produto_nome FROM produto, categoria_produto
            WHERE categoria_produto.categoria_produto_id = produto.categoria_produto_id
            AND produto.produto_status = '$status'
            ORDER BY produto_nome ASC", 'list');
    }
}
