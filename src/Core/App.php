<?php 

namespace Src\Core;

use Src\Http\Controllers\File\PDF2Text as FilePDF2Text;

/**
* Classe responsável por iniciar a aplicação
* @author Oseas Moreto
*/

class App {
  
  /**
  * Método responsável por iniciar aplicação
  * @method init
  * @return void
  */
  public static function init(){
    try {

      echo "<pre>"; print_r('teste'); echo "</pre>"; exit;

    } catch (\Exception $e) {
      echo "<pre>"; print_r($e); echo "</pre>"; exit;
    }
  }
}