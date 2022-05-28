<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 采集原始数据表
 * Class CollectionOriginal
 *
 * @since 2.0
 *
 * @Entity(table="collection_original")
 */
class CollectionOriginal extends Model
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
     * mongoDb id
     *
     * @Column()
     *
     * @var string|null
     */
    private $oid;

    /**
     * 番号
     *
     * @Column()
     *
     * @var string|null
     */
    private $number;

    /**
     * 所属mongodb表
     *
     * @Column(name="db_name", prop="dbName")
     *
     * @var string|null
     */
    private $dbName;

    /**
     * 处理计数 为防止意外一个影片数据会处理两次
     *
     * @Column(name="dis_sum", prop="disSum")
     *
     * @var int
     */
    private $disSum;

    /**
     * json 数据
     *
     * @Column()
     *
     * @var string|null
     */
    private $data;

    /**
     * 1.未处理  2.已处理 3.需要重新处理
     *
     * @Column()
     *
     * @var int|null
     */
    private $status;

    /**
     * 采集方的创建时间 最大值用于从采集那边的筛选条件
     *
     * @Column()
     *
     * @var string|null
     */
    private $ctime;

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
     * @param string|null $oid
     *
     * @return self
     */
    public function setOid(?string $oid): self
    {
        $this->oid = $oid;

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
     * @param string|null $dbName
     *
     * @return self
     */
    public function setDbName(?string $dbName): self
    {
        $this->dbName = $dbName;

        return $this;
    }

    /**
     * @param int $disSum
     *
     * @return self
     */
    public function setDisSum(int $disSum): self
    {
        $this->disSum = $disSum;

        return $this;
    }

    /**
     * @param string|null $data
     *
     * @return self
     */
    public function setData(?string $data): self
    {
        $this->data = $data;

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
     * @param string|null $ctime
     *
     * @return self
     */
    public function setCtime(?string $ctime): self
    {
        $this->ctime = $ctime;

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
    public function getOid(): ?string
    {
        return $this->oid;
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
    public function getDbName(): ?string
    {
        return $this->dbName;
    }

    /**
     * @return int
     */
    public function getDisSum(): ?int
    {
        return $this->disSum;
    }

    /**
     * @return string|null
     */
    public function getData(): ?string
    {
        return $this->data;
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
    public function getCtime(): ?string
    {
        return $this->ctime;
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
