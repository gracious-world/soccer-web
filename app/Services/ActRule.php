<?php
/**
 * 防刷机制
 * User: damon
 * Date: 1/24/16
 * Time: 7:14 PM
 */
namespace App\Services;
use \Redis;

class ActRule{

    /**
     * 接口防刷机制
     *
     * @param StringTool app 每个应用的唯一标记号，前台为user,后台为admin
     * @param StringTool method,调用的接口名称和方法,长度<20
     * @param StringTool identify, 区分的标记，长度<20，必须是字母数字下划线，例如奖期ID
     * @param int interval, 防刷的时间间隔，以秒为单位，>=1
     * @param int total, 防刷的上限次数，>=1
     * @param int add_count, 添加的次数
     * @param int add_time, 调用防刷的时间，是否异步处理时后台脚本传入，默认是当前时间time()
     * @param int action_interval, 单次动作间隔的时间，<= $interval
     *
     * @return array ret, ret['data']=
     * 	array(
     * 		'ban'=>true/false, /// true表示达到上限了 false标明还没达到上限
     * 		'total'=>34, /// 目前已经达到的统计数（不包含本次）
     * 		'run_total'=>2, /// 连续达到上线数
     * 	)
     */
    public function rule($app, $method, $identify, $interval, $total, $add_count=1, $add_time='', $action_interval=0) {
        if (empty($app)) return $this->makeResult(2, 'app参数非法');
        if (is_null($method) || ($method === '') || (strlen($method) > 20)) return $this->makeResult(2, 'type参数非法');
        if (is_null($identify) || ($identify === '') || (strlen($identify) > 32)) return $this->makeResult(2, 'identify参数非法');
        if (!(is_numeric($interval) && ($interval > 0))) return $this->makeResult(2, 'interval参数非法');
        if (!(is_numeric($total) && ($total > 0))) return $this->makeResult(2, 'total参数非法');
        if (!(is_numeric($add_count) && ($add_count >= 0))) return $this->makeResult(2, 'add_count参数非法');
        if (!(is_numeric($action_interval) && ($action_interval >= 0))) return $this->makeResult(2, 'action_interval参数非法');
        if ($action_interval > $interval) return $this->makeResult(2, 'action_interval不能大于interval');

        if (empty($add_time)) $add_time = time();

        /// 计算rule_id, rule_log_id
        $rule_id = $app.'-'.$method.'-'.$identify.'-'.$interval;
        $rule_log_id = $rule_id.'-'.$identify.'-'.ceil(($add_time-1275235200)/$interval);
        $prev_rule_log_id = $rule_id.'-'.$identify.'-'.(ceil(($add_time-1275235200)/$interval)-1);

        /// 判断缓存里是否有key是$rule_log_id的记录，如果没有，创建！
        $rule_log = $this->doRedis('get', $rule_log_id, '');
        /// 如果缓存存在
        if ($rule_log) {
            $last_action_time = 0;
            if (isset($rule_log['action_time'])) $last_action_time = $rule_log['action_time'];

            /// 判断是否有单次动作时间间隔限制
            /// 如果有，而且$add_count>0 && $last_action_time>0
            /// 进行动作时间判断
            /// 备注：为了节约缓存，这种情况，不往缓存里存储防刷数据，也不更新防刷数据
            if (($action_interval > 0) && ($add_count > 0) && ($last_action_time > 0) && (($add_time - $last_action_time) < $action_interval)) {

                $ret = array(
                    'ban'=>true,
                    'total'=>$rule_log['total'],
                    'run_total'=>$rule_log['run_total'],
                    'rule_log_id'=>$rule_log_id,
                );

                return $this->makeResult(0, '单次间隔缓存返回(update'.($add_time - $last_action_time).'<'.$action_interval.')', $ret);
            }

            /// 更新缓存
            $rule_log['total'] = $rule_log['total'] + $add_count; /// 这里不是原子操作，有缺陷 @todo
            $rule_log['update_time'] = $add_time;

            if ($add_count > 0) $rule_log['action_time'] = $add_time;

            $this->doRedis('set', $rule_log_id, $rule_log);

            $ban = false;

            /// 如果已经达到上限了
            if ($rule_log['total'] > $total) {
                $ban = true;
            }

            $ret = array(
                'ban'=>$ban,
                'total'=>$rule_log['total'],
                'run_total'=>$rule_log['run_total'],
                'rule_log_id'=>$rule_log_id,
            );

            return $this->makeResult(0, '缓存返回', $ret);
        }
        /// 如果缓存不存在
        else {

            $last_action_time = 0;

            /// 判断前一次是否达到上线，从而计算run_total
            $prev_rule_log = $this->doRedis('get', $prev_rule_log_id, '');

            $run_total = 0;
            if ($prev_rule_log) {

                /// 得到上一次动作的时间
                if (isset($prev_rule_log['action_time'])) {
                    $last_action_time = $prev_rule_log['action_time'];
                }

                if ($prev_rule_log['total'] >= $total) {
                    $run_total = $prev_rule_log['run_total']+1;
                }
                else {
                    $run_total = $prev_rule_log['run_total'];
                }
            }

            $data = array(
                'rule_log_id' => $rule_log_id,
                'total'       => $add_count,
                'run_total'   => $run_total,
                'record_time' => $add_time,
                'update_time' => $add_time,
                'action_time' => ($add_count > 0) ? $add_time : 0, /// 如果添加是0, 这个时候设置最后一次动作的时间是0
            );

            /// 判断是否有单次动作时间间隔限制
            /// 如果有，而且$add_count>0 && $last_action_time>0
            /// 进行动作时间判断
            /// 备注：为了节约缓存，这种情况，不往缓存里存储防刷数据，也不更新防刷数据
            if (($action_interval > 0) && ($add_count > 0) && ($last_action_time > 0) && (($add_time - $last_action_time) < $action_interval)) {

                $ret = array(
                    'ban'=>true,
                    'total'=>$data['total'],
                    'run_total'=>$data['run_total'],
                    'rule_log_id'=>$rule_log_id,
                );

                return $this->makeResult(0, '单次间隔缓存返回(add'.($add_time - $last_action_time).'<'.$action_interval.')', $ret);
            }

            /// 新增缓存前，判断是否存在rule_id的缓存
            /// 如果不存在，创建；如果存在，则删除它last_log_id对应的缓存，并更新last_log_id
            $rule = $this->doRedis('get', $rule_id, '');

            if ($rule) {
                if ($rule['last_log_id']) {
                    $this->doRedis('del', $rule['last_log_id'], '');
                }

                $rule['total'] = $total;
                $rule['last_log_id'] = $rule_log_id;
                $rule['update_time'] = $add_time;
            }
            else {
                $rule = array(
                    'rule_id'=>$rule_id,
                    'app'=>$app,
                    'method'=>$method,
                    'interval'=>$interval,
                    'total'=>$total,
                    'last_log_id'=>$rule_log_id,
                    'record_time'=>$add_time,
                    'update_time'=>$add_time,
                );
            }
            $this->doRedis('set', $rule_id, $rule);

            /// 增加缓存
            $this->doRedis('set', $rule_log_id, $data);

            /// 计算是否达到上线
            $ban = false;
            if ($add_count > $total) {
                $ban = true;
            }

            $ret = array(
                'ban' => $ban,
                'total' => $data['total'],
                'run_total' => $data['run_total'],
                'rule_log_id' => $rule_log_id,
            );

            return $this->makeResult(0, '缓存返回(新增缓存)', $ret);
        }
    }

    /**
     * 格式化返回值
     * @param $code int 代码
     * @param $msg StringTool 信息
     * @param array $data array 数据
     * @return array
     */
    private function makeResult($code,$msg,$data = []){
        $aReturn = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ];
        return $aReturn;
    }

    private function doRedis($op,$k,$v=''){
        switch($op){
            case 'get':
                $ret = Redis::get($k);
                return json_decode($ret,true);
                break;
            case 'set':
                $ret = Redis::set($k,json_encode($v));
                return $ret;
                break;
            case 'del':
                $ret = Redis::del($k);
                return $ret;
                break;
            default:
                return $this->makeResult(-1,'非法缓存操作类型!');
                break;
        }
    }

}