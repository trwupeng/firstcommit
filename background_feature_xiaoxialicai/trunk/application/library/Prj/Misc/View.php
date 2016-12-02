<?php

namespace Prj\Misc;

/**
 * 显示的时候用的函数
 *
 * @author simon.wang
 */
class View {

    protected static $msg;

    public static function realUrl($url, $redirByHeader = false) {
        if (substr($url, 0, 2) == '__') {
            $tmp = null;
            parse_str($url, $tmp);
            $__ = $tmp['__'];
            unset($tmp['__']);
            $__ = explode('/', $__);
            if (sizeof($__) == 2) {
                array_unshift($__, 'index');
            }
            $url = \Sooh\Base\Tools::uri($tmp, $__[2], $__[1], $__[0]);
        } elseif (empty($url)) {
            $url = \Sooh\Base\Tools::uri(null);
        }
        if ($redirByHeader) {
            header('Location: ' . $url);
        } else {
            return $url;
        }
    }

    public static function redirectIfNeeds($code, $returnUrl, $arr = []) {
        $arr['returnUrl'] = $returnUrl;
        switch ($code) {
            case \Prj\Consts\ReturnCode::notBind:
                //header('Location: '.\Sooh\Base\Tools::uri($arr,'bindcard','user'));
                $notice = '请先绑定银行卡';
                $url = \Sooh\Base\Tools::uri($arr, 'bindcard', 'user');
                header('Location: ' . \Sooh\Base\Tools::uri(['notice' => $notice, 'url' => $url], 'redirectshow', 'orders'));
                return true;
            case \Prj\Consts\ReturnCode::walletOut:
                //header('Location: '.\Sooh\Base\Tools::uri($arr,'recharge','user'));
                $notice = '余额不足，请充值';
                $url = \Sooh\Base\Tools::uri($arr, 'recharge', 'user');
                header('Location: ' . \Sooh\Base\Tools::uri(['notice' => $notice, 'url' => $url], 'redirectshow', 'orders'));
                return true;
            case \Prj\Consts\ReturnCode::notLogin:
                header('Location: ' . \Sooh\Base\Tools::uri($arr, 'login', 'user'));
                return true;
            case \Prj\Consts\ReturnCode::userLocked:
                echo '[user_lock]';
                return false;
            case \Prj\Consts\ReturnCode::recordLocked:
                echo '[wares_lock]';
                return false;
            case \Prj\Consts\ReturnCode::dbError:
                echo '[db_error]';
                return false;
            case 400:
                echo '[error]' . self::$msg;
                break;
        }
    }

    /**
     * 默认的分页栏
     * @param \Sooh\DB\Pager $pager
     */
    public static function pagerStandard($pager) {
        $str = '
    <div class="pages">
        <span>每页&nbsp;</span>
        <div class="selectPagesize">
            <select data-toggle="selectpicker" data-toggle-change="changepagesize">';
        $r = explode(',', $pager->enumPagesize);
        foreach ($r as $n) {
            $str.="<option value=\"$n\">$n</option>";
        }
        $str .= '</select>
        </div>
        <span>&nbsp;条，共 ' . $pager->total . ' 条' . ($pager->recordsSum !== null ? ' (求和:' . $pager->recordsSum . ')' : '') . '</span>
    </div>
    <div class="pagination-box" data-toggle="pagination" data-total="' . $pager->total . '" data-page-size="' . $pager->page_size . '" data-page-current="' . $pager->pageid() . '"></div>
';
        return $str;
    }

    public static function fmtYmd($number, $type = null) {
        $allNumber = $number;
        if (is_numeric($allNumber)) {
            if ($number < 23450101) {
                $d = $number % 100;
                $y = substr($number, 0, 4);
                $m = floor(($number % 10000) / 100);
            } elseif ($number < 2147483647) {
                return date('Y-m-d', $number);
            } else {
                $number = substr($number, 0, 8);
                $d = $number % 100;
                $y = substr($number, 0, 4);
                $m = floor(($number % 10000) / 100);
                if ($type == 'time') {
                    $h = substr($allNumber, 8, 2);
                    $i = substr($allNumber, 10, 2);
                    $s = substr($allNumber, 12, 2);
                    return "$y-$m-$d $h:$i:$s";
                }
            }
        } else {
            $dt0 = strtotime($number);
            return date('Y-m-d', $dt0);
        }

        return "$y-$m-$d";
    }

    public static function encodePkey($arr) {
        return base64_encode(json_encode($arr));
    }

    public static function decodePkey($str) {
        if (!empty($str)) {
            $str = base64_decode(str_replace('%3D', '=', $str));
            return json_decode($str, true);
        } else {
            return null;
        }
    }

    public static function strIncludeJs($fullnameUnderDir, $path = '/BJUI') {
        return '<script  src="' . $path . '/' . $fullnameUnderDir . '"></script>' . "\n";
    }

    public static function strIncludeCss($fullnameUnderDir, $path = '/BJUI') {
        return '<link href="' . $path . '/' . $fullnameUnderDir . '" rel="stylesheet" type="text/css" media="screen"/>' . "\n";
    }

    public static function btnDeleteInDatagrid($url) {
        return '<a href="' . $url . '" class="btn btn-red" data-toggle="doajax" data-confirm-msg="确定要删除该行信息吗？">删</a>';
    }

    // 普通的按钮，点击就执行，没有提示的。
    public static function btn($url, $capt) {
        return '<a href="' . $url . '" class="btn btn-blue">' . $capt . '</a>';
    }

    /**
     * 通用的弹窗按钮
     * mask = true 是否模态
     * onClose = refresh 是否关闭刷新
     * max = true 是否最大化
     * width/height
     */
    public static function btnEditInDatagrid($notice = '修改', $url, $options = '', $ext = '', $class = 'btn-green') {
        if (!empty($options))
            $str = json_encode($options);
        return '<a href="' . $url . '" class="btn ' . $class . ' " '
                . 'data-options=\'' . $str . '\' '
                //.'data-on-close=refresh '
                . 'data-toggle="dialog" '
                . 'data-id="form" '
                . 'data-reload-warn="本页已有打开的内容，确定将刷新本页内容，是否继续？" '
                . $ext
                . '>' . $notice . '</a>';
    }

    /**
     * 打开新的标签页
     */
    public static function btnDefaultInDatagrid($capt, $url) {
        return '<a type="button" class="btn btn-green" data-toggle="navtab" data-id="' . $capt . '"  data-url="' . $url . '" data-title="' . $capt . '">' . $capt . '</a>';
    }

    /**
     * url 跳转
     * @param $capt
     * @param $notice
     * @param $url
     * @return string
     */
    public static function doUrlTo($capt, $notice, $url, $id = '') {
        $id = $id ? $id : $notice;
        return '<a type="button" data-fresh="true" class="btn btn-green" style="cursor:pointer;" data-toggle="navtab" data-id="' . $id . '"  data-url="' . $url . '" data-title="' . $notice . '">' . $capt . '</a>';
    }

    /**
     * ajax 提交按钮
     */
    public static function btnAjax($capt, $url, $msg = '确定?', $options = []) {
        return "<a id='" . $options['id'] . "' href='" . $url . "' class='btn btn-blue' data-toggle='doajax' data-confirm-msg='" . $msg . "' data-callback='" . $options['callback'] . "' >" . $capt . "</a>";
    }

    //tgh 新增
    public static function btnAddInDatagrid($url) {
        return '<a href="' . $url . '" class="btn btn-blue" '
                //. 'data-toggle="navtab" '
                //.' data-on-close="refresh" '
                . 'data-toggle="dialog" data-width="800" data-height="400" data-id="dialog-max" '
                . 'data-id="form" '
                . 'data-reload-warn="本页已有打开的内容，确定将刷新本页内容，是否继续？" '
                . '>新增</a>';
    }

    //tgh 删除选中行
    public static function btnDelClickInDatagrid($url) {
        return '<button type="button" class="btn btn-red" '
                . 'data-url="' . $url . '" data-toggle="doajax" '
                . 'data-confirm-msg="确定要删除选中项吗？"  title="可以在控制台(network)查看被删除ID">'
                . '<i class="fa fa-remove"></i> 删除选中行</button>';
    }

    //tgh 删除勾选项
    public static function btnDelChooseInDatagrid($url) {
        return '<a href="' . $url . '" data-toggle="doajaxchecked" '
                . 'data-confirm-msg="确定要删除选中项吗？" data-idname="delids" data-group="ids">删除选中</a>';
    }

    //tgh 正常/禁用按钮
    public static function btnDisableDatagrid($urlArr, $arg) {
        $url = $urlArr[$arg];
        if ($arg) {
            return '<a href="' . $url . '" class="btn btn-green" data-toggle="doajax" data-confirm-msg="确定要禁用该用户吗？">正常</a>';
        } else {
            return '<a href="' . $url . '" class="btn btn-red" data-toggle="doajax" data-confirm-msg="确定要启用该用户吗？">禁用</a>';
        }
    }

    //tgh 购买按钮名称
    public static function btnBuyName($num) {
        switch ($num) {
            case \Prj\Consts\Wares::status_open:
                return "立即投资";
            case \Prj\Consts\Wares::status_go:
                return "已投满";
            default:
                return "敬请期待";
        }
    }

    /**
     * 多选框ajax提交
     * By Hand
     */
    public static function btnCheckAjaxInDatagrid($notice, $url, $warn = '确定？') {
        return '<a href="' . $url . '" data-toggle="doajaxchecked" '
                . 'data-confirm-msg="' . $warn . '" data-idname="ids" data-group="ids">' . $notice . '</a>';
    }

    /**
     * 表头checkBox
     */
    public static function thCheckBox() {
        return '<input type="checkbox" class="checkboxCtrl" data-group="ids" data-toggle="icheck">';
    }

    /**
     * 表中checkBox
     */
    public static function trCheckBox($value = 0) {
        return '<input type="checkbox" name="ids" data-group="ids" data-toggle="icheck" value="' . $value . '">';
    }

    /**
     * 对账diff翻译
     */
    public static function checkDiff($diff) {
        if (!is_array($diff)) {
            $diff = explode(',', $diff);
        }
        if (empty($diff)) {
            return '';
        } else {
            $tmp = [];
            foreach ($diff as $v) {
                $tmp[] = \Prj\Consts\Check::$noticceName[$v];
            }
            return implode(',', $tmp);
        }
    }

    /**
     * 编辑页模版
     */
    public static function editHtml($num = 1) {
        $html = '<div class="bjui-pageContent">';
        $renderer = new \Prj\Misc\FormRenderer;
        $html.= $renderer->getEditStandard(\Sooh\Base\Form\Broker::getCopy('default'), $num);
        $html.= '</div><div class="bjui-pageFooter"><ul><li><button type="button" class="btn-close" data-icon="close">取消</button></li><li><button type="submit" class="btn-green" data-icon="save">确定</button></li></ul></div>';
        return $html;
    }

    /**
     * 重发请求的按钮
     * @param $obj
     * @param null $retryBtnShow
     * @return string
     */
    public static function retryBtn($obj, $retryBtnShow = null) {
        if (is_object($obj)) {
            try {
                $retryUrl = $obj->getField('retryUrl');
                $isShow = $obj->getField('retryBtnShow');
            } catch (\ErrorException $e) {
                var_log('error#retryBtn#' . $e->getMessage());
                return '';
            }
        } else {
            $retryUrl = $obj;
            $isShow = $retryBtnShow;
        }
        return $isShow ? \Prj\Misc\View::btnAjax('重试', $retryUrl, '确定重发请求?') : '';
    }

    public static $retryUrl = '';

    /**
     * 导出报表按钮组
     * @param string $exportAllUrl 导出全部的url
     * @param string $exportSelUrl 导出选中的url
     * @return html
     */
    public static function exportBox($exportAllUrl = null, $exportSelUrl = null) {       
        return include APP_PATH . '/application/views/plugins/exportbox.www.phtml';
    }
    
    /**
     * 通过手机号查找用户ID
     * @param string $url 查询的url
     * @param string $htmlId 需要查找的html输入框id
     * @return html
     */
    public static function phoneBox($url, $htmlId = '_userId_eq') {
        return include APP_PATH . '/application/views/plugins/phonebox.www.phtml';
    }

}
