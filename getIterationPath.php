<?php

/*
use App\AzureBoards\Services\WIQLService;
use App\AzureBoards\Services\WorkItemHistoryService;
use App\AzureBoards\Services\WorkItemService;
use App\Utils\EnvLoader;
use App\Utils\FileReader;
*/

use App\AzureBoards\AzureBoardsClient;
use App\AzureBoards\Services\IterationService;

require_once 'vendor/autoload.php';


$is = new IterationService(new AzureBoardsClient());
$r = $is->execute([]);
echo '<pre>';
var_dump($r);
exit;


/*
// Carregar variáveis de ambiente
$envLoader = new EnvLoader(__DIR__ . '/.env', new FileReader);
$envLoader->load();

// Configurações básicas
$organization = EnvLoader::get('AZURE_DEVOPS_ORGANIZATION');
$project = EnvLoader::get('AZURE_DEVOPS_PROJECT');
$personalAccessToken = EnvLoader::get('AZURE_DEVOPS_PAT');
$team = "Squad%20TMS";


// Endpoint para buscar sprints da equipe
$url = "https://dev.azure.com/$organization/$project/$team/_apis/work/teamsettings/iterations?api-version=7.0";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Basic ' . base64_encode(":" . $personalAccessToken)
]);

$response = curl_exec($ch);



if (curl_errno($ch)) {
    echo "Erro: " . curl_error($ch);
} else {
    $data = json_decode($response, true);

    echo '<pre>';
    print_r($data);
    exit;

    foreach ($data['value'] as $sprint) {
        echo "Sprint Name: " . $sprint['name'] . PHP_EOL;
        echo "Path: " . $sprint['path'] . PHP_EOL;
        echo "Start Date: " . $sprint['attributes']['startDate'] . PHP_EOL;
        echo "Finish Date: " . $sprint['attributes']['finishDate'] . PHP_EOL;
        echo PHP_EOL;
        echo '<br /><br />';
    }
}

curl_close($ch);
*/