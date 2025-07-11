<?php

namespace ContainerWkETnLp;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getCountryControllerService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'App\Controller\CountryController' shared autowired service.
     *
     * @return \App\Controller\CountryController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/framework-bundle/Controller/AbstractController.php';
        include_once \dirname(__DIR__, 4).'/src/Controller/CountryController.php';
        include_once \dirname(__DIR__, 4).'/src/Model/CountryScenarios.php';
        include_once \dirname(__DIR__, 4).'/src/Model/CountryRepository.php';
        include_once \dirname(__DIR__, 4).'/src/Rdb/CountryStorage.php';
        include_once \dirname(__DIR__, 4).'/src/Rdb/SqlHelper.php';

        $container->services['App\\Controller\\CountryController'] = $instance = new \App\Controller\CountryController(new \App\Model\CountryScenarios(new \App\Rdb\CountryStorage(new \App\Rdb\SqlHelper())));

        $instance->setContainer(($container->privates['.service_locator.ZyP9f7K'] ?? $container->load('get_ServiceLocator_ZyP9f7KService'))->withContext('App\\Controller\\CountryController', $container));

        return $instance;
    }
}
