<?php
// Encrypt Function
function encrypt($plainText, $key) {
    $secretKey = md5($key);
    $iv = substr(hash('sha256', "aaaabbbbcccccddddeweee"), 0, 16);
    $encryptedText = openssl_encrypt($plainText, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $iv);
    return base64_encode($encryptedText);
}

//Decrypt Function
function decrypt($encryptedText, $key) {
    $key = md5($key);
    $iv = substr(hash('sha256',"aaaabbbbcccccddddeweee"), 0, 16);
    $decryptedText = openssl_decrypt(base64_decode($encryptedText), 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return $decryptedText;
}
?>