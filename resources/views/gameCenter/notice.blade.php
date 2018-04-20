<style type="text/css">
.notice-content li{
    float:left;
    margin-right:30px;
}
.color-highlight{
    color:#e80000;
}
.notice-block{
    background:#000;
    width:100%;
}
.opaque {
  -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)";
  filter: alpha(opacity=50);
  opacity: 0.5;
}

.notice-title{
    text-align:center;
    width:70px;
    background-color:#f2f2f2;
    color:#f00;
    position:absolute;
    left:0;
    top:0;
}
.notice-content{
    height:100%;
    width:910px;
    padding-left:10px;
    overflow:hidden;
    position:absolute;
    left:70px;
    top:0;
}
.noticeWrap{width:9999px;}
.notice-content li{
    width:280px;
    overflow:hidden;
    white-space:nowrap;
    text-overflow: ellipsis;
}

.notice-wrap{
    position:relative;
    height:38px;
    line-height:38px;
    color:#fff;
    margin:0 0 10px 0;
}
.notice-wrap a{
    color:#fff;
}
.notice-list p{
    line-height:22px;
    margin:0;
    text-align:left;
}

</style>
<div class="notice-wrap">
    <div class="notice-block opaque">&nbsp;</div>
    <div class="notice-title">赛事公告</div>
    <div class="notice-content">
        <ul class="clearfix noticeWrap"></ul>
    </div>
</div>
<script id="gamesNotice" type="text/x-dot-template">
    <div class="notice-list">
        @{{ for(var i=0,len=it.length;i<len;i++){ }}
            <p>@{{=it[i] }}</p>
        @{{ } }}
    </div>
</script>
<script id="noticeItem" type="text/x-dot-template">
    @{{ for(var i=0,len=it.length;i<len;i++){ }}
    <li><a href="javascript:void(0)">@{{=i+1 }}. @{{=it[i].text}}</a></li>
    @{{ } }}
</script>

<script type="text/javascript">
    $(function() {
        var controller = (function() {
            return {
                moveBlock: $(".noticeWrap"),
                moveId: null,
                index: 1,
                setp:1,
                datas: null,
                init: function() {
                    this.pullData();
                    this.events();
                },
                events: function() {
                    var me = this;
                    var noticeWin = new UIscript.Message();
                    var noticeTmpl = $('#gamesNotice').html();
                    var doTtmpl = doT.template(noticeTmpl);
                    //点击赛事公告，显示赛事详情
                    $(document).on('click', '.notice-content a', function() {
                        var datas = [this.innerHTML];
                        var content = doTtmpl(datas);
                        noticeWin.show({
                            isShowMask: true,
                            title: '赛事公告',
                            confirmIsShow: true,
                            confirmText: '关 闭',
                            confirmFun: function() {
                                noticeWin.hide();
                            },
                            content: content
                        });
                    });

                    $(".notice-content").on("mouseover", function() {
                        clearInterval(me.moveId);
                    }).on("mouseout", function() {
                        me.move();
                    });
                },
                render: function(datas, domSelector) {
                    var noticeItem = $('#noticeItem').html();
                    var doTtmpl = doT.template(noticeItem);
                    var content = doTtmpl(datas);
                    $(domSelector).html(content);
                    this.datas = datas;
                    this.move();
                },
                move: function() {

                    var me = this;
                    var oneLi = 310;
                    if (!me.datas) return;
                    if (me.datas.length <= 3) return;
                    var length = me.datas.length;
                    var times = Math.ceil(length / me.setp);

                    me.moveId = setInterval(function() {
                        if (me.index == times - 2) {
                            me.index = 1;
                             me.moveBlock.css({
                                'margin-left':oneLi+'px'
                             });
                            me.moveBlock.animate({
                                'margin-left': 0
                            }, 2000, function() {

                            });
                        }else{
                            me.moveBlock.animate({
                                'margin-left': '-=' + (me.setp * oneLi)
                            }, 2000, function() {
                                me.index++;
                            });
                        }

                    }, 7000);
                },
                pullData: function() {
                    var me = this;
                    $.ajax({
                        url: "{!! route('notices.latest') !!}",
                        type: 'GET',
                        dataType: 'json',
                        data: {},
                        beforeSend: function() {},
                        success: function(res) {
                            if(res.length > 0){
                                me.render(res,".noticeWrap");
                            }else{
                            $('.noticeWrap').html('<li>暂无赛公告</li>');
                            }

                        },
                        complete: function() {

                        },
                        error: function(d) {
                            me.pullData();
                        }

                    });
                }

            };
        })();

        // controller.init();


    });
</script>

