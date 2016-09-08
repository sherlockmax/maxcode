/**
 * Created by max on 16年8月24日.
 */

$(document).ready(function () {

    function autoReplace(str) {
        return str.replace("]", "").replace("[", "");
    }

    $('#btn_games_no_search').click(function(){
        location.href = '/record/' + $('#input_game_no_search').val();
    });

    $('#btn_games_no_last').click(function(){
        location.href = '/record/' + $('#input_games_no_last').val();
    });

    $('#btn_games_no_next').click(function(){
        location.href = '/record/' + $('#input_games_no_next').val();
    });

    $('div[id^=round]').click(function () {
        $('.roundTimes').slideToggle("slow");
    });

    $('.roundTimes').hide();

    $('input[type=radio]').click(function () {
        var element_name = autoReplace($(this).attr("name"));
        var radioId = "num_" + $(this).val();
        if (element_name != 'numbers') {
            radioId = "numType_" + $(this).val();
        }

        //reset all radio (unchecked)
        $('input[name=' + element_name + ']').attr('checked', false);
        //reset all label (removeClass "active")
        $('#' + element_name + 'Controller').find("label").removeClass("active");

        $('#' + radioId).attr('checked', true);
        $('#' + radioId).parent("label").addClass("active");
    });

    $('input[type=checkbox]').click(function () {
        var element_name = autoReplace($(this).attr("name"));
        var checkboxId = "num_" + $(this).val();

        if (element_name != 'numbers') {
            checkboxId = "numType_" + $(this).val();
        }

        if ($('#' + checkboxId)[0].checked) {
            $('#' + checkboxId).attr('checked', true);
            $('#' + checkboxId).parent("label").addClass("active");
        } else {
            $('#' + checkboxId).attr('checked', false);
            $('#' + checkboxId).parent("label").removeClass("active");
        }
    });

    $('input[type=number]').keydown(function (e) {
        return false;
    });

    $('.fa-btn').each(function () {
        $(this).addClass('fa-lg');
    });

    $('input[name*=bet_]').change(function () {
        var maxCash = parseInt($('#userCash').text());
        var bet1 = parseInt($('input[name=bet_part1]').val());
        var bet2 = parseInt($('input[name=bet_part2]').val());

        if (bet1 + bet2 > maxCash) {
            var otherBet = bet1;
            if ($(this).attr('name') != 'bet_part2') {
                otherBet = bet2;
            }

            $(this).val(maxCash - otherBet);
        }
    });
});