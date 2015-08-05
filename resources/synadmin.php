<?php
require_once dirname(__FILE__) . '/../synapp/admin/account/register_admin_user.php';
require_once dirname(__FILE__) . '/../synapp/admin/account/change_admin_password.php';
// php.exe -dxdebug.remote_enable=1 -dxdebug.remote_host=127.0.0.1 -dxdebug.remote_port=9000 -dxdebug.remote_mode=req -f synadmin.php -- command [param1 [.. paramN]]
/**
 * @param string $user
 * @param string $email
 * @param string $password
 * @param string $lang
 * @param \PDO $link
 * @return bool
 */
function adduser($user, $email, $password, $lang, $link){
    $out = register_admin_user(array(
            'user' => $user,
            'email' => $email,
            'password' => $password,
            'password2' => $password,
            'lang' => $lang,
        ), 
        $link
    );
    if ($out === false){
        return false;
    } elseif (isset($out['error'])){
        return $out['error'];
    } else {
        return true;
    }
}

/**
 * @param string $user
 * @param string $lang
 * @param \PDO $link
 * @return string
 */
function deluser($user, $lang, $link){
    // TODO: implement deletion of account, preferably with password prompt and cascade deletion of associated resources warning
    return $lang === 'spa'? 'funcion no implementada':'function not implemented';
}

/**
 * @param string $user
 * @param string $password
 * @param \PDO $link
 * @return bool
 */
function changepassword($user, $password, $link){
    return change_admin_password_no_verify($user, $password, $link);
}

/**
 * @param string $termfrompage
 * @param string $termtopage
 * @param string $message
 * @param bool $hidden
 * @return bool|string
 */
function prompt($termfrompage = 'auto', $termtopage = 'pass', $message = 'Password: ', $hidden = true) {
    if (PHP_SAPI !== 'cli') {
        return false;
    }
    echo mb_convert_encoding($message, $termtopage, 'UTF-8');
    $ret =
        $hidden
            ? exec(
            PHP_OS === 'WINNT' || PHP_OS === 'WIN32'
                ? __DIR__ . '\prompt_win.bat'
                : 'read -s PW; echo $PW'
        )
            : rtrim(fgets(STDIN), PHP_EOL)
    ;
    if ($hidden) {
        echo PHP_EOL;
    }
    return mb_convert_encoding($ret, 'UTF-8', $termfrompage);
}
if (PHP_OS === 'WINNT' || PHP_OS === 'WIN32' && stripos(setlocale(LC_ALL,"0"),'LC_CTYPE=Spanish')){
    exec('chcp 850');
    $termfrompage = 'CP850';
    $termtopage = 'CP850';
} else {
    $termfrompage = 'auto';
    $termtopage = 'pass';
}
$lang = (stripos(setlocale(LC_ALL,"0"),'LC_CTYPE=Spanish')!==false)?'spa':'eng';
if (php_sapi_name()!=="cli"){
    if ($lang === 'spa'){
        echo  "Este script debe ser ejecutado desde la linea de comandos." . PHP_EOL . " Intente php -f `nombre_del_script' desde la linea de comandos." . PHP_EOL;
    } else {
        echo  "This is a command line script." . PHP_EOL . "Try php -f `script_file_name' from the command line." . PHP_EOL;
    }
    exit;
}
if (isset($argv[1])&&($argv[1]==='-h'||$argv[1]==='--help'||$argv[1]==='help')&&($argc===2)){
    echo mb_convert_encoding(long_help($argv, $lang), $termtopage, 'UTF-8');
    exit;
}
$bError = ($argc!==3&&$argc!==4);
if (!$bError){
    $out = false;
    require_once  dirname(__FILE__) . '/../synapp/connect.php';
    switch ($argv[1]){
        case 'adduser':
            $out = $argc===4?adduser($argv[2], $argv[3], prompt($termfrompage, $termtopage), $lang, connect()):false;
            break;
        case 'deluser':
            $out = $argc===3?deluser($argv[2], $lang, connect()):false;
            break;
        case 'changepassword':
            $out =  $argc===3?changepassword($argv[2], prompt($termfrompage, $termtopage), connect()):false;
            break;
        default: $bError = true;
    }
    $bError = ($out !== true);
}
if ($bError !== false){
    if (isset($out) && is_string($out)){
        echo mb_convert_encoding($out, $termtopage, 'UTF-8') . PHP_EOL . PHP_EOL;
    }
    echo mb_convert_encoding(short_help($argv, $lang), $termtopage, 'UTF-8');
    exit;
}
/**
 * @param array $argv
 * @param string $lang
 * @return string
 */
function short_help($argv, $lang = 'eng'){
    if ($lang === 'spa'){
        return "Uso: php -f ".$argv[0]." -- adduser <nombre_de_usuario> <email>" . PHP_EOL .
               "     php -f ".$argv[0]." -- deluser <nombre_de_usuario>" . PHP_EOL . PHP_EOL .
               "     php -f ".$argv[0]." -- changepassword <nombre_de_usuario> " . PHP_EOL .
               "     php -f ".$argv[0]." -- help | -h | --help" . PHP_EOL;
    } else {
        return "Usage: php -f ".$argv[0]." -- adduser <user_name> <email>". PHP_EOL .
               "       php -f ".$argv[0]." -- deluser <user_name>". PHP_EOL . PHP_EOL .
               "       php -f ".$argv[0]." -- changepassword <user_name> ". PHP_EOL . PHP_EOL .
               "       php -f ".$argv[0]." -- help | -h | --help" . PHP_EOL;
    }
}

/**
 * @param array $argv
 * @param string $lang
 * @return string
 */
function long_help($argv, $lang = 'eng'){
    if ($lang === 'spa'){
        return "Synapp - script de gestión de cuentas administrativas v0.2.0, por Gael Abadin" . PHP_EOL .
             "Licencia: GPLv3 (http://www.gnu.org/licenses/gpl.html)" . PHP_EOL .
             "Este producto no ofrece ninguna garantia." . PHP_EOL . PHP_EOL .
             "Uso: php -f ".$argv[0]." -- adduser <nombre_de_usuario> <email>" . PHP_EOL .
             "     php -f ".$argv[0]." -- deluser <nombre_de_usuario>" . PHP_EOL . PHP_EOL .
             "     php -f ".$argv[0]." -- changepassword <nombre_de_usuario>" . PHP_EOL . PHP_EOL .
             "     php -f ".$argv[0]." -- help | -h | --help" . PHP_EOL . PHP_EOL .
             "adduser <nombre_de_usuario> <email> Añadir a Synapp un usuario administrativo web" . PHP_EOL .
             "deluser <nombre_de_usuario>         Eliminar un usuario administrativo web de Synapp" . PHP_EOL .
             "changepassword <nombre_de_usuario>  Cambiar el password de un usuario administrativo" . PHP_EOL .
             "help | -h | --help                  Mostrar esta ayuda" . PHP_EOL . PHP_EOL .
            "El idioma de la interfaz se tomará de la locale por defecto o el lenguaje por defecto(podra modificarse en las opciones de la aplicación)" . PHP_EOL . PHP_EOL .
            "No olvide configurar los datos de conexión a la base de datos en" . PHP_EOL . PHP_EOL .
             "synapp/account/{SYNAPP_CONFIG_DIRNAME}/database_host_and_credentials.php" . PHP_EOL . PHP_EOL .
             "(SYNAPP_CONFIG_DIRNAME se define en synapp/account/config/deployment_environment.php)" . PHP_EOL . PHP_EOL;
    } else {
        return "Synapp administrative account management script v0.2.0 by Gael Abadin" . PHP_EOL .
             "License: GPLv3 (http://www.gnu.org/licenses/gpl.html)" . PHP_EOL .
             "This product comes with no warranty." . PHP_EOL .
             "" . PHP_EOL .
             "Usage: php -f ".$argv[0]." -- adduser <username> <email>" . PHP_EOL .
             "       php -f ".$argv[0]." -- deluser <username>" . PHP_EOL . PHP_EOL .
             "       php -f ".$argv[0]." -- changepassword <username>" . PHP_EOL . PHP_EOL .
             "       php -f ".$argv[0]." -- help | -h | --help" . PHP_EOL . PHP_EOL .
             "The interface language will be set to match the default locale or the fallback value (user can change it upon login)" . PHP_EOL . PHP_EOL .
             "adduser <username> <email> Add a synapp web administrative user account" . PHP_EOL .
             "deluser <username>         Remove a synapp web administrative user account" . PHP_EOL .
             "changepassword <username>  Change an administrative user password" . PHP_EOL .
             "help | -h | --help Show this help" . PHP_EOL . PHP_EOL .
             "Don't forget to set up database connection parameters on" . PHP_EOL . PHP_EOL .
             "synapp/account/{SYNAPP_CONFIG_DIRNAME}/database_host_and_credentials.php" . PHP_EOL . PHP_EOL .
             "(SYNAPP_CONFIG_DIRNAME is set on synapp/account/config/deployment_environment.php)" . PHP_EOL . PHP_EOL;
    }
}