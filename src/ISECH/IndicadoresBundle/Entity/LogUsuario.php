<?php

namespace ISECH\IndicadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ISECH\IndicadoresBundle\Validator as CustomAssert;

/**
 * LogUsuario
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="ISECH\IndicadoresBundle\Entity\LogUsuarioRepository")
 */
class LogUsuario
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
     * @var \string
     *
     * @ORM\Column(name="usuario", type="string", length=300)
	 * @CustomAssert\OnlyAlphanumeric(message="OnlyAlphanumeric.Message")
     */
    private $usuario;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creado", type="datetime")
     */
    private $creado;

    /**
     * @var \string
     *
     * @ORM\Column(name="ip", type="string", length=30)
     */
    private $ip;

    /**
     * @var \string
     *
     * @ORM\Column(name="mac", type="string", length=30)
     */
    private $mac;

    /**
     * @var string
     *
     * @ORM\Column(name="info", type="string", length=300)
     */
    private $info;

    /**
     * @var string
     *
     * @ORM\Column(name="accion", type="string", length=300)
     */
    private $accion;

    /**
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=300)
     */
    private $class;

	
	public function __construct()
    {
        $this->setCreado(new \DateTime());
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
     * Set usuario
     *
     * @param string $usuario
     *
     * @return LogUsuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return string
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set creado
     *
     * @param \DateTime $creado
     *
     * @return LogUsuario
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
     * Set ip
     *
     * @param string $ip
     *
     * @return LogUsuario
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set mac
     *
     * @param string $mac
     *
     * @return LogUsuario
     */
    public function setMac($mac)
    {
        $this->mac = $mac;

        return $this;
    }

    /**
     * Get mac
     *
     * @return string
     */
    public function getMac()
    {
        return $this->mac;
    }

    /**
     * Set info
     *
     * @param string $info
     *
     * @return LogUsuario
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set accion
     *
     * @param string $accion
     *
     * @return LogUsuario
     */
    public function setAccion($accion)
    {
        $this->accion = $accion;

        return $this;
    }

    /**
     * Get accion
     *
     * @return string
     */
    public function getAccion()
    {
        return $this->accion;
    }

    /**
     * Set class
     *
     * @param string $class
     *
     * @return LogUsuario
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
}
