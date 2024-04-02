<!-- Are you sure? modal -->

<div class="modal fade" id="areyousureModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="areyousureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Biztosan törölni szeretnéd?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelButton">Mégse</button>
                <button type="button" class="btn btn-danger" id="deleteTaskSure">Törlés</button>
            </div>
        </div>
    </div>
</div>



<!-- Project settings modal -->

<div class="modal fade" id="projectSettingsModal" tabindex="-1" aria-labelledby="projectSettingsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="projectSettingsModalLabel">Projekt beállítások</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="projectName" class="col-form-label">Projekt neve:</label>
                        <input type="text" class="form-control" id="projectName">
                    </div>
                    <div class="mb-3">
                        <label for="projectVisibility" class="col-form-label">Projekt láthatósága:</label>
                        <select class="form-select" id="projectVisibility">
                            <option value="0">Mindenki</option>
                            <option value="1">Médiás</option>
                            <option value="2">Stúdiós</option>
                            <option value="3">Admin</option>
                            <option value="4">Hozzáadott emberek</option>
                        </select>
                    </div>
                    <div class="mb-3 input-group">
                        <span class="input-group-text">Projekt határideje: </span>
                        <input type="date" class="form-control" id="projectDate">
                        <input type="time" class="form-control" id="projectTime">
                    </div>
                    <div class="mb-3 input-group">
                        <span class="input-group-text">Projekt törlése: </span>
                        <input type="text" class="form-control" id="deleteText">
                        <button type="button" class="btn btn-outline-danger" id="deleteButton">Törlés</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" id="archiveButton">Befejezés (archiválás)</button>
                <button type="button" class="btn btn-success" id="saveButton">Mentés</button>
            </div>
        </div>
    </div>
</div>


<!-- Description modal -->
<div class="modal fade" id="projectDescModal" tabindex="-1" aria-labelledby="projectDescModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Leírás</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <textarea class="form-control" id="projectDescription"></textarea>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="saveDescButton">Mentés</button>
            </div>
        </div>
    </div>
</div>

<!-- Task modal -->
<div class="modal fade" id="taskEditorModal" tabindex="-1" aria-labelledby="taskEditorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskTitle">Új feladat hozzáadása</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding-top: 0;">
                <form>
                    <div class="mb-1" id="taskName">
                        <label for="textTaskName" class="col-form-label">Feladat neve:</label>
                        <input type="text" class="form-control" id="textTaskName">
                    </div>
                    <div class="mb-1" id="taskData">
                    </div>
                    <div class="mb-3">
                        <label for="taskMembers" class="col-form-label">Felelősök:</label>
                        <div class="memberSelect" id="taskMembers">
                        </div>
                    </div>
                    <div class="mb-3 input-group">
                        <span class="input-group-text">Feladat határideje: </span>
                        <input type="date" class="form-control" id="taskDate" placeholder="Nap">
                        <input type="time" class="form-control" id="taskTime" placeholder="Időpont">
                    </div>
                    <div class="mb-2">
                        <button type="button" class="btn" data-bs-toggle="button" id="taskSubmittable">Elvégzendő
                            feladat</button>
                        <button type="button" class="btn" data-bs-toggle="button" id="singleAnswer" disabled>Csak
                            egyszeri leadás
                        </button>
                        <script>
                            let taskSubmittable = document.getElementById('taskSubmittable');

                            let observer = new MutationObserver(function (mutations) {
                                mutations.forEach(function (mutation) {
                                    if (mutation.attributeName === "class") {
                                        if (taskSubmittable.classList.contains('active')) {
                                            document.getElementById("singleAnswer").disabled = false;
                                            document.getElementById("fillOutText").disabled = false;
                                        } else {
                                            document.getElementById("fillOutText").disabled = true;
                                            document.getElementById("singleAnswer").disabled = true;
                                            document.getElementById("singleAnswer").classList.remove('active');
                                        }
                                    }
                                });
                            });

                            observer.observe(taskSubmittable, {
                                attributes: true //configure it to listen to attribute changes
                            });

                        </script>
                    </div>
                    <input type="text" class="form-control" id="fillOutText" placeholder="Kitöltés szövege...">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" data-bs-target="#areyousureModal"
                    data-bs-toggle="modal" id="deleteTask" style="display: none;">Törlés</button>
                <button type="button" class="btn btn-success" id="saveNewTask">Mentés</button>
            </div>
        </div>
    </div>
</div>

<!-- UI modal -->
<div class="modal fade" id="taskFillModal" tabindex="-1" aria-labelledby="taskFillModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskFillTitle">Feladat kitöltése</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3" id="taskFillData">
                    </div>
                </form>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <div id="taskFillDeadline">
                </div>
                <button type="button" class="btn btn-success" id="submitAnswer">Mentés</button>
            </div>
        </div>
    </div>
</div>

<!-- New member modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tagok szerkesztése</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="projectMembers" class="col-form-label">Projekt tagjai:</label>
                        <div class="memberSelect" id="projectMembersSelect"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bezárás</button>
                <button type="button" class="btn btn-success" id="saveProjectMembers">Mentés</button>
            </div>
        </div>
    </div>
</div>


<!-- Expand image modal -->
<div class="modal modal-xl fade" id="expandImageModal" tabindex="-1" aria-labelledby="expandImageModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <img src="" alt="" id="expandedImage" style="width: 100%;">
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" id="imgDownloadButton"><i class="fas fa-download fa-lg"></i></button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bezárás</button>
            </div>
        </div>
    </div>
</div>