<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once "ebClientAmocrm.php";
require_once "test.php";

 $config = [
     'secret_key' => "kPej2b4g49u6R78mcJfmRitBeyLeC92JOe3z6OCtVmKuRCBb56F4uf5Thigy2aPg",
     'intagration_id' => "7109c243-a0ba-481f-9589-67e73334433d",
     'client_domen' => "",
     'redirect_uri' => "",
     'auth_token' => "def50200c2a702d0866907455b8abaae5e654808cfcba52532482949fde0e87f651686d116726543f10d4b3bacc1965279c279a9591f15ab4716682c6868b9d7c4667eb77e8bd48e6452fef20db4cd136ed39f6aaa0d381cd564b6f51c1d9433fe425ce798dbf823ca3641d6ee70d03080db80f13c2fddeb997b6c62a85a111483831653efbd35a877262ae25eb7cdf767826863e57d6a00d1996cf4c3279d1063f6820a1add85b4ec4582ce9a78e9157e7315aa0f5e61e225f8f85d54a93d9fa5cc7770978194dcad2bd38db53190d8a38b2fb64c806af14aa86ff73947fecf2a01f087519e2121382c1aa47f69085d8ce8ea6c11a3d2531003922c84e75ce18aa9201bfa65d03336a3effbafa7ee988d9e13216d4e99e6a32227977bf4d4f34ef349b017071b1e055cf15700f61349d6e9c7ff3ed35f3220a9358f6ed0df07df8c646575e510c8f40f050ad3639cd2fdc53ea079300cc39a6827f05ddb1c7e264d36a570911b21d85e44825a5cc43fc0378f715c6cec511440b7688ad5abcbd65fed0941c976b1b47ba38976934ba4ed4533c4ce976ef145b3ac8370256c4bd8fbb1a3251fa61277e0b23f191c71cee5c96ff293f8afd8d9dd861dd413ef4fa9ac244f62f5a34db09e74b0821afe92e263b94067",
];

// if(!isset($_POST['leads'])) die();
$hook = $_POST['leads'];
$log = "Точная дата: " . date("Y-m-d H:i:s") . "\n";
$log .= "Запрос " . serialize($_POST) . "\n";
// file_put_contents("log.txt", $log , FILE_APPEND);
// $log = "\n\n";

file_put_contents("hook.txt", json_encode($hook));

$act_lead = "";
if($hook['update']) {$act_lead = "update"; $hook = $hook['update'][0];}
else if($hook['add']) {$act_lead = "add"; $hook = $hook['add'][0];}
else if($hook['delete']) {$act_lead = "delete"; $hook = $hook['delete'][0];}
echo "<pre>";
$log .= "Акт: " . $act_lead . "\n";
if($act_lead == "add" || $act_lead == "update"){

    $amo = new EbClientAmocrm($config['secret_key'], $config['intagration_id'], $config['client_domen'], $config['redirect_uri'], $config['auth_token']);
    $lead = $amo -> get_leads("id=" . $hook['id'])[1]['_embedded']['items'][0];
    $statuses = $amo -> api_pipelines()[1]['_embedded']['pipelines'][$lead['pipeline_id']]['statuses'];
    $log .= "Подключение к амо(результат): " . serialize($amo) . "\n";
    $log .= "Получение сделки с id - {$hook['id']} (результат): " . serialize($lead) . "\n";
    $tags_text = "";
    $custom_fields = [];

    foreach($lead["tags"] as $key => $value){
        if($key!=0) $tags_text .= ", ";
        $tags_text .= $value['name'];
    }
    $tags_lead = $tags_text; // теги сделки
    $status_lead = $statuses[$lead['status_id']]['name']; // статус сделки
 
     //if($tags_lead!=='Лид С' || $tags_lead!=='' ){
     if($status_lead !== 'Регистрация на бесплатный вебинар'){

    foreach($lead['custom_fields'] as $key => $value){
        $custom_fields[$value['id']] = $value['values'][0]['value'];
    }
    // если каких то полей нет
    $manager = "неизвестно";
    $manager_id = $lead['responsible_user_id'];

    if ($manager_id == '2595445') {$manager = 'Дима';}
    if ($manager_id == '2850649') {$manager = 'Стас';}
    if ($manager_id == '3259117') {$manager = 'Рита';}
    if ($manager_id == '3318277') {$manager = 'Гоша';}
    if ($manager_id == '3429505') {$manager = 'Наташа';}
    if ($manager_id == '3766356') {$manager = 'Саша';}
    if ($manager_id == '3830190') {$manager = 'Люда';}
    if ($manager_id == '5920623') {$manager = 'Таня';}
    if ($manager_id == '6197568') {$manager = 'Артур';}
    if ($manager_id == '6197631') {$manager = 'Ксюша';}
    if ($manager_id == '2876512') {$manager = 'Марк';}

    $product = $custom_fields['688784'];
    $tarif = $custom_fields['697214'];
    $corse_date = $custom_fields['688782'];
    $paid = $custom_fields['663327'];
    $start_date = $custom_fields['695358'];
    $paydate = $custom_fields['695680'];
    $prepay = $custom_fields['695682'];
    $prepaydate = $custom_fields['695684'];
    $paytype = $custom_fields['696280'];
    $autopay = $custom_fields['695478'];
    $utm_source = $custom_fields['655613'];
    $utm_medium = $custom_fields['655611'];
    $utm_campaign = $custom_fields['655609'];
    $bonus = $custom_fields['693976'];
    $city = $custom_fields['694202'];
    $country = $custom_fields['694262'];  

    $id_lead = $lead['id']; // id сделки
    $name_lead = $lead['name']; // имя сделки
    $sale_lead = $lead['sale']; // бюджет
    $created_date_lead = date("d.m.o", $lead['created_at']); // дата создания
    if($lead['closed_at'] != 0) $closed_date_lead = date("d.m.o", $lead['closed_at']); //дата закрытия сделки(если указана, т.е. если сделка закрыта)
    
    $data = [$id_lead, $name_lead, $manager, $status_lead, $sale_lead, $created_date_lead, $tags_lead, $closed_date_lead, $product, $corse_date, $paid, $start_date, $paysum, $paydate, $prepay, $prepaydate, $paytype, $autopay, $tarif, $utm_source, $utm_medium, $utm_campaign, $bonus, $city, $country];

    if(add_lead($data)){
         $lead_config['update'] = array([
         'id' => $id_lead,
         'updated_at' => strtotime("now"),
         'custom_fields' => array(
                 array(
                     'id' => 697274, 
                     'values' => array([
                         'value' => '1'
                     ]),
                 )
             )
         ]);

         if($status_lead == 'Успешно реализовано' || $status_lead == 'предоплата') {
         $lead_config['update'] = array([
             'id' => $id_lead,
             'updated_at' => strtotime("now"),
             'custom_fields' => array(
                     array(
                         'id' => 697020, 
                         'values' => array([
                            'value' => '1'
                         ]),
                     )
                 )
            ]);
         }
        $amo->update_lead($lead_config);
    }      
 }
}else if($act_lead == "delete"){  // если пришел хук на удаление сделки
    $id = $hook['id']; // id удаленной сделки
    $log .= "Удаление сделки с id - {$hook['id']} \n";
    delete_lead($id); 
}

$log.="\n\n";
file_put_contents("log.txt", $log, FILE_APPEND);

//697018
