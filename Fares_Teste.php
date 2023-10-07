<?php

function validarTicket($ticket) 
{
    if (!empty($ticket) && !is_numeric($ticket)) 
    {
        echo "Por favor, insira apenas números para o ticket.";
        exit;
    }
}

function consultarAPI($ticket = '') {
    $url = 'http://10.29.25.30/json/outage.php';
    $timeout = 5;

    $options = 
    [
        'http' => 
        [
            'method' => 'GET',
            'timeout' => $timeout,
        ],
    ];

    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        if (http_response_code() === 500) 
        {
            echo "A API está indisponível.";
        } 
        elseif (http_response_code() === 404) 
        {
            echo "A API pode ter mudado de endereço.";
        } 
        else 
        {
            echo "Erro na requisição da API.";
        }
    } 
    else 
    {
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
                    echo "<button onclick=\"aceitarTicket('{$item['ticket']}')\">Aceitar</button>";
                } 
                else 
                {
                    echo "<button onclick=\"alert('Ticket não aceito devido à sua natureza')\">Não Aceitar</button>";
                }
                echo "</td>";
                echo "</tr>";
            }
        }
        echo "</table>";
    }
}

echo <<<HTML

<script>
function aceitarTicket(ticket) 
{
    alert(ticket + " Aceito com sucesso");
}
</script>

HTML;

$ticket = isset($_GET['ticket']) ? $_GET['ticket'] : '';

validarTicket($ticket);

consultarAPI($ticket);
?>
