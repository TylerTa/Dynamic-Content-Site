/**
 * Created by Tyler on 4/16/2017.
 */

$(document).ready(function(){

    $(".content_detail").hide();

    $(".content_title_dropDown").click(function(){
        $(this).next(".content_detail").slideToggle('slow');
    });

});


