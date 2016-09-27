<?php

namespace ISECH\IndicadoresBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * BoletinRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BoletinRepository extends EntityRepository
{
	public function getUsuarioGrupo($grupo)
    {							
		$stmt = $this->getEntityManager()
		->getConnection()
		->prepare("SELECT * FROM fos_user_user u left join fos_user_user_group g on g.user_id=u.id where g.group_id='".$grupo->getId()."'");
		$stmt->execute();
		return $stmt->fetchAll();
	}
	public function getRuta($sala,$token)
    {							
		$stmt = $this->getEntityManager()
		->getConnection()
		->prepare("SELECT * FROM boletin b left join grupo_indicadores s on s.id=b.sala where b.token='$token' and ".((gettype($sala) == 'integer') ? "s.id=".$sala :  "s.nombre='".$sala."'"));
		$stmt->execute();
		$result= $stmt->fetchAll();
		
		foreach($result as $res)
		{
			$tiempo=$this->duracion(\DateTime::createFromFormat('Y-m-d H:i:s',$res['actualizado']));
			if(stripos($tiempo,'dia'))
			{
				if(substr($tiempo,0,stripos($tiempo,' '))>2)
				return "Error";
			}
		}
		return $result;
	}
	
	public function getGroup()
    {							
		$stmt = $this->getEntityManager()
		->getConnection()
		->prepare("SELECT * FROM fos_user_group ");
		$stmt->execute();
		$result= $stmt->fetchAll();
		
		return $result;
	}
	public function duracion(\DateTime $dateTime)
    {
        $delta = time() - $dateTime->getTimestamp();
        if ($delta < 0)
            throw new \Exception("createdAgo is unable to handle dates in the future");

        $duration = "";
        if ($delta < 60)
        {
            // Segundos
            $time = $delta;
            $duration = $time . " segundo" . (($time > 1) ? "s" : "") ;
        }
        else if ($delta <= 3600)
        {
            // Minutos
            $time = floor($delta / 60);
            $duration = $time . " minuto" . (($time > 1) ? "s" : "") ;
        }
        else if ($delta <= 86400)
        {
            // Horas
            $time = floor($delta / 3600);
            $duration = $time . " hora" . (($time > 1) ? "s" : "") ;
        }
        else
        {
            // Días
            $time = floor($delta / 86400);
            $duration = $time . " dia" . (($time > 1) ? "s" : "") ;
        }

        return $duration;
    }
}