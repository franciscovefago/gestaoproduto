<?php
$status = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    

    $query = file_get_contents('create.sql');

    $conection = new mysqli($_POST['hostname'], $_POST['username'], $_POST['password']);

    if ($conection->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    mysqli_set_charset($conection, 'latin1');

    //Cria a base de dados
    mysqli_multi_query($conection, 'DROP DATABASE IF EXISTS `gerencia_produto`');
    mysqli_multi_query($conection, $query);

    //Alterar arquivo de credenciais
    file_put_contents(
        '../lib/credential.php',
        "<?php 
    class Credendial{
        public static function credenciais(){
            return array(
                'host' => '{$_POST['hostname']}',
                'user' => '{$_POST['username']}',
                'pass' => '{$_POST['password']}',
                'db' => 'gerencia_produto',
            );
        }
    }"
    );

    //Cria usuario padrao
    $_POST['senha'] = md5($_POST['senha']);
    $conection = new mysqli($_POST['hostname'], $_POST['username'], $_POST['password'], 'gerencia_produto');

    $exec = mysqli_query($conection, "INSERT INTO usuario (usuario_nome, usuario_email, usuario_senha, usuario_ativo) VALUES ('padrao', '{$_POST['email']}', '{$_POST['senha']}', 'Ativo')");
    
    if ($exec) {
        $status = 'Banco de dados Criado com Sucesso';
    } else {
        $status = 'Erro ao criar banco de dados ';
    }
}
?>

<html>

<head>
    <title>Setup</title>
    <link href="../public/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container">
        <h2 class="text-center">Setup</h2>
        <h1 class="text-center text-warning"><?php  echo $status; ?></h1>
        <form action="" method="POST">
            <input type="hidden" name="usuario_id" value="{usuario_id}">
            <div class="mb-3">
                <label for="hostname" class="form-label">HostName</label>
                <input type="text" class="form-control" name="hostname" id="hostname" value="localhost" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">UserName</label>
                <input type="text" class="form-control" name="username" id="username" value="root" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="text" class="form-control" name="password" id="password" value="" >
            </div>
            <hr>
            <div class="mb-3">
                <label for="email" class="form-label">Email para Login</label>
                <input type="email" class="form-control" name="email" id="email" value="adm@gmail.com" required>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha para Login</label>
                <input type="text" class="form-control" name="senha" id="senha" value="padrao123" required>
            </div>


            <button type="submit" class="btn btn-primary">Enviar</button>

        </form>
    </div>
</body>

</html>