<?php 

class homeController{

    private $page = 'home';

    function index(){
        $html = file_get_contents("../view/{$this->page}/index.phtml");
    
        return $html;
    }
    function cadastrar(){
        return "<h2>cad<h2>";
    }
}