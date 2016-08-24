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

    $('#btn_reset').click(function(){
        //reset all radio (unchecked)
        $('input[type=radio]').attr('checked', false);
        //reset all label (removeClass "active")
        $('div[id*=Controller]').find("label").removeClass("active");
    });

    $('input[type=number]').keydown( function(e) {
        return false;
    });
});