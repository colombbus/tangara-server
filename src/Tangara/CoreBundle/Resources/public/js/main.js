function updateLocal() {
    document.getElementById('local-frame').contentWindow.updateEnvironment();
}

function updateUserMenu() {
    $("#user-dropdown-trigger").addClass("loading");
    var $element = $("#user-menu");
    $element.load(url_user_menu, function() {
        $("#logout-link").click(logout);
        ajaxify($element);
        updateLocal();
    });
}

function openContent() {
    var $content = $("#content");
    $content.show();
    $content.animate({top:"0px"}, 600,function(){$('#local-frame').hide();});
}

function closeContent() {
    var $content = $("#content");
    var height = $content[0].scrollHeight;
    $('#local-frame').show();
    $content.animate({top:-height+"px"}, 600, function(){$(this).hide();});
}

function recordHistory(historyData) {
    if (typeof historyData.title === 'undefined') {
        historyData.title = "Tangara";
    }
    window.history.pushState(historyData.data, historyData.title, historyData.url);        
}

function fetchContent(url, data, title) {
    if (typeof data !== 'undefined') {
        historyData = {data:data, url:url};
        if (typeof data.content === 'undefined') {
            historyData.data.content = url;
        }
        if (typeof title !== 'undefined') {
            historyData.title = title;
        }
        recordHistory(historyData);
    }
    var $element = $("#content");
    $element.addClass("loading");
    $element.empty();
    $element.load(url, function() {
           ajaxify($(this));
           $element.removeClass("loading");
    });
}

function discover(event) {
    event.preventDefault();
    $("#navigation-menu > li").removeClass("active");
    $("#discover").addClass("active");
    openContent();
    fetchContent(url_discover, {active:'discover'});
}

function create(event) {
    event.preventDefault();
    $("#navigation-menu > li").removeClass("active");
    $("#create").addClass("active");
    closeContent();
    recordHistory({url:url_create, data:{active:'create'}});
}

function share(event) {
    event.preventDefault();
    $("#navigation-menu > li").removeClass("active");
    $("#share").addClass("active");
    openContent();
    fetchContent(url_share, {active:'share'});
}

function login(event) {
    event.preventDefault();
    var $form = $(this);
    var url = $form.attr( "action" );
    var posting = $.post(url, $form.serialize(), "json");
    $("#user-dropdown-trigger").addClass("loading");
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
    $("#user-dropdown-trigger").addClass("loading");
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

function contentLink(event) {
    event.preventDefault();
    var $link = $(this);
    var url = $link.attr("href");
    var active = $link.attr("data-active");
    var title = $link.attr("data-title");
    openContent();
    if (typeof active !== 'undefined') {
        fetchContent(url, {active:active}, title);
    } else {
        fetchContent(url);
    }
}

function contentForm(event) {
    event.preventDefault();
    var $form = $(this);
    var url = $form.attr( "action" );
    openContent();
    var $content = $("#content");
    $content.addClass("loading");
    $content.empty();
    var posting = $.post(url, $form.serialize());
    posting.done(function( data ) {
        $content.html(data);
        ajaxify($content);
        $content.removeClass("loading");
    });
}

function ajaxify(element) {
    if (typeof element !== 'undefined') {
        element.find("a.content-link").click(contentLink);
        element.find("form.content-form").submit(contentForm);
    } else {
        $("a.content-link").click(contentLink);
        $("form.content-form").submit(contentForm);
    }
}

window.onpopstate = function(event) {
    var state = event.state;
    if (state) {
        // set content
        if (typeof state.content !== 'undefined') {
            openContent();
            fetchContent(state.content);
        } else {
            closeContent();
        }
        // set active nav
        if (typeof state.active !== 'undefined') {
            $("#navigation-menu > li").removeClass("active").find("a").blur();
            $("#"+state.active).addClass("active");
        }
    }
};

$(function() {
    // bind menu links
    $("#logo").click(discover);
    $("#discover a").click(discover);
    $("#create a").click(create);
    $("#share a").click(share);
    // bind login form
    $("#login-form").submit(login);
    // bind logout link
    $("#logout-link").click(logout);
    // ajaxify links and forms
    ajaxify();
    // hide content if requested
    var $content = $("#content.hide-at-startup");
    if ($content.length > 0) {
        var height = $content[0].scrollHeight;
        $('#local-frame').show();
        $content.css('top',-height+"px");
    }
    // set current history record
    var data = {active:active_nav};
    if (content_url)
        data.content = content_url;
    window.history.replaceState(data, 'Tangara', document.URL);
});


