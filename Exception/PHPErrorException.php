<?php

namespace SIGA\Exception;

/**
 * Description of PHPErrorException
 *
 * @author Claudio Campos
 */
class PHPErrorException extends \Exception {

    private $context = null;

    public function __construct
    ($code, $message, $file, $line, $context = null) {
        parent::__construct($message, $code);
        $this->file = $file;
        $this->line = $line;
        $this->context = $context;
        //$this->errorHandler($code, $message, $file, $line, $context);
        $this->userErrorHandler($code, $message, $file, $line, $context);
    }

    private function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
        \SIGA\Utils::dump('Into ' . __FUNCTION__ . '() at line ' . __LINE__ .
                "\n\n---ERRNO---\n" . print_r($errno, true) .
                "\n\n---ERRSTR---\n" . print_r($errstr, true) .
                "\n\n---ERRFILE---\n" . print_r($errfile, true) .
                "\n\n---ERRLINE---\n" . print_r($errline, true) .
                "\n\n---ERRCONTEXT---\n" . print_r($errcontext, true) .
                "\n\nBacktrace of errorHandler()\n" .
                print_r(debug_backtrace(), true));
    }

// user defined error handling function
    private function userErrorHandler($errno, $errmsg, $filename, $linenum, $vars) {
        // timestamp for the error entry
        $dt = date("Y-m-d H:i:s (T)");

        // define an assoc array of error string
        // in reality the only entries we should
        // consider are E_WARNING, E_NOTICE, E_USER_ERROR,
        // E_USER_WARNING and E_USER_NOTICE
        $errortype = array(
            E_ERROR => 'Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parsing Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Runtime Notice',
            E_RECOVERABLE_ERROR => 'Catchable Fatal Error'
        );
        // set of errors for which a var trace will be saved
        $user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);

        $err = "<errorentry>\n";
        $err .= "\t<datetime>" . $dt . "</datetime>\n";
        $err .= "\t<errornum>" . $errno . "</errornum>\n";
        $err .= "\t<errortype>" . $errortype[$errno] . "</errortype>\n";
        $err .= "\t<errormsg>" . $errmsg . "</errormsg>\n";
        $err .= "\t<scriptname>" . $filename . "</scriptname>\n";
        $err .= "\t<scriptlinenum>" . $linenum . "</scriptlinenum>\n";

        if (in_array($errno, $user_errors)) {
            $err .= "\t<vartrace>" . wddx_serialize_value($vars, "Variables") . "</vartrace>\n";
        }
        $err .= "</errorentry>\n\n";

        // for testing
        // echo $err;
        // save to the error log, and e-mail me if there is a critical user error
        error_log($err, 3, "error.log");
        if ($errno == E_USER_ERROR) {
            mail("callcocam@gmail.com", "Critical User Error", $err);
        }
    }

}
