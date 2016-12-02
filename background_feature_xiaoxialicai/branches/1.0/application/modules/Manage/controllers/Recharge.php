<?php
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Investment as Investment;

/**
 * 订单
 * By Hand
 */
class RechargeController extends \Prj\ManagerCtrl
{
    public function indexAction()
    {
        $pageid = $this->_request->get('pageId', 1) - 0;
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        $pager = new \Sooh\DB\Pager(10, $this->pageSizeEnum, false);
        $pager->page_size = $this->_request->get('pageSize', 10);
        $pager->init(-1,$pageid);
        //>>>search
        $frm = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_userId_eq', form_def::factory('用户ID(精确)', '', form_def::text))
            ->addItem('_waresId_eq', form_def::factory('标的ID(精确)', '', form_def::text))
            ->addItem('pageid', $pageid)
            ->addItem('pagesize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) { //submit
            $where = $frm->getWhere();
        } else {
            $where = array();
        }

        //>>>

        $fieldsMapArr = array(
            'ordersId'=>['订单号','20'],
            'waresId'=>['标的号','15'],
            'userId'=>['用户ID','15'],
            'amount'=>['实投金额','15'],
            'amountExt'=>['红包','15'],
            'amountFake'=>['券金','15'],
            'yieldStatic'=>['固定年化','15'],
            'interest'=>['本金收益','15'],
            'interestExt'=>['奖励收益','15'],
            'extDesc'=>['赠送说明','15'],
            'orderTime'=>['下单时间','15'],
            'orderStatus'=>['订单状态','15'],
            'returnType'=>['还款方式','15'],
            'returnNext'=>['下次还款日','15'],
        );
        $rs = Investment::loopAll($where);
        $header = array();
        foreach($fieldsMapArr as $k=>$v)
        {
            $header[$v[0]] = 'auto';
        }

        foreach($rs as $v)
        {
            foreach($fieldsMapArr as $kk=>$vv)
            {
                $temp[$kk] = $v[$kk];
            }
            $temp['amount']/=100;
            $temp['amountExt']/=100;
            $temp['amountFake']/=100;
            $temp['interest']/=100;
            $temp['interestExt']/=100;
            $temp['yieldStatic'] = (string)($temp['yieldStatic']*100).'%';
            $temp['orderStatus'] = \Prj\Consts\OrderStatus::$enum[$temp['orderStatus']];
            $temp['returnType'] = \Prj\Consts\ReturnType::$enum[$temp['returnType']];
            $new[] = $temp;
        }
        $rs = $new;
        $this->_view->assign('header',$header);
        $this->_view->assign('rs',$rs);

    }
}
