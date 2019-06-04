<?php
/**
 * Created by PhpStorm.
 * User: bennet
 * Date: 18.04.18
 * Time: 20:56
 */

namespace Angle\Engine\Template;

use org\bovigo\vfs\vfsStream;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

/**
 * Class Engine
 * @package Angle\Engine\Template
 *
 * @author Bennet Gallein <bg@esyoil.com>
 */
class Engine {

    protected $tokens;
    private $stream;

    /**
     * render a template to the screen.
     *
     * @param $view string the filename of the file to render. TEMPLATES_FOLDER/$view
     * @param array $params an assoc array of parameters to render in the template.
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render($view, $params = []) {

        if (!empty($params)) extract($params);
        $viewArray = explode('/', $view);
        $viewPath = implode('/', $viewArray);

        vfsStream::setup($viewPath);

        $file = vfsStream::url($view . '.php');
        $this->localCompile(file_get_contents($view), $params);

        file_put_contents($file, $this->getStream());

        ob_start();
        include $file;
        ob_end_flush();
    }

    /**
     *
     * This function takes the content of the file to render and turns it into an template object for Twig to render.
     * Then it does some Twig magic to insert all the parameters correctly and return the stream of content to the main function.
     *
     * @author Bennet Gallein <bg@esyoil.com>
     *
     * @param $stream
     * @param array $params
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function localCompile($stream, $params = []) {
        $this->setStream($stream);
        
        $loader = new FilesystemLoader(TEMPLATES_FOLDER);
        $twig = new Environment($loader, [
            'cache' => CACHE_FOLDER
        ]);
        try {
            $file = $twig->createTemplate($stream);
        } catch (LoaderError $e) {
            throw new \Exception("Error Loading Template");
        } catch (SyntaxError $e) {
            throw new \Exception("Error parsing Twig Template");
        }
        $this->stream = $twig->render($file, $params);
    }



    /**
     *
     * this function does the same as #localCompile but does not print the output to the screen, only compiling
     * the parameters into the template
     *
     *
     * @author Bennet Gallein <bg@esyoil.com>
     *
     * @param $view
     * @param array $params
     * @return false|string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function compile($view, $params = []) {

        if (!empty($params)) extract($params);
        $viewArray = explode('/', $view);
        $viewPath = implode('/', $viewArray);

        vfsStream::setup($viewPath);

        $file = vfsStream::url($view . '.php');
        $this->localCompile(file_get_contents($view), $params);

        file_put_contents($file, $this->getStream());

        ob_start();
        include $file;
        $cont = ob_get_contents();
        ob_end_clean();
        return $cont;
    }

    /**
     * @return mixed
     */
    public function getStream() {
        return $this->stream;
    }

    public function setStream($stream) {
        $this->stream = $stream;
    }
}