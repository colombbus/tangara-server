<?php

namespace Tangara\CoreBundle\Controller;


use FOS\UserBundle\Controller\ProfileController as BaseController;

class FOSProfileController extends BaseController
{
    /*
     * Si on doit rajouter des instructions en plus,
     * alors on creer les actions
     * puis on fait les instruction du parent()
     * et on fait une redirection vers le controller TangaraCoreBundle:Profile
     * et dans le controller TangaraCoreBundle:Profile on rajoute les instructions
     * et toujours dans le controller TangaraCoreBundle:Profile on fait un render le template
     */
}
