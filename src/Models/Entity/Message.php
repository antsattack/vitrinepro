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

}
