<?php

/*
 * Copyright (C) 2014 Régis
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Description of ProfileController
 *
 * @author Régis
 */

namespace Tangara\CoreBundle\Controller;

use FOS\UserBundle\Controller\ProfileController as BaseController;
use Tangara\CoreBundle\Entity\Group;

class ProfileController extends BaseController {

    //Controller to get the user main page
    public function profileAction() {

        //$response = parent::profileAction();
        //$user = parent::showAction();
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        $this->container->get('session')->getFlashBag()->add(
            'notice',
            'Vos changements ont été sauvegardés!'
        );
                
        return $this->container->get('templating')->renderResponse('TangaraCoreBundle:Profile:show.html.' . $this->container->getParameter('fos_user.template.engine'), 
                array('user' => $user));
    }

}
