<?php
namespace Prj\WaresTpl\Std03;
use Prj\WaresTpl\Base;

/**
 * Description of Editor
 *  车贷模板
 * @author simon.wang
 */
class Editor extends  Base
{
    public static $_allTabs = array(
        'a' => '项目详情',
        'b' => [
            'title'=>'借款人信息',
            'fields'=>[
                'name'=>'姓名',
                'idCard'=>'身份证',
                'reason'=>'借款原因',
            ]
        ],
        'c' => [
            'title'=>'抵押物信息',
            'fields'=>[
                'brand'=>'车辆品牌',
                'idCar'=>'车牌号',
                'meter'=>'行驶公里数(km)',
                //'addr'=>'抵押物地址',
                'ymd'=>'购买日期',
                'buy'=>'购买价(单位万)',
                'price'=>'评估价(单位万)',
            ]
        ],
        'd' => [
            'title'=>'安全审核',
            'fields'=>[
                'id'=>[
                    'title'=>'身份证',
                    'fields'=>[
                        'name'=>'审核员',
                        'img'=>'明细',
                    ]
                ],
                'book'=>[
                    'title'=>'车辆质押借款合同',
                    'fields'=>[
                        'name'=>'审核员',
                        'img'=>'明细',
                    ]
                ],
                'drive'=>[
                    'title'=>'行驶证',
                    'fields'=>[
                        'name'=>'审核员',
                        'img'=>'明细',
                    ]
                ],
                'bill'=>[
                    'title'=>'购车发票',
                    'fields'=>[
                        'name'=>'审核员',
                        'img'=>'明细',
                    ]
                ],
                'tax'=>[
                    'title'=>'购置税完税证',
                    'fields'=>[
                        'name'=>'审核员',
                        'img'=>'明细',
                    ]
                ],
                'key'=>[
                    'title'=>'车辆钥匙',
                    'fields'=>[
                        'name'=>'审核员',
                        'img'=>'明细',
                    ]
                ],

            ]
        ]
    );

    public static $contentStructure = [  //默认值
        'a'=>'',
        'b'=>[
            'name'=>'',
            'idCard'=>'',
            'reason'=>'',
        ],
        'c'=>[
            'brand'=>'',
            'idCar'=>'',
            'meter'=>'',
            //'addr'=>'抵押物地址',
            'ymd'=>'',
            'buy'=>'',
            'price'=>'',
        ],
        'd'=>[
            'id'=>[
                'name'=>'',
                'img'=>[],
            ],
            'book'=>[
                'name'=>'',
                'img'=>[],
            ],
            'drive'=>[
                'name'=>'',
                'img'=>[],
            ],
            'bill'=>[
                'name'=>'',
                'img'=>[],
            ],
            'tax'=>[
                'name'=>'',
                'img'=>[],
            ],
            'key'=>[
                'name'=>'',
                'img'=>[],
            ],

        ]
    ];





    protected function contentOut(){
        self::$content['c']['price']/=1000000;
        self::$content['c']['buy']/=1000000;
        self::$content['c']['ymd'] = date('Y-m-d',strtotime(self::$content['c']['ymd']));
    }

    protected function contentIn(){
        self::$content['c']['price']*=1000000;
        self::$content['c']['buy']*=1000000;
        self::$content['c']['ymd'] = date('Ymd',strtotime(self::$content['c']['ymd']));
    }


}