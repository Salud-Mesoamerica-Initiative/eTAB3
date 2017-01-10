<?php

namespace ISECH\IndicadoresBundle\Entity;

use Doctrine\ORM\EntityRepository;

use ISECH\IndicadoresBundle\Entity\GrupoIndicadores;

/**
 * GrupoIndicadoresRepository
 *
 */
class GrupoIndicadoresRepository extends EntityRepository
{
    public function getIndicadoresSala(GrupoIndicadores $sala)
    {
        $em = $this->getEntityManager();

        $dql = "SELECT i.filtro, i.dimension, i.posicion, i.tipoGrafico, i.orden,
                    f.id as idIndicador, i.filtroPosicionDesde, i.filtroPosicionHasta,
                    i.filtroElementos
                    FROM IndicadoresBundle:GrupoIndicadoresIndicador i
                    JOIN i.indicador f
                    WHERE
                        i.grupo = :sala";
        $query = $em->createQuery($dql);
        $query->setParameter('sala', $sala->getId());

        return $query->getArrayResult();
    }
	public function getSalaGrupo($user)
    {        
		$sql="SELECT i.grupoindicadores_id
                     FROM group_grupoindicadores i
                     LEFT JOIN fos_user_user_group g on g.group_id=i.group_id
                     WHERE
                     g.user_id = '$user'";
					 
		$query=$this->getEntityManager()->getConnection()->executeQuery($sql)->fetchAll();
		
        return $query;
    }
}
