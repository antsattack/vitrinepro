<?php



namespace App\Models\Entity;

/**
 * ProductColor
 *
 * @Table(name="product_color", indexes={@Index(name="fk_product_has_color_color1_idx", columns={"color1_id"}), @Index(name="fk_product_has_color_product1_idx", columns={"product_id"}), @Index(name="fk_product_color_color1_idx", columns={"color2_id"})})
 * @Entity
 */
class ProductColor
{
    /**
     * @var \App\Models\Entity\Color
     *
     * @ManyToOne(targetEntity="App\Models\Entity\Color")
     * @JoinColumns({
     *   @JoinColumn(name="color2_id", referencedColumnName="id")
     * })
     */
    public $color2;

    /**
     * @var \App\Models\Entity\Color
     *
     * @Id
     * @GeneratedValue(strategy="NONE")
     * @OneToOne(targetEntity="App\Models\Entity\Color")
     * @JoinColumns({
     *   @JoinColumn(name="color1_id", referencedColumnName="id")
     * })
     */
    public $color1;

    /**
     * @var \App\Models\Entity\Product
     *
     * @Id
     * @GeneratedValue(strategy="NONE")
     * @OneToOne(targetEntity="App\Models\Entity\Product")
     * @JoinColumns({
     *   @JoinColumn(name="product_id", referencedColumnName="id")
     * })
     */
    public $product;


}
