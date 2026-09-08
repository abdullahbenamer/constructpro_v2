<h4>
    Project: <strong><?= strtoupper(htmlspecialchars($project->title)) ?></strong>
</h4>

<br>

<h4>
    <i class="fas fa-folder-open"></i>
    Project Documents
</h4>

<hr>

<a href="<?= URLROOT ?>/projects/uploadDocument/<?= $project->id ?>"
    class="btn btn-primary mb-3">
    <i class="fas fa-upload"></i>
    Upload Documents
</a>

<table class="table table-striped table-hover align-middle">

    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Category</th>
            <th>Document</th>
            <th>Type</th>
            <th>Doc. Version Date</th>
            <th>Size</th>
            <th>upload Date</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>

        <?php if (!empty($documents)): ?>

            <?php foreach ($documents as $doc): ?>

                <?php
                $fileUrl = URLROOT . '/uploads/projects/' .
                    $doc->project_id . '/' .
                    $doc->stored_name;
                ?>

                <tr>

                    <td><?= $doc->id ?></td>

                    <td>
                        <span class="badge bg-secondary">
                            <?= ucfirst($doc->category) ?>
                        </span>
                    </td>

                    <td>

                        <?php if (str_starts_with($doc->file_type, 'image/')): ?>

                            <a href="<?= $fileUrl ?>" target="_blank">

                                <img src="<?= $fileUrl ?>"
                                    style="width:70px;height:70px;object-fit:cover;border-radius:6px;">
                                <?= htmlspecialchars($doc->title) ?>

                            </a>

                        <?php elseif ($doc->file_type === 'application/pdf'): ?>

                            <a href="<?= $fileUrl ?>" target="_blank">
                                <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                <br>
                                <?= htmlspecialchars($doc->title) ?>
                            </a>

                        <?php else: ?>

                            <a href="<?= $fileUrl ?>" target="_blank">
                                <i class="fas fa-file fa-2x text-primary"></i>
                                <br>
                                <?= htmlspecialchars($doc->title) ?>
                            </a>

                        <?php endif; ?>

                    </td>

                    <td>
                        <?php

                        $type = match ($doc->file_type) {

                            'application/pdf' => 'PDF',

                            'application/msword' => 'MS Word',

                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Word',

                            'application/vnd.ms-excel' => 'MS Excel',

                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'Excel',

                            'application/vnd.ms-powerpoint' => 'PowerPoint',

                            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'PowerPoint',

                            'image/jpeg' => 'JPEG Image',

                            'image/png' => 'PNG Image',

                            'image/gif' => 'GIF Image',

                            'image/webp' => 'WEBP Image',

                            'text/plain' => 'Text file',

                            'application/zip' => 'ZIP compressed file',

                            'application/x-rar-compressed' => 'RAR compressed file',

                            default => strtoupper(pathinfo($doc->original_name, PATHINFO_EXTENSION))
                        };

                        echo htmlspecialchars($type);

                        ?>
                    </td>
<td>
                        <?= date('Y-m-d', strtotime($doc->document_date)) ?>
                    </td>
                    <td>
                        <?= number_format($doc->file_size / 1024, 1) ?> KB
                    </td>

                    <td>
                        <?= date('Y-m-d', strtotime($doc->uploaded_at)) ?>
                    </td>

                    <td>

                        <a href="<?= $fileUrl ?>"
                            target="_blank"
                            class="btn btn-sm btn-primary">

                            View

                        </a>

                        <a href="<?= $fileUrl ?>"
                            download
                            class="btn btn-sm btn-success">

                            Download

                        </a>

                        <a href="<?= URLROOT ?>/projects/deleteDocument/<?= $doc->id ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Delete this document?')">

                            Delete

                        </a>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>

                <td colspan="7" class="text-center text-muted py-4">

                    <i class="fas fa-folder-open fa-2x"></i>

                    <br><br>

                    No documents uploaded yet.

                </td>

            </tr>

        <?php endif; ?>

    </tbody>

</table>