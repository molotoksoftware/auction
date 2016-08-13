<?php
$codeTemplateFile=dirname(__FILE__).DIRECTORY_SEPARATOR.$this->baseClass.'.php';
if (file_exists($codeTemplateFile))
    include($codeTemplateFile);
else {
    echo "There are no code template file for your new class/component!\n\n";
    echo "Sorry, I don't know your base class: {$this->baseClass}.\n";
    echo "Create your own code template file: $codeTemplateFile";
}
?>