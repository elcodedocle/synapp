<?php
require_once dirname(__FILE__) . '/../../../synapp/languages/create_all_languages_table.php';
require_once dirname(__FILE__) . '/../../../synapp/connect.php';
function init()
{
    $link = connect(false);
    if (mysqli_select_db($link, "synapp_db")) {
        mysqli_close($link);
        unset($link);
        return INST_DB_EXISTS;
    }
    $sql = "CREATE DATABASE synapp_db";
    if (!mysqli_query($link, $sql)) {
        die ("mysql CREATE DATABASE error.");
    }
    $sql = "ALTER DATABASE synapp_db CHARACTER SET utf8 COLLATE utf8_unicode_ci";
    if (!mysqli_query($link, $sql)) {
        die ("mysql ALTER DATABASE synapp_db CHARACTER SET utf8 COLLATE utf8_unicode_ci error.");
    }
    if (!mysqli_select_db($link, "synapp_db")) {
        die ("mysqli_select_db() error.");
    }

    create_all_languages_table($link);

    $sql = "CREATE TABLE IF NOT EXISTS users (user VARCHAR(32) UNIQUE KEY,pass VARCHAR(64),recovery VARCHAR(64),"
        . "firstdate INT,hfirstdate BIT DEFAULT 0,missed_logins INT DEFAULT 0,last_login INT,hlast_login BIT DEFAULT 0,ip INT UNSIGNED,last_update INT,"
        . "interface_language VARCHAR(4),hinterface_language BIT DEFAULT '0',working_group VARCHAR(32),hworking_group BIT DEFAULT 0,input_language VARCHAR(4),hinput_language BIT DEFAULT 0,hprofile BIT DEFAULT 0,"
        . "gender VARCHAR(1) DEFAULT NULL, hgender BIT DEFAULT 0,birthday DATE, hbirthday BIT DEFAULT 0,studies VARCHAR(256), hstudies BIT DEFAULT 0,studies_type VARCHAR(64), hstudies_type BIT DEFAULT 0,studies_level INT, hstudies_level BIT DEFAULT 0, occupation VARCHAR(256),hoccupation BIT DEFAULT 0,"
        . "email VARCHAR(128),hemail BIT DEFAULT 0,email_confirmation_code VARCHAR(64),confirmed_email BIT DEFAULT 0,avatar VARCHAR(128),hstats BIT DEFAULT 0, ditloid_lock_timestamp INT DEFAULT NULL, ditloid_time_left_when_locked INT DEFAULT 0, gotestbefore INT DEFAULT 0,"
        . "gotestafter INT DEFAULT 0, timer_ctestb_start INT DEFAULT 0, timer_ctestb_end INT DEFAULT 0, timer_utestb_start INT DEFAULT 0, timer_utestb_end INT DEFAULT 0, timer_utesta_start INT DEFAULT 0, timer_utesta_end INT DEFAULT 0, timer_ctesta_start INT DEFAULT 0, timer_ctesta_end INT DEFAULT 0, fbid VARCHAR(255) DEFAULT NULL)";
    if (!mysqli_query($link, $sql)) {
        die('Error: ' . var_export($link->errorInfo(), true));
    }

    $sql = "CREATE TABLE `interface_languages` (native_name VARCHAR(32), iso6392_code VARCHAR(4) UNIQUE KEY, users INT DEFAULT 0)";
    if (mysqli_query($link, $sql)) {
        $sql = "INSERT INTO interface_languages VALUES (\"español\", \"spa\", \"0\")";
        mysqli_query($link, $sql);
        $sql = "INSERT INTO interface_languages VALUES (\"english\", \"eng\", \"0\")";
        mysqli_query($link, $sql);
        $sql = "INSERT INTO interface_languages VALUES (\"galego\", \"glg\", \"0\")";
        mysqli_query($link, $sql);
        $sql = "INSERT INTO interface_languages VALUES (\"polski\", \"pol\", \"0\")";
        mysqli_query($link, $sql);
    }

    $sql = "CREATE TABLE `groups` (name VARCHAR(32), users INT DEFAULT 0)";
    if (mysqli_query($link, $sql)) {
        $sql = "INSERT INTO groups VALUES (\"DEFAULT_GROUP_A\", \"0\")";
        mysqli_query($link, $sql);
        $sql = "INSERT INTO groups VALUES (\"DEFAULT_GROUP_B\", \"0\")";
        mysqli_query($link, $sql);
    }

    $sql = "CREATE TABLE IF NOT EXISTS `images` (id VARCHAR(69) UNIQUE KEY, name VARCHAR(32), associated_group VARCHAR(32), uploader VARCHAR(32), KEY(associated_group))";
    if (!mysqli_query($link, $sql)) {
        die('Error: ' . var_export($link->errorInfo(), true));
    }

    $sql = "CREATE TABLE IF NOT EXISTS `stats` (user VARCHAR(32),lang VARCHAR(4), day DATE,type INT,"
        . "assigned_group VARCHAR(32), upvotes INT DEFAULT 0, downvotes INT DEFAULT 0, neutralvotes INT DEFAULT 0, popupvotes INT DEFAULT 0, popdownvotes INT DEFAULT 0, popneutralvotes INT DEFAULT 0, count INT DEFAULT 0, UNIQUE INDEX `user_lang_day_type_assigned_group` (`user`, `lang`, `day`, `type`, `assigned_group`))";
    if (!mysqli_query($link, $sql)) {
        die('Error: ' . var_export($link->errorInfo(), true));
    }
    $sql = "CREATE TABLE IF NOT EXISTS `test_ranks` (user VARCHAR(32), ttype VARCHAR(32),evals INT,orgfullvotes INT, orghalfvotes INT, orgzerovotes INT, cohfullvotes INT, cohhalfvotes INT, cohzerovotes INT, UNIQUE INDEX uti (user,ttype))";
    if (!mysqli_query($link, $sql)) {
        die('Error: ' . var_export($link->errorInfo(), true));
    }
    $sql = "CREATE TABLE IF NOT EXISTS `confirmed_emails` (user VARCHAR(32),email VARCHAR(128) UNIQUE KEY, KEY(user))";
    if (!mysqli_query($link, $sql)) {
        die('Error: ' . var_export($link->errorInfo(), true));
    }
    return INST_OK;
}
