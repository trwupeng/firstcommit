<?php
namespace Lib\Services\evts;

class onLogin
{
    /**
     *
     *@param \Sooh\Base\Log\Data $data
     *@return void
     */
    public function run($data)
    {
        error_log('###EVT###' . __CLASS__);
        $this->messageQueue($data);
    }

    /**
     * @param \Sooh\Base\Log\Data $data data
     */
    public function messageQueue($data)
    {
        $strFlag = $data->sarg1;
        $arrResChanged = $data->resChanged;
    }
}