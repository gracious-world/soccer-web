function doCombinations(e,a){var t=[];return function n(e,a,s){if(0==s)return t.push(e);for(var i=0,c=a.length;c-s>=i;i++)n(e.concat(a[i]),a.slice(i+1),s-1)}([],e,a),t}var calculateBetsNum=function(e,a,t,n){var s=[],i=0,c=(a.length,n.length),o=[];n.join("^");if(!a.length)return 0;for(var d in t)$.merge(o,t[d]);if(c>0)for(var r=0,l=a.length;l>r;r++)for(var u=0;c>u;u++)a[r]==n[u]&&(a.splice(r,1),r--);for(var r=o.length-1;r>=0;r--){var d=o[r]-c;a.length==d?s.push(a):s=s.concat(combine(a,d))}for(var h=[],r=s.length-1;r>=0;r--){var f=s[r];h.push(f.concat(n))}for(var r=h.length-1;r>=0;r--){for(var v=h[r],m=1,u=v.length-1;u>=0;u--)m*=e[v[u]].length;i+=m}return i},combine=function(e,a){var t=[];return function n(e,a,s){if(0==s)return t.push(e);for(var i=0,c=a.length;c-s>=i;i++)n(e.concat(a[i]),a.slice(i+1),s-1)}([],e,a),t};Object.keys||(Object.keys=function(e){var a,t=[];for(a in e)Object.prototype.hasOwnProperty.call(e,a)&&t.push(a);return t}),Array.prototype.max=function(){return Math.max.apply(null,this)},Array.prototype.min=function(){return Math.min.apply(null,this)},Array.prototype.contains=function(e){for(var a=this.length;a--;)if(this[a]===e)return!0;return!1},Array.prototype.each=function(e){e=e||Function.K;for(var a=[],t=Array.prototype.slice.call(arguments,1),n=0;n<this.length;n++){var s=e.apply(this,[this[n],n].concat(t));null!=s&&a.push(s)}return a},Array.prototype.uniquelize=function(){for(var e=new Array,a=0;a<this.length;a++)e.contains(this[a])||e.push(this[a]);return e},Array.complement=function(e,a){return Array.minus(Array.union(e,a),Array.intersect(e,a))},Array.intersect=function(e,a){return e.uniquelize().each(function(e){return a.contains(e)?e:null})},Array.minus=function(e,a){return e.uniquelize().each(function(e){return a.contains(e)?null:e})},Array.union=function(e,a){return e.concat(a).uniquelize()},sortNumber=function(e,a){return e-a},Function.prototype.bind||(Function.prototype.bind=function(e){if("function"!=typeof this)throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");var a=Array.prototype.slice.call(arguments,1),t=this,n=function(){},s=function(){return t.apply(this instanceof n?this:e,a.concat(Array.prototype.slice.call(arguments)))};return this.prototype&&(n.prototype=this.prototype),s.prototype=new n,s}),Mango=window.Mango||{},Mango.Config=function(){return{gameType:"football",containers:{gamesFilter:$("#wayFilter"),games:$("#gamesContainer"),betting:$("#submitContainer"),champion:$(".e_main")},events:{},gameConfigUrl:"/game-config",gameDataUrl:"/game-data",gameBettingUrl:"/betting",oddParam:{},oddBlockClass:{h:"blockWin",ch:"blockWin",d:"blockPing",cd:"blockPing",a:"blockFu",ca:"blockFu",other:"blockYel"},scriptTmpl:{gamesList:$("#gamesList"),gamesHistoryList:$("#gamesHistoryList"),selectedGames:$("#selectedGames"),selectedOdds:$("#selectedOdds"),gatesBox:$("#gatesBox"),gameForm:$("#gameForm"),gamesMenu:$("#focusGamesMenu"),champion:$("#championTmpl"),championship:$("#championshipTmpl")},gameConfig:null}}(),Mango.Data=function(){var e=3e5,a=null,t={game_type:"football"},n=null,s=!0,i=null,c=new UIscript.Mask,o=new UIscript.MiniWindow,d={implant:!1,callback:null},r=function(e,a,t,n){return e?void $.ajax({url:e,type:"GET",dataType:"json",data:t,beforeSend:function(){c.show()},success:function(e){a(e,t),o.hideTip()},complete:function(){o.showTip("正在加载数据"),setTimeout(function(){o.hideTip(),c.hide()},1e3)},error:function(s){r(e,a,t,n)}}):!1},l=function(){r(a.gameConfigUrl,u,t)},u=function(e,a){e.coding<0&&dospecile&&confirm("获取游戏数据失败，是否返回首页？")&&(window.location.href="/"),Mango.Config.gameConfig=e.data,Mango.Config.gameConfig.defaultWays=Array.minus(Mango.Config.gameConfig.ways,Mango.Config.gameConfig.extra_ways),h()},h=function(e){n=e,r(a.gameDataUrl,f,$.extend({},t,n||{}))},f=function(e,t){e.coding<0?d.implant?d.implant&&d.callback&&d.callback():confirm("获取游戏数据失败，是否返回首页？")&&(window.location.href="/"):d.implant&&d.implant&&d.restore&&d.restore();var n=moment().format("YYYY-MM-DD");s=!t||!t.bet_date||t.bet_date>=n,s?v():m();var i=+isFocusGame?"gamesList":["gamesList","gamesHistoryList"][Number(t.bet_date<n)];a.callback(a.gamesContainer,e.data,i,!0)},v=function(){m(),i=setInterval(function(){h(n)},e)},m=function(){i&&clearInterval(i)};return{init:function(e){return a=$.extend(a,null,e||{}),l(),this},getGameData:h,set:function(e,a){switch(e){case"implant":$.extend(d,a)}}}}(),Mango.Render=function(){var e=function(e,a,t,n,s){var i=$(t).html(),c=doT.template(i),o=c(a);n&&$(e).html(""),s?$(s).before(o):$(e).append(o)};return{render:e}}(),Mango.Game=function(){function e(e){for(var a={},t=!0,n=0,s=e.length;s>n;n++)a[e[n].game]&&(t=!1),a[e[n].game]=!0;return t}function a(e,a,t){var n=e[a];e[a]=e[t],e[t]=n}function n(e){var t,n,s,i=e.length;for(t=0;i>t;t++)for(n=0,s=i-t;s>n;n++)try{+e[n].odd<+e[n+1].odd&&a(e,n,n+1)}catch(c){}return e}var s={},i={},c=8,o=[],d=[],r=0,l={},u=[],h=0,f=[],v=null,m=!1,g="mixdisabled",p=2e4,C=!0,b=function(){$(s.gamesListBox).on("click",function(e){var a=e.target||e.srcElement,t=$(a).attr("action_type");switch(t||(a=$(a).parent(),t=$(a).attr("action_type")),t||e.stopPropagation(),t){case"gamesShowSwitcher":x(a);break;case"moreOddsSwitcher":B(a);break;case"moreOddsSwitcherBig":_(a);break;case"chooseOdd":k(a)}})},x=function(e){var e=$(e),a=e.attr("bet_date"),t=s.gamesListBox.find("dl[gamedate="+a+"]");t.hasClass("closeData")?(t.removeClass("closeData"),e.find("font").text("隐藏")):(t.addClass("closeData"),e.find("font").text("显示"))},y=function(){$(s.selectedGamesBox).on("click",function(e){var a=e.target||e.srcElement,t=$(a).attr("action_type");switch(t||(a=$(a).parent(),t=$(a).attr("action_type")),t||e.stopPropagation(),t){case"cancelOdd":T(a);break;case"cancelGame":L(a);break;case"danSettingEvent":w(a)}})},w=function(e){var a=$(e).parent().parent().attr("game_bn");if($(e).hasClass("icoDanDis"))return!1;if($(e).hasClass("icoDanSele")){$(e).removeClass("icoDanSele");var t=$.inArray(a,f);t>-1&&f.splice(t,1)}else $(e).addClass("icoDanSele"),f.push(a);N(),j()},B=function(e){var e=$(e),a=e.attr("game"),t=$(s.gamesListBox).find("dd.analyMore[game="+a+"]");t.hasClass("hide")?(e.addClass("closeBtn").html('隐藏<i class="arrow"></i>'),t.removeClass("hide")):(t.find("td[action_type=chooseOdd]").hasClass("active")?e.addClass("openBtnSele"):e.removeClass("openBtnSele"),e.removeClass("closeBtn").html('展开<i class="arrow"></i>'),t.addClass("hide"))},_=function(e){var e=$(e),a=e.attr("game"),t=$(s.gamesListBox).find("dd.analyMore[game="+a+"]");if(t.hasClass("hide"))e.parents("span.moreOptBtnBox").addClass("openBtn").end().find(".imitateSelect").html("隐藏比赛投注区"),t.removeClass("hide");else{var n=s.gamesListBox.find("dd[game="+a+"]").find("td[action_type=chooseOdd]").hasClass("active");n?e.addClass("active"):e.removeClass("active"),e.parents("span.moreOptBtnBox").removeClass("openBtn").end().find(".imitateSelect").html("展开比赛投注区"),t.addClass("hide")}},k=function(e){var a=$(e).parents("dd"),t=$(a).attr("game"),n=$(e).attr("index"),i=n.split("_")[1],c=$(e).hasClass("active");if(c){var o=$(s.selectedGamesBox).find("a[action_type=cancelOdd][index="+n+"]");G(e,o,t,i)}else M(a,e)},M=function(e,a){var t=$(e).attr("game"),n=+$(e).attr("game_index"),c=$.parseJSON($(e).attr("data")),o=$(".scrollMoni"),d=null;c.gameIndexNum=n;var r=o.jScrollPane().data("jsp");i[t]||(""!=s.selectedGamesBox.html()&&s.selectedGamesBox.find("tr[game_index]").each(function(e){return $(this).attr("game_index")>n?(d=this,!1):void 0}),i[t]={},Mango.Render.render(s.selectedGamesBox,{data:c,available:C},Mango.Config.scriptTmpl.selectedGames,!1,d),r.reinitialise(),r.scrollToY(parseInt(60))),$.isEmptyObject(i)||$(s.selectedGamesContainer).removeClass("unselect").addClass("select");var l=s.selectedGamesBox.find("tr.gameOption[game_bn="+t+"]").attr("game_index");r.scrollToY(parseInt(60*l)),D(a,t)},G=function(e,a,t,n){var c=$(".scrollMoni"),o=c.jScrollPane().data("jsp"),d=$(a).parent().parent();$(e).removeClass("active"),$(s.gamesListBox).find("dd.analyMore[game="+t+"]").find("td[action_type=chooseOdd]").hasClass("active")?$(s.gamesListBox).find('[action_type="moreOddsSwitcher"][game="'+t+'"]').addClass("openBtnSele"):$(s.gamesListBox).find('[action_type="moreOddsSwitcher"][game="'+t+'"]').removeClass("openBtnSele"),$(a).unbind("click").remove(),delete i[t][n],$.isEmptyObject(i[t])&&(delete i[t],$(d).siblings("tr.gameTitle[game_bn="+t+"]").unbind("click").remove(),$(d).unbind("click").remove(),o.reinitialise(),S(t)),V()},D=function(e,a){var t=$.parseJSON($(e).attr("data")),n=$(e).attr("index"),c=t.name,o=+$(e).attr("single");t.index=n,t.single=o;var d=$(s.gamesListBox).find("[action_type=chooseOdd][index="+n+"]");if(!i[a][c]){i[a][c]=t.odd;var r=s.selectedGamesBox.find("tr.gameOption[game_bn="+a+"] > td.betList");Mango.Render.render(r,t,Mango.Config.scriptTmpl.selectedOdds);var l=r.find('a[index="'+t.index+'"]'),u=$(l).clone().css({position:"absolute",display:"block",zIndex:100,left:$(e).offset().left,top:$(e).offset().top,opacity:1}).appendTo("body");l.hide();var h={left:r.offset().left,top:r.offset().top,opacity:0};$(u).animate(h,function(){l.show(),$(u).remove()}),d.addClass("active"),V()}},L=function(e){var a=$(e).attr("index");S(a)},S=function(e){var a=$.inArray(e,f);a>-1&&f.splice(a,1),s.selectedGamesBox.find(".gameOption[game_bn="+e+"] > .betList").find("a").each(function(e){T(this)}),V()},T=function(e){var a=$(e).attr("index"),t=a.split("_"),n=t[0],i=t[1],c=$(s.gamesListBox).find("[action_type=chooseOdd][index="+a+"]");G(c,e,n,i)},O=function(){$(s.gatesContainer).on("click",function(e){var a=e.target||e.srcElement,t=$(a).attr("action_type");switch(t||(a=$(a).parent(),t=$(a).attr("action_type")),t||e.stopPropagation(),t){case"gateEvent":P(a);break;case"gateSwitcher":z(a)}l=U(),u=Object.keys(l),N(),u.length>0?s.confirmContainer.removeClass("unselect"):s.confirmContainer.addClass("unselect"),j()})},j=function(){l=U(),o=Object.keys(i),d=A(),r=calculateBetsNum(d,o,l,f),F(),s.confirmContainer.find(".gameNum").text(Object.keys(i).length).end().find(".selectedBets").text(r).end(),I(),ae()},F=function(e){if(r>p){var a=new UIscript.Message;if(a.show({isShowMask:!0,confirmIsShow:!0,confirmText:"关 闭",confirmFun:function(){a.hide()},content:"投注单数请不要大于20000"}),e)return!0}},I=function(e){var a=e||s.confirmContainer.find(".select-game-statics-multiple").find(".choose-input").val(),t=r*a*2;s.confirmContainer.find(".amount").text(t)},A=function(){var e={};for(var a in i){e[a]=[];for(var t in i[a])e[a].push(t+"_"+i[a][t])}return e},U=function(){var e={};return s.gatesContainer.find(".guoguanList.active").find("li.active").each(function(){var a=$(this).attr("gate"),t=$(this).attr("combination"),n=t?t.split(","):null;e[a]=n,h=Math.max(h,n.min())-1}),e},N=function(){var e=s.selectedGamesBox.find("tr.gameTitle[game_bn]").length-1;s.gatesContainer.find(".jtip[action_type=gateEvent]").each(function(a){var t=$(this).attr("gate"),n=$(this).attr("max_dan");if(n<f.length){$(this).addClass("disabled").removeClass("active").find("i").addClass("icoFx_disable").removeClass("active");var s=$.inArray(t,u);s>-1&&u.splice(s,1)}else $(this).removeClass("disabled").find("i").removeClass("icoFx_disable");$(this).hasClass("active")&&(e=Math.min(e,n))}),f.length==e?(s.selectedGamesBox.find(".icoDan").each(function(){$(this).hasClass("icoDanSele")||$(this).addClass("icoDanDis")}),C=!1):(s.selectedGamesBox.find(".icoDan").each(function(){$(this).hasClass("icoDanSele")||$(this).removeClass("icoDanDis")}),C=!0)},z=function(e){var a=$(".scrollMoniGuoguanList"),t=$(e).attr("gate_type"),n=s.gatesContainer.find(".guoguanList[gate_type="+t+"]");$(e).addClass("active").siblings("li.active").removeClass("active"),"m_n"===t?(a.siblings(".guoguanList").find("li,i").removeClass("active").removeClass(g),a.siblings(".guoguanList").removeClass("active").removeClass(g).hide(),n.addClass("active"),a.show(),a.jScrollPane().data("jsp")):(a.find(".guoguanList").find("li,i").removeClass("active"),a.find(".guoguanList").removeClass("active"),a.hide(),n.addClass("active").show()),V()},P=function(e){var a=$(e).find("i");if(!$(e).hasClass(g)&&!$(e).find("i").hasClass("icoFx_disable")){$(e).hasClass("active")?($(e).removeClass("active"),$(a).removeClass("active")):($(a).hasClass("icoDx")&&$(e).siblings("li").removeClass("active").find("i.active").removeClass("active"),$(e).addClass("active"),$(a).addClass("active"));var t=s.gatesContainer.find(".guoguanTab li:eq(0)").hasClass("active")?!0:!1,n=s.gatesContainer.find(".guoguanList[gate_type=m_1] li:eq(0)"),i=n.hasClass("active")?!0:!1,c=s.gatesContainer.find(".guoguanList[gate_type=m_1] li:gt(0)"),o=s.gatesContainer.find(".guoguanList[gate_type=m_1] li.active").not(n).size();m&&t&&(i?c.addClass(g):(c.removeClass(g),o>=1?n.addClass(g):n.removeClass(g)))}},V=function(){c=8,l={},o=Object.keys(i);var e=q(),a=o.length,t=!1,n=Math.min(+betType||c,a);1==a?t=e:a>1&&(t=!0);var d=s.gatesContainer.find(".guoguanList[gate_type=m_1]"),r=s.gatesContainer.find(".guoguanList[gate_type=m_n]");if(t){s.gatesContainer.hasClass("unselect")&&s.gatesContainer.removeClass("unselect").addClass("select");var u=W(n,e),h=u.freeGatesData.data.length-d.find("li").size(),f=u.combinGatesData.data.length-r.find("li").size(),v=$(Mango.Config.scriptTmpl.gatesBox).html(),g=doT.template(v),p="1x1"===u.freeGatesData.data[0].identity?!0:!1,C=/单关/g.test($(".guoguanList.active").html());if(p){if(0>=h||!C)Mango.Render.render(d,u.freeGatesData,Mango.Config.scriptTmpl.gatesBox,!0);else if(h>0){u.freeGatesData.data=u.freeGatesData.data.slice(0).splice(d.find("li").size(),h);var b=g(u.freeGatesData);d.append(b)}m=!0}else{if(C||0>h)Mango.Render.render(d,u.freeGatesData,Mango.Config.scriptTmpl.gatesBox,!0);else if(h>0){u.freeGatesData.data=u.freeGatesData.data.slice(0).splice(d.find("li").size(),h);var b=g(u.freeGatesData);d.append(b)}m=!1}if(0!==f)if(0>f)0!==u.combinGatesData.data.length?Mango.Render.render(r,u.combinGatesData,Mango.Config.scriptTmpl.gatesBox,!0):r.html("");else{u.combinGatesData.data=u.combinGatesData.data.slice(0).splice(r.find("li").size(),f);var b=g(u.combinGatesData);r.append(b)}}else d.html(""),r.html(""),s.gatesContainer.removeClass("select").addClass("unselect");j(),s.gatesContainer.hasClass("unselect")?$(s.confirmContainer).removeClass("select").addClass("unselect"):$(s.confirmContainer).removeClass("unselect").addClass("select"),$(".guoguanTab li:eq(1)").hasClass("active")&&$(".scrollMoniGuoguanList").jScrollPane().data("jsp"),N()},W=function(e,a){var t=Mango.Config.gameConfig.gate_map.free.slice(0,e-1);+a&&t.unshift(Mango.Config.gameConfig.gate_map.single[0]);var n={gate_type:0,data:1>=e?[Mango.Config.gameConfig.gate_map.single[0]]:t},s={gate_type:1,data:E(e)};return{freeGatesData:n,combinGatesData:s}},E=function(e){return $(Mango.Config.gameConfig.gate_map.combin).map(function(){return this.identity.substring(0,1)<=e?this:void 0})},q=function(){var e=1;return s.selectedGamesBox.find(".betList > a").each(function(a){var t=$(this).attr("way"),n=+$(this).attr("single");n||(e=n),c=Math.min(c,Mango.Config.gameConfig.max_gates[t])}),e},H=function(){s.confirmContainer.find("#multipleSelector");v=function(){var e={config:{btnSub:".J-select-sub",btnAdd:".J-select-add",realDom:"#multipleSelector"},setValue:function(e){$(this.config.realDom).val(e)},getValue:function(){var e=this,a=$(e.config.realDom).val();return a},add:function(){var e=this,a=Number(e.getValue());9999>a?e.setValue(a+1):e.setValue(a)},sub:function(){var e=this,a=Number(e.getValue());1>=a?e.setValue(1):e.setValue(a-1)}};return e},$(".J-select-add").click(function(){v().add();var e=v().getValue();$(".gameMultiple").html(e),ae(),I(e)}),$(".J-select-sub").click(function(){v().sub();var e=v().getValue();$(".gameMultiple").html(e),ae(),I(e)}),$("#multipleSelector").on("keydown",function(e){if(35!=e.keyCode&&36!=e.keyCode&&46!=e.keyCode&&8!=e.keyCode&&37!=e.keyCode&&39!=e.keyCode){if(!(e.keyCode>=48&&e.keyCode<=57||e.keyCode>=96&&e.keyCode<=105))return!1;if($.trim($(this).val()).length>6)return!1}}).on("keyup",function(){var e=parseInt($.trim(this.value));e>999999&&(this.value=e=999999),$(".gameMultiple").html(e),ae(),I(e)}),$(s.confirmContainer).click(function(e){return $(this).hasClass("unselect")?!1:void 0}),$(s.confirmContainer).find("a.clearBetting").click(function(e){Q()}).end().find(".submitBtn").click(function(e){return F(!0)?!1:void K()})},J=function(e){var a=new UIscript.MiniWindow,t=new UIscript.Message;$.ajax({url:s.billSubmitUrl,type:"POST",dataType:"json",data:e||{},beforeSend:function(){a.showTip("提交中...")},success:function(e){Y(e,a,t),v().setValue(10),$(".gameMultiple").html(10)},complete:function(e){a.hideTip()},error:function(e){R(e,t)}})},Y=function(e,a,t){a.hideTip();var n='<div class="bd text-center"><div class="pop-title"><i class="ico-error"></i><h4 class="pop-text">'+(e&&e.msg?e.msg:"")+"<br></h4></div></div>";+e.coding>0&&(n='<div class="bd text-center"><div class="pop-title"><i class="ico-success"></i><h4 class="pop-text">'+e.msg+'<br></h4></div><p class="text-note" style="padding:5px 0;">您可以通过”投注记录“查询您的投注记录！</p></div>',Q()),t.show({isShowMask:!0,confirmIsShow:!0,confirmText:"关 闭",confirmFun:function(){t.hide()},content:n})},R=function(e,a){a.hide();var t="服务器信息错误,请在投注列表中查询";401==e.status&&(t="您没有登录或没有投注权限"),a.show({isShowMask:!0,confirmIsShow:!0,confirmText:"关 闭",confirmFun:function(){a.hide()},content:'<div class="pop-waring"><i class="ico-waring"></i><h4 class="pop-text">'+t+"</h4></div>"})},K=function(){var e=new UIscript.Mask,a=new UIscript.MiniWindow,t="",n=!0;if(l=U(),u=Object.keys(l),o.length<1&&(t="缺少赛事数据",n=!1),u.length<1&&(t="缺少过关方式数据",n=!1),!n)return e.show(),a.showTip(t),setTimeout(function(){e.hide(),a.hideTip()},1e3),!1;var i=s.confirmContainer.find(".gameMultiple").html(),h=r*i*2,v={max_gate:c,gate:u,games:d,dan:f,bet_num:r,multiple:i,amount:h},m=new UIscript.Message({cls:"w-12 game-form"}),g=$("tbody.selectedGamesBox").clone().html(),p=$("div.methodContent"),C="";p.find("li").each(function(e,a){$(this).hasClass("active")?C+=$(this).html():""});var b=['<div class="submitBox">','<div class="tableBox">',"<table>",'<colgroup><col width="65"><col><col width="20"><col><col width="35"></colgroup>','<thead><tr><th>场次</th><th colspan="3">主队　VS　客队</th><th>胆</th></tr></thead>',"<tbody>",g,"</tbody>","</table>","</div>",'<div class="init">',"<h3>过关方式</h3><p>"+C+"</p>","<h3>投注信息</h3>",'<p class="text">',"注数：共<font>"+v.bet_num+"</font>注<br/>","倍数：<font>"+v.multiple+"</font>倍<br/>","总金额：<font>"+v.amount+"</font>元","</p>","</div>",'<div style="clear:both"></div>',"</div>"];m.show({isShowMask:!0,confirmIsShow:!0,cancelIsShow:!0,confirmText:"确 认",cancelText:"取 消",title:"订单详细",cancelFun:function(){m.hide()},confirmFun:function(){m.hide(),J(v)},content:b.join("")})},Q=function(){i={},c=8,o=[],d=[],r=0,l={},u=[],h=0,f=[],C=!0,s.selectedGamesBox.html(""),s.selectedGamesContainer.addClass("unselect"),s.gatesContainer.addClass("unselect").find(".guoguanList").html(""),s.confirmContainer.addClass("unselect"),j(),s.gamesListBox.find("[action_type=chooseOdd].active").removeClass("active"),s.gamesListBox.find(".openBtn.openBtnSele").removeClass("openBtnSele")},X=function(){$(window).scroll(function(e){setTimeout(function(){if($(window).scrollTop()>320){if($(s.gamesContainer).find("dl.dataHead").css({position:"fixed",top:"0px"}),$(window).height()>$(s.submitContainer).height()-20){var e=$(".docBody").width()/2-$("#submitContainer").width();$(s.gamesContainer).find("#submitContainer").css({position:"fixed",top:"0px",left:"50%","margin-left":e+"px"})}$(s.gamesContainer).find("div.dataHeadBg").show()}else $(s.gamesContainer).find("dl.dataHead").css({position:"relative",top:"auto"}),$(s.gamesContainer).find("#submitContainer").css({position:"relative",top:"auto",left:"auto","margin-left":"0px"}),$(s.gamesContainer).find("div.dataHeadBg").hide()},5)})},Z=function(){$("#J-select-recharge").change(function(e){var a=$(this).val(),t={bet_date:a};Mango.Data.getGameData(t)}),$(s.gamesContainer).find("#supportDg").click(function(e){$(s.gamesContainer).hasClass("showDgTips")?$(s.gamesContainer).removeClass("showDgTips"):$(s.gamesContainer).addClass("showDgTips"),$(this).find("i.icoFx").hasClass("active")?$(this).find("i.icoFx").removeClass("active"):$(this).find("i.icoFx").addClass("active")}),$(s.gamesContainer).find(".choosedGamesFilter").click(function(e){if($(this).find("i.icoFx").hasClass("active"))$(this).find("i.icoFx").removeClass("active"),s.gamesListBox.find("dd.basicWays,dd.league_470").removeClass("hide");else{s.gamesListBox.find("dd.basicWays,dd.league_470").addClass("hide");var a=o.join("_");s.gamesListBox.find("dd.basicWays").each(function(e,t){var n=$(this).attr("game");-1===a.indexOf(n)&&($(this).find(".openBtn").hasClass("closeBtn")&&$(this).find(".openBtn").click(),$(this).find(".co6.moreOptBtnBox").hasClass("openBtn")&&$(this).find(".mcSelectBox").click())}),$(this).find("i.icoFx").addClass("active"),$("dd[game="+o.join("].basicWays,dd[game=")+"].basicWays").removeClass("hide"),$("dd[game="+o.join("].league_470,dd[game=")+"].league_470").removeClass("hide")}}),$(s.gamesContainer).find(".timeSwitcher").hover(function(e){$(this).find(".optionList").show()},function(e){$(this).find(".optionList").hide()}).find(".optionList > a").click(function(e){var a=($(this).attr("action_type"),$(this).text());$(s.gamesContainer).find(".timeSwitcher").find("a.imitateSelect").text(a),ee($(this).index()),$(s.gamesContainer).find(".optionList").hide()}),$(s.gamesContainer).find(".stopGame").click(function(e){$(this).find("i.icoFx").hasClass("active")?($(this).find("i.icoFx").removeClass("active"),s.gamesListBox.find('[istop="1"]').addClass("isOver")):($(this).find("i.icoFx").addClass("active"),s.gamesListBox.find('[istop="1"]').removeClass("isOver"))});var e=new UIscript.Tip({cls:"j-ui-tip-alert j-ui-tip-t j-ui-tip-showrule"});$(s.gamesContainer).find(".jtip").hover(function(){e.setText($(this).attr("inf")),e.show(e.getDom().width()/2*-1+$(this).width()/2,20,$(this))},function(){e.hide()})},ee=function(e){$(s.gamesListBox).find(".gameTime").each(function(a,t){var n=$(this).attr("data").split(";");$(this).find("i").html(n[e])})},ae=function(){var e={max:0,min:0},a={},t=u;if(s.selectedGamesBox.find(".gameOption").each(function(e,t){var n=$(t).attr("game_bn");a[n]={ranNumber:0,data:{}},$(t).find("a").each(function(e,t){var s=$(t).attr("way"),i=$(t).attr("index"),c=$(t).attr("fixed");a[n].data[s]||(a[n].data[s]=[]),"0"!==c&&(a[n].ranNumber=parseInt(c,10)),a[n].data[s].push({desc:s,value:i.split("_")[1],odd:$(t).attr("odd").split(",")[0],game:n})})}),$(".guoguanTab li:eq(1)").hasClass("active")){var n=$(".scrollMoniGuoguanList").find(".jtip.active");if(n.size()>0){var i=n.attr("combination");t=i.indexOf(",")>-1?i.split(","):[i]}else t=[]}for(var c=0,o=t.length;o>c;c++){var d=t[c];if(d.indexOf("x")>-1)var r=+d.split("x")[0];else var r=+d;var l=oe(a),h=doCombinations(te(l),r),f=ne(h);e.max+=f}$(".theorybonus").html(parseFloat(e.max).toFixed(2)+"</strong>元")},te=function(e){for(var a=[],t=0,n=e.length;n>t;t++)for(var s=0,i=e[t].length;i>s;s++)a.push(e[t][s]);return a},ne=function(e){for(var a=0,t=s.confirmContainer.find(".select-game-statics-multiple").find(".choose-input").val(),n=0,i=e.length;i>n;n++)if(f.length>0){var c=ie(e[n]);ce(c,f)?e[n].unshift(!0):e[n].unshift(!1)}else e[n].unshift(!0);e=se(e);for(var n=0,i=e.length;i>n;n++){for(var o=parseFloat(e[n][1].odd),d=2,r=e[n].length;r>d;d++)o*=parseFloat(e[n][d].odd);a+=2*o*t}return a},se=function(a){for(var t=[],n=0,s=a.length;s>n;n++)a[n][0]&&e(a[n])&&t.push(a[n]);return t},ie=function(e){for(var a=[],t=0,n=e.length;n>t;t++)a.push(e[t].game);return a},ce=function(e,a){for(var n=0;n<a.length;n++){t=!1;for(var s=0;s<e.length;s++)if(a[n]==e[s]){t=!0;break}if(!t)return!1}return!0},oe=function(e){var a=[];for(var t in e){var n=re(e[t].ranNumber),s=e[t].data,i=0,c=[];for(var o in n){var d=n[o],r=0,l=[];for(var u in s){var h=d[u],f=s[u],v=[];if("hafu"===u)v=de(h.value,f),v.length>0&&(r+=+v[0].odd,l.push(v[0]));else for(var m=0,g=f.length;g>m;m++)f[m].value===h.value&&(r+=+f[m].odd,l.push(f[m]))}r>i&&(i=r,c=l)}a.push(c)}return a},de=function(e,a){for(var t=[],s=0,i=a.length;i>s;s++)e.indexOf(a[s].value)>-1&&t.push(a[s]);return n(t)},re=function(e){return{"1:0":{had:{desc:"胜平负",value:"h"},ttg:{desc:"总进球",value:"s1"},hhad:{desc:"让球胜平负",value:function(){return e+1>0?"ch":e+1==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["hh","dh"],desc4value:"胜胜或平胜"},crs:{desc:"比分",value:"0100"}},"2:0":{had:{desc:"胜平负",value:"h"},ttg:{desc:"总进球",value:"s2"},hhad:{desc:"让球胜平负",value:function(){return e+2>0?"ch":e+2==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["hh","dh"],desc4value:"胜胜或平胜"},crs:{desc:"比分",value:"0200"}},"2:1":{had:{desc:"胜平负",value:"h"},ttg:{desc:"总进球",value:"s3"},hhad:{desc:"让球胜平负",value:function(){return e+1>0?"ch":e+1==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["hh","dh","ca"],desc4value:"胜胜或平胜或负胜"},crs:{desc:"比分",value:"0201"}},"3:0":{had:{desc:"胜平负",value:"h"},ttg:{desc:"总进球",value:"s3"},hhad:{desc:"让球胜平负",value:function(){return e+3>0?"ch":e+3==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["hh","dh"],desc4value:"胜胜或平胜"},crs:{desc:"比分",value:"0300"}},"3:1":{had:{desc:"胜平负",value:"h"},ttg:{desc:"总进球",value:"s4"},hhad:{desc:"让球胜平负",value:function(){return e+2>0?"ch":e+2==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["hh","dh","ah"],desc4value:"胜胜或平胜或负胜"},crs:{desc:"比分",value:"0301"}},"3:2":{had:{desc:"胜平负",value:"h"},ttg:{desc:"总进球",value:"s5"},hhad:{desc:"让球胜平负",value:function(){return e+1>0?"ch":e+1==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["hh","dh","ah"],desc4value:"胜胜或平胜或负胜"},crs:{desc:"比分",value:"0302"}},"4:0":{had:{desc:"胜平负",value:"h"},ttg:{desc:"总进球",value:"s4"},hhad:{desc:"让球胜平负",value:function(){return e+4>0?"ch":e+4==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["hh","dh"],desc4value:"胜胜或平胜"},crs:{desc:"比分",value:"0400"}},"4:1":{had:{desc:"胜平负",value:"h"},ttg:{desc:"总进球",value:"s5"},hhad:{desc:"让球胜平负",value:function(){return e+3>0?"ch":e+3==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["hh","dh","ah"],desc4value:"胜胜或平胜或负胜"},crs:{desc:"比分",value:"0401"}},"4:2":{had:{desc:"胜平负",value:"h"},ttg:{desc:"总进球",value:"s6"},hhad:{desc:"让球胜平负",value:function(){return e+2>0?"ch":e+2==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["hh","dh","ah"],desc4value:"胜胜或平胜或负胜"},crs:{desc:"比分",value:"0402"}},"5:0":{had:{desc:"胜平负",value:"h"},ttg:{desc:"总进球",value:"s5"},hhad:{desc:"让球胜平负",value:function(){return e+5>0?"ch":e+5==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["hh","dh"],desc4value:"胜胜或平胜"},crs:{desc:"比分",value:"0500"}},"5:1":{had:{desc:"胜平负",value:"h"},ttg:{desc:"总进球",value:"s6"},hhad:{desc:"让球胜平负",value:function(){return e+4>0?"ch":e+4==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["hh","dh","ah"],desc4value:"胜胜或平胜或负胜"},crs:{desc:"比分",value:"0501"}},"5:2":{had:{desc:"胜平负",value:"h"},ttg:{desc:"总进球",value:"s7"},hhad:{desc:"让球胜平负",value:function(){return e+3>0?"ch":e+3==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["hh","dh","ah"],desc4value:"胜胜或平胜或负胜"},crs:{desc:"比分",value:"0502"}},"-1-h":{had:{desc:"胜平负",value:"h"},ttg:{desc:"总进球",value:"s7"},hhad:{desc:"让球胜平负",value:function(){return e+6>0?"ch":e+6==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["hh","dh","ah"],desc4value:"胜胜或平胜或负胜"},crs:{desc:"比分",value:"-1-h"}},"0:0":{had:{desc:"胜平负",value:"d"},ttg:{desc:"总进球",value:"s0"},hhad:{desc:"让球胜平负",value:function(){return e>0?"ch":0==e?"cd":"ca"}()},hafu:{desc:"半全场",value:["dd"],desc4value:"平平"},crs:{desc:"比分",value:"0000"}},"1:1":{had:{desc:"胜平负",value:"d"},ttg:{desc:"总进球",value:"s2"},hhad:{desc:"让球胜平负",value:function(){return e>0?"ch":0==e?"cd":"ca"}()},hafu:{desc:"半全场",value:["hd","dd","ad"],desc4value:"胜平或平平或负平"},crs:{desc:"比分",value:"0101"}},"2:2":{had:{desc:"胜平负",value:"d"},ttg:{desc:"总进球",value:"s4"},hhad:{desc:"让球胜平负",value:function(){return e>0?"ch":0==e?"cd":"ca"}()},hafu:{desc:"半全场",value:["hd","dd","ad"],desc4value:"胜平或平平或负平"},crs:{desc:"比分",value:"0202"}},"3:3":{had:{desc:"胜平负",value:"d"},ttg:{desc:"总进球",value:"s6"},hhad:{desc:"让球胜平负",value:function(){return e>0?"ch":0==e?"cd":"ca"}()},hafu:{desc:"半全场",value:["hd","dd","ad"],desc4value:"胜平或平平或负平"},crs:{desc:"比分",value:"0303"}},"-1-d(平其它)":{had:{desc:"胜平负",value:"d"},ttg:{desc:"总进球",value:"s7"},hhad:{desc:"让球胜平负",value:function(){return e>0?"ch":0==e?"cd":"ca"}()},hafu:{desc:"半全场",value:["hd","dd","ad"],desc4value:"胜平或平平或负平"},crs:{desc:"比分",value:"-1-d"}},"0:1":{had:{desc:"胜平负",value:"a"},ttg:{desc:"总进球",value:"s1"},hhad:{desc:"让球胜平负",value:function(){return e-1>0?"ch":e-1==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["da","aa"],desc4value:"平负或负负"},crs:{desc:"比分",value:"0001"}},"0:2":{had:{desc:"胜平负",value:"a"},ttg:{desc:"总进球",value:"s2"},hhad:{desc:"让球胜平负",value:function(){return e-2>0?"ch":e-2==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["da","aa"],desc4value:"平负或负负"},crs:{desc:"比分",value:"0002"}},"1:2":{had:{desc:"胜平负",value:"a"},ttg:{desc:"总进球",value:"s3"},hhad:{desc:"让球胜平负",value:function(){return e-1>0?"ch":e-1==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["ha","da","aa"],desc4value:"胜负或平负或负负"},crs:{desc:"比分",value:"0102"}},"0:3":{had:{desc:"胜平负",value:"a"},ttg:{desc:"总进球",value:"s3"},hhad:{desc:"让球胜平负",value:function(){return e-3>0?"ch":e-3==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["da","aa"],desc4value:"平负或负负"},crs:{desc:"比分",value:"0003"}},"1:3":{had:{desc:"胜平负",value:"a"},ttg:{desc:"总进球",value:"s4"},hhad:{desc:"让球胜平负",value:function(){return e-2>0?"ch":e-2==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["ha","da","aa"],desc4value:"胜负或平负或负负"},crs:{desc:"比分",value:"0103"}},"2:3":{had:{desc:"胜平负",value:"a"},ttg:{desc:"总进球",value:"s5"},hhad:{desc:"让球胜平负",value:function(){return e-1>0?"ch":e-1==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["ha","da","aa"],desc4value:"胜负或平负或负负"},crs:{desc:"比分",value:"0203"}},"0:4":{had:{desc:"胜平负",value:"a"},ttg:{desc:"总进球",value:"s4"},hhad:{desc:"让球胜平负",value:function(){return e-4>0?"ch":e-4==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["da","aa"],desc4value:"平负或负负"},crs:{desc:"比分",value:"0004"}},"1:4":{had:{desc:"胜平负",value:"a"},ttg:{desc:"总进球",value:"s5"},hhad:{desc:"让球胜平负",value:function(){return e-3>0?"ch":e-3==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["ha","da","aa"],desc4value:"胜负或平负或负负"},crs:{desc:"比分",value:"0104"}},"2:4":{had:{desc:"胜平负",value:"a"},ttg:{desc:"总进球",value:"s6"},hhad:{desc:"让球胜平负",value:function(){return e-2>0?"ch":e-2==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["ha","da","aa"],desc4value:"胜负或平负或负负"},crs:{desc:"比分",value:"0204"}},"0:5":{had:{desc:"胜平负",value:"a"},ttg:{desc:"总进球",value:"s5"},hhad:{desc:"让球胜平负",value:function(){return e-5>0?"ch":e-5==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["da","aa"],desc4value:"平负或负负"},crs:{desc:"比分",value:"0005"}},"1:5":{had:{desc:"胜平负",value:"a"},ttg:{desc:"总进球",value:"s6"},hhad:{desc:"让球胜平负",value:function(){return e-4>0?"ch":e-4==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["ha","da","aa"],desc4value:"胜负或平负或负负"},crs:{desc:"比分",value:"0105"}},"2:5":{had:{desc:"胜平负",value:"a"},ttg:{desc:"总进球",value:"s7"},hhad:{desc:"让球胜平负",value:function(){return e-3>0?"ch":e-3==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["ha","da","aa"],desc4value:"胜负或平负或负负"},crs:{desc:"比分",value:"0205"}},"-1-a(负其他)":{had:{desc:"胜平负",value:"a"},ttg:{desc:"总进球",value:"s7"},hhad:{desc:"让球胜平负",value:function(){return e-6>0?"ch":e-6==0?"cd":"ca"}()},hafu:{desc:"半全场",value:["ha","da","aa"],desc4value:"胜负或平负或负负"},crs:{desc:"比分",value:"-1-a"}}}},le=function(){s.gamesListBox=$(s.gamesContainer).find(".gamesList"),s.selectedGamesContainer=$(s.submitContainer).find(".selectedGamesContainer"),s.selectedGamesBox=$(s.selectedGamesContainer).find(".selectedGamesBox"),s.gatesContainer=$(s.submitContainer).find(".gatesContainer"),s.confirmContainer=$(s.submitContainer).find(".confirmContainer")};return{init:function(e){s=$.extend(s,null,e||{}),le(),Z(),X(),b(),y(),O(),H()}}}();