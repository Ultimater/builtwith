<?php

namespace _htrouter_php;

class htRouter
{
    static public $uri = '', $ext = '', $fullFilePath = '';
    static public $mimeTypes = [
        'css'=>'text/css',
        'js'=>'application/javascript',
        'png'=>'image/png',
        'gif'=>'image/gif',
        'jpg'=>'image/jpeg',
        'txt'=>'text/plain',
        'ico'=>'image/x-icon'
    ];
    static public function parseExt($fullFilePathAndName)
    {
        $path_parts = pathinfo($fullFilePathAndName);
        self::$ext = $path_parts['extension'] ?? '';
    }
    static public function sendMime()
    {
        if(array_key_exists(self::$ext,self::$mimeTypes))
        {
            header('Content-Type: '.self::$mimeTypes[self::$ext]);
            
        }
    }
    static public function serveIfExists()
    {
        if (file_exists(htRouter::$fullFilePath))
        {
            htRouter::parseExt(htRouter::$fullFilePath);
            htRouter::sendMime();
            if(htRouter::$ext == 'php')
            {
                chdir(dirname(htRouter::$fullFilePath));
                require(htRouter::$fullFilePath);
                exit;
            }
            if(is_file(htRouter::$fullFilePath))
            {
                echo file_get_contents(htRouter::$fullFilePath);
                exit;
            }
            if(is_dir(htRouter::$fullFilePath))
            {
                if(file_exists(htRouter::$fullFilePath.'/index.php'))
                {
                    chdir(dirname(htRouter::$fullFilePath.'/index.php'));
                    require(htRouter::$fullFilePath.'/index.php');
                    exit;
                }
            }
        }
    }
}

htRouter::$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
if(!htRouter::$uri)htRouter::$uri = '/';

if (htRouter::$uri !== '/')
{
    htRouter::$fullFilePath = realpath(__DIR__ . htRouter::$uri);
    htRouter::serveIfExists();
    htRouter::$fullFilePath = realpath(__DIR__ . '/public'.htRouter::$uri);
    htRouter::serveIfExists();
}


$_GET['_url'] = htRouter::$uri;
chdir(__DIR__.'/public');

require_once __DIR__ . '/public/index.php';