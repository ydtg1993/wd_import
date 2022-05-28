<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 影片评分记录
 * Class MovieScoreNotes
 *
 * @since 2.0
 *
 * @Entity(table="movie_score_notes")
 */
class MovieScoreNotes extends Model
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
     * 影片ID
     *
     * @Column()
     *
     * @var int
     */
    private $mid;

    /**
     * 用户ID
     *
     * @Column()
     *
     * @var int
     */
    private $uid;

    /**
     * 1.正常计分  2.取消计分
     *
     * @Column()
     *
     * @var int|null
     */
    private $status;

    /**
     * 源数据ID
     *
     * @Column()
     *
     * @var int
     */
    private $oid;

    /**
     * 评分
     *
     * @Column()
     *
     * @var float
     */
    private $score;

    /**
     * 来源采集ID - 只有source_type 为3时生效
     *
     * @Column(name="collection_id", prop="collectionId")
     *
     * @var int
     */
    private $collectionId;

    /**
     * 模拟 用户昵称 --目前只有采集的才会
     *
     * @Column()
     *
     * @var string|null
     */
    private $nickname;

    /**
     * 来源类型 1.用户评分 2.虚拟用户评分 3.采集评分
     *
     * @Column(name="source_type", prop="sourceType")
     *
     * @var int|null
     */
    private $sourceType;

    /**
     * 评分时间
     *
     * @Column(name="score_time", prop="scoreTime")
     *
     * @var string|null
     */
    private $scoreTime;

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
     * @param int $mid
     *
     * @return self
     */
    public function setMid(int $mid): self
    {
        $this->mid = $mid;

        return $this;
    }

    /**
     * @param int $uid
     *
     * @return self
     */
    public function setUid(int $uid): self
    {
        $this->uid = $uid;

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
     * @param float $score
     *
     * @return self
     */
    public function setScore(float $score): self
    {
        $this->score = $score;

        return $this;
    }

    /**
     * @param int $collectionId
     *
     * @return self
     */
    public function setCollectionId(int $collectionId): self
    {
        $this->collectionId = $collectionId;

        return $this;
    }

    /**
     * @param string|null $nickname
     *
     * @return self
     */
    public function setNickname(?string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * @param int|null $sourceType
     *
     * @return self
     */
    public function setSourceType(?int $sourceType): self
    {
        $this->sourceType = $sourceType;

        return $this;
    }

    /**
     * @param string|null $scoreTime
     *
     * @return self
     */
    public function setScoreTime(?string $scoreTime): self
    {
        $this->scoreTime = $scoreTime;

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
     * @return int
     */
    public function getMid(): ?int
    {
        return $this->mid;
    }

    /**
     * @return int
     */
    public function getUid(): ?int
    {
        return $this->uid;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getOid(): ?int
    {
        return $this->oid;
    }

    /**
     * @return float
     */
    public function getScore(): ?float
    {
        return $this->score;
    }

    /**
     * @return int
     */
    public function getCollectionId(): ?int
    {
        return $this->collectionId;
    }

    /**
     * @return string|null
     */
    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    /**
     * @return int|null
     */
    public function getSourceType(): ?int
    {
        return $this->sourceType;
    }

    /**
     * @return string|null
     */
    public function getScoreTime(): ?string
    {
        return $this->scoreTime;
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
