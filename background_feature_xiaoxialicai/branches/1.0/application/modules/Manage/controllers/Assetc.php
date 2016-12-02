<?php
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Asset as Asset;
include __DIR__.'/Asset.php';

class AssetcController extends \AssetController {
    public function doCheckAction(){
        $this->closeAndReloadPage();
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $do = $this->_request->get('do');
        $asset = Asset::getCopy($where['assetId']);
        $asset->load();
        if($do=='yes'){
            $asset->setField('status',\Prj\Consts\Asset::status_ok);
        }elseif($do=='no'){
            $asset->setField('status',\Prj\Consts\Asset::status_abandon);
        }else{
            return $this->returnError('操作失败');
        }
        try{
            $asset->update();
        }catch (\ErrorException $e){
            return $this->returnError('操作失败:'.$e->getMessage());
        }
        return $this->returnOK('ok');
    }
}