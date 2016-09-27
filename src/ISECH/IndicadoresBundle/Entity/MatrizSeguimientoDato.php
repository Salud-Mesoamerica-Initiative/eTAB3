<?php

namespace ISECH\IndicadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ISECH\IndicadoresBundle\Validator as CustomAssert;

/**
 * MatrizIndicadoresDesempeno
 *
 * @ORM\Table(name="matriz_seguimiento_dato")
 * @ORM\Entity(repositoryClass="ISECH\IndicadoresBundle\Entity\MatrizSeguimientoDatoRepository")
 */
class MatrizSeguimientoDato
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
     *
     * @ORM\ManyToOne(targetEntity="MatrizSeguimiento")
     * @ORM\JoinColumn(name="id_matriz", referencedColumnName="id")
     * @ORM\OrderBy({"id" = "ASC"})
     **/
    private $matriz;

    /**
     * @var string
     *
     * @ORM\Column(name="mes", type="string", length=20)
     */
    private $mes;

    /**
     * @var string
     *
     * @ORM\Column(name="planificado", type="string", length=20)
     */
    private $planificado;

    /**
     * @var string
     *
     * @ORM\Column(name="real", type="string", length=20, nullable=true)
     */
    private $real;

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
     * Set mes
     *
     * @param string $mes
     *
     * @return MatrizSeguimientoDato
     */
    public function setMes($mes)
    {
        $this->mes = $mes;

        return $this;
    }

    /**
     * Get mes
     *
     * @return string
     */
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * Set planificado
     *
     * @param string $planificado
     *
     * @return MatrizSeguimientoDato
     */
    public function setPlanificado($planificado)
    {
        $this->planificado = $planificado;

        return $this;
    }

    /**
     * Get planificado
     *
     * @return string
     */
    public function getPlanificado()
    {
        return $this->planificado;
    }

    /**
     * Set real
     *
     * @param string $real
     *
     * @return MatrizSeguimientoDato
     */
    public function setReal($real)
    {
        $this->real = $real;

        return $this;
    }

    /**
     * Get real
     *
     * @return string
     */
    public function getReal()
    {
        return $this->real;
    }

    /**
     * Set matriz
     *
     * @param \ISECH\IndicadoresBundle\Entity\MatrizSeguimiento $matriz
     *
     * @return MatrizSeguimientoDato
     */
    public function setMatriz(\ISECH\IndicadoresBundle\Entity\MatrizSeguimiento $matriz = null)
    {
        $this->matriz = $matriz;

        return $this;
    }

    /**
     * Get matriz
     *
     * @return \ISECH\IndicadoresBundle\Entity\MatrizSeguimiento
     */
    public function getMatriz()
    {
        return $this->matriz;
    }
}
