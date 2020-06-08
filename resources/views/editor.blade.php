@extends('layouts.app')
@include('modal.instruction')
@include('modal.project-form')
@include('modal.file-form')
@include('modal.run-func')
@include('modal.project-import-code')
@include('modal.import-project')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2">
            <div class="card card-primary mt-3 text-left">
                <div class="card-header">Проводник</div>
                <div class="card-body explorer">
                    <ul class="nav nav-tabs">
                        <li class="nav-item tab-projects">
                            <a class="nav-link active" data-toggle="tab" href="#projects">Проекты</a>
                        </li>
                        <li class="nav-item tab-project-contents">
                            <a class="nav-link disabled" data-toggle="tab" href="#project-contents" id="project-contents-tab">Файлы проекта</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="projects">
                            <ul id="project-list">
                                @foreach ($projects as $project)
                                <li><a href="" class="project-item" id="{{$project->id}}" data-importcode="{{$project->import_code}}">{{$project->name}}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="tab-pane fade overflow--auto" id="project-contents">

                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    &nbsp;
                    <a href="" id="create-project" data-toggle="modal" data-target="#modalProjectForm"><i class="fas fa-plus"></i></a>
                    &nbsp;
                    <a href="" class="disabled" id="create-file" data-toggle="modal" data-target="#modalFileForm"><i class="fas fa-file-plus"></i></a>
                    &nbsp;
                    <a href="" class="disabled" id="save-contents"><i class="fas fa-save"></i></a>
                    &nbsp;
                    <a href="" id="project-import-list" onclick="listExportProject()" data-toggle="modal" data-target="#listProjectImportCode"><i class="fas fa-pen"></i></a>
                    &nbsp;
                    <a href="" id="import-project" data-toggle="modal" data-target="#modalImportProject"><i class="fas fa-file-import"></i></a>
                    &nbsp;
                    <a href="" id="instruction" data-toggle="modal" data-target="#modalInstruction"><i class="fas fa-info"></i></a>
                </div>
            </div>
        </div>

        @yield('project-form')

        @yield('file-form')

        @yield('run-func')

        @yield('project-import-code')

        @yield('instruction')
        
        @yield('import-project')

        <div class="col-sm-6 editor-container">
            <ul class="editor-tabs" id="tab-container">

            </ul>
            <div id="editor" style="height:80%; width:100%"></div>
            <div class="row">
                <div class="text-center mt-3">
                    <button type="button" id="toggle-run-func-modal" class="btn btn-outline-secondary">run func</button>
                    <button type="button" id="trans" class="btn btn btn-outline-secondary">trans</button>
                    <button type="button" id="cgen" class="btn btn btn-outline-secondary">cgen</button>
                    <button type="button" id="inter" class="btn btn btn-outline-secondary">inter</button>
                    <button type="button" id="rig2png" class="btn btn btn-outline-secondary">rig2png</button>
                    <button type="button" id="cg2png" class="btn btn btn-outline-secondary">cg2png</button>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="output-label-card card card-body scroll-layer container">

                </div>
            </div>
        </div>
        <div class="col-sm-6 home-tab">
            <div class="row">
                <blockquote class="blockquote text-center">
                    <p class="mb-0">Прежде чем начать работу ознакомьтесь с инструкциями, нажав на символ <a href="" id="instruction" data-toggle="modal" data-target="#modalInstruction"><i class="fas fa-info"></i></a> под проводником.</p>
                </blockquote>
            </div>
        </div>
        <div class="col-sm-4 image-gallery">


        </div>
    </div>

</div>

<script
  src="https://code.jquery.com/jquery-3.5.1.min.js"
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  crossorigin="anonymous"></script>
<script src="{{ asset('js/ace/src/ace.js') }}" type="text/javascript" charset="utf-8"></script>
<script src="{{ asset('js/editor.js') }}" type="text/javascript" charset="utf-8"></script>
<script>

</script>

@endsection
