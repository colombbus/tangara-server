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

function recordHistory(historyData) {
    window.history.pushState(historyData.data, historyData.title, historyData.url);        
}

function fetchContent(url, historyData) {
    var $element = $("#content");
    if (typeof historyData !== 'undefined') {
       $element.load(url, function() {
           recordHistory(historyData);
           ajaxify($(this));
   });
   } else {
       $element.load(url, function() {
           ajaxify($(this));
       });
   }
}

function discover(event) {
    event.preventDefault();
    $("#navigation-menu > li").removeClass("active");
    $("#discover").addClass("active");
    openContent();
    fetchContent(url_discover, {url:url_discover, data:{content:url_discover, active:'discover'}, title:'Tangara\n\
'});
}

function create(event) {
    event.preventDefault();
    $("#navigation-menu > li").removeClass("active");
    $("#create").addClass("active");
    closeContent();
    recordHistory({url:url_create, data:{active:'create'}, title:'Tangara'});
}

function share(event) {
    event.preventDefault();
    $("#navigation-menu > li").removeClass("active");
    $("#share").addClass("active");
    openContent();
    fetchContent(url_share, {url:url_share, data:{content:url_share, active:'share'}, title:'Tangara'});
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

function contentLink(event) {
    event.preventDefault();
    var $link = $(this);
    var url = $link.attr( "href" );
    openContent();
    fetchContent(url);
}

function contentForm(event) {
    event.preventDefault();
    var $form = $(this);
    var url = $form.attr( "action" );
    openContent();
    var posting = $.post(url, $form.serialize());
    posting.done(function( data ) {
        var $content = $("#content");
        $content.html(data);
        ajaxify($content);
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


