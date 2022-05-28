<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 影片基础信息表
 * Class Movie
 *
 * @since 2.0
 *
 * @Entity(table="movie")
 */
class Movie extends Model
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
     * 番号源
     *
     * @Column(name="number_source", prop="numberSource")
     *
     * @var string|null
     */
    private $numberSource;

    /**
     * 影片名称
     *
     * @Column()
     *
     * @var string|null
     */
    private $name;

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
     * 发行
     *
     * @Column()
     *
     * @var string|null
     */
    private $issued;

    /**
     * 卖家
     *
     * @Column()
     *
     * @var string|null
     */
    private $sell;

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
     * 评分-计算过后的
     *
     * @Column()
     *
     * @var float|null
     */
    private $score;

    /**
     * 评分人数-冗余
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
     * 评分-采集
     *
     * @Column(name="collection_score", prop="collectionScore")
     *
     * @var float
     */
    private $collectionScore;

    /**
     * 评分人数-冗余-采集
     *
     * @Column(name="collection_score_people", prop="collectionScorePeople")
     *
     * @var int
     */
    private $collectionScorePeople;

    /**
     * 评论数-采集
     *
     * @Column(name="collection_comment_num", prop="collectionCommentNum")
     *
     * @var int
     */
    private $collectionCommentNum;

    /**
     * 想看数量-冗余
     *
     * @Column(name="wan_see", prop="wanSee")
     *
     * @var int
     */
    private $wanSee;

    /**
     * 看过数量-冗余
     *
     * @Column()
     *
     * @var int
     */
    private $seen;

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
     * 状态 1.正常  2.禁用
     *
     * @Column()
     *
     * @var int|null
     */
    private $status;

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
     * 状态 1.普通  2.热门
     *
     * @Column(name="is_hot", prop="isHot")
     *
     * @var int|null
     */
    private $isHot;

    /**
     * 状态  1.上架  2.下架
     *
     * @Column(name="is_up", prop="isUp")
     *
     * @var int|null
     */
    private $isUp;

    /**
     * 状态 1.不含短评  2.含短评
     *
     * @Column(name="is_short_comment", prop="isShortComment")
     *
     * @var int|null
     */
    private $isShortComment;

    /**
     * 最新评论时间 -冗余
     *
     * @Column(name="new_comment_time", prop="newCommentTime")
     *
     * @var string|null
     */
    private $newCommentTime;

    /**
     * 磁链更新时间
     *
     * @Column(name="flux_linkage_time", prop="fluxLinkageTime")
     *
     * @var string|null
     */
    private $fluxLinkageTime;

    /**
     * 源数据ID
     *
     * @Column()
     *
     * @var int
     */
    private $oid;

    /**
     * 分类ID
     *
     * @Column()
     *
     * @var int
     */
    private $cid;

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
     * @param float $collectionScore
     *
     * @return self
     */
    public function setCollectionScore(float $collectionScore): self
    {
        $this->collectionScore = $collectionScore;

        return $this;
    }

    /**
     * @param int $collectionScorePeople
     *
     * @return self
     */
    public function setCollectionScorePeople(int $collectionScorePeople): self
    {
        $this->collectionScorePeople = $collectionScorePeople;

        return $this;
    }

    /**
     * @param int $collectionCommentNum
     *
     * @return self
     */
    public function setCollectionCommentNum(int $collectionCommentNum): self
    {
        $this->collectionCommentNum = $collectionCommentNum;

        return $this;
    }

    /**
     * @param int $wanSee
     *
     * @return self
     */
    public function setWanSee(int $wanSee): self
    {
        $this->wanSee = $wanSee;

        return $this;
    }

    /**
     * @param int $seen
     *
     * @return self
     */
    public function setSeen(int $seen): self
    {
        $this->seen = $seen;

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
     * @param int|null $isHot
     *
     * @return self
     */
    public function setIsHot(?int $isHot): self
    {
        $this->isHot = $isHot;

        return $this;
    }

    /**
     * @param int|null $isUp
     *
     * @return self
     */
    public function setIsUp(?int $isUp): self
    {
        $this->isUp = $isUp;

        return $this;
    }

    /**
     * @param int|null $isShortComment
     *
     * @return self
     */
    public function setIsShortComment(?int $isShortComment): self
    {
        $this->isShortComment = $isShortComment;

        return $this;
    }

    /**
     * @param string|null $newCommentTime
     *
     * @return self
     */
    public function setNewCommentTime(?string $newCommentTime): self
    {
        $this->newCommentTime = $newCommentTime;

        return $this;
    }

    /**
     * @param string|null $fluxLinkageTime
     *
     * @return self
     */
    public function setFluxLinkageTime(?string $fluxLinkageTime): self
    {
        $this->fluxLinkageTime = $fluxLinkageTime;

        return $this;
    }

    /**
     * @param int $oid
     *
     * @return self
     */
    public function setOid(int $oid): self
    {
        $this->oid = $oid;

        return $this;
    }

    /**
     * @param int $cid
     *
     * @return self
     */
    public function setCid(int $cid): self
    {
        $this->cid = $cid;

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
    public function getName(): ?string
    {
        return $this->name;
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
    public function getIssued(): ?string
    {
        return $this->issued;
    }

    /**
     * @return string|null
     */
    public function getSell(): ?string
    {
        return $this->sell;
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
     * @return float
     */
    public function getCollectionScore(): ?float
    {
        return $this->collectionScore;
    }

    /**
     * @return int
     */
    public function getCollectionScorePeople(): ?int
    {
        return $this->collectionScorePeople;
    }

    /**
     * @return int
     */
    public function getCollectionCommentNum(): ?int
    {
        return $this->collectionCommentNum;
    }

    /**
     * @return int
     */
    public function getWanSee(): ?int
    {
        return $this->wanSee;
    }

    /**
     * @return int
     */
    public function getSeen(): ?int
    {
        return $this->seen;
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
    public function getStatus(): ?int
    {
        return $this->status;
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
    public function getIsHot(): ?int
    {
        return $this->isHot;
    }

    /**
     * @return int|null
     */
    public function getIsUp(): ?int
    {
        return $this->isUp;
    }

    /**
     * @return int|null
     */
    public function getIsShortComment(): ?int
    {
        return $this->isShortComment;
    }

    /**
     * @return string|null
     */
    public function getNewCommentTime(): ?string
    {
        return $this->newCommentTime;
    }

    /**
     * @return string|null
     */
    public function getFluxLinkageTime(): ?string
    {
        return $this->fluxLinkageTime;
    }

    /**
     * @return int
     */
    public function getOid(): ?int
    {
        return $this->oid;
    }

    /**
     * @return int
     */
    public function getCid(): ?int
    {
        return $this->cid;
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
