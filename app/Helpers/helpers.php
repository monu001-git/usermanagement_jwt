<?php

use Illuminate\Support\Facades\Log;

function dEncrypt($value)
{
    $key = hex2bin('b99fe877e5326a31a13096ccb6605553'); 
    $iv = hex2bin('3e66eae19b4959beaa39f7a5e9708ce7'); 
    $encrypted = openssl_encrypt($value, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($encrypted); 
}

function dDecrypt($value)
{ 
    $key = hex2bin('b99fe877e5326a31a13096ccb6605553');
    $iv = hex2bin('3e66eae19b4959beaa39f7a5e9708ce7');
    $encrypted = base64_decode($value);
    $decrypted = openssl_decrypt($encrypted, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return $decrypted;
}


