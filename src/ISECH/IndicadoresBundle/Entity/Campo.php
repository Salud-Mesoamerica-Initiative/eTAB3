<?php

namespace ISECH\IndicadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ISECH\IndicadoresBundle\Validator as CustomAssert;

/**
 * ISECH\IndicadoresBundle\Entity\Campo
 *
 * @ORM\Table(name="campo")
 * @ORM\Entity
 */
class Campo
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
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=100, nullable=false)
     * @CustomAssert\OnlyAlphanumeric(message="OnlyAlphanumeric.Message")
     */
    private $nombre;

    /**
     * @var string $descripcion
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;

    /**
     * @var string $formula
     *
     * @ORM\Column(name="formula", type="text", nullable=true)
     */
    private $formula;

    /**
     * @ORM\ManyToOne(targetEntity="OrigenDatos", inversedBy="campos")
     * @ORM\JoinColumn(name="id_origen_datos", referencedColumnName="id", onDelete="CASCADE")
     * */
    private $origenDato;

    /**
     * @ORM\ManyToOne(targetEntity="TipoCampo")
     * @ORM\JoinColumn(name="id_tipo_campo", referencedColumnName="id")
     * */
    private $tipoCampo;

    /**
     * @ORM\ManyToOne(targetEntity="SignificadoCampo")
     * @ORM\JoinColumn(name="id_significado_campo", referencedColumnName="id")
     * */
    private $significado;

    /**
     * @ORM\ManyToOne(targetEntity="Diccionario")
     * @ORM\JoinColumn(name="id_diccionario", referencedColumnName="id")
     * */
    private $diccionario;

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
     * @param  string $nombre
     * @return Campo
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
     * Set tipoCampo
     *
     * @param  ISECH\IndicadoresBundle\Entity\TipoCampo $tipoCampo
     * @return Campo
     */
    public function setTipoCampo(\ISECH\IndicadoresBundle\Entity\TipoCampo $tipoCampo = null)
    {
        $this->tipoCampo = $tipoCampo;

        return $this;
    }

    /**
     * Get tipoCampo
     *
     * @return ISECH\IndicadoresBundle\Entity\TipoCampo
     */
    public function getTipoCampo()
    {
        return $this->tipoCampo;
    }

    /**
     * Set origenDato
     *
     * @param  ISECH\IndicadoresBundle\Entity\OrigenDatos $origenDato
     * @return Campo
     */
    public function setOrigenDato(\ISECH\IndicadoresBundle\Entity\OrigenDatos $origenDato = null)
    {
        $this->origenDato = $origenDato;

        return $this;
    }

    /**
     * Get origenDato
     *
     * @return ISECH\IndicadoresBundle\Entity\OrigenDatos
     */
    public function getOrigenDato()
    {
        return $this->origenDato;
    }

    /**
     * Set significado
     *
     * @param  ISECH\IndicadoresBundle\Entity\SignificadoCampo $significado
     * @return Campo
     */
    public function setSignificado(\ISECH\IndicadoresBundle\Entity\SignificadoCampo $significado = null)
    {
        $this->significado = $significado;

        return $this;
    }

    /**
     * Get significado
     *
     * @return ISECH\IndicadoresBundle\Entity\SignificadoCampo
     */
    public function getSignificado()
    {
        return $this->significado;
    }

    /**
     * Set id
     *
     * @param  integer $id
     * @return Campo
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set diccionario
     *
     * @param  \ISECH\IndicadoresBundle\Entity\Diccionario $diccionario
     * @return Campo
     */
    public function setDiccionario(\ISECH\IndicadoresBundle\Entity\Diccionario $diccionario = null)
    {
        $this->diccionario = $diccionario;

        return $this;
    }

    /**
     * Get diccionario
     *
     * @return \ISECH\IndicadoresBundle\Entity\Diccionario
     */
    public function getDiccionario()
    {
        return $this->diccionario;
    }

    /**
     * Set formula
     *
     * @param  string $formula
     * @return Campo
     */
    public function setFormula($formula)
    {
        $this->formula = $formula;

        return $this;
    }

    /**
     * Get formula
     *
     * @return string
     */
    public function getFormula()
    {
        return $this->formula;
    }

    public function __toString()
    {
        return $this->nombre ? : '';
    }

}
