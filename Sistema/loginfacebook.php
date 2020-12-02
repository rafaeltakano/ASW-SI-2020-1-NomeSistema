<?php
//Inclui o SDK PHP do Facebook
require('sdk-facebook/facebook.php');

//Cria uma instância do objeto Facebook passando o AppID e Secret
$facebook = new Facebook(array(
    'appId' => '000000000000000',
    'secret' => '000000000000000000000000000000',
));

//Define as permissões que serão solicitadas para a API
$perm = 'public_profile_email';

//Cria uma variável com a URL necessária para o usuário efetuar login
$urlLogin = $facebook->getLoginUrl(array('scope' => $perm));

//Captura o UID do usuário logado. Caso ele não esteja logado esse método retorna 0;
$user = $facebook->getUser();

if($user){
    try {
        //Teste para garantir que temos acesso ao usuário via API
        $facebook->api('/me');
    } catch (FacebookApiException $e){
        //Se falhar o valor null é atribuido
        $user = null;
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Login com Facebook</title>
    </head>
    <body>
        <?php
            //Verifica se o usuário está deslogado ou se ele ainda não deu as permissões
            if(is_null($user) || $user==0){
                //Caso não esteja autenticado, um link é apresentado pedindo para ele acessar o Facebook e conceder as permissões solicitadas
        ?>
                <a href="<?php echo $urlLogin;?>">Entrar com o Facebook</a>
        <?php
            }
            else{
                //Caso ele esteja logado e tenha dado as permissões uma query em FQL (Facebook Query Language) é montada para obter os dados desejados
                $fql = "'SELECT uid, email, username, name, pic_square, profile_url, sex FROM user WHERE uid = me()'"

                //A instrução abaixo utiliza o método API para executar a query em FQL e retornar o resultado para a variável $result. O resultado desta chamada contém os dados solicitados formatados em um Array
                $result = $facebook->api(array('method' => 'fql.query', 'query' => $fql));
                $userData = $result[0];
        ?>
                <p>
                    Nome: <?php echo $userData['name'];?><br>
                    username: <?php echo $userData['username'];?><br>
                    Link Perfil: <?php echo $userData['profile_url'];?><br>
                    Email: <?php echo $userData['email'];?><br>
                    Sexo: <?php echo $userData['sex'];?><br>
                    ID: <?php echo $userData['uid'];?><br>
                </p>
                <p><img src="<?php echo $userData['pic_square'];?>"/></p>
        <?php
            }
        ?>
    </body>
</html>