<?php

//Configurações padrões
session_start();
date_default_timezone_set('America/Sao_Paulo');
header("Content-type: text/html; charset=ISO-8859-1");

require '../lib/util.php';
// require '../lib/Ferramentas.php';

//Page = controller
//action = action
$class = isset($_GET['page']) ? $_GET['page'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : NULL;


if (isset($_SESSION['login']) && $_SESSION['login'] == 'autorizado') {
    //Verifica se
    $patch = "../controller/{$class}.php";

    //Caso não tenha arquivo, ele define a home como padrão
    if (!file_exists($patch)) {
        $patch = '../controller/home.php';
    }

    //Define o corpo do HTML para area adm
    $html = file_get_contents('estrutura/estrutura_principal.html');
} else {
    //arquivo de login
    $class = 'login';
    $patch = '../controller/login.php';

    //Define o corpo do html para o login
    $html = file_get_contents('estrutura/estrutura_login.html');
}

require_once($patch);

//Instancia o controller
$controller = $class . "Controller";
$page = new $controller();

//Define para qual Action vai abrir
if (method_exists($page, $action)) {
    $content = $page->$action();
} else {
    $content = $page->index();
}

//Faz a união da estrutura do html com o conteudo da Action
$tela = str_replace('{corpo}', $content, $html);
//Apaga as tags de substituição não preenchidas
$tela = preg_replace("(([{]{1})([a-zA-Z_0-9_-]*)([}]{1}))", "", $tela);

//Mostra HTML montado
echo $tela;
