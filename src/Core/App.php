<?php

namespace Src\Core;

use Src\Http\Controllers\File\File;

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

      $obFile = new File('livro.epub');

      $obFile->changeExtension()->extractFile()->loadFiles()->loadHtmlFile();

    } catch (\Throwable $e) {
      echo "<pre>"; print_r($e); echo "</pre>"; exit;
    }
  }
}