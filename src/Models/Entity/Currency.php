<?php



namespace App\Models\Entity;

/**
 * Currency
 *
 * @Table(name="currency")
 * @Entity
 */
class Currency
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
     * @var \DateTime|null
     *
     * @Column(name="register", type="datetime", nullable=true)
     */
    public $register;

    /**
     * @var \DateTime|null
     *
     * @Column(name="exclusion", type="datetime", nullable=true)
     */
    public $exclusion;


}
