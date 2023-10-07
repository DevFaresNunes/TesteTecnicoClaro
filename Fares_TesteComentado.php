<?php

/*
Código criado por Fares André Camargo Nunes, com o propósito de atender a um teste de conhecimentos técnicos e não deve ser utilizado para fins de produção.
*/

// Função para validar se o parâmetro 'ticket' contém apenas números
function validarTicket($ticket) 
{
    // Verifica se o 'ticket' não está vazio e se não é número
    if (!empty($ticket) && !is_numeric($ticket)) 
    {
        // Se não for número, exibe uma mensagem de erro e encerra o script
        echo "Por favor, insira apenas números para o ticket.";
        exit;
    }
}

// Função para consultar a API
function consultarAPI($ticket = '') 
{
    $url = 'http://10.29.25.30/json/outage.php';
    $timeout = 5; // Timeout em segundos

    // Configuração da requisição HTTP
    $options = 
    [
        'http' => 
        [
            'method' => 'GET',
            'timeout' => $timeout,
        ],
    ];

    // Realiza a requisição da API
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);

    if ($response === false) 
    {
        // Tratamento de erros na requisição da API
        if (http_response_code() === 500) 
        {
            // Se o código de resposta for 500, diz "a API está indisponível"
            echo "A API está indisponível.";
        } 
        elseif (http_response_code() === 404) 
        {
            // Se o código de resposta for 404, diz "a API pode ter mudado de endereço"
            echo "A API pode ter mudado de endereço.";
        } 
        else 
        {
            // Outros erros na requisição da API
            echo "Erro na requisição da API.";
        }
    } 
    else 
    {
        // Sucesso na requisição da API (Código 200)
        $data = json_decode($response, true);
        echo "<table border='1'>";
        echo "<tr><th>Ticket</th><th>Natureza</th><th>Descrição</th><th>Aceitar</th></tr>";
        
        foreach ($data as $item) 
        {
            if (empty($ticket) || $ticket == $item['ticket']) 
            {
                echo "<tr>";
                echo "<td>{$item['ticket']}</td>";
                echo "<td>{$item['natureza']}</td>";
                echo "<td>{$item['descricao']}</td>";
                echo "<td>";
                if ($item['natureza'] === 'CORRETIVA') 
                {
                    // Botão "Aceitar" com função JS
                    echo "<button onclick=\"aceitarTicket('{$item['ticket']}')\">Aceitar</button>";
                } 
                else 
                {
                    // Botão "Não Aceitar" com mensagem de alerta JS
                    echo "<button onclick=\"alert('Ticket não aceito devido à sua natureza')\">Não Aceitar</button>";
                }
                echo "</td>";
                echo "</tr>";
            }
        }
        echo "</table>";
    }
}

// Função JS para aceitar o ticket
echo <<<HTML
<script>
function aceitarTicket(ticket) 
{
    // Exibe um alerta com a mensagem de aceitação do ticket
    alert(ticket + " Aceito com sucesso");
}
</script>
HTML;

// Verifica se o valor está disponível e então, obtém o valor do parâmetro 'ticket' da variável GET
$ticket = isset($_GET['ticket']) ? $_GET['ticket'] : '';

// Valida o valor do parâmetro 'ticket'
validarTicket($ticket);

// Consulta a API e exibe os resultados em uma tabela
consultarAPI($ticket);

?>
