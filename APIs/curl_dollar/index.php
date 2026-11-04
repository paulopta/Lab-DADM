<?php

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/CotacaoDolarDia(dataCotacao=@dataCotacao)?@dataCotacao='04-01-2026'&\$top=100&\$format=json",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  //CURLOPT_CUSTOMREQUEST => 'POST',
  //CURLOPT_POSTFIELDS => json_encode(['call' => 'PesquisarLancamentos', 'app_key' => 'XXXXXXXXX','app_secret' => 'XXXXXXXXXX']),
  CURLOPT_HTTPHEADER => [
    'Content-type: application/json'
  ],
]);

$response = curl_exec($curl);

curl_close($curl);

// Transforma o JSON em um objeto PHP
$dados = json_decode($response);

// A API retorna um array dentro de 'value'
if (isset($dados->value) && !empty($dados->value)) {
    $cotacaoCompra = $dados->value[0]->cotacaoCompra;
    $cotacaoVenda = $dados->value[0]->cotacaoVenda;
    echo "O valor da cotação de compra é: R$ " . number_format($cotacaoCompra, 4, ',', '.');
	echo "O valor da cotação de venda é: R$ " . number_format($cotacaoVenda, 4, ',', '.');
} else {
    echo "";
}
?>