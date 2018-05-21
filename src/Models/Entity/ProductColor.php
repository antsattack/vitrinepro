<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * ProductColor
 *
 * @ORM\Table(name="product_color", indexes={@ORM\Index(name="fk_product_has_color_color1_idx", columns={"color1_id"}), @ORM\Index(name="fk_product_has_color_product1_idx", columns={"product_id"}), @ORM\Index(name="fk_product_color_color1_idx", columns={"color2_id"})})
 * @ORM\Entity
 */
class ProductColor
{
    /**
     * @var \Color
     *
     * @ORM\ManyToOne(targetEntity="Color")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="color2_id", referencedColumnName="id")
     * })
     */
    private $color2;

    /**
     * @var \Color
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Color")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="color1_id", referencedColumnName="id")
     * })
     */
    private $color1;

    /**
     * @var \Product
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Product")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     * })
     */
    private $product;


}
