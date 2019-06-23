<?php



namespace App\Models\Entity;

/**
 * Category
 *
 * @Table(name="category", indexes={@Index(name="fk_category_category1_idx", columns={"parent_id"})})
 * @Entity
 */
class Category
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
     *   @JoinColumn(name="parent_id", referencedColumnName="id")
     * })
     */
    public $parent;

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
     * Get the value of description
     *
     * @return  string|null
     */ 
    public function getDescription()
    {
        return $this->description;
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
            throw new \InvalidArgumentException("Name of category is required", 400);
        }
        
        $this->name = $name;

        return $this;
    }

    /**
     * @return App\Models\Entity\Category
     */
    public function getValues() {
        return get_object_vars($this);
    }

}
