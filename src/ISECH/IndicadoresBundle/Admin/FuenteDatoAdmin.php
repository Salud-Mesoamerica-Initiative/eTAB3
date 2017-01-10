<?php

namespace ISECH\IndicadoresBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

use Sonata\AdminBundle\Validator\ErrorElement;

class FuenteDatoAdmin extends Admin{
    protected $datagridValues = array(
        '_page' => 1, // Display the first page (default = 1)
        '_sort_order' => 'ASC', // Descendant ordering (default = 'ASC')
        '_sort_by' => 'contacto' // name of the ordered field (default = the model id field, if any)
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper            
            ->add('establecimiento', null, array('label'=> $this->getTranslator()->trans('last_fuente')))
			->add('contacto', null, array('label'=> $this->getTranslator()->trans('contacto')))
            ->add('correo', null, array('label'=> $this->getTranslator()->trans('correo_electronico')))
            ->add('telefono', null, array('label'=> $this->getTranslator()->trans('telefono')))
            ->add('cargo', null, array('label'=> $this->getTranslator()->trans('cargo')))
            /*->setHelps(array(
                'telefono' => $this->getTranslator()->trans('Formato XXXX-XXXX')
            ))*/
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('contacto', null, array('label'=> $this->getTranslator()->trans('contacto')))
            ->add('establecimiento',null, array('label'=> $this->getTranslator()->trans('last_fuente')))
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper            
            ->addIdentifier('establecimiento', null, array('label'=> $this->getTranslator()->trans('last_fuente')))
			->add('contacto', null, array('label'=> $this->getTranslator()->trans('contacto')))
            ->add('correo', null, array('label'=> $this->getTranslator()->trans('correo_electronico')))
            ->add('telefono', null, array('label'=> $this->getTranslator()->trans('telefono')))
            ->add('cargo', null, array('label'=> $this->getTranslator()->trans('cargo')))

        ;
    }
    
	public function validate(ErrorElement $errorElement, $object)
    {
    	$piecesURL = explode("/", $_SERVER['REQUEST_URI']);
    	$pieceAction = $piecesURL[count($piecesURL) - 1]; // create or update
    	$pieceId = $piecesURL[count($piecesURL) - 2]; // id/edit
    	 
    	$obj = new \ISECH\IndicadoresBundle\Entity\FuenteDato;
    	 
    	$rowsRD = $this->getModelManager()->findBy('IndicadoresBundle:FuenteDato',
    			array('establecimiento' => $object->getEstablecimiento()));
    
    	if (strpos($pieceAction,'create') !== false) // entra cuando es ALTA
    	{
    		if (count($rowsRD) > 0){
    			$errorElement
    			->with('establecimiento')
    			->addViolation($this->getTranslator()->trans('registro existente, no se puede duplicar'))
    			->end();
    		}
    	}
    	else // entra cuando es EDICION
    	{
    		if (count($rowsRD) > 0){
    			$obj = $rowsRD[0];
    			if ($obj->getId() != $pieceId)
    			{
    				$errorElement
    				->with('establecimiento')
    				->addViolation($this->getTranslator()->trans('registro existente, no se puede duplicar'))
    				->end();
    			}
    		}
    	}
    }

    public function getBatchActions()
    {
        $actions = parent::getBatchActions();
        $actions['delete'] = null;
    }
}
