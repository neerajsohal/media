<?php

namespace Dotlogics\Media\App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Dotlogics\Media\App\Models\TempMedia;
use Illuminate\Database\Eloquent\Collection;

class TempFileUploadComponent extends Component
{
    use WithFileUploads;

    public Collection $files;

    public $config = [];
    public $file;
    public $name;
    public $maxFiles;
    public $total_files = null;
    public $canAddMoreFiles = true;

    public function mount(string $name, array $config=[], $maxFiles=10, $totalFiles = null)
    {
        $this->maxFiles = $maxFiles;
        $this->name = $name;
        $this->total_files = $totalFiles;
        $this->config = array_merge($this->config(), $config);
        $this->files =  TempMedia::find(
            array_merge(
                old($name,[]), $this->config['files']
            )
        );
    }

    public function render()
    {
        $this->setCanAddMore();
        return view('media::livewire.temp-file-upload-component');
    }

    public function updatedFile()
    {
        if ( !is_null($this->maxFiles) && ($this->files->count() + $this->total_files) >=  $this->maxFiles ){
            $this->addError($this->name, "Cannot add more then {$this->maxFiles} Images");
            return;
        }

        $tempFile = TempMedia::create();
        $tempFilename = $this->file->getClientOriginalName();
        $this->file->storeAs('/temp', $tempFilename);

        $tempFile->addMedia(
            storage_path('app/temp/'.$tempFilename)
        )
        ->toMediaCollection('default');

        $this->files[] = $tempFile;
    }

    public function removeMedia($id)
    {
        TempMedia::find($id)->delete();
        $this->files = $this->files->reject(function($file) use($id){
            return $file->id == $id;
        });
    }

    public function setCanAddMore(){
        $this->canAddMoreFiles = (is_null($this->maxFiles) || ($this->files->count() + $this->total_files) <  $this->maxFiles);
    }

    private function config()
    {
        return [
            'classes' => 'd-flex flex-column justify-content-center align-items-center w-100 rounded',
            'styles' => "background-color:#ededed;min-height:70px;text-align:center;cursor:pointer;",
            'defaultText' => 'Click to Select and Upload Files',
            'accept' => implode(',', [
                '*'
            ]),
            'files' => []
        ];
    }

    protected function getListeners()
    {
        return ["file_removed-{$this->name}" => 'fileRemoved'];
    }

    public function fileRemoved($total_files){
        $this->total_files = $total_files;
    }

}
