<?php

namespace ISECH\IndicadoresBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use ISECH\IndicadoresBundle\Entity\MatrizIndicadoresDesempeno;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

class MatrizIndicadoresDesempenoAdmin extends Admin
{   
    private $repository;
	protected $datagridValues = array(
        '_page' => 1, // Display the first page (default = 1)
        '_sort_order' => 'ASC', // Descendant ordering (default = 'ASC')
        '_sort_by' => 'nombre' // name of the ordered field (default = the model id field, if any)
    );	
    public function setRepository($repository)
    {
        $this->repository = $repository;
    }
	public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('nombre') 
            ->add('matrizIndicadoresEtab', null, array('label' => $this->getTranslator()->trans('_indicador_etab_'),                 
                    'expanded' => true,
                    'class' => 'IndicadoresBundle:FichaTecnica',
                    'query_builder' => function ($repository) {
                        return $repository->createQueryBuilder('i');
                    }))
                ->add('indicators', 'sonata_type_collection', 
                array(              
                    'label' => $this->getTranslator()->trans('_indicador_rel_'),
                    'required' => true
                ), 
                array(
                    'class'=>'form-control',
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'position'                                    
                ))           
			->add('creado')
			->add('actualizado')
        ;
    }
	public function configureFormFields(FormMapper $formMapper) 
	{
        $formMapper
                ->add('nombre', null, array('label' => $this->getTranslator()->trans('nombre')))
				->add('matrizIndicadoresEtab', null, array('label' => $this->getTranslator()->trans('_indicador_etab_'),                 
                    'expanded' => true,
                    'class' => 'IndicadoresBundle:FichaTecnica',
                    'query_builder' => function ($repository) {
                        return $repository->createQueryBuilder('i');
                    }))
                ->add('indicators', 'sonata_type_collection', 
                array(              
                    'label' => $this->getTranslator()->trans('_indicador_rel_'),
                    'required' => true
                ), 
                array(
                    'class'=>'form-control',
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'position'                                    
                ))
        ;
    }
	public function configureDatagridFilters(DatagridMapper $datagridMapper) 
	{
        $datagridMapper
                ->add('nombre', null, array('label' => $this->getTranslator()->trans('nombre')))
        ;
    }

    public function configureListFields(ListMapper $listMapper) 
	{
        $listMapper
                ->addIdentifier('nombre', null, array('label' => $this->getTranslator()->trans('nombre')))				
                ->add('creado', null, array('label' => $this->getTranslator()->trans('creado')))
				->add('actualizado', null, array('label' => $this->getTranslator()->trans('actualizado')))
        ;
    }
	public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
        ->with('nombre')
	        ->assertLength(array('max' => 500))
    	    ->end()
    	;
    }

	public function getBatchActions() {
        $actions = parent::getBatchActions();
        $actions['delete'] = null;
    }
	public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                return 'IndicadoresBundle:CRUD:campo-edit.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }    
    
    public function getExportFields()
    {
    	return array('id', 'nombre', 'creado', 'actualizado');
    }

    public function prePersist($indicators)
    {
        $this->setIndicadors($indicators);
    }

    public function setIndicadors($MatrizIndicadoresDesempeno)
    {
        $indicators = $MatrizIndicadoresDesempeno->getIndicators();
        $MatrizIndicadoresDesempeno->removeIndicators();
        if (count($indicators) > 0) {
            foreach ($indicators as $indicator) {
                $indicator->setDesempeno($MatrizIndicadoresDesempeno);
                $MatrizIndicadoresDesempeno->addIndicator($indicator);
            }
        }
    }

    public function preUpdate($indicators)
    {
        $this->setIndicadors($indicators);       
    }
}
?>