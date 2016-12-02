<?php
namespace Rpt\Misc\BaiduEcharts;
/**
 *
 * 漏斗图
 * 基本的漏斗图形封装
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/5/24 0024
 * Time: 下午 4:08
 */

class Funnel {


    public static function show ($elementId, $title, $subtitle='', $data, $series_name, $series_data) {
        $script = <<< EOF
<script type="text/javascript">
    // 基于准备好的dom，初始化echarts实例
    var $elementId = echarts.init(document.getElementById("$elementId"));

    // 指定图表的配置项和数据
    var option = {
        title: {
            text: "$title",
            subtext: "$subtitle",
            left: "right",
            top: "auto"
        },
        tooltip: {
            trigger: "item",
            formatter: "{b}"
        },
        toolbox: {
            orient: "vertical",
            top: "center",
            feature: {
                dataView: {readOnly: false},
                restore: {},
                saveAsImage: {}
            }
        },
        legend: {
            orient: "vertical",
            left: "auto",
            data: $data
        },
        calculable: true,
        series: [
            {
                name: "$series_name",
                type: "funnel",
                sort: "descending",
                maxSize: "80%",
                data:$series_data
            }
        ]
    };

    // 使用刚指定的配置项和数据显示图表。
    $elementId.setOption(option);
</script>
EOF;

        return $script;
    }
}