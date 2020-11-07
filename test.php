<?php 
require_once 'vendor/autoload.php';

$googleAccountKeyFilePath = __DIR__ . '/secret.json';
putenv( 'GOOGLE_APPLICATION_CREDENTIALS=' . $googleAccountKeyFilePath );

$client = new Google_Client();
$client->useApplicationDefaultCredentials();
$client->addScope( 'https://www.googleapis.com/auth/spreadsheets' );
 
$service = new Google_Service_Sheets( $client );

// ID таблицы
$spreadsheetId = '1Zxat1V6QtFKuunnXZuYLBghKu60x-h7hfgCqfRYE1zc';

$response = $service->spreadsheets->get($spreadsheetId);

foreach ($response->getSheets() as $sheet) {
    $name = $sheet->getProperties()->title; // Название листа
    $sheetId = $sheet->getProperties()->sheetId; // ID листа
}


function add_lead($data){
    global $name, $service, $spreadsheetId;
    $dat = [];

    foreach($data as $key =>$values){
        $dat[$key] =  isset($values) ? $values : "-";
    }
    $range = $name.'!A1:B100000';
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);

    $k = ($count = count($response['values'])) + 1; //выбираем пустую строку для добавления сделки

    foreach($response['values'] as $num => $i){ //проверяем не существует ли сделки с таким id
        if($i[0] == $dat[0]){
            $k=$num+1;
            break;
        }
    }

    $values = [
        $dat
    ];
    $body = new Google_Service_Sheets_ValueRange( [ 'values' => $values ] );
    $options = array( 'valueInputOption' => 'USER_ENTERED' );
    $result = $service->spreadsheets_values->update( $spreadsheetId, $name.'!A'.$k, $body, $options );
    return $result -> getUpdatedRows() == 1;
}

function delete_lead($id){
    global $name, $service, $spreadsheetId;

    $range = $name.'!A1:B100000';
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);

    foreach($response['values'] as $num => $i){ //проверяем не существует ли сделки с таким id
        if($i[0] == $id){
            $deleteindex=$num+1;
            break;
        }
    }

    $requests = [
        new Google_Service_Sheets_Request( [
            'deleteRange' => [
                'range'          => [
                    'sheetId' => $sheetId,
                    'startRowIndex' => $deleteindex-1,
                    'endRowIndex' => $deleteindex,
                ],
                'shiftDimension' => 'ROWS'
            ]
        ] )
    ];
    
    $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest( [
        'requests' => $requests
    ] );
    
    $service->spreadsheets->batchUpdate( $spreadsheetId, $batchUpdateRequest );
}

// $data = ["223875701010", null, "123Иосиф", "ку132пленно", "1243000", "20-24230-2000", "тег1, тег2", "10-10-1000", "пиво", "14-14-1414", "132", "13-13-13"];
// print_r(add_lead($data));


// delete_lead(323);

