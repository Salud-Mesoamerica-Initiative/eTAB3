<?php

namespace ISECH\IndicadoresBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class MatrizIndicadoresRelAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1, // Display the first page (default = 1)
        '_sort_order' => 'ASC', // Descendant ordering (default = 'ASC')
        '_sort_by' => 'nombre' // name of the ordered field (default = the model id field, if any)
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        if($this->getRequest()->get('_sonata_admin') == 'sonata.admin.indicadores_rel') {
            $formMapper
                ->add('desempeno', null, array('label' => $this->getTranslator()->trans('_indicador_desempeno_')))
                ->add('fuente', null, array('label' => $this->getTranslator()->trans('_fuente_')));
        }
        $formMapper
                ->add('nombre', null, array('label' => $this->getTranslator()->trans('nombre')))   
                ->add('fuente', null, array('label' => $this->getTranslator()->trans('_fuente_')))             
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
                ->add('nombre', null, array('label' => $this->getTranslator()->trans('nombre')))
                ->add('desempeno', null, array('label' => $this->getTranslator()->trans('_indicador_desempeno_')))
                ->add('fuente', null, array('label' => $this->getTranslator()->trans('_fuente_')))
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
                ->addIdentifier('nombre', null, array('label' => $this->getTranslator()->trans('nombre')))
                ->add('desempeno', null, array('label' => $this->getTranslator()->trans('_indicador_desempeno_')))
                ->add('fuente', null, array('label' => $this->getTranslator()->trans('_fuente_')))

        ;
    }
    

    public function getBatchActions()
    {
        $actions = parent::getBatchActions();
        $actions['delete'] = null;
    }
}
