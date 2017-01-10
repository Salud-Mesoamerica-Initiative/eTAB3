<?php

namespace ISECH\IndicadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ISECH\IndicadoresBundle\Validator as CustomAssert;

/**
 * MatrizIndicadoresDesempeno
 *
 * @ORM\Table(name="matriz_indicadores_desempeno")
 * @ORM\Entity(repositoryClass="ISECH\IndicadoresBundle\Entity\MatrizIndicadoresDesempenoRepository")
 */
class MatrizIndicadoresDesempeno
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=500)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="orden", type="string", length=4, nullable=true)
     */
    private $orden;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creado", type="datetime")
     */
    private $creado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="actualizado", type="datetime")
     */
    private $actualizado;

	/**
     * @ORM\ManyToMany(targetEntity="FichaTecnica")
     **/
    private $matrizIndicadoresEtab;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="MatrizIndicadoresRel", mappedBy="desempeno", cascade={"all"}, orphanRemoval=true)
     *
     */
    private $indicators;

	public function __construct()
    {
		$this->matrizIndicadoresEtab = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setCreado(new \DateTime());
        $this->setActualizado(new \DateTime());
		
    }
	/**
     * @ORM\PreUpdate
     */
    public function setUpdatedValue()
    {
       $this->setActualizado(new \DateTime());
    }
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
     * Set nombre
     *
     * @param string $nombre
     * @return MatrizIndicadoresDesempeno
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set creado
     *
     * @param DateTime $creado
     * @return MatrizIndicadoresDesempeno
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
     * Set actualizado
     *
     * @param DateTime $actualizado
     * @return MatrizIndicadoresDesempeno
     */
    public function setActualizado($actualizado)
    {
        $this->actualizado = $actualizado;
    	
        return $this;
    }

    /**
     * Get actualizado
     *
     * @return \DateTime 
     */
    public function getActualizado()
    {
        return $this->actualizado;
    }

    /**
     * Set matrizIndicadoresEtab
     *
     * @param integer $matrizIndicadoresEtab
     * @return MatrizIndicadoresDesempeno
     */
    public function setMatrizIndicadoresEtab($matrizIndicadoresEtab)
    {
        $this->matrizIndicadoresEtab = $matrizIndicadoresEtab;
    
        return $this;
    }

    /**
     * Get matrizIndicadoresEtab
     *
     * @return integer 
     */
    public function getMatrizIndicadoresEtab()
    {
        return $this->matrizIndicadoresEtab;
    }

	
	public function __toString() {
        return $this->nombre ? :'';
    }

    /**
     * Add matrizIndicadoresEtab
     *
     * @param ISECH\IndicadoresBundle\Entity\FichaTecnica $matrizIndicadoresEtab
     *
     * @return MatrizIndicadoresDesempeno
     */
    public function addMatrizIndicadoresEtab(\ISECH\IndicadoresBundle\Entity\FichaTecnica $matrizIndicadoresEtab)
    {
        $this->matrizIndicadoresEtab[] = $matrizIndicadoresEtab;

        return $this;
    }

    /**
     * Remove matrizIndicadoresEtab
     *
     * @param ISECH\IndicadoresBundle\Entity\FichaTecnica $matrizIndicadoresEtab
     */
    public function removeMatrizIndicadoresEtab(\ISECH\IndicadoresBundle\Entity\FichaTecnica $matrizIndicadoresEtab)
    {
        $this->matrizIndicadoresEtab->removeElement($matrizIndicadoresEtab);
    }

    /**
     * Get indicators
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getIndicators()
    {
        return $this->indicators;
    }

    /**
     * Add alertas
     *
     * @param  ISECH\IndicadoresBundle\Entity\MatrizIndicadoresRel $indicators
     * @return MatrizIndicadoresDesempeno
     */
    public function addIndicators(\ISECH\IndicadoresBundle\Entity\MatrizIndicadoresRel $indicators)
    {
        $this->addIndicator($indicators);
    }

    /**
     * Add indicators
     *
     * @param ISECH\IndicadoresBundle\Entity\MatrizIndicadoresRel $indicators
     *
     * @return MatrizIndicadoresDesempeno
     */
    public function addIndicator(\ISECH\IndicadoresBundle\Entity\MatrizIndicadoresRel $indicators)
    {
        $this->indicators[] = $indicators;

        return $this;
    }

    /**
     * Remove indicators
     *
     * @param ISECH\IndicadoresBundle\Entity\MatrizIndicadoresRel $indicators
     */
    public function removeIndicator(\ISECH\IndicadoresBundle\Entity\MatrizIndicadoresRel $indicators)
    {
        $this->indicators->removeElement($indicators);
    }

    public function removeIndicators()
    {
        $this->indicators=array();
    }

    /**
     * Set orden
     *
     * @param string $orden
     *
     * @return MatrizSeguimiento
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden
     *
     * @return string
     */
    public function getOrden()
    {
        return $this->orden;
    }
}
