<?php
require_once dirname(__FILE__) . '/config/deployment_environment.php';
require_once dirname(
        __FILE__
    ) . '/' . SYNAPP_CONFIG_DIRNAME . '/profile_constants_constraints_defaults_and_selector_values.php';
require_once dirname(__FILE__) . '/parsers.php';
require_once dirname(__FILE__) . '/user_email_exist.php';

/**
 * @param PDO $link
 * @param array $rd
 * @param bool $nocaptcha
 * @return array
 */
function process_registration_form($link, $rd, $nocaptcha = false)
{

    $ea = array('err' => false, 'usr' => "", 'pass' => "", 'il' => "", 'capt' => "");

    $i = parse($rd['user'], USER_MINLENGTH, USER_MAXLENGTH);
    switch ($i) {
        case 0:
            $ea['usr'] = "";
            break;
        case 1:
            $ea['usr'] = REG_ERR_USR_1;
            break;
        case 2:
            $ea['usr'] = REG_ERR_USR_2;
            break;
        case 3:
            $ea['usr'] = REG_ERR_USR_3;
            break;
        case 4:
            $ea['usr'] = REG_ERR_USR_4;
            break;
    }
    if ($i !== 0) {
        $ea['err'] = true;
    } elseif ($rd['user'] == $rd['pass']) {
        $ea['pass'] = REG_ERR_PASS_1;
        $ea['err'] = true;
    }
    $i = parse($rd['pass'], PASS_MINLENGTH, PASS_MAXLENGTH, 1);
    switch ($i) {
        case 2:
            $ea['pass'] = REG_ERR_PASS_2;
            break;
        case 3:
            $ea['pass'] = REG_ERR_PASS_3;
            break;
        case 4:
            $ea['pass'] = REG_ERR_PASS_4;
            break;
        case 5:
            $ea['pass'] = REG_ERR_PASS_5;
            break;
    }
    if ($i !== 0) {
        $ea['err'] = true;
    } elseif (!($rd['pass'] === $rd['pass2'])) {
        $ea['pass'] = REG_ERR_PASS_6;
        $ea['err'] = true;
    }
    $found = false;
    foreach ($_SESSION['interface_languages'] as $lang) {
        if ($lang['val'] == $rd['ilang']) {
            $found = true;
            break;
        }
    }
    if (($rd['ilang'] == "") || !$found) {
        $ea['il'] = REG_ERR_ILANG;
        $ea['err'] = true;
    }

    if ($ea['err'] == false) {
        if (!$nocaptcha && !captcha_verify_word()) {
            $ea['capt'] = REG_ERR_CAPT;
            $ea['err'] = true;
        } elseif (user_exist($link, $rd['user'])) {
            $ea['usr'] = REG_ERR_USR_5;
            $ea['err'] = true;
        }
    }

    if (!$ea['err']) {
        
        if (($stmt = $link->query("SELECT name FROM groups ORDER BY RAND() LIMIT 1")) === false || ($row = $stmt->fetch(PDO::FETCH_ASSOC)) === false){
            error_log("Database operation error retrieving user registration group.");
            die("Database operation error.");
        }
        $group = $row['name'];

        /* adding new user to users table */

        $use_password_hash =
            defined('SYNAPP_USE_PASSWORD_HASH_AUTHENTICATION')
            && (
                SYNAPP_USE_PASSWORD_HASH_AUTHENTICATION === true
                ||
                is_string(SYNAPP_USE_PASSWORD_HASH_AUTHENTICATION)
                && (trim(strtolower(SYNAPP_USE_PASSWORD_HASH_AUTHENTICATION)) === 'on'
                    || trim(strtolower(SYNAPP_USE_PASSWORD_HASH_AUTHENTICATION)) === 'true'
                    || trim(strtolower(SYNAPP_USE_PASSWORD_HASH_AUTHENTICATION)) === '1')
            ) ?
                true : false;
        
        $hashedPassword = $use_password_hash ? password_hash(
            $rd['pass'],
            SYNAPP_PASSWORD_DEFAULT
        ) : hash("sha256", $rd['pass'] . NORAINBOW_SALT);
        $sql = "INSERT INTO users (
              user
            , pass 
            , recovery
            , firstdate
            , hfirstdate
            , missed_logins
            , last_login
            , hlast_login
            , ip
            , last_update
            , interface_language
            , hinterface_language
            , working_group
            , hworking_group
            , input_language
            , hinput_language
            , hprofile
            , gender
            , hgender
            , birthday
            , hbirthday
            , studies
            , hstudies
            , studies_type
            , hstudies_type
            , studies_level
            , hstudies_level
            , occupation
            , hoccupation
            , email
            , hemail
            , email_confirmation_code
            , confirmed_email
            , avatar
            , nocaptcha
            , hstats
            , ditloid_lock_timestamp
            , ditloid_time_left_when_locked
            , gotestbefore 
            , gotestafter
            , timer_ctestb_start
            , timer_ctestb_end
            , timer_utestb_start
            , timer_utestb_end
            , timer_utesta_start
            , timer_utesta_end
            , timer_ctesta_start
            , timer_ctesta_end
            , fbid
            , active
        ) VALUES (
              :user
            , :hashedpass 
            , :recovery
            , :firstdate
            , b'0' -- hfirstdate
            , 0 -- missed_logins
            , :lastlogin -- last_login
            , b'0' -- hlast_login
            , 0 -- ip
            , 0 -- last_update
            , :ilang -- interface_language
            , b'0' -- hinterface_language
            , :group -- working_group
            , b'0' -- hworking_group
            , :iolang -- input_language
            , b'0' -- hinput_language
            , b'0' -- hprofile
            , '' -- gender
            , b'0' -- hgender
            , NULL -- birthday
            , b'0' -- hbirthday
            , '' -- studies
            , b'0' -- hstudies
            , '' -- studies_type
            , b'0' -- hstudies_type
            , NULL -- studies_level
            , b'0' -- hstudies_level
            , '' -- occupation
            , b'0' -- hoccupation
            , '' -- email
            , b'0' -- hemail
            , :emailconfirmationcode
            , 1 -- confirmed_email
            , '' -- avatar
            , b'0' -- nocaptcha
            , b'0' -- hstats
            , 0 -- ditloid_lock_timestamp
            , 0 -- ditloid_time_left_when_locked
            , 1 -- gotestbefore 
            , 0 -- gotestafter
            , 0 -- timer_ctestb_start
            , 0 -- timer_ctestb_end
            , 0 -- timer_utestb_start
            , 0 -- timer_utestb_end
            , 0 -- timer_utesta_start
            , 0 -- timer_utesta_end
            , 0 -- timer_ctesta_start
            , 0 -- timer_ctesta_end
            , :fbid -- fbid
            , 1 -- active
        )";
        
        $stmt = $link->prepare($sql);
        
        $stmt->bindValue(':user',$rd['user'],PDO::PARAM_STR);
        $stmt->bindValue(':hashedpass',$hashedPassword,PDO::PARAM_STR);
        $stmt->bindValue(':recovery',hash("sha256", mt_rand()),PDO::PARAM_STR);
        $stmt->bindValue(':firstdate',time(),PDO::PARAM_INT);
        $stmt->bindValue(':lastlogin',time(),PDO::PARAM_INT);
        $stmt->bindValue(':ilang',$_SESSION['if_lang'],PDO::PARAM_STR);
        $stmt->bindValue(':group',$group,PDO::PARAM_STR);
        $stmt->bindValue(':iolang',$rd['ilang'],PDO::PARAM_STR);
        $stmt->bindValue(':fbid',isset($rd['fbid'])?$rd['fbid']:null,isset($rd['fbid'])?PDO::PARAM_STR:PDO::PARAM_NULL);
        $stmt->bindValue(':emailconfirmationcode',hash("sha256", mt_rand()),PDO::PARAM_STR);
        
        if ($stmt->execute() === false) {
            die('Error: ' . var_export($link->errorInfo(), true) . PHP_EOL . $sql);
        }

    }
    
    return $ea;
    
}
