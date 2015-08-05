<?php
/**
 * @param PDO $link
 * @return array|bool|null
 */
function get_evaluation_pair($link)
{
    $pair = false;
    $user = $_SESSION['user_array']['user'];
    $group = $_SESSION['user_array']['working_group'];
    $language = $_SESSION['user_array']['input_language'];
    $sql = "SELECT 
          a.associd
        , e.evaluator user
        , a.user otheruser
        , a.assigned_group
        , a.lang
        , a.id1
        , a.id2
        , a.word
        , a.type 
        FROM `associations` a
        LEFT JOIN `evaluations` e 
        ON e.associd=a.associd
        WHERE 
          e.evaluator <> :user
          AND a.user <> :useragain
          AND a.assigned_group <> :group
          AND a.lang = :language
        ORDER BY RAND() LIMIT 1";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(":user", $user, PDO::PARAM_STR);
    $stmt->bindValue(":useragain", $user, PDO::PARAM_STR);
    $stmt->bindValue(":group", $group, PDO::PARAM_STR);
    $stmt->bindValue(":language", $language, PDO::PARAM_STR);
    if ($stmt->execute()!==false) {
        if (($pair = $stmt->fetch(PDO::FETCH_ASSOC))!==false) {
            $pair['group'] = $pair['assigned_group'];
            return $pair;
        }
    }
    return $pair;
}
