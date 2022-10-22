<div id="editable-upload" class="col-md-6">
    <div class="progress mb-2">
        <div class="progress-bar" id="upload-progress" style="width: 38%" role="progressbar" aria-valuenow="38" aria-valuemin="0" aria-valuemax="100" aria-label="38% Complete">
            <span class="visually-hidden">38% Complete</span>
        </div>
        <br>
    </div>
    <small class="text-muted">Upload <span id="total-uploaded"></span> of <span id="upload-total-size"></span> bytes</small>
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">Edit video details</h3>
        </div>
        <div class="card-body">
            <form>
                <div class="form-group mb-3 ">
                    <label class="form-label required">Title</label>
                    <div>
                        @include('components.fields.text', [
                            'name' => 'title',
                            'required' => true,
                            'placeholder' => 'Jon Snow climbs the wall',
                            'hint' => 'Captivate your audience with an interesting title'
                        ])
                    </div>
                </div>
                <div class="form-group mb-3 ">
                    <label class="form-label required">Description</label>
                    <div>
                        @include('components.fields.textarea', [
                            'name' => 'description',
                            'required' => true,
                            'placeholder' => 'He was alone. The others were scared to follow.',
                            'hint' => 'Explain what your video is about.',
                            'rows' => 5,
                        ])
                    </div>
                </div>
                <div class="form-group mb-3 ">
                    <label class="form-label">Scope</label>
                    <div>
                        @include('components.fields.select', [
                            'name' => 'scope',
                            'hint' => 'Who should see your video',
                            'options' => [
                                'public' => 'Public',
                                'private' => 'Private',
                                'unlisted' => 'Unlisted'
                            ],
                        ])
                    </div>
                </div>
                <div class="form-footer">
                    @include('components.button', [
                            'modifier_class' => 'btn-outline-success',
                            'type' => 'submit',
                            'text' => 'Update',
                    ])
                </div>
            </form>
        </div>
    </div>
</div>