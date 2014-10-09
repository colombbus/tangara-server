function updateLocal() {
    document.getElementById('local-frame').contentWindow.updateEnvironment();
}

function openContent() {
    $("#content").animate({top:"0px"}, 600,function(){$('#local-frame').hide();});
}

function closeContent() {
    var $content = $("#content");
    var height = $content[0].scrollHeight;
    $('#local-frame').show();
    $content.animate({top:-height+"px"}, 600);
}

function fetchContent(url) {
    var $element = $("#content");
    $element.load(url);
    ajaxify($element);

}

function discover() {
    $("#navigation-menu > li").removeClass("active");
    $("#discover").addClass("active");
    openContent();
    fetchContent(url_discover);
}

function create() {
    $("#navigation-menu > li").removeClass("active");
    $("#create").addClass("active");
    closeContent();
}

function share() {
    $("#navigation-menu > li").removeClass("active");
    $("#share").addClass("active");
    openContent();
    fetchContent(url_share);
}

function login(event) {
    event.preventDefault();
    var $form = $(this);
    var url = $form.attr( "action" );
    var posting = $.post(url, $form.serialize(), "json");
    posting.done(function( data ) {
        if (typeof data.content !== 'undefined') {
            var $element = $("#user-menu");
            $element.html(data.content);
            $("#login-form").submit(login);
            $("#logout-link").click(logout);
            ajaxify($element);
        }
        if (typeof data.success !== 'undefined') {
            if (data.success) {
                updateLocal();
            }
        }
    });
}

function logout(event) {
    event.preventDefault();
    var $link = $(this);
    var url = $link.attr( "href" );
    // TODO: check if project dirty first
    var getting = $.get(url, "json");
    getting.done(function( data ) {
        if (typeof data.content !== 'undefined') {
            var $element = $("#user-menu");
            $element.html(data.content);
            $("#login-form").submit(login);
            $("#logout-link").click(logout);
            ajaxify($element);
        }
        if (typeof data.success !== 'undefined') {
            if (data.success) {
                updateLocal();
            }
        }
    });
}

function content(event) {
    event.preventDefault();
    var $link = $(this);
    var url = $link.attr( "href" );
    openContent();
    fetchContent(url);
}

function ajaxify(element) {
    if (typeof element !== 'undefined') {
        element.find(".content-link").click(content);
    } else {
        $(".content-link").click(content);
    }
}

$(function() {
    // bind menu links
    $("#logo").click(discover);
    $("#discover a").click(discover);
    $("#create a").click(create);
    $("#share a").click(share);
    // bind login form
    $("#login-form").submit(login);
    $("#logout-link").click(logout);
    ajaxify();
});


