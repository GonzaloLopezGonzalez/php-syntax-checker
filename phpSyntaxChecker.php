<?php

class phpSyntaxChecker
{
    private $filesWithSyntaxErrors;
    private $syntaxCheckResult;

    public function __construct()
    {
        $this->filesWithSyntaxErrors = array();
       if (!$this->checkIfSyntaxCheckerCommandCanBeExecuted()){
         throw new ErrorException('Es necesario poder ejecutar el comando "exec" de php');
       }
    }

    private function checkIfSyntaxCheckerCommandCanBeExecuted(): bool
    {
        $disabledFunctions = explode(',', ini_get('disable_functions'));
       return (in_array('exec', $disabledFunctions)) ? false : true;
    }

    public function checkPhpFileSyntax(string $fileName)
    {
        $result = array();
        $return = 0;
        exec("php -ln {$fileName}", $result, $return);
        if (255 === $return){
            $this->filesWithSyntaxErrors[] = $result[1];
        }
    }

    public function analyzePhpFilesSyntax(string $ruta = __DIR__, bool $checkRoot = true)
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($ruta));
        foreach ($iterator as $archivo) {
            $posPunto = strpos($archivo, '\.');
            if ($archivo->isDir() || $posPunto !== false) {
                continue; // Saltar directorios
            }
            if ('php' === $archivo->getExtension()){
               $this->checkPhpFileSyntax($archivo->getPathname());
            }
        }
        unset($iterator);
        $this->getResult();
    }

    private function getResult(): void {
        foreach ($this->filesWithSyntaxErrors as $message){
           echo $message."\n";
        }
    }
}
