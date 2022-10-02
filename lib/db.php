<?php
require_once 'credential.php';
class Db
{

    public $conection;

    public function __construct()
    {
        $this->openConnection();
    }

    /**
     * Busca varios registros
     * $wherearr = array com key|value do where
     * $order = order by para o sql
     */
    public function fetchAll($wherearr, $order)
    {
        $where = 'WHERE';

        //Monta o WHERE do SELECT com os dados do Array
        if (is_array($wherearr)) {
            foreach ($wherearr as $key => $value) {
                $where .= " $key = '$value' AND";
            }
        } else {
            $where = '';
        }

        //Tira o Ultimo AND da String
        $where = trim($where, 'AND');

        //
        return $this->query("SELECT * FROM {$this->name} $where $order", 'list');
    }

    /**
     * Faz um select para Buscar um item com informações do array
     * $wherearr = array com key|value do where
     */
    public function fetchRowArr($wherearr)
    {
        $where = 'WHERE';

        //Monta o WHERE do SELECT com os dados do Array
        if (is_array($wherearr)) {
            foreach ($wherearr as $key => $value) {
                $where .= " $key = '$value' AND";
            }
        } else {
            $where = '';
        }

        //Tira o Ultimo AND da String
        $where = trim($where, 'AND');

        //
        return $this->query("SELECT * FROM {$this->name} $where", 'load');
    }

    /**
     * Inserir um registro no banco baseado em um array key|value
     * $array = valores para inserir
     */
    public function insertRow($array)
    {
        $insertname = '';
        $insertvalue = '';

        //Manipula os valores para inserir
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (!empty($value)) {
                    $insertname .= "$key ,";
                    $insertvalue .= "'$value' ,";
                }
            }
        }


        //Tira a ultima virgula de ambos
        $insertname = trim($insertname, ',');
        $insertvalue = trim($insertvalue, ',');

        //
       $this->query("INSERT INTO {$this->name} ($insertname) VALUES ($insertvalue)", 'out');
       $id = $this->query('SELECT LAST_INSERT_ID() as codigo', 'load');
       
       return isset($id['codigo']) ? $id['codigo'] : false;
    }

    /**
     * Realizar UPDATE no banco baseado em um array key|value
     * $array = valores para inserir 
     * $wherearr = Somente as KEYS do $array que deseja colocar no WHERE
     */
    public function updateRow($array, array $wherearr)
    {
        $where = 'WHERE ';
        $update = '';

        //Monta o WHERE do SELECT com os dados do Array
        if (is_array($wherearr)) {
            foreach ($wherearr as $value) {
                $where .= " $value = '{$array[$value]}' AND";
            }
        }

        //Manipula os valores para UPDATE
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if($value === null){
                    $update .= "$key = null ,";
                }else{
                    $update .= "$key = '$value' ,";
                }
                
            }
        }

        //Tira o Ultimo AND da String
        $where = trim($where, 'AND');
        $update = trim($update, ',');

        //
        return $this->query("UPDATE {$this->name} SET $update $where", 'out');
    }

    /**
     * Realizar DELETE no banco baseado em um array key|value
     * $wherearr = um array com as informações do WHERE
     */
    public function deleteRow(array $wherearr)
    {
        $where = '';

        //Monta o WHERE do SELECT com os dados do Array
        if (is_array($wherearr)) {
            foreach ($wherearr as $key => $value) {
                $where .= " $key = '$value' AND";
            }
        }

        //Tira o Ultimo AND da String
        $where = trim($where, 'AND');

        //Garante que vai ter WHERE, para não deletar todos os registros da tabela
        if (!empty($where)) {
            return $this->query("DELETE FROM {$this->name} WHERE $where", 'out');
        } else {
            return false;
        }
    }

    /**
     * Requisição Query
     * $stringQuery = query
     * $type = List = varios itens | Load = Somente um Item
     */
    public function query($stringQuery, $type = 'list')
    {
        $return = false;
        //Realiza a query
        
        $result = mysqli_query($this->conection, $stringQuery);

        if ($type == 'list') {
            //Trás todos os registros no formato ASSOC
            $return = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else if ($type == 'load') {
            //Trás só um registro no formato ASSOC
            $return = mysqli_fetch_assoc($result);
        } else if ($type == 'out') {
            //Recomendado para Update, Insert, Delete
            $return = $result ? true : false;
        }else{
            $return = $result ? $result : false;
        }

        return $return;
    }

    /**
     * 
     */
    public function querypgn($stringQuery, $pgn = 0, $qtdtela = 8, $qtdpgn = 6)
    {
        $sqlcount = mysqli_query($this->conection, $stringQuery);

        $totRow = mysqli_num_rows($sqlcount);

        $indicepgn = ((int)$pgn) * $qtdtela;

        $result = mysqli_query($this->conection, $stringQuery . " LIMIT $indicepgn,$qtdtela");

        $result = mysqli_fetch_all($result, MYSQLI_ASSOC);

        $htmlpaginacao = $this->getPaginacao($pgn, $qtdtela, $qtdpgn, $totRow);

        return array('dados' => $result, 'paginacao' => $htmlpaginacao);
    }

    /**
     * 
     * $pgn = Indice da Pagina
     * $qtdtela = Quantidade de Ul na Paginação
     * $totRow = Total de Itens do Select
     */
    public function getPaginacao($pgn, $qtdtela, $qtdpgn, $totRow)
    {
        $rtn = "";

        //Total de Paginas
        $totpag = ceil($totRow / $qtdtela);

        //Verifica se não está passando da quantidade de paginas
        $pgn = $pgn > $totpag ? $totpag : (int)$pgn;

        //Divide quantidade do filtro, metade para antes da pagina atual, e metado para apos
        $digitos = intval($qtdpgn / 2);

        //Antes da pagina atual
        for ($i = ($pgn - $digitos); $i < $pgn; $i++) {

            if ($i >= 0) {
                $rtn .= '<li class="page-item"><a class="page-link" href="?page=' . $this->page . '&pgn=' . $i . '">' . $i + 1 . '</a></li>';
            }
        }

        //Pagina atual
        $rtn .= '<li class="page-item active"><a class="page-link" href="?page=' . $this->page . '&pgn=' . $pgn . '">' . $pgn + 1 . '</a></li>';

        //Depois da Pagina atual
        for ($i = ($pgn + 1); $i <= ($pgn + $digitos); $i++) {
            if ($i < $totpag) {
                $rtn .= '<li class="page-item"><a class="page-link" href="?page=' . $this->page . '&pgn=' . $i . '">' . $i + 1 . '</a></li>';
            } else {
                break;
            }
        }

        return '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">' . $rtn . "</ul></nav>";
    }
    /**
     * Abre conexão com o banco
     */
    public function openConnection()
    {
        $credenciais = Credendial::credenciais();
        $conection = new mysqli($credenciais['host'], $credenciais['user'], $credenciais['pass'], $credenciais['db']);
        if ($conection->connect_error) {
            echo 'Erro ao conectar ao banco, favor verifique as credenciais';
            exit;
        }
        mysqli_set_charset($conection, 'latin1');
        $this->conection = $conection;
    }

   
   
}
