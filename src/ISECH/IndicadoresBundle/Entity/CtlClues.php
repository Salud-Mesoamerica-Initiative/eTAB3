<?php

namespace ISECH\IndicadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use ISECH\IndicadoresBundle\Validator as CustomAssert;
use Doctrine\ORM\EntityManager as Manager;

/**
 * ISECH\IndicadoresBundle\Entity\CtlClues
 *
 * @ORM\Table(name="ctl_clues")
 * @ORM\Entity
 */
class CtlClues
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="text", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
 

    /**
     * @var string $descripcion
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;		
	
	/**
     * @var string $jurisdiccion
     *
     * @ORM\Column(name="jurisdiccion", type="text", nullable=true)
     */
    private $jurisdiccion;		
	
	/**
     * @var string $municipio
     *
     * @ORM\Column(name="municipio", type="text", nullable=true)
     */
    private $municipio;		
	
	/**
     * @var string $localidad
     *
     * @ORM\Column(name="localidad", type="text", nullable=true)
     */
    private $localidad;		
	
	/**
     * @var string $tipo_unidad
     *
     * @ORM\Column(name="tipo_unidad", type="text", nullable=true)
     */
    private $tipo_unidad;		
	
	/**
     * @var string $tipologia
     *
     * @ORM\Column(name="tipologia", type="text", nullable=true)
     */
    private $tipologia;		
    
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
     * Set descripcion
     *
     * @param  string $descripcion
     * @return Campo
     */
    public function setDescripcion($descripcion)
    {		
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }
		
    /**
     * Set jurisdiccion
     *
     * @param  string $jurisdiccion
     * @return Campo
     */
    public function setJurisdiccion($descripcion)
    {		
        $this->jurisdiccion = $jurisdiccion;

        return $this;
    }

    /**
     * Get jurisdiccion
     *
     * @return string
     */
    public function getJurisdiccion()
    {
        return $this->jurisdiccion;
    }
	
	/**
     * Set municipio
     *
     * @param  string $municipio
     * @return Campo
     */
    public function setMunicipio($municipio)
    {		
        $this->municipio = $municipio;

        return $this;
    }

    /**
     * Get municipio
     *
     * @return string
     */
    public function getMunicipio()
    {
        return $this->municipio;
    }
	
	/**
     * Set localidad
     *
     * @param  string $localidad
     * @return Campo
     */
    public function setLocalidad($localidad)
    {		
        $this->localidad = $localidad;

        return $this;
    }

    /**
     * Get localidad
     *
     * @return string
     */
    public function getLocalidad()
    {
        return $this->localidad;
    }
	
	/**
     * Set tipo_unidad
     *
     * @param  string $tipo_unidad
     * @return Campo
     */
    public function setTipoUnidad($tipo_unidad)
    {		
        $this->tipo_unidad = $tipo_unidad;

        return $this;
    }

    /**
     * Get tipo_unidad
     *
     * @return string
     */
    public function getTipoUnidad()
    {
        return $this->tipo_unidad;
    }
	
	/**
     * Set tipologia
     *
     * @param  string $tipologia
     * @return Campo
     */
    public function setTipologia($tipologia)
    {		
        $this->tipologia = $tipologia;

        return $this;
    }

    /**
     * Get tipologia
     *
     * @return string
     */
    public function getTipologia()
    {
        return $this->tipologia;
    }
	
    public function __toString()
    {
		try 
		{				
			return (string)$this->descripcion ? : '';
		} 
		catch (Exception $exception) 
		{
			return $exception;
		}
		
		
    }

}
