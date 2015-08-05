<?php

require_once dirname(__FILE__) . '/../account/config/deployment_environment.php';
if (file_exists(dirname(__FILE__) . '/../account/'.SYNAPP_CONFIG_DIRNAME.'/crypt_constants.php')){
    require_once dirname(__FILE__) . '/../account/'.SYNAPP_CONFIG_DIRNAME.'/crypt_constants.php';
    putenv('GNUPGHOME='.SYNAPP_GNUPG_HOME);
}

/**
 * @param string $enc_data
 * @param string $salt
 * @return bool|mixed
 */
function decrypt_gpg($enc_data,$salt){
    if (!function_exists("gnupg_init") || !function_exists("gnupg_adddecryptkey") || !function_exists("gnupg_decrypt") || !function_exists("gnupg_geterror")){
        error_log("Required GnuPG functions not available. Bypassing password RSA decryption...");
        return $enc_data;
    }
    if(!defined('SYNAPP_GPG_KEY_FINGERPRINT')){
        error_log("Missing required GnuPG settings. Bypassing password RSA decryption...");
        return $enc_data;
    }
    if (defined('CRYPT_BYPASS') && (CRYPT_BYPASS===true || strtolower(CRYPT_BYPASS)==="1" || strtolower(CRYPT_BYPASS)==="true" || strtolower(CRYPT_BYPASS)==="on")){
        return $enc_data;
    }
    $res = gnupg_init();
    $env = getenv('GNUPGHOME');
    if (!gnupg_adddecryptkey($res,SYNAPP_GPG_KEY_FINGERPRINT,"")){
        $error = gnupg_geterror($res);
        error_log("Error trying to add decryption key on ".$env);
        error_log($error);
    }
    if (($dec_data = gnupg_decrypt($res,$enc_data))===false){
        $error = gnupg_geterror($res);
        error_log("Error trying to use decryption key on ".$env);
        error_log($error);
    }
    $dec_data_saltless=preg_replace('/'. preg_quote($salt, '/') . '$/', '', $dec_data);
	
    if ($dec_data_saltless===$dec_data) return false;
    
    $dec_out=preg_replace('/'. preg_quote(strpbrk($dec_data_saltless,'@'), '/') . '$/', '', $dec_data_saltless);
    
    return $dec_out;
}
