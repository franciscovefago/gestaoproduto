<?php
class Util
{


    /**
     * Validação do Post enviado em forma de array
     * $arr = post para validacao
     * $condic = condição para validacao de valores especificos
     */
    public static function validPost($arr, $condic = false, $notvalid = array())
    {
        $valid = true;
        $msg = '';
        //Realiza validação se foi digitado algo no campo
        foreach ($arr as $key => $value) {
            if (empty($value) && !in_array($key, $notvalid)) {
                $valid = false;
                $msg = 'É necessário preencher todos os campos';
                break;
            }
        }

        //Validacao especifica
        if (is_array($condic)) {
            foreach ($condic as $key => $value) {
                $type = $value['type'];
                switch ($type) {
                    case 'max':
                        $iptvld = $arr[$key] > $value['max'];
                        if ($iptvld) {
                            $valid = false;
                            $msg = "O campo {$value['nmipt']} tem valor maximo de {$value['max']}";
                        }
                        break;
                }
            }
        }

        return array('status' => $valid, 'msg' => $msg);
    }

    /**
     * Substitui as Tags {} de uma String, com os valores do array
     */
    public static function replaceArrtoString($string, $arr)
    {
        if (is_array($arr)) {
            foreach ($arr as $key => $value) {
                $string = str_replace('{' . $key . '}', $value, $string);
            }
        }
        return $string;
    }


    /**
     * Tranforma um array em Linhas de TD HTML
     * $id = id da TR
     * $array = array para transformar em TD
     */
    public static function replaceArrToTr($id = '', $array)
    {
        $td = '';
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $idtd = !empty($id) ? " id='$key-$id'" : '';
                $td .= "<td $idtd>$value</td>";
            }
        }

        return "<tr id='$id'>" . $td . "</tr>";
    }


    /**
     * Tranforma um array em Linhas de Option HTML
     * $config = array com quais valores do array popular
     * $array = array para transformar em option
     */
    public static function replaceArrToOption($array, $config, $selected = false)
    {
        $option = '';
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                //Selecionar Opção caso queira preencher
                $opselected = $selected == $value[$config['value']] ? 'selected' : '';

                //Monta o HTML
                $option .= "<option value='{$value[$config['value']]}' $opselected>{$value[$config['label']]}</option>";
            }
        }

        return $option;
    }
    /**
     * Busca parametro envido pelo Get
     * $nome = Nome do Atributo desejado do $_GET
     */
    public static function getParam($nome)
    {
        return isset($_GET[$nome]) ? $_GET[$nome] : '';
    }

    /**
     * Busca parametro envido pelo Get
     * $nome = Nome do Atributo desejado do $_GET
     */
    public static function getPost($nome)
    {
        return isset($_POST[$nome]) ? $_POST[$nome] : '';
    }

    /**
     * Verifica se a Requisição é POST
     * $nome = Nome do Atributo desejado do $_GET
     */
    public static function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST' ? true : false;
    }

    /**
     * Formata um número inteiro para o formato de moeda em real
     */
    public static function intToMoney($int, $tipo = 'grid')
    {

        if ($tipo == 'grid') {
            if (is_numeric($int)) {
                return number_format(($int / 100), 2, ',', '.');
            }
        } else {
            $int = str_replace('.', '', $int);
            $int = str_replace(',', '', $int);
            return $int;
        }
    }

    /**
     * Formata um número inteiro para o formato float
     */
    public static function intToFloat($int)
    {

        if (is_numeric($int)) {
            return number_format(($int / 100), 2, '.', '.');
        }
    }

    /**
     * Formata uma data do formato português para o formato em ingles
     */
    public static function dateFormat($data, $tipo = 'db')
    {
        if ($tipo == 'db') {
            $exp = explode('/', $data);
            if (count($exp) < 3) {
                return '0000-00-00';
            }
            return $exp[2] . '-' . $exp[1] . '-' . $exp[0];
        } else {
            $exp = explode('-', $data);
            if (count($exp) == 3)
                return $exp[2] . '/' . $exp[1] . '/' . $exp[0];
        }
    }

    /**
     * Formata uma data do formato português para o formato em ingles
     */
    public static function datetimeFormat($data, $tipo = 'db')
    {
        $horas = substr($data, 10);
        $data = substr($data, 0, 10);
        
        if ($tipo == 'db') {
            $exp = explode('/', $data);
            if (count($exp) < 3) {
                return '0000-00-00';
            }
            return $exp[2] . '-' . $exp[1] . '-' . $exp[0] . ' ' . $horas;
        } else {
            $exp = explode('-', $data);
            if (count($exp) == 3)
                return $exp[2] . '/' . $exp[1] . '/' . $exp[0] . ' ' . $horas;
        }
    }


    /**
     * Quantidade de meses entre duas datas
     */
    public static function datediff($inicio, $fim)
    {
        $inicio = new DateTime($inicio);
        $fim = new DateTime($fim);
        $diff = $inicio->diff($fim);
        return $diff->m;
    }

    /**
     * Calcula a Mensalidade e Repasse baseada na regra de negocio
     */
    public static function calculaMensalidadeRepasse($data_inicio, $data_fim, $taxaadm, $aluguel, $iptu, $condominio)
    {
        $meses = static::datediff($data_inicio, $data_fim);

        if ($meses > 1 & $meses <= 12) {
            $rtn = array();
            for ($i = 0; $i <= $meses; $i++) {
                if ($i == 0) {
                    //Calcula valor proporcional apenas na primeira parcela
                    $inicio = new DateTime($data_inicio);
                    //Calcular a quantidade de dias da Data
                    $qtddias = $inicio->format('t') - $inicio->format('d');
                    //Calcula valor do aluguel proporcional aos dias
                    $vlaluguel = ($aluguel / $inicio->format('t')) * $qtddias;
                } else {
                    $vlaluguel = $aluguel;
                }
                //Iptu dividido por 12, para caso o contrato seja 6 meses, seja pago proporcional
                $mensaliptu = $iptu / 12;
                //Valor da taxa administrativa da Imobiliaria
                $valoradm = $vlaluguel * ($taxaadm / 100);
                //Mensalidade do locatario
                $mensalidade = $vlaluguel + $valoradm + $mensaliptu + $condominio;
                //Repasse para o Locador
                $repasse = $vlaluguel + $mensaliptu;
                //Formata valores
                $mensalidade = static::intToMoney(static::intToMoney($mensalidade), 'db');
                $repasse = static::intToMoney(static::intToMoney($repasse), 'db');
                //Referencia do Mes da Mensalidade/Repasse
                $referencia = date("m/Y", strtotime("$data_inicio +" . $i . " month"));
                //Vencimento
                $vencimento = date("Y-m-", strtotime("$data_inicio +" . $i + 1 . " month"));
                $rtn[] = array(
                    'mensalidade' =>
                    array('valor' => $mensalidade, 'referencia' => $referencia, 'vencimento' => $vencimento . '01'),
                    'repasse' =>
                    array('valor' => $repasse, 'referencia' => $referencia, 'vencimento' => $vencimento)
                );
            }
            //var_dump($rtn);
            return array('status' => 1, 'arrvalores' => $rtn);
        } else {
            return array('status' => 0, 'msg' => 'Contrato deve conter o periodo de 2 a 12 meses');
        }
    }

    /**
     * Calcula totais de um Contrato
     */
    public static function calculaTotaisContrato($arr)
    {
        $meses = static::datediff($arr['contrato_data_inicio'], $arr['contrato_data_fim']);

        $total = 0;

        for ($i = 0; $i <= $meses; $i++) {
            if ($i == 0) {
                //Calcula valor proporcional apenas na primeira parcela
                $inicio = new DateTime($arr['contrato_data_inicio']);
                //Calcular a quantidade de dias da Data
                $qtddias = $inicio->format('t') - $inicio->format('d');
                //Calcula valor do aluguel proporcional aos dias
                $vlaluguel = ($arr['contrato_valor_aluguel'] / $inicio->format('t')) * $qtddias;
            } else {
                $vlaluguel = $arr['contrato_valor_aluguel'];
            }
            //Iptu dividido por 12, para caso o contrato seja 6 meses, seja pago proporcional
            $mensaliptu = $arr['contrato_valor_iptu'] / 12;
            //Valor da taxa administrativa da Imobiliaria
            $valoradm = $vlaluguel * (util::intToFloat($arr['contrato_taxa_adm']) / 100);
            //Mensalidade do locatario
            $mensalidade = $vlaluguel + $valoradm + $mensaliptu + $arr['contrato_valor_condominio'];
            //Repasse para o Locador
            $repasse = $vlaluguel + $mensaliptu;
            //Formata valores
            $total += $mensalidade;

            //$mensalidade = static::intToMoney(static::intToMoney($mensalidade), 'db');
            //$repasse = static::intToMoney(static::intToMoney($repasse), 'db');


        }
        //var_dump($rtn);
        return util::intToMoney($total);
    }

    /**
     * Valida data e caso nã existe, tras a mais proxima dela
     * $data = Formato Y-m-d
     */
    public static function validaData($data)
    {
        $ano = substr($data, 0, 4);
        $mes = substr($data, 5, 2);
        $dia = substr($data, 8, 2);
        $condic = true;

        $rtn = $data;

        do {
            if (checkdate($mes, $dia, $ano)) {
                $rtn = "{$ano}-{$mes}-{$dia}";
                $condic = false;
            } else {
                $dia -= 1;
            }
        } while ($condic);

        return $rtn;
    }

    public static function convertearrhtml($array)
    {
        $rs = array();
        foreach ($array as $id => $val) {

            if (is_array($val)) {
                foreach ($val as $index => $value) {
                    if ($value != '') { //Verifica se o campo tem valor preenchido
                        //Adiciona ao Array
                        $rs['arr'][$index][$id] = $value;
                    }
                }
            } else {
                $rs[$id] = $val;
            }
        }
        return $rs;
    }
}
