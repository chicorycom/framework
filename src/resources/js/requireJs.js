/**
 * Created by assane on 10/08/16.
 */

$.when(
        $.getScript( "/js/bootstrap.min.js"),
        $.getScript( "/js/bootstrap/js/bootstrap-select.js"),
        $.getScript( "/js/admin.js"),
        $.getScript( "/js/bootstrap/js/fileinput.js"),
        $.getScript( "/js/jquery.notyfy/js/jquery.notyfy.js"),
        $.getScript( "/js/jquery/plugins/jquery.validate.js"),
        $.getScript( "/js/gritter/js/jquery.gritter.min.js"),
    $.Deferred(function( deferred ){
        $( deferred.resolve );
    })
).done(()=>{
        //Select all anchor tag with rel set to tooltip
        $('a[rel=tooltip]').mouseover(function(e) {

            //Grab the title attribute's value and assign it to a variable
            var tip = $(this).attr('title');

            //Remove the title attribute's to avoid the native tooltip from the browser
            $(this).attr('title','');

            //Append the tooltip template and its value
            $(this).append('<div id="tooltip"><div class="tipHeader"></div><div class="tipBody">' + tip + '</div><div class="tipFooter"></div></div>');

            //Show the tooltip with faceIn effect
            $('#tooltip').fadeIn('500');
            $('#tooltip').fadeTo('10',0.9);

        }).mousemove(function(e) {
            //Keep changing the X and Y axis for the tooltip, thus, the tooltip move along with the mouse
            $('#tooltip').css('top', e.pageY + -100 );
            $('#tooltip').css('left', e.pageX + -200 );
        }).mouseout(function() {
            //Put back the title attribute's value
            $(this).attr('title',$('.tipBody').html());
            //Remove the appended tooltip template
            $(this).children('div#tooltip').remove();

        });
});
