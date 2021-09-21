<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class MY_Input
 */
class MY_Input extends CI_Input
{

    /**
     * Fetch an item from the FILES array
     *
     * @param mixed $index  Index for item to be fetched from $_FILES
     * @param mixed $index2 Whether to apply XSS filtering
     *
     * @return array|bool
     */
    public function files($index, $index2 = null)
    {
        $a = $this->_fetch_from_array($_FILES, $index, null);
        if ($index2 === null) {
            return $a;
        }

        return array_key_exists($index2, $a) ? is_uploaded_file($a[$index2]) : false;
    }

}
