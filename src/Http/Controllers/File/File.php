<?php

namespace Src\Http\Controllers\File;

use DOMDocument;
use ZipArchive;
use Throwable;

/**
 * @class File
 * @author Oseas Moreto <dev@oseasmoreto.com>
*/

class File{

  /**
   * Atributo que receberá o nome do arquivo
   * @var string
  */
  private string $filename = '';

  /**
   * Atributo que armazenará o caminho da pasta de arquivos
   * @var string
   */
  private string $path = '';

  /**
   * Atributo que armazenará o caminho temporário da pasta de arquivos
   * @var string
   */
  private string $tmpPath = '';

  /**
   * Atributo que armazenará o nome da pasta temporária
   * @var string
  */
  private string $tmpFolder = '';

  /**
   * Atributo que armazenará os arquivos que serão lidos
   *
   * @var array
  */
  private array $listFiles = [];

  /**
   * Atributo que armazenará os arquivos que serão lidos com seus conteúdos
   *
   * @var array
  */
  private array $listFilesContent = [];

  /**
   * Método construtor da classe
   * @param string $filename
  */
  public function __construct(string $filename){
    $this->filename  = $filename;
    $this->tmpFolder = base64_encode($filename);

    $this->path     = $_SERVER['DOCUMENT_ROOT'].'/resources/files';
    $this->tmpPath  = $_SERVER['DOCUMENT_ROOT'].'/storage/files/'.$this->tmpFolder;

    if(!is_dir($this->tmpPath)){
      mkdir($this->tmpPath, 0777, true);
    }
  }

  /**
   * Método responsável por trocar a extensão do arquivo para .zip
   * @return File
  */
  public function changeExtension() {
    $file    = $this->path.'/'.$this->filename;

    $newName = str_replace('.epub','.zip',$this->filename);
    $tmpFile = $this->tmpPath.'/'.$newName;

    if(!copy($file,$tmpFile)) throw new Throwable("Não foi possível copiar o arquivo",500);

    $this->filename = $newName;

    return $this;
  }

  /**
   * Método responsável por extrair os arquivos
   * @return File
  */
  public function extractFile(){
    $obZipAchive = new ZipArchive;

    $file = $this->tmpPath.'/'.$this->filename;

    if(!$obZipAchive->open($file)) throw new Throwable("Não foi possível copiar o arquivo",500);

    $obZipAchive->extractTo($this->tmpPath);
    $obZipAchive->close();

    return $this;
  }

  /**
   * Método responsável por limpar os arquivos temporários
   * @return File
   */
  public function emptyTmp(){
    $dir = $this->tmpPath;
    if(shell_exec("rm -rf $dir")) throw new Throwable("Não foi possível copiar o arquivo",500);

    return $this;
  }

  /**
   * Método responsável por listar os arquivos html no atributo listFiles
   * @return File
  */
  public function loadFiles(){
    foreach (scandir($this->tmpPath) as $filename) {
      if(in_array($filename,['.','..'])) continue;

      $fileParts = pathinfo($filename);

      if(!isset($fileParts['extension']) or $fileParts['extension'] != 'html') continue;

      $path = $this->tmpPath . '/' . $filename;

      if(is_file($path)) $this->listFiles[] = $path;
    }

    return $this;
  }

  /**
   * Método responsável por ler os arquivos html
   * @return File
  */
  public function loadHtmlFile(){
    foreach ($this->listFiles as $key => $file) {

      $fFile = fopen($file,'r');

      if(!$fFile) throw new Throwable("Não foi possível abrir o arquivo",500);

      $fileContent = fread($fFile, filesize($file));

      $fileContent = str_replace(['“','”'], ['"','"'],$fileContent);
      $fileContent = str_replace([',"','."'], [',"'."\n",'."'."\n"], $fileContent);
      $fileContent = str_replace('</p><p class="block_14">', ' ', $fileContent);

      $obDocument = new DOMDocument();

      $obDocument->loadHTML($fileContent);

      if(!strlen($obDocument->textContent)) continue;

      $this->listFilesContent[$file] = $obDocument;
    }

    return $this;
  }

  /**
   * Método responsável por ler os arquivos html
   * @return File
  */
  public function converterQuotesInDashes(){
    $newListFiles = [];

    foreach ($this->listFilesContent as $key => $obDocument) {
      $total = preg_match_all('("(.*)")',$obDocument->textContent,$matches);

      if($total == 0) continue;

      $newContent = clone $obDocument;
      $textContent = $newContent->textContent;
      echo "<pre>"; print_r($newContent); echo "</pre>"; exit;
      foreach ($matches[0] as $i => $line) {
        $textContent = str_replace($line, '-- '.$matches[1][$i],$textContent);
      }

      $newContent->textContent = $textContent;
      $newContent->textContent = '';
      echo "<pre>objeto"; print_r($newContent->textContent); echo "</pre>";
      echo "<pre>text"; print_r($textContent); echo "</pre>";

      $newListFiles[$key] = $newContent;
      echo "<pre>"; print_r($textContent); echo "</pre>";
      echo "<pre>"; print_r($newContent); echo "</pre>"; exit;
    }

    return $this;
  }
}