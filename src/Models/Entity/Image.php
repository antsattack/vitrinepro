<?php



namespace App\Models\Entity;

/**
 * Image
 *
 * @Table(name="image", indexes={@Index(name="fk_image_product1_idx", columns={"product_id"})})
 * @Entity
 */
class Image
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
     * @Column(name="prefix", type="string", length=255, nullable=true)
     */
    public $prefix;

    ///**
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
     * @var bool|null
     *
     * @Column(name="main", type="boolean", nullable=true)
     */
    public $main = '0';

    /**
     * @var \App\Models\Entity\Product
     *
     * @ManyToOne(targetEntity="App\Models\Entity\Product")
     * @JoinColumns({
     *   @JoinColumn(name="product_id", referencedColumnName="id")
     * })
     */
    public $product;



    /**
     * Get the value of prefix
     *
     * @return  string|null
     */ 
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set the value of prefix
     *
     * @param  string|null  $prefix
     *
     * @return  self
     */ 
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Get the value of product
     *
     * @return  \App\Models\Entity\Product
     */ 
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set the value of product
     *
     * @param  \App\Models\Entity\Product  $product
     *
     * @return  self
     */ 
    public function setProduct(\App\Models\Entity\Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get the value of main
     *
     * @return  bool|null
     */ 
    public function getMain()
    {
        return $this->main;
    }

    /**
     * Set the value of main
     *
     * @param  bool|null  $main
     *
     * @return  self
     */ 
    public function setMain($main)
    {
        $this->main = $main;

        return $this;
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
}
