function updateLocal() {
    document.getElementById('local-frame').contentWindow.updateEnvironment();
}

function openContent() {
    $('#content').slideDown('slow',function(){$('#local-frame').hide();});
}

function closeContent() {
    $('#local-frame').show();
    $('#content').slideUp('slow');
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
        if (typeof data.success !== 'undefined') {
            if (data.success) {
                // TODO update nav bar
                window.alert("ok");
                updateLocal();
            } else {
                // TODO display error
                window.alert("nok")
            }
        }
    });
}

$(function() {
    // hide with jquery so that display:block is cached
    $("#local-frame ").hide();
    // bind menu links
    $("#logo").click(discover);
    $("#discover a").click(discover);
    $("#create a").click(create);
    $("#share a").click(share);
    // bind login form
    $("#login-form").submit(login);
    // start with discover
    discover();
});


