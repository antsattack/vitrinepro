<?php



namespace App\Models\Entity;

/**
 * User
 *
 * @Table(name="user", indexes={@Index(name="fk_user_address1_idx", columns={"address_payment_id"}), @Index(name="fk_user_address2_idx", columns={"address_shipping_id"})})
 * @Entity
 */
class User
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

    /**
     * @var \DateTime|null
     *
     * @Column(name="exclusion", type="datetime", nullable=true)
     */
    public $exclusion;

    /**
     * @var \App\Models\Entity\Address
     *
     * @ManyToOne(targetEntity="App\Models\Entity\Address")
     * @JoinColumns({
     *   @JoinColumn(name="address_payment_id", referencedColumnName="id")
     * })
     */
    public $addressPayment;

    /**
     * @var \App\Models\Entity\Address
     *
     * @ManyToOne(targetEntity="App\Models\Entity\Address")
     * @JoinColumns({
     *   @JoinColumn(name="address_shipping_id", referencedColumnName="id")
     * })
     */
    public $addressShipping;



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
     * Get the value of name
     *
     * @return  string|null
     */ 
    public function getName()
    {
        return $this->name;
    }
}
