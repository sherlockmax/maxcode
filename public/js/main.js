/**
 * Created by max on 16年8月24日.
 */

$(document).ready(function(){
    $('input[type=radio]').click(function(){
        var element_name = $(this).attr("name");
        var radioId = "num_" + $(this).val();

        //reset all radio (unchecked)
        $('input[name='+element_name+']').attr('checked', false);
        //reset all label (removeClass "active")
        $('#'+element_name+'Controller').find("label").removeClass("active");

        $('#' + radioId).attr('checked', true);
        $('#' + radioId).parent("label").addClass("active");
    });

    $('input[type=number]').keydown( function(e) {
        return false;
    });

    $('.fa-btn').each(function(){
        $(this).addClass('fa-lg');
    });

    $('input[name*=bet_]').change(function(){
        var maxCash = parseInt($('#userCash').text());
        var bet1 = parseInt($('input[name=bet_part1]').val());
        var bet2 = parseInt($('input[name=bet_part2]').val());

        if(bet1 + bet2 > maxCash){
            var otherBet = bet1;
            if($(this).attr('name') != 'bet_part2'){
                otherBet = bet2;
            }

            $(this).val( maxCash - otherBet );
        }
    });
});