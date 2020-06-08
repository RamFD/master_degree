@section('project-form')
    <div class="modal fade" id="modalProjectForm" tabindex="-1" role="dialog" aria-labelledby="ProjectModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title w-100 font-weight-bold">Создание проекта</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">
                    <div class="md-form mb-5">
                        <label for="project-name">Имя проекта</label>
                        <input type="text" id="project-name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button class="btn btn-default" type="button" id="submit-project">Создать проект</button>
                </div>
            </div>
        </div>
    </div>
@endsection
