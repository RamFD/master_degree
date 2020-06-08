$(document).ready(function() {
    var editor = ace.edit("editor");
    editor.session.setUseSoftTabs(true);

    var chosenFolder;
    var openedProject;

    function Tab(contents, filename, element, project, ext) {
        this.contents = contents;
        this.filename = filename;
        this.element = element;
        this.project = project;
        this.unsavedChanges = false;
        this.ext = ext;
    };

    var tabManager = {
        tabArray: Array(),

        addTab : function(contents, filename, project, ext) {
            if (!($(".editor-container").is(":visible"))) {
                $(".home-tab").hide();
                $(".editor-container").show();
                $(".output-label-card").show();
                editor.renderer.updateFull();
                editor.resize();
            }
            var self = this;

            var liElement = document.createElement('li');
            var iElement = document.createElement('span');

            var newtab;

            if (contents && filename) {
                newtab = new Tab(new ace.createEditSession(contents, "text"), filename, iElement, project, ext);
            } else {
                newtab = new Tab(new ace.createEditSession("", "text"), "New File", iElement, project, ext);
            }
            newtab.contents.on('change', function () {
                newtab.unsavedChanges = true;
                newtab.element.parentNode.classList.add("bold");
            });

            iElement.setAttribute('id', this.tabArray.length);
            var textChild = document.createElement('span');
            textChild.setAttribute('id', this.tabArray.length);
            textChild.addEventListener('click', function(event) {
                self.selectTab(textChild.id);
            });

            this.tabArray.push(newtab);
            iElement.className = 'fa fa-times';
            iElement.addEventListener('click', function(event) {
                self.deleteTab(iElement, iElement.id);
            }, false);

            if (filename) {
                textChild.innerHTML = filename;
            } else {
                textChild.innerHTML = "New File";
            }
            liElement.appendChild(textChild);
            liElement.appendChild(iElement);
            liElement.classList.add('editor-tab--active');
            liElement.classList.add('editor-tab');
            var tabContainer = document.getElementById('tab-container');
            tabContainer.append(liElement);

            self.selectTab(this.tabArray.length-1);
        },

        selectTab : function(tabId) {
            const lastSelectedTab = document.getElementsByClassName('editor-tab--active');
            for (let i = 0; i < lastSelectedTab.length; i += 1) {
                lastSelectedTab[i].classList.remove('editor-tab--active');
            }
            this.tabArray[tabId].element.parentNode.classList.add('editor-tab--active');
            editor.setSession(this.tabArray[tabId].contents);
            this.configureButtons(this.tabArray[tabId].ext);
        },

        configureButtons : function (ext) {
            /**
             * 0 => toggle-run-func-modal
             * 1 => trans
             * 2 => cgen
             * 3 => inter
             * 4 => rig2png
             * 5 => cg2png
             */
            let btnArray = ["toggle-run-func-modal", "trans", "cgen", "inter", "rig2png", "cg2png"];
            let needToHide;
            let needToVisible;
            if (ext === "ext-pfg") {
                //выключить кнопки cg2png и rig2png
                needToHide = [4, 5];
                needToVisible = [0, 1, 2, 3];
            } else if (ext === "ext-rig") {
                needToHide = [0, 1, 2, 3, 5];
                needToVisible = [4];
            } else if (ext === "ext-cg") {
                //выключить все кроме cg2png
                needToHide = [0, 1, 2, 3, 4];
                needToVisible = [5];
            } else {
                //выключить все
                needToHide = [0, 1, 2, 3, 4, 5];
            }
            if (needToVisible !== undefined) {
                needToVisible.forEach(function (item) {
                    let elem = document.getElementById(btnArray[item]);
                    elem.style.display = "inline-block";
                });
            }
            needToHide.forEach(function (item) {
                let elem = document.getElementById(btnArray[item]);
                elem.style.display = "none";
            });
        },

        deleteTab : function(tab, tabId) {
            tabId = parseInt(tabId);
            var removed = this.tabArray.splice(tabId, 1);
            for (var i = tabId; i < this.tabArray.length; i++) {
                this.tabArray[i].element.id = i;
                this.tabArray[i].element.parentNode.children[0].id = i;
            }
            delete(removed);
            while(tab.nodeName != 'LI') {
               tab = tab.parentNode;
            }
            tab.parentNode.removeChild(tab);
            if (this.tabArray.length == 0) {
                editor.setSession("");
                $(".editor-container").hide(100);
                $(".output-label-card").hide(100);
                $(".home-tab").show(100);
                $("#save-contents").addClass('disabled');
            } else {
                this.selectTab(0);
            }
         }
    };

    $(".editor-container").hide();
    $(".output-label-card").hide();

    function getTabFromArray() {
        var activeTab = $('.editor-tab--active');
        var id = activeTab.children()[1].id;
        return tabManager.tabArray[id];
    }

    function getFuncNames() {
        var regEx = new RegExp("\\s*<<\\s*funcdef");
        var lines = getTabFromArray().contents.doc.getAllLines();
        var found = new Array();
        for (const element of lines) {
            if (regEx.test(element)) {
                found.push(element.substring(0, element.search(regEx)));
            }
        }
        return found;
    }

    $('#trans').click(function(event) {
        event.preventDefault();
        var activeTab = getTabFromArray();
        if (activeTab.unsavedChanges) {
            alert('Сохраните изменения');
        } else {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "editor/trans",
                type: "POST",
                data: {
                    "filename": activeTab.filename,
                    "project": activeTab.project,
                }, success: function(result) {
                    $('.output-label-card').empty();
                    $('.output-label-card').append(result);
                    refreshProjectContents(activeTab.project);
                }, error: function(data) {
                    alert(data.responseText);
                }
            });
        }
    });

    $('#project-contents').on("click", "a.treeview-folder", function(event) {
        event.preventDefault();
        if (event.target.classList.contains('folder-active')) {
            event.target.classList.remove('folder-active');
            chosenFolder = "";
        } else {
            $(".folder-active").removeClass('folder-active');
            event.target.classList.add('folder-active');
            chosenFolder = event.target.id;
        }
    });

    $('#project-contents').on("click", "a.treeview-file", function(event) {
        event.preventDefault();
        var ext = $(this).data('ext');
        if ($(this).data('ext') == 'ext-png') {
            getPngFile();
        } else {
            event.preventDefault();
            getFileContents(ext);
        }
    });

    function getPngFile() {
        chosenFile = event.target.id;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "editor/getPngFile",
            type: "POST",
            data: {
                "filename": chosenFile,
                "project": openedProject
            },
            success: function(result) {
                displayImage(result);
            }
        });
    }

    function getFileContents(ext) {
        chosenFile = event.target.id;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "editor/getFile",
            type: "POST",
            data: {
                "filename": chosenFile,
                "project": openedProject
            },
            success: function(result) {
                tabManager.addTab(result, chosenFile, openedProject, ext);
                $('#save-contents').removeClass('disabled');
            }
        });
    }

    $('#save-contents').click(function(event) {
        event.preventDefault();
        saveFileContents();
    });

    function saveFileContents() {
        var activeTab = getTabFromArray();
        var fileContents = editor.getValue();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax ({
            url: "editor/saveFileContents",
            type: "POST",
            data: {
                "filename": activeTab.filename,
                "contents": fileContents,
                "project": activeTab.project,
            }, success: function(result) {
                alert("Файл сохранен");
                activeTab.unsavedChanges = false;
                activeTab.element.parentNode.classList.remove('bold');
            }, error: function(data) {
                alert(data.responseText);
            }
        });
    }

    $("#submit-project").click(function(event) {
        event.preventDefault();
        var project = $("#project-name").val();
        var regEx = new RegExp("^[A-Za-z0-9_-]+$");
        if (!(regEx.test(project))) {
            alert("Некорректное имя проекта: возможно использовать только латинские буквы, цифры и следующие символы: _ -");
        } else {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "editor/createProject",
                type: "POST",
                data: {
                    "project": project,
                }, success: function(result) {
                    alert("Проект создан");
                    $('#project-list').append(result);
                }, error: function(data) {
                    alert(data.responseText);
                }
            });
        }
    });

    $('#project-list').on("click", "a.project-item", function(event) {
        openedProject = $(this).text();
        refreshProjectContents($(this).text());
        alert("Проект загружен");
    });

    function refreshProjectContents(project) {
        if (openedProject == project) {
            event.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "editor/getProjectTreeview",
                type: "POST",
                data: {
                    "project": project,
                }, success: function(result) {
                    $('.php-file-tree').remove();
                    $('#project-contents').append(result);
                    $('#project-contents-tab').removeClass('disabled');
                    openedProject = project;
                    $('#create-file').removeClass('disabled');
                    //Скрыть все папки
                    $(".php-file-tree").find("UL").hide();

    	            //Открыть/закрыть папку по клику
    	            $(".pft-directory A").click( function() {
    	            	$(this).parent().find("UL:first").slideToggle(0);
    	            	if( $(this).parent().attr('className') == "pft-directory" ) return false;
    	            });
                }
            });
        }
    }

    $('#submit-file').click(function(event) {
        event.preventDefault();
        var fileName = $("#file-name").val();
        var regEx = new RegExp("^[A-Za-z0-9_-]+$");
        if (!(regEx.test(fileName))) {
            alert("Некорректное имя файла: возможно использовать только латинские буквы, цифры и следующие символы: _ -");
        } else {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "editor/createFile",
                type: "POST",
                data: {
                    "filename": fileName,
                    "project": openedProject,
                }, success: function(result) {
                    alert("Файл был успешно создан");
                    $('.php-file-tree').append(result);
                }, error: function(data) {
                    alert(data.responseText);
                }
            });
        }
    });

    function displayFuncNames() {
        var funcnames = getFuncNames();
        $('#func-names').empty();
        for (const element of funcnames) {
            $('#func-names').append('<option value="' + element + '">'
                                        +element+"</option>");
        }
    }

    var triggeredBy;

    $('#toggle-run-func-modal').click(function(event) {
        displayFuncNames();
        $("#modalRunFunctionForm").modal('show');
        triggeredBy = "editor/runProject";
    });

    $('#inter').click(function(event) {
        displayFuncNames();
        $('#modalRunFunctionForm').modal('show');
        triggeredBy = "editor/inter";
    });

    $('#run-function').click(function(event) {
        event.preventDefault();
        var activeTab = getTabFromArray();
        if (activeTab.unsavedChanges) {
            alert("Пожалуйста сохраните изменения")
        } else {
            var funcName = $("#func-names option:selected").text();
            var argRequired = $("#arg-checkbox").is(':checked');
            var arg;
            if (argRequired) {
                arg = $("#arg-value").val();
            } else {
                arg = "";
            }
            var regEx = new RegExp("^[A-Za-z.]+$");
            if (!(regEx.test(funcName))) {

            } else {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: triggeredBy,
                    type: "POST",
                    data: {
                        "filename": activeTab.filename,
                        "project": activeTab.project,
                        "funcname": funcName,
                        "arg": arg,
                        "argRequired": argRequired,
                    }, success: function(result) {
                        if ('trans_result' in result) {
                            $('.output-label-card').append(result['trans_result'] + "\n");
                        }
                        if ('cgen_result' in result) {
                            for (const element of result['cgen_result']) {
                                $('.output-label-card').append(element + "\n");
                            }
                        }
                        if ('inter_result' in result) {
                            for (const element of result['inter_result']) {
                                $('.output-label-card').append(element + "\n");
                            }
                        } else {
                            for (const element of result) {
                                $('.output-label-card').append('<p>'+ element + '</p>');
                            }
                        }
                        refreshProjectContents(activeTab.project);
                    }, error: function(data) {
                        alert(data.responseText);
                    }
                });
            }
        }
    });

    $('#cgen').click(function(event) {
        event.preventDefault();
        var activeTab = getTabFromArray();
        if (activeTab.unsavedChanges) {
            alert("Пожалуйста сохраните изменения")
        } else {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "editor/cgen",
                type: "POST",
                data: {
                    "filename": activeTab.filename,
                    "project": activeTab.project,
                }, success: function(result) {
                    $('.output-label-card').empty();
                    for (const element of result) {
                        $('.output-label-card').append(element + "\n");
                    }
                    refreshProjectContents(activeTab.project);
                }, error: function(data) {
                    alert(data.responseText);
                }
            });
        }
    });

    function displayImage(base64code) {
        $(".image-gallery").empty();
        var img = new Image();
        img.src = "data:image/png;base64, " + base64code;
        img.classList.add('img-fluid');
        $('.image-gallery').append(img);
    }

    $('#rig2png').click(function(event) {
        event.preventDefault();
        var activeTab = getTabFromArray();
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
        });
        $.ajax({
            url: "editor/rigtopng",
            type: "POST",
            data: {
                "filename": activeTab.filename,
                "project": activeTab.project,
            }, success: function(result) {
                displayImage(result);
            }, error: function(data) {
                alert(data.responseText);
            }
        });
    });

    $('#cg2png').click(function(event) {
        event.preventDefault();
        var activeTab = getTabFromArray();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "editor/cgtopng",
            type: "POST",
            data: {
                "filename": activeTab.filename,
                "project": activeTab.project,
            }, success: function(result) {
                displayImage(result);
            }, error: function(data) {
                alert(data.responseText);
            }
        });
    });

    $('#arg-checkbox').on('click', function() {
        if ($(this).is(':checked')) {
            $('#arg-div').show();
            $('.arg-value').attr('required', true);
        } else {
            $('#arg-div').hide();
            $('.arg-value').attr('required', false);
        }
    });

    $('#import-project-btn').click(function(event) {
        var projectImportCode = $("#import-code-input").val();
        var projectNameAfterImport = $("#project-name-after-import").val();
        var regEx = new RegExp("^[A-Za-z0-9_-]+$");
        if (!(regEx.test(projectNameAfterImport))) {
            alert("Некорректное имя проекта: возможно использовать только латинские буквы, цифры и следующие символы: _ -");
        } else {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "editor/importProject",
                type: "POST",
                data: {
                    "projectNameAfterImport": projectNameAfterImport,
                    "importCode": projectImportCode,
                }, success: function(result) {
                    $('#project-list').append(result);
                    alert("Проект успешно импортирован");
                }, error: function(data) {
                    alert(data.responseText);
                }
            });
        }
    });
});


function listExportProject() {
    let divListProject = document.getElementById("modal-list-project-body");
    let listProject = document.getElementsByClassName('project-item');
    if (listProject !== undefined) {
        let row;
        let countProjects = listProject.length;
        row = listProject[0].innerHTML + ": " +  listProject[0].getAttribute('data-importcode') + "<br>";
        divListProject.innerHTML = row;
        for(let i=1; i < countProjects; i++) {
            row = listProject[i].innerHTML + ": " +  listProject[i].getAttribute('data-importcode') + "<br>";
            divListProject.innerHTML += row;
        }
    }
}