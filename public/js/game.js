/**
 * Created by max on 16年8月29日.
 */

var lastRoundEndAt = 0;
var isRunning = true;

$(document).ready(function () {

    Date.prototype.dateDiff = function (interval, objDate) {
        var dtEnd = new Date(objDate);
        if (isNaN(dtEnd)) return undefined;
        switch (interval) {
            case "s":
                return parseInt((dtEnd - this) / 1000);
            case "n":
                return parseInt((dtEnd - this) / 60000);
            case "h":
                return parseInt((dtEnd - this) / 3600000);
            case "d":
                return parseInt((dtEnd - this) / 86400000);
            case "w":
                return parseInt((dtEnd - this) / (86400000 * 7));
            case "m":
                return (dtEnd.getMonth() + 1) + ((dtEnd.getFullYear() - this.getFullYear()) * 12) - (this.getMonth() + 1);
            case "y":
                return dtEnd.getFullYear() - this.getFullYear();
        }
    };

    function clearChoose(){
        //reset all radio (unchecked)
        $('input[type=radio]').attr('checked', false);

        //reset all label (removeClass "active")
        $('div[id*=Controller]').find("label").removeClass("active");

        //reset betAmount to init value (1000)
        $('input[name*=bet_]').val(1000);
    }

    function closeInput() {
        $('input').attr('disabled', 'disabled');
        $('button').attr('disabled', 'disabled');
        $('input').closest('label').addClass('disabled');
        clearChoose();
        resetAllPanelHighLight();
    }

    function openInput() {
        $('input').attr('disabled', false);
        $('button').attr('disabled', false);
        $('input').closest('label').removeClass('disabled');
    }

    function getLeftTime(datetime, interval) {
        var now = new Date(datetime);
        var end = new Date(lastRoundEndAt);
        end.setSeconds(end.getSeconds() + interval);

        return now.dateDiff('s', end);
    }

    function clearRoundData() {
        $('div[id^=round]').each(function () {
            $(this).find('#startTime').text('0000-00-00 00:00:00');
            $(this).find('#endTime').text('0000-00-00 00:00:00');
            $(this).find('#roundCode').text('?');
        });
    }

    function resetAllPanelHighLight() {
        //reset all
        $('div[id^=round]').each(function () {
            $(this).removeClass('panel-danger');
            $(this).addClass('panel-success');
        });
    }

    function setRoundPanelHighLight(roundIndex) {
        resetAllPanelHighLight();

        //set round
        $('#round' + roundIndex).removeClass('panel-success');
        $('#round' + roundIndex).addClass('panel-danger');
    }

    function openNumbersByRange(min, max){
        $('#numbersController').find('input[id^=num_]').attr('disabled', false);
        $('#numbersController').find('label').removeClass('disabled');
        var button_max = $('#numbersController').find('input[id^=num_]').length;
        for(var i = 1; i <= button_max; i++){
            if( i <= min || i >= max){
                $('#numbersController #num_'+i).attr('disabled', 'disabled');
                $('#numbersController #num_'+i).closest('label').addClass('disabled');
            }
        }
    }

    $('#btn_reset').click(function(){
        clearChoose();
    });

    function getGameData() {
        $.ajax({
            url: '/gameData',
            type: 'post',
            error: function (xhr) {
                $('#leftTime').text('Something wrong!! The game is not running!');
                isRunning = false;
                console.log(xhr);
            },
            success: function (response) {
                var isCanInput = true;
                var leftTime = -1;
                //console.log(response);
                var gameObj = jQuery.parseJSON(response);

                if (gameObj.state != 0) {
                    isCanInput = false;
                    leftTime = getLeftTime(gameObj.now, gameObj.game_interval);
                    $('#leftTime').text('New game will start in ' + leftTime + " sec.");
                    $('#finalCode').text(gameObj.final_code);
                } else {
                    $('#gamesNo').text(gameObj.no);
                    $('#finalCode').text('?');

                    if (gameObj.round != null) {
                        var roundCount = gameObj.round.length - 1;

                        var roundObj = gameObj.round[roundCount];
                        lastRoundEndAt = roundObj.end_at;

                        var now = new Date(gameObj.now);
                        var end = new Date(roundObj.end_at);

                        leftTime = now.dateDiff('s', end);
                        if (parseInt(leftTime) < 0) {
                            isCanInput = false;
                            leftTime = getLeftTime(now, gameObj.round_interval);
                            $('#leftTime').text('Round ' + (roundObj.round + 1) + ' will start in ' + leftTime + " sec.");
                        } else {
                            setRoundPanelHighLight(roundObj.round);
                            $('#leftTime').text('Round ' + roundObj.round + ' will end in ' + leftTime + " sec.");
                        }
                    }
                }

                clearRoundData();
                $.each(gameObj.round, function (key, roundObj) {
                    var roundNo = roundObj.round;
                    var roundCode = roundObj.round_code;
                    if (roundCode == 0) {
                        roundCode = '?';
                    }
                    $('#round' + roundNo + ' #startTime').text(roundObj.start_at);
                    $('#round' + roundNo + ' #endTime').text(roundObj.end_at);
                    $('#round' + roundNo + ' #roundCode').text(roundCode);
                });

                if(leftTime < 0){
                    $('#leftTime').text('The game is not running!');
                    isRunning = false;
                }

                if(isCanInput){
                    openInput();
                    openNumbersByRange(gameObj.current_min, gameObj.current_max);
                }else{
                    closeInput();
                }

                if(isRunning){
                    setTimeout(getGameData, 1000);
                }
            }
        });
    }

    function getFinalCode(game_no){
        $.ajax({
            url: '/finalcode/'+game_no,
            type: 'post',
            error: function (xhr) {
                console.log(xhr);
            },
            success: function (response) {

            }
        });
    }

    clearRoundData();
    getGameData();
});
