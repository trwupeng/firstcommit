<?php

/**
 * Description of Financing2
 *
 * @author wu.chen
 */
class Financing2Controller extends FinancingController {

    /**
     * 添加参数 __VIEW__=json 返回json数据格式
     * 列表项字段：
     * {
     * 		waresId: 标的ID,
     * 		waresName：标的名称,
     * 		waresSN：第几期,
     * 		ICON:图标，如果有，没有的话，空串""，
     * 		priceStart:起投金额，
     * 		priceStep:递增金额
     * 		amount:总金额
     * 		remain:剩余金额
     * 		periodDesc:周期（天数，月数）
     * 		interestStart:起息T+?
     * 		guaranteeType：担保类型
     * 		yieldStatic:年化利率,8.4
     * 		yieldStaticAdd:上浮年化利率
     * 		yieldFloatFrom:浮动收益下限
     * 		yieldFloatTo:浮动收益上限
     * 		tags：额外标签（新手标之类）
     *      更多的可以去数据字典中查询
     *
     *      canSetRemindWares 是否可以设置开标提醒
     *      isReminWares  是否已经开启开标提醒
     * }
     * @input int pageId 分页ID,从1开始
     * @input int pageSize 分页尺寸
     * @input int shelfId 类型Id 默认2000  2000/3000/4000
     * @input string order 排序 like sort_shelfId/rsort_shelfId
     * //返回客户端旧标识信息
     * @output {"code":200,"order":排序方式,"WaresList":{"rs":[【列表项1字段】，【列表项2字段】],"pager":{"pageId":1开始的数字,"total":总共多少符合条件的记录}}}
     * //返回客户端新标识信息
     * @output {"code":200,"order":排序方式,"WaresList":{"rs":[【列表项1字段】，【列表项2字段】],"byshelfpager":{"pageId":1开始的数字,"total":总共多少符合条件的记录}}}
     * @errors {"code":400,"msg":"arg_error"}
     */
    public function byshelfAction() {
        $shelfId = $this->_request->get('shelfId', 4000);
        $pageId = $this->_request->get('pageId', 1) - 0;
        $order = $this->_request->get('order');
        if (!empty($order)) {
            $order = str_replace("_", " ", $order); //下划线分割
        }
        $this->loger->mainType = $shelfId; //日志记录
        $this->loger->target = $pageId;
        $shelfId = str_replace([2000,3000], [4000,5000], $shelfId); //替换
        if ($shelfId > 0) {
            $pager = new \Sooh\DB\Pager($this->_request->get('pageSize', 5) - 0);
            $pager->init(-1, $pageId);
            $where['timeStartPlan<'] = date('YmdHis', strtotime('+' . (\Prj\Data\Config::get('WARES_PLAN_HOURS') - 0) . ' hours', time()));
            $where['statusCode'] = [
                \Prj\Consts\Wares::status_go,
                \Prj\Consts\Wares::status_ready,
                \Prj\Consts\Wares::status_open,
                \Prj\Consts\Wares::status_return,
                \Prj\Consts\Wares::status_close,
                \Prj\Consts\Wares::status_ahead,
            ];
            $where['statusCode1!'] = -1;  //todo -1的标不展示
            //var_log($where,'>>>>>>>>>>');
            $wares = \Prj\Data\Wares::paged($shelfId, $pager, $order, $where);
            if (empty($wares) || \Sooh\Base\Tests\Bomb::blowup('wares_is_empty', false)) {
                return $this->returnOK('content_empty');
            }
            foreach ($wares as $k => $v) {
                $wares[$k]['ymdPayPlan'] = date('YmdHis', $v['ymdPayPlan']);
                //$wares[$k]['item'] = '××基金';
                unset($wares[$k]['introDisplay']);
                unset($wares[$k]['iRecordVerID']);
                //unset($wares[$k]['sLockData']);
                unset($wares[$k]['realRaise']);
                $wares[$k]['tags'] = preg_replace('/[X-Y]*/i', '', $wares[$k]['tags']);
                if ($v['statusCode'] == \Prj\Consts\Wares::status_ready) {
                    $ware = \Prj\Data\Wares::getCopy($v['waresId']);
                    $ware->load();
                    $userId = \Sooh\Base\Session\Data::getInstance()->get('accountId');
                    if ($userId) {
                        $user = \Prj\Data\User::getCopy($userId);
                        $user->load();
                        $wares[$k]['canSetRemindWares'] = \Prj\Wares\Wares::canSetRemindWares($ware, $user);
                        $wares[$k]['isReminWares'] = \Prj\Wares\Wares::isReminWares($user, $v['waresId']);
                    }
                    $wares[$k]['remindSecond'] = \Prj\Data\Config::get('wares_remind_second') - 0;
                }
            }
            $this->_view->assign('order', $order);
            $this->_view->assign('waresList', $wares);
            $this->_view->assign('pager', $pager->toArray()); //多个assign字段中出现pager,修改如下作为唯一根节点标识
            $this->_view->assign('byshelfpager', $pager->toArray());

            $this->returnOK();
        } else {
            $this->returnError(\Sooh\Base\ErrException::msgErrorArg);
        }
    }

}
