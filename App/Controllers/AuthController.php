<?php

namespace App\Controllers;

class AuthController {
    
    private static $key = 'minhacarteira';
    
    public function post(){

        $connPdo = new \PDO(DBDRIVE.': host='.DBHOST.'; dbname='.DBNAME, DBUSER, DBPASS);

        $data = json_decode(file_get_contents("php://input"));

        $sql = 'SELECT u.id
                FROM usuarios u 
                WHERE u.email = :email AND u.senha = :senha';

        $stmt = $connPdo->prepare($sql);

        $stmt->bindValue(':email', $data->email);
        $stmt->bindValue(':senha', $data->senha);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            $payload = [
                'email' => $data->email,
            ];

            $header = json_encode($header);
            $payload = json_encode($payload);

            $header = self::base64UrlEncode($header);
            $payload = self::base64UrlEncode($payload);

            $sign = hash_hmac('sha256', $header . "." . $payload, self::$key, true);
            $sign = self::base64UrlEncode($sign);

            $token = $header . '.' . $payload . '.' . $sign;

            return $token;
        }
        
        throw new \Exception('Usuário(a) sem acesso');

    }

    public static function checkAuth() {
        
        $http_header = apache_request_headers();

        if (isset($http_header['Authorization']) && $http_header['Authorization'] != null) {
            
            $bearer = explode (' ', $http_header['Authorization']);

            $token = explode('.', $bearer[1]);
            $header = $token[0];
            $payload = $token[1];
            $sign = $token[2];

            $valid = hash_hmac('sha256', $header . "." . $payload, self::$key, true);
            $valid = self::base64UrlEncode($valid);

            if ($sign === $valid) {
                return true;
            }
        }

        return false;
    }
    
    private static function base64UrlEncode($data) {
        
        $b64 = base64_encode($data);
        
        if ($b64 === false) {
            return false;
        }

        $url = strtr($b64, '+/', '-_');
        
        return rtrim($url, '=');
    }
}