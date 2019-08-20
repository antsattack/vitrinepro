<?php

namespace App\Models\Entity;

/**
 * Coupon
 *
 * @Table(name="coupon", indexes={@Index(name="fk_coupon_user1_idx", columns={"user_id"})})
 * @Entity
 */
class Coupon
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
     * @var \App\Models\Entity\User
     *
     * @ManyToOne(targetEntity="App\Models\Entity\User")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    public $user;

    /**
     * @var string|null
     *
     * @Column(name="title", type="string", length=45, nullable=true)
     */
    public $title;

    /**
     * @var string|null
     *
     * @Column(name="description", type="string", length=255, nullable=true)
     */
    public $description;

    /**
     * @var decimal|null
     *
     * @Column(name="value", type="decimal", precision=6, scale=2, nullable=true)
     */
    public $value;

    /**
     * @var \DateTime|null
     *
     * @Column(name="expiration", type="datetime", nullable=true)
     */
    public $expiration;

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
     * Set the value of title
     *
     * @param  string|null  $title
     *
     * @return  self
     */
    public function setTitle($title)
    {
        if (!$title && !is_string($title)) {
            throw new \InvalidArgumentException("Title is required", 400);
        }

        $this->title = $title;

        return $this;
    }

    /**
     * Set the value of description
     *
     * @param  string|null  $description
     *
     * @return  self
     */
    public function setDescription($description)
    {
        if (!$description && !is_string($description)) {
            throw new \InvalidArgumentException("Description is required", 400);
        }

        $this->description = $description;

        return $this;
    }

    /**
     * Set the value of Value
     *
     * @param  string|null  $value
     *
     * @return  self
     */
    public function setValue($value)
    {
        if (!$value && !is_string($value)) {
            throw new \InvalidArgumentException("Value is required", 400);
        }

        $this->value = $value;

        return $this;
    }

    /**
     * Set the value of User
     *
     * @param  \App\Models\Entity\User|null  $user
     *
     * @return  self
     */
    public function setUser($user)
    {
        if (!$user) {
            throw new \InvalidArgumentException("User is required", 400);
        }

        $this->user = $user;

        return $this;
    }

    /**
     * @return App\Models\Entity\Coupon
     */
    public function getValues()
    {
        return get_object_vars($this);
    }

}
