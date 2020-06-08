@section('instruction')
    <div class="modal fade" id="modalInstruction" tabindex="-1" role="dialog" aria-labelledby="instructionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Инструкция</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div id="instruction-body">
                    {{-- список вкладок --}}
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#instruction1" role="tab">Начало работы</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#instruction2" role="tab">Работа с редактором кода</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#instruction3" role="tab">Функции кнопок</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#instruction4" role="tab">Дополнительные возможности</a>
                        </li>
                    </ul>
                    {{-- содержимое вкладок --}}
                    <div class="modal-body">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="instruction1" role="tabpanel">
                                <p>Чтобы начать работу необходимо создать проект при помощи кнопки "<i class="fas fa-plus"></i>", которая находится в нижней части проводника.</p>
                                <p>После этого проект появится в списке и при нажатии на него вкладка "Файлы проекта" Проводника разблокируется.</p>
                                <p>В проекте изначально есть только служебный файл repository.ini. Для создания нового файла необходимо нажать на кнопку "<i class="fas fa-file-plus"></i>".</p>
                                <p>При удачном создании файла он появится в списке файлов проекта, а при нажатии на него откроется редактор кода.</p>
                            </div>
                            <div class="tab-pane fade" id="instruction2" role="tabpanel">
                                <p>После написания кода необходимо сохранить содержимое файла нажатием на кнопку "<i class="fas fa-save"></i>".</p>   
                                <p>После этого можно обратить внимание на кнопки, находящиеся под редактором кода.</p> 
                                <p>Для файла с расширением .pfg - это "run func", "trans", "cgen" и "inter".</p>
                                <p>Для файла с расширением .cg - это "cg2png".</p>
                                <p>И наконец для файла с расширением .rig - это "rig2png".</p>
                            </div>
                            <div class="tab-pane fade" id="instruction3" role="tabpanel">
                                <p>Кнопка "trans" транслирует содержимое файла с кодом, генерируя тем самым реверсивные информационные графы.</p>
                                <p>Кнопка "cgen" строит управляющие графы для каждой функции в коде.</p>
                                <p>Кнопка "inter" открывает модальное окно, в котором вам предложится выбрать функцию подлежащую интерпретации, а также при необходимости ввести аргумент для данной функции.</p>
                                <p>Кнопка "run func" делает все то, что делают предыдущие кнопки одновременно.</p>
                                <p>Кнопка "rig2png" преобразует реверсивный информационный граф (файл с расширением .rig) в изображение и выводит его на экран.</p>
                                <p>Кнопка "cg2png" преобразует управляющий граф (файл с расширением .cg) в изображение и выводит его на экран.</p>
                            </div>
                            <div class="tab-pane fade" id="instruction4" role="tabpanel">
                                <p>Дополнительную отладочную информацию можно найти в файлах с расширением .errlog, .dbglog, .txt.</p>
                                <p>При нажатии на уже сгенерированный файл с расширением .png он выведется на экран.</p>
                                <p>У каждой вкладки редактора есть свой "Undo Manager". При внесении нежеланных изменений нажмите комбинацию клавиш ctrl+z.</p>
                                <p>При нажатии на кнопку "<i class="fas fa-pen"></i>" откроется модальное окно со списком всех проектов и кодов для их импорта.</p>
                                <p>При нажатии на кнопку "<i class="fas fa-file-import"></i>" откроется модальное окно с предложением ввести код импорта проекта, а так же его новое название.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
@endsection
