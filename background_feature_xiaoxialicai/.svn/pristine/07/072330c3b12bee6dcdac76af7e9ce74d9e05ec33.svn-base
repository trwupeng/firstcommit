<?php
namespace Prj\WaresTpl\Std02;
use Prj\WaresTpl\Base;

/**
 * Description of Editor
 *
 * @author simon.wang
 */
class Editor extends Base
{
    public static $_allTabs = array(
        'a' => '项目详情',
        'b' => [
            'title'=>'借款人信息',
            'fields'=>[
                'name'=>'姓名',
                'idCard'=>'身份证号',
                'married'=>'婚姻状态',
                'rootAddr'=>'户籍所在地',
                'addr'=>'现居地',
                'reason'=>'借款原因',
            ]
        ],
        'c' => [
            'title'=>'抵押物信息',
            'fields'=>[
                'addr'=>'抵押物地址',
                'ymd'=>'购买日期',
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
                    'title'=>'户口本',
                    'fields'=>[
                        'name'=>'审核员',
                        'img'=>'明细',
                    ]
                ],
                'married'=>[
                    'title'=>'婚姻状态',
                    'fields'=>[
                        'name'=>'审核员',
                        'img'=>'明细',
                    ]
                ],
                'credit'=>[
                    'title'=>'征信报告',
                    'fields'=>[
                        'name'=>'审核员',
                        'img'=>'明细',
                    ]
                ],
                'inspect'=>[
                    'title'=>'实地考察图册',
                    'fields'=>[
                        'name'=>'审核员',
                        'img'=>'明细',
                    ]
                ],
                'contract'=>[
                    'title'=>'借款抵押保证合同',
                    'fields'=>[
                        'name'=>'审核员',
                        'img'=>'明细',
                    ]
                ],
                'receipt'=>[
                    'title'=>'转账回执',
                    'fields'=>[
                        'name'=>'审核员',
                        'img'=>'明细',
                    ]
                ],
                'property'=>[
                    'title'=>'房产证',
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
            'married'=>'',
            'rootAddr'=>'',
            'addr'=>'',
            'reason'=>'',
        ],
        'c'=>[
            'addr'=>'',
            'ymd'=>'',
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
            'married'=>[
                'name'=>'',
                'img'=>[],
            ],
            'credit'=>[
                'name'=>'',
                'img'=>[],
            ],
            'inspect'=>[
                'name'=>'',
                'img'=>[],
            ],
            'contract'=>[
                'name'=>'',
                'img'=>[],
            ],
            'receipt'=>[
                'name'=>'',
                'img'=>[],
            ],
            'property'=>[
                'name'=>'',
                'img'=>[],
            ]
        ]
    ];

    protected function contentOut(){
        self::$content['c']['price']/=1000000;
        self::$content['c']['ymd'] = date('Y-m-d',strtotime(self::$content['c']['ymd']));
    }

    protected function contentIn(){
        self::$content['c']['price']*=1000000;
        self::$content['c']['ymd'] = date('Ymd',strtotime(self::$content['c']['ymd']));
    }


}