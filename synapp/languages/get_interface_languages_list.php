<?php
/**
 * @param \PDO $link
 * @return array
 */
function get_interface_languages_list($link)
{
    $sql = "SELECT * FROM interface_languages";
    $stmt = $link->query($sql);
    $list = array();
    if ($stmt!==false){
        while ($language = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $node['name'] = $language['native_name'];
            $node['val'] = $language['iso6392_code'];
            $list[$language['iso6392_code']] = $node;
        }
    }
    return $list;
}