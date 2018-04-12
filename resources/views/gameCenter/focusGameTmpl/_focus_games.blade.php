<script id="gamesList" type="text/x-dot-template">
@{{ for (var n in it) { var groupItem = it[n], bet_date = n.replace(/-/g, ''); }}
@{{ for (var i = 0, l = groupItem.length; i < l; i++) { var item = groupItem[i]; }}
<div class="fevent gameBox">
    <div class="fetop">
    	<div style="border-right: 1px solid #7f7f7f;">
    		 <p style="margin-top: 15px;">@{{=item.l_cn_abbr}}</p>
        	 <p>@{{=(item.date + ' ' + item.time)}}开赛</p>
    	</div>
    	<div style="text-align: center;">
    		<span class="left">@{{=item.h_cn_abbr}}</span>
    	</div>
    	<div style="text-align: center;">
    		 <span class="right">@{{=item.a_cn_abbr}}</span>
    	</div>
       <div class="feyopfour"></div>
        <!--<div class="fTit">
        </div>-->
    </div>
    @{{ if (item.had && +item.single_ways.had) { }}
    <div class="focusTab">
    <p class="focusTtime">距投注截止时间：<span class="countDown" data-time="@{{=item.sale_stop_at }}">00天00时00分00秒</span></p>
        <ul class="focuslist " action_type="tabSwitcher">
            @{{ if (item.had && +item.single_ways.had) { }}
            <li class="on" index="@{{=item.bn + '_had'}}">猜胜平负</li>
            @{{ } if (item.hhad && +item.single_ways.hhad) { }}
            <li class="" index="@{{=item.bn + '_hhad'}}">猜让球胜平负</li>
            @{{ } }}
        </ul>
        <div class="focusbox">
            <!-- 猜胜平负 -->
            <div class="focuschild wayBox" index="@{{=item.bn + '_had'}}">
                <div class="focuscht">
                    <ul class="Flist01 fix">
                        <li class="" onstyle="true">
                            <p>0%支持</p>
                            @{{ var data = JSON.stringify({way: 'had', name: 'h', odd: item.had.h[0], cn: Mango.Config.gameConfig.way_odd_map['h']['name']}); }}
                            <div class="Flistborder" action_type="chooseOdd" data=@{{=data}}>
                                <span>
                                    <b>@{{=item.h_cn_abbr}}</b>
                                    <font>胜@{{=item.had.h[0]}}</font>
                                </span>
                                <em class=""></em>
                            </div>
                        </li>
                        <li>
                            <p>0%支持</p>
                            @{{ var data = JSON.stringify({way: 'had', name: 'd', odd: item.had.d[0], cn: Mango.Config.gameConfig.way_odd_map['d']['name']}); }}
                            <div class="Flistborder" action_type="chooseOdd" data=@{{=data}}>
                                <span>
                                    <b>平局</b>
                                    <font>@{{=item.had.d[0]}}</font>
                                </span>
                                <em class=""></em>
                            </div>
                        </li>
                        <li>
                            <p>0%支持</p>
                            @{{ var data = JSON.stringify({way: 'had', name: 'a', odd: item.had.a[0], cn: Mango.Config.gameConfig.way_odd_map['a']['name']}); }}
                            <div class="Flistborder" action_type="chooseOdd" data=@{{=data}}>
                                <span>
                                    <b>@{{=item.a_cn_abbr}}</b>
                                    <font>胜@{{=item.had.a[0]}}</font>
                                </span>
                                <em class=""></em>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="focuschright bettingBox" way_type="had">
                    <input type="hidden" name="bet_num" value="" />
                    <h3>投注倍数</h3>
                    <div class="Fmultiple " action_type="multipleTuner">
                        <a href="javascript:void(0);" class="minusMultiple">-</a>
                        <input type="text" name="multiple" value="10"/>
                        <a href="javascript:void(0);" class="plusMultiple">+</a>
                    </div>
                    <p>购买金额：￥<span class="Fpaybig amountText">0</span></p>
                    <p>参考奖金：￥<span class="referPrizeText">0</span></p>
                    <p>盈利金额：￥<span class="profitText">0</span></p>
                    <div class="focusBtn">
                        <!--<a class="btnDisabled btn-ll" href="javascript:void(0);">奖金优化</a> -->
                        <a class="Redbtn btntext-red btn-ll" href="javascript:void(0);" action_type="betting"></a>
                    </div>
                    <i></i>
                </div>
            </div>
            @{{ } if (item.hhad && +item.single_ways.hhad) { }}
            <!-- 猜让球胜平负 -->
            <div class="focuschild wayBox" index="@{{=item.bn + '_hhad'}}" style="display: @{{= item.had ? 'none' : '' }};">
                <div class="focuscht">
                    <ul class="Flist01 fix">
                        <li>
                            <p>0%支持</p>
                            @{{ var data = JSON.stringify({way: 'hhad', name: 'ch', odd: item.hhad.ch[0], cn: Mango.Config.gameConfig.way_odd_map['ch']['name']}); }}
                            <div class="Flistborder" action_type="chooseOdd" data=@{{=data}}>
                                <span>
                                    <b>@{{=item.h_cn_abbr}}</b>
                                    <font>胜@{{=item.hhad.ch[0]}}</font>
                                </span>
                                <em class=""></em>
                            </div>
                        </li>
                        <li>
                            <p>0%支持</p>
                            @{{ var data = JSON.stringify({way: 'hhad', name: 'cd', odd: item.hhad.cd[0], cn: Mango.Config.gameConfig.way_odd_map['cd']['name']}); }}
                            <div class="Flistborder" action_type="chooseOdd" data=@{{=data}}>
                                <span>
                                    <b>平局</b>
                                    <font>@{{=item.hhad.cd[0]}}</font>
                                </span>
                                <em class=""></em>
                            </div>
                        </li>
                        <li  onstyle="true">
                            <p>0%支持</p>
                            @{{ var data = JSON.stringify({way: 'hhad', name: 'ca', odd: item.hhad.ca[0], cn: Mango.Config.gameConfig.way_odd_map['ca']['name']}); }}
                            <div class="Flistborder" action_type="chooseOdd" data=@{{=data}}>
                                <span>
                                    <b>@{{=item.a_cn_abbr}}</b>
                                    <font>胜@{{=item.hhad.ca[0]}}</font>
                                </span>
                                <em class=""></em>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="focuschright bettingBox" way_type="hhad">
                    <input type="hidden" name="bet_num" value="" />
                    <h3>投注倍数</h3>
                    <div class="Fmultiple " action_type="multipleTuner">
                        <a href="javascript:void(0);" class="minusMultiple">-</a>
                        <input type="text" name="multiple" value="10"/>
                        <a href="javascript:void(0);" class="plusMultiple">+</a>
                    </div>
                    <p>购买金额：￥<span class="Fpaybig amountText">0</span></p>
                    <p>参考奖金：￥<span class="referPrizeText">0</span></p>
                    <p>盈利金额：￥<span class="profitText">0</span></p>
                    <div class="focusBtn">
                        <!-- <a class="btnDisabled btn-ll" href="javascript:void(0);">奖金优化</a> -->
                        <a class="Redbtn btntext-red btn-ll" href="javascript:void(0);" action_type="betting"></a>
                    </div>
                    <i></i>
                </div>
            </div>
         
        </div>
    </div>
       @{{ } }}
    <div class="focusTab">
    <p class="focusTtime">距投注截止时间：<span class="countDown" data-time="@{{=item.sale_stop_at }}">00天00时00分00秒</span></p>        <ul class="focuslist " action_type="tabSwitcher">
            <li class="on" index="@{{=item.bn + '_bigsmall'}}">猜大小</li>
            <li index="@{{=item.bn + '_oddeven'}}">猜单双</li>
            <li index="@{{=item.bn + '_winscore'}}">猜赢球数</li>
        </ul>
        <div class="focusbox">
            <div class="focuschild wayBox" index="@{{=item.bn + '_bigsmall'}}">
                <div class="focuscht">
                    <div class="focusprompt">
                        <p>说明：两队进球数相加小于3球为小，大于等于3球为大</p>
                        <p>示例：比分1：1  1+1=2→ 小；比分 4：5  4+5=9=7+→ 大</p>
                    </div>
                    <ul class="Flist02 fix">
                        <li class="" onstyle="true" title="总进球数0,1,2">
                        @{{ var ttgNameSmall = ['s0', 's1', 's2'], ttgCnSmall = [], ttgOddSmall = []; for(var k = 0, kl = ttgNameSmall.length; k < kl; k++) { ttgCnSmall.push(Mango.Config.gameConfig.way_odd_map[ttgNameSmall[k]]['name']); ttgOddSmall.push(item.ttg[ttgNameSmall[k]][0]); } var data = JSON.stringify({"way": 'ttg', "name": ttgNameSmall, "odd": ttgOddSmall, "cn": ttgCnSmall}); }}
                            <div class="Flistborder "action_type="chooseOdd" data=@{{=data}}>
                                <span>
                                    <b>小</b>
                                    <font>@{{=(Math.min.apply(null, ttgOddSmall)+ '-' + Math.max.apply(null, ttgOddSmall))}}</font>
                                </span>
                                <em></em>
                            </div>
                        </li>
                        <li title="总进球数3,4,5,6,7+">
                        @{{ var ttgNameBig = ['s3', 's4', 's5', 's6', 's7'], ttgCnBig = [], ttgOddBig = []; for(var k = 0, kl = ttgNameBig.length; k < kl; k++) { ttgCnBig.push(Mango.Config.gameConfig.way_odd_map[ttgNameBig[k]]['name']); ttgOddBig.push(item.ttg[ttgNameBig[k]][0]); } var data = JSON.stringify({"way": 'ttg', "name": ttgNameBig, "odd": ttgOddBig, "cn": ttgCnBig}); }}
                            <div class="Flistborder"action_type="chooseOdd" data=@{{=data}}>
                                <span>
                                    <b>大</b>
                                    <font>@{{=(Math.min.apply(null, ttgOddBig)+ '-' + Math.max.apply(null, ttgOddBig))}}</font>
                                </span>
                                <em></em>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="focuschright bettingBox" way_type="ttg">
                    <input type="hidden" name="bet_num" value="" />
                    <h3>投注倍数</h3>
                    <div class="Fmultiple " action_type="multipleTuner">
                        <a href="javascript:void(0);" class="minusMultiple">-</a>
                        <input type="text" name="multiple" value="10"/>
                        <a href="javascript:void(0);" class="plusMultiple">+</a>
                    </div>
                    <p>购买金额：￥<span class="Fpaybig amountText">0</span></p>
                    <p>参考奖金：￥<span class="referPrizeText">0</span></p>
                    <p>盈利金额：￥<span class="profitText">0</span></p>
                    <div class="focusBtn">
                        <!-- <a class="btnDisabled btn-ll" href="javascript:void(0);">奖金优化</a> -->
                        <a class="Redbtn btntext-red btn-ll" href="javascript:void(0);" action_type="betting"></a>
                    </div>
                    <i></i>
                </div>
            </div>
            <div class="focuschild wayBox" index="@{{=item.bn + '_oddeven'}}" style="display: none;">
                <div class="focuscht">
                    <div class="focusprompt">
                        <p>说明：两队进球数相加 0、2、4、6、7+ → 双；两队进球数相加 1、3、5、7+ → 单</p>
                        <p>示例：比分 2:1  总进球数3 → 单；比分 4:0 总进球数4 → 双</p>
                    </div>
                    <ul class="Flist02 fix">
                        <li class="" onstyle="true" title="总进球数1,3,5,7+">
                            @{{ var ttgNameOdd = ['s1', 's3', 's5', 's7'], ttgCnOdd = [], ttgOddOdd = []; for(var k = 0, kl = ttgNameOdd.length; k < kl; k++) { ttgCnOdd.push(Mango.Config.gameConfig.way_odd_map[ttgNameOdd[k]]['name']); ttgOddOdd.push(item.ttg[ttgNameOdd[k]][0]); }; var data = JSON.stringify({"way": 'ttg', "name": ttgNameOdd, "odd": ttgOddOdd, "cn": ttgCnOdd}); }}
                            <div class="Flistborder"action_type="chooseOdd" data=@{{=data}}>
                                <span>
                                    <b>单</b>
                                    <font>@{{=(Math.min.apply(null, ttgOddOdd)+ '-' + Math.max.apply(null, ttgOddOdd))}}</font>
                                </span>
                                <em class=""></em>
                            </div>
                        </li>
                        <li title="总进球数0,2,4,,6,7+">
                            @{{ var ttgNameEven = ['s0', 's2', 's4', 's6', 's7'], ttgCnEven = [], ttgOddEven = []; for(var k = 0, kl = ttgNameEven.length; k < kl; k++) { ttgCnEven.push(Mango.Config.gameConfig.way_odd_map[ttgNameEven[k]]['name']); ttgOddEven.push(item.ttg[ttgNameEven[k]][0]); }; var data = JSON.stringify({"way": 'ttg', "name": ttgNameEven, "odd": ttgOddEven, "cn": ttgCnEven}); }}
                            <div class="Flistborder"action_type="chooseOdd" data=@{{=data}}>
                                <span>
                                    <b>双</b>
                                    <font>@{{=(Math.min.apply(null, ttgOddEven) + '-' + Math.max.apply(null, ttgOddEven))}}</font>
                                </span>
                                <em class=""></em>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="focuschright bettingBox" way_type="ttg">
                    <input type="hidden" name="bet_num" value="" />                    <input type="hidden" name="bet_num" value="" />
                    <input type="hidden" name="bet_num" value="" />
                    <h3>投注倍数</h3>
                    <div class="Fmultiple " action_type="multipleTuner">
                        <a href="javascript:void(0);" class="minusMultiple">-</a>
                        <input type="text" name="multiple" value="10"/>
                        <a href="javascript:void(0);" class="plusMultiple">+</a>
                    </div>
                    <p>购买金额：￥<span class="Fpaybig amountText">0</span></p>
                    <p>参考奖金：￥<span class="referPrizeText">0</span></p>
                    <p>盈利金额：￥<span class="profitText">0</span></p>
                    <div class="focusBtn">
                        <!-- <a class="btnDisabled btn-ll" href="javascript:void(0);">奖金优化</a> -->
                        <a class="Redbtn btntext-red btn-ll" href="javascript:void(0);" action_type="betting"></a>
                    </div>
                    <i></i>
                </div>
            </div>
            <div class="focuschild wayBox" index="@{{=item.bn + '_winscore'}}" style="display: none;">
                <div class="focuscht">
                    <span class="focusBall left">@{{=item.h_cn_abbr}}</span>
                    <div class="Flist03">
                        <ul>
                            <li class="" onstyle="true">
                            @{{ var crsNameH1 = ['0100', '0201', '0302', '-1-h'], crsCnH1 = [], crsOddH1 = []; for(var k = 0, kl = crsNameH1.length; k < kl; k++) { crsCnH1.push(Mango.Config.gameConfig.way_odd_map[crsNameH1[k]['name']]); crsOddH1.push(item.crs[crsNameH1[k]][0]); } var data = JSON.stringify({way: 'crs', name: crsNameH1, odd: crsOddH1, cn: crsCnH1}); }}
                                <div class="Flistborder FlistMin" action_type="chooseOdd" data=@{{=data}} title="1:0,2:1,3:2,胜其他">
                                    <span>
                                        <b>胜 1 球</b>
                                        <font>@{{=(Math.min.apply(null, crsOddH1) + '-' + Math.max.apply(null, crsOddH1))}}</font>
                                    </span>
                                    <em class="smllqb"></em>
                                </div>
                            </li>
                            <li>
                            @{{ var crsNameH2 = ['0200', '0301', '0402', '-1-h'], crsCnH2 = [], crsOddH2 = []; for(var k = 0, kl = crsNameH2.length; k < kl; k++) { crsCnH2.push(Mango.Config.gameConfig.way_odd_map[crsNameH2[k]['name']]); crsOddH2.push(item.crs[crsNameH2[k]][0]); } var data = JSON.stringify({way: 'crs', name: crsNameH2, odd: crsOddH2, cn: crsCnH2}); }}
                                <div class="Flistborder FlistMin" action_type="chooseOdd" data=@{{=data }} title="2:0,3:1,4:2,胜其他">
                                    <span>
                                        <b>胜 2 球</b>
                                        <font>@{{=(Math.min.apply(null, crsOddH2) + '-' + Math.max.apply(null, crsOddH2))}}</font>
                                    </span>
                                    <em class="smllqb"></em>
                                </div>
                            </li>
                            <li>
                            @{{ var crsNameH3 = ['0300', '0401', '0502', '-1-h'], crsCnH3 = [], crsOddH3 = []; for(var k = 0, kl = crsNameH3.length; k < kl; k++) { crsCnH3.push(Mango.Config.gameConfig.way_odd_map[crsNameH3[k]['name']]); crsOddH3.push(item.crs[crsNameH3[k]][0]); } var data = JSON.stringify({way: 'crs', name: crsNameH3, odd: crsOddH3, cn: crsCnH3}); }}
                                <div class="Flistborder FlistMin" action_type="chooseOdd" data=@{{=data}} title="3:0,4:1,5:2,胜其他">
                                    <span>
                                        <b>胜 3 球</b>
                                        <font>@{{=(Math.min.apply(null, crsOddH3) + '-' + Math.max.apply(null, crsOddH3))}}</font>
                                    </span>
                                    <em class="smllqb"></em>
                                </div>
                            </li>
                            <li>
                            @{{ var crsNameHother = ['0400', '0500', '0501', '-1-h'], crsCnHother = [], crsOddHother = []; for(var k = 0, kl = crsNameHother.length; k < kl; k++) { crsCnHother.push(Mango.Config.gameConfig.way_odd_map[crsNameHother[k]['name']]); crsOddHother.push(item.crs[crsNameHother[k]][0]); } var data = JSON.stringify({way: 'crs', name: crsNameHother, odd: crsOddHother, cn: crsCnHother}); }}
                                <div class="Flistborder FlistMin" action_type="chooseOdd" data=@{{=data}} title="4:0,5:0,5:1,胜其他">
                                    <span>
                                        <b>胜更多</b>
                                        <font>@{{=(Math.min.apply(null, crsOddHother) + '-' + Math.max.apply(null, crsOddHother))}}</font>
                                    </span>
                                    <em class="smllqb"></em>
                                </div>
                            </li>
                        </ul>
                        <ul>
                            <li>
                            @{{ var crsNameDraw = ['0000', '0101', '0202', '0303', '-1-d'], crsCnDraw = [], crsOddDraw = []; for(var k = 0, kl = crsNameDraw.length; k < kl; k++) { crsCnDraw.push(Mango.Config.gameConfig.way_odd_map[crsNameDraw[k]['name']]); crsOddDraw.push(item.crs[crsNameDraw[k]][0]); } var data = JSON.stringify({way: 'crs', name: crsNameDraw, odd: crsOddDraw, cn: crsCnDraw}); }}
                                <div class="Flistborder FlistMin" action_type="chooseOdd" data=@{{=data}} title="0:0,1:1,2:2,3:3,平其他">
                                    <span>
                                        <b>平局</b>
                                        <font>@{{=(Math.min.apply(null, crsOddDraw) + '-' + Math.max.apply(null, crsOddDraw))}}</font>
                                    </span>
                                    <em class="smllqb"></em>
                                </div>
                            </li>
                        </ul>
                        <ul>
                            <li class="" onstyle="true">
                            @{{ var crsNameA1 = ['0001', '0102', '0203', '-1-a'], crsCnA1 = [], crsOddA1 = []; for(var k = 0, kl = crsNameA1.length; k < kl; k++) { crsCnA1.push(Mango.Config.gameConfig.way_odd_map[crsNameA1[k]['name']]); crsOddA1.push(item.crs[crsNameA1[k]][0]); } var data = JSON.stringify({way: 'crs', name: crsNameA1, odd: crsOddA1, cn: crsCnA1}); }}
                                <div class="Flistborder FlistMin" action_type="chooseOdd" data=@{{=data}} title="0:1,1:2,2:3,负其他">
                                    <span>
                                        <b>胜 1 球</b>
                                        <font>@{{=(Math.min.apply(null, crsOddA1) + '-' + Math.max.apply(null, crsOddA1))}}</font>
                                    </span>
                                    <em class="smllqb"></em>
                                </div>
                            </li>
                            <li>
                            @{{ var crsNameA2 = ['0002', '0103', '0204', '-1-a'], crsCnA2 = [], crsOddA2 = []; for(var k = 0, kl = crsNameA2.length; k < kl; k++) { crsCnA2.push(Mango.Config.gameConfig.way_odd_map[crsNameA2[k]['name']]); crsOddA2.push(item.crs[crsNameA2[k]][0]); } var data = JSON.stringify({way: 'crs', name: crsNameA2, odd: crsOddA2, cn: crsCnA2}); }}
                                <div class="Flistborder FlistMin" action_type="chooseOdd" data=@{{=data}} title="0:2,1:3,2:4,负其他">
                                    <span>
                                        <b>胜 2 球</b>
                                        <font>@{{=(Math.min.apply(null, crsOddA2) + '-' + Math.max.apply(null, crsOddA2))}}</font>
                                    </span>
                                    <em class="smllqb"></em>
                                </div>
                            </li>
                            <li>
                            @{{ var crsNameA3 = ['0003', '0104', '0205', '-1-a'], crsCnA3 = [], crsOddA3 = []; for(var k = 0, kl = crsNameA3.length; k < kl; k++) { crsCnA3.push(Mango.Config.gameConfig.way_odd_map[crsNameA3[k]['name']]); crsOddA3.push(item.crs[crsNameA3[k]][0]); } var data = JSON.stringify({way: 'crs', name: crsNameA3, odd: crsOddA3, cn: crsCnA3}); }}
                                <div class="Flistborder FlistMin" action_type="chooseOdd" data=@{{=data}} title="0:3,1:4,2:5,负其他">
                                    <span>
                                        <b>胜 3 球</b>
                                        <font>@{{=(Math.min.apply(null, crsOddA3) + '-' + Math.max.apply(null, crsOddA3))}}</font>
                                    </span>
                                    <em class="smllqb"></em>
                                </div>
                            </li>
                            <li>
                            @{{ var crsNameAother = ['0004', '0005', '0105', '-1-a'], crsCnAother = [], crsOddAother = []; for(var k = 0, kl = crsNameAother.length; k < kl; k++) { crsCnAother.push(Mango.Config.gameConfig.way_odd_map[crsNameAother[k]['name']]); crsOddAother.push(item.crs[crsNameAother[k]][0]); } var data = JSON.stringify({way: 'crs', name: crsNameAother, odd: crsOddAother, cn: crsCnAother}); }}
                                <div class="Flistborder FlistMin" action_type="chooseOdd" data=@{{=data}} title="0:4,0:5,1:5,负其他">
                                    <span>
                                        <b>胜更多</b>
                                        <font>@{{=(Math.min.apply(null, crsOddAother) + '-' + Math.max.apply(null, crsOddAother))}}</font>
                                    </span>
                                    <em class="smllqb"></em>
                                </div>
                            </li>
                        </ul>
                        <!-- <div class="focusBalldraw">
                            <p>中国: 1场未出</p>
                            <p>卡塔尔: 8场未出</p>
                        </div> -->
                    </div>
                    <span class="focusBall right">@{{=item.a_cn_abbr}}</span>
                </div>
                <div class="focuschright bettingBox" way_type="crs">
                    <input type="hidden" name="bet_num" value="" />
                    <h3>投注倍数</h3>
                    <div class="Fmultiple " action_type="multipleTuner">
                        <a href="javascript:void(0);" class="minusMultiple">-</a>
                        <input type="text" name="multiple" value="10" />
                        <a href="javascript:void(0);" class="plusMultiple">+</a>
                    </div>
                    <p>购买金额：￥<span class="Fpaybig amountText">0</span></p>
                    <p>参考奖金：￥<span class="referPrizeText">0</span></p>
                    <p>盈利金额：￥<span class="profitText">0</span></p>
                    <div class="focusBtn">
                        <!-- <a class="btnDisabled btn-ll" href="javascript:void(0);">奖金优化</a> -->
                        <a class="Redbtn btntext-red btn-ll" href="javascript:void(0);" action_type="betting"></a>
                    </div>
                    <i></i>
                </div>
            </div>
        </div>
    </div>
</div>
@{{ } }}
@{{ } }}
</script>