<?php

namespace Topdot\Media\App\Http\Livewire;

use Livewire\Component;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FilePreviewComponent extends Component
{
    public Model $model;
    public string $collection;

    public function mount($model, $collection)
    {
        $this->model = $model;
        $this->collection = $collection;
    }

    public function render()
    {
        return view('media::livewire.file-preview-component');
    }

    public function remove(Media $media)
    {
        $media->delete();
        $this->model->refresh();
        $this->emit('file_removed', $this->model->getMedia($this->collection)->count());
    }
}
