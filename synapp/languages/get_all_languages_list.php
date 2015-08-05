<?php
/**
 * @param \PDO $link
 * @return array
 */
function get_all_languages_list($link)
{
    $sql = "SELECT * FROM all_languages";
    $stmt = $link->query($sql);

    $list = array();
    if ($stmt!==false){
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $node['name'] = $row['native_name'];
            $node['val'] = $row['iso6392_code'];
            if (trim($node['name']) === "") {
                $node['name'] = $row['english_name'];
            }
            $list[$row['iso6392_code']] = $node;
        }
    }
    return $list;
}
