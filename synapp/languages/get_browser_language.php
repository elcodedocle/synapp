<?php
#########################################################
# Copyright (c) 2008 Darrin Yeager                      #
# http://www.dyeager.org/                               #
# Licensed under BSD license.                           #
#   http://www.dyeager.org/downloads/license-bsd.php    #
#                                                       #
# Improved by Gael Abadin,                              #
# Universidade de Vigo - AGH Kraków                     #
# gael.abadin (mailboxat) gmail (dot) com               #
#########################################################
/**
 * @return string
 */
function getDefaultLanguage()
{
    if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
        return parseDefaultLanguage($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
    } else {
        return parseDefaultLanguage(null);
    }
}

/**
 * @param string $http_accept
 * @param string $deflang
 * @return string
 */
function parseDefaultLanguage($http_accept, $deflang = "en")
{
    if (isset($http_accept) && strlen($http_accept) > 1) {
        # Split possible languages into array
        $x = explode(",", $http_accept);
        $lang = array();
        foreach ($x as $val) {
            #check for q-value and create associative array. No q-value means 1 by rule
            if (preg_match("/(.*);q=([0-1]{0,1}\\.\\d{0,4})/i", $val, $matches)) {
                $lang[$matches[1]] = (float)$matches[2];
            } else {
                $lang[$val] = 1.0;
            }
        }

        #return default language (highest q-value)
        $qval = 0.0;
        foreach ($lang as $key => $value) {
            if ($value > $qval) {
                $qval = (float)$value;
                $deflang = $key;
            }
        }
    }
    return strtolower($deflang);
}
