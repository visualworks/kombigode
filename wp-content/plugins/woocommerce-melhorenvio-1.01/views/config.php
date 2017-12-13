<?php


//Pegando as transportadoras
$transporte = getTransportadoras();


//Fazendo o Submit das transportadoras
if(isset($_POST['submit'])){
    if(saveTransportadoras($_POST)){

        ?>
        <div class="notice notice-success is-dismissible">
            <h1>Configurações Salvas</h1>
            Configurações salvas com sucesso, não se esqueça de cadastrar o endereço de remetente.
        </div>

        <?php
    }
}

//Fazendo o Submit dos Addresses
if(isset($_POST['submit2'])){
    if(saveRemetente($_POST)){
        echo '<div class="notice notice-success is-dismissible">
                    <h1>Remetentes Salvos</h1>
                    Endereço de remetente salvo com sucesso, vá para o seu <a href="">painel de pedidos Melhor Envio</a> e veja os seus pedidos .
                </div>';
    }
}


$selectedshippers = (array) getTransportadoraSelecionada();

//Pegando os Addresses
$address = getRemetente();
?>

<div class="wrap">



    <style>

        .checkboxtransporte,.imgtransporte{
            display: inline-block;
            vertical-align: middle;
        }

        label{
            display:block;
        }

        .imgtransporte{
            padding: 0px;
            margin: 6px;
            border-color: rgba(20,20,20,0.4);
            border-style: solid;
            border-width: 2px;
            transition: 200ms;
            opacity: 0.4;

        }

        .imgtransporte:hover{
            border-color: rgba(0,0,200,0.6);
            border-style: solid;
            border-width: 2px;
            padding: 0px;
            margin: 6px;
            transition: 200ms;

        }

        <?php foreach ($transporte as $key => $t):?>
        #chk<?=$key?>:checked ~ #img<?=$key?>{
            border-color: rgba(0,150,0,1);
            border-style: solid;
            border-width: 2px;
            opacity: 0.9;
        }
        <?php
                  endforeach;

              ?>

    </style>

    <h1>Configurações</h1>
    <p>Aqui você configura o seu plugin do Melhor Envio para funcionamento de acordo com as suas necessidades. <br>Preste atenção ao selecionar suas transportadores e opcionais, eles influenciam diretamente no cálculo de frete.</p>
    <div class="">



        <div class="panel-wrap">


            <div style="width: 40%;float: left;margin:0.5%; padding:4%;" class="panel postbox">
                <h3>Configurações do Plugin</h3>
                <form action="<?= $_SERVER['REQUEST_URI']?>" method="post">

                    <label for="">Mostrar frete para o cliente?</label> <br>
                    <select class="" name="show_in_front">
                        <option value="1">Mostrar valor</option>
                        <option value="0">Não mostrar valor</option>
                    </select>
                    <br> <br> <br>

                    <label for="">Definir porcetagem de lucro sobre o frete:</label> <br>
                    <input type="text" name="profit_value" value="<?=$selectedshippers['profit_value'];?>">
                    <br> <br> <br>

                    <label for="">Definir dias extras para envio:</label> <br>
                    <input type="text" name="profit_days" value="<?=$selectedshippers['profit_days'];?>">
                    <br> <br> <br>

                    <label for="">Utilizar declaração de valor?</label> <br>
                    <select class="" name="value_declaration">
                        <option value="0">Não usar declaração de valor.</option>
                        <option value="1">Usar declaração de valor.</option>
                    </select> <br> <br> <br>

                    <label for="">Entrega de mão própria?</label> <br>
                    <select class="" name="own_hand">
                        <option value="0">Não usar mão própria.</option>
                        <option value="1">Usar mão própria.</option>
                    </select> <br> <br> <br>

                    <label for="">Aviso de recebimento?</label> <br>
                    <select class="" name="receipt">
                        <option value="0">Não exigir aviso de recebimento.</option>
                        <option value="1">Exigir aviso de recebimento.</option>
                    </select> <br> <br> <br>

                    <h5>Escolha as transportadoras:</h5>
                    <?php
                    if(is_array($transporte)):
                    foreach ($transporte as $key => $transportadora  ):
                        ?>
                        <label>

                            <input id="chk<?=$key?>"  value="<?=$transportadora->name?>" type="checkbox" name="<?=$transportadora->name?>" class="checkboxtransporte"
                                <?php

                                print_r(isset($selectedshippers[str_replace(" ","_",$transportadora->name)]));

                                if(isset($selectedshippers[str_replace(" ","_",$transportadora->name)])){
                                    echo " checked='checked'";
                                }
                                ?>>

                            <img id="img<?=$key?>" src="<?= $transportadora->icon?>" width="100px;" title="<?=$transportadora->name?>" class="imgtransporte">

                        </label>
                        <?php
                    endforeach;
                    endif;
                    ?>

                    <br><br>
                    <button type="submit" name="submit" class="button button-primary">Salvar Configurações</button>
                </form>
            </div>

            <div style="width: 40%; float: left;margin:0.5%; padding:4%;" class="panel postbox">
                <h3>Dados do Remetente</h3>
                <form action="<?= $_SERVER['REQUEST_URI']?>" method="post">
                    <label for="">E-mail :<?php echo get_option('me_email');?> </label><br><br>

                    <label for="">CEP</label><br>
                    <input type="text" name="cep" value="<?php if (isset($address->cep)){echo $address->cep;} ?> "> <br><br>

                    <label for="">Endereço</label><br>
                    <input type="text" name="address" value="<?php if(isset($address->address)){ echo $address->address;} ?>"><br><br>

                    <label for="">Numero</label><br>
                    <input type="text" name="number" value="<?php if(isset($address->number)){echo $address->number;} ?>"><br><br>

                    <label for="">Bairro</label><br>
                    <input type="text" name="district" value="<?php if(isset($address->district)){echo $address->district;} ?>"><br><br>

                    <label for="">Complemento</label><br>
                    <input type="text" name="complement" value="<?php if(isset($address->complement)){echo $address->complement;} ?>"><br><br>

                    <label for="">Cidade</label><br>
                    <input type="text" name="city" value="<?php if(isset($address->city)){echo $address->city;} ?>"><br><br>

                    <label for="">UF</label><br>
                    <input type="text" name="uf" value="<?php if(isset($address->uf)){echo $address->uf;} ?>"><br><br>

                    <button type="submit" name="submit2" class="button button-primary">Salvar Remetente</button>


                </form>
            </div>

        </div>

        <div>

        </div>
    </div>

</div>