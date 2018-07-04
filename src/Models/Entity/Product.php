<?php



namespace App\Models\Entity;

/**
 * Product
 *
 * @Table(name="product", indexes={@Index(name="fk_product_category1_idx", columns={"category_id"}), @Index(name="fk_product_seller1_idx", columns={"seller_id"}), @Index(name="fk_product_brand1_idx", columns={"brand_id"}), @Index(name="fk_product_currency1_idx", columns={"currency_id"})})
 * @Entity
 */
class Product
{
    /**
     * @var int
     *
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    public $id;

    /**
     * @var string|null
     *
     * @Column(name="title", type="string", length=255, nullable=true)
     */
    public $title;

    /**
     * @var string|null
     *
     * @Column(name="description", type="text", length=65535, nullable=true)
     */
    public $description;

    /**
     * @var string|null
     *
     * @Column(name="model", type="string", length=125, nullable=true)
     */
    public $model;

    ////**
    // * @var \DateTime|null
    // *
    // * @Column(name="register", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
    // */
    //public $register = 'CURRENT_TIMESTAMP';

    ///**
    // * @var \DateTime|null
    // *
    // * @Column(name="exclusion", type="datetime", nullable=true)
    // */
    //public $exclusion;

    /**
     * @var decimal|null
     *
     * @Column(name="price", type="decimal", precision=10, scale=2, nullable=true)
     */
    public $price;

    /**
     * @var bool|null
     *
     * @Column(name="new", type="boolean", nullable=true)
     */
    public $new;

    /**
     * @var int|null
     *
     * @Column(name="quantity", type="integer", nullable=true)
     */
    public $quantity;

    /**
     * @var \App\Models\Entity\Brand
     *
     * @ManyToOne(targetEntity="App\Models\Entity\Brand")
     * @JoinColumns({
     *   @JoinColumn(name="brand_id", referencedColumnName="id")
     * })
     */
    public $brand;

    /**
     * @var \App\Models\Entity\Category
     *
     * @ManyToOne(targetEntity="App\Models\Entity\Category")
     * @JoinColumns({
     *   @JoinColumn(name="category_id", referencedColumnName="id")
     * })
     */
    public $category;

    /**
     * @var \App\Models\Entity\Currency
     *
     * @ManyToOne(targetEntity="App\Models\Entity\Currency")
     * @JoinColumns({
     *   @JoinColumn(name="currency_id", referencedColumnName="id")
     * })
     */
    public $currency;

    /**
     * @var \App\Models\Entity\User
     *
     * @ManyToOne(targetEntity="App\Models\Entity\User")
     * @JoinColumns({
     *   @JoinColumn(name="seller_id", referencedColumnName="id")
     * })
     */
    public $seller;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ManyToMany(targetEntity="App\Models\Entity\Attribute", inversedBy="product")
     * @JoinTable(name="datasheet",
     *   joinColumns={
     *     @JoinColumn(name="product_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @JoinColumn(name="attribute_id", referencedColumnName="id")
     *   }
     * )
     */
    public $attribute;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ManyToMany(targetEntity="App\Models\Entity\Tag", inversedBy="product")
     * @JoinTable(name="product_tag",
     *   joinColumns={
     *     @JoinColumn(name="product_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @JoinColumn(name="tag_id", referencedColumnName="id")
     *   }
     * )
     */
    public $tag;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attribute = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tag = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get the value of id
     *
     * @return  int
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param  int  $id
     *
     * @return  self
     */ 
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }
}
