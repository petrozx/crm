<?php

namespace core\classes;


use core\controller\Controller;
use core\controller\PointRouter;
use core\helpers\Response;
use core\helpers\ScanUserDirectory;
use core\security\Security;
use core\security\Verify;
use core\traits\Singleton;

class Middleware extends Controller
{
    use Singleton;

    private array $userClasses;
    private const CONTROLLER = 'PointRouter';
    private const SECURITY = 'Security';

    private function __construct()
    {
        parent::__construct();
    }

    public function getResponse(): Response
    {
        $step_1 = Verify::getInstance()->verify();
        $reflectionClasses = ScanUserDirectory::getInstance()->getAllReflectionClasses();
        if ($reflectionClasses->status) {
            [
                self::SECURITY => [[&$securityUserConfig, $_]],
                self::CONTROLLER => &$controllersUserClasses,
            ] = $reflectionClasses->body;
            if (!$step_1->status) {
                return (new Security)->checkSecurity($securityUserConfig);
            } else {
                return (new PointRouter)->checkPoint($controllersUserClasses);
            }
        } else {
            return $reflectionClasses;
        }
    }

}

