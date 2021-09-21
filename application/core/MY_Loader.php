<?php defined('BASEPATH') or exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';

/**
 * Class MY_Loader
 */
class MY_Loader extends CI_Loader
{
    /**
     * @var Twig_Environment
     */
    protected $_ci_twig;

    public function initialize()
    {
        parent::initialize();

        $loader = new Twig_Loader_Filesystem(current(array_keys($this->_ci_view_paths)));
        $this->_ci_twig = new Twig_Environment($loader, [
            'charset'     => config_item('charset'),
            'cache'       => APPPATH . 'cache/twig',
            'auto_reload' => true,
        ]);

        $filter = new Twig_SimpleFilter('re_replace', function ($str, $regexp, $replace) {
            return preg_replace($regexp, $replace, $str);
        });
        $this->_ci_twig->addFilter($filter);
    }

    /**
     * Twig Loader
     *
     * Loads "twig" files.
     *
     * @param string $twig   Twig name
     * @param array  $vars   An associative array of data to be extracted for use in the twig
     * @param bool   $return Whether to return the view output or leave it to the Output class
     *
     * @return $this|string
     */
    public function twig($twig, array $vars = [], $return = false)
    {
        ob_start();

        try {
            echo $this->_ci_twig->render(dirname($twig) . '/' . basename($twig, '.twig') . '.twig', $vars);
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
        }

        log_message('info', 'File loaded: ' . $twig);

        if ($return === true) {
            $buffer = ob_get_contents();
            @ob_end_clean();

            return $buffer;
        }

        if (ob_get_level() > $this->_ci_ob_level + 1) {
            ob_end_flush();
        } else {
            $_ci_CI =& get_instance();
            $_ci_CI->output->append_output(ob_get_contents());
            @ob_end_clean();
        }

        return $this;
    }

}
