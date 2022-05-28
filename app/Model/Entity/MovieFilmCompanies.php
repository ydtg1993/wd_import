<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 影片片商表
 * Class MovieFilmCompanies
 *
 * @since 2.0
 *
 * @Entity(table="movie_film_companies")
 */
class MovieFilmCompanies extends Model
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
     * 片商名称
     *
     * @Column()
     *
     * @var string|null
     */
    private $name;

    /**
     * 影片数量-冗余
     *
     * @Column(name="movie_sum", prop="movieSum")
     *
     * @var int
     */
    private $movieSum;

    /**
     * 收藏数量-冗余
     *
     * @Column(name="like_sum", prop="likeSum")
     *
     * @var int
     */
    private $likeSum;

    /**
     * 1.正常  2.弃用
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
     * @param int $movieSum
     *
     * @return self
     */
    public function setMovieSum(int $movieSum): self
    {
        $this->movieSum = $movieSum;

        return $this;
    }

    /**
     * @param int $likeSum
     *
     * @return self
     */
    public function setLikeSum(int $likeSum): self
    {
        $this->likeSum = $likeSum;

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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getMovieSum(): ?int
    {
        return $this->movieSum;
    }

    /**
     * @return int
     */
    public function getLikeSum(): ?int
    {
        return $this->likeSum;
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
