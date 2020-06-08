<?php

namespace App;

Use DB;
use Illuminate\Database\Eloquent\Model;

class Editor extends Model
{
    /**
     * Ассоциируемая таблица базы данных
     *
     * @var string
     */

    function get_treeview($directory, $return_link, $extensions = array(), $path) {
        if (substr($directory, -1) == "/") $directory = substr($directory, 0, strlen($directory) - 1);
        $treeview = "";
        $treeview .= $this->php_file_tree_dir($directory, $return_link, $extensions, true, $path);
        return $treeview;
    }

    function php_file_tree_dir($directory, $return_link, $extensions = array(), $first_call = true, $path) {

        if( function_exists("scandir") ) $file = scandir($directory);
        natcasesort($file);
        $files = $dirs = array();
        foreach($file as $this_file) {
            if( is_dir("$directory/$this_file" ) ) $dirs[] = $this_file; else $files[] = $this_file;
        }
        $file = array_merge($dirs, $files);

        if( !empty($extensions) ) {
            foreach( array_keys($file) as $key ) {
                if( !is_dir("$directory/$file[$key]") ) {
                    $ext = substr($file[$key], strrpos($file[$key], ".") + 1);
                    if( !in_array($ext, $extensions) ) unset($file[$key]);
                }
            }
        }

        if( count($file) >= 2 ) {
            $php_file_tree = "<ul";
            if( $first_call ) { $php_file_tree .= " class=\"php-file-tree\""; $first_call = false; }
            $php_file_tree .= ">";
            foreach( $file as $this_file ) {
                if( $this_file != "." && $this_file != ".." ) {
                    if( is_dir("$directory/$this_file") ) {
                        $link = str_replace("[link]", "$directory/" . urlencode($this_file), $return_link);
                        $link = str_replace($path . "/", "", $link);
                        $link = str_replace("+", " ", $link);
                        $php_file_tree .= "<li class=\"pft-directory\"><a href=\"#\" class=\"treeview-folder\" style='word-wrap: normal' id=\"$link\">" . htmlspecialchars($this_file) . "</a>";
                        $php_file_tree .= $this->php_file_tree_dir("$directory/$this_file", $return_link ,$extensions, false, $path);
                        $php_file_tree .= "</li>";
                    } else {
                        $ext = "ext-" . substr($this_file, strrpos($this_file, ".") + 1);
                        $link = str_replace("[link]", "$directory/" . urlencode($this_file), $return_link);
                        $link = str_replace($path . "/", "", $link);
                        $php_file_tree .= "<li class=\"pft-file\"><a  class=\"treeview-file\" style='word-wrap: normal' data-ext=\"" . $ext . "\" href=\"\" id=\"$link\">" . htmlspecialchars($this_file) . "</a></li>";
                    }
                }
            }
            $php_file_tree .= "</ul>";
        }
        return $php_file_tree;
    }

    public function getFileContents($project, $filename, $id) {
        $path = config('editor.PATH_TO_PROJECTS') . $id . "/" . $project . "/" . $filename;
        if (file_exists($path)) {
            $pathinfo = pathinfo($path);
            if (isset($pathinfo['extension']) && $pathinfo['extension'] == "png") {
                return "something";
            } else {
                $contents = file_get_contents($path);
                if (empty($contents)) {
                    $contents = " ";
                }
                return $contents;
            }
        } else {
            return 0;
        }
    }

    public function createFile($filename, $userId, $project, $ext) {
        $dirpath = config('editor.PATH_TO_PROJECTS') . $userId . "/" . $project . "/";
        $fullpath = $dirpath . $filename;
        if (!is_dir($dirpath) || !is_writable($dirpath)) {
            return 0; //"Произошла ошибка: директория не существует или нельзя создать файл."
        } else if (is_file($fullpath) && !is_writable($fullpath)) {
            return 1; //"Произошла ошибка: невозможно записать в файл.";
        }
        if (!file_exists($fullpath . $ext)) {
            $handle = fopen($fullpath . $ext, 'w');
            if (!$handle) {
                return 2; //"Произошла ошибка при создании файла";
            }
        } else {
            return 3;//"Файл с таким именем уже существует";
        }
        return $this->makeFileForTreeview($filename . $ext, $ext);
    }

    public function makeFileForTreeview($filename, $ext) {
        $ext = substr($ext, 1);
        $ext = "ext-".$ext;
        return "<li class=\"pft-file\"><a class=\"treeview-file\" href=\"#\" style='word-wrap: normal' data-ext='$ext' id=\"$filename\">" . $filename . "</a></li>";
    }

    public function saveFileContents($contents, $filename, $userId, $project) {
        $fullpath =  config('editor.PATH_TO_PROJECTS') . $userId . "/" . $project . "/";
        if (!file_exists($fullpath . $filename)) {
            return 0; //Файла не существует
        }
        if (is_file($fullpath . $filename) && !is_writable($fullpath . $filename)) {
            return 1; //"Произошла ошибка: невозможно записать в файл.";
        }
        $result = file_put_contents($fullpath . $filename, $contents);
        return "success";
    }

    public function getFuncNames($path) {
        $result = array();
        $file = fopen($path, "r");
        if ($file) {
            while (($buffer = fgets($file, 4096)) !== false) {
                if (strpos($buffer, "funcdef")) {
                    $buffer = str_replace(" ", "", $buffer);
                    $buffer = substr($buffer, 0, strpos($buffer, "<<funcdef"));
                    $result[] = $buffer;
                }
            }
            if (!feof($file)) {
                return 0;
            }
        }
        return $result;
    }

    public function createProject($projectName, $userid, $makeFolder = true) {

        $same_name = DB::table('projects')->where([
            ['user_id', '=', $userid],
            ['name', '=', $projectName],
        ])->first();

        if ($same_name) {
            return 0;//"Проект с таким именем уже существует";
        } else {
            $import_code = $this->makeImportCode();
            $item = ['name' => $projectName, 'user_id' => $userid , 'import_code' => $import_code];
            $status = DB::table('projects')->insert($item);
            $projectId = DB::getPdo()->lastInsertId();
            if ($status) {
                $path = config('editor.PATH_TO_PROJECTS') . $userid . "/" . $projectName;
                if (!mkdir($path)) {
                    $this->removeProjectFromDb($userid, $projectName);
                    return 1;//"Произошла ошибка при создании директории проекта";
                } else {
                    file_put_contents($path . '/repository.ini', 'default ./repository/');
                    return $this->makeProject($projectName, $projectId, $import_code);//"Проект с именем " . $projectName . " был создан";
                }
                
            } else {
                return 2;//"Произошла ошибка при создании записи в базу данных";
            }
        }
    }

    public function importProject($importCode, $projectName, $userid) {
        $importedProject = $this->getImportedProject($importCode);
        if (is_null($importedProject)) {
            return 3;
        } else {
            $path_to_imported = config('editor.PATH_TO_PROJECTS') . $importedProject->user_id . "/" . $importedProject->name . "/";
            $path_to_dir = config('editor.PATH_TO_PROJECTS') . $userid . "/";
            $return_var = $this->createProject($projectName, $userid, false);
            if (is_numeric($return_var)) {
                return $return_var;
            } else {
                exec('rm -rf ' . $path_to_dir . $projectName);
                exec("cp -r $path_to_imported $path_to_dir");
                exec("mv $path_to_dir$importedProject->name $path_to_dir$projectName");
                return $return_var;
            }
        }
    }

    public function getImportedProject($importCode) {
        return DB::table('projects')->where('import_code', '=', $importCode)->first();
    }

    public function makeImportCode() {
        return bin2hex(random_bytes(16));
    }

    public function makeProject($projectName, $projectId, $import_code) {
        return "<li><a href=\"\" class=\"project-item\" id=\"$projectId\" data-importcode='$import_code'>".$projectName."</a></li>";
    }

    public function removeProjectFromDb($userid, $projectName) {
        $result = DB::table('projects')->where([
            ['user_id', '=', $userid],
            ['name', '=', $projectName]
        ])->delete();

        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getProjects($userid) {
        $result = DB::table('projects')
            ->where('user_id', '=', $userid)->get();
        if ($result) {
            return $result;
        } else {
            return 0;
        }
    }

    public function runProject($userId, $project, $filename, $funcname, $arg, $argrequired) {
        
        $path = config('editor.PATH_TO_PROJECTS') . $userId . "/" . $project . "/";

        $trans_result = $this->runTrans($path, $filename);
        if ($trans_result['return_var'] != 0) {
            return $trans_result;
        }

        $cgen_result = $this->runCgen($path, $filename);

        if (!(count(array_unique($cgen_result['return_var'])) === 1 && end($cgen_result['return_var']) === 0)) {
            return $cgen_result;
        }

        if ($argrequired) {
            $argresult = $this->prepareArg($arg, $path, $project, $userId);
            if (array_key_exists('failed_operation', $argresult)) {
                return "arg_error";
            }
        }

        $inter_result = $this->runInter($funcname, $path);
        $result = ['trans_result' => $trans_result['output'], 'cgen_result' => $cgen_result['output'], 'inter_result' => $inter_result['output']];
        return $result;
    }

    public function prepareArg($arg, $path, $project, $userid) {
        $createResult = $this->createFile("arg", $userid, $project, ".pfg");
        if (is_numeric($createResult) && $createResult !=3) {
            return ['failed_operation' => 1, 'fail_code' => $createResult];
        }
        $saveResult = $this->saveFileContents($arg, "arg.pfg", $userid, $project);
        if (is_numeric($saveResult)) {
            return ['failed_operation' => 2, 'fail_code' => $saveResult];
        }
        return $this->runTrans($path, "arg.pfg", true);
    }

    public function runTrans($path, $filename, $isArg = false) {
        $mode = $isArg? "-c" : "-t";
        if (!file_exists($path . $filename)) {
            return false;
        } else {
            exec("cd ". $path ." && ". config('editor.PATH_TO_TRANS') . " $mode $filename $filename.errlog $filename.dbglog", $output, $return_var);
            return ['output' => $output, 'return_var' => $return_var];
        }
    }

    public function runCgen($path, $filename) {
        $funcnames = $this->getFuncNames($path . $filename);
        foreach ($funcnames as $func) {
            exec("cd $path && " . config('editor.PATH_TO_CGEN') . " -n $func", $output, $return_var);
            $return_var_arr[] = $return_var;
        }
        return ['output' => $output, 'return_var'=>$return_var_arr];
    }

    public function runInter($funcname, $path) {
        exec("cd " . $path . " && " .config('editor.PATH_TO_INTER') . " $funcname", $output, $return_var);
        return ['output'=>$output, 'return_var'=>$return_var];
    }

    public function runCgToDot($filename, $path) {
        exec("cd $path && " . config('editor.PATH_TO_CG2DOT') . " -png $filename", $output, $return_var);
        if ($return_var == -1) {
            return $return_var;
        } else {
            return $this->getImage($path, $filename . ".png");
        }
    }

    public function runRigToDot($filename, $path) {
        exec("cd $path && " . config('editor.PATH_TO_RIG2DOT') . " -png $filename", $output, $return_var);
        if ($return_var == -1) {
            return $return_var;
        } else {
            return $this->getImage($path, $filename . ".png");
        }
    }

    public function getImage($path, $filename) {
        return base64_encode(file_get_contents($path . $filename));
    }
}
