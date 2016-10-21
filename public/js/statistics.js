$(document).ready(function () {
    var selectStartDate = $('select[name=startDate]');
    var selectEndDate = $('select[name=endDate]');
    var selectStartGamesNo = $('select[name=startGamesNo]');
    var selectEndGamesNo = $('select[name=endGamesNo]');

    getStatistics('all', 'all', 'first', 'last');
    getDateList(selectStartDate, selectStartGamesNo);
    getDateList(selectEndDate, selectEndGamesNo);

    $('#showAll').click(function(){
        getStatistics('all', 'all', 'first', 'last');
    });

    $('#today').click(function(){
        getStatistics('today', 'today', 'first', 'last');
    });

    $('#lastDate').click(function(){
        var date = $('input[name=lastDateValue]').val();
        getStatistics(date, date, 'first', 'last');
    });

    $('#nextDate').click(function(){
        var date = $('input[name=nextDateValue]').val();
        getStatistics(date, date, 'first', 'last');
    });

    $('#userChange').click(function(){
        var date = $('input[name=userChangeDate]').val();
        getStatistics(date, date, 'first', 'last');
    });

    $('#blockSearch').click(function(){
        var startDate = $(selectStartDate).val();
        var endDate = $(selectEndDate).val();
        var startGamesNo = $(selectStartGamesNo).val();
        var endGamesNo = $(selectEndGamesNo).val();

        if(startDate+startGamesNo > endDate+endGamesNo){
            showMsg("開始日+期數 不能大於 結束日+期數。");
        }else{
            getStatistics(startDate, endDate, startGamesNo, endGamesNo);
        }
    });

    $(selectStartDate).change(function(){
        var date = $(this).val();
        getGamesNoList(date, selectStartGamesNo);
    });

    $(selectEndDate).change(function(){
        var date = $(this).val();
        getGamesNoList(date, selectEndGamesNo);
    });

    function getStatistics(startDate, endDate, startGameNo, endGameNo){
        $.ajax({
            url: '/statistics_final_code/',
            type: 'post',
            data: { startDate: startDate, endDate: endDate, startGameNo: startGameNo, endGameNo: endGameNo},
            success: function (response) {
                var obj = jQuery.parseJSON( response );
                var numObjs = jQuery.parseJSON( obj.dataArray );
                var numDatas = numObjs.dataPoints;
                var biggestNum = -1;
                var smallerNum = 41;
                var biggestArray = [];
                var smallerArray = [];
                var avgArray = [];
                var avgTimes = parseInt(obj.no_total / 40, 10);

                $('#digitalContainer').html("");
                $.each(numDatas, function(key, value){
                    $('#digitalContainer').append(
                        "<div class='btn-default' id='digital_"+value.label+"'>" +
                        "<div>" + value.label + "</div>" +
                        "<div>" + value.y + "&nbsp;次<div>" +
                        "</div>"
                    );
                    if(value.y > biggestNum){
                        biggestArray = [];
                        biggestNum = value.y;
                    }
                    if(value.y == biggestNum){
                        biggestArray.push(value.label);
                    }

                    if(value.y < smallerNum){
                        smallerArray = [];
                        smallerNum = value.y;
                    }
                    if(value.y == smallerNum){
                        smallerArray.push(value.label);
                    }
                    if(value.y == avgTimes){
                        avgArray.push(value.label);
                    }

                });

                var x_interval = 1;
                if(biggestNum > 20){
                    x_interval = parseInt( biggestNum / 20, 10 );
                }

                var chart = new CanvasJS.Chart("chartContainer", {
                    theme: "theme2",
                    axisY:{
                        interval: x_interval,
                        lineThickness: 3
                    },
                    axisX:{
                        interval: 1,
                        lineThickness: 3
                    },
                    data: [jQuery.parseJSON( obj.dataArray )]
                });
                chart.render();

                $('#date_min').text(transferTimestamp(obj.date_min));
                $('#date_max').text(transferTimestamp(obj.date_max));
                $('#no_min').text(obj.no_min);
                $('#no_max').text(obj.no_max);
                $('#no_total').text(obj.no_total);
                $('input[name=lastDateValue]').val(obj.last_date);
                $('input[name=nextDateValue]').val(obj.next_date);
                $('input[name=userChangeDate]').val(obj.current_date);

                $.each(smallerArray, function(key, value){
                    $('#digital_' + value).addClass('btn-info');
                });
                $.each(biggestArray, function(key, value){
                    $('#digital_' + value).addClass('btn-danger');
                });
                $.each(avgArray, function(key, value){
                    $('#digital_' + value).removeClass('btn-default');
                    $('#digital_' + value).addClass('bg-success');
                });
            }
        });
    }

    function getDateList(element, noElement) {
        $.ajax({
            url: '/dateList/',
            type: 'post',
            success: function (response) {
                var objArray = jQuery.parseJSON( response );
                fillOption(element, objArray);

                var date = $(element).val();
                getGamesNoList(date, noElement);
            }
        });
    }

    function getGamesNoList(date, element){
        $.ajax({
            url: '/gamesNoList/',
            type: 'post',
            data: { date: date},
            success: function (response) {
                var objArray = jQuery.parseJSON( response );
                fillOption(element, objArray);
            }
        });
    }

    function fillOption(element, data_array){
        if(true){
            $(element).html("");
        }

        $.each(data_array, function(key, value){
            $(element).append("<option value='" + value + "'>" + value + "</option>");
        });
    }

});
