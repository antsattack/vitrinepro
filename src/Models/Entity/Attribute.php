<?php



namespace App\Models\Entity;

/**
 * Attribute
 *
 * @Table(name="attribute", indexes={@Index(name="fk_attribute_category1_idx", columns={"category_id"})})
 * @Entity
 */
class Attribute
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
     * @Column(name="name", type="string", length=255, nullable=true)
     */
    public $name;

    /**
     * @var string|null
     *
     * @Column(name="description", type="text", length=65535, nullable=true)
     */
    public $description;

    /**
     * @var \DateTime|null
     *
     * @Column(name="register", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    //public $register = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime|null
     *
     * @Column(name="exclusion", type="datetime", nullable=true)
     */
    //public $exclusion;

    /**
     * @var string|null
     *
     * @Column(name="unit", type="string", length=45, nullable=true)
     */
    public $unit;

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
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ManyToMany(targetEntity="App\Models\Entity\Product", mappedBy="attribute")
     */
    public $product;

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
     * Get the value of name
     *
     * @return  string|null
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param  string|null  $name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        if (!$name && !is_string($name)) {
            throw new \InvalidArgumentException("Name is required", 400);
        }
        
        $this->name = $name;

        return $this;
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
            throw new \InvalidArgumentException("Description is required", 400);
        }
        
        $this->description = $description;

        return $this;
    }

    /**
     * Set the value of parent
     *
     * @param  \App\Models\Entity\Category|null  $category
     *
     * @return  self
     */ 
    public function setCategory($category)
    {
        if (!$category) {
            throw new \InvalidArgumentException("Category is required", 400);
        }
        
        $this->category = $category;

        return $this;
    }

    /**
     * @return App\Models\Entity\Brand
     */
    public function getValues() {
        return get_object_vars($this);
    }

}
