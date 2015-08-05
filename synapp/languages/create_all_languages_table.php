<?php
/**
 * @param \PDO $link
 */
function create_all_languages_table($link)
{
    $file = "languages/languages.txt";
    if (!($fp = fopen($file, "r"))) {
        die ("Error - cannot open" . $file);
    } else {
        $languages_list = fread($fp, filesize($file));
        fclose($fp);
    }
    $sql = "CREATE TABLE temp (languages_list LONGTEXT)";
    if ($link->exec($sql)===false) {
        die('Error: ' . var_export($link->errorInfo(), true));
    }
    $sql = "INSERT INTO temp values ( :languageslist )";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':languageslist', $languages_list, PDO::PARAM_STR);
    if ($stmt->execute() === false) {
        die('Error: ' . var_export($link->errorInfo(), true));
    }
    $dumpfile = time() . rand(1, 10000000) . ".txt";
    $sql = "SELECT * FROM temp INTO dumpfile \"" . $dumpfile . "\"";
    if ($link->exec($sql)===false) {
        die('Error: ' . var_export($link->errorInfo(), true));
    }
    $sql = "DROP TABLE temp";
    if ($link->exec($sql)===false) {
        die('Error: ' . var_export($link->errorInfo(), true));
    }
    $sql = "CREATE TABLE IF NOT EXISTS all_languages (iso6392_code VARCHAR(10),iso6391_code VARCHAR(10),english_name VARCHAR(256),native_name VARCHAR(256))";
    if ($link->exec($sql)===false) {
        die('Error: ' . var_export($link->errorInfo(), true));
    }
    $sql = "TRUNCATE all_languages";
    if ($link->exec($sql)===false) {
        die('Error: ' . var_export($link->errorInfo(), true));
    }
    $sql = "LOAD DATA INFILE \"" . $dumpfile . "\" IGNORE INTO TABLE all_languages FIELDS TERMINATED BY ' \t'";
    if ($link->exec($sql)===false) {
        die('Error: ' . var_export($link->errorInfo(), true));
    }
}
