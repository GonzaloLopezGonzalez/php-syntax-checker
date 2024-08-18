<?php

class phpSyntaxChecker
{
    private $filesWithSyntaxErrors;
    private $syntaxCheckResult;

    public function __construct()
    {
       if (!$this->checkIfSyntaxCheckerCommandCanBeExecuted()){
         throw new ErrorException('Es necesario poder ejecutar el comando "system" de php');
       }

    }

    private function checkIfSyntaxCheckerCommandCanBeExecuted(): bool
    {
        $disabledFunctions = explode(',', ini_get('disable_functions'));
       return (in_array('exec', $disabledFunctions)) ? false : true;
    }

    public function checkPhpFileSyntax(string $file)
    {
        $result = array();
        $comando = 'php -l ' . $file;
        exec($comando, $result);
        if (false === strpos($result[0], 'No syntax errors detected in')){
            $this->filesWithSyntaxErrors[] = $result[1];
        }
    }


    public function analyzePhpFilesSyntax(string $ruta = __DIR__, bool $checkRoot = true)
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator("C:/xampp/htdocs/pruebas"));
        foreach ($iterator as $archivo) {
            if ($archivo->isDir()) {
                continue; // Saltar directorios
            }
            if ('php' === $archivo->getExtension()){
               $this->checkPhpFileSyntax($archivo->getPathname());
            }
        }
        $this->getResult();
    }

    private function getResult(): void {
        foreach ($this->filesWithSyntaxErrors as $message){
            echo $message."\n";
        }
    }
}