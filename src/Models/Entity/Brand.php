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

}
