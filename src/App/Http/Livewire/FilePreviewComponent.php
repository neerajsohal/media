<?php

namespace Dotlogics\Media\App\Http\Livewire;

use Livewire\Component;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FilePreviewComponent extends Component
{
    public Model $model;
    public string $collection;
    public $deleted = [];

    public function mount($model, $collection)
    {
        $this->model = $model;
        $this->collection = $collection;

        $this->deleted = old("deleted_files.{$collection}", []);
    }

    public function render()
    {
        return view('media::livewire.file-preview-component');
    }

    public function remove(Media $media)
    {
        if(config('media.delete_from_db')){
            $media->delete();
            $this->model->refresh();

            $less = 0;
        }else{
            $this->deleted[] = $media->id;
            $less = count($this->deleted);
        }

        $total = $this->model->getMedia($this->collection)->count();

        $this->emit("file_removed-{$this->collection}", $total - $less);
    }
}
