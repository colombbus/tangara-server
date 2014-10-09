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
    $("#content").load(url);
}

function discover() {
    $(".main-menu").removeClass("active");
    $("#discover").addClass("active");
    openContent();
    fetchContent(url_discover);
}

function create() {
    $(".main-menu").removeClass("active");
    $("#create").addClass("active");
    closeContent();
}

function share() {
    $(".main-menu").removeClass("active");
    $("#share").addClass("active");
    openContent();
    fetchContent(url_share);
}

function login(event) {
    event.preventDefault();
     // Get some values from elements on the page:
    var $form = $(this);
    /*var username = $("#username").val();
    var password = $("#password").val();
    var csrf = $("#csrf").val();*/
    var url = $form.attr( "action" );
    // Send the data using post
    var posting = $.post(url, $form.serialize(), "json");
    // Put the results in a div
    posting.done(function( data ) {
        if (typeof data.content !== 'undefined') {
            $("#user-menu").html(data.content);
        }
        if (typeof data.success !== 'undefined') {
            if (data.success) {
                updateLocal();
            }
        }
    });
}

$(function() {
    // bind menu links
    $("#logo").click(discover);
    $("#discover a").click(discover);
    $("#create a").click(create);
    $("#share a").click(share);
    // bind login form
    $("#login-form").submit(login);
});


