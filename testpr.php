<?php

require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class AzureDevOpsService
{
    private Client $httpClient;
    private string $organization;
    private string $project;
    private string $personalAccessToken;

    public function __construct(string $organization, string $project, string $personalAccessToken)
    {
        $this->organization = $organization;
        $this->project = $project;
        $this->personalAccessToken = $personalAccessToken;
        $this->httpClient = new Client([
            'base_uri' => "https://dev.azure.com/{$this->organization}/{$this->project}/",
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode(":" . $this->personalAccessToken),
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    public function getSprintPBIs(string $team, string $sprintId): array
    {
        $encodedTeam = rawurlencode($team);

        try {
            $response = $this->httpClient->get("{$encodedTeam}/_apis/work/teamsettings/iterations/{$sprintId}/workitems?api-version=7.0");
            $data = json_decode($response->getBody(), true);

            return $data['workItemRelations'] ?? [];
        } catch (RequestException $e) {
            throw new \Exception('Erro ao buscar PBIs do sprint: ' . $e->getMessage());
        }
    }

    public function getWorkItemDetails(int $workItemId): array
    {
        try {
            // Corrigida a URL gerada
            $response = $this->httpClient->get("wit/workitems/{$workItemId}?expand=relations&api-version=7.0");
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            throw new \Exception('Erro ao buscar detalhes do Work Item: ' . $e->getMessage());
        }
    }

    public function getPullRequestHistory(string $repositoryId, int $pullRequestId): int
    {
        try {
            $response = $this->httpClient->get("git/repositories/{$repositoryId}/pullRequests/{$pullRequestId}/threads?api-version=7.0");
            $threads = json_decode($response->getBody(), true);

            $waitForAuthorCount = 0;
            foreach ($threads['value'] as $thread) {
                foreach ($thread['comments'] as $comment) {
                    if (strpos(strtolower($comment['content']), 'wait for author') !== false) {
                        $waitForAuthorCount++;
                    }
                }
            }

            return $waitForAuthorCount;
        } catch (RequestException $e) {
            throw new \Exception('Erro ao buscar histórico do Pull Request: ' . $e->getMessage());
        }
    }

    public function getTeamIterations(string $team): array
    {
        $encodedTeam = rawurlencode($team);

        try {
            $response = $this->httpClient->get("{$encodedTeam}/_apis/work/teamsettings/iterations?api-version=7.0");
            return json_decode($response->getBody(), true)['value'] ?? [];
        } catch (RequestException $e) {
            throw new \Exception('Erro ao buscar iterações da equipe: ' . $e->getMessage());
        }
    }
}

// Configurações
// Configurações
$organization = $_ENV['AZURE_DEVOPS_ORGANIZATION'];
$project = $_ENV['AZURE_DEVOPS_PROJECT'];
$personalAccessToken = $_ENV['AZURE_DEVOPS_PAT'];
$team = $_ENV['AZURE_DEVOPS_TEAM'];
$sprintId = 'c64f5207-b060-4d21-ae3b-88636b8f5455';

try {
    $azureService = new AzureDevOpsService($organization, $project, $personalAccessToken);

    // Valida se o sprint está configurado para a equipe
    $teamIterations = $azureService->getTeamIterations($team);
    $sprintFound = array_filter($teamIterations, fn($iteration) => $iteration['id'] === $sprintId);

    if (empty($sprintFound)) {
        die("O sprint não está configurado para a equipe '{$team}'.");
    }

    // Busca os PBIs do sprint
    $workItems = $azureService->getSprintPBIs($team, $sprintId);

    
    
    // Processa cada PBI e busca PRs associados
    $resultados = [];
    foreach ($workItems as $workItem) {
        $pbiId = $workItem['target']['id'];

        

        $pbiDetails = $azureService->getWorkItemDetails($pbiId);

        echo '<pre>';
        print_r($pbiDetails);
        exit;
        
        // Filtra relações para encontrar PRs
        $prLinks = array_filter($pbiDetails['relations'] ?? [], function ($relation) {
            return strpos($relation['rel'], 'ArtifactLink') !== false && strpos($relation['url'], 'pullRequestId') !== false;
        });

        foreach ($prLinks as $link) {
            $prDetails = parse_url($link['url']);
            parse_str($prDetails['query'], $params);

            $repositoryId = $params['repositoryId'] ?? null;
            $pullRequestId = $params['pullRequestId'] ?? null;

            if ($repositoryId && $pullRequestId) {
                // Conta ocorrências de "wait for author"
                $waitForAuthorCount = $azureService->getPullRequestHistory($repositoryId, $pullRequestId);

                if ($waitForAuthorCount > 0) {
                    $resultados[] = [
                        'pbi' => $pbiId,
                        'pr' => $pullRequestId,
                        'wait_for_author_count' => $waitForAuthorCount
                    ];
                }
            }
        }
    }

    // Exibe o resultado final
    echo '<pre>';
    print_r($resultados);
    echo '</pre>';
} catch (\Exception $e) {
    echo 'Erro: ' . $e->getMessage();
}
