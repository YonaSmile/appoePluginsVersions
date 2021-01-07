<?php

namespace App\Plugin\Cms;
if (!defined('CACHE_PATH')) {
    define('CACHE_PATH', ROOT_PATH . 'static/');
}

class CmsCache
{

    public $dirname = CACHE_PATH . LANG . DIRECTORY_SEPARATOR;
    public $filename;
    public $file;
    public $duration = CACHE_DURATION; // In minutes

    private $buffer = false;

    public function __construct($filename)
    {
        if (createFolder($this->dirname, 0755, true)) {
            createFile(CACHE_PATH . 'index.php', ['content' => DEFAULT_INDEX_CONTENT]);
        }
        $this->filename = $filename;
        $this->file = $this->dirname . $this->filename;
    }

    /**
     * Read from cache file
     * @return bool|false|string
     */
    public function read()
    {

        if (file_exists($this->file)) {
            $lifetime = (time() - filemtime($this->file)) / 60;
            if ($lifetime > $this->duration) {
                return false;
            }

            return file_get_contents($this->file);
        }

        return false;
    }

    /**
     * write in cache file
     *
     * @param $content
     */
    public function write($content)
    {
        file_put_contents($this->file, $content);
    }

    /**
     * Starting write in cache file if APPOE isn't in maintenance mode
     * @return bool
     */
    public function start()
    {

        $AppConfig = new \App\AppConfig();
        if ('false' === $AppConfig->get('options', 'cacheProcess') || 'true' === $AppConfig->get('options', 'maintenance')) {
            return false;
        }

        if ($content = $this->read()) {
            echo $content;

            return true;
        }
        $this->buffer = true;
        ob_start();

        return false;
    }

    /**
     * end writing in cache file
     * @return bool
     */
    public function end()
    {
        if (!$this->buffer) {
            return false;
        }
        $content = $this->minifyHtml(ob_get_clean());
        echo $content;
        $this->write($content);

        return true;
    }

    /**
     * @param $buffer
     * @return string
     */
    public function minifyHtml($buffer)
    {
        $search = array("/\>[^\S ]+/s", "/[^\S ]+\</s", "/<!--(.|\s)*?-->/", "/\s+\n/", "/\n\s+/", "/ +/");
        $replace = array(">", "<", "", "\n", "\n ", " ");
        $buffer = preg_replace($search, $replace, $buffer);
        return $buffer;
    }


    /**
     * delete cache file
     */
    public function delete()
    {

        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }
}