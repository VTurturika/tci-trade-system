<?php

use \Firebase\JWT\JWT;

class AuthModel extends Model {

    public function login($params) {

        if($params == null) $params = array();
        Logger::logWithMsg("Request body", $params);

        if(array_key_exists("username", $params) &&
           array_key_exists("password", $params)
        ) {

            if($params["username"] == "boss" && $this->verifyPassword($params["password"])) {

                return $this->generateTokens();
            }
            else return array("msg" => "wrong username or password");
        }
        else return array("msg" => "username and password required");
    }

    public function changePassword($params) {

        if($params == null) $params = array();
        Logger::logWithMsg("Request body", $params);

        if(array_key_exists("username", $params) &&
           array_key_exists("oldPassword", $params) &&
           array_key_exists("newPassword", $params)
        ) {

            if($params["username"] == "boss" && $this->verifyPassword($params["oldPassword"])) {

                $hash = password_hash($params["newPassword"], PASSWORD_BCRYPT);
                $this->db->table("Data")->update(array(
                    "password" => $hash
                ));

                return $this->generateTokens();
            }
            else return array("msg" => "wrong username or password");
        }
        else return array("msg" => "username, oldPassword, newPassword required");
    }

    public function refreshToken($params) {

        if($params == null) $params = array();
        Logger::logWithMsg("Request body", $params);

        if(array_key_exists("refreshToken", $params)) {

            $data = $this->db->table("Data")->first();

            try{
                JWT::decode($params["refreshToken"], "SECRET" . $data->token, array("HS256"));
            }catch (Exception $e) {
                Logger::log($e->getMessage());
                return array("msg" => "wrong refreshToken");
            }
            Logger::log("return");
            return $this->generateTokens();
        }
        else return array("msg" => "refreshToken required");
    }

    private function verifyPassword($password) {

        $hash = $this->db->table("Data")->select("*")->first()->password;
        return password_verify($password, $hash);
    }

    private function generateTokens() {

        $iat = time();
        $exp = time() + 60*60;

        $payload = array(
            "user" => "boss",
            "iat" => $iat,
            "exp" => $exp
        );

        $token = JWT::encode($payload, "SECRET");
        $refreshToken = JWT::encode(array(), "SECRET" . $token);

        $this->db->table("Data")->update(array(
            "token" => $token,
            "refresh_token" => $refreshToken
        ));

        return array(
            "token" => $token,
            "refresh_token" => $refreshToken,
            "createdAt" => date("Y-m-d h:i:s", $iat),
            "expireAt" => date("Y-m-d h:i:s", $exp)
        );
    }

}