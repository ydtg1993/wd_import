<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 影片演员类别关联表
 * Class MovieActorCategoryAssociate
 *
 * @since 2.0
 *
 * @Entity(table="movie_actor_category_associate")
 */
class MovieActorCategoryAssociate extends Model
{
    /**
     * 演员ID
     *
     * @Column()
     *
     * @var int
     */
    private $aid;

    /**
     * 关联时间
     *
     * @Column(name="associate_time", prop="associateTime")
     *
     * @var string|null
     */
    private $associateTime;

    /**
     * 类别ID
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
     * 
     * @Id()
     * @Column()
     *
     * @var int
     */
    private $id;

    /**
     * 1.正常  2.弃用
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
     * @param int $aid
     *
     * @return self
     */
    public function setAid(int $aid): self
    {
        $this->aid = $aid;

        return $this;
    }

    /**
     * @param string|null $associateTime
     *
     * @return self
     */
    public function setAssociateTime(?string $associateTime): self
    {
        $this->associateTime = $associateTime;

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
     * @return int
     */
    public function getAid(): ?int
    {
        return $this->aid;
    }

    /**
     * @return string|null
     */
    public function getAssociateTime(): ?string
    {
        return $this->associateTime;
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
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
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

}
