<?php
include_once ABSPATH.WPINC.'/option.php';
include_once ABSPATH.WPINC.'/class-requests.php';
include_once ABSPATH . 'wp-content/plugins/woocommerce-extra-checkout-fields-for-brazil/includes/class-extra-checkout-fields-for-brazil-api.php';

/**
 * @return array $config
 */
function getTransportadoras()
{
    $params = array('headers'=>['content-type'=>'application/json;charset=UTF-8']);
    $curl = new WP_Http();
    $response = $curl->get('https://api.melhorenvio.com.br/v1/shipping/services',$params);
    if(is_array($response)){
        $data = $response['body'];
    }
    else{
        echo'<div class="notice notice-error is-dismissible">
               <h1>Erro de conexão</h1>
                    Houve uma falha para se conectar a API do Melhor Envio, tente novamente em alguns instantes.
            </div>';
        return false;
    }
    return json_decode($data);
}

/**
 * @return boolean
 * @param array $array
 */
function saveTransportadoras(array $array)
{
    update_option('me_transportadoras', json_encode($array));
    return true;
}

/**
 * @return array $transportadoras
 */
function getTransportadoraSelecionada()
{
    return json_decode(get_option('me_transportadoras'));
}

function getRemetente()
{
    return json_decode(get_option('me_remetente'));
}

/**
 * @param array $remetente
 */
function saveRemetente(array $remetente)
{
    update_option('me_remetente', json_encode($remetente));

    return true;
}

/**
 * @param array $loja
 * @return boolean
 */
function saveLoja(array $loja)
{
   update_option('me_name',$loja['name']);
   update_option('me_lastname',$loja['lastname']);
   update_option('me_email',$loja['email']);
   update_option('me_phone',$loja['phone']);
   update_option('me_cpf',$loja['cpf']);

   if (isset($loja['me_cnpj'])){
       update_option('me_cnpj',$loja['cnpj']);
   }

    if (isset($loja['me_ie'])){
        update_option('me_ie',$loja['ie']);
    }

    //TODO: Adicionar as chaves de API da Melhor Envio e a requisição utilizando o Guzzle
    $params = array('headers'=>['content-type'=>'application/json;charset=UTF-8'],'body'=>'{
                      "name": "'.$loja['name'].'", 
                      "url": "'.get_site_url().'"
                    }');
    $curl = new WP_Http();
    $response = $curl->post('https://api.melhorenvio.com.br/v1/authorize',$params);

    if(is_array($response)){
        $data = json_decode($response['body']);
        update_option("me_url",null);
    }else{
        return false;
    }
    if (update_option('me_api_key',$data->api_key) && update_option('me_token',$data->secret_token))
    {return true;}else{return false;}
}


/// Relacionadas

/**
 * @param $id
 * @return object
 */
function getCotacao($id)
{
    $order = wc_get_order($id);
    $items = $order->get_items();
    $curl = new WP_Http();
    $volume =0;
    $weight =0;
    $total  =0;
    $package = new stdClass();
    // Monta o pacote
    foreach ($items as $item){
      $width = wc_get_product($item->get_product_id())->get_width();
      $height = wc_get_product($item->get_product_id())->get_height();
      $length = wc_get_product($item->get_product_id())->get_length();
      $weight += wc_get_product($item->get_product_id())->get_weight()  * $item->get_quantity();

      $valor = wc_get_product($item->get_product_id())->get_price() * $item->get_quantity();
      $volume  = $volume +  (int) ($width * $length * $height) * $item->get_quantity();

        $total += $valor ;
    }

    $side   =  ceil(pow($volume,1/3));

    $package->width  = $side;
    $package->height = $side;
    $package->length = $side;
    $package->weight = $weight;

    //Monta os optionals
    $config = getTransportadoraSelecionada();
    $options = new stdClass();
    if($config->value_declaration == 1){
        $options->declared_value = $total;
    }else{
        $options->declared_value = 0;
    }

    $options->own_hand = $config->own_hand;
    $options->receipt = $config->receipt;

    //Monta o from
    $from = new stdClass();
    $remetente = getRemetente();

    $from->postal_code = $remetente->cep;
    $from->address =  $remetente->address;
    $from->number = $remetente->number;

    //Monta o to
    $to = new stdClass();
    $to->postal_code = $order->get_shipping_postcode();
    $to->number =  $order->get_meta( '_shipping_number');
    $to->district =  $order->get_meta( '_shipping_neighborhood'); //get Shipping neighborhood based on Claudio Sanches App


    $request = new stdClass();

    $request->to = $to;
    $request->from = $from;
    $request->package = $package;
    $request->options = $options;

    //var_dump($request);
    $args = array('timeout'=>20,'headers'=>['content-type'=>'application/json;charset=UTF-8','authorization' => base64_encode(get_option('me_api_key').':'.get_option('me_token'))],
                    'body' => json_encode($request)
                    );

    $response = wp_remote_post("https://api.melhorenvio.com.br/v1/shipping/calculate",$args);
    //var_dump($response);
    if(is_array($response)){
        return $response['body'];
    }else{
        echo '<div class="notice notice-error is-dismissible">
                    <h1>ATENÇÃO!!!</h1>
                Não foi possível acessar a cotação, tente novamente mais tarde!
                </div>';
    }

}


function getCotacaoFront($pck)
{
    //$items = $pck->get_contents();
    $curl = new WP_Http();
    $volume =0;
    $weight =0;
    $total  =0;
    $package = new stdClass();


    // Monta o pacote
    foreach ($pck['contents'] as $item){
        $width = wc_get_product($item['product_id'])->get_width();
        $height = wc_get_product($item['product_id'])->get_height();
        $length = wc_get_product($item['product_id'])->get_length();
        $weight += wc_get_product($item['product_id'])->get_weight()  * $item['quantity'];

        $valor = wc_get_product($item['product_id'])->get_price() * $item['quantity'];
        $volume  = $volume +  (int) ($width * $length * $height) * $item['quantity'];

        $total += $valor ;
    }

    $side   =  ceil(pow($volume,1/3));

    $package->width  = $side;
    $package->height = $side;
    $package->length = $side;
    $package->weight = $weight;

    //Monta os optionals
    $config = getTransportadoraSelecionada();
    $options = new stdClass();
    if($config->value_declaration == 1){
        $options->declared_value = $total;
    }else{
        $options->declared_value = 0;
    }

    $options->own_hand = $config->own_hand;
    $options->receipt = $config->receipt;

    //Monta o from
    $from = new stdClass();
    $remetente = getRemetente();

    $from->postal_code = $remetente->cep;
    $from->address =  $remetente->address;
    $from->number = $remetente->number;

    //Monta o to
    $to = new stdClass();
    $to->postal_code = $pck['destination']['postcode'];
    $to->address = $pck['destination']['address'];
    $to->number = $pck['destination']['address_2'];


    $request = new stdClass();

    $request->to = $to;
    $request->from = $from;
    $request->package = $package;
    $request->options = $options;

    $args = array('timeout'=>20,'headers'=>['content-type'=>'application/json;charset=UTF-8','authorization' => base64_encode(get_option('me_api_key').':'.get_option('me_token'))],
        'body' => json_encode($request)
    );

    $response = wp_remote_post("https://api.melhorenvio.com.br/v1/shipping/calculate",$args);

    if(is_array($response)){
        return json_decode($response['body']);
    }else {
        return [];
    }
}


function getName($id)
{
    $order = wc_get_order($id);
    $items = $order->get_items();
    foreach ($items as $item){
        echo $item->get_name().' - '.$item->get_quantity()."<br>";
     }

}


function getChosen($id)
{
    $order = wc_get_order($id);
    return $order->get_shipping_method();


}

function getImg($id)
{
    $order = wc_get_order($id);
    $items = $order->get_items();
    foreach ($items as $item){
        echo wc_get_product($item->get_product_id())->get_image()."<br>";
    }
}

function getData($id){

    $request = new stdClass();
    $request->email = get_option('me_email');
    $request->filter = array($id);



    $args = array('timeout'=>20,'headers'=>['content-type'=>'application/json;charset=UTF-8','authorization' => 'Basic '.base64_encode(get_option('me_api_key').':'.get_option('me_token'))],
        'body' => json_encode($request)
    );
    $response = wp_remote_post("https://api.melhorenvio.com.br/v1/shipping",$args);

    //var_dump($response['body']);
    if(is_array($response)){
    return json_decode($response['body']);
    }
}

function comprar($id, $svc)
{

    $order = wc_get_order($id);
    $items = $order->get_items();
    $curl = new WP_Http();
    $volume =0;
    $weight =0;
    $total  =0;
    $package = new stdClass();
    // Monta o pacote
    foreach ($items as $item){
        $width = wc_get_product($item->get_product_id())->get_width();
        $height = wc_get_product($item->get_product_id())->get_height();
        $length = wc_get_product($item->get_product_id())->get_length();
        $weight += wc_get_product($item->get_product_id())->get_weight()  * $item->get_quantity();

        $valor = wc_get_product($item->get_product_id())->get_price() * $item->get_quantity();
        $volume  = $volume +  (int) ($width * $length * $height) * $item->get_quantity();

        $total += $valor ;
    }

    $side   =  ceil(pow($volume,1/3));

    $package->width  = $side;
    $package->height = $side;
    $package->length = $side;
    $package->weight = (float) $weight;

    //Monta os optionals
    $config = getTransportadoraSelecionada();
    $options = new stdClass();
    if($config->value_declaration == 1){
        $options->declared_value = $total;
    }else{
        $options->declared_value = 0;
    }

    $options->own_hand = (int) $config->own_hand;
    $options->receipt = (int) $config->receipt;

    //Monta o from
    $from = new stdClass();
    $remetente = getRemetente();

    $from->name = get_option('me_name');
    $from->phone = get_option('me_phone');
    $from->postal_code = $remetente->cep;
    $from->address =  $remetente->address;
    $from->number = $remetente->number;
    $from->district = $remetente->district;
    $from->city = $remetente->city;
    $from->uf = $remetente->uf;
    $from->complement = $remetente->complement;
    $from->cpf = get_option('me_cpf');
    //$from->cnpj = get_option('me_cnpj');
    //$from->ie = get_option('me_ie');


    //Monta o to
    $to = new stdClass();
    $to->name = $order->get_shipping_first_name();
    $to->phone = $order->get_billing_phone();
    $to->postal_code = str_replace('-','',$order->get_shipping_postcode());
    $to->address = $order->get_shipping_address_1();
    $to->number = $order->get_meta( '_shipping_number');
    $to->district = $order->get_meta( '_shipping_neighborhood');
    $to->city = $order->get_shipping_city();
    $to->uf = $order->get_shipping_state();
    $to->complement = $order->get_shipping_address_2();
    //$to->complement = $order->get_shipping_address_map_url();



    $request = new stdClass();

    $request->email = get_option('me_email');

    $rqst = new stdClass();
    $rqst->ref = $id;
    $rqst->from = $from;
    $rqst->to = $to;
    $rqst->package = $package;
    $rqst->service = (int) $svc;
    $rqst->optionals = $options;

    $array = array($rqst);
    $request->cart = $array;

    $args = array('timeout'=>20,'headers'=>['content-type'=>'application/json;charset=UTF-8','authorization' => 'Basic '.base64_encode(get_option('me_api_key').':'.get_option('me_token'))],
        'body' => json_encode($request)
    );

    $response = wp_remote_post("https://api.melhorenvio.com.br/v1/shipping/cart",$args);
    if(is_array($response)){
        update_option("me_url",json_decode($response['body'])->url);
        wp_redirect(get_admin_url(get_current_blog_id(),"/admin.php?page=melhor-envio-relato&su=1&id=").$_GET['id']);
        return true;
    }else{
        return false;
    }
}

