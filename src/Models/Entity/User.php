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
     * @Column(name="email", type="string", length=255, nullable=true)
     */
    public $email;

    /**
     * @var string|null
     *
     * @Column(name="name", type="text", length=65535, nullable=true)
     */
    public $name;

    /**
     * @var string|null
     *
     * @Column(name="passwd", type="string", length=255, nullable=true)
     */
    public $passwd;

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
     * @var string|null
     *
     * @Column(name="indication", type="string", length=255, nullable=true)
     */
    public $indication;

    /**
     * @var \DateTime|null
     *
     * @Column(name="exclusion", type="datetime", nullable=true)
     */
    public $exclusion;

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
     * Get the value of email
     *
     * @return  string|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @param  string|null  $email
     *
     * @return  self
     */
    public function setEmail($email)
    {
        $this->email = $email;

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
        $this->name = $name;

        return $this;
    }

    /**
     * Set the value of passwd
     *
     * @param  string|null  $passwd
     *
     * @return  self
     */
    public function setPasswd($passwd)
    {
        $this->passwd = $passwd;

        return $this;
    }

    /**
     * Set the value of indication
     *
     * @param  string|null  $indication
     *
     * @return  self
     */
    public function setIndication($indication)
    {
        $this->indication = $indication;

        return $this;
    }

    /**
     * @return App\Models\Entity\User
     */
    public function getValues()
    {
        return get_object_vars($this);
    }
}
