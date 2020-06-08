<?php

namespace App\Http\Controllers;

use App\Editor;
use Illuminate\Http\Request;
use Auth;

class EditorController extends Controller
{

    protected $Model;

    public function __construct(Editor $obj)
    {
        $this->middleware('auth');
        $this->Model = $obj;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = $this->Model->getProjects(Auth::user()->id);
        return view('editor', compact('projects'));
    }

    public function getFileContents(Request $request) {
        $project = $request->project;
        $filename = $request->filename;
        $contents = $this->Model->getFileContents($project, $filename, Auth::user()->id);
        if ($contents) {
            return response($contents);
        } else {
            return response("Невозможно получить содержимое файла", 400);
        }
        return response($contents);
    }

    public function saveFileContents(Request $request) {
        $contents = $request->contents;
        $filename = $request->filename;
        $project = $request->project;
        $response = $this->Model->saveFileContents($contents, $filename, Auth::user()->id, $project);
        if (is_numeric($response)) {
            $response = "Произошла ошибка при сохранении содержимого файла ";
            if ($response == 0) {
                $response = $response . ": Файла не существует";
            } else {
                $response = $response . ": Запись в файл невозможна";
            }
            return response($response, 400);
        } else {
            $response = "Содержимое файла $filename было сохранено";
            return response($response);
        }
    }

    public function createFile(Request $request) {
        $filename = $request->filename;
        $project = $request->project;
        $result = $this->Model->createFile($filename, Auth::user()->id, $project, ".pfg");
        if (is_string($result)) {
            return response($result);
        } else {
            if ($result == 0) {
                return response("Произошла ошибка: директория не существует или нельзя создать файл.", 400);
            } else if ($result == 2) {
                return response("Произошла ошибка: невозможно создать файл", 400);
            } else if ($result == 3) {
                return response("Произошла ошибка: файл с таким именем уже существует", 400);
            }
        }
    }

    public function createProject(Request $request) {
        $project = $request->project;
        $result = $this->Model->createProject($project, Auth::user()->id);
        if (is_numeric($result)) {
            if ($result == 0) {
                return response("Проект с таким именем уже существует", 400);
            } else {
                return response("Произошла ошибка при создании проекта", 400);
            }
        }
        return response($result);
    }

    public function importProject(Request $request) {
        $import_code = $request->importCode;
        $project_name_after_import = $request->projectNameAfterImport;
        $result = $this->Model->importProject($import_code, $project_name_after_import, Auth::user()->id);
        if (is_numeric($result)) {
            if ($result == 0) {
                return response("Проект с таким именем уже существует", 400);
            } else if ($result == 1 || $result == 2) {
                return response("Произошла ошибка при создании проекта", 400);
            } else {
                return response("Такого кода импорта не существует", 400);
            }
        }
        return $result;
    }
    
    public function getProjectTreeview(Request $request) {
        $project = $request->project;
        $path = config('editor.PATH_TO_PROJECTS') . Auth::user()->id . "/" . $project;
        $treeview = $this->Model->get_treeview($path, "[link]", "", $path);
        return $treeview;
    }

    public function getPngFile(Request $request) {
        $filename = $request->filename;
        $project = $request->project;
        $path_to_file = "";
        if (strpos($filename, '/') !== false) {
            $path_to_file = substr($filename, 0, strrpos($filename, '/')+1);
            $filename = substr($filename, strrpos($filename, '/')+1, strlen($filename) - 1);
        }
        $path = config('editor.PATH_TO_PROJECTS') . Auth::user()->id . "/" . $project . "/" . $path_to_file;
        return $this->Model->getImage($path, $filename);
    }

    public function cgToPng(Request $request) {
        $filename = $request->filename;
        $project = $request->project;
        $path_to_file = "";
        if (strpos($filename, '/') !== false) {
            $path_to_file = substr($filename, 0, strrpos($filename, '/')+1);
            $filename = substr($filename, strrpos($filename, '/')+1, strlen($filename) - 1);
        }
        $path = config('editor.PATH_TO_PROJECTS') . Auth::user()->id . "/" . $project . "/" . $path_to_file;
        return $this->Model->runCgToDot($filename, $path);
    }

    public function rigToPng(Request $request) {
        $filename = $request->filename;
        $project = $request->project;
        $path_to_file = "";
        if (strpos($filename, '/') !== false) {
            $path_to_file = substr($filename, 0, strrpos($filename, '/')+1);
            $filename = substr($filename, strrpos($filename, '/')+1, strlen($filename) - 1);
        }
        $path = config('editor.PATH_TO_PROJECTS') . Auth::user()->id . "/" . $project . "/" . $path_to_file;
        return $this->Model->runRigToDot($filename, $path);
    }

    public function runTrans(Request $request) {
        $project = $request->project;
        $filename = $request->filename;
        $path = config('editor.PATH_TO_PROJECTS') . Auth::user()->id . "/" . $project . "/";
        $result = $this->Model->runTrans($path, $filename, false);
        return $result['output'];
    }

    public function runCgen(Request $request) {
        $filename = $request->filename;
        $project = $request->project;
        $path = config('editor.PATH_TO_PROJECTS') . Auth::user()->id . "/" . $project . "/";
        $result = $this->Model->runCgen($path, $filename);
        return $result['output'];
    }

    public function runInter(Request $request) {
        $funcname = $request->funcname;
        $project = $request->project;
        $path = config('editor.PATH_TO_PROJECTS') . Auth::user()->id . "/" . $project . "/";
        $argrequired = $request->argRequired;
        if ($argrequired) {
            $arg = $request->arg;
            $argresult = $this->Model->prepareArg($arg, $path, $project, Auth::user()->id);
            if (array_key_exists('failed_operation', $argresult)) {
                return response('Произошла ошибка при подготовке файла аргумента');
            }
        }
        $interResult = $this->Model->runInter($funcname, $path);
        return $interResult['output'];
    }

    public function runProject(Request $request) {
        $filename = $request->filename;
        $project = $request->project;
        $arg = $request->arg;
        $argrequired = $request->argRequired;
        $funcname = $request->funcname;
        $result = $this->Model->runProject(Auth::user()->id, $project, $filename, $funcname, $arg, $argrequired);
        return $result;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
}
