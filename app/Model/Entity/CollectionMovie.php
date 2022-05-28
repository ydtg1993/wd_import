<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 采集影片信息表
 * Class CollectionMovie
 *
 * @since 2.0
 *
 * @Entity(table="collection_movie")
 */
class CollectionMovie extends Model
{
    /**
     * 
     * @Id()
     * @Column()
     *
     * @var int
     */
    private $id;

    /**
     * 番号
     *
     * @Column()
     *
     * @var string|null
     */
    private $number;

    /**
     * 源番号
     *
     * @Column(name="number_source", prop="numberSource")
     *
     * @var string|null
     */
    private $numberSource;

    /**
     * 番号名称
     *
     * @Column(name="number_name", prop="numberName")
     *
     * @var string|null
     */
    private $numberName;

    /**
     * 影片名称/标题
     *
     * @Column()
     *
     * @var string|null
     */
    private $name;

    /**
     * 来源网站
     *
     * @Column(name="source_site", prop="sourceSite")
     *
     * @var string|null
     */
    private $sourceSite;

    /**
     * 来源路径
     *
     * @Column(name="source_url", prop="sourceUrl")
     *
     * @var string|null
     */
    private $sourceUrl;

    /**
     * 导演名称
     *
     * @Column()
     *
     * @var string|null
     */
    private $director;

    /**
     * 卖家
     *
     * @Column()
     *
     * @var string|null
     */
    private $sell;

    /**
     * 播放时长/秒
     *
     * @Column()
     *
     * @var int|null
     */
    private $time;

    /**
     * 发布/发行时间
     *
     * @Column(name="release_time", prop="releaseTime")
     *
     * @var string|null
     */
    private $releaseTime;

    /**
     * 小封面
     *
     * @Column(name="small_cover", prop="smallCover")
     *
     * @var string|null
     */
    private $smallCover;

    /**
     * 大封面
     *
     * @Column(name="big_cove", prop="bigCove")
     *
     * @var string|null
     */
    private $bigCove;

    /**
     * 预告片
     *
     * @Column()
     *
     * @var string|null
     */
    private $trailer;

    /**
     * json 数组 其他组图-预览图
     *
     * @Column()
     *
     * @var string|null
     */
    private $map;

    /**
     * 系列
     *
     * @Column()
     *
     * @var string|null
     */
    private $series;

    /**
     * 片商
     *
     * @Column(name="film_companies", prop="filmCompanies")
     *
     * @var string|null
     */
    private $filmCompanies;

    /**
     * 发行
     *
     * @Column()
     *
     * @var string|null
     */
    private $issued;

    /**
     * json 数组 演员
     *
     * @Column()
     *
     * @var string|null
     */
    private $actor;

    /**
     * 类别
     *
     * @Column()
     *
     * @var string|null
     */
    private $category;

    /**
     * json 数组 标签
     *
     * @Column()
     *
     * @var string|null
     */
    private $label;

    /**
     * 评分
     *
     * @Column()
     *
     * @var float|null
     */
    private $score;

    /**
     * 评分人数
     *
     * @Column(name="score_people", prop="scorePeople")
     *
     * @var int
     */
    private $scorePeople;

    /**
     * 评论数
     *
     * @Column(name="comment_num", prop="commentNum")
     *
     * @var int
     */
    private $commentNum;

    /**
     * json 数组 评论数组
     *
     * @Column()
     *
     * @var string|null
     */
    private $comment;

    /**
     * 验证网址
     *
     * @Column(name="actual_source", prop="actualSource")
     *
     * @var string|null
     */
    private $actualSource;

    /**
     * 磁链信息数
     *
     * @Column(name="flux_linkage_num", prop="fluxLinkageNum")
     *
     * @var int
     */
    private $fluxLinkageNum;

    /**
     * json 数组 磁链信息
     *
     * @Column(name="flux_linkage", prop="fluxLinkage")
     *
     * @var string|null
     */
    private $fluxLinkage;

    /**
     * 状态 1.不可下载  2.可下载
     *
     * @Column(name="is_download", prop="isDownload")
     *
     * @var int|null
     */
    private $isDownload;

    /**
     * 状态 1.不含字幕  2.含字幕
     *
     * @Column(name="is_subtitle", prop="isSubtitle")
     *
     * @var int|null
     */
    private $isSubtitle;

    /**
     * 状态 1.是今日新种  2.否今日新种
     *
     * @Column(name="is_new", prop="isNew")
     *
     * @var int|null
     */
    private $isNew;

    /**
     * json 数组 异常数据ID组
     *
     * @Column(name="abnormal_data_id", prop="abnormalDataId")
     *
     * @var string|null
     */
    private $abnormalDataId;

    /**
     * 1.未处理  2.已处理【人工处理】 3.系统处理 4.舍弃 5.异常数据需要人工处理
     *
     * @Column()
     *
     * @var int|null
     */
    private $status;

    /**
     * 1.未处理  2.已下载  3.处理异常-部分未下载需要人工审核
     *
     * @Column(name="resources_status", prop="resourcesStatus")
     *
     * @var int|null
     */
    private $resourcesStatus;

    /**
     * json 数组 资源信息
     *
     * @Column(name="resources_info", prop="resourcesInfo")
     *
     * @var string|null
     */
    private $resourcesInfo;

    /**
     * json 数组 资源异常信息
     *
     * @Column(name="resources_odd_info", prop="resourcesOddInfo")
     *
     * @var string|null
     */
    private $resourcesOddInfo;

    /**
     * 处理用户id
     *
     * @Column(name="admin_id", prop="adminId")
     *
     * @var int
     */
    private $adminId;

    /**
     * 处理计数防止处理过快导致部分视频附属属性还未处理
     *
     * @Column(name="dis_sum", prop="disSum")
     *
     * @var int
     */
    private $disSum;

    /**
     * 源数据ID
     *
     * @Column(name="original_id", prop="originalId")
     *
     * @var int
     */
    private $originalId;

    /**
     * 同步爬取时间
     *
     * @Column()
     *
     * @var string|null
     */
    private $ctime;

    /**
     * 同步更新时间
     *
     * @Column()
     *
     * @var string|null
     */
    private $utime;

    /**
     * 创建时间
     *
     * @Column(name="created_at", prop="createdAt")
     *
     * @var string|null
     */
    private $createdAt;

    /**
     * 更新时间
     *
     * @Column(name="updated_at", prop="updatedAt")
     *
     * @var string|null
     */
    private $updatedAt;


    /**
     * @param int $id
     *
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param string|null $number
     *
     * @return self
     */
    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @param string|null $numberSource
     *
     * @return self
     */
    public function setNumberSource(?string $numberSource): self
    {
        $this->numberSource = $numberSource;

        return $this;
    }

    /**
     * @param string|null $numberName
     *
     * @return self
     */
    public function setNumberName(?string $numberName): self
    {
        $this->numberName = $numberName;

        return $this;
    }

    /**
     * @param string|null $name
     *
     * @return self
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string|null $sourceSite
     *
     * @return self
     */
    public function setSourceSite(?string $sourceSite): self
    {
        $this->sourceSite = $sourceSite;

        return $this;
    }

    /**
     * @param string|null $sourceUrl
     *
     * @return self
     */
    public function setSourceUrl(?string $sourceUrl): self
    {
        $this->sourceUrl = $sourceUrl;

        return $this;
    }

    /**
     * @param string|null $director
     *
     * @return self
     */
    public function setDirector(?string $director): self
    {
        $this->director = $director;

        return $this;
    }

    /**
     * @param string|null $sell
     *
     * @return self
     */
    public function setSell(?string $sell): self
    {
        $this->sell = $sell;

        return $this;
    }

    /**
     * @param int|null $time
     *
     * @return self
     */
    public function setTime(?int $time): self
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @param string|null $releaseTime
     *
     * @return self
     */
    public function setReleaseTime(?string $releaseTime): self
    {
        $this->releaseTime = $releaseTime;

        return $this;
    }

    /**
     * @param string|null $smallCover
     *
     * @return self
     */
    public function setSmallCover(?string $smallCover): self
    {
        $this->smallCover = $smallCover;

        return $this;
    }

    /**
     * @param string|null $bigCove
     *
     * @return self
     */
    public function setBigCove(?string $bigCove): self
    {
        $this->bigCove = $bigCove;

        return $this;
    }

    /**
     * @param string|null $trailer
     *
     * @return self
     */
    public function setTrailer(?string $trailer): self
    {
        $this->trailer = $trailer;

        return $this;
    }

    /**
     * @param string|null $map
     *
     * @return self
     */
    public function setMap(?string $map): self
    {
        $this->map = $map;

        return $this;
    }

    /**
     * @param string|null $series
     *
     * @return self
     */
    public function setSeries(?string $series): self
    {
        $this->series = $series;

        return $this;
    }

    /**
     * @param string|null $filmCompanies
     *
     * @return self
     */
    public function setFilmCompanies(?string $filmCompanies): self
    {
        $this->filmCompanies = $filmCompanies;

        return $this;
    }

    /**
     * @param string|null $issued
     *
     * @return self
     */
    public function setIssued(?string $issued): self
    {
        $this->issued = $issued;

        return $this;
    }

    /**
     * @param string|null $actor
     *
     * @return self
     */
    public function setActor(?string $actor): self
    {
        $this->actor = $actor;

        return $this;
    }

    /**
     * @param string|null $category
     *
     * @return self
     */
    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @param string|null $label
     *
     * @return self
     */
    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @param float|null $score
     *
     * @return self
     */
    public function setScore(?float $score): self
    {
        $this->score = $score;

        return $this;
    }

    /**
     * @param int $scorePeople
     *
     * @return self
     */
    public function setScorePeople(int $scorePeople): self
    {
        $this->scorePeople = $scorePeople;

        return $this;
    }

    /**
     * @param int $commentNum
     *
     * @return self
     */
    public function setCommentNum(int $commentNum): self
    {
        $this->commentNum = $commentNum;

        return $this;
    }

    /**
     * @param string|null $comment
     *
     * @return self
     */
    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @param string|null $actualSource
     *
     * @return self
     */
    public function setActualSource(?string $actualSource): self
    {
        $this->actualSource = $actualSource;

        return $this;
    }

    /**
     * @param int $fluxLinkageNum
     *
     * @return self
     */
    public function setFluxLinkageNum(int $fluxLinkageNum): self
    {
        $this->fluxLinkageNum = $fluxLinkageNum;

        return $this;
    }

    /**
     * @param string|null $fluxLinkage
     *
     * @return self
     */
    public function setFluxLinkage(?string $fluxLinkage): self
    {
        $this->fluxLinkage = $fluxLinkage;

        return $this;
    }

    /**
     * @param int|null $isDownload
     *
     * @return self
     */
    public function setIsDownload(?int $isDownload): self
    {
        $this->isDownload = $isDownload;

        return $this;
    }

    /**
     * @param int|null $isSubtitle
     *
     * @return self
     */
    public function setIsSubtitle(?int $isSubtitle): self
    {
        $this->isSubtitle = $isSubtitle;

        return $this;
    }

    /**
     * @param int|null $isNew
     *
     * @return self
     */
    public function setIsNew(?int $isNew): self
    {
        $this->isNew = $isNew;

        return $this;
    }

    /**
     * @param string|null $abnormalDataId
     *
     * @return self
     */
    public function setAbnormalDataId(?string $abnormalDataId): self
    {
        $this->abnormalDataId = $abnormalDataId;

        return $this;
    }

    /**
     * @param int|null $status
     *
     * @return self
     */
    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param int|null $resourcesStatus
     *
     * @return self
     */
    public function setResourcesStatus(?int $resourcesStatus): self
    {
        $this->resourcesStatus = $resourcesStatus;

        return $this;
    }

    /**
     * @param string|null $resourcesInfo
     *
     * @return self
     */
    public function setResourcesInfo(?string $resourcesInfo): self
    {
        $this->resourcesInfo = $resourcesInfo;

        return $this;
    }

    /**
     * @param string|null $resourcesOddInfo
     *
     * @return self
     */
    public function setResourcesOddInfo(?string $resourcesOddInfo): self
    {
        $this->resourcesOddInfo = $resourcesOddInfo;

        return $this;
    }

    /**
     * @param int $adminId
     *
     * @return self
     */
    public function setAdminId(int $adminId): self
    {
        $this->adminId = $adminId;

        return $this;
    }

    /**
     * @param int $disSum
     *
     * @return self
     */
    public function setDisSum(int $disSum): self
    {
        $this->disSum = $disSum;

        return $this;
    }

    /**
     * @param int $originalId
     *
     * @return self
     */
    public function setOriginalId(int $originalId): self
    {
        $this->originalId = $originalId;

        return $this;
    }

    /**
     * @param string|null $ctime
     *
     * @return self
     */
    public function setCtime(?string $ctime): self
    {
        $this->ctime = $ctime;

        return $this;
    }

    /**
     * @param string|null $utime
     *
     * @return self
     */
    public function setUtime(?string $utime): self
    {
        $this->utime = $utime;

        return $this;
    }

    /**
     * @param string|null $createdAt
     *
     * @return self
     */
    public function setCreatedAt(?string $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @param string|null $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt(?string $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * @return string|null
     */
    public function getNumberSource(): ?string
    {
        return $this->numberSource;
    }

    /**
     * @return string|null
     */
    public function getNumberName(): ?string
    {
        return $this->numberName;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getSourceSite(): ?string
    {
        return $this->sourceSite;
    }

    /**
     * @return string|null
     */
    public function getSourceUrl(): ?string
    {
        return $this->sourceUrl;
    }

    /**
     * @return string|null
     */
    public function getDirector(): ?string
    {
        return $this->director;
    }

    /**
     * @return string|null
     */
    public function getSell(): ?string
    {
        return $this->sell;
    }

    /**
     * @return int|null
     */
    public function getTime(): ?int
    {
        return $this->time;
    }

    /**
     * @return string|null
     */
    public function getReleaseTime(): ?string
    {
        return $this->releaseTime;
    }

    /**
     * @return string|null
     */
    public function getSmallCover(): ?string
    {
        return $this->smallCover;
    }

    /**
     * @return string|null
     */
    public function getBigCove(): ?string
    {
        return $this->bigCove;
    }

    /**
     * @return string|null
     */
    public function getTrailer(): ?string
    {
        return $this->trailer;
    }

    /**
     * @return string|null
     */
    public function getMap(): ?string
    {
        return $this->map;
    }

    /**
     * @return string|null
     */
    public function getSeries(): ?string
    {
        return $this->series;
    }

    /**
     * @return string|null
     */
    public function getFilmCompanies(): ?string
    {
        return $this->filmCompanies;
    }

    /**
     * @return string|null
     */
    public function getIssued(): ?string
    {
        return $this->issued;
    }

    /**
     * @return string|null
     */
    public function getActor(): ?string
    {
        return $this->actor;
    }

    /**
     * @return string|null
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @return float|null
     */
    public function getScore(): ?float
    {
        return $this->score;
    }

    /**
     * @return int
     */
    public function getScorePeople(): ?int
    {
        return $this->scorePeople;
    }

    /**
     * @return int
     */
    public function getCommentNum(): ?int
    {
        return $this->commentNum;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @return string|null
     */
    public function getActualSource(): ?string
    {
        return $this->actualSource;
    }

    /**
     * @return int
     */
    public function getFluxLinkageNum(): ?int
    {
        return $this->fluxLinkageNum;
    }

    /**
     * @return string|null
     */
    public function getFluxLinkage(): ?string
    {
        return $this->fluxLinkage;
    }

    /**
     * @return int|null
     */
    public function getIsDownload(): ?int
    {
        return $this->isDownload;
    }

    /**
     * @return int|null
     */
    public function getIsSubtitle(): ?int
    {
        return $this->isSubtitle;
    }

    /**
     * @return int|null
     */
    public function getIsNew(): ?int
    {
        return $this->isNew;
    }

    /**
     * @return string|null
     */
    public function getAbnormalDataId(): ?string
    {
        return $this->abnormalDataId;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @return int|null
     */
    public function getResourcesStatus(): ?int
    {
        return $this->resourcesStatus;
    }

    /**
     * @return string|null
     */
    public function getResourcesInfo(): ?string
    {
        return $this->resourcesInfo;
    }

    /**
     * @return string|null
     */
    public function getResourcesOddInfo(): ?string
    {
        return $this->resourcesOddInfo;
    }

    /**
     * @return int
     */
    public function getAdminId(): ?int
    {
        return $this->adminId;
    }

    /**
     * @return int
     */
    public function getDisSum(): ?int
    {
        return $this->disSum;
    }

    /**
     * @return int
     */
    public function getOriginalId(): ?int
    {
        return $this->originalId;
    }

    /**
     * @return string|null
     */
    public function getCtime(): ?string
    {
        return $this->ctime;
    }

    /**
     * @return string|null
     */
    public function getUtime(): ?string
    {
        return $this->utime;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

}
