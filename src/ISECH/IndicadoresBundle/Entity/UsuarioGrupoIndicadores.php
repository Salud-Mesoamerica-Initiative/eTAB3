<?php

namespace ISECH\IndicadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ISECH\IndicadoresBundle\Entity\UsuarioGrupoIndicadores
 *
 * @ORM\Table(name="usuario_grupo_indicadores")
 * @ORM\Entity
 */
class UsuarioGrupoIndicadores
{
    /**
     *
     * @ORM\ManyToOne(targetEntity="GrupoIndicadores", inversedBy="usuarios")
     * @ORM\JoinColumn(name="grupo_indicadores_id", referencedColumnName="id")
     * @ORM\Id
     */
    private $grupoIndicadores;

    /**
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="gruposIndicadores")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * @ORM\Id
     */
    private $usuario;

    /**
     * @var string $esDuenio
     *
     * @ORM\Column(name="es_duenio", type="boolean", nullable=true)
     */
    private $esDuenio;

    /**
     * Set esDuenio
     *
     * @param  boolean                 $esDuenio
     * @return UsuarioGrupoIndicadores
     */
    public function setEsDuenio($esDuenio)
    {
        $this->esDuenio = $esDuenio;

        return $this;
    }

    /**
     * Get esDuenio
     *
     * @return boolean
     */
    public function getEsDuenio()
    {
        return $this->esDuenio;
    }

    /**
     * Set grupoIndicadores
     *
     * @param  \ISECH\IndicadoresBundle\Entity\GrupoIndicadores $grupoIndicadores
     * @return UsuarioGrupoIndicadores
     */
    public function setGrupoIndicadores(\ISECH\IndicadoresBundle\Entity\GrupoIndicadores $grupoIndicadores)
    {
        $this->grupoIndicadores = $grupoIndicadores;

        return $this;
    }

    /**
     * Get grupoIndicadores
     *
     * @return \ISECH\IndicadoresBundle\Entity\GrupoIndicadores
     */
    public function getGrupoIndicadores()
    {
        return $this->grupoIndicadores;
    }

    /**
     * Set usuario
     *
     * @param  \ISECH\IndicadoresBundle\Entity\User $usuario
     * @return UsuarioGrupoIndicadores
     */
    public function setUsuario(\ISECH\IndicadoresBundle\Entity\User $usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \ISECH\IndicadoresBundle\Entity\User
     */
    public function getUsuario()
    {
        return $this->usuario;
    }
    
    public function __toString()
    {
        return $this->grupoIndicadores->getNombre();
    }
}
