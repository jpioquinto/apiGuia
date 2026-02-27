<?php

namespace App\Http\Clases\Upload;

use App\Http\Clases\Validations\ValidaUploadAnexo;
use App\Models\Project\Proyecto AS Modelo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use App\Http\Helpers\CadenaHelper;
use App\Http\Clases\Proyecto;
use Illuminate\Http\Request;

class UploadAnexo extends ValidaUploadAnexo
{
    protected $directory;
    protected $project;
    protected $request;
    protected $error;
    protected $path;
    protected $env;

    public function __construct(Request $request)
    {
        $this->env = Config::get('filesystems.default');

        parent::__construct($request->all());
        $this->setRequest($request);

        if ($request->has('proyectoId') && is_numeric($request->get('proyectoId'))) {
            $this->project = new Proyecto(Modelo::where('id_proyecto', $request->get('proyectoId'))->first());
        }

        $this->setDirectory(
            sprintf("documentos/%s/%d/anexos", $this->folderInstitution(), $this->project ? $this->project->getAnio() : date('Y'))
        );
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
        if ($this->existsError()) {
            return ['solicitud'=>false, 'message'=>$this->getFirstError()];
        }

        if (!$this->existsDirectory()) {
            return $this->getError();
        }

        if (!$this->move()) {
            return ['solicitud'=>false, 'message'=>'Error al intentar cargar el archivo.'];
        }

        return [
            'solicitud'=>true,
            'path'=>$this->getPath(),
            'message'=>'Carga realizada correctamente.',
        ];
    }

    protected function move()
    {

        $nombre = CadenaHelper::removeExtension($this->request->get('nombre'));

        $load = $this->request->file('archivo')->storePubliclyAs(
            $this->getDirectory(),
            $this->fileName(CadenaHelper::clearFileName($nombre)) . "." .trim($this->request->file('archivo')->extension()),
            $this->env
        );

        if ($load!==FALSE) {
            $this->setPath($load);
        }

        return $load !== FALSE ? true : false;
    }

    protected function fileName(string $name, $numFile = 2)
    {
        if (Storage::disk($this->env)->exists($this->getDirectory() . "/{$name}.{$this->request->file('archivo')->extension()}")) {
            return $this->fileName( trim( preg_replace("/(\d+)$/", "", $name) ) . ' ' . $numFile, ++$numFile);
        }

        return $name;
    }

    protected function existsDirectory()
    {
        if (empty($this->folderInstitution())) {
            $this->setError(['solicitud'=>false, 'message'=>'No se encontró el directorio principal de la Institución.']);
            return false;
        }

        return true;
    }

    protected function folderInstitution()
    {
        return $this->project ? $this->project->getCarpeta() : auth()->user()->getCarpeta();
    }
}
