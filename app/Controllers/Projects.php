<?php

class Projects extends Controller
{

    public function index()
    {
        $model = $this->model('Project');

        $data['projects'] = $model->getAll();

        $data['showArchived'] = false;

        $this->view('projects/index', $data);
    }


    public function create() // crete a new Project
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = [
                'customer_id'      => (int)$_POST['customer_id'],
                'title'            => trim($_POST['title']),
                'project_type'     => $_POST['project_type'],
                'scopes'           => $_POST['scopes'] ?? [],
                'description'      => trim($_POST['description']),
                'site_location'    => trim($_POST['site_location']),
                'start_date'       => $_POST['start_date'] ?: null,
                'deadline'         => $_POST['deadline'] ?: null,
                'project_manager_id' => !empty($_POST['project_manager_id'])
                    ? (int)$_POST['project_manager_id']
                    : null,
                'contract_number'  => trim($_POST['contract_number']),
                'project_code'     => trim($_POST['project_code']),
                'priority'         => $_POST['priority'],
                'status'           => $_POST['status'],
                'budget'           => (float)$_POST['budget']
            ];
            $projectModel = $this->model('Project');

            try {

                $projectId = $projectModel->create($data);

                if ($projectId) {
                    FlashHelper::success('Project created successfully.');
                    header('Location: ' . URLROOT . '/projects');
                    exit;
                }
            } catch (Throwable $e) {

                FlashHelper::error(
                    $e->getMessage()
                );
            }

            FlashHelper::error('Unable to create project.');
        }

        $data['customers'] = $this->model('Customer')->getAll();
        $data['users'] = $this->model('User')->getProjectManagers(); // display only users with management roles.

        $this->view('projects/create', $data);
    }

    public function edit($id)
    {
        $model = $this->model('Project');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = [
                'customer_id'        => (int)$_POST['customer_id'],
                'title'              => trim($_POST['title']),
                'project_type'       => $_POST['project_type'],
                'scopes'             => $_POST['scopes'] ?? [],
                'description'        => trim($_POST['description']),
                'site_location'      => trim($_POST['site_location']),
                'start_date'         => $_POST['start_date'] ?: null,
                'deadline'           => $_POST['deadline'] ?: null,
                'project_manager_id' => !empty($_POST['project_manager_id'])
                    ? (int)$_POST['project_manager_id']
                    : null,
                'contract_number'    => trim($_POST['contract_number']),
                'priority'           => $_POST['priority'],
                'status'             => $_POST['status'],
                'budget'             => (float)$_POST['budget']
            ];

            if ($model->update($id, $data)) {
                FlashHelper::success('Project updated successfully.');
                header('Location: ' . URLROOT . '/projects');
                exit;
            }

            FlashHelper::error('Unable to update project.');
        }

        $data['project'] = $model->getById($id);
        $data['project_scopes'] = $model->getProjectScopes($id);
        $data['customers'] = $this->model('Customer')->getAll();
        $data['users'] = $this->model('User')->getProjectManagers();

        $this->view('projects/edit', $data);
    }

    // DELETE if Cost is 0 
    public function delete($id)
    {
        $projectModel = $this->model('Project');
        $costModel = $this->model('ProjectCost');

        $project =
            $projectModel->getById($id);

        if (!$project) {

            $_SESSION['error'] =
                'Project not found';

            header('Location: ' . URLROOT . '/projects');
            exit;
        }

        $costs =
            $costModel->getProjectCosts($id);

        if (!empty($costs)) {

            $_SESSION['error'] =
                'Cannot delete project. Remove all project costs first.';

            header('Location: ' . URLROOT . '/projects');
            exit;
        }

        $projectModel->delete($id);

        $_SESSION['success'] =
            'Project deleted successfully';

        header('Location: ' . URLROOT . '/projects');
        exit;
    }

    public function archive($id)
    {
        $projectModel = $this->model('Project');

        if ($projectModel->archive($id)) {

            $_SESSION['success'] =
                'Project archived successfully';
        } else {

            $_SESSION['error'] =
                'Archive failed';
        }

        header('Location: ' . URLROOT . '/projects');
        exit;
    }


    public function restore($id)
    {
        $model = $this->model('Project');

        $model->restore($id);

        FlashHelper::success('Project restored successfully.');

        header('Location: ' . URLROOT . '/projects');
        exit;
    }


    public function ledger($project_id)
    {
        AuthHelper::can('projects.view');

        $projectModel = $this->model('Project');
        $ledgerModel  = $this->model('ProjectLedger');

        $project = $projectModel->getById($project_id);

        if (!$project) {

            $_SESSION['error'] = "Project not found";

            header(
                "Location: " . URLROOT . "/projects"
            );

            exit;
        }

        /*
    |--------------------------------------------------------------------------
    | GET ALL ENTRIES FROM project_ledger
    |--------------------------------------------------------------------------
    */

        $ledger = $ledgerModel->getLedger(
            $project_id
        );

        /*
    |--------------------------------------------------------------------------
    | GET FINAL SUMMARY
    |--------------------------------------------------------------------------
    */

        $summary = $ledgerModel->getProjectSummary(
            $project_id
        );

        $data = [

            'project' => $project,

            'ledger' => $ledger,

            'balance' => $summary->balance,

            'summary' => $summary
        ];

        $this->view(
            'project-costs/ledger',
            $data
        );
    }

    // Show archived projects
    public function archived()
    {
        $model = $this->model('Project');

        $data['projects'] = $model->getArchivedProjects();

        $data['showArchived'] = true;

        $this->view('projects/index', $data);
    }

    // Project Documnets
    public function documents($project_id)
    {
        $projectModel = $this->model('Project');
        $documentModel = $this->model('ProjectDocument');

        $project = $projectModel->getById($project_id);

        if (!$project) {

            FlashHelper::error("Project not found");

            header("Location: " . URLROOT . "/projects");

            exit;
        }

        $data = [

            'project' => $project,

            'documents' => $documentModel->getByProject($project_id)

        ];

        $this->view("projects/documents", $data);
    }


    public function uploadDocument($project_id)
    {
        $projectModel = $this->model('Project');

        $project = $projectModel->getById($project_id);

        if (!$project) {

            FlashHelper::error("Project not found");

            header("Location: " . URLROOT . "/projects");

            exit;
        }

        // =========================
        // POST HANDLER
        // =========================
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $folder = PUBLICROOT . '/uploads/projects/' . $project_id;

            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }

            $documentModel = $this->model('ProjectDocumentModel');

            $files = $_FILES['documents'];

            $uploadedCount = 0;

            for ($i = 0; $i < count($files['name']); $i++) {

                if ($files['error'][$i] !== 0) {
                    continue;
                }

                $originalName = $files['name'][$i];
                $tmpName      = $files['tmp_name'][$i];
                $size         = $files['size'][$i];
                $type         = $files['type'][$i];

                // unique file name
                $storedName = uniqid() . '_' . $originalName;

                $destination = $folder . '/' . $storedName;

                if (move_uploaded_file($tmpName, $destination)) {

                    $documentModel->create([
                        'project_id'     => $project_id,
                        'category'       => $_POST['category'] ?? 'other',
                        'title'          => $_POST['title'] ?? $originalName,
                        'description'    => $_POST['description'] ?? '',
                        'document_date'  => $_POST['document_date'] ?: null,
                        'original_name'  => $originalName,
                        'stored_name'    => $storedName,
                        'file_type'      => $type,
                        'file_size'      => $size,
                        'uploaded_by'    => $_SESSION['user_id'],
                    ]);

                    $uploadedCount++;
                }
            }

            FlashHelper::success("Uploaded {$uploadedCount} file(s) successfully");

            header("Location: " . URLROOT . "/projects/documents/" . $project_id);
            exit;
        }

        // =========================
        // LOAD VIEW
        // =========================
        $this->view('projects/upload_document', [
            'project' => $project
        ]);
    }

    public function deleteDocument($id)
    {
        $documentModel = $this->model('ProjectDocumentModel');

        $doc = $documentModel->getById($id);

        if (!$doc) {
            FlashHelper::error('Document not found.');
            header('Location: ' . URLROOT . '/projects');
            exit;
        }

        $file = PUBLICROOT . '/uploads/projects/' .
            $doc->project_id . '/' .
            $doc->stored_name;

        if (file_exists($file)) {
            unlink($file);
        }

        $documentModel->delete($id);

        FlashHelper::success('Document deleted.');

        header('Location: ' . URLROOT . '/projects/documents/' . $doc->project_id);
        exit;
    }
}
