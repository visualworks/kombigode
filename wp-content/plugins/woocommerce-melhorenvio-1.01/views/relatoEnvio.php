
<div class="wrap">
<h1>Relatório do Pedido</h1>
<?php if(isset($_GET['su']) && $_GET['su'] == 1): ?>
    <div class="notice notice-success is-dismissible"><h1>Pedido cadastrado</h1>
        Seu pedido foi adicionado ao carrinho com sucesso, vá em Carrinho de Compras para confirmar o seu pedido.
    </div>
<?php endif; ?>
<style media="screen">

    .panel-wrap{
        width: 100%;
    }
    p{
        padding: 10px;
    }

    th{
        padding-top: 10px;
        padding-bottom: 10px;
        border: solid 1px;
        border-color:  rgba(20,50,70,0.1);
        width: 215px;
        background-color: rgba(20,50,70,0.07);
        text-align: center;
    }
    th > h5{
        margin: 0 5px;
        font-size: 0.9em;
    }

    th:hover{
        background-color:  rgba(20,50,70,0.1);
    }

    td {
        text-align: center;
        padding: 10px;
        background-color: rgba(20,200,80,0.13);
        font-size: .75em;
    }

    td:nth-child(1){
        width: 10%;
    }

    td:nth-child(2){
        width: 15%;
    }

    td:nth-child(3){
        width: 30%;
    }

    td:nth-child(4){
        width: 30%;
    }

    td:nth-child(5){
        width: 15%;
    }

    .panel{
        width: 70%;
        margin-left:8%;
    }
    .price{
        color:green;
        font-size: large;
        margin:auto;
        text-align: center;
    }

    .btn{
        background-color: #46A450;
        text-decoration: none;
        padding:8px 13px;
        border-radius: 2px;
        color: whitesmoke;
        box-shadow: 0px 1px 1px grey;
        margin:10px;
        display: inline-block;
    }

    .btn:hover{
        background-color: #46B450;
        text-decoration: none;
        padding:8px 13px;
        border-radius: 2px;
        color: whitesmoke;
        box-shadow: 0px 1px 2px grey;

    }

    .left-image{
        float:left;
    }

    .left-image img{
        display: block;
        max-width: 180px;
    }

</style>

<div class="panel-wrap">


    <div class="left-image">
        <?php

        $id = $_GET['id'];


        $cotacao = getData($id);

        if(is_array($cotacao)):

        getImg($id); ?>

    <h1 class="text-center"><?php echo getName($id); ?></h1>
    </div>


<div class="panel postbox" style="float:left;">

    <table style="width:100%;">
        <thead>
        <tr>
            <th><h5>Id da compra</h5></th>
            <th><h5>Status</h5></th>
            <th><h5>Rastreio</h5></th>
            <th><h5>Preço</h5></th>
            <th><h5>Atualizado</h5></th>
        </tr>
        </thead>
        <tbody>

        <?php foreach ($cotacao as $cot): ?>
            <tr>
                <td>
                    <?php echo $cot->ref;?>
                </td>

                <td>
                     <?php

                        if($cot->state != null){
                            switch ($cot->state) {
                                case 1:
                                    echo "Pendente";
                                    break;
                                case 2:
                                    echo "Liberado";
                                    break;
                                case 3:
                                    echo "Postado";
                                    break;
                                case 4:
                                    echo "Entregue";
                                    break;
                                case 0:
                                    echo "Cancelado";
                                    break;
                                case -1:
                                    echo "Não entregue";
                                    break;
                                default:
                                    echo "Indisponível";
                                    break;
                            }
                        }else{
                            echo "Indisponível";
                        }



                        ;?>
                </td>

                <td>
                    <?php if ($cot->tracking == NULL && $cot->price == 0): ?>
                        Código de rastreio ainda indisponível, confirme sua compra.
                    <?php else: ?>
                        <?php if ($cot->price > 0): ?>
                            Código de rastreio ainda indisponível, <a href="http://www.melhorenvio.com.br/vendedor/meus_envios/?a=1.php?>" target="_blank">imprima sua etiqueta.</a>
                        <?php else: ?>
                            <h3> <a href=" <?php echo $cot->tracking;?>"> Rastreio : <?php echo $cot->tracking;?> </a></h3>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>

                <td>
                     <?php

                            if($cot->price != null)
                            {
                                echo "<p class='price'> R$".$cot->price."</p>";
                            }else{
                                echo "Valor ainda indisponível, confirme sua compra.";
                            }

                            ?>

                </td>

                <td>
                    <?php echo  date('d/m/Y',time($cot->updated_at));?>
                </td>

            </tr>


        <?php endforeach; ?>


        </tbody>
    </table>

    <div>
        <?php if ($cotacao[0]->price == null && $cotacao[0]->state == null): ?>

            <p>Esperando confirmação de compra... <br> Acesse o carrinho de compras para confirmar sua compra.</p>
        <?php endif; ?>
        <?php else:?>
            <div class="notice notice-error is-dismissible">
                <h1>Atenção!!!</h1>
                <p>Não foi possível carregar os dados do pedido, recarregue mais tarde ou acesse o carrinho através do botão.</p>
            </div>
        <?php endif; ?>

        <a class="btn" href="<?php echo get_option('me_url');?>" target="_blank">Carrinho de compras</a>
    </div>

</div>

</div>

</div>
