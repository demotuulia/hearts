<?php
/**
 * Autoloader
 *
 *
 *
 */
namespace Lib\Core;

class Autoloader
{

    /**
     * loader
     *
     * @param $className
     * @return bool
     */
    public static function loader($className)
    {

        $filename = str_replace('\\', '/', $className) . ".php";

        $filename = __DIR__ . '/../../' . $filename;


        if (file_exists($filename)) {
            include($filename);

            if (class_exists($className)) {

                return true;
            }
        }
        return false;
    }
}

spl_autoload_register('Lib\Core\Autoloader::loader');
