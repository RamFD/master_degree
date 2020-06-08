@section('file-form')
    <div class="modal fade" id="modalFileForm" tabindex="-1" role="dialog" aria-labelledby="FileModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title w-100 font-weight-bold">Создание файла</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">
                    <div class="md-form mb-5">
                        <label for="file-name">Название файла</label>
                        <div class="form-inline">
                            <input type="text" id="file-name" class="form-control" required>
                            <label for="file-name">.pfg</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button class="btn btn-default" type="button" id="submit-file">Создать файл</button>
                </div>
            </div>
        </div>
    </div>
@endsection
