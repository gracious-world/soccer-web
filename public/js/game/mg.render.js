// 页面渲染中心
Mango.Render = (function() {
    var render = function(container, data, tmplObj, needClear, prependDom) {
        // 渲染模板
        var tmpl = $(tmplObj).html(),
            doTtmpl = doT.template(tmpl);
        var content = doTtmpl(data);
        if (needClear) {
            $(container).html('');
        }
        if (prependDom) {
            $(prependDom).before(content);
        } else {
            $(container).append(content);
        }

        // return $(content);
    };

    return {
        render: render
    }
})();
