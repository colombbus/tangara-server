<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // Symfony2 core
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            
            // Tangara Bundle dependencies
            new FOS\UserBundle\FOSUserBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            /* new Ladela\SlickGridBundle\LadelaSlickGridBundle(), */
            //new Norzechowicz\AceEditorBundle\NorzechowiczAceEditorBundle(),
            //new Braincrafted\Bundle\BootstrapBundle\BraincraftedBootstrapBundle(),
            
            // Tangara Bundles
            //new Tangara\UserBundle\TangaraUserBundle(),
            //new Tangara\EditorBundle\TangaraEditorBundle(),
            //new Tangara\SVGEditorBundle\TangaraSVGEditorBundle(),
            //new Tangara\wPaintBundle\TangarawPaintBundle(),
            new Tangara\CoreBundle\TangaraCoreBundle()
            
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
    
}
