<?php
/**
 * Created by PhpStorm.
 * User: night
 * Date: 2021/5/21
 * Time: 11:22
 */

namespace App\Model\Logic\DataProcessing;


use App\Http\Common\ErrorInfo;
use App\Model\Entity\CollectionLabel;
use Swoft\Log\Helper\CLog;

class TagLogic extends DataProcessingBaseLogic
{

    protected $type_dis = 3;//处理类型 1 是影片 2 是演员 3 是标签
    public function resolveHandle($data,$id = 0)
    {
        //初始化临时错误对象
        $this->errorTempInfo = new ErrorInfo();

        //处理类别数据
        $reCategoryData = $this->categoryDataHandle($data,$id);
        if(($this->errorTempInfo->code??500) != 200)
        {
            CLog::info('处理演员数据  原始数据ID：'.$id.'出现错误！错误说明：'.($this->errorTempInfo->msg??'未知错误'));
        }
        $this->errorTempInfo == null?($this->errorTempInfo = new ErrorInfo()):$this->errorTempInfo->reset();//重置错误信息

        //处理类别数据
        $reLabelData = $this->labelDataHandle($data,$id);
        if(($this->errorTempInfo->code??500) != 200)
        {
            CLog::info('处理演员数据  原始数据ID：'.$id.'出现错误！错误说明：'.($this->errorTempInfo->msg??'未知错误'));
        }
        $this->errorTempInfo == null?($this->errorTempInfo = new ErrorInfo()):$this->errorTempInfo->reset();//重置错误信息

        $reData = array();
        $reData['categoryData'] = $reCategoryData;
        $reData['labelData'] = $reLabelData;
        return $reData;
    }

    /**
     * 采集标签处理
     * @param $data
     * @return int|null
     */
    public function labelDataHandle($data,$id)
    {
        $this->errorTempInfo == null?($this->errorTempInfo = new ErrorInfo()):null;

        //读取标签数据
        foreach ($data as $k=>$val)
        {
            $group = $data['group']??'';
            $group = trim($group);
            if($k != 'db_name')
            {
                if($k != 'group')
                {
                    $k = trim($k);
                    if(is_array($val) && count($val) > 0)
                    {
                        foreach ($val as $value)
                        {
                            $value = trim($value);
                            $collectionLabel = CollectionLabel::where('category',$group)
                                ->where('name',$value)
                                ->where('name_child',$value)
                                ->firstArray();
                            if(($collectionLabel['id']??0)<=0)//没有该标签
                            {
                                //创建标签
                                $collectionLabelDb = new CollectionLabel();
                                $collectionLabelDb->setName($k);
                                $collectionLabelDb->setCategory($group);
                                $collectionLabelDb->setNameChild($value);
                                $collectionLabelDb->setOriginalId($id);
                                $collectionLabelDb->save();
                                $reData[] = $collectionLabelDb->getId();
                            }
                        }
                    }
                }

            }
        }

        return true;
    }

    /**
     * 下载资源 并生成保存新的资源以及资源路径 标签没有资源
     * @return bool
     */
    public function downResources($data = [])
    {

    }
}