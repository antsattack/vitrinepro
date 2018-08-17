<?php



namespace App\Models\Entity;

/**
 * Shippingformat
 *
 * @Table(name="shippingformat")
 * @Entity
 */
class Shippingformat
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
    public $register = 'CURRENT_TIMESTAMP';


}
