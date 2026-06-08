<?php
class JwtHandler {
    private $secret_key = 'chave_super_secreta_aqui'; // 🔐 Substitua por uma chave segura

    // Função para gerar (encode) o token
    public function encode($payload, $expTime = 3600) {
        // Validação manual opcional para garantir que é um array
        if (!is_array($payload)) {
            throw new InvalidArgumentException('O payload precisa ser um array.');
        }

        $header = json_encode(array('alg' => 'HS256', 'typ' => 'JWT'));

        // Adiciona tempo de expiração ao payload
        $payload['exp'] = time() + (int)$expTime;

        // Codifica base64Url
        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode(json_encode($payload));

        // Cria assinatura com HMAC-SHA256
        $signature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, $this->secret_key, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);

        // Retorna token completo
        return $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature;
    }

    // Função para decodificar (decode) e validar o token
    public function decode($jwt) {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            throw new Exception('Token inválido');
        }

        // PHP 5.6 não aceita a sintaxe curta [$header, $payload] = $parts
        list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $parts;

        // Verifica assinatura
        $signature = $this->base64UrlEncode(
            hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, $this->secret_key, true)
        );

        if (!hash_equals($signature, $base64UrlSignature)) {
            throw new Exception('Assinatura inválida');
        }

        $payload = json_decode($this->base64UrlDecode($base64UrlPayload), true);

        // Verifica expiração
        if (isset($payload['exp']) && time() > $payload['exp']) {
            throw new Exception('Token expirado');
        }

        return $payload;
    }

    // Funções auxiliares de encode/decode Base64Url
    private function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode($data) {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
?>
