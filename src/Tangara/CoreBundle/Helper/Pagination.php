<?php

namespace Tangara\CoreBundle\Helper;

use Knp\Bundle\PaginatorBundle\Definition\PaginatorAware;
use Doctrine\ORM\EntityManager;

class Pagination extends PaginatorAware {

    protected $manager;

    function __construct(EntityManager $em) {
        $this->manager = $em;
    }
    
    public function paginate($request, $tag, $entity, $parameters=false) {
        $session = $request->getSession();
        
        // handle page number
        $page = $request->get("page", 1);
        $session->set($tag."_page", $page);           

        // handle search
        $search = $request->get("search", false);        
        if ($search !== false) {
            // search set: store it in session
            if (strlen(trim($search))==0) {
                // reset search
                $session->set($tag."_search", false);           
            } else {
                $session->set($tag."_search", $search);
            }
        }        
        // get search from session if any
        $search = $session->get($tag."_search", false);
        
        // get users
        $repository = $this->manager->getRepository($entity);
        
        if ($search !== false) {
            if ($parameters!== false) {
                $data = $repository->getSearchQuery($search, $parameters);
            } else {
                $data = $repository->getSearchQuery($search);
            }
        } else {
            if ($parameters!== false) {
                $data = $repository->findAll($parameters);
            } else {
                $data = $repository->findAll();
            }
        }
        // TODO: put itemNumber as parameter
        return $this->getPaginator()->paginate($data, $page, 10);
    }

}