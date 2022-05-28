<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 演员热度值表
 * Class ActorPopularityChart
 *
 * @since 2.0
 *
 * @Entity(table="actor_popularity_chart")
 */
class ActorPopularityChart extends Model
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
     * 热度值
     *
     * @Column(name="hot_val", prop="hotVal")
     *
     * @var float
     */
    private $hotVal;

    /**
     * 
     * @Id()
     * @Column()
     *
     * @var int
     */
    private $id;

    /**
     * 月份
     *
     * @Column()
     *
     * @var string|null
     */
    private $mtime;

    /**
     * 上月热度值
     *
     * @Column(name="up_mhot", prop="upMhot")
     *
     * @var float
     */
    private $upMhot;

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
     * @param float $hotVal
     *
     * @return self
     */
    public function setHotVal(float $hotVal): self
    {
        $this->hotVal = $hotVal;

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
     * @param string|null $mtime
     *
     * @return self
     */
    public function setMtime(?string $mtime): self
    {
        $this->mtime = $mtime;

        return $this;
    }

    /**
     * @param float $upMhot
     *
     * @return self
     */
    public function setUpMhot(float $upMhot): self
    {
        $this->upMhot = $upMhot;

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
     * @return float
     */
    public function getHotVal(): ?float
    {
        return $this->hotVal;
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
    public function getMtime(): ?string
    {
        return $this->mtime;
    }

    /**
     * @return float
     */
    public function getUpMhot(): ?float
    {
        return $this->upMhot;
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

}
