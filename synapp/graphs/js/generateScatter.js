/*global 
 d5:false,
 d6:false,
 d7:false,
 labelType1:false,
 labelType2:false,
 labelType3:false,
 scxaxlab:false,
 scyaxlab:false,
 scT1Ans:false,
 scT2Ans:false,
 scT3Ans:false,
 scatterData:false,
 user:false,
 bAdm:false,
 authuser:false,
 bAssociation:false 
 */
$(function () {
    var $placeholderscatter = $("#placeholderscatter"), previousPoint = null, data;
    /* create data from any combination d5 or d6 or d7 or d5, d6 or d6, d7 or d5, d7 or d5, d6, d7 */
    if (typeof d5 === 'undefined') {
        if (typeof d6 === 'undefined') {
            //only d7 
            data = [
                { data: d7, points: { symbol: "circle" }, label: labelType3 }
            ];
        } else {
            if (typeof d7 === 'undefined') {
                //only d6
                data = [
                    { data: d6, points: { symbol: "diamond" }, label: labelType2 }
                ];

            } else {
                //d6 and d7 
                data = [
                    { data: d6, points: { symbol: "diamond" }, label: labelType2 },
                    { data: d7, points: { symbol: "circle" }, label: labelType3 }
                ];
            }
        }
    } else {
        if (typeof d6 === 'undefined') {
            if (typeof d7 === 'undefined') {
                //only d5
                data = [
                    { data: d5, points: { symbol: "square" }, label: labelType1 }
                ];
            } else {
                //d5 and d7
                data = [
                    { data: d5, points: { symbol: "square" }, label: labelType1 },
                    { data: d7, points: { symbol: "circle" }, label: labelType3 }
                ];
            }
        } else {
            if (typeof d7 === 'undefined') {
                //d5 and d6
                data = [
                    { data: d5, points: { symbol: "square" }, label: labelType1 },
                    { data: d6, points: { symbol: "diamond" }, label: labelType2 }
                ];
            } else {
                //d5, d6 and d7
                data = [
                    { data: d5, points: { symbol: "square" }, label: labelType1 },
                    { data: d6, points: { symbol: "diamond" }, label: labelType2 },
                    { data: d7, points: { symbol: "circle" }, label: labelType3 }
                ];
            }
        }
    }
    $.plot($placeholderscatter, data, {
        series: { points: { show: true, radius: 3 } },
        grid: { hoverable: true, clickable: true },
        xaxis: { min: 0, max: 10, axisLabel: scxaxlab,
            axisLabelUseCanvas: true },
        yaxis: { min: 0, max: 10,
            axisLabel: scyaxlab,
            axisLabelUseCanvas: true }
    });

    function showTooltip(x, y, contents) {
        $('<div id="tooltip">' + contents + '</div>').css({
            position: 'absolute',
            display: 'none',
            top: y + 5,
            left: x + 5,
            border: '1px solid #fdd',
            padding: '2px',
            'background-color': '#fee',
            opacity: 0.80
        }).appendTo("body").fadeIn(200);
    }

    $placeholderscatter.bind("plothover", function (event, pos, item) {
        $("#x").text(pos.x.toFixed(2));
        $("#y").text(pos.y.toFixed(2));

        if (item) {
            if (previousPoint != item.dataIndex) {
                previousPoint = item.dataIndex;

                $("#tooltip").remove();
                var x = item.datapoint[0].toFixed(2),
                    y = item.datapoint[1].toFixed(2);
                var i = ((item.series.label == labelType1) ? 0 : ((item.series.label == labelType2) ? 1 : 2));
                var ansType = ((i == 0) ? (scT1Ans) : ((i == 1) ? scT2Ans : scT3Ans));
                showTooltip(item.pageX, item.pageY,
                    ansType + ": " + scatterData[i][item.dataIndex]['word'] + " " + scxaxlab + ": " + x + " " + scyaxlab + ": " + y);
            }
            document.body.style.cursor = 'pointer';
        }
        else {
            $("#tooltip").remove();
            previousPoint = null;
            document.body.style.cursor = 'default';
        }
    });

    $placeholderscatter.bind("plotclick", function (event, pos, item) {
        if (item && (bAdm || user.toLowerCase() === authuser.toLowerCase())) {
            var i = bAssociation?((item.series.label === labelType1) ? 0 : 1):((item.series.label === labelType1)? 0 : (item.series.label == labelType2?1:2)),
                detail;
            detail = window.open(
                (bAssociation?"show_association.phtml?user=":"show_answer.phtml?user=")
                    + encodeURIComponent(user)
                    + "&id1="
                    + encodeURIComponent(scatterData[i][item.dataIndex]['id1'])
                    + "&id2="
                    + encodeURIComponent(scatterData[i][item.dataIndex]['id2'])
                    + "&word="
                    + encodeURIComponent(scatterData[i][item.dataIndex]['word'])
                    + "&type="
                    + encodeURIComponent(scatterData[i][item.dataIndex].type),
                'STATS',
                'location=0,status=0,scrollbars=0,resizable=0,width=880,height=440'
            );
            setTimeout(detail.focus, 500);
        }
    });
    
});