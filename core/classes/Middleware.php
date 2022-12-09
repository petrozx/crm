<?php

namespace core\classes;


use core\helpers\Response;
use core\traits\Singleton;
use ReflectionClass;

class Middleware
{
    use Singleton;

    private string $uri;
    private array $request;
    private array $headers;
    private array $userClasses;
    private string $requestHttpMethod;

    private const WAY = '../../src/web_back/';
    private const CONTROLLER = 'Controller';
    private const SECURITY = 'Security';
    private const URI = 'uri';
    private const ENTITY_CLASS = 'entity';
    private const ACCESS = 'access';
    private const GET = 'GET';
    private const POST = 'POST';
    private const LOGIN = 'login';
    private const REGISTER = 'register';

    private function __construct()
    {
        $this->request = $this->getRequest();
        $this->headers = $this->getHeaders();
        $this->requestHttpMethod = $_SERVER['REQUEST_METHOD'];
        $this->userClasses = $this->scanClasses(self::WAY);
        $this->uri = preg_replace('/\/$/', '', $_SERVER['REDIRECT_URL']);
    }

    private function getHeaders(): bool|array
    {
        try {
            return getallheaders();
        } catch (\Exception) {
            return [];
        }
    }

    private function getRequest()
    {
        $jDada = file_get_contents('php://input');
        $pData = $_POST;
        return json_decode($jDada, true)?:$pData;
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

    public function getResponse()
    {
        $_ = [];
        foreach ($this->userClasses as $way => $names) {
            foreach ($names as $name) {
                $fullClassName = $way . '\\' . $name;
                try {
                    $reflectionClass = new ReflectionClass($fullClassName);
                    $classAttributes = $reflectionClass->getAttributes();
                    foreach ($classAttributes as $classAttribute) {
                        $_[preg_replace('/^.+\\\\/m','', $classAttribute->getName())][] =
                            [$reflectionClass, $classAttribute->getName()];
                    }
                } catch (\Exception) {
                    return Response::take(false, 'Ошибка в конфигурационных классах.');
                }
            }
        }
        if (isset($_)) {
            [self::SECURITY => [[&$security, $securityClass]], self::CONTROLLER => &$controller] = $_;
            if (isset($security)) {
                $__ = [];
                $securityReflectionClass = $security;
                $securityReflectionMethods = $securityReflectionClass->getMethods();
                foreach ($securityReflectionMethods as $securityReflectionMethod) {
                    $securityReflectionMethodAttributes = $securityReflectionMethod->getAttributes();
                    foreach ($securityReflectionMethodAttributes as $securityReflectionMethodAttribute) {
                        $__[preg_replace('/^.+\\\\/m','', $securityReflectionMethodAttribute->getName())] =
                            $securityReflectionMethodAttribute;
                    }
                }
                [self::LOGIN => &$login, self::REGISTER => &$register] = $__;
                if (isset($login) && isset($register)) {
                    [
                        self::URI => $uriLogin,
                        self::ENTITY_CLASS => $entityClassLogin,
                        self::ACCESS => $accessLogin
                    ] = $login->getArguments();
                    if ($this->uri === $uriLogin) {
                        $method = self::LOGIN;
                        return $securityClass::$method(
                            $this->request,
                            $entityClassLogin,
                            $accessLogin);
                    }
                    [
                        self::URI => $uriRegister,
                        self::ENTITY_CLASS => $entityClassRegister,
                        self::ACCESS => $accessRegister
                    ] = $register->getArguments();
                    if ($this->uri === $uriRegister) {
                        $method = self::REGISTER;
                        return $securityClass::$method(
                            $this->request,
                            $entityClassRegister,
                            $accessRegister,
                            $this->headers
                        );
                    } else {
                        $decodeJWT = $securityClass::verify($this->headers);
                         if ($decodeJWT->status && isset($controller)) {
                             return (new Controller)->getDataEndpoint(
                                 $controller,
                                 $decodeJWT->body,
                                 $this->request,
                                 $this->requestHttpMethod,
                                 $this->uri,
                             );
                         } else {
                             return Response::take(false, 'Вы не авторизованы.');
                         }
                    }
                }
            }
        }
    }
}

