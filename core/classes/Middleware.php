<?php

namespace core\classes;


use core\helpers\Response;
use core\security\Verify;
use core\traits\Singleton;
use ReflectionClass;

class Middleware extends Controller
{
    use Singleton;

    private array $userClasses;
    private const WAY = '../../src/web_back/';
    private const CONTROLLER = 'Controller';
    private const SECURITY = 'Security';

    private function __construct()
    {
        parent::__construct();
        $this->userClasses = $this->scanClasses(self::WAY);
    }

    private function scanClasses($dir): array
    {

        $result = array();

        $cdir = scandir($dir);

        foreach ($cdir as $key => $value)
        {

            if (!in_array($value,array(".","..")))
            {
                $currentWay = str_replace('../..', '', $dir);
                $currentWay = str_replace('/', '\\', $currentWay);
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$currentWay . $value] = $this->scanClasses($dir . DIRECTORY_SEPARATOR . $value);
                } else {
                    if ($value[0] === strtoupper($value[0])) {
                        if ($dir === self::WAY) {
                            $result[rtrim($currentWay, '\\')][] = preg_replace('/\.php$/', '', $value);
                        } else
                            $result[] = preg_replace('/\.php$/', '', $value);
                    }
                }
            }
        }

        return $result;
    }

    private function getAllReflectionClasses(): Response
    {
        $_ = [];
        foreach ($this->userClasses as $way => $names) {
            foreach ($names as $name) {
                $fullClassName = $way . '\\' . $name;
                try {
                    $reflectionClass = new ReflectionClass($fullClassName);
                    $classAttributes = $reflectionClass->getAttributes();
                    foreach ($classAttributes as $classAttribute) {
                        $_[preg_replace('/^.+\\\\/m', '', $classAttribute->getName())][] =
                            [$reflectionClass, $classAttribute->getName()];
                    }
                } catch (\Exception) {
                    return Response::take(false, 'Ошибка в конфигурационных классах.');
                }
            }
        }
        return Response::take(true, $_);
    }

    public function getResponse()
    {
        $step_1 = Verify::getInstance()->verify();
        if ($this->getAllReflectionClasses()->status) {
            [self::SECURITY => [[&$security, $securityClass]], self::CONTROLLER => &$controllers] = $this->getAllReflectionClasses()->body;
            if (!empty($security)) {
                $__ = [];
                $securityReflectionClass = $security;
                $securityReflectionMethods = $securityReflectionClass->getMethods();
                foreach ($securityReflectionMethods as $securityReflectionMethod) {
                    $securityReflectionMethodAttributes = $securityReflectionMethod->getAttributes();
                    foreach ($securityReflectionMethodAttributes as $securityReflectionMethodAttribute) {
                        $__[preg_replace('/^.+\\\\/m', '', $securityReflectionMethodAttribute->getName())] =
                            $securityReflectionMethodAttribute;
                    }
                }
                [self::LOGIN => &$login, self::REGISTER => &$register] = $__;
                if (!empty($login) && !empty($register)) {

                }
            } else {
                return Response::take(false, 'Вы не авторизованы.');
            }
        }
        if (!empty($controllers)) {
            foreach ($controllers as $controllerTuple) {
                [$userController, $_] = $controllerTuple;
                $methodsController = $userController->getMethods();
                foreach ($methodsController as $methodController) {
                    [$methodControllerAttribute] = $methodController->getAttributes();
                    $targetMethod =
                        preg_replace('/^.+\\\\/m', '', $methodControllerAttribute->getName());
                    if ($targetMethod === $this->method) {
                        return ($methodControllerAttribute->newInstance())->getAccess(
                            $methodController,
                            $userController,
                        );
                    }
                }
            }
            return Response::take(false, 'Запрашиваемый ресурс отсутствует');
        }
    }

}

