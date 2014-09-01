<?php

namespace Tangara\CoreBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FOSGroupControllerTest extends WebTestCase
{
  public function testIndex()
    {
        /*
$user = new User();
$user->setRole('ROLE_ADMIN');
$user->setUsername('tangara-admin');
$user->setPassword('password');
*/
        
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertTrue(true);
    }
    
    public function getMockEntityUser($value) {
        $employee = $this->getMock('\Acme\DemoBundle\Entity\Employee');
        $employee->expects($this->once())
                ->method('getSalary')
                ->will($this->returnValue($value));
        $employee->expects($this->once())
                ->method('getBonus')
                ->will($this->returnValue($value + 100));
    }

    public function getMockRepositoryUser($employee) {
        // Maintenant, mockez le repository pour qu'il retourne un mock de l'objet emloyee
        $employeeRepository = $this->getMockBuilder('\Doctrine\ORM\EntityRepository')
                ->disableOriginalConstructor()
                ->getMock();
        $employeeRepository->expects($this->once())
                ->method('find')
                ->will($this->returnValue($employee));
    }

    public function getMockEntityManager($employeeRepository) {
        // Et enfin, mockez l'EntityManager pour qu'il retourne un mock du repository
        $entityManager = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
                ->disableOriginalConstructor()
                ->getMock();
        $entityManager->expects($this->once())
                ->method('getRepository')
                ->will($this->returnValue($employeeRepository));
    }

    public function testUserCase() {
        $salaryCalculator = new SalaryCalculator($entityManager);
        $this->assertEquals(2100, $salaryCalculator->calculateTotalSalary(1));
    }

}
