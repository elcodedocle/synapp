<?php
/**
 * @param string $iso6392_code
 * @param \PDO $link
 * @return mixed
 */
function get_language_by_iso6392_code($iso6392_code, $link)
{
    $sql = "SELECT * FROM interface_languages WHERE iso6392_code = :isocode";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':isocode', $iso6392_code, PDO::PARAM_STR);
    if ($stmt->execute()!==false && $row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['native_name'] !== "") {
            return $row['native_name'];
        } else {
            return $row['eng'];
        }
    } else {
        $link = null;
        die("get_language_by_international_code() ERROR: Language not found.");
    }
}
