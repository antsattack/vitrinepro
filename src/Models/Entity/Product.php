<?php



namespace App\Models\Entity;
use \Doctrine\Common\Collections\ArrayCollection;

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

    /**
     * Get the value of title
     *
     * @return  string|null
     */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @param  string|null  $title
     *
     * @return  self
     */ 
    public function setTitle($title)
    {
        if (!$title && !is_string($title)) {
            throw new \InvalidArgumentException("Title of product is required", 400);
        }
        
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of description
     *
     * @return  string|null
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param  string|null  $description
     *
     * @return  self
     */ 
    public function setDescription($description)
    {
        if (!$description && !is_string($description)) {
            throw new \InvalidArgumentException("Description of product is required", 400);
        }
        
        $this->description = $description;

        return $this;
    }

    /**
     * Set the value of category
     *
     * @param  \App\Models\Entity\Category  $category
     *
     * @return  self
     */ 
    public function setCategory(\App\Models\Entity\Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Set the value of brand
     *
     * @param  \App\Models\Entity\Brand  $brand
     *
     * @return  self
     */ 
    public function setBrand(\App\Models\Entity\Brand $brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get the value of currency
     *
     * @return  \App\Models\Entity\Currency
     */ 
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set the value of currency
     *
     * @param  \App\Models\Entity\Currency  $currency
     *
     * @return  self
     */ 
    public function setCurrency(\App\Models\Entity\Currency $currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get the value of seller
     *
     * @return  \App\Models\Entity\User
     */ 
    public function getSeller()
    {
        return $this->seller;
    }

    /**
     * Set the value of seller
     *
     * @param  \App\Models\Entity\User  $seller
     *
     * @return  self
     */ 
    public function setSeller(\App\Models\Entity\User $seller)
    {
        $this->seller = $seller;

        return $this;
    }

    /**
     * Set the value of model
     *
     * @param  string|null  $model
     *
     * @return  self
     */ 
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Set the value of price
     *
     * @param  string|null  $price
     *
     * @return  self
     */ 
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Set the value of new
     *
     * @param  string|null  $new
     *
     * @return  self
     */ 
    public function setNew($new)
    {
        $this->new = $new;

        return $this;
    }

    /**
     * Set the value of quantity
     *
     * @param  string|null  $quantity
     *
     * @return  self
     */ 
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get the value of tag
     *
     * @return  \Doctrine\Common\Collections\Collection
     */ 
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set the value of tag
     *
     * @param  \Doctrine\Common\Collections\Collection  $tag
     *
     * @return  self
     */ 
    /*public function setTag($tag)
    {
        //$tagCollection = new ArrayCollection($tag);
        $this->tag->add($tag);

        return $this;
    }*/

    /**
     * Get the value of attribute
     *
     * @return  \Doctrine\Common\Collections\Collection
     */ 
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Set the value of attribute
     *
     * @param  \Doctrine\Common\Collections\Collection  $attribute
     *
     * @return  self
     */ 
    public function setAttribute($attribute)
    {
        $attributeCollection = new ArrayCollection($attribute);
        $this->attribute = $attributeCollection;

        return $this;
    }

    /**
     * @return App\Models\Entity\Product
     */
    public function getValues() {
        return get_object_vars($this);
    }
}
