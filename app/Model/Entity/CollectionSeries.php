<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 采集系列表
 * Class CollectionSeries
 *
 * @since 2.0
 *
 * @Entity(table="collection_series")
 */
class CollectionSeries extends Model
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
     * json 数组 类别
     *
     * @Column()
     *
     * @var string|null
     */
    private $category;

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
     * 影片数量
     *
     * @Column(name="movie_sum", prop="movieSum")
     *
     * @var int
     */
    private $movieSum;

    /**
     * 系列名称
     *
     * @Column()
     *
     * @var string|null
     */
    private $name;

    /**
     * 源数据ID
     *
     * @Column(name="original_id", prop="originalId")
     *
     * @var int
     */
    private $originalId;

    /**
     * 1.未处理  2.已处理  【人工处理】  3.系统处理 4.需要重新处理 5.异常数据需要人工处理
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
    public function getAdminId(): ?int
    {
        return $this->adminId;
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
    public function getMovieSum(): ?int
    {
        return $this->movieSum;
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
    public function getOriginalId(): ?int
    {
        return $this->originalId;
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
