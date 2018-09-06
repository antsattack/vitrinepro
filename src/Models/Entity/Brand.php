<?php



namespace App\Models\Entity;

/**
 * Brand
 *
 * @Table(name="brand")
 * @Entity
 */
class Brand
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
     * @Column(name="name", type="string", length=45, nullable=true)
     */
    public $name;

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
     * Get the value of category
     *
     * @return  \App\Models\Entity\Category
     */ 
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return App\Models\Entity\Brand
     */
    public function getValues() {
        return get_object_vars($this);
    }


    
}
