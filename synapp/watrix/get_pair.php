<?php
/**
 * @param PDO $link
 * @param int $type
 * @return bool
 */
function get_pair($link, $type)
{
    $group = $_SESSION['user_array']['working_group'];
    $sql = "SELECT collectionid FROM vgroup_collections WHERE `groupname` = :group";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':group',$group,PDO::PARAM_STR);
    $stmt->execute();
    $collectionids = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    $collectionids_string = implode(',',$collectionids);
    $sql = "SELECT
          (
            SELECT COUNT(*) FROM images WHERE collectionid IN ({$collectionids_string})
          ) AS c1
        , (
            SELECT COUNT(*) FROM `associations` 
            WHERE 
                type = :type
                AND user = :user
                AND assigned_group = :working_group
                AND lang = :input_language 
          ) AS c2
        , (
            SELECT ROUND( 
                RAND() * ( ( c1 * ( c1 - 1 ) ) - 2 * c2 - 1 )
            )
        ) AS random_offset";
    /* die ($sql); */
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':user', $_SESSION['user_array']['user'], PDO::PARAM_STR);
    $stmt->bindValue(':working_group', $_SESSION['user_array']['working_group'], PDO::PARAM_STR);
    $stmt->bindValue(':input_language', $_SESSION['user_array']['input_language'], PDO::PARAM_STR);
    $stmt->bindValue(':type', $type, PDO::PARAM_INT);
    if ($stmt->execute()!==false) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row['c1'] * ($row['c1'] - 1) - 2 * $row['c2'] <= 0) {
            return false;
        }
        $random_offset = $row['random_offset'];
        $sql = "SELECT im.* FROM (
            SELECT 
              im1.id AS id1
            , im2.id AS id2 
            FROM (
                SELECT DISTINCT id FROM `images` 
                WHERE collectionid IN ({$collectionids_string})
            ) AS im1 
            INNER JOIN (
                SELECT DISTINCT id FROM `images` WHERE collectionid IN ({$collectionids_string})
            ) AS im2 
            ON im1.id <> im2.id
        ) AS im 
        LEFT OUTER JOIN (
            (
                SELECT id1, id2 FROM `associations`
                WHERE type = :type
                AND user = :user
                AND assigned_group = :groupagainagain
                AND lang = :input_language
            ) UNION ALL (
                SELECT id2, id1 
                FROM `associations`
                WHERE type = :typeagain
                AND user = :useragain
                AND assigned_group = :groupagainagainagain
                AND lang = :input_languageagain
            )
        ) AS assoc 
        USING (id1,id2) 
        WHERE assoc.id1 IS NULL 
        LIMIT 1 OFFSET :offset";
        
        $stmt = $link->prepare($sql);
        $stmt->bindValue(':type', $type, PDO::PARAM_INT);
        $stmt->bindValue(':user',  $_SESSION['user_array']['user'], PDO::PARAM_STR);
        $stmt->bindValue(':groupagainagain',  $_SESSION['user_array']['working_group'], PDO::PARAM_STR);
        $stmt->bindValue(':input_language',  $_SESSION['user_array']['input_language'], PDO::PARAM_STR);
        $stmt->bindValue(':typeagain', $type, PDO::PARAM_INT);
        $stmt->bindValue(':useragain',  $_SESSION['user_array']['user'], PDO::PARAM_STR);
        $stmt->bindValue(':groupagainagainagain',  $_SESSION['user_array']['working_group'], PDO::PARAM_STR);
        $stmt->bindValue(':input_languageagain',  $_SESSION['user_array']['input_language'], PDO::PARAM_STR);
        $stmt->bindValue(':offset', $random_offset, PDO::PARAM_INT);
        /* die($sql); */
        if ($stmt->execute()!==false) {
            $pair = $stmt->fetch(PDO::FETCH_ASSOC);
            return $pair;
        }
    }
    return false;
}
