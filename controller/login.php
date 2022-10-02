<?php 
require_once '../model/usuario.php';

class loginController{

    private $page = 'login';

    function index(){
        $html = file_get_contents("../view/{$this->page}/index.phtml");

        $replace = array('display' => 'none');

        $modelUsuario = new UsuarioModel();

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $fetch = $modelUsuario->findLogin($_POST['email'], $_POST['senha']);
            
            if($fetch){
                $_SESSION['dados'] = $fetch;
                $_SESSION['login'] = 'autorizado';

                header("Location: ?page=home");
            }else{
                $replace['display'] = 'block';
                $replace['email'] = $_POST['email'];
            }
        }

        return Util::replaceArrtoString($html, $replace);
    }

    function logout(){
        
        session_destroy();
        header("Location: ?page=login");
    }
   
}