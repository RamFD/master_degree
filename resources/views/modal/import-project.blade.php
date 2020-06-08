@section('import-project')
    <div class="modal fade" id="modalImportProject" tabindex="-1" role="dialog" aria-labelledby="ImportProjectModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title w-100 font-weight-bold">Импорт проекта</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">
                    <div class="md-form form-sm">
                        <label for="import-code-input">Код импорта</label>
                        <input type="text" id="import-code-input" class="form-control" required>
                    </div>
                    <div class="md-form form-sm">
                        <label for="project-name-after-import">Название проекта после импорта</label>
                        <input type="text" id="project-name-after-import" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button class="btn btn-default" type="button" id="import-project-btn">Импортировать проект</button>
                </div>
            </div>
        </div>
    </div>
@endsection
