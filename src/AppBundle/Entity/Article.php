<?php

namespace AppBundle\Entity;

use JMS\Serializer\Annotation as Serializer;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;


/**
 * @ORM\Entity
 * @ORM\Table()
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "api_get",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute=true
 *      )
 * )
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "api_postArticle",
 *          absolute=true
 *      )
 * )
 * @Hateoas\Relation(
 *     "weather",
 *     embedded = @Hateoas\Embedded("expr(service('app.weather').getCurrent())")
 * )
 */
class Article
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(groups={"post"})
     * @Serializer\Since("1.0")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(groups={"post"})
     * @Serializer\Since("1.0")
     */
    private $content;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Serializer\Since("2.0")
     */
    private $shortDescription;

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    
}