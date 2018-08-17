<?php



namespace App\Models\Entity;

/**
 * Address
 *
 * @Table(name="address")
 * @Entity
 */
class Address
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
     * @Column(name="street", type="string", length=500, nullable=true)
     */
    public $street;

    /**
     * @var bool|null
     *
     * @Column(name="number", type="boolean", nullable=true)
     */
    public $number;

    /**
     * @var bool|null
     *
     * @Column(name="zip", type="boolean", nullable=true)
     */
    public $zip;

    /**
     * @var \DateTime|null
     *
     * @Column(name="register", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    public $register = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime|null
     *
     * @Column(name="exclusion", type="datetime", nullable=true)
     */
    public $exclusion;

    /**
     * @var string|null
     *
     * @Column(name="city", type="string", length=255, nullable=true)
     */
    public $city;

    /**
     * @var string|null
     *
     * @Column(name="phone", type="string", length=45, nullable=true)
     */
    public $phone;

    /**
     * @var string|null
     *
     * @Column(name="uf", type="string", length=2, nullable=true)
     */
    public $uf;


}
