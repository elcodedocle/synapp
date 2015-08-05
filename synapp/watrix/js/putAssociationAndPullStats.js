/*global errAnswerTooLong*/
var bReentry = false;
function putAssociationAndPullStats(dabuttonval) {
    if (document.getElementById("answer").value.length > 64) {
        alert(errAnswerTooLong);
        return false;
    }
    if (bReentry) window.location.reload(true);
    var xmlhttp;
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {// code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            if (xmlhttp.responseText) {
                document.getElementById("results").innerHTML = xmlhttp.responseText;
                document.getElementById("dabutton").value = dabuttonval;
                bReentry = true;
            } else window.location.reload(true);
        }
    };

    xmlhttp.open("POST", "watrix/put_association_and_pull_stats.php", true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("ticket=" + document.getElementById("ticket").value + "&id1=" + document.getElementById("id1").value
        + "&id2=" + document.getElementById("id2").value + "&input=" + document.getElementById("answer").value
        + "&type=" + document.getElementById("type").value);

    return true;
}

/* Pressing return on a textnode clicks "dabutton" */
function stopRKey(evt) {
    var node;
    evt = (evt) ? evt : ((event) ? event : null);
    node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
    if ((evt.keyCode == 13) && (node.type == "text")) {
        document.getElementById("dabutton").onclick();
    }
}
document.onkeypress = stopRKey;

/* Only for flash mode */
function switchPair() {
    document.getElementById("hr1").style.display = "none";
    document.getElementById("img1").style.display = "none";
    document.getElementById("hr2").style.display = "inline";
    document.getElementById("img2").style.display = "inline";
    document.getElementById("formdiv").style.display = "inline";
    document.getElementById("answer").focus();
}