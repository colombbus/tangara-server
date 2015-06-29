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

namespace Tangara\CoreBundle\Manager;

use Doctrine\ORM\EntityRepository;

abstract class BaseManager {
    public function persistAndFlush($entity) {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function persist($entity) {
        $this->em->persist($entity);
    }
}
