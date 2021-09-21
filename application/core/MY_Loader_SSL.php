<?php // -*- coding: UTF-8 -*-

// CA identifier
define('SSL_AUTHORITY_KEY_IDENTIFIER', 'keyid:AA:BB:CC:DD:EE:FF
DirName:/C=RU/ST=Rostov Region/L=Rostov-on-Don/CN=Certname ROOT CA/emailAddress=email@domain.tld
serial:AA:BB:CC:DD:EE:FF');

// CA attributes
define('SSL_CA_FILE', APPPATH . '/certs/ca.crt');
define('SSL_CA_MD5_FINGERPRINT', 'AA:BB:CC:DD:EE:FF');
define('SSL_CA_SERIAL', 'AABBCCDDEEFF');
define('SSL_CA_SERIAL_DEC', '1234567890');
define('SSL_CA_SUBJECT_KEY_IDENTIFIER', 'AA:BB:CC:DD:EE:FF');

// main certificate attributes
define('SSL_CERT_FILE', APPPATH . '/certs/file.crt');
define('SSL_CERT_MD5_FINGERPRINT', 'AA:BB:CC:DD:EE:FF');
define('SSL_CERT_SERIAL', '02');
define('SSL_CERT_SERIAL_DEC', '2');
define('SSL_CERT_SUBJECT_KEY_IDENTIFIER', 'AA:BB:CC:DD:EE:FF');

class MY_Loader_SSL extends CI_Loader
{
    // All these are set automatically. Don't mess with them.
    var $_my_licence = null;

    function MY_Loader_SSL()
    {
        parent::CI_Loader();

        $this->_my_licence = $this->_load_licence_file(APPPATH . '/signs/licence.xml');
    }
    // --------------------------------------------------------------------

    /**
     * @fn    mixed _load_x509_cert(string $filename)
     * @brief A function which loads X509 formated certificate.
     *
     * This function loads X509 formated certificate with its attributes
     * into associative array. The attributes are:
     *    MD5_FINGERPRINT  - the fingerprint of X509 certificate (by MD5 hash algorithm)
     *    SERIAL           - the serial number of X509 certificate in hex representation
     *    SERIAL_DEC       - the serial number of X509 certificate in decimal representation
     *    SUBJECT_KEY_IDENTIFIER - the extended key identifier
     *    CERT             - the X509 object (must be free with openssl_x509_free(...) when unneeded
     *
     * @param $filename a string which contains name of the X509 certificate file.
     *
     * @return array|false an associative array which contains X509 cerificate object and its attributes or FALSE on failures.
     */
    function _load_x509_cert($filename)
    {
        if (file_exists($filename)) {
            $x509_cert = array();

            // Some X509 attributes require for us.
            $cmd = 'openssl x509 -serial -md5 -fingerprint -noout -in ' . escapeshellcmd($filename) . ' 2> /dev/null';
            $handle = popen($cmd, 'r');
            if ($handle) {
                while (!feof($handle)) {
                    $read = fgets($handle, 4096);
                    if (trim($read) == "") {
                        continue;
                    }

                    $tmp = explode('=', $read);
                    $x509_cert[strtoupper(preg_replace('/\s/', '_', trim($tmp[0])))] = trim($tmp[1]);
                }
                pclose($handle);
            }

            // Read some extra X509 attributes via x509 functions.
            $ssl_CA_cert = file_get_contents($filename);

            $ssl_X509_cert = openssl_x509_read($ssl_CA_cert);
            if ($ssl_X509_cert !== false) {
                $ssl_X509_cert_info = openssl_x509_parse($ssl_X509_cert, false);

                if (array_key_exists("serialNumber", $ssl_X509_cert_info)) {
                    $x509_cert['SERIAL_DEC'] = trim($ssl_X509_cert_info["serialNumber"]);
                }

                if (array_key_exists("extensions", $ssl_X509_cert_info)) {
                    if (array_key_exists("subjectKeyIdentifier", $ssl_X509_cert_info["extensions"])) {
                        $x509_cert["SUBJECT_KEY_IDENTIFIER"] = trim($ssl_X509_cert_info["extensions"]["subjectKeyIdentifier"]);
                    }

                    if (array_key_exists("authorityKeyIdentifier", $ssl_X509_cert_info["extensions"])) {
                        $x509_cert["AUTHORITY_KEY_IDENTIFIER"] = trim($ssl_X509_cert_info["extensions"]["authorityKeyIdentifier"]);
                    }
                }
                $x509_cert['CERT'] = $ssl_X509_cert;
                //openssl_x509_free($ssl_X509_cert);

                ksort($x509_cert);

                return $x509_cert;
            }
        }

        return false;
    }
    // --------------------------------------------------------------------

    /**
     * @fn    resource _load_CA(void)
     * @brief A function which loads SmartPPC6 CA certificate.
     *
     * This function loads SmartPPC6 CA certificate from file and checks its attributes
     * (such as SERIAL, MD5_FINGERPRINT) for validity of SmartPPC6 system.
     *
     * The SmartPPC6 constants for validity checking are defined in helpers/_sppc6_private.php
     * file.
     *
     * @return false|mixed a x509 resource object or FALSE on failures
     */
    function _load_CA()
    {
        if (!defined('SSL_CA_SERIAL') || !defined('SSL_CA_MD5_FINGERPRINT')
            || !defined('SSL_CA_SUBJECT_KEY_IDENTIFIER')) {
            return false;
        }

        $x509_cert = $this->_load_x509_cert(SSL_CA_FILE);

        if ($x509_cert !== false) {
            $is_valid = true;

            if ($is_valid === true && $x509_cert['SERIAL'] != SSL_CA_SERIAL) {
                $is_valid = false;
            }
            if ($is_valid === true && $x509_cert['MD5_FINGERPRINT'] != SSL_CA_MD5_FINGERPRINT) {
                $is_valid = false;
            }
            if ($is_valid === true && $x509_cert['SUBJECT_KEY_IDENTIFIER'] != SSL_CA_SUBJECT_KEY_IDENTIFIER) {
                $is_valid = false;
            }
            if ($is_valid === true) {
                return $x509_cert['CERT'];
            }

            openssl_x509_free($x509_cert['CERT']);
            unset($x509_cert['CERT']);
        }

        return false;
    }
    // --------------------------------------------------------------------

    /**
     * @fn    resource _load_cert(void)
     * @brief A function which loads SmartPPC6 main certificate.
     *
     * This function loads SmartPPC6 main certificate from file and checks its attributes
     * (such as SERIAL, MD5_FINGERPRINT) for validity of SmartPPC6 system.
     *
     * The SmartPPC6 constants for validity checking are defined in helpers/_sppc6_private.php
     * file.
     *
     * @return false|mixed a x509 resource object or FALSE on failures
     */
    function _load_cert()
    {
        if (!defined('SSL_CERT_SERIAL') || !defined('SSL_CERT_MD5_FINGERPRINT')
            || !defined('SSL_CERT_SUBJECT_KEY_IDENTIFIER')) {
            return false;
        }

        $x509_cert = $this->_load_x509_cert(SSL_CERT_FILE);

        if ($x509_cert !== false) {
            $is_valid = true;

            if ($is_valid === true && $x509_cert['SERIAL'] != SSL_CERT_SERIAL) {
                $is_valid = false;
            }
            if ($is_valid === true && $x509_cert['MD5_FINGERPRINT'] != SSL_CERT_MD5_FINGERPRINT) {
                $is_valid = false;
            }
            if ($is_valid === true && $x509_cert['SUBJECT_KEY_IDENTIFIER'] != SSL_CERT_SUBJECT_KEY_IDENTIFIER) {
                $is_valid = false;
            }
            if ($is_valid === true) {
                return $x509_cert['CERT'];
            }

            openssl_x509_free($x509_cert['CERT']);
            unset($x509_cert['CERT']);
        }

        return false;
    }
    // --------------------------------------------------------------------

    /**
     * @fn    string _load_signed_file(string $filename)
     * @brief A function which loads the data from SMIME siggned file.
     *
     * This function loads the data from SMIME (PKCS7) signed file, verify file sign for validity of
     * SmartPPC6 system.
     *
     * @param $filename a string which contains name of the SMIME signed file.
     *
     * @return false|string a string which contains contents of SMIME signed file if sign is valid and sign was made with valid SmartPPC6 cerificate or FALSE on failures.
     */
    function _load_signed_file($filename)
    {
        if (!file_exists($filename)) {
            return false;
        }

        // check CA certificate for validity of smartppc6
        $x509 = $this->_load_CA();
        if ($x509 === false) {
            return false;
        }
        openssl_x509_free($x509);

        // check signer certificate for validity of smartppc6
        $x509 = $this->_load_cert();
        if ($x509 === false) {
            return false;
        }
        openssl_x509_free($x509);

        $signer_cert_out = tempnam('/tmp', 'smartppc6');
        $content_out = tempnam('/tmp', 'smartppc6');

        $result_content = false;
        $phpversion = phpversion();

        if (version_compare($phpversion, "5.1.0", "<")) {
            $cmd = sprintf('openssl smime -signer %s -verify -in %s -nointern -nochain -CAfile %s -certfile %s -out %s 2> /dev/null',
                escapeshellcmd($signer_cert_out), escapeshellcmd($filename),
                escapeshellcmd(SSL_CA_FILE), escapeshellcmd(SSL_CERT_FILE),
                escapeshellcmd($content_out));

            system($cmd, $retval);
            if ($retval == 0) {
                $result = true;
            }
        } else {
            $result = openssl_pkcs7_verify(
                $filename, PKCS7_NOVERIFY, $signer_cert_out,
                array(SSL_CA_FILE), SSL_CERT_FILE,
                $content_out);
        }

        if ($result === true) {
            $result_content = file_get_contents($content_out);
        }

        if (file_exists($signer_cert_out)) {
            unlink($signer_cert_out);
        }
        if (file_exists($content_out)) {
            unlink($content_out);
        }

        return $result_content;
    }
    // --------------------------------------------------------------------

    /**
     * @fn    DOMDocument _load_licence_file(string $licence_xml)
     * @brief A function which loads the data from SMIME siggned licence file.
     *
     * This function loads the data from SMIME (PKCS7) signed licence file, verify file sign for validity of
     * SmartPPC6 system.
     *
     * @param $licence_xml a string which contains name of the SMIME signed licence file.
     *
     * @return DOMDocument|false a dom object which contains LICENCE data or FALSE on failures.
     */
    function _load_licence_file($licence_xml)
    {
        if (!file_exists($licence_xml)) {
            return false;
        }

        $licence = $this->_load_signed_file($licence_xml); // file_get_contents
        if ($licence == "") {
            return false;
        }

        $dom = new DOMDocument('1.0', 'UTF-8');

        if ($dom->loadXML($licence) !== false && $dom->documentElement->tagName === "licence") {
            // Look for licence expiration time
            $valid = null;
            foreach ($dom->documentElement->childNodes as $node) {
                if ($node->nodeType !== XML_ELEMENT_NODE) {
                    continue;
                }
                if ($node->tagName === 'valid') {
                    $valid = $node;
                    break;
                }
            }

            // If the licence expiration date was found, then check this.
            if (!is_null($valid) && is_a($valid, 'DOMElement')) {
                $date = trim($valid->getAttribute('till'));
                if (strtotime($date) < strtotime('now') /*|| TRUE*/) {
                    // The licence is expired
                    return false;
                }
            }

            return $dom;
        }

        return false;
    }
}
