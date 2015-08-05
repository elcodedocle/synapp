<?php
/**
 * @param array $list
 * @param string|array $default
 * @param bool $echowithnameandid
 * @param bool $doblank
 * @return array
 */
function option_list($list, $default = "", $echowithnameandid = false, $doblank = false)
{

    if ($echowithnameandid !== false) {
        echo "<select name=\"$echowithnameandid\" id=\"$echowithnameandid\">";
    }
    $ll = array();
    if ($doblank) {
        $ll[0] = "<option value=\"\"";
        if ($default === "") {
            $ll[0] = $ll[0] . " selected=\"selected\"";
        }
        $ll[0] = $ll[0] . " >" . "</option>" . PHP_EOL;
        $i = 1;
    } else {
        $i = 0;
    }
    if (is_array($list)) {
        foreach ($list as $val) {
            $ll[$i] = "<option value=\"" . $val['val'] . "\"";
            if (is_array($default)){
                if (in_array($val['val'],$default)) {
                    $ll[$i] = $ll[$i] . " selected=\"selected\"";
                }
            } else if ($default == $val['val']) {
                $ll[$i] = $ll[$i] . " selected=\"selected\"";
            }
            $ll[$i] = $ll[$i] . " >" . $val['name'] . "</option>" . PHP_EOL;
            if ($echowithnameandid !== false) {
                echo $ll[$i];
            }
            $i++;
        }
    }
    if ($echowithnameandid !== false) {
        echo "</select>";
    }

    return $ll;

}
