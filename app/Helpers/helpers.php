<?php


function dEncrypt($value)
{
    $newkey = 'AX345678ZX98765Y';
    $newEncrypter = new \Illuminate\Encryption\Encrypter($newkey, 'AES-128-CBC');
    return $newEncrypter->encrypt($value);
}

function dDecrypt($value)
{
    $newkey = 'AX345678ZX98765Y';
    $newEncrypter = new \Illuminate\Encryption\Encrypter($newkey, 'AES-128-CBC');
    return $newEncrypter->decrypt($value);
}
