<div>
	@if(is_null($total_files) || $total_files < $maxFiles)
		<label for="{{$name}}-file" id="root-{{$name}}" class="p-1 position-relative {{ $config['classes'] }}" style="{{ $config['styles'] }}">

			@if($canAddMoreFiles)
				<div class="input-wrapper">
					{{ $config['defaultText'] }}
					<input accept="{{ $config['accept'] }}" type="file" id="{{$name}}-file" type="file" wire:model="file" class="d-none">
				</div>
			@endif

			<div wire:loading wire:target="file" id="progress-bar" class="w-100 bg-light position-absolute rounded" style="height: 10px;top:0;left:0;z-index:100;">
				<div class="progress h-100 bg-success d-block position-relative rounded m-0">
					<span style="font-size: 8px;color:#fff;font-weight:bold;"></span>
				</div>
			</div>



			<div class="file-preview p-1 cursor-default {{ $files->isEmpty() ? 'd-none' :'' }}">
				@foreach($files as $fileTemp)
					@if($file = $fileTemp->getFile())
						<div class="preview position-relative d-inline-block">
							<input type="hidden" name="{{ $name }}[]" value="{{$fileTemp->id}}">
							<button type="button" wire:click="removeMedia('{{$fileTemp->id}}')" class="btn p-0 rounded-circle btn-danger position-absolute" style="top:0;right:0;width:25px; height:25px;">X</button>
							
							@if($file->type == 'image')
								<img src="{{ route('media.show', $file) }}" class="m-1" style="width: 100px;height:100px;" alt="">
							@else
								<br>
								<br>
								{{ $file->name }}
							@endif
						</div>
					@endif
				@endforeach
			</div>

			<div class="loader w-100 h-100  position-absolute" style="background-color:#fff;opacity:0.5;top:0;right:0;" wire:loading>
				<div class="d-flex w-100 h-100 justify-content-center align-items-center">
					<h4>
						<i class="fa fa-spinner fa-spin"></i>
					</h4>
				</div>
			</div>

			@error($name)
				<div class="text-danger">
					{{ $message }}
				</div>
			@enderror
		</label>
	@endif
</div>

@push('lvjs')
	<script>
		document.addEventListener('livewire:load', function() {
			let progressBar = null;
			let progress = null;
			let progressText = null;

			$('#{{$name}}-file').on('livewire-upload-start', function(data) {
				progressBar = $('#root-{{$name}}').find('#progress-bar');
				progress = $(progressBar).find('.progress');
				progressText = $(progress).find('span');

				$(progress).css({
					width: `0%`
				});
				$(progressText).text(`0%`);
			})

			$('#{{$name}}-file').on('livewire-upload-progress', function(data) {
				$(progress).css({
					width: `${data.detail.progress}%`
				});
				$(progressText).text(`${data.detail.progress}%`);
			})
		})
	</script>
@endpush