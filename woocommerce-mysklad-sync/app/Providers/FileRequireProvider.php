<?php


namespace WCSTORES\WC\MS\Providers;


/**
 * Class FileRequireProvider
 * @package WCSTORES\WC\MS\Providers
 */
class FileRequireProvider extends Providers
{

    /**
     * @var
     */
    protected $dir;

    /**
     *
     */
    public function boot()
    {
        $dir = str_replace('//', '/', $this->dir);

        $files = scandir( $dir);
        if($files){
            foreach ($files as $file){
                if(strpos($file,'php') !== false){
                    require_once $dir . $file;
                }
            }
        }


    }

}