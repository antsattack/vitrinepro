<?php



namespace App\Models\Entity;

/**
 * Tag
 *
 * @Table(name="tag")
 * @Entity
 */
class Tag
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
     * @ManyToMany(targetEntity="App\Models\Entity\Product", mappedBy="tag")
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
            throw new \InvalidArgumentException("Name of tag is required", 400);
        }
        
        $this->name = $name;

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
     * @return App\Models\Entity\Category
     */
    public function getValues() {
        return get_object_vars($this);
    }

}
