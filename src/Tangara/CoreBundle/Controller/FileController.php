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

    public function sendContentAction($cat, $project) {
        $request = $this->getRequest();
        //$user = $request->get('security.context')->getToken()->getUser()->getId();
        if ($request->isXmlHttpRequest()) {
            if ($request->query->get('sendfile'))
                echo "USER PROJECT";

            //$filename = $request->query->get('wanted');
            //$toSend = $request->request->get('filename');

 
            
            if ($filename) {
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
    public function getResourcesAction($cat, Project $project) {
        $request = $this->getRequest();
        // check($user, $project);
        //if ($request->isXmlHttpRequest()) {
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
        //}
    }

    //getContentAction
    public function getFilesAction($cat, Project $project) {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $auth = $this->get('tangara_core.project_manager')->isAuthorized($project, $user);
        if (!$auth)
            return $this->render('TangaraCoreBundle:Default:forbidden.html.twig');
        var_dump($prj);
        exit();
        
        $request = $this->getRequest();
        if ($request->query->get('filename'))
            echo "USER PROJECT";
        echo "reponse " . $project->isGranted($user);
        exit();
        
        return new Response("request");
        
    }

    public function removeFileAction(Project $project) {

        $fileName = null;

        if ($request->query->get('removedfile')) {
            $fileName = $request->query->get('removedfile');
        }

        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {

            //verifie si le fichier existe, si vrai
            if ($fileName) {
                $em = $this->getDoctrine()->getManager();
                $fileRepository = $em->getRepository('TangaraCoreBundle:Document');

                $file = $fileRepository->findBy(array('path' => $fileName));

                $em->remove($file);
                $em->flush();
            }
        }
    }

    public function getTgrContentAction(Project $project) {
        if ($request->query->get('tgrfile'))
            echo "USER PROJECT";
    }

    public function getParseContentAction(Project $project) {
        if ($request->query->get('parsefile'))
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

}
