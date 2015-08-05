<?php


/**
 * @param \PDO $link
 * @param string $ttype
 * @param string $user
 * @param int $ttypeid
 * @param int $tresourceid
 * @param string $evaluator
 * @param string $lang
 */
function process_test_evaluation($link, $ttype, $user, $ttypeid, $tresourceid, $evaluator, $lang){
    
    $votes = update_test_results($link, $ttype, $user, $lang, $evaluator);

    if ($ttype !== 'ditloid'){
        update_test_evaluations($link, $user, $ttypeid, $tresourceid, $evaluator, $lang, $votes);
    }
    
}

/**
 * @param \PDO $link
 * @param string $ttype
 * @param string $user
 * @param string $lang
 * @param string $evaluator
 * @return array
 */
function update_test_results($link, $ttype, $user, $lang, $evaluator){

    $votes = array(
        'orgfullvotes' => 0,
        'orghalfvotes' => 0,
        'orgzerovotes' => 0,
        'cohfullvotes' => 0,
        'cohhalfvotes' => 0,
        'cohzerovotes' => 0,
    );

    $i = 0;

    /* update test results table; granularity: votes per answer or vote per answer per evaluator (ditloids test) */
    while (isset($_POST['index_' . $i])) {

        $did = $_POST['index_' . $i];

        $did1 = $did . '_1';
        $did2 = $did . '_2';
        
        $voteMap = array(
            'cohzerovotes' => 0,
            'cohhalfvotes' => 1,
            'cohfullvotes' => 2,
            'orgzerovotes' => 0,
            'orghalfvotes' => 1,
            'orgfullvotes' => 2,
        );

        if (isset($_POST[$did1])) {

            $param1 = "`".str_replace("`","``",$_POST[$did1])."`";
            $param2 = "`".str_replace("`","``",$_POST[$did2])."`";

            if ($ttype === 'ditloid') {
                $sql = "INSERT INTO `ditloid_results` (duid, user, evaluator, vote, popvote) VALUES (:did, :user, :evaluator, :vote, :popvote)";
                $stmt = $link->prepare($sql);
                $stmt->bindValue(':user',$user,PDO::PARAM_STR);
                $stmt->bindValue(':evaluator',$evaluator,PDO::PARAM_STR);
                $stmt->bindValue(':vote',$voteMap[$_POST[$did1]],PDO::PARAM_INT);
                $stmt->bindValue(':popvote',$voteMap[$_POST[$did2]],PDO::PARAM_INT);
            } elseif ($ttype === 'utest' || $ttype === 'ctest') {
                $sql = "UPDATE `test_results` SET {$param1} = {$param1} + 1, {$param2} = {$param2} + 1 WHERE resultid = :did AND user = :user AND lang = :lang";
                $stmt = $link->prepare($sql);
                $stmt->bindValue(':user',$user,PDO::PARAM_STR);
                $stmt->bindValue(':lang',$lang,PDO::PARAM_STR);
            } else {
                error_log("Wrong ttype");
                die("Wrong test type.");
            }

            $stmt->bindValue(':did',$did,PDO::PARAM_INT);
            if ($stmt->execute() === false) {

                error_log(
                    "Couldn't save evaluation on " . $ttype . " for user " . $user . PHP_EOL .
                    $sql . PHP_EOL .
                    var_export($link->errorInfo(), true)
                );

            }

            $votes[$_POST[$did1]]++;
            $votes[$_POST[$did2]]++;

        }

        $i++;

    }
    
    return $votes;
    
}

/**
 * @param \PDO $link
 * @param string $user
 * @param int $ttypeid
 * @param int $tresourceid
 * @param string $evaluator
 * @param string $lang
 * @param string $votes
 */
function update_test_evaluations($link, $user, $ttypeid, $tresourceid, $evaluator, $lang, $votes){

    /* update test_evaluations table; glanularity: votes per test per user per evaluator */
    $sql = "INSERT INTO `test_evaluations` VALUES (
            :user
          , :ttypeid
          , :tresourceid
          , :evaluator
          , :lang
          , :orgfullvotes
          , :orghalfvotes
          , :orgzerovotes
          , :cohfullvotes
          , :cohhalfvotes
          , :cohzerovotes
          ) ON DUPLICATE KEY UPDATE 
            orgfullvotes = orgfullvotes + :orgfullvotesagain
          , orghalfvotes = orghalfvotes + :orghalfvotesagain
          , orgzerovotes = orgzerovotes + :orgzerovotesagain
          , cohfullvotes = cohfullvotes + :cohfullvotesagain
          , cohhalfvotes = cohhalfvotes + :cohhalfvotesagain
          , cohzerovotes = cohzerovotes + :cohzerovotesagain";
    $stmt = $link->prepare($sql);
    $stmt->bindValue(':user',$user,PDO::PARAM_STR);
    $stmt->bindValue(':ttypeid',$ttypeid,PDO::PARAM_INT);
    $stmt->bindValue(':tresourceid',$tresourceid,PDO::PARAM_INT);
    $stmt->bindValue(':evaluator',$evaluator,PDO::PARAM_STR);
    $stmt->bindValue(':lang',$lang,PDO::PARAM_STR);
    $stmt->bindValue(':orgfullvotes',$votes['orgfullvotes'],PDO::PARAM_INT);
    $stmt->bindValue(':orghalfvotes',$votes['orghalfvotes'],PDO::PARAM_INT);
    $stmt->bindValue(':orgzerovotes',$votes['orgzerovotes'],PDO::PARAM_INT);
    $stmt->bindValue(':cohfullvotes',$votes['cohfullvotes'],PDO::PARAM_INT);
    $stmt->bindValue(':cohhalfvotes',$votes['cohhalfvotes'],PDO::PARAM_INT);
    $stmt->bindValue(':cohzerovotes',$votes['cohzerovotes'],PDO::PARAM_INT);
    $stmt->bindValue(':orgfullvotesagain',$votes['orgfullvotes'],PDO::PARAM_INT);
    $stmt->bindValue(':orghalfvotesagain',$votes['orghalfvotes'],PDO::PARAM_INT);
    $stmt->bindValue(':orgzerovotesagain',$votes['orgzerovotes'],PDO::PARAM_INT);
    $stmt->bindValue(':cohfullvotesagain',$votes['cohfullvotes'],PDO::PARAM_INT);
    $stmt->bindValue(':cohhalfvotesagain',$votes['cohhalfvotes'],PDO::PARAM_INT);
    $stmt->bindValue(':cohzerovotesagain',$votes['cohzerovotes'],PDO::PARAM_INT);

    if ($stmt->execute() === false) {

        error_log(
            "Couldn't save evaluation on test_evaluations" . PHP_EOL .
            $sql . PHP_EOL .
            var_export($link->errorInfo(), true)
        );

    }
    
}