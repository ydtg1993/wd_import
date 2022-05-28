<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 采集评论表
 * Class CollectionComments
 *
 * @since 2.0
 *
 * @Entity(table="collection_comments")
 */
class CollectionComments extends Model
{
    /**
     * 处理用户id
     *
     * @Column(name="admin_id", prop="adminId")
     *
     * @var int
     */
    private $adminId;

    /**
     * 评论的内容
     *
     * @Column()
     *
     * @var string|null
     */
    private $content;

    /**
     * 评论的时间
     *
     * @Column(name="content_time", prop="contentTime")
     *
     * @var string|null
     */
    private $contentTime;

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
     * 评论的番号
     *
     * @Column()
     *
     * @var string|null
     */
    private $number;

    /**
     * 源数据ID
     *
     * @Column(name="original_id", prop="originalId")
     *
     * @var int
     */
    private $originalId;

    /**
     * 评论评分
     *
     * @Column()
     *
     * @var float|null
     */
    private $score;

    /**
     * 1.未处理  2.已处理【人工处理】  3.系统处理 4.需要重新处理  5.已放弃
     *
     * @Column()
     *
     * @var int|null
     */
    private $status;

    /**
     * 更新时间
     *
     * @Column(name="updated_at", prop="updatedAt")
     *
     * @var string|null
     */
    private $updatedAt;

    /**
     * 评论的用户名称
     *
     * @Column(name="user_name", prop="userName")
     *
     * @var string|null
     */
    private $userName;


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
     * @param string|null $content
     *
     * @return self
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @param string|null $contentTime
     *
     * @return self
     */
    public function setContentTime(?string $contentTime): self
    {
        $this->contentTime = $contentTime;

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
     * @param string|null $userName
     *
     * @return self
     */
    public function setUserName(?string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * @return int
     */
    public function getAdminId(): ?int
    {
        return $this->adminId;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @return string|null
     */
    public function getContentTime(): ?string
    {
        return $this->contentTime;
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
     * @return string|null
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * @return int
     */
    public function getOriginalId(): ?int
    {
        return $this->originalId;
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
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    /**
     * @return string|null
     */
    public function getUserName(): ?string
    {
        return $this->userName;
    }

}
