<?php
namespace usualtool\KouZi;
class KouZi{
    public function __construct($workid){
        $json=file_get_contents(dirname(__FILE__)."/config.json");
        $data=json_decode($json,true);
        $this->client_id=$data["client_id"];
        $this->client_secret=$data["client_secret"];
        $this->redirect_uri=$data["redirect_uri"];
        $this->flow_id=$workid;
    }
    public function GetLogin(){
        $authurl = "https://api.coze.cn/api/permission/oauth2/authorize?" . http_build_query([
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_uri,
            'response_type' => 'code',
            'state' => bin2hex(random_bytes(5))
        ]);
        return $authurl;
    }
    public function GetRefToken(){
        $file = file_get_contents(UTF_ROOT."/log/kouzi.token.json",true);
        $result = json_decode($file,true);
        if(time() > $result['expires']):
            return $this->GetLogin();
        else:
            $ref_token=$result['ref_token'];
            return $ref_token;
        endif;
    }
    public function GetNewRefToken($authcode){
        $url = 'https://api.coze.cn/api/permission/oauth2/token';
        $postData = [
            "grant_type" => "authorization_code",
            "code" => $authcode,
            "redirect_uri" => $this->redirect_uri,
            "client_id" => $this->client_id
        ];
        $ch = curl_init();
        curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->client_secret
        ]
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($response, true);
        if(isset($json['refresh_token'])):
            $ref_token=$json['refresh_token'];
            $data = array();
            $data['ref_token'] = $json['refresh_token'];
            $data['expires']=time()+2500000;
            $jsonstr=json_encode($data);
            file_put_contents(APP_ROOT."/log/kouzi.token.json",$jsonstr);
        else:
            $ref_token="";
        endif;
        return $ref_token;
    }
    public function GetAccToken($reftoken){
        $url = 'https://api.coze.cn/api/permission/oauth2/token';
        $postData = [
            "grant_type" => "refresh_token",
            "refresh_token" => $reftoken,
            "client_id" => $this->client_id
        ];
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer '.$this->client_secret
            ]
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($response, true);
        if(isset($json['refresh_token'])):
            $acc_token=$json['access_token'];
            $data = array();
            $data['ref_token'] = $json['refresh_token'];
            $data['expires']=time()+2500000;
            $jsonstr=json_encode($data);
            file_put_contents(APP_ROOT."/log/kouzi.token.json",$jsonstr);
        else:
            $acc_token="";
        endif;
        return $acc_token;
    }
    function RunKouZi($parameters,$acc_token){
        $url = 'https://api.coze.cn/v1/workflow/run';
        $postData = array(
            "workflow_id" => $this->flow_id,
            "parameters" => $parameters,
            "is_async"=>true
        );
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $acc_token",
                "Content-Type: application/json"
            ]
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($response,true);
        return $json;
    }
}