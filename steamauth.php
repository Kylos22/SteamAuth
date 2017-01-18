<?php
include 'openid.php';

class SteamAuth{

    private $api_key = false;
    private $logged = false;
    private $openid;

    public function __construct(){
        if(!class_exists('LightOpenID'))
            throw new Exception('Missing LightOpenID class');
    }

    public function setSteamKey($api_key){
        if(strpos(file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$api_key}&steamids=X"),'Forbidden') !== false)
            throw new Exception('Invalid Steam API key');

        $this->api_key = $api_key;
    }

    public function setReturnUrl($login_page){
        $this->openid = new LightOpenID('http'.('443' == $_SERVER['SERVER_PORT']?'s':'').'://'.$_SERVER['SERVER_NAME'].'/'.$login_page);
        $this->openid->identity = 'http://steamcommunity.com/openid/';
    }

    public function getAuthUrl(){
        return $this->openid instanceof LightOpenID ? $this->openid->authUrl() : false;
    }

    public function getAuthButton($type = 1){
        switch($type){
            case 1:
                return "<a href='".$this->getAuthUrl()."'><img src='https://steamcommunity-a.akamaihd.net/public/images/signinthroughsteam/sits_large_noborder.png'></a>";
                break;
            case 2:
                return "<a href='".$this->getAuthUrl()."'><img src='https://steamcommunity-a.akamaihd.net/public/images/signinthroughsteam/sits_large_border.png'></a>";
                break;
            case 3:
                return "<a href='".$this->getAuthUrl()."'><img src='https://steamcommunity-a.akamaihd.net/public/images/signinthroughsteam/sits_small.png'></a>";
                break;
            default:
                return false;
        }
    }

    public function verifyLogin(){
        return $this->openid instanceof LightOpenID && $this->openid->mode ? $this->openid->validate() && ($this->logged = true) : false;
    }

    public function loadProfile(){
        if($this->logged) {
            $profile = explode('/', $this->openid->identity);
            $profile = end($profile);

            if($this->api_key) {
                $json = json_decode(file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$this->api_key}&steamids={$profile}"));
                return $json->response->players[0];
            }
        }
        return false;
    }
}
