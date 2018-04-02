/*ajax拉数据的util*/
if (!window.Mango) {
    Mango = {};
}
Mango.UtilData = (function() {
    return (function() {
        var mask = new UIscript.Mask();
        var message = new UIscript.MiniWindow();
        return {
            getData: function(url, callback, param, type) {
                var me = this;
                if (!url) return false;
                $.ajax({
                    url: url,
                    type: type || 'GET',
                    dataType: 'json',
                    data: param,
                    beforeSend: function() {
                        mask.show();

                    },
                    success: function(d) {
                        if (d.coding < 0) {
                            if (confirm('获取游戏数据失败，是否返回首页？')) {
                                window.location.href = '/';
                            }
                        }else{
                            callback && callback(d, param);
                        }
                        message.hideTip();
                    },
                    complete: function() {
                        message.showTip('正在加载数据');
                        setTimeout(function() {
                            message.hideTip();
                            mask.hide();
                        }, 1000);
                    },
                    error: function(d) {

                    }
                });
            }
        }
    })();
})();