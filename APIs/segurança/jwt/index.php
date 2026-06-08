<?php
require_once 'JwtHandler.php';

$jwt = new JwtHandler();

// Criando um token com dados do usuário
$token = $jwt->encode([
    'user_id' => 123,
    'name' => 'Paulo Henrique',
    'role' => 'admin'
]);

echo "Token gerado: " . $token . "\n\n";

// Decodificando o token
try {
    $dados = $jwt->decode($token);
    echo "Token válido!\n";
    print_r($dados);
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}

?>