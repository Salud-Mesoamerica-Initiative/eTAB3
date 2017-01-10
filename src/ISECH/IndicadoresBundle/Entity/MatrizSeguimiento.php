<?php

namespace ISECH\IndicadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ISECH\IndicadoresBundle\Validator as CustomAssert;

/**
 * MatrizSeguimiento
 *
 * @ORM\Table(name="matriz_seguimiento")
 * @ORM\Entity(repositoryClass="ISECH\IndicadoresBundle\Entity\MatrizSeguimientoRepository")
 */
class MatrizSeguimiento
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
     * @ORM\Column(name="anio", type="string", length=4)
     */
    private $anio;

    /**
     * @var string
     *
     * @ORM\Column(name="etab", type="boolean", nullable=true)
     */
    private $etab;

    /**
     * @var string
     *
     * @ORM\Column(name="meta", type="string", length=65, nullable=true)
     */
    private $meta;

    /**
     *
     * @ORM\ManyToOne(targetEntity="MatrizIndicadoresDesempeno")
     * @ORM\JoinColumn(name="id_desempeno", referencedColumnName="id")
     * @ORM\OrderBy({"id" = "ASC"})
     **/
    private $desempeno;

    /**
     * @var integer
     *
     * @ORM\Column(name="indicador", type="integer", nullable=false)
     */
    private $indicador;

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

	public function __construct()
    {
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
     * Set anio
     *
     * @param string $anio
     *
     * @return MatrizPlanificado
     */
    public function setAnio($anio)
    {
        $this->anio = $anio;

        return $this;
    }

    /**
     * Get anio
     *
     * @return string
     */
    public function getAnio()
    {
        return $this->anio;
    }

    /**
     * Set etab
     *
     * @param boolean $etab
     *
     * @return MatrizPlanificado
     */
    public function setEtab($etab)
    {
        $this->etab = $etab;

        return $this;
    }

    /**
     * Get etab
     *
     * @return boolean
     */
    public function getEtab()
    {
        return $this->etab;
    }

    /**
     * Set indicador
     *
     * @param integer $indicador
     *
     * @return MatrizPlanificado
     */
    public function setIndicador($indicador)
    {
        $this->indicador = $indicador;

        return $this;
    }

    /**
     * Get indicador
     *
     * @return integer
     */
    public function getIndicador()
    {
        return $this->indicador;
    }

    /**
     * Set desempeno
     *
     * @param \ISECH\IndicadoresBundle\Entity\MatrizIndicadoresDesempeno $desempeno
     *
     * @return MatrizPlanificado
     */
    public function setDesempeno(\ISECH\IndicadoresBundle\Entity\MatrizIndicadoresDesempeno $desempeno = null)
    {
        $this->desempeno = $desempeno;

        return $this;
    }

    /**
     * Get desempeno
     *
     * @return \ISECH\IndicadoresBundle\Entity\MatrizIndicadoresDesempeno
     */
    public function getDesempeno()
    {
        return $this->desempeno;
    }

    /**
     * Set meta
     *
     * @param string $meta
     *
     * @return MatrizSeguimiento
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Get meta
     *
     * @return string
     */
    public function getMeta()
    {
        return $this->meta;
    }
}
