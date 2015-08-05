<?php
/**
 * @param \PDO $link
 * @return bool
 */
function put_evaluation($link)
{
    /**
     * @param PDO $link
     * @param string $user
     * @param string $field1
     * @param string $field2
     * @param string $lang
     * @param string $group
     * @param string $id1
     * @param string $id2
     * @param string $word
     * @param int $type
     */
    function update(
        $link,
        $user,
        $field1,
        $field2,
        $lang,
        $group,
        $id1,
        $id2,
        $word,
        $type
    ) {
        
        $sql = "SELECT associd FROM `associations` WHERE id1 = :id1 AND id2 = :id2 AND word = :word AND user = :user AND assigned_group = :group AND lang = :lang AND type = :type";
        $stmt = $link->prepare($sql);
        $stmt->bindValue(':id1' , $id1 , PDO::PARAM_STR);
        $stmt->bindValue(':id2' , $id2 , PDO::PARAM_STR);
        $stmt->bindValue(':word' , $word , PDO::PARAM_STR);
        $stmt->bindValue(':type' , $type , PDO::PARAM_INT);
        $stmt->bindValue(':user' , $user , PDO::PARAM_STR);
        $stmt->bindValue(':group' , $group , PDO::PARAM_STR);
        $stmt->bindValue(':lang' , $lang , PDO::PARAM_STR);
        if ($stmt->execute() === false) {
            error_log(var_export($link->errorInfo(), true));
            die("Error performing database operation.");
        }
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $sql = "INSERT INTO `evaluations` ( associd , evaluator , vote , popvote ) values ( :associd , :user , :vote , :popvote )";
            $stmt = $link->prepare($sql);
            $stmt->bindValue(':associd',$row['associd'],PDO::PARAM_STR);
            $stmt->bindValue(':user',$_SESSION['user_array']['user'],PDO::PARAM_STR);
            $stmt->bindValue(':vote',$field1==='upvotes'?2:($field1==='neutralvotes'?1:0),PDO::PARAM_INT);
            $stmt->bindValue(':popvote',$field2==='popupvotes'?2:($field2==='popneutralvotes'?1:0),PDO::PARAM_INT);
            if ($stmt->execute() === false) {
                error_log(var_export($link->errorInfo(), true));
                die("Error performing database operation.");
            }
        }
        
    }

    // Only the last presented item on a session can be evaluated
    // this is done on purpose, to avoid abuse.
    // A ticket is generated to ensure the item evaluation received 
    // matches the presented item, both to avoid abuse and accidental
    // duplicates.
    if (
        !isset($_SESSION['rand_ticket']) ||
        !isset($_SESSION['hashed_ticket']) ||
        hash(
            "sha256",
            $_SESSION['rand_ticket'] . $_SESSION['otheruser']
        ) !== $_POST['ticket']
        ||
        $_SESSION['hashed_ticket'] !== hash("sha256", $_POST['ticket'] . $_POST['id1'] . $_POST['id2'])
    ) {
        die ("Bad ticket.");
    }
    
    unset($_SESSION['hashed_ticket']);
    unset($_SESSION['rand_ticket']);
    
    if (!isset($_POST['popvote']) || !isset($_POST['vote'])) {
        return false;
    }
    update(
        $link,
        $_SESSION['otheruser'],
        $_POST['vote'],
        $_POST['popvote'],
        $_POST['lang'],
        $_POST['group'],
        $_POST['id1'],
        $_POST['id2'],
        $_POST['word'],
        $_POST['type']
    );
    return true;
}
