<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 影片片商类别关联表
 * Class MovieFilmCompaniesCategoryAssociate
 *
 * @since 2.0
 *
 * @Entity(table="movie_film_companies_category_associate")
 */
class MovieFilmCompaniesCategoryAssociate extends Model
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
     * 演员ID
     *
     * @Column(name="film_companies_id", prop="filmCompaniesId")
     *
     * @var int
     */
    private $filmCompaniesId;

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
     * @param int $filmCompaniesId
     *
     * @return self
     */
    public function setFilmCompaniesId(int $filmCompaniesId): self
    {
        $this->filmCompaniesId = $filmCompaniesId;

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
    public function getFilmCompaniesId(): ?int
    {
        return $this->filmCompaniesId;
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
