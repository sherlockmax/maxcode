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

    $('div[id^=money_keyboard_]').hide();
    blockAllInput();
    getGameData();
    showBetHistory();

    $('#choose_all').click(function(){
        $('#numbersController input[type=checkbox]').each(function(){
            if(!$(this).attr('disabled'))
            {
                $(this).attr('checked', 'checked');
                $(this).closest('label').addClass('active');
            }
        });
    });

    $('#btn_reset').click(function () {
        clearChoose();
    });

    $('div[id^=show_keyboard_]').click(function(){
        if(!$('input[name^=bet_]').attr("disabled")) {
            var key = $(this).attr('id').split('_')[2];
            $('#money_keyboard_' + key).slideDown();
        }
    });

    $('#btn_close_big_winner_card').click(function () {
        $('#big_winner_card').hide();
    });

    $('div[id^=money_keyboard_] button').click(function(){
        var key = $(this).parent().parent().attr('id').split('_')[2];
        var action = $(this).text();
        if(action == 'OK'){
            $('#money_keyboard_'+key).slideUp();
        }

        if(action == 'Del'){
            $('input[name=bet_part'+key+']').val(parseInt(0));
        }

        if(action >= 0 && action <= 9){
            var tmp = $('input[name=bet_part'+key+']').val();

            $('input[name=bet_part'+key+']').val(parseInt( tmp + action ));
        }
    });

    function pad(str, max) {
        str = str.toString();
        return str.length < max ? pad("0" + str, max) : str;
    }

    function transferTimestamp(timestamp) {
        var date = new Date(timestamp * 1000);
        var y = pad(date.getFullYear(), 4);
        var m = pad(date.getMonth() + 1, 2);
        var d = pad(date.getDate(), 2);
        var H = pad(date.getHours(), 2);
        var i = pad(date.getMinutes(), 2);
        var s = pad(date.getSeconds(), 2);

        return y + "-" + m + "-" + d + " " + H + ":" + i + ":" + s;
    }

    function blockAllInput() {
        element_all_radio.attr('disabled', 'disabled');
        element_all_button.attr('disabled', 'disabled');
        element_all_label.addClass('disabled');

        $('div[id^=money_keyboard_]').hide();

        $('#btn_close_big_winner_card').attr('disabled', false);
    }

    function unblockAllInput() {
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

    function clearChoose() {
        //reset all radio (unchecked)
        $('input[type=radio]').attr('checked', false);

        //reset all checkbox (unchecked)
        $('input[type=checkbox]').attr('checked', false);

        //reset all label (removeClass "active")
        $('div[id*=Controller]').find("label").removeClass("active");

        //reset betAmount to init value (1000)
        $('input[name*=bet_]').val(0);
    }

    function openNumbersByRange(min, max) {
        $('#numbersController').find('input[id^=num_]').attr('disabled', false);
        $('#numbersController').find('label').removeClass('disabled');
        var button_max = $('#numbersController').find('input[id^=num_]').length;
        for (var i = 1; i <= button_max; i++) {
            if (i < min || i > max) {
                $('#numbersController #num_' + i).attr('disabled', 'disabled');
                $('#numbersController #num_' + i).closest('label').addClass('disabled');
            }
        }
    }

    function setTimer(seconds, msg, next_step_function) {
        if (seconds > 0) {
            var new_msg = msg.replace("{sec}", seconds--);
            element_state.html(new_msg);
            setTimeout(function () {
                setTimer(seconds, msg, next_step_function);
            }, 1000);
        } else {
            next_step_function();
        }
    }

    function showFinalCode(games_no) {
        $.ajax({
            url: '/finalCode/' + games_no,
            type: 'post',
            error: function (xhr) {
                console.log(xhr);
                setTimeout(function () {
                    showFinalCode(games_no);
                }, 1000);
            },
            success: function (response) {
                var datas = jQuery.parseJSON(response);
                element_final_code.text(datas.final_code);

                if (datas.big_winner != '?') {
                    $('#big_winner_name').text(datas.big_winner.name);
                    $('#big_winner_no').text(datas.big_winner.games_no);
                    $('#big_winner_win_cash').text(datas.big_winner.win_cash);

                    $('#big_winner_card').show('pulsate', 600);
                }
            }
        });
    }

    function showBetHistory() {
        $.ajax({
            url: '/betHistory/' + $('input[name=games_no]').val(),
            type: 'post',
            error: function (xhr) {
                console.log(xhr);
            },
            success: function (response) {
                //console.log(response);
                $('#bet_history_part_1 #bet_history_box').html("");
                $('#bet_history_part_2 #bet_history_box').html("");
                var bet_detail = jQuery.parseJSON(response);
                var bet_count_part1 = 0;
                var bet_count_part2 = 0;
                $('#userCash').text(bet_detail.cash);
                $.each(bet_detail, function (key, bet) {
                    if (key != 'cash' && bet.games_no == $('form').find('input[name=games_no]').val()) {
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

                        bet_detail_box.find('#round').text(bet.round);
                        bet_detail_box.find('#bet').text('$ ' + bet.bet);
                        bet_detail_box.find('#win_cash').text('$ ' + bet.win_cash);
                        bet_detail_box.find('#odds').text(bet.odds);
                        if (bet.part == 1) {
                            bet_count_part1++;
                            if (bet.guess % 2 == 0) {
                                bet_detail_box.find('#guess').text('雙');
                            } else {
                                bet_detail_box.find('#guess').text('單');
                            }
                            var round_code = '??';
                            if (bet.round_code != 0) {
                                round_code = bet.round_code;
                            }
                            bet_detail_box.find('#code').text('[' + round_code + ']');

                            $('#bet_history_part_1 #bet_history_box').append(bet_detail_box);
                        }

                        if (bet.part == 2) {
                            bet_count_part2++;
                            bet_detail_box.find('#guess').text(pad(bet.guess, 2));
                            bet_detail_box.find('#code').text(pad('[' + bet.final_code + ']', 2));
                            $('#bet_history_part_2 #bet_history_box').append(bet_detail_box);
                        }

                        bet_detail_box.show();
                    }
                });
                if (bet_count_part1 <= 0) {
                    $('#bet_history_part_1 #bet_history_box').html("您尚未對本期遊戲進行下注");
                }
                if (bet_count_part2 <= 0) {
                    $('#bet_history_part_2 #bet_history_box').html("您尚未對本期遊戲進行下注");
                }
            }
        });
    }

    function getGameData() {
        $.ajax({
            url: '/gameData',
            type: 'post',
            error: function (xhr) {
                BootstrapDialog.alert('無法與伺服器取得連接。');
                console.log(xhr);
            },
            success: function (response) {
                var gameObj = jQuery.parseJSON(response);
                //console.log(response);
                if (gameObj.timer > -1) {
                    element_game_no.text(gameObj.no);
                    $('input[name=games_no]').val(gameObj.no);
                    $('span#games_no').text(gameObj.no);

                    $.each(gameObj.round, function (key, roundObj) {
                        var roundNo = roundObj.round;
                        var roundCode = roundObj.round_code;

                        if (roundNo == 1 && roundCode == 0) {
                            clearRoundData();
                        }

                        if (roundCode != 0) {
                            resetAllPanelHighLight();
                            $('#round' + roundNo + ' #roundCode').text(roundCode);
                        }
                        if (roundCode == 0) {
                            setRoundPanelHighLight(roundNo);
                            $('input[name=round_no]').val(roundNo);
                        }

                        var start_at = transferTimestamp(roundObj.start_at);
                        var end_at = transferTimestamp(roundObj.end_at);
                        $('#round' + roundNo + ' #startTime').text(start_at);
                        $('#round' + roundNo + ' #endTime').text(end_at);

                        if (gameObj.msg.indexOf('結束下注') >= 0) {
                            unblockAllInput();
                            if (roundObj.round_code == 0) {
                                openNumbersByRange(roundObj.current_min, roundObj.current_max);
                            }
                        }
                    });

                    if (gameObj.msg.indexOf('新的一期') >= 0) {
                        showFinalCode(gameObj.no);
                    }

                    if (gameObj.msg.indexOf('新的一期') >= 0 ||
                        gameObj.msg.indexOf('開放下注') >= 0) {
                        blockAllInput();
                        clearChoose();
                    }

                    $('#odds_numbers').text(gameObj.odds.numbers);
                    $('input[name=odds_numbers]').val(gameObj.odds.numbers);
                    $('#odds_odd').text(gameObj.odds.odd);
                    $('input[name=odds_odd]').val(gameObj.odds.odd);
                    $('#odds_even').text(gameObj.odds.even);
                    $('input[name=odds_even]').val(gameObj.odds.even);

                    setTimer(gameObj.timer, gameObj.msg, getGameData);
                } else {
                    element_state.text('維護中，暫不提供服務。');
                }

                showBetHistory();
            }
        });
    }
});