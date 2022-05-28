<?php
/**
 * Created by PhpStorm.
 * User: night
 * Date: 2021/5/27
 * Time: 8:56
 */

namespace App\Model\Logic\MovieData;


use App\Http\Common\Common;
use App\Model\Entity\CollectionComments;
use App\Model\Entity\CollectionMovie;
use App\Model\Entity\CollectionScore;
use App\Model\Entity\Movie;
use App\Model\Entity\MovieActor;
use App\Model\Entity\MovieActorAssociate;
use App\Model\Entity\MovieActorName;
use App\Model\Entity\MovieCategory;
use App\Model\Entity\MovieCategoryAssociate;
use App\Model\Entity\MovieComment;
use App\Model\Entity\MovieDirector;
use App\Model\Entity\MovieDirectorAssociate;
use App\Model\Entity\MovieFilmCompanies;
use App\Model\Entity\MovieFilmCompaniesAssociate;
use App\Model\Entity\MovieLabel;
use App\Model\Entity\MovieLabelAssociate;
use App\Model\Entity\MovieLabelCategory;
use App\Model\Entity\MovieLabelCategoryAssociate;
use App\Model\Entity\MovieNumber;
use App\Model\Entity\MovieNumberAssociate;
use App\Model\Entity\MovieScoreNotes;
use App\Model\Entity\MovieSeries;
use App\Model\Entity\MovieSeriesAssociate;
use Swoft\Log\Helper\CLog;

class MovieLogic extends MovieDataBaseLogic
{

    /**
     * 数据处理
     */
    public function dataRun()
    {
        $time = intval(config('WaitingDataTime',48));
        //$time = ($time<2||($time>(24*15)))?48:$time;

        $time = $time*60*60;
        $beginTime = time() - $time;
        //$sql = '(status = 1 or status = 4)';

        $count = CollectionMovie::where('created_at','>=',date('Y-m-d H:i:s',$beginTime))
           ->wherein('status',[1,4])
           ->wherein('resources_status',[2,3])
           ->count();
        // ->whereRaw($sql)->where('resources_status',2)
        $disSum = intval($count);

        $tempIndex = 0;
        $pageIndex = 1;//翻页从第一页开始
        $disCount = 500;
        //$disSum = 1;
        while ($tempIndex <= $disSum)
        {
            CLog::info('开始处理视频数据 第'.$pageIndex.'页数据！总共'.(ceil($disSum/$disCount)).'页!一次处理数据量：'.$disCount.'条');
            $handleData = CollectionMovie::where('created_at','>=',date('Y-m-d H:i:s',$beginTime))
                ->wherein('status',[1,4])
                ->where('resources_status',2)
                ->offset((($pageIndex - 1 ) * $disCount)<= 0 ? 0:(($pageIndex - 1 ) * $disCount))
                ->limit($disCount)->get();
            //$handleData = CollectionMovie::where('id',2)->get();
            //->whereRaw($sql)->where('resources_status',2)
            foreach ($handleData as $val)
            {

                $statusTemp = $val['status']??0;
                $resources_status = $val['resources_status']??0;

                if($resources_status != 2)
                {
                    CLog::info('resources_status处理跳过：'.$val['id'].$resources_status);
                    continue;
                }
                if(!($statusTemp == 1 || $statusTemp == 4 ))
                {
                    CLog::info('statusTemp处理跳过：'.$val['id'].$resources_status);
                    continue;
                }

                $this->errorInfo->reset();//重置错误信息
                $this->dataDis($val);

                $status= 3;
                if(($this->errorInfo->code??500)!=200)
                {
                    if(($this->errorInfo->code??500)!=300)
                    {
                        CLog::info('视频数据处理，同步视频数据出错！.错误说明：'.($this->errorInfo->msg??'未知错误'));
                        $status = 5;
                    }
                    else
                    {
                        CLog::info('视频数据处理，同步视频数据需要等待！.等待说明：'.($this->errorInfo->msg??'未知错误'));
                        CollectionMovie::where('id',$val['id']??0)->increment('dis_sum',1);
                        $status= 1;
                    }

                }

                //成功修改状态为已处理
                CollectionMovie::where('id',$val['id']??0)->update(
                    [
                        'status'=>$status
                    ]
                );
            }

            $tempIndex+=$disCount;
            $pageIndex++;
        }
    }

    /**
     * 指定数据处理
     * @param $data
     */
    public function dataDis($data)
    {
        if(($data['id']??0)<=0)
        {
            $this->errorInfo->setCode(500,'无效的影片数据！');
            return false;
        }

        $id = ($data['id']??0);
        $number = ($data['number']??'');
        if($number == '')
        {
            $this->errorInfo->setCode(500,'没有番号的影片数据！');
            return false;
        }
        $number = trim($number);
        $number_source = ($data['number_source']??'');
        $number_source = trim($number_source);
        $number_name = ($data['number_name']??'');
        $name = ($data['name']??'');
        $director = ($data['director']??'');//导演
        $sell= ($data['sell']??'');//卖家

        $time = ($data['time']??null);//到主库用秒
        $time = $time == null ?null:$time*60;
        $release_time = ($data['release_time']??null);//
        $series = ($data['series']??'');
        $film_companies = ($data['film_companies']??'');

        $issued = ($data['issued']??'');
        $actor = json_decode(($data['actor']??''),true);
        $category = ($data['category']??'');
        $label = json_decode(($data['label']??''),true);

        $score = ($data['score']??'');
        $score_people = ($data['score_people']??'');
        $comment_num = ($data['comment_num']??'');
        $comment = json_decode(($data['comment']??''),true);

        $flux_linkage_num = ($data['flux_linkage_num']??'');
        $flux_linkage = json_decode(($data['flux_linkage']??''),true);
        $is_download = ($data['is_download']??1);
        $is_subtitle = ($data['is_subtitle']??1);

        $is_new = ($data['is_new']??2);
        $resources_info = json_decode(($data['resources_info']??''),true);
        $small_cover = ($resources_info['small_cover']??'');
        $big_cove = ($resources_info['big_cove']??'');

        $trailer = ($resources_info['trailer']??'');
        $map = ($resources_info['map']??[]);
        $dis_sum = ($data['dis_sum']??0);
        $status = ($data['status']??1);

        //磁链数据处理
        $flux_linkage_time = 0;
        foreach ($flux_linkage as $valueFL)
        {
            if(($valueFL['time']??'')!='')
            {
                $timeTemp  = strtotime($valueFL['time']);
                $flux_linkage_time = (($timeTemp>$flux_linkage_time)?$timeTemp:$flux_linkage_time);
            }
        }
        $flux_linkage_time = date('Y-m-d H:i:s',($time == 0?1609430400:$time));

        //读取导演ID
        $directorId = 0;
        if($director != '')
        {
            $arrData = MovieDirector::where('name',$director)->firstArray();
            $directorId = ($arrData['id']??0);
            if($directorId <= 0)
            {
                $this->errorInfo->setCode((($dis_sum>=5)?500:300),(($dis_sum>=5)?'无效的导演数据':('导演数据：'.$director.' 延迟处理 id:'.$id)));
                return false;
            }
        }

        //读取系列ID
        $seriesId = 0;
        if($series != '')
        {
            $arrData = MovieSeries::where('name',$series)->firstArray();
            $seriesId = ($arrData['id']??0);
            if($seriesId <= 0)
            {
                $this->errorInfo->setCode((($dis_sum>=5)?500:300),(($dis_sum>=5)?'无效的系列数据':('系列数据：'.$series.' 延迟处理 id:'.$id)));
                return false;
            }
        }

        //读取片商ID
        $film_companiesId = 0;
        if($film_companies != '')
        {
            $arrData = MovieFilmCompanies::where('name',$film_companies)->firstArray();
            $film_companiesId = ($arrData['id']??0);
            if($film_companiesId <= 0)
            {
                $this->errorInfo->setCode((($dis_sum>=5)?500:300),(($dis_sum>=5)?'无效的片商数据':('片商数据：'.$film_companies.' 延迟处理 id:'.$id)));
                return false;
            }
        }

        //读取类别ID
        $categoryId = 0;
        if($category != '')
        {
            $category = trim($category);
            $arrData = MovieCategory::where('name',$category)->firstArray();
            $categoryId = ($arrData['id']??0);
            if($categoryId <= 0)
            {
                $this->errorInfo->setCode((($dis_sum>=5)?500:300),(($dis_sum>=5)?'无效的类别数据':('类别数据：'.$category.' 延迟处理 id:'.$id)));
                return false;
            }
        }

        //读取番号 名称
        $number_nameId = 0;
        if($number_name  != '')
        {
            $number_name = trim($number_name);
            $arrData = MovieNumber::where('name',$number_name)->firstArray();
            $number_nameId = ($arrData['id']??0);
            if($number_nameId <= 0)
            {
                $this->errorInfo->setCode((($dis_sum>=5)?500:300),(($dis_sum>=5)?'无效的番号数据':('番号数据：'.$number_name.' 延迟处理 id:'.$id)));
                return false;
            }
        }

        //读取标签
        $labelIds = [];
        if(is_array($label) && count($label) >0)
        {
            if($categoryId <= 0)
            {
                foreach ($label as $value)
                {
                    $value = trim($value);
                    $arrData = MovieLabel::where('name',$value)->where('cid',0)->firstArray();
                    $labelId = ($arrData['id']??0);
                    if($labelId <= 0)
                    {
                        $this->errorInfo->setCode((($dis_sum>=5)?500:300),(($dis_sum>=5)?'无效的标签数据':('标签数据：'.$value.' 延迟处理 id:'.$id)));
                        return false;
                    }
                    $labelIds[] = $labelId;
                }
            }
            else
            {
                foreach ($label as $value)
                {
                    $value = trim($value);
                    $arrData = MovieLabel::where('name',$value)->get()->toArray();
                    if(is_array($arrData) && count($arrData)>0)
                    {
                        $labelId = 0;
                        $labelCategory = MovieLabelCategory::where('name',$category)->firstArray();
                        $labelCategoryId = $labelCategory['id']??0;
                        if($labelCategoryId > 0)
                        {
                            foreach ($arrData as $TempVal)
                            {
                                $tempLabelCategoryAssociate = MovieLabelCategoryAssociate::where('cid',$labelCategoryId)->where('lid',$TempVal['id']??0)->firstArray();
                                $tempLabelCategoryAssociateId = $tempLabelCategoryAssociate['id']??0;
                                if($tempLabelCategoryAssociateId > 0)
                                {
                                    $labelId = $TempVal['id']??0;
                                    break;
                                }
                            }
                        }

                        if($labelId <= 0)
                        {
                            $this->errorInfo->setCode((($dis_sum>=5)?500:300),(($dis_sum>=5)?'无效的标签类别数据':('标签类别数据, 标签：'.$value.' 延迟处理 id:'.$id)));
                            return false;
                        }

                        $labelIds[] = $labelId;
                    }
                    else
                    {
                        $this->errorInfo->setCode((($dis_sum>=5)?500:300),(($dis_sum>=5)?'无效的标签数据':('标签数据：'.$value.' 延迟处理 id:'.$id)));
                        return false;
                    }

                }

            }

        }

        //读演员
        $actorIds = [];
        if(is_array($actor) && count($actor) >0)
        {
            foreach ($actor as $actorName)
            {
                $actorName = (is_array($actorName) && count($actorName)>0)?$actorName[0]:$actorName;
                $actorName = trim($actorName);
                $actorInfo = MovieActorName::where('name',$actorName)->firstArray();

                if(($actorInfo['aid']??0)<=0)
                {
                    $this->errorInfo->setCode((($dis_sum>=5)?500:300),(($dis_sum>=5)?'无效的演员数据':('演员数据：'.$actorName.' 延迟处理  id:'.$id)));
                    return false;
                }
                $actorIds[] = ($actorInfo['aid']??0);
            }
        }

        $movieInfo = Movie::where('number',$number)->firstArray();
        $movieId = ($movieInfo['id']??0);
        if($movieId >0 )
        {
            if($status == 4)
            {
                Movie::where('id',$movieId)
                    ->update([
                       'number'=> $number,
                        'name'=> $name,
                        'time'=> $time,
                        'release_time'=> $release_time,

                        'issued'=> $issued,
                        'sell'=> $sell,
                        'small_cover'=> $small_cover,
                        'big_cove'=> $big_cove,

                        'trailer'=> $trailer,
                        'map'=> json_encode($map),
                        'collection_score'=> $score,
                        'collection_score_people'=> $score_people,

                        'collection_comment_num'=> $comment_num,
                    //    'flux_linkage_num'=> count($flux_linkage),      //取消同步数据时，update磁链
                    //    'flux_linkage'=> json_encode($flux_linkage),
                        'status'=> 1,

                        'is_download'=> $is_download,
                        'is_subtitle'=> $is_subtitle,
                        'is_hot'=> 1,
                       'flux_linkage_time'=> $flux_linkage_time,
                        'oid'=> $id,
                        'cid'=> $categoryId,

                        'number_source'=> $number_source
                    ]);

                if($number_nameId > 0)
                {
                    $objTempArr = MovieNumberAssociate::where('nid',$number_nameId)->where('mid',$movieId)->firstArray();
                    if(($objTempArr['id']??0)<=0)
                    {
                        $objTemp = new MovieNumberAssociate();
                        $objTemp->setNid($number_nameId);
                        $objTemp->setMid($movieId);
                        $objTemp->save();
                    }
                }

                if($directorId > 0)
                {
                    $objTempArr = MovieDirectorAssociate::where('did',$directorId)->where('mid',$movieId)->firstArray();
                    if(($objTempArr['id']??0)<=0)
                    {
                        $objTemp = new MovieDirectorAssociate();
                        $objTemp->setDid($directorId);
                        $objTemp->setMid($movieId);
                        $objTemp->save();

                        $numCountTemp = MovieDirectorAssociate::where('did',$directorId)->where('status',1)->count();
                        MovieDirector::where('id',$directorId)->update(
                            [
                                'movie_sum'=>intval($numCountTemp),//防止出现对象
                            ]
                        );
                    }

                }

                if($seriesId > 0)
                {
                    $objTempArr = MovieSeriesAssociate::where('series_id',$seriesId)->where('mid',$movieId)->firstArray();
                    if(($objTempArr['id']??0)<=0)
                    {
                        $objTemp = new MovieSeriesAssociate();
                        $objTemp->setSeriesId($seriesId);
                        $objTemp->setMid($movieId);
                        $objTemp->save();

                        $numCountTemp = MovieSeriesAssociate::where('series_id',$seriesId)->where('status',1)->count();
                        MovieSeries::where('id',$seriesId)->update(
                            [
                                'movie_sum'=>intval($numCountTemp),//防止出现对象
                            ]
                        );
                    }

                }

                if($film_companiesId > 0)
                {
                    $objTempArr = MovieFilmCompaniesAssociate::where('film_companies_id',$film_companiesId)->where('mid',$movieId)->firstArray();
                    if(($objTempArr['id']??0)<=0)
                    {
                        $objTemp = new MovieFilmCompaniesAssociate();
                        $objTemp->setFilmCompaniesId($film_companiesId);
                        $objTemp->setMid($movieId);
                        $objTemp->save();

                        $numCountTemp = MovieFilmCompaniesAssociate::where('film_companies_id',$film_companiesId)->where('status',1)->count();
                        MovieFilmCompanies::where('id',$film_companiesId)->update(
                            [
                                'movie_sum'=>intval($numCountTemp),//防止出现对象
                            ]
                        );
                    }

                }

                if($categoryId > 0)
                {
                    $objTempArr = MovieCategoryAssociate::where('cid',$categoryId)->where('mid',$movieId)->firstArray();
                    if(($objTempArr['id']??0)<=0)
                    {
                        $objTemp = new MovieCategoryAssociate();
                        $objTemp->setCid($categoryId);
                        $objTemp->setMid($movieId);
                        $objTemp->save();
                    }

                }

                if(is_array($actorIds) && count($actorIds) )
                {
                    foreach ($actorIds as $actorIdVal)
                    {
                        $objTempArr = MovieActorAssociate::where('aid',$actorIdVal)->where('mid',$movieId)->firstArray();
                        if(($objTempArr['id']??0)<=0)
                        {
                            $objTemp = new MovieActorAssociate();
                            $objTemp->setAid($actorIdVal);
                            $objTemp->setMid($movieId);
                            $objTemp->save();
                        }
                    }

                    $numCountTemp = MovieActorAssociate::where('aid',$actorIdVal)->where('status',1)->count();
                    MovieActor::where('id',$actorIdVal)->update(
                        [
                            'movie_sum'=>intval($numCountTemp),//防止出现对象
                        ]
                    );
                }

                if(is_array($labelIds) && count($labelIds) )
                {
                    foreach ($labelIds as $labelIdVal)
                    {
                        $objTempArr = MovieLabelAssociate::where('cid',$labelIdVal)->where('mid',$movieId)->firstArray();
                        if(($objTempArr['id']??0)<=0)
                        {
                            $objTemp = new MovieLabelAssociate();
                            $objTemp->setCid($labelIdVal);
                            $objTemp->setMid($movieId);
                            $objTemp->save();
                        }

                    }
                }

                //评论关联处理
                //读取番号的评论数据
                $commentData = CollectionComments::where('number',$number)->where('status','<>',5)->get();

                foreach ($commentData as $commentDataVal)
                {
                    if(Common::isJapanese($commentDataVal['content']??''))//存在日语忽略
                    {
                        continue;
                    }
                    $objTempArr = MovieComment::where('collection_id',$commentDataVal['id']??0)
                        ->where('mid',$movieId)
                        ->where('nickname',$commentDataVal['user_name']??'')
                        ->where('source_type',3)
                        ->where('comment',$commentDataVal['content']??null)
                        ->where('comment_time',$commentDataVal['content_time']??null)
                        ->where('oid',$id)
                        ->firstArray();
                    if(($objTempArr['id']??0)<=0)
                    {
                        $objTemp = new MovieComment();
                        $objTemp->setMid($movieId);
                        $objTemp->setSourceType(3);
                        $objTemp->setComment($commentDataVal['content']??null);

                        $objTemp->setNickname($commentDataVal['user_name']??'');
                        $objTemp->setCollectionId($commentDataVal['id']??0);
                        $objTemp->setCommentTime(Common::isDateTime($commentDataVal['content_time']??null));
                        $objTemp->setType(1);

                        $objTemp->setScore($commentDataVal['score']??0);
                        $objTemp->setOid($id);
                        $objTemp->setStatus(1);
                        $objTemp->save();
                    }

                }

                //评分关联处理
                $scoreData = CollectionScore::where('number',$number)->where('status','<>',5)->get();
                foreach ($scoreData as $scoreDataVal)
                {
                    $objTempArr = MovieScoreNotes::where('collection_id',$scoreDataVal['id']??0)
                        ->where('mid',$movieId)
                        ->where('nickname',$scoreDataVal['user_name']??'')
                        ->where('source_type',3)
                        ->where('score_time',Common::isDateTime($scoreDataVal['content_time']??null))
                        ->where('score',$scoreDataVal['score']??null)
                        ->where('oid',$id)
                        ->firstArray();

                    if(($objTempArr['id']??0)<=0)
                    {
                        $objTemp = new MovieScoreNotes();
                        $objTemp->setMid($movieId);
                        $objTemp->setSourceType(3);
                        $objTemp->setScore($scoreDataVal['score']??0);

                        $objTemp->setNickname($scoreDataVal['user_name']??'');
                        $objTemp->setCollectionId($scoreDataVal['id']??0);
                        $objTemp->setScoreTime(Common::isDateTime($scoreDataVal['content_time']??null));
                        $objTemp->setStatus(1);

                        $objTemp->setOid($id);
                        $objTemp->save();
                    }

                    //计算评分
                    $peopleNotes = MovieScoreNotes::where('mid',$movieId)->where('source_type',1)->where('status',1)->count();
                    $scoreNotes = MovieScoreNotes::where('mid',$movieId)->where('source_type',1)->where('status',1)->sum('score');

                    if(($score_people + $peopleNotes) > 0)
                    {
                        $score = ($scoreNotes + ($score*$score_people))/($score_people + $peopleNotes);
                    }
                    else
                    {
                        $score = 0;
                    }
                }

                $numCountTemp = MovieComment::where('mid',$movieId)->where('status',1)->count();
                $numCountTempPeople = MovieScoreNotes::where('mid',$movieId)->where('status',1)->count();
                Movie::where('id',$movieId)->update([
                    'comment_num'=>intval($numCountTemp),
                    'score_people'=>intval($numCountTempPeople),
                    'score'=>$score,
                ]);

            }
        }
        else
        {
            $movieObj = new Movie();
            $movieObj->setName($name);
            $movieObj->setNumber($number);
            $movieObj->setOid($id);
            $movieObj->setCid($categoryId);

            $movieObj->setSell($sell);
            $movieObj->setTime($time);
            $movieObj->setReleaseTime($release_time);
            $movieObj->setIssued($issued);

            $movieObj->setCollectionScore($score);
            $movieObj->setCollectionScorePeople($score_people);
            $movieObj->setCollectionCommentNum($comment_num);
            $movieObj->setFluxLinkageNum(count($flux_linkage));

            $movieObj->setFluxLinkage(json_encode($flux_linkage));
            $movieObj->setIsDownload($is_download);
            $movieObj->setIsSubtitle($is_subtitle);
            $movieObj->setIsHot(1);

            $movieObj->setFluxLinkageTime($flux_linkage_time);
            $movieObj->setBigCove($big_cove);
            $movieObj->setSmallCover($small_cover);
            $movieObj->setTrailer($trailer);

            $movieObj->setMap(json_encode($map));
            $movieObj->setNumberSource($number_source);
            $movieObj->save();
            $movieId = $movieObj->getId();

            if($number_nameId > 0)
            {
                $objTemp = new MovieNumberAssociate();
                $objTemp->setNid($number_nameId);
                $objTemp->setMid($movieId);
                $objTemp->save();
                $numCountTemp = MovieNumberAssociate::where('nid',$number_nameId)->where('status',1)->count();
                MovieNumber::where('id',$directorId)->update(
                    [
                        'movie_sum'=>intval($numCountTemp),//防止出现对象
                    ]
                );

            }

            if($directorId > 0)
            {
                $objTemp = new MovieDirectorAssociate();
                $objTemp->setDid($directorId);
                $objTemp->setMid($movieId);
                $objTemp->save();

                $numCountTemp = MovieDirectorAssociate::where('did',$directorId)->where('status',1)->count();
                MovieDirector::where('id',$directorId)->update(
                    [
                        'movie_sum'=>intval($numCountTemp),//防止出现对象
                    ]
                );
            }

            if($seriesId > 0)
            {
                $objTemp = new MovieSeriesAssociate();
                $objTemp->setSeriesId($seriesId);
                $objTemp->setMid($movieId);
                $objTemp->save();

                $numCountTemp = MovieSeriesAssociate::where('series_id',$seriesId)->where('status',1)->count();
                MovieSeries::where('id',$seriesId)->update(
                    [
                        'movie_sum'=>intval($numCountTemp),//防止出现对象
                    ]
                );
            }

            if($film_companiesId > 0)
            {
                $objTemp = new MovieFilmCompaniesAssociate();
                $objTemp->setFilmCompaniesId($film_companiesId);
                $objTemp->setMid($movieId);
                $objTemp->save();

                $numCountTemp = MovieFilmCompaniesAssociate::where('film_companies_id',$film_companiesId)->where('status',1)->count();
                MovieFilmCompanies::where('id',$film_companiesId)->update(
                    [
                        'movie_sum'=>intval($numCountTemp),//防止出现对象
                    ]
                );
            }

            if($categoryId > 0)
            {
                $objTemp = new MovieCategoryAssociate();
                $objTemp->setCid($categoryId);
                $objTemp->setMid($movieId);
                $objTemp->save();
            }

            if(is_array($actorIds) && count($actorIds) )
            {
                foreach ($actorIds as $actorIdVal)
                {
                    $objTemp = new MovieActorAssociate();
                    $objTemp->setAid($actorIdVal);
                    $objTemp->setMid($movieId);
                    $objTemp->save();
                }

                $numCountTemp = MovieActorAssociate::where('aid',$actorIdVal)->where('status',1)->count();
                MovieActor::where('id',$actorIdVal)->update(
                    [
                        'movie_sum'=>intval($numCountTemp),//防止出现对象
                    ]
                );
            }

            if(is_array($labelIds) && count($labelIds) )
            {
                foreach ($labelIds as $labelIdVal)
                {
                    $objTemp = new MovieLabelAssociate();
                    $objTemp->setCid($labelIdVal);
                    $objTemp->setMid($movieId);
                    $objTemp->save();
                }
            }

            //评论关联处理
            //读取番号的评论数据
            $commentData = CollectionComments::where('number',$number)->where('status','<>',5)->get();
            foreach ($commentData as $commentDataVal)
            {
	    	if(Common::isJapanese($commentDataVal['content']??''))//存在日语忽略
                {
                        continue;
                }

                $objTemp = new MovieComment();
                $objTemp->setMid($movieId);
                $objTemp->setSourceType(3);
                $objTemp->setComment($commentDataVal['content']??null);

                $objTemp->setNickname($commentDataVal['user_name']??'');
                $objTemp->setCollectionId($commentDataVal['id']??0);
                $objTemp->setCommentTime(Common::isDateTime($commentDataVal['content_time']??null));
                $objTemp->setType(1);

                $objTemp->setScore($commentDataVal['score']??0);
                $objTemp->setOid($id);
                $objTemp->setStatus(1);
                $objTemp->save();
            }

            //评分关联处理
            $scoreData = CollectionScore::where('number',$number)->where('status','<>',5)->get();

            foreach ($scoreData as $scoreDataVal)
            {
                $objTemp = new MovieScoreNotes();
                $objTemp->setMid($movieId);
                $objTemp->setSourceType(3);
                $objTemp->setScore($scoreDataVal['score']??0);

                $objTemp->setNickname($scoreDataVal['user_name']??'');
                $objTemp->setCollectionId($scoreDataVal['id']??0);
                $objTemp->setScoreTime(Common::isDateTime($commentDataVal['content_time']??null));
                $objTemp->setStatus(1);

                $objTemp->setOid($id);
                $objTemp->save();
                //计算评分
                $peopleNotes = MovieScoreNotes::where('mid',$movieId)->where('source_type',1)->where('status',1)->count();
                $scoreNotes = MovieScoreNotes::where('mid',$movieId)->where('source_type',1)->where('status',1)->sum('score');

                if(($score_people + $peopleNotes) > 0)
                {
                    $score = ($scoreNotes + ($score*$score_people))/($score_people + $peopleNotes);
                }
                else
                {
                    $score = 0;
                }
            }

            $numCountTemp = MovieComment::where('mid',$movieId)->where('status',1)->count();
            $numCountTempPeople = MovieScoreNotes::where('mid',$movieId)->where('status',1)->count();
            Movie::where('id',$movieId)->update([
                'comment_num'=>intval($numCountTemp),
                'score_people'=>intval($numCountTempPeople),
                'score'=>intval($score),
            ]);

        }

        return $movieId;
    }



}
