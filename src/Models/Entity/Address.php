<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Address
 *
 * @ORM\Table(name="address")
 * @ORM\Entity
 */
class Address
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="street", type="string", length=500, nullable=true)
     */
    private $street;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="number", type="boolean", nullable=true)
     */
    private $number;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="zip", type="boolean", nullable=true)
     */
    private $zip;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="register", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $register = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="exclusion", type="datetime", nullable=true)
     */
    private $exclusion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone", type="string", length=45, nullable=true)
     */
    private $phone;


}
