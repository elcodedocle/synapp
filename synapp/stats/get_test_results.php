<?php

/**
 * WARNING: This function does not perform sanity check on the orderby clause!
 * To prevent SQL injection never use it without validating the clause first.
 *
 * $user and $input_language should also be checked to prevent access bypass
 * injections, since are used to compose the names of the tables to be queried.
 *
 * @param \PDO $link
 * @param string $user
 * @param string $input_language
 * @param string $orderby
 * @param int|null $id
 * @param int|null $type
 * @return bool|PDOStatement|null
 */
function get_test_results($link, $user, $input_language, $orderby, $id = null, $type = null)
{
    if ($type !== null && $type==1){
        $sql = '';
    } else {
        $sql = "SELECT 
                  resultid AS id
                , ttypeid +1 AS type
                , tresourceid
                , val AS word
                , lang
                , ROUND(
                    (
                        (
                              IFNULL(orgfullvotes,0)
                            + IFNULL(orghalfvotes,0) * 0.5
                            + IFNULL(cohfullvotes,0)
                            + IFNULL(cohhalfvotes,0) *0.5 
                        )
                        /
                        IFNULL(
                            NULLIF(
                                  IFNULL(orgfullvotes,0)
                                + IFNULL(orgzerovotes,0)
                                + IFNULL(orghalfvotes,0)
                                + IFNULL(cohfullvotes,0)
                                + IFNULL(cohzerovotes,0)
                                + IFNULL(cohhalfvotes,0)
                                , 0
                            )
                            , 1
                        )
                    ) * 10
                    , 2
                ) AS allpointszerotenscale
                , ROUND(
                    (
                        (
                              IFNULL(cohfullvotes,0)
                            + IFNULL(cohhalfvotes,0) * 0.5 
                        )
                        /
                        IFNULL(
                            NULLIF(
                                  IFNULL(cohfullvotes,0)
                                + IFNULL(cohzerovotes,0)
                                + IFNULL(cohhalfvotes,0)
                                ,0
                            )
                            , 1 
                        )
                    ) * 10
                    , 2
                ) AS intpointszerotenscale
                , ROUND(
                    (
                        (
                              IFNULL(orgfullvotes,0)
                            + IFNULL(orghalfvotes,0) * 0.5 
                        )
                        /
                        IFNULL(
                            NULLIF(
                                  IFNULL(orgfullvotes,0)
                                + IFNULL(orgzerovotes,0)
                                + IFNULL(orghalfvotes,0)
                                , 0
                            )
                            , 1
                        )
                    ) * 10
                    , 2
                ) AS orgpointszerotenscale
                , firstedit
                , FROM_UNIXTIME(lastedit) AS time_stamp
                , orgfullvotes AS upvotes
                , orghalfvotes AS neutralvotes
                , orgzerovotes AS downvotes
                , cohfullvotes AS popupvotes
                , cohhalfvotes AS popneutralvotes
                , cohzerovotes AS popdownvotes
                FROM test_results WHERE user = :user AND lang = :lang " 
            . ($id!==null?' AND resultid = :id ':'');
    }
    
    if ($type === null || $type==1){
        if ($sql !== ''){
            $sql .= " UNION ALL ";
        }
        $sql .= "SELECT 
            did AS id
            , dgid + 1 AS type
            , NULL AS tresourceid
            , val AS word
            , lang
            , ROUND(
                (
                    (
                          IFNULL(orgfullvotes,0)
                        + IFNULL(orghalfvotes,0) * 0.5
                        + IFNULL(cohfullvotes,0)
                        + IFNULL(cohhalfvotes,0) *0.5 
                    )
                    /
                    IFNULL(
                        NULLIF(
                              IFNULL(orgfullvotes,0)
                            + IFNULL(orgzerovotes,0)
                            + IFNULL(orghalfvotes,0)
                            + IFNULL(cohfullvotes,0)
                            + IFNULL(cohzerovotes,0)
                            + IFNULL(cohhalfvotes,0)
                            , 0
                        )
                        , 1
                    )
                ) * 10
                , 2
            ) AS allpointszerotenscale
            , ROUND(
                (
                    (
                          IFNULL(cohfullvotes,0)
                        + IFNULL(cohhalfvotes,0) * 0.5
                    )
                    /
                    IFNULL(
                        NULLIF(
                              IFNULL(cohfullvotes,0)
                            + IFNULL(cohzerovotes,0)
                            + IFNULL(cohhalfvotes,0)
                            , 0
                        )
                        , 1
                    )
                ) * 10
                , 2
            ) AS intpointszerotenscale
            , ROUND(
                (
                    (
                          IFNULL(orgfullvotes,0)
                        + IFNULL(orghalfvotes,0) * 0.5
                    )
                    /
                    IFNULL(
                        NULLIF(
                              IFNULL(orgfullvotes,0)
                            + IFNULL(orgzerovotes,0)
                            + IFNULL(orghalfvotes,0)
                            , 0
                        )
                        , 1
                    )
                ) * 10
                , 2
            ) AS orgpointszerotenscale
            , 'NULL' AS firstedit
            , 'NULL' AS time_stamp
            , orgfullvotes AS upvotes
            , orghalfvotes AS neutralvotes
            , orgzerovotes AS downvotes
            , cohfullvotes AS popupvotes
            , cohhalfvotes AS popneutralvotes
            , cohzerovotes AS popdownvotes
            FROM vditloid_results WHERE user = :useragain AND lang = :langagain " 
            . ($id!==null?' AND did = :id ':'');
    }
    
    if (isset($orderby) && $orderby !== ''){
        $sql .= " ORDER BY $orderby ";
    }
    
    $stmt = $link->prepare($sql);

    if ($type === null || $type!=1){
        $stmt->bindValue(':user',$user,PDO::PARAM_STR);
        $stmt->bindValue(':lang',$input_language,PDO::PARAM_STR);
    }
    if ($type === null || $type==1){
        $stmt->bindValue(':useragain',$user,PDO::PARAM_STR);
        $stmt->bindValue(':langagain',$input_language,PDO::PARAM_STR);
    }
    if ($id!==null){
        $stmt->bindValue(':id',$id,PDO::PARAM_INT);
    }
        
    if ($stmt->execute() === false) {

        error_log(var_export($link->errorInfo(), true) . PHP_EOL . $sql);

    }

    return $stmt;

}