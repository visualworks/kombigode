
<?php

//Função de validação dos dados
function validation($name,$lastname,$email,$phone,$cpf){

    global $reg_errors;
    $reg_errors = new WP_Error();

    if (!is_email($email)) {
        echo '<div class="notice notice-error is-dismissible">
                    <h1>Email inválido</h1>
                    Favor digitar um endereço de E-mail válido.
                </div>';
        return false;
    }

    if ( empty( $name ) || empty( $lastname ) || empty( $email ) || empty( $phone ) || empty( $cpf ) ) {
        $reg_errors->add('field', 'Campo Obrigatório');
        echo '<div class="notice notice-error is-dismissible">
                    <h1>Campo Obrigatório não preenchido</h1>
                    Favor cadastrar todos os campos obrigatórios.
                </div>';
        return false;
    }

    if (is_integer($cpf)){
        echo '<div class="notice notice-error is-dismissible">
                    <h1>CPF Inválido</h1>
                    favor digitar apenas os números do CPF.
                </div>';
        return false;
    }





    return true;

}

//Verificação para saber se o plugin já foi ativado e dar a mensagem de warning caso já esteja
if(!isset($_POST['submit']) && null != get_option('me_api_key')){
    echo ' <div class="notice notice-warning is-dismissible">
                    <h1>ATENÇÃO</h1>
                Ao atualizar esses dados você reinicia o plugin e perde dados relacionados a suas antigas compras na plataforma do Melhor Envio.
                </div>';
}

//Submissão para os getters
if( isset($_POST['submit'])){
    $name       = $_POST['name'];
    $lastname   = $_POST['lastname'];
    $email      = sanitize_email($_POST['email']);
    $phone      = $_POST['phone'];
    $cpf        = $_POST['cpf'];
    $cnpj       = $_POST['cnpj'];
    $ie         = $_POST['ie'];

    if(validation($name,$lastname,$email,$phone,$cpf)){
        $array = array('name' => $name, 'lastname' => $lastname, 'email' => $email, 'phone' => $phone, 'cpf' => $cpf , 'cnpj' => $cnpj , 'ie' => $ie );

        if(saveLoja($array)){
                ?>
                <div class="notice notice-success is-dismissible">
                    <h1>Cadastro feito com sucesso</h1>
                    Cadastro feito com sucesso, <a href="<?= get_admin_url(get_current_blog_id(),"/admin.php?page=melhor-envio-config")?>">clique aqui</a> Para ir ao próximo passo.
                </div>
                <?php

        }else{
            ?>
            <div class="notice notice-error is-dismissible">
                    <h1>Erro no cadastro</h1>
                    Erro no cadastro, favor cadastre-se novamente.
                </div>
            <?php
        }

    }
}

?>




<style>

    table{
        margin-top:-25px !important;
    }
</style>

<div class="wrap" >
<h1>Informações da Melhor Envio</h1>
    <span>As informações aqui solicitadas servem para o seu cadastro e login na plataforma da Melhor Envio.</span><br>
    <span>Algumas empresas, tais como a Jadlog solicitam o CNPJ e o IE para a compra de frete.</span>
    <span></span><br><br>
    <div class="panel-wrap">
        <div class="panel postbox" style="padding:2%; margin:0.5% ;">


    <form action=" <?= $_SERVER['REQUEST_URI'] ?>" method="post">
        <table class="form-table">
        <tr>
            <th>
               <label>  Nome  </label></th>
            <td><input name="name" class="wp" type="text" value="<?php if(null != get_option('me_name')) echo get_option('me_name') ;?>" required></td>
        </tr>
        <tr>
            <th> <label>Sobrenome</label></th>
            <td> <input name="lastname" type="text" value="<?php if(null != get_option('me_lastname')) echo get_option('me_lastname') ;?>" required></td>
        </tr>
        <tr>
            <th> <label>E-mail</label></th>
            <td>  <input name="email" type="text" value="<?php if(null != get_option('me_email')) echo get_option('me_email') ;?>" required></td>
        </tr>
        <tr>
            <th>  <label>Telefone</label></th>
            <td> <input name="phone" type="text" value="<?php if(null != get_option('me_phone')) echo get_option('me_phone') ;?>" required></td>
        </tr>
        <tr>
            <th> <label>CPF</label></th>
            <td>  <input name="cpf" type="text" value="<?php if(null != get_option('me_cpf')) echo get_option('me_cpf') ;?>" required></td>
        </tr>
        <tr>
            <th>  <label>CNPJ</label></th>
            <td>  <input name="cnpj" type="text" value="<?php if(null != get_option('me_cnpj')) echo get_option('me_cnpj') ;?>"></td>
        </tr>
        <tr>
            <th>  <label>IE</label></th>
            <td>  <input name="ie" type="text" value="<?php if(null != get_option('me_ie')) echo get_option('me_ie') ;?>"></td>
        </tr>
        <tr>
        <br> <br>
        <td><button name="submit" type="submit" value="submit" class="button button-primary"> Cadastre-se</button></td>
        </tr>
        </table>
    </form>
    </div>
</div>

</div>

