<?php
require_once dirname(__FILE__) . '/../../account/config/deployment_environment.php';
require_once dirname(__FILE__) . '/../../account/' . SYNAPP_CONFIG_DIRNAME . '/database_host_and_credentials.php';
/**
 * @param \PDO $link
 * @param string $tableName
 * @param array $restrictions
 * @return \PDOStatement
 */
function get_list($link, $tableName = 'vuser_admin_stats', $restrictions = array()){
    // no restrictions
    if (!is_array($restrictions)) { $restrictions = array(); }
    $sql = "SELECT `COLUMN_NAME` 
        FROM `INFORMATION_SCHEMA`.`COLUMNS` 
        WHERE `TABLE_SCHEMA`='".SYNAPP_DB_NAME."'
        AND `TABLE_NAME`='$tableName'";
    $stmt = $link->query($sql);
    $columnNames = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    $acceptedFilters = isset($restrictions['acceptedFilters'])?$restrictions['acceptedFilters']:array(
        'user',
        'email',
    );
    $acceptedOperators = isset($restrictions['acceptedOperators'])?$restrictions['acceptedOperators']: array(
        '=',
        '<>',
        '!=',
        'LIKE',
        'IS NULL',
        'IS NOT NULL',
    );
    $acceptedLogicalOperators = isset($restrictions['acceptedLogicalOperators'])?$restrictions['acceptedLogicalOperators']: array(
        'AND',
        'AND (',
        ') AND',
        ') AND (',
        'OR',
        'OR (',
        ') OR',
        ') OR (',
        'NOT',
        'NOT (',
        ') NOT',
        ') NOT (',
        '(',
        ')',
    );
    $acceptedTypes =  isset($restrictions['acceptedTypes'])?$restrictions['acceptedTypes']: array(
        PDO::PARAM_STR,
        PDO::PARAM_NULL,
        PDO::PARAM_INT,
        PDO::PARAM_NULL,
    );
    $sql = "SELECT * FROM `$tableName` ";
    if (isset($restrictions['filters'])){
        $filters = $restrictions['filters'];
        $sql .= " WHERE ";
        foreach($filters as $filter){
            if (in_array($filter['fieldname'],$acceptedFilters)){
                if (isset($filter['prefix']) && in_array($filter['prefix'],$acceptedLogicalOperators)){
                    $sql .= " {$filter['prefix']} ";
                }
                if (isset($filter['operator'])){
                    if ($filter['operator'] === 'IS NULL'){
                        $sql .= " `{$filter['fieldname']}` IS NULL ";
                    } else if ($filter['operator'] === 'IS NOT NULL'){
                        $sql .= " `{$filter['fieldname']}` IS NOT NULL ";
                    } else if (in_array($filter['operator'],$acceptedOperators)){
                        $sql .= " `{$filter['fieldname']}` {$filter['operator']} :{$filter['placeholder']} ";
                    }
                }
                if (isset($filter['sufix']) && in_array($filter['sufix'],$acceptedLogicalOperators)){
                    $sql .= " {$filter['sufix']} ";
                }
            }
        }
    } else {
        $filters = array();
    }
    if (isset($restrictions['orderby']) && is_array($restrictions['orderby'])){
        $sorderby = " ORDER BY ";
        foreach($restrictions['orderby'] as $column){
            if (in_array($column['name'],$columnNames)){
                $sorderby .= ' `'.$column['name'].'`,';
                if (isset($column['order'])){
                    $sorderby .= strtoupper($column['order']) === 'ASC'? ' ASC ' : (strtoupper($column['order']) === 'DESC'? ' DESC ':'');
                }
            }
        }
        $sorderby = rtrim($sorderby,',') . ' ';
        if ($sorderby !== ' ORDER BY '){
            $sql .= $sorderby;
        }
    }
    if (isset($restrictions['pagesize']) && is_int($restrictions['pagesize'])){
        if (isset($restrictions['page']) && is_int($restrictions['page'])){
            $offset = ($restrictions['page'] - 1) * $restrictions['pagesize'];
            $sql .= " LIMIT {$offset},{$restrictions['pagesize']} ";
        } else {
            $sql .= " LIMIT {$restrictions['pagesize']} ";
        }
    }
    $stmt = $link->prepare($sql);
    foreach($filters as $filter){
        if (in_array($filter['fieldname'],$acceptedFilters) && isset($filter['type']) && in_array($filter['type'],$acceptedTypes)){
            $stmt->bindValue(':'.$filter['placeholder'], $filter['value'],$filter['type']);
        }
    }
    $stmt->execute();
    return $stmt;
}