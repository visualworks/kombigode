<?php
/**
 * Created by PhpStorm.
 * User: pikachu
 * Date: 06/06/17
 * Time: 10:08
 */

if(isset($_POST['id'])){
  $volta =  comprar($_POST['id'],$_POST['service']);
    if($volta){
        wp_redirect(get_admin_url(get_current_blog_id(),"/admin.php?page=melhor-envio-relato&su=1&id=").$_GET['id']);
    }else{
        echo '<div class="notice notice-error is-dismissible"><h1>Atenção</h1> Não foi possível efetuar seu pedido, tente novamente mais tarde. </div>';
    }
}


if(isset($_GET['id'])):
    $cotacao = getData($_GET['id']);
    if(!is_array(getData($_GET['id']))){
        echo '<div class="wrap"> </div><div class="notice  notice-error is-dismissible"><h1>Atenção</h1> Não foi possível abrir este pedido, tente novamente mais tarde. </div></div>';
    }else{
    if(!isset(getData($_GET['id'])[0]->updated_at)):?>
<div class="wrap">
    <div class="content-header">
        <h1>Produto</h1>
    </div>

    <pre>
<?php
$cot = (object) json_decode(getCotacao($_GET['id']));

//var_dump($cot);
?>
</pre>
    <div class="" style="width:24.5%;float:left;">
        <?php getImg($_GET['id']); ?>
        <h1 class="text-center"><?php getName($_GET['id']); ?></h1>
    </div>
    <h2>
        A forma de frete escolhida pelo usuário foi: <?php echo getChosen($_GET['id']); ?>

    </h2>
    <div class="postbox panel" style="width:75%;float:left;">




        <style media="screen">

            table{
                width:100%;
            }

            form{
                margin: 0px;
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
                min-width:50%;
                border-bottom-width: 1px;
                border-bottom-style: solid;
                padding: 15px 10px;
                font-size: .9em;
                text-align: center;
            }

            .postbox .button{
                display:block;
                margin: 0 auto;
            }

            .chosen{
                color:green;
                background-color: rgba(0,220,50,0.18);
            }
        </style>

        <table>
            <thead>
                <th><h5>Frete</h5></th>
                <th><h5>Disponibilidade</h5></th>
                <th><h5>Tempo de Entrega</h5></th>
                <th><h5>Preço</h5></th>
                <th><h5>Ação</h5></th>
            </thead>
            <?php foreach ($cot as $ship ): ?>
                            <?php foreach ($ship->services as $service): ?>
                                <?php if($service->status == 1 ):?>
                                <tr style="border-style: none !important;" class="<?php

                         $compare = $ship->name.' '.$service->name;
                        if($compare == getChosen($_GET['id'])){
                            echo 'chosen';
                        } ?>" >

                                    <td style="width:350px;border-style: none !important;">
                                        <?php echo $ship->name." "." ".$service->name ?>
                                    </td>
                                    <td style="width:200px;border-style: none !important;">
                                        <?php echo $service->status == 1? "Disponível":"Indisponível"; ?>
                                    </td>
                                    <td style="width:200px;border-style: none !important;">
                                        <?php if(isset($service->delivery_time)){ echo $service->delivery_time." Dias";} ?>
                                    </td>
                                    <td style="border-style: none !important;">
                                        <?php if(isset($service->price)){echo "R$ ".number_format($service->price,2,',','.');} ?>
                                    </td>
                                    <td style="border-style: none !important;">
                                        <?php if ($service->status == 1): ?>
                                            <form action="<?php echo $_SERVER['REQUEST_URI'] ;?>" method="post">

                                                <input type="hidden" name="service" value="<?php echo $service->id ;?>">
                                                <input type="hidden" name="id" value="<?php echo $_GET['id'];?>">
                                                <button type="submit" name="button" class="button button-primary">    <?php echo $ship->name." ".$service->name ?></button>
                                            </form>

                                        <?php else: ?>
                                            Indisponível
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
            <?php endforeach; ?>
        </table>




    </div>
</div>


    <?php
    else:
        wp_redirect(get_admin_url(get_current_blog_id(),"/admin.php?page=melhor-envio-relato&id=").$_GET['id']);
    endif;
    }
else:
    wp_redirect(get_site_url());

endif;
