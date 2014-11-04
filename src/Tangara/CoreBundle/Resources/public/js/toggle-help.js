var helpVisible = false;
var $localFrame, $helpFrame;


function openHelp() {
    $helpFrame.stop().show().animate({
                  'left': '0'
                  }, 200);
    $localFrame.stop().animate({
            'padding-left': '265px'
            },200);
    helpVisible = true;
}

function closeHelp() {
    $helpFrame.stop().animate({
            left: '-265px'
            }, 200, function(){$('#help-frame').hide();});
    $localFrame.animate({
            'padding-left': '0px'
            },200);
    helpVisible = false;
}

function hideHelp() {
    if (helpVisible) {
        $helpFrame.hide();
    }
}

function showHelp() {
    if (helpVisible) {
        $helpFrame.show();
    }
}

function toggleHelp() {
    if (helpVisible) {
        closeHelp();
    } else {
        openHelp();
    }
}


$(function() {
    $localFrame = $('#local-frame');
    $helpFrame = $('#help-frame');
    $helpFrame.hide();
});