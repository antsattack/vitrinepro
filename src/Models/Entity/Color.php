<?php

namespace App\Models\Entity;


/**
 * Color
 *
 * @Entity @Table(name="color")
 */
class Color
{
    /**
     * @var int
     *
     * @Id @Column(name="id", type="integer", nullable=false)
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
     * @Column(name="hexadecimal", type="string", length=45, nullable=true)
     */
    public $hexadecimal;


    /**
     * Get the value of id
     *
     * @return int id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of name
     *
     * @return string name
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
        if (!$name && !is_string($name)) {
            throw new \InvalidArgumentException("Color name is required", 400);
        }
        
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of hexadecimal
     *
     * @return string hexadecimal
     */ 
    public function getHexadecimal()
    {
        return $this->hexadecimal;
    }

    /**
     * Set the value of hexadecimal
     *
     * @param  string|null  $hexadecimal
     *
     * @return  self
     */ 
    public function setHexadecimal($hexadecimal)
    {
        if (!$hexadecimal && !is_string($hexadecimal)) {
            throw new \InvalidArgumentException("Hexadecimal of color is required", 400);
        }
        
        $this->hexadecimal = $hexadecimal;

        return $this;
    }

    /**
     * @return App\Models\Entity\Color
     */
    public function getValues() {
        return get_object_vars($this);
    }
}
