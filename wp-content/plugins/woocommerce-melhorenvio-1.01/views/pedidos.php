<?php
/**
 * Created by PhpStorm.
 * User: pikachu
 * Date: 05/06/17
 * Time: 09:12
 */

include_once ABSPATH.'/wp-content/plugins/woocommerce/includes/wc-template-functions.php';
if(isset($_GET['paged'])){
    $page = $_GET['paged'];
}else{
    $page =1;
}

$empty_array = array('limit' => 100000);
$valor  = count(wc_get_orders($empty_array));
$offset = (($page-1)*10);
$array = array(
    'limit' => 10,
    'offset'      => $offset,
    'page'        => $page
              );
$orders = wc_get_orders($array);
//woocommerce_order_details_table();

?>
<style>

    table{
        width: 100%;
    }
    tr{
        border-bottom:solid;
    }

    th{
        padding-top: 10px;
        padding-bottom: 10px;
        border: solid 1px;
        border-color:  rgba(20,50,70,0.1);
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
        padding: 20px;
        font-size: .9em;
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

    tr:nth-child(even){
        background-color: rgba(100,100,100,0.04);
    }

    tr:hover{
        background-color: rgba(40,40,40,0.08);;
    }

    .btn{
        color: #3A3A3A;
        text-decoration: none;
        font-size: small;
        background-color: rgba(10,30,60,0.2);
        padding: 7px;
        border-radius: 3px;
        box-shadow: 0px 4px 2px rgba(0,0,20,0.18);
    }

    .btn:hover{
        color: #3A3A3A;
        text-decoration: none;
        font-size: small;
        background-color: rgba(10,30,60,0.23);
        padding: 7px;
        border-radius: 3px;
        box-shadow: 0px 4px 2px rgba(0,0,20,0.16);
    }

    .btn2{
        background-color: #46A450;
        text-decoration: none;
        padding:8px 13px;
        border-radius: 2px;
        color: whitesmoke;
        box-shadow: 0px 1px 1px grey;
        margin:10px;
        position: relative;
    }

    .btn2:hover{
        background-color: #46B450;
        text-decoration: none;
        padding:8px 13px;
        border-radius: 2px;
        color: whitesmoke;
        box-shadow: 0px 1px 2px grey;

    }

</style>
<div class="wrap">

    <h1>Pedidos efetuados</h1>
    <br>
    <span class="lead">Aqui estão os pedidos feitos pelos seus clientes na plataforma WooCommerce. <br> Escolha um pedido e clique em Visualizar Cotação, para descobrir qual a forma mais adequada de envio.</span>
    <?php if(get_option('me_url') != null): ?>
    <a class="btn2" href="<?php echo get_option('me_url');?>" target="_blank">Carrinho de compras</a> <br><br>
    <?php endif; ?>
    <div class="panel-wrap postbox" >
        <div class="panel">
            <table>
                <thead>
                <tr>
                <th>ID </th>
                <th>Cliente</th>
                <th>Valor Total</th>
                <th>Ultima atualização</th>
                <th>Ação</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $order):?>
                  <?php
                  if($order instanceof WC_Order_Refund): ?>

                    <?php else:?>
                <tr>
                    <td>
                        <?php if(null != $order->get_order_number()){echo $order->get_order_number() ;}?>
                    </td>
                    <td>
                        <?php if (isset($order->get_user()->display_name)){echo $order->get_user()->display_name;}?>
                    </td>

                    <td>
                        <?=$order->get_total();?>
                    </td>

                    <td>
                        <?=date('d/m/Y',time($order->get_date_modified()));?>
                    </td>
                    <td>
                        <a href="<?php echo get_admin_url(get_current_blog_id(),"/admin.php?page=melhor-envio-request&id=").$order->get_order_number(); ?>" class="btn">Ver Cotação</a>
                    </td>
                </tr>
              <?php endif; ?>
                <?php endforeach;?>
                </tbody>
            </table>


        </div>
        <br>
        <br>
        <?php
        $args = array(
            'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
            'format'=>'?paged=%#%',
            'current' => $page,
            'total'=> ceil($valor/10));
        echo paginate_links($args);  ?>
    </div>
</div>
