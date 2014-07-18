<?php

namespace Tangara\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tangara\CoreBundle\Form\ProjectType;
use Tangara\CoreBundle\Entity\Document;
use Tangara\CoreBundle\Entity\Project;
use Tangara\CoreBundle\Entity\User;
use Tangara\CoreBundle\Entity\Group;

class FileController extends Controller {

    function check($user, $project) {
        // Check if 
        $groupList = $user->getGroups();
        $projectGroup = $project->getGroup();

//        if (in_array($projectGroup, $groupList))
//                echo "granted !!!!";
//        exit();
//
//        if ($user)
//            return false;
//        else {
//            return true;
//            /* check if directory exists */
//            $project_path = $base_path . "/" . $project_id;
//
//            if (!$fs->exists($project_path)) {
//                $fs->mkdir($project_path);
//            }
//        }
    }

    public function testAction(Project $project) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $projectPath = $this->container->getParameter('tangara_core.settings.directory.upload');
        $tangaraPath = $this->container->getParameter('tangara_core.settings.directory.tangarajs');

        echo "Uploadr path " . $projectPath . "<br/>";
        echo "Tangara path " . $tangaraPath . "<br/>";
        $this->check($user, $project);


        $this->get('session')->getFlashBag()->add(
                'notice', 'Vos changements ont été sauvegardés!'
        );


        return $this->render('TangaraCoreBundle::test.html.twig');
    }

    public function sendContentAction() {
        $request = $this->getRequest();
        //$id = $request->get('security.context')->getToken()->getUser()->getId();
        if ($request->isXmlHttpRequest()) {
            if ($request->query->get('userproject'))
                echo "USER PROJECT";

            if ($request->query->get('filename')) {
                $filename = $request->query->get('filename');
                $project_id = 23;
                $user_id = 2;
                $base_path = 'C:\tangara';
                $project_user_path = $base_path . "/" . $user_id;
                $project_path = $base_path . "/" . $project_id;
                $filepath = $project_path . "/" . $filename;
                $fs = new Filesystem();

                if ($fs->exists($filepath))
                    return new BinaryFileResponse($filepath);
            }
        }
    }

    /**
     * Get all resources included in a project
     * @param \Tangara\CoreBundle\Entity\Project $project
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getResourcesAction(Project $project) {

    // check($user, $project);
        if ($request->isXmlHttpRequest()) {
            $projectList = $this->getDoctrine()
                    ->getManager()
                    ->getRepository('TangaraCoreBundle:Document')
                    ->findByOwnerProject($project->getId());
            
            foreach ($projectList as $prj) {
                $files[] = $prj->getPath();
            }
            $response = new JsonResponse();
            $response->setData($files);

            return $response;
        }
    }
    //getContentAction
    public function getFilesAction(Project $project) {
        $request = $this->getRequest();
        if ($request->query->get('filename'))
            echo "USER PROJECT";
        return $this->redirect($this->generateUrl('tangara_core_homepage'));
    }

    public function removeFileAction(Project $project) {
        if ($request->query->get('filename'))
            echo "USER PROJECT";
    }

    public function getTgrContentAction(Project $project) {
        if ($request->query->get('filename'))
            echo "USER PROJECT";
    }

    public function getParseContentAction(Project $project) {
        if ($request->query->get('filename'))
            echo "USER PROJECT";
    }

    public function getGrantedAction() {
        $manager = $this->getDoctrine()->getManager();
        $repository = $manager->getRepository('TangaraCoreBundle:Project');

        $query = $repository->findGranted();

        return $this->render('TangaraCoreBundle:Default:granted.html.twig', array(
                    'query' => $query
        ));
    }
    /*
    public function getContentTgrAction() {
        $request = $this->getRequest();
        $locale = $request->getLocale();

        if ($request) {
            $file = 'C:/Bin/cmd_aliases.txt';
            $response = new BinaryFileResponse($file);
            
            return $response;
        }
        
        
            public function getAjaxAction() {
        $data = "ok";
        $request = $this->getRequest();

        $data = $this->getDoctrine()
                ->getManager()
                ->getRepository('TangaraCoreBundle:Project')
                ->myFindAll();

        if ($this->getRequest()->isMethod('POST')) {
            $data = $request->request->get('data');
            //var_dump($request->request->all());
        }
        if ($this->getRequest()) {
            //$this->getRequest()->request();

            return new Response('Reçu en POST : ' . $data);
        }

        //return new Response('<h1>Reçu en normal</h1>');
    }
        
        
        */
}
