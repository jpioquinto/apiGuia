<?php

namespace App\Http\Clases\Upload;

use App\Http\Clases\Validations\ValidaUploadFiel;
use App\Models\Project\Proyecto AS Modelo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use App\Http\Helpers\CadenaHelper;
use App\Http\Clases\Proyecto;
use Illuminate\Http\Request;

class UploadFiel extends ValidaUploadFiel
{
    protected $directory;
    protected $request;
    protected $error;
    protected $path;
    protected $env;

    protected $nameCer;
    protected $nameKey;

    public function __construct(Request $request)
    {
        $this->env = Config::get('filesystems.default');

        parent::__construct($request->all());

        $this->setRequest($request);

        $this->setDirectory(
            sprintf("temp/signature/%s/", ($this->request->file('archivoCer'))->getClientOriginalName())
        );
    }

    public function setNameCer(string $nameCer)
    {
        $this->nameCer = $nameCer;
    }

    public function getNameCer()
    {
        return $this->nameCer;
    }

    public function setNameKey(string $nameKey)
    {
        $this->nameKey = $nameKey;
    }

    public function getNameKey()
    {
        return $this->nameKey;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function setDirectory(string $directory)
    {
        $this->directory = $directory;
    }

    public function getDirectory()
    {
        return $this->directory;
    }

    public function setError(array $error)
    {
        $this->error = $error;
    }

    public function getError()
    {
        return $this->error;
    }

    public function setPath(string $path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function upload()
    {
        if ($this->existsError()) {#$this->request->file('archivoKey')->getClientOriginalExtension()
            return ['solicitud'=>false, 'message'=>$this->getFirstError()];
        }

        if (!$this->move($this->request->file('archivoCer'))) {
            return ['solicitud'=>false, 'message'=>'Error al intentar cargar el certificado de la FIEL.'];
        }

        $this->setNameCer($this->getPath());

        if (!$this->move($this->request->file('archivoKey'))) {
            return ['solicitud'=>false, 'message'=>'Error al intentar cargar la llave privada de la FIEL.'];
        }

        $this->setNameKey($this->getPath());

        return [
            'solicitud'=>true,
            'message'=>'Carga de los archivos de la FIEL realizada correctamente.',
        ];
    }

    protected function move(File $archivo)
    {

        $nombre = CadenaHelper::clearFileName($archivo->getClientOriginalName());

        if (Storage::disk($this->env)->exists($this->getDirectory() . "{$nombre}.{$archivo->getClientOriginalExtension()}")) {
            Storage::disk($this->env)->delete($this->getDirectory() . "{$nombre}.{$archivo->getClientOriginalExtension()}");
        }

        $load = $archivo->storePubliclyAs(
            $this->getDirectory(),
            $nombre. "." .$archivo->getClientOriginalExtension(),
            $this->env
        );

        if ($load!==FALSE) {
            $this->setPath($load);
        }

        return $load !== FALSE ? true : false;
    }
}
