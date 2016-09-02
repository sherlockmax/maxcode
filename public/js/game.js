/**
 * Created by max on 2016/8/31.
 */
$(document).ready(function () {

    //define element to use
    var element_game_no = $('#gamesNo');
    var element_final_code = $('#finalCode');
    var element_state = $('#gameState');

    var element_all_round = $('div[id^=round]');

    var element_all_radio = $('input');
    var element_all_label = $('input').closest('label');
    var element_all_button = $('button');

    $('.roundTimes').hide();
    blockAllInput();
    getGameData();
    showBetHistory();

    $('#btn_reset').click(function(){
        clearChoose();
    });

    element_all_round.click(function(){
        $('.roundTimes').slideToggle("slow");
    });

    function pad (str, max) {
        str = str.toString();
        return str.length < max ? pad("0" + str, max) : str;
    }

    function transferTimestamp(timestamp){
        var date = new Date(timestamp*1000);
        var y = pad(date.getFullYear(), 4);
        var m = pad(date.getMonth()+1, 2);
        var d = pad(date.getDate(), 2);
        var H = pad(date.getHours(), 2);
        var i = pad(date.getMinutes(), 2);
        var s = pad(date.getSeconds(), 2);

        return y + "-" + m + "-" + d + " " + H + ":" + i + ":" + s;
    }

    function blockAllInput(){
        element_all_radio.attr('disabled', 'disabled');
        element_all_button.attr('disabled', 'disabled');
        element_all_label.addClass('disabled');
    }

    function unblockAllInput(){
        element_all_radio.attr('disabled', false);
        element_all_button.attr('disabled', false);
        element_all_label.removeClass('disabled');
    }

    function resetAllPanelHighLight() {
        //reset all
        element_all_round.each(function () {
            $(this).removeClass('panel-danger');
            $(this).addClass('panel-success');
        });
    }

    function setRoundPanelHighLight(roundIndex) {
        resetAllPanelHighLight();

        $('#round' + roundIndex).removeClass('panel-success');
        $('#round' + roundIndex).addClass('panel-danger');
    }

    function clearRoundData() {
        element_all_round.each(function () {
            $(this).find('#startTime').text('0000-00-00 00:00:00');
            $(this).find('#endTime').text('0000-00-00 00:00:00');
            $(this).find('#roundCode').text('?');
        });

        element_final_code.text('?');
    }

    function clearChoose(){
        //reset all radio (unchecked)
        $('input[type=radio]').attr('checked', false);

        //reset all label (removeClass "active")
        $('div[id*=Controller]').find("label").removeClass("active");

        //reset betAmount to init value (1000)
        $('input[name*=bet_]').val(0);
    }

    function openNumbersByRange(min, max){
        $('#numbersController').find('input[id^=num_]').attr('disabled', false);
        $('#numbersController').find('label').removeClass('disabled');
        var button_max = $('#numbersController').find('input[id^=num_]').length;
        for(var i = 1; i <= button_max; i++){
            if( i < min || i > max ){
                $('#numbersController #num_'+i).attr('disabled', 'disabled');
                $('#numbersController #num_'+i).closest('label').addClass('disabled');
            }
        }
    }

    function setTimer(seconds, msg, next_step_function){
        if(seconds >= 0){
            seconds--;
            var new_msg = msg.replace("{sec}", seconds + 1);
            element_state.text(new_msg);
            setTimeout(function() {
                setTimer(seconds, msg, next_step_function);
            }, 1000);
        }else{
            next_step_function();
        }
    }

    function showFinalCode(games_no){
        $.ajax({
            url: '/finalCode/'+games_no,
            type: 'post',
            error: function (xhr) {
                console.log(xhr);
                setTimeout(function(){showFinalCode(games_no);}, 1000);
            },
            success: function (response) {
                //console.log(response);
                element_final_code.text(response);
            }
        });
    }

    function billing(type){
        if(type == 'round'){
            $.ajax({
                url: '/billingRound',
                type: 'get',
                success: function (response) {
                    showBetHistory();
                }
            });
        }else{
            $.ajax({
                url: '/billingGame',
                type: 'get',
                success: function (response) {
                    showBetHistory();
                }
            });
        }
    }

    function showBetHistory(){
        $.ajax({
            url: '/betHistory',
            type: 'post',
            error: function (xhr) {
                console.log(xhr);
            },
            success: function (response) {
                //console.log(response);
                $('#bet_history_box').html("");
                var bet_detail = jQuery.parseJSON(response);
                $('#userCash').text(bet_detail.cash);
                $.each(bet_detail, function(key, bet){
                    if(key != 'cash') {
                        var bet_detail_box = $('#bet_details_ex').clone();
                        bet_detail_box.removeAttr('id');
                        if (bet.win_cash == 0) {
                            bet_detail_box.find('div:first()').addClass('bg-success');
                        }
                        if (bet.win_cash > 0) {
                            bet_detail_box.find('div:first()').addClass('bg-info');
                        }
                        if (bet.win_cash < 0) {
                            bet_detail_box.find('div:first()').addClass('bg-danger');
                        }

                        bet_detail_box.find('#games_no').text(bet.games_no);
                        bet_detail_box.find('#round').text('Round ' + bet.round);
                        bet_detail_box.find('#bet').text('$ ' + bet.bet);
                        bet_detail_box.find('#win_cash').text('$ ' + bet.win_cash);
                        if (bet.part == 1) {
                            bet_detail_box.find('#code').text(bet.round_code);
                        } else {
                            bet_detail_box.find('#code').text('[' + bet.final_code + ']');
                        }
                        if (bet.part == 1) {
                            if (bet.guess % 2 == 0) {
                                bet_detail_box.find('#guess').text('Even');
                            } else {
                                bet_detail_box.find('#guess').text('Odd');
                            }
                        } else {
                            bet_detail_box.find('#guess').text(bet.guess);
                        }

                        $('#bet_history_box').append(bet_detail_box);
                        bet_detail_box.show();
                    }
                });
            }
        });
    }

    function getGameData(){
        $.ajax({
            url: '/gameData',
            type: 'post',
            error: function (xhr) {
                console.log(xhr);
            },
            success: function (response) {
                var gameObj = jQuery.parseJSON(response);
                //console.log(response);

                if(gameObj.timer > -1) {
                    element_game_no.text(gameObj.no);
                    $('input[name=games_no]').val(gameObj.no);

                    $.each(gameObj.round, function (key, roundObj) {
                        var roundNo = roundObj.round;
                        var roundCode = roundObj.round_code;

                        if(roundNo == 1 && roundCode == 0){
                            clearRoundData();
                        }

                        if (roundCode != 0) {
                            resetAllPanelHighLight();
                            $('#round' + roundNo + ' #roundCode').text(roundCode);
                        }
                        if(roundCode == 0){
                            setRoundPanelHighLight(roundNo);
                            $('input[name=round_no]').val(roundNo);
                        }

                        var start_at = transferTimestamp(roundObj.start_at);
                        var end_at = transferTimestamp(roundObj.end_at);
                        $('#round' + roundNo + ' #startTime').text(start_at);
                        $('#round' + roundNo + ' #endTime').text(end_at);

                        if(gameObj.msg.indexOf('will end in') >= 0){
                            unblockAllInput();
                            if(roundObj.round_code == 0){
                                openNumbersByRange(roundObj.current_min, roundObj.current_max);
                            }
                        }
                    });

                    if(gameObj.msg.indexOf('New game will start in') >= 0){
                        billing('game');
                        showFinalCode(gameObj.no);

                    }

                    if(gameObj.msg.indexOf('New game will start in') >= 0 ||
                        gameObj.msg.indexOf('will start in') >= 0){
                        blockAllInput();
                        clearChoose();
                        billing('round');
                    }

                    $('#odds_numbers').text(gameObj.odds.numbers);
                    $('input[name=odds_numbers]').val(gameObj.odds.numbers);
                    $('#odds_odd').text(gameObj.odds.odd);
                    $('input[name=odds_odd]').val(gameObj.odds.odd);
                    $('#odds_even').text(gameObj.odds.even);
                    $('input[name=odds_even]').val(gameObj.odds.even);

                    setTimer(gameObj.timer, gameObj.msg, getGameData);
                }else{
                    element_state.text('The game is not running!');
                }
            }
        });
    }
});