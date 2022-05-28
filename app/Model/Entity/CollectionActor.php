<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 采集演员信息表
 * Class CollectionActor
 *
 * @since 2.0
 *
 * @Entity(table="collection_actor")
 */
class CollectionActor extends Model
{
    /**
     * 验证网址
     *
     * @Column(name="actual_source", prop="actualSource")
     *
     * @var string|null
     */
    private $actualSource;

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
     * json 数组 社交账号
     *
     * @Column()
     *
     * @var string|null
     */
    private $interflow;

    /**
     * 影片数量
     *
     * @Column(name="movie_sum", prop="movieSum")
     *
     * @var int
     */
    private $movieSum;

    /**
     * 演员名称-主
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
     * 演员照片
     *
     * @Column()
     *
     * @var string|null
     */
    private $photo;

    /**
     * json 数组 资源信息
     *
     * @Column(name="resources_info", prop="resourcesInfo")
     *
     * @var string|null
     */
    private $resourcesInfo;

    /**
     * json 数组 资源异常信息
     *
     * @Column(name="resources_odd_info", prop="resourcesOddInfo")
     *
     * @var string|null
     */
    private $resourcesOddInfo;

    /**
     * 1.未处理  2.已下载  3.处理异常-部分未下载需要人工审核
     *
     * @Column(name="resources_status", prop="resourcesStatus")
     *
     * @var int|null
     */
    private $resourcesStatus;

    /**
     * 演员性别
     *
     * @Column()
     *
     * @var string|null
     */
    private $sex;

    /**
     * json 数组 社交账户 产品说手动补目前保留字段
     *
     * @Column(name="social_accounts", prop="socialAccounts")
     *
     * @var string|null
     */
    private $socialAccounts;

    /**
     * 1.演员处理来源  2.影片处理来源【影片处理来源的时候演员处理需要重新更新】 3.异常【这个状态表示数据冲突需要人工处理】
     *
     * @Column()
     *
     * @var int|null
     */
    private $source;

    /**
     * 1.未处理  2.已处理【人工处理】  3.系统处理 4.需要重新处理  5.异常数据需要人工处理
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
     * @param string|null $actualSource
     *
     * @return self
     */
    public function setActualSource(?string $actualSource): self
    {
        $this->actualSource = $actualSource;

        return $this;
    }

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
     * @param string|null $interflow
     *
     * @return self
     */
    public function setInterflow(?string $interflow): self
    {
        $this->interflow = $interflow;

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
     * @param string|null $photo
     *
     * @return self
     */
    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @param string|null $resourcesInfo
     *
     * @return self
     */
    public function setResourcesInfo(?string $resourcesInfo): self
    {
        $this->resourcesInfo = $resourcesInfo;

        return $this;
    }

    /**
     * @param string|null $resourcesOddInfo
     *
     * @return self
     */
    public function setResourcesOddInfo(?string $resourcesOddInfo): self
    {
        $this->resourcesOddInfo = $resourcesOddInfo;

        return $this;
    }

    /**
     * @param int|null $resourcesStatus
     *
     * @return self
     */
    public function setResourcesStatus(?int $resourcesStatus): self
    {
        $this->resourcesStatus = $resourcesStatus;

        return $this;
    }

    /**
     * @param string|null $sex
     *
     * @return self
     */
    public function setSex(?string $sex): self
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * @param string|null $socialAccounts
     *
     * @return self
     */
    public function setSocialAccounts(?string $socialAccounts): self
    {
        $this->socialAccounts = $socialAccounts;

        return $this;
    }

    /**
     * @param int|null $source
     *
     * @return self
     */
    public function setSource(?int $source): self
    {
        $this->source = $source;

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
    public function getActualSource(): ?string
    {
        return $this->actualSource;
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
     * @return string|null
     */
    public function getInterflow(): ?string
    {
        return $this->interflow;
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
     * @return string|null
     */
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    /**
     * @return string|null
     */
    public function getResourcesInfo(): ?string
    {
        return $this->resourcesInfo;
    }

    /**
     * @return string|null
     */
    public function getResourcesOddInfo(): ?string
    {
        return $this->resourcesOddInfo;
    }

    /**
     * @return int|null
     */
    public function getResourcesStatus(): ?int
    {
        return $this->resourcesStatus;
    }

    /**
     * @return string|null
     */
    public function getSex(): ?string
    {
        return $this->sex;
    }

    /**
     * @return string|null
     */
    public function getSocialAccounts(): ?string
    {
        return $this->socialAccounts;
    }

    /**
     * @return int|null
     */
    public function getSource(): ?int
    {
        return $this->source;
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
