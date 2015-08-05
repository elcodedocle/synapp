<?php

require_once dirname(__FILE__) . '/../../selectors/option_list.php';
/** @noinspection PhpIncludeInspection */
require_once dirname(__FILE__) . "/../../languages/{$_SESSION['if_lang']}/admin/admresourcestxt.php";

/**
 * @return array
 */
function get_resources_list_fieldname_options(){
    $fieldNames = get_resources_list_fieldnames();
    $list = array();
    foreach ($fieldNames as $i=>$field){
        $list[$i]['name'] = $field->title;
        $list[$i]['val'] = ''.$i;
    }
    return option_list($list, get_resources_list_default_column_indexes());
}

/**
 * @return array
 */
function get_resources_list_default_column_indexes(){
    // FIXME: magic numbers
    return array(
        "0", // id
        "1", // name
        "2", // associated group
        "3", // uploader
    );
}

/**
 * @return array
 */
function get_user_groups_list_fieldnames(){
    return array(
        (object) array( "title"=>ADMRESOURCES_NAME, "class"=>"center" ),
        (object) array( "title"=>ADMRESOURCES_COLLECTION, "class"=>"center" ),
        (object) array( "title"=>ADMRESOURCES_COMMONPPRERESOURCES, "class"=>"center" ),
        (object) array( "title"=>ADMRESOURCES_COMMONPPOSTRESOURCES, "class"=>"center" ),
        (object) array( "title"=>ADMRESOURCES_USESPRERESOURCES, "class"=>"center" ),
        (object) array( "title"=>ADMRESOURCES_USESPOSTRESOURCES, "class"=>"center" ),
    );
}

/**
 * @return array
 */
function get_resources_list_fieldnames(){
    return array(
        (object) array( "title"=>ADMRESOURCES_ID, "class"=>"center id" ),
        (object) array( "title"=>ADMRESOURCES_NAME, "class"=>"center" ),
        (object) array( "title"=>ADMRESOURCES_COLLECTION, "class"=>"center select-filter" ),
        (object) array( "title"=>ADMRESOURCES_ADDED_BY, "class"=>"center" ),
    );
}

/**
 * @return array
 */
function get_test_resources_list_fieldnames(){
    $mergeNames = array();
    $fieldNames = array(
        (object) array( "title"=>ADMRESOURCES_ASSOCIATED_TESTS, "class"=>"center" ),
    );
    foreach ($_SESSION['interface_languages'] as $language){
        if ($language['val']===$_SESSION['if_lang']){
            $fieldNames[] = (object) array( "title"=>ADMRESOURCES_VALUE." ({$language['name']})", "class"=>"center" );
        } else {
            $mergeNames[] = (object) array( "title"=>ADMRESOURCES_VALUE." ({$language['name']})", "class"=>"center" );
        }
    }
    return array_merge($fieldNames,$mergeNames);
}