@extends('l.home')

@section('title')
   系统通知
@parent
@stop


@section('main')

<div class="nav-bg nav-bg-tab">
    <div class="title-normal">系统通知</div>
</div>

<div class="content">
    <table width="100%" class="table-info table-toggle">
        <thead>
            <tr>
                <th>标题</th>
                <th>消息类型</th>
                <!-- <th>发送人</th> -->
                <th>发送时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datas as $data)
                <!-- TODO 判断已读和未读状态, 根据消息记录中的某个字段 -->
                <tr>
                    <td class="text-left"><a href="{!! route('station-letters.view', $data->id) !!}">
                        <i class="{!! !!$data->readed_at ? 'ico-mail-read' : 'ico-mail' !!}"></i>
                        {!! $data->msg_title !!}</a>
                    </td>
                    <td>{!! $aMsgTypes[$data->type_id] !!}</td>
                    <!-- <td>{!! $data->type_id == 6 ? $data->sender : '' !!}</td> -->
                    <td>{!! $data->created_at !!}</td>
                    <td>
                        <a href="{!! route('station-letters.view', $data->id) !!}">阅读</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @include('pagination.default', ['paginator' => $datas])
</div>

@include('userCenter.stationLetter.sendMessageBox')


@stop


@section('end')
@parent
<script type="text/javascript">
$(document).ready(function(){
    var postUrl      = "{!! route('station-letters.send-message') !!}";
    var parentInfo   = {!! $sJsonParent !!};
    var childrenInfo = {!! $sJsonChildren !!};

    var getSendMessageBoxContent = function () {
        return $('#sendMessageBox').html();
    };

    var addMask = new gagame.Mask(),
        addMiniwindow = new gagame.Message();

    var objTypeSelector = null;
    var objUserSelector = null;

    var rerenderReceiverSelectBox = function (data) {
        if (!data.length) objUserSelector.reBuildSelect([{id: '', username:'--', checked:true}]);
        data[0]['checked'] = true;
        objUserSelector.reBuildSelect(  data);
    };

    var data = {
        cls : 'w-12  add-card-miniwindow',
        title: '发送站内信',
        content: getSendMessageBoxContent(),
        confirmIsShow: true,
        cancelIsShow: true,
        confirmFun: function () {
            sendMessage();
        },
        cancelFun: function () {
            addMiniwindow.hide();
            addMask.hide();
        }
    };
    $('#J-sendBox').on('click',function(){
        addMiniwindow.show(data);
        addMask.show();

        var domFormBox = addMiniwindow.getContainerDom();
        $(domFormBox).find('select[name=user_type]').attr('id', 'J-select-type').end()
                     .find('select[name=receiver]').attr('id', 'J-select-username');

        objTypeSelector = new gagame.Select({realDom: '#J-select-type', cls: 'w-5'});
        objUserSelector = new gagame.Select({realDom: '#J-select-username', cls: 'w-5', valueKey: 'id', textKey: 'username'});

        objTypeSelector.addEvent('change', function() {
            var index = objTypeSelector.getValue();
            var data = [parentInfo, [], childrenInfo][+index - 1];
            rerenderReceiverSelectBox(data);
        });
    });

    var sendMessage = function () {
        var domFormBox = addMiniwindow.getContainerDom();
        var formData = $(domFormBox).find('form[name=sendMessageForm]').serializeArray();
        var jsonData = {};
        $(formData).each(function () {
            jsonData[this.name] = this.value;
        });
        if (jsonData['title'].length > 20) {
            alert('标题最多20个字符');
            return false;
        }
        if (jsonData['content'].length < 20) {
            alert('内容最少20个字符');
            return false;
        }
        $.ajax({
            url: postUrl,
            type: 'POST',
            dataType: 'json',
            data: jsonData,
        })
        .done(function(rdata) {
            alert(rdata.msg);
            if (rdata.success) {
                addMiniwindow.hide();
                addMask.hide();
                window.location.href = '/letters/outbox';
            }
        })
        .fail(function(rdata) {
            alert(rdata.msg);
        })
        .always(function(rdata) {
            // console.log("complete");
        });

    };
});
</script>

@stop
