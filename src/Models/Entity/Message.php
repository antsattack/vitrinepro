<?php

namespace App\Models\Entity;

/**
 * Message
 *
 * @Table(name="message", indexes={@Index(name="fk_message_sender_idx", columns={"sender"}), @Index(name="fk_message_recipient_idx", columns={"recipient"})})
 * @Entity
 */
class Message
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
     * @var int|null
     *
     * @Column(name="type", type="integer", nullable=true)
     */
    public $type;

    /**
     * @var string|null
     *
     * @Column(name="message", type="string", length=255, nullable=true)
     */
    public $message;

    /**
     * @var \App\Models\Entity\User
     *
     * @ManyToOne(targetEntity="App\Models\Entity\User")
     * @JoinColumns({
     *   @JoinColumn(name="sender", referencedColumnName="id")
     * })
     */
    public $sender;

    /**
     * @var \App\Models\Entity\User
     *
     * @ManyToOne(targetEntity="App\Models\Entity\User")
     * @JoinColumns({
     *   @JoinColumn(name="recipient", referencedColumnName="id")
     * })
     */
    public $recipient;

    /**
     * @var int|null
     *
     * @Column(name="readed", type="integer", nullable=true)
     */
    public $readed;

    /**
     * @var \DateTime|null
     *
     * @Column(name="register", type="datetime")
     */
    public $register;

    /**
     * @var \DateTime|null
     *
     * @Column(name="exclusion", type="datetime", nullable=true)
     */
    public $exclusion;

    /**
     * Set the value of sender
     *
     * @param  \App\Models\Entity\User  $sender
     *
     * @return  self
     */ 
    public function setSender(\App\Models\Entity\User $sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Set the value of recipient
     *
     * @param  \App\Models\Entity\User  $recipient
     *
     * @return  self
     */ 
    public function setRecipient(\App\Models\Entity\User $recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Set the value of message
     *
     * @param  string|null  $message
     *
     * @return  self
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Set the value of read
     *
     * @param  integer|null  $read
     *
     * @return  self
     */
    public function setReaded($readed)
    {
        $this->readed = $readed;

        return $this;
    }

    /**
     * Set the value of register
     *
     * @param  datetime  $register
     *
     * @return  self
     */
    public function setRegister($register)
    {
        $this->register = $register;

        return $this;
    }

    /**
     * Set the value of type
     *
     * @param  int  $type
     *
     * @return  self
     */
    public function setType(int $type)
    {
        $this->type = $type;

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
