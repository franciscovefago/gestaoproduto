<?php
require_once '../lib/db.php';
class VendaModel extends Db
{
    public $name = 'venda';
    public $page;

    public function __construct($page = 'venda')
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

        if(!empty($busca['de']) && !empty($busca['ate'])){
            $de = util::dateFormat($busca['de'], 'db') . ' 00:00:00';
            $ate = util::dateFormat($busca['ate'], 'db') . ' 23:59:59';
            $sql = " AND venda.venda_data_emitida BETWEEN '$de' AND '$ate'";
        }

        return $this->querypgn("SELECT *,
            (SELECT sum(venda_produto.venda_produto_valor * venda_produto.venda_produto_qtd) as total FROM venda_produto WHERE venda_produto.venda_id = venda.venda_id) as total,
            (SELECT sum((venda_produto.venda_produto_valor * venda_produto.venda_produto_qtd) * (venda_produto.venda_produto_percentual / 100)) FROM venda_produto WHERE venda_produto.venda_id = venda.venda_id) as totalimpostos
            FROM venda
            WHERE venda.venda_id IS NOT NULL
            $sql
            ORDER BY venda.venda_data_emitida ASC", $pgn, 8, 4);
    }

}
