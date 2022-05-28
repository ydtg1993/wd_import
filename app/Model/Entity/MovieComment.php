<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 影片评论表
 * Class MovieComment
 *
 * @since 2.0
 *
 * @Entity(table="movie_comment")
 */
class MovieComment extends Model
{
    /**
     * 模拟 头像 -- 目前只有采集的才会
     *
     * @Column()
     *
     * @var string|null
     */
    private $avatar;

    /**
     * 归属评论ID 0表示顶级评论
     *
     * @Column()
     *
     * @var int
     */
    private $cid;

    /**
     * 来源采集ID - 只有source_type 为3时生效
     *
     * @Column(name="collection_id", prop="collectionId")
     *
     * @var int
     */
    private $collectionId;

    /**
     * 评论记录
     *
     * @Column()
     *
     * @var string|null
     */
    private $comment;

    /**
     * 评论时间
     *
     * @Column(name="comment_time", prop="commentTime")
     *
     * @var string|null
     */
    private $commentTime;

    /**
     * 创建时间
     *
     * @Column(name="created_at", prop="createdAt")
     *
     * @var string|null
     */
    private $createdAt;

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
     * 模拟 用户昵称 --目前只有采集的才会
     *
     * @Column()
     *
     * @var string|null
     */
    private $nickname;

    /**
     * 源数据ID
     *
     * @Column()
     *
     * @var int
     */
    private $oid;

    /**
     * 回复的目标用户ID
     *
     * @Column(name="reply_uid", prop="replyUid")
     *
     * @var int
     */
    private $replyUid;

    /**
     * 评分0代表没有评分
     *
     * @Column()
     *
     * @var float|null
     */
    private $score;

    /**
     * 来源类型 1.用户评论 2.虚拟用户评论 3.采集评论
     *
     * @Column(name="source_type", prop="sourceType")
     *
     * @var int|null
     */
    private $sourceType;

    /**
     * 1.正常  2.删除
     *
     * @Column()
     *
     * @var int|null
     */
    private $status;

    /**
     * 评论类型 1.评论  2.回复
     *
     * @Column()
     *
     * @var int|null
     */
    private $type;

    /**
     * 用户ID/回复的用户ID
     *
     * @Column()
     *
     * @var int
     */
    private $uid;

    /**
     * 更新时间
     *
     * @Column(name="updated_at", prop="updatedAt")
     *
     * @var string|null
     */
    private $updatedAt;


    /**
     * @param string|null $avatar
     *
     * @return self
     */
    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

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
     * @param string|null $commentTime
     *
     * @return self
     */
    public function setCommentTime(?string $commentTime): self
    {
        $this->commentTime = $commentTime;

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
     * @param int $replyUid
     *
     * @return self
     */
    public function setReplyUid(int $replyUid): self
    {
        $this->replyUid = $replyUid;

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
     * @param int|null $type
     *
     * @return self
     */
    public function setType(?int $type): self
    {
        $this->type = $type;

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
     * @return string|null
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * @return int
     */
    public function getCid(): ?int
    {
        return $this->cid;
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
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @return string|null
     */
    public function getCommentTime(): ?string
    {
        return $this->commentTime;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
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
     * @return string|null
     */
    public function getNickname(): ?string
    {
        return $this->nickname;
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
    public function getReplyUid(): ?int
    {
        return $this->replyUid;
    }

    /**
     * @return float|null
     */
    public function getScore(): ?float
    {
        return $this->score;
    }

    /**
     * @return int|null
     */
    public function getSourceType(): ?int
    {
        return $this->sourceType;
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
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getUid(): ?int
    {
        return $this->uid;
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

}
