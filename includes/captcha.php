<?php
header('Content-type: image/jpeg');
session_start();
if(isset($_GET['login'])){
    $captcha_num = '1234567890abcdefghijklmnopqrstuvwxyz';
    $captcha_num = substr(str_shuffle($captcha_num), 0, 6);
    $_SESSION["cap-login"] = $captcha_num;
    $height = 50; 
    $width = 100;   
    $image_p = imagecreate($width, $height); 
    $black = imagecolorallocate($image_p, 0, 0, 0); 
    $white = imagecolorallocate($image_p, 255, 255, 255); 
    $font_size = 30; 
    imagestring($image_p, $font_size, 20, 20, $captcha_num, $white);
    imagejpeg($image_p, null, 80);
}
if(isset($_GET['register'])){
    $captcha_num = '1234567890abcdefghijklmnopqrstuvwxyz';
    $captcha_num = substr(str_shuffle($captcha_num), 0, 6);
    $_SESSION["cap-register"] = $captcha_num;
    $height = 50; 
    $width = 100;   
    $image_p = imagecreate($width, $height); 
    $black = imagecolorallocate($image_p, 0, 0, 0); 
    $white = imagecolorallocate($image_p, 255, 255, 255); 
    $font_size = 30; 
    imagestring($image_p, $font_size, 20, 20, $captcha_num, $white);
    imagejpeg($image_p, null, 80);
}
if(isset($_GET['pass'])){
    $captcha_num = '1234567890abcdefghijklmnopqrstuvwxyz';
    $captcha_num = substr(str_shuffle($captcha_num), 0, 6);
    $_SESSION["cap-pass"] = $captcha_num;
    $height = 50; 
    $width = 100;   
    $image_p = imagecreate($width, $height); 
    $black = imagecolorallocate($image_p, 0, 0, 0); 
    $white = imagecolorallocate($image_p, 255, 255, 255); 
    $font_size = 30; 
    imagestring($image_p, $font_size, 20, 20, $captcha_num, $white);
    imagejpeg($image_p, null, 80);
}
?>