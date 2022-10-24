<div id="draggable-upload" class="col-8">
    <div class="card">
        <div class="card-body mb-3">
            <h3 class="card-title">Upload Video</h3>
            <form class="dropzone" id="dropzone-custom" method="POST" action="{{ route('store_video') }}">
                <div class="fallback">
                    <input name="file" type="file"  />
                </div>
                <div class="dz-message">
                    <h3 class="dropzone-msg-title">Drag and drop video files to upload</h3>
                    <span class="dropzone-msg-desc">Please ensure to only upload content you own. Copyright violation may result in video removal.</span>
                </div>
            </form>
        </div>
    </div>
</div>
