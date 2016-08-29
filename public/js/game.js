/**
 * Created by max on 16年8月29日.
 */

var lastRoundEndAt = 0;

$(document).ready(function(){

    Date.prototype.dateDiff = function(interval,objDate){
        var dtEnd = new Date(objDate);
        if(isNaN(dtEnd)) return undefined;
        switch (interval) {
            case "s":return parseInt((dtEnd - this) / 1000);
            case "n":return parseInt((dtEnd - this) / 60000);
            case "h":return parseInt((dtEnd - this) / 3600000);
            case "d":return parseInt((dtEnd - this) / 86400000);
            case "w":return parseInt((dtEnd - this) / (86400000 * 7));
            case "m":return (dtEnd.getMonth()+1)+((dtEnd.getFullYear()-this.getFullYear())*12) - (this.getMonth()+1);
            case "y":return dtEnd.getFullYear() - this.getFullYear();
        }
    };

    function closeInput(){
        $('input').attr('disabled', 'disabled');
        $('button').attr('disabled', 'disabled');
        $('input').closest('label').addClass('disabled');
    }

    function openInput(){
        $('input').attr('disabled', false);
        $('button').attr('disabled', false);
        $('input').closest('label').removeClass('disabled');
    }

    function getLeftTime(datetime, interval){
        var now = new Date(datetime);
        var end = new Date(lastRoundEndAt);
        end.setSeconds(end.getSeconds() + interval);

        return now.dateDiff('s', end);
    }

    function clearRoundData(){
        $('div[id^=round]').each(function(){
            $(this).find('#startTime').text('0000-00-00 00:00:00');
            $(this).find('#endTime').text('0000-00-00 00:00:00');
            $(this).find('#roundCode').text('?');
        });
    }

    function resetAllPanelHighLight(){
        //reset all
        $('div[id^=round]').each(function(){
            $(this).removeClass('panel-danger');
            $(this).addClass('panel-success');
        });
    }

    function setRoundPanelHighLight(roundIndex){
        resetAllPanelHighLight();

        //set round
        $('#round' + roundIndex).removeClass('panel-success');
        $('#round' + roundIndex).addClass('panel-danger');
    }

    function getGameData(){
        $.ajax({
            url: '/gameData',
            type: 'post',
            error: function(xhr) {
                alert('Can not get game\'s data by ajax');
            },
            success: function(response) {
                //console.log(response);
                var gameObj = jQuery.parseJSON(response);

                if (gameObj.state != 0) {
                    clearRoundData();
                    var leftTime = getLeftTime(gameObj.now, gameObj.game_interval);
                    if(parseInt(leftTime) < 0 || lastRoundEndAt == 0){
                        $('#leftTime').text('The Game is not running');
                    } else {
                        resetAllPanelHighLight();
                        $('#leftTime').text('New game will start in ' + leftTime + " sec.");
                    }
                    closeInput();
                }

                openInput();
                $('#gamesNo').text(gameObj.no);
                $('#createTime').text(gameObj.create_at);
                $('#finalCode').text(gameObj.final_code);



                if(gameObj.round != null) {
                    var roundCount = gameObj.round.length-1;

                    var roundObj = gameObj.round[roundCount];
                    lastRoundEndAt = roundObj.end_at;

                    var now = new Date(gameObj.now);
                    var end = new Date(roundObj.end_at);

                    var leftTime = now.dateDiff('s', end);
                    if(roundCount < 4) {
                        if (parseInt(leftTime) < 0) {
                            closeInput();
                            leftTime = getLeftTime(now, gameObj.round_interval);
                            $('#leftTime').text('Round ' + (roundObj.round + 1) + ' will start in ' + leftTime + " sec.");
                        } else {
                            openInput();
                            setRoundPanelHighLight(roundObj.round);
                            $('#leftTime').text('Round ' + roundObj.round + ' will end in ' + leftTime + " sec.");
                        }
                    }

                    $.each( gameObj.round, function( key, roundObj ) {
                        var roundNo = roundObj.round;
                        var roundCode = roundObj.round_code;
                        if (roundCode == 0){
                            roundCode = '?';
                        }
                        $('#round' + roundNo + ' #startTime').text(roundObj.start_at);
                        $('#round' + roundNo + ' #endTime').text(roundObj.end_at);
                        $('#round' + roundNo + ' #roundCode').text(roundCode);
                    });
                }

                setTimeout(getGameData, 500);
            }
        });
    }

    clearRoundData();
    getGameData();
});
