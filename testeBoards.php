<?php

use App\AzureBoards\AzureBoardsClient;
use App\AzureBoards\Services\WIQLService;
use App\AzureBoards\Services\WorkItemHistoryService;
use App\AzureBoards\Services\WorkItemService;

require_once 'vendor/autoload.php';

// Configurações básicas
$project = $_ENV['AZURE_DEVOPS_PROJECT'];
$areaPath = $_ENV['AZURE_DEVOPS_AREA_PATH'];
$iterationPath = $_ENV['AZURE_DEVOPS_ITARATION_PATH'];
$state = "Pronto para Teste";

// Inicializa o cliente principal do Azure Boards
$azureClient = new AzureBoardsClient();

// Inicializa os serviços
$wiqlService = new WIQLService($azureClient);
$workItemService = new WorkItemService($azureClient);
$workItemHistoryService = new WorkItemHistoryService($azureClient);


// Query WIQL para buscar os Work Items desejados
$query = [
    "query" => "
        SELECT
            [System.Id],
            [System.Title],
            [System.WorkItemType],
            [System.State],
            [System.ChangedDate]
        FROM WorkItems
        WHERE
            [System.TeamProject] = '$project'
            AND [System.AreaPath] = '$areaPath'
            AND [System.IterationPath] = '$iterationPath'
            AND ([System.WorkItemType] = 'Product Backlog Item' OR [System.WorkItemType] = 'Bug')
    "
];

// Executa a consulta WIQL
$response = $wiqlService->execute($query);

// Obtém os IDs dos Work Items retornados
$workItemIds = array_column($response['workItems'], 'id');

echo 'Total de tarefas = ' . count($workItemIds) . "<br />";

// Verifica se há Work Items encontrados
if (empty($workItemIds)) {
    die("Nenhum Work Item encontrado para os critérios especificados.");
}

// Obtém os detalhes dos Work Items
$workItems = $workItemService->execute(['ids' => $workItemIds]);

// Processa os dados para calcular o tempo médio no estado
$timeInState = [];
foreach ($workItems['value'] as $workItem) {
    $history = $workItemHistoryService->execute(['id' => $workItem['id']]);

    $currentState = null;
    $stateEntryTime = null;

    foreach ($history['value'] as $update) {
        if (isset($update['fields']['System.State'])) {
            $newState = $update['fields']['System.State']['newValue'];
            $changedDate = $update['fields']['System.ChangedDate']['newValue'];

            if ($newState === $state) { // Mude para o estado desejado
                $stateEntryTime = strtotime($changedDate);
            } elseif ($stateEntryTime && $newState !== $state) {
                $stateExitTime = strtotime($changedDate);
                $timeInState[] = $stateExitTime - $stateEntryTime;
                $stateEntryTime = null; // Reset
            }
        }
    }
}

// Calcula o tempo médio no estado específico
if (!empty($timeInState)) {
    $averageTimeInSeconds = array_sum($timeInState) / count($timeInState);
    $averageTimeInHours = $averageTimeInSeconds / 3600;

    echo "Tempo médio no estado especificado: " . round($averageTimeInHours, 2) . " horas.\n";
} else {
    echo "Nenhum Work Item ficou no estado especificado.\n";
}
