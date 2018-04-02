@extends('l.ucenter')

@section('title')
    投注记录
@parent
@stop

@section('main')
<div class="panel panel-warning">
  <div class="panel-heading">
      <span class="col-xs-2">欢迎您：{!! Session::get('nickname') !!}</span>
      <span class="pull-right">
          <span>余额：<font class="text-danger">{!!$fAvailable!!}</font>元 </span>
          <input class="btn btn-xs btn-danger" type="button" value="充值">
          <input class="btn btn-xs btn-danger" type="button" value="提现">
          <input class="btn btn-xs btn-danger" type="button" value="转账">
      </span>
      <div class="clearfix"></div>
  </div>
</div>


  <!-- Nav tabs -->
  <ul class="nav nav-tabs " role="tablist">
    <li role="presentation" class="active"><a href="#1" aria-controls="1" role="tab" data-toggle="tab">投注记录</a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div class="panel-body">

      <form class="form-inline">
        <div class="form-group">
          <label>我要查询</label>
          <select class="form-control">
            <option>全部彩种</option>
            <option>2</option>
          </select>
          <select class="form-control">
            <option>全部游戏</option>
            <option>2</option>
          </select>
        </div>
        <div class="form-group">
          <label>开始时间</label>
          <input type="text" class="form-control" value="2016-10-10">
        </div>
        <div class="form-group">
          <label>结束时间</label>
          <input type="text" class="form-control" value="2016-10-10" >
        </div>
        <button type="submit" class="btn btn-success">查询</button>
      </form>

    </div>
  </div>

  <div class="panel panel-default mt10">
      <table class="table table-striped table-hover">
          <thead>
              <tr class="info">
                <th>彩种</th>
                <th>玩法</th>
                <th>投注时间</th>
                <th>投注金额</th>
                <th>方案状态</th>
                <th>您的奖金</th>
                <th>方案编号</th>
                <th>方案详情</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>竞彩足球</td>
                <td>胜平负</td>
                <td>2015.12.03  16:03:07</td>
                <td>￥20.00</td>
                <td>未出票</td>
                <td>-</td>
                <td>2016JCZQ235797</td>
                <td><a href="##">查看</a></td>
              </tr>
              <tr>
                <td>竞彩足球</td>
                <td>胜平负</td>
                <td>2015.12.03  16:03:07</td>
                <td>￥20.00</td>
                <td>未出票</td>
                <td>-</td>
                <td>2016JCZQ235797</td>
                <td><a href="##">查看</a></td>
              </tr>
              <tr>
                <td>竞彩足球</td>
                <td>胜平负</td>
                <td>2015.12.03  16:03:07</td>
                <td>￥20.00</td>
                <td>未出票</td>
                <td>-</td>
                <td>2016JCZQ235797</td>
                <td><a href="##">查看</a></td>
              </tr>
              <tr>
                <td>竞彩足球</td>
                <td>胜平负</td>
                <td>2015.12.03  16:03:07</td>
                <td>￥20.00</td>
                <td>未出票</td>
                <td>-</td>
                <td>2016JCZQ235797</td>
                <td><a href="##">查看</a></td>
              </tr>
              <tr>
                <td>竞彩足球</td>
                <td>胜平负</td>
                <td>2015.12.03  16:03:07</td>
                <td>￥20.00</td>
                <td>未出票</td>
                <td>-</td>
                <td>2016JCZQ235797</td>
                <td><a href="##">查看</a></td>
              </tr>
              <tr>
                <td>竞彩足球</td>
                <td>胜平负</td>
                <td>2015.12.03  16:03:07</td>
                <td>￥20.00</td>
                <td>未出票</td>
                <td>-</td>
                <td>2016JCZQ235797</td>
                <td><a href="##">查看</a></td>
              </tr>
              <tr>
                <td>竞彩足球</td>
                <td>胜平负</td>
                <td>2015.12.03  16:03:07</td>
                <td>￥20.00</td>
                <td>未出票</td>
                <td>-</td>
                <td>2016JCZQ235797</td>
                <td><a href="##">查看</a></td>
              </tr>
              <tr>
                <td>竞彩足球</td>
                <td>胜平负</td>
                <td>2015.12.03  16:03:07</td>
                <td>￥20.00</td>
                <td>未出票</td>
                <td>-</td>
                <td>2016JCZQ235797</td>
                <td><a href="##">查看</a></td>
              </tr>
              <tr>
                <td>竞彩足球</td>
                <td>胜平负</td>
                <td>2015.12.03  16:03:07</td>
                <td>￥20.00</td>
                <td>未出票</td>
                <td>-</td>
                <td>2016JCZQ235797</td>
                <td><a href="##">查看</a></td>
              </tr>
              <tr>
                <td>竞彩足球</td>
                <td>胜平负</td>
                <td>2015.12.03  16:03:07</td>
                <td>￥20.00</td>
                <td>未出票</td>
                <td>-</td>
                <td>2016JCZQ235797</td>
                <td><a href="##">查看</a></td>
              </tr>
              <tr>
                <td>竞彩足球</td>
                <td>胜平负</td>
                <td>2015.12.03  16:03:07</td>
                <td>￥20.00</td>
                <td>未出票</td>
                <td>-</td>
                <td>2016JCZQ235797</td>
                <td><a href="##">查看</a></td>
              </tr>


            </tbody>
            <tfoot>
              <tr class="active">
                <th>彩种</th>
                <th>玩法</th>
                <th>投注时间</th>
                <th>投注金额</th>
                <th>方案状态</th>
                <th>您的奖金</th>
                <th>方案编号</th>
                <th>方案详情</th>
              </tr>
            </tfoot>
          </table>
  </div>

  <div class="text-right">
      <ul class="pagination pagination-sm">
          <li><a href="#" aria-label="Previous"><span aria-hidden="true">«</span></a></li>
          <li><a href="#">1</a></li>
          <li><a href="#">2</a></li>
          <li><a href="#">3</a></li>
          <li><a href="#">4</a></li>
          <li><a href="#">5</a></li>
          <li><a href="#" aria-label="Next"><span aria-hidden="true">»</span></a></li>
        </ul>

  </div>

@stop