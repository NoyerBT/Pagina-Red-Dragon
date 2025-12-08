<?php

class TokenGenerator {
    private $tokens_file = 'tokens_database.json';
    private $tokens = [];

    public function __construct() {
        $this->tokens = $this->load_tokens();
    }

    private function load_tokens() {
        if (file_exists($this->tokens_file)) {
            $json_data = file_get_contents($this->tokens_file);
            return json_decode($json_data, true) ?: [];
        }
        return [];
    }

    private function save_tokens() {
        try {
            $json_data = json_encode($this->tokens, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return file_put_contents($this->tokens_file, $json_data) !== false;
        } catch (Exception $e) {
            error_log("Error al guardar tokens: " . $e->getMessage());
            return false;
        }
    }

    public function generate_token($player_name, $tournament_name = "L4D2 Tournament") {
        // Generar token único y seguro
        $token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        
        // Crear hash del token para verificación
        $token_hash = hash('sha256', $token);
        
        // Fecha de expiración (2 días fijos) -> Reemplazar expires_days=30 del python por 2 días fijos
        $created_date = new DateTime();
        $expires_date = (clone $created_date)->add(new DateInterval('P2D'));
        
        // Crear entrada del token
        $token_data = [
            'player_name' => $player_name,
            'tournament_name' => $tournament_name,
            'token' => $token,
            'token_hash' => $token_hash,
            'created_date' => $created_date->format(DateTime::ISO8601),
            'expires_date' => $expires_date->format(DateTime::ISO8601),
            'is_active' => true,
            'used_count' => 0,
            'last_used' => null
        ];
        
        // Guardar token
        $this->tokens[$token_hash] = $token_data;
        
        if ($this->save_tokens()) {
            return $token_data;
        } else {
            return null;
        }
    }

    public function validate_token($token) {
        $token_hash = hash('sha256', $token);
        
        if (!isset($this->tokens[$token_hash])) {
            return [false, "Token no encontrado"];
        }
        
        $token_data = &$this->tokens[$token_hash]; // Referencia para poder modificar
        
        // Verificar si está activo
        if (isset($token_data['is_active']) && !$token_data['is_active']) {
            return [false, "Token desactivado"];
        }
        
        // Verificar expiración
        $expires_date = new DateTime($token_data['expires_date']);
        $now = new DateTime();
        if ($now > $expires_date) {
            return [false, "Token expirado"];
        }
        
        // Actualizar uso
        if (!isset($token_data['used_count'])) $token_data['used_count'] = 0;
        $token_data['used_count']++;
        $token_data['last_used'] = $now->format(DateTime::ISO8601);
        
        $this->save_tokens();
        
        return [true, $token_data];
    }

    public function deactivate_token($token_hash) {
        if (isset($this->tokens[$token_hash])) {
            $this->tokens[$token_hash]['is_active'] = false;
            return $this->save_tokens();
        }
        return false;
    }

    public function get_token_stats() {
        $total_tokens = count($this->tokens);
        $active_tokens = 0;
        $expired_tokens = 0;
        $now = new DateTime();
        
        foreach ($this->tokens as $token) {
            // Contar activos (is_active true Y no expirado)
            $isActive = isset($token['is_active']) ? $token['is_active'] : true;
            $expiresDate = new DateTime($token['expires_date']);
            
            if ($now > $expiresDate) {
                $expired_tokens++;
            } elseif ($isActive) {
                $active_tokens++;
            }
        }
        
        return [
            'total_tokens' => $total_tokens,
            'active_tokens' => $active_tokens,
            'expired_tokens' => $expired_tokens
        ];
    }

    public function list_tokens() {
        return $this->tokens;
    }
}
?>
