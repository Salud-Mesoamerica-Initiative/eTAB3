<?php

namespace ISECH\IndicadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ISECH\IndicadoresBundle\Validator as CustomAssert;

/**
 * Social
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ISECH\IndicadoresBundle\Entity\SocialRepository")
 */
class Social
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creado", type="datetime")
     */
    private $creado;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=72)
     */
    private $token;

	/**
     * @ORM\ManyToOne(targetEntity="GrupoIndicadores")
     * @ORM\JoinColumn(name="sala", referencedColumnName="id")
     * @return integer
     */
    
    private $sala;

	
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
	
    
    /**
     * Set creado
     *
     * @param \DateTime $creado
     * @return Boletin
     */
    public function setCreado($creado)
    {
        $this->creado = $creado;
    
        return $this;
    }

    /**
     * Get creado
     *
     * @return \DateTime 
     */
    public function getCreado()
    {
        return $this->creado;
    }


    /**
     * Set token
     *
     * @param string $token
     * @return Boletin
     */
    public function setToken($token)
    {
        $this->token = $token;
    
        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set sala
     *
     * @param integer $sala
     * @return Boletin
     */
    public function setSala($sala)
    {
        $this->sala = $sala;
    
        return $this;
    }

    /**
     * Get sala
     *
     * @return integer 
     */
    public function getSala()
    {
        return $this->sala;
    }
}
