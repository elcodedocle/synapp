/*global 
 $:false, 
 d1:false, 
 d2:false, 
 d3:false, 
 d4:false, 
 recognitionOthersLab:false, 
 originalityOthersLab:false, 
 recognitionYouLab:false, 
 originalityYouLab:false 
 */
$(function () {
    var data = [
            {
                data: d2,
                bars: {
                    show: true,
                    steps: true,
                    fill: true,
                    barWidth: 24 * 60 * 60 * 1000
                },
                label: recognitionOthersLab
            },
            {
                data: d1,
                bars: {
                    show: true,
                    steps: true,
                    fill: true,
                    barWidth: 24 * 60 * 60 * 1000
                },
                label: originalityOthersLab
            },
            {
                data: d4,
                curvedLines: {
                    show: true
                },
                label: recognitionYouLab
            },
            {
                data: d3,
                curvedLines: {
                    show: true
                },
                label: originalityYouLab
            }
        ],
        dataoverview = [
            {
                data: d2,
                bars: {
                    show: true,
                    steps: true,
                    fill: true,
                    barWidth: 24 * 60 * 60 * 1000
                }
            },
            {
                data: d1,
                bars: {
                    show: true,
                    steps: true,
                    fill: true,
                    barWidth: 24 * 60 * 60 * 1000
                }
            },
            {
                data: d4,
                curvedLines: {
                    show: true
                }
            },
            {
                data: d3,
                curvedLines: {
                    show: true
                }
            }
        ];
    // helper for returning the weekends in a period
    function weekendAreas(axes) {
        var markings = [];
        var d = new Date(axes.xaxis.min);
        // go to the first Saturday
        d.setUTCDate(d.getUTCDate() - ((d.getUTCDay() + 1) % 7));
        d.setUTCSeconds(0);
        d.setUTCMinutes(0);
        d.setUTCHours(0);
        var i = d.getTime();
        do {
            // when we don't set yaxis, the rectangle automatically
            // extends to infinity upwards and downwards
            markings.push({ xaxis: { from: i, to: i + 2 * 24 * 60 * 60 * 1000 } });
            i += 7 * 24 * 60 * 60 * 1000;
        } while (i < axes.xaxis.max);

        return markings;
    }

    var options = {
        series: {
            curvedLines: {
                active: true
            }
        },
        xaxis: { mode: "time", tickLength: 5},
        yaxis: { min: -10, max: +10 },
        selection: { mode: "x" },
        grid: { markings: weekendAreas }
    };

    var $placeholder = $("#placeholder"),
        $overview = $("#overview"),
        plot = $.plot(
            $placeholder,
            data,
            options
        ),
        overview = $.plot(
            $overview,
            dataoverview,
            {
                series: {
                    curvedLines: {
                        active: true
                    },
                    shadowSize: 0
                },
                xaxis: { ticks: [], mode: "time" },
                yaxis: { ticks: [], autoscaleMargin: 0.1 },
                selection: { mode: "x" }
            }
        );

    // now connect the two

    $placeholder.bind("plotselected", function (event, ranges) {
        // do the zooming
        plot = $.plot($("#placeholder"), data,
            $.extend(true, {}, options, {
                xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
            }));

        // don't fire event on the overview to prevent eternal loop
        overview.setSelection(ranges, true);
    });

    $overview.bind("plotselected", function (event, ranges) {
        plot.setSelection(ranges);
    });
});