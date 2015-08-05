function getAnswersTable(orderby, orderbytype, user, typenames) {

    var xmlhttp
        , typenamesStr = ''
        , i = 0;

    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {// code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }

    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            if (xmlhttp.responseText) {
                document.getElementById("answersTableBody").innerHTML = xmlhttp.responseText;
            }
        }
    };

    while (typenames[i] != undefined) {
        i++;
        typenamesStr += '&type_' + i.toString() + '=' + typenames[i-1];
    }

    xmlhttp.open("POST", "stats/get_answers_table.php?user=" + user, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send("orderby=" + orderby + "&orderbytype=" + orderbytype + typenamesStr);

}