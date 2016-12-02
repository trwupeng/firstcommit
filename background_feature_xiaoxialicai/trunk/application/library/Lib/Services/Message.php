<?php
namespace Lib\Services;

/**
 * Class Message
 * @package Lib\Services
 * @author  LTM <605415184@qq.com>
 */
class Message
{

    /**
     * @var Message
     */
    public static $_instance = null;

    /**
     * @var \Sooh\Base\Rpc\Broker
     */
    protected $rpc;

    const errServerBusy = '服务器忙';
    const errMessageNotExist = '消息不存在';

    public static function getInstance($rpcOnNew = null)
    {
        if (self::$_instance === null) {
            $c                    = get_called_class();
            self::$_instance      = new $c;
            self::$_instance->rpc = $rpcOnNew;
        }

        return self::$_instance;
    }

    /**
     * 新增一条消息
     * @param string  $sendId     发送者ID
     * @param string  $receiverId 接受者ID或者all
     * @param integer $type       消息类型
     * @param string  $title      消息标题
     * @param string  $content    消息内容,当包含message的键时为自定义消息
     * @param array   $ext        扩展字段
     * @param boolean $isPush     是否推送，true推送，false不推送
     * @param string  $platform   推送平台
     * @return bool
     * @throws \Sooh\Base\ErrException
     */
    public function add($sendId, $receiverId, $type, $title, $content, $ext = null, $isPush = true, $platform = 'all')
    {
        if ($this->rpc !== null) {
            return $this->rpc->initArgs([
                'sendId'     => $sendId,
                'receiverId' => $receiverId,
                'type'       => $type,
                'title'      => $title,
                'content'    => $content,
                'ext'        => $ext,
                'isPush'     => $isPush,
                'platform'   => $platform,
            ])->send(__FUNCTION__);
        }

        if (is_numeric($type) && is_int($type + 0) && $type >= 1) {
        } else {
            error_log('message type not valid, type(serialize):' . serialize($type));
            throw new \Sooh\Base\ErrException(self::errServerBusy);
        }

        $msgId = mt_rand(1000, 9999) . sprintf('%06d', mt_rand(0, 999999)) . sprintf('%04d', substr($receiverId, -4));
        $i     = 0;
        while ($i < 10) {
            $dbMessage = \Prj\Data\Message::getCopy($msgId);
            $dbMessage->load();
            if ($dbMessage->exists()) {
                $i++;
            } else {
                break;
            }
        }
        if ($i >= 10) {
            throw new \Sooh\Base\ErrException(self::errServerBusy);
        }

        $dbMessage->setField('sendId', $sendId);
        $dbMessage->setField('receiverId', $receiverId);
        $dbMessage->setField('type', $type);
        $dbMessage->setField('title', $title);
        $dbMessage->setField('content', $content);
        $dbMessage->setField('status', \Prj\Consts\Message::status_unread);
        $dbMessage->setField('createTime', \Sooh\Base\Time::getInstance()->ymdhis());
        $ext && $dbMessage->setField('ext', json_encode($ext));

        $dbMessage->update();

        if ($isPush == true) {
            if (is_array($content) && array_key_exists('message', $content)) {
                \Lib\Services\Push::getInstance()->push($platform, $receiverId, null, $content['message']);
            } else {
                \Lib\Services\Push::getInstance()->push($platform, $receiverId, $content);
            }
        }
        return true;
    }

    /**
     * 新增一条消息
     * @param string $receiverId 接受者ID或者all
     * @param string $content    消息内容,当包含message的键时为自定义消息
     * @param string $platform   推送平台
     * @return bool
     * @throws \Sooh\Base\ErrException
     */
    public function push($receiverId, $content, $platform = 'all')
    {
        try {
            if (is_array($content) && array_key_exists('message', $content)) {
                \Lib\Services\Push::getInstance()->push($platform, $receiverId, null, $content['message']);
            } else {
                \Lib\Services\Push::getInstance()->push($platform, $receiverId, $content);
            }
        } catch (\Exception $e) {
            \Sooh\Base\Log\Data::getInstance()->ret    = 'push message';
            \Sooh\Base\Log\Data::getInstance()->target = $e->getMessage();
        }
    }

    /**
     * upd
     */
    public function upd($msgId, $status)
    {

    }

    /**
     * 获取综述
     * @param array $where 查询条件
     * @return integer
     */
    public function getCount($where)
    {
        return \Prj\Data\Message::getAccountNum($where);
    }

    /**
     * 读取消息
     * @param string|array $msgId  消息ID，多个用英文逗号隔开
     * @param string       $userId 用户ID
     * @return bool
     * @throws \Sooh\Base\ErrException
     */
    public function read($msgId, $userId)
    {
        if (is_string($msgId)) {
            $arr = explode(',', $msgId);
        } else {
            $arr = (array)$msgId;
        }

        $err  = false;
        $flag = [];
        foreach ($arr as $v) {
            $dbMessage = \Prj\Data\Message::getCopy($v);
            $dbMessage->load();
            if ($dbMessage->exists() && $dbMessage->getField('receiverId') === $userId) {
                $dbMessage->setField('status', \Prj\Consts\Message::status_read);
                $dbMessage->update();
                $flag[] = ['msgId' => $v, 'status' => $dbMessage->getField('status')];
            } else {
                $err = true;
                break;
            }
        }
        if ($err) {
            foreach ($flag as $v) {
                $dbMessage = \Prj\Data\Message::getCopy($v['msgId']);
                $dbMessage->load();
                $dbMessage->setField('status', $v['status']);
            }
            throw new \Sooh\Base\ErrException(self::errServerBusy);
        }
        return true;
    }

    /**
     * 全部设为已读
     * @param int $userId 用户ID
     * @return bool
     */
    public function readAll($userId)
    {
        return \Prj\Data\Message::readAll($userId);
    }

    /**
     * 读取消息
     * @param string|array $msgId  消息ID，多个用英文逗号隔开
     * @param string       $userId 用户ID
     * @return bool
     * @throws \Sooh\Base\ErrException
     */
    public function del($msgId, $userId)
    {
        if (is_string($msgId)) {
            $arr = explode(',', $msgId);
        } else {
            $arr = (array)$msgId;
        }

        foreach ($arr as $v) {
            $dbMessage = \Prj\Data\Message::getCopy($v);
            $dbMessage->load();
            if ($dbMessage->exists() && $dbMessage->getField('receiverId') === $userId) {
                $dbMessage->setField('status', \Prj\Consts\Message::status_abandon);
                $dbMessage->update();
                $flag[] = ['msgId' => $v, 'status' => $dbMessage->getField('status')];
            } else {
                $err = true;
                break;
            }
        }
        if ($err) {
            foreach ($flag as $v) {
                $dbMessage = \Prj\Data\Message::getCopy($v['msgId']);
                $dbMessage->load();
                $dbMessage->setField('status', $v['status']);
            }
            throw new \Sooh\Base\ErrException(self::errServerBusy);
        }
        return true;
    }

    /**
     * 获取消息列表
     * @param string  $receiverId 接收用户ID
     * @param integer $type       消息类型
     * @param integer $status     消息状态
     * @param integer $pageId     当前页数
     * @param integer $pageSize   每页大小
     * @return array ['list' => $list, 'countPage' => $countPage]
     */
    public function getList($receiverId, $type = null, $status = \Prj\Consts\Message::status_unread, $pageId = 1, $pageSize = 10)
    {
        $maps = ['receiverId' => $receiverId,];
        $status !== null && $maps['status'] = $status;
        !empty($type) && $maps['type'] = $type;

        $order = 'rsort createTime';

        $dbMsg = \Prj\Data\Message::getCopy($receiverId);
        $db    = $dbMsg->db();
        $tb    = $dbMsg->tbname();

        $page      = new \Sooh\DB\Pager($pageSize);
        $counts    = $db->getRecordCount($tb, $maps);
        $countPage = ceil($counts / $pageSize);
        $page->init($counts, $pageId);

        $ret = $db->getRecords($tb, '*', $maps, $order, $page->page_size, $page->rsFrom());
        foreach ($ret as &$v) {
            $v['title'] = html_entity_decode($v['title']);
            $v['content'] = html_entity_decode($v['content']);
        }
        return ['list' => $ret, 'countPage' => $countPage];
    }
}