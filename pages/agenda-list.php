<?php 
/*
function getMeetingSummaries(): array
{
   
    $meetings = [];
    foreach ($pdo->query('SELECT * FROM tiab_meetings ORDER BY meeting_date DESC, created_at DESC') as $meeting) {
        $meetings[$meeting['id']] = $meeting;
    }
    // Load only schedule inputs in batches; avoid full catalogue/officer queries per meeting.
    foreach (['prepared_speakers', 'tt_speakers', 'evaluators'] as $kind) {
        foreach ($pdo->query('SELECT meeting_id, duration FROM tiab_' . $kind) as $row) {
            if (isset($meetings[$row['meeting_id']])) $meetings[$row['meeting_id']][$kind][] = ['duration' => $row['duration']];
        }
    }
    foreach ($pdo->query('SELECT meeting_id, item_key, duration FROM tiab_agenda_durations') as $row) {
        if (isset($meetings[$row['meeting_id']])) $meetings[$row['meeting_id']]['agenda_durations'][$row['item_key']] = $row['duration'];
    }
    return array_values($meetings);
}
    */

$meetings = $objectMeetings->selectAll();
?>

<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Meeting Agendas</h2>
        <a href="<?php echo AGENDA_BUILDER?>form.php" class="btn btn-primary">Create New Agenda</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (empty($meetings)): ?>
        <div class="alert alert-info">No agendas yet. Create your first one!</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover border">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Meeting Number</th>
                        <th>Theme</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($meetings as $index => $meeting): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($meeting->meeting_number) ?></td>
                            <td><?= htmlspecialchars($meeting->theme) ?></td>
                            <td><?= date('D, d M Y', strtotime($meeting->meeting_date)) ?></td>
                            <td>
                                <a target="_blank" href="<?php echo AGENDA_BUILDER?>view.php?id=<?= (int)$meeting->id ?>" class="btn btn-sm btn-info text-white" title="View">View</a>
                                <a target="_blank" href="<?php echo AGENDA_BUILDER?>generate_pdf.php?id=<?= (int)$meeting->id ?>" class="btn btn-sm btn-success">Download PDF</a>
                                <a target="_blank" href="<?php echo AGENDA_BUILDER?>form.php?id=<?= (int)$meeting->id ?>" class="btn btn-sm btn-warning" title="Edit">Edit</a>
                                
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>