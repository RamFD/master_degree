@section('run-func')
    <div class="modal fade" id="modalRunFunctionForm" tabindex="-1" role="dialog" aria-labelledby="RunFunctionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="modal-title w-100 font-weight-bold">Запуск проекта</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">
                    <div class="md-form form-sm">
                        <label for="func-name">Выберите функцию</label>
                        <!--<input type="text" id="func-name" class="form-control" required>-->
                        <select class="mdb-select md-form" id="func-names">
                        </select>
                    </div>
                    <div class="md-form form-sm">
                        <input type="checkbox" class="form-check-input" id="arg-checkbox">
                        <label class="form-check-label" for="arg-checkbox">Необходим аргумент</label>
                    </div>
                    <div class="md-form form-sm" id="arg-div" style="display:none">
                        <label for="arg-value">Аргумент функции</label>
                        <input type="text" id="arg-value" class="form-control">
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button class="btn btn-default" type="button" id="run-function">Запустить функцию</button>
                </div>
            </div>
        </div>
    </div>
@endsection
