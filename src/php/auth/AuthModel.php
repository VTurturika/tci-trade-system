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

                return array(
                    "token" => $this->generateJwt()
                );
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

                return array(
                    "token" => $this->generateJwt()
                );
            }
            else return array("msg" => "wrong username or password");
        }
        else return array("msg" => "username, oldPassword, newPassword required");
    }

    private function verifyPassword($password) {

        $hash = $this->db->table("Data")->select("*")->first()->password;
        return password_verify($password, $hash);
    }

    private function generateJwt() {

        $payload = array(
            "user" => "boss"
        );
        return JWT::encode($payload, "SECRET");
    }

}