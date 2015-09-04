<?php

require_once dirname(__FILE__) . '/../../selectors/option_list.php';
/** @noinspection PhpIncludeInspection */
require_once dirname(__FILE__) . "/../../languages/{$_SESSION['if_lang']}/admin/admuserstxt.php";

/**
 * @param array $selected_column_indexes
 * @return array
 */
function get_admin_users_list_fieldname_options($selected_column_indexes){
    $fieldNames = get_admin_users_list_fieldnames();
    $list = array();
    foreach ($fieldNames as $i=>$field){
        $list[$i]['name'] = $field->title;
        $list[$i]['val'] = ''.$i;
    }
    return option_list($list, $selected_column_indexes);
}

/**
 * @return array
 */
function get_admin_users_default_column_indexes(){
    // FIXME: magic numbers
    return array(
        "0", // user
        "1", // email
//      "2", // verified email
        "3", // group
        "4", // first session
        "5", // last session
        "6", // language
        "7", // age
//        "8", // gender
//        "9", // occupation
//        "10", // formation
        "11", // active
//        "12", // dstart
        "13", // dtime
//        "14", // cstartb
        "15", // ctimeb
//        "16", // cstarta
        "17", // ctimea
//        "18", // ustartb
        "19", // utimeb
//        "20", // ustarta
        "21", // utimea
        "22", // asscount
        "23", // assavg
//        "24", // assintavg
//        "25", // assorgavg
        "26", // evacount
        "27", // evaavg
//        "28", // evaintavg
//        "29", // evaorgavg
//        "30", // cavg
//        "31", // cintavg
//        "32", // corgavg
//        "33", // uavg
//        "34", // uintavg
//        "35", // uorgavg
//        "36", // davg
//        "37", // dintavg
//        "38", // dorgavg
//        "39", // cevaavg
//        "40", // cevaintavg
//        "41", // cevaorgavg
//        "42", // uevaavg
//        "43", // uevaintavg
//        "44", // uevaorgavg
//        "45", // devaavg
//        "46", // devaintavg
//        "47", // devaorgavg
        
    );
}

/**
 * @return array
 */
function get_admin_users_list_fieldnames(){
    return array(
        (object) array( "title"=>ADMUSERS_USER, "class"=>"user" ),
        (object) array( "title"=>ADMUSERS_EMAIL ),
        (object) array( "title"=>ADMUSERS_VERIFIEDEMAIL ),
        (object) array( "title"=>ADMUSERS_GROUP, "class"=>"center select-filter" ),
        (object) array( "title"=>ADMUSERS_FIRSTSESSION, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_LASTSESSION, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_LANGUAGE, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_AGE, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_SEX, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_OCCUPATION, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_STUDIES, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_ACTIVE, "class"=>"center active" ),
        (object) array( "title"=>ADMUSERS_DITLOIDS_END, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_DITLOIDS_TIME_LEFT, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_COMMONP_PRE_START, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_COMMONP_PRE_DURATION, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_COMMONP_POST_START, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_COMMONP_POST_DURATION, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_ALTERNATIVE_USES_PRE_START, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_ALTERNATIVE_USERS_PRE_DURATION, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_ALTERNATIVE_USES_POST_START, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_ALTERNATIVE_USES_POST_DURATION, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_ASSOCIATIONS_WITH_EVALUATIONS, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_ASSOCIATIONS_AVERAGE, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_ASSOCIATIONS_AVERAGE_INT, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_ASSOCIATIONS_AVERAGE_ORG, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_EVALUATIONS, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_EVALUATIONS_AVERAGE, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_EVALUATIONS_AVERAGE_INT, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_EVALUATIONS_AVERAGE_ORG, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_COMMONP_AVERAGE, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_COMMONP_AVERAGE_INT, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_COMMONP_AVERAGE_ORG, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_ALTERNATIVE_USES_AVERAGE, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_ALTERNATIVE_USES_AVERAGE_INT, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_ALTERNATIVE_USES_AVERAGE_ORG, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_DITLOIDS_AVERAGE, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_DITLOIDS_AVERAGE_INT, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_DITLOIDS_AVERAGE_ORG, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_COMMONP_EV_AVERAGE, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_COMMONP_EV_AVERAGE_INT, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_COMMONP_EV_AVERAGE_ORG, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_ALTERNATIVE_USES_EV_AVERAGE, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_ALTERNATIVE_USES_EV_AVERAGE_INT, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_ALTERNATIVE_USES_EV_AVERAGE_ORG, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_DITLOID_EV_AVERAGE, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_DITLOID_EV_AVERAGE_INT, "class"=>"center" ),
        (object) array( "title"=>ADMUSERS_DITLOID_EV_AVERAGE_ORG, "class"=>"center" ),
    );
}