<?php
include 'openid.php';
include 'steamauth.php';

session_start();
$steam = new SteamAuth();
$steam->setReturnUrl('example.php');

if($steam->verifyLogin()) {
    $steam->setSteamKey('API_KEY'); //YOU CAN GET THIS HERE https://steamcommunity.com/dev/apikey
    $user = $steam->loadProfile();
    $_SESSION['user'] = $user->personaname;
    
    echo '<pre>'.print_r($user,true).'</pre>';
}else if(!isset($_SESSION['user']))
    echo $steam->getAuthButton();

if(isset($_SESSION['user']))
    echo 'Welcome, '.$_SESSION['user'].'!';
