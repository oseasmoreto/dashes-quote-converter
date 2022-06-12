<?php

namespace Src\Http\Controllers\File;

/**
 * @class File
 * @author Oseas Moreto <dev@oseasmoreto.com>
*/

class File{
  
  /**
   * Atributo que receberá o nome do arquivo
   * @var string
  */
  private string $filename;

  /**
   * Atributo que armazenará o caminho da pasta de arquivos
   * @var string
   */
  private string $path;

  /**
   * Atributo que armazenará o caminho temporário da pasta de arquivos
   * @var string
   */
  private string $tmpPath;

  /**
   * Método construtor da classe
   * @param string $filename
   */
  public function __construct(string $filename){
    $this->filename = $filename;

    $this->path     = $_SERVER['DOCUMENT_ROOT'].'/resources/files';
    $this->tmpPath  = $_SERVER['DOCUMENT_ROOT'].'/storage/files/'.base64_encode($filename);

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
    $tmpFile = $this->tmpPath.'/'.(str_replace('.epub','.zip',$this->filename));

    if(!copy($file,$tmpFile)) throw new \Throwable("Não foi possível copiar o arquivo",500);

    return $this;
  }
}