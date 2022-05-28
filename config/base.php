<?php
return [
    'name'  => 'Swoft framework 2.0',
    'debug' => env('SWOFT_DEBUG', 1),
    'CollectDataApi'=>env('COLLECTDATAAPI','http://vmadmin.yellowdouban.com'),//采集数据管理端接口域名
    'CollectResourcesUrl'=>env('COLLECTRESOURCESURL','http://35.72.160.109:8888/'),//采集资源域名
    'DisCountSync'=>env('DISCOUNTSYNC',500),//处理分页一次处理的量
    'DownResourcesSave'=>env('DOWN_RESOURCES_SAVE','/'),//资源保存路径
    'ProcessTime'=>env('PROCESS_TIME',60),//处理进程执行间隔 单位分钟
    'CollectProcessTime'=>env('COLLECT_PROCESS_TIME',60),//采集处理进程执行间隔 单位分钟
    'ResourcesProcessTime'=>env('RESOURCES_PROCESS_TIME',60),//资源处理进程执行间隔 单位分钟
    'WaitingDataTime'=>env('WAITING_DATA_TIME',200),//数据处理等待时间 单位小时
];
