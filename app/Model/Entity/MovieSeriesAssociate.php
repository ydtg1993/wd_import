<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 影片系列关联表
 * Class MovieSeriesAssociate
 *
 * @since 2.0
 *
 * @Entity(table="movie_series_associate")
 */
class MovieSeriesAssociate extends Model
{
    /**
     * 关联时间
     *
     * @Column(name="associate_time", prop="associateTime")
     *
     * @var string|null
     */
    private $associateTime;

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
     * 导演ID
     *
     * @Column(name="series_id", prop="seriesId")
     *
     * @var int
     */
    private $seriesId;

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
     * @param int $seriesId
     *
     * @return self
     */
    public function setSeriesId(int $seriesId): self
    {
        $this->seriesId = $seriesId;

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
     * @return string|null
     */
    public function getAssociateTime(): ?string
    {
        return $this->associateTime;
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
     * @return int
     */
    public function getSeriesId(): ?int
    {
        return $this->seriesId;
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
