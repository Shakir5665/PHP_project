<?php
require_once __DIR__ . '/config.php';

function e($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
$page = $_GET['page'] ?? 'dashboard';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'add_meeting') {
            $stmt = $pdo->prepare("INSERT INTO Meeting (MeetingId, MeetingName, MeetingDate, MeetingTime, MeetingVenue, Recording, HOD_user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                trim($_POST['MeetingId']), trim($_POST['MeetingName']), $_POST['MeetingDate'],
                $_POST['MeetingTime'], trim($_POST['MeetingVenue']),
                trim($_POST['Recording']) !== '' ? trim($_POST['Recording']) : null,
                $_POST['HOD_user_id']
            ]);
            $message = 'Meeting added successfully.';
            $page = 'meetings';
        } elseif ($action === 'add_decision') {
            $stmt = $pdo->prepare("INSERT INTO Meeting_Decision (MeetingId, decision) VALUES (?, ?)");
            $stmt->execute([$_POST['MeetingId'], trim($_POST['decision'])]);
            $message = 'Decision saved successfully.';
            $page = 'decisions';
        } elseif ($action === 'update_confirmation') {
            $stmt = $pdo->prepare("REPLACE INTO Confirm (UserId, MeetingId, Status) VALUES (?, ?, ?)");
            $stmt->execute([$_POST['UserId'], $_POST['MeetingId'], $_POST['Status']]);
            $message = 'Attendance confirmation updated.';
            $page = 'attendance';
        } elseif ($action === 'add_agenda') {
            $stmt = $pdo->prepare("INSERT INTO Agenda (AgendaId, AgendaTitle, Document, HOD_user_id, MeetingId) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([trim($_POST['AgendaId']), trim($_POST['AgendaTitle']), trim($_POST['Document']), $_POST['HOD_user_id'], $_POST['MeetingId']]);
            $message = 'Agenda added successfully.';
            $page = 'agenda';
        } elseif ($action === 'add_participation') {
            $stmt = $pdo->prepare("INSERT IGNORE INTO Participate (UserId, MeetingId) VALUES (?, ?)");
            $stmt->execute([$_POST['UserId'], $_POST['MeetingId']]);
            $message = 'Participation record saved.';
            $page = 'attendance';
        }
    } catch (PDOException $ex) {
        $error = 'Could not save the record. Check that the ID is unique and the selected user/meeting exists.';
    }
}

function rows($pdo, $sql, $params = []) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
function scalar($pdo, $sql) { return (int)$pdo->query($sql)->fetchColumn(); }

$totalMeetings = scalar($pdo, "SELECT COUNT(*) FROM Meeting");
$totalUsers = scalar($pdo, "SELECT COUNT(*) FROM `User`");
$pending = scalar($pdo, "SELECT COUNT(*) FROM Confirm WHERE Status = 'Pending'");
$upcoming = scalar($pdo, "SELECT COUNT(*) FROM Meeting WHERE MeetingDate >= CURDATE()");
$hods = rows($pdo, "SELECT h.UserId, u.Name FROM HOD h JOIN `User` u ON u.UserId = h.UserId ORDER BY u.Name");
$meetings = rows($pdo, "SELECT m.*, u.Name AS HODName FROM Meeting m LEFT JOIN HOD h ON h.UserId=m.HOD_user_id LEFT JOIN `User` u ON u.UserId=h.UserId ORDER BY m.MeetingDate, m.MeetingTime");
try {
    $users = rows($pdo, "SELECT UserId, Role, Name, ContactNumber, Email FROM `User` ORDER BY Name");
} catch (PDOException $ex) {
    $users = rows($pdo, "SELECT UserId, Role, Name, ContactNumber, NULL AS Email FROM `User` ORDER BY Name");
}
$agenda = rows($pdo, "SELECT a.*, m.MeetingName FROM Agenda a JOIN Meeting m ON m.MeetingId=a.MeetingId ORDER BY a.AgendaId");
$decisions = rows($pdo, "SELECT d.MeetingId, d.decision, m.MeetingName FROM Meeting_Decision d JOIN Meeting m ON m.MeetingId=d.MeetingId ORDER BY d.MeetingId");
$attendance = rows($pdo, "SELECT c.UserId, u.Name, c.MeetingId, m.MeetingName, c.Status FROM Confirm c JOIN `User` u ON u.UserId=c.UserId JOIN Meeting m ON m.MeetingId=c.MeetingId ORDER BY m.MeetingDate, u.Name");
$participation = rows($pdo, "SELECT p.UserId, u.Name, p.MeetingId, m.MeetingName FROM Participate p JOIN `User` u ON u.UserId=p.UserId JOIN Meeting m ON m.MeetingId=p.MeetingId ORDER BY p.MeetingId, u.Name");

function active($current, $target) { return $current === $target ? 'active' : ''; }
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Department Communication Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand"><div class="brand-mark">DC</div><div><strong>Department</strong><span>Communication System</span></div></div>
        <div class="nav-label">MENU</div>
        <nav>
            <a class="<?=active($page,'dashboard')?>" href="index.php"><span>▦</span> Dashboard</a>
            <a class="<?=active($page,'meetings')?>" href="index.php?page=meetings"><span>▣</span> Meetings</a>
            <a class="<?=active($page,'users')?>" href="index.php?page=users"><span>♙</span> Users</a>
            <a class="<?=active($page,'agenda')?>" href="index.php?page=agenda"><span>▤</span> Agenda</a>
            <a class="<?=active($page,'decisions')?>" href="index.php?page=decisions"><span>✓</span> Decisions</a>
            <a class="<?=active($page,'attendance')?>" href="index.php?page=attendance"><span>◷</span> Attendance</a>
        </nav>
        <div class="sidebar-bottom"><div class="mini-avatar">H</div><div><strong>Department Office</strong><small>Meeting management</small></div></div>
    </aside>
    <main class="main">
        <header class="topbar"><div><span class="eyebrow">DEPARTMENT OFFICE</span><h1><?=e([
            'dashboard'=>'Dashboard','meetings'=>'Meetings','users'=>'Users','agenda'=>'Meeting Agenda',
            'decisions'=>'Meeting Decisions','attendance'=>'Attendance & Participation'
        ][$page] ?? 'Dashboard')?></h1></div><div class="top-date"><?=date('D, d M Y')?></div></header>
        <?php if ($message): ?><div class="alert success"><?=e($message)?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert error"><?=e($error)?></div><?php endif; ?>

        <?php if ($page === 'dashboard'): ?>
            <section class="welcome"><div><p class="eyebrow light">WELCOME</p><h2>Department Meeting Overview</h2><p>Manage schedules, agendas, decisions and attendance in one place.</p></div><div class="welcome-icon">▦</div></section>
            <section class="stats">
                <article class="stat-card"><div class="stat-icon blue">▣</div><div><p>Total Meetings</p><strong><?=$totalMeetings?></strong><small>All scheduled meetings</small></div></article>
                <article class="stat-card"><div class="stat-icon purple">♙</div><div><p>Registered Users</p><strong><?=$totalUsers?></strong><small>Lecturers and representatives</small></div></article>
                <article class="stat-card"><div class="stat-icon green">◷</div><div><p>Upcoming Meetings</p><strong><?=$upcoming?></strong><small>Today and future</small></div></article>
                <article class="stat-card"><div class="stat-icon orange">!</div><div><p>Pending Confirmations</p><strong><?=$pending?></strong><small>Awaiting a response</small></div></article>
            </section>
            <section class="panel"><div class="panel-head"><div><h3>Meeting Schedule</h3><p>Your upcoming and previous department meetings</p></div><a class="button" href="index.php?page=meetings">View meetings →</a></div>
                <?php if (!$meetings): ?><div class="empty">No meetings found. Add your first meeting from the Meetings page.</div><?php else: ?>
                <div class="table-wrap"><table><thead><tr><th>MEETING</th><th>DATE & TIME</th><th>VENUE</th><th>HOD</th></tr></thead><tbody>
                <?php foreach (array_slice($meetings,0,6) as $m): ?><tr><td><strong><?=e($m['MeetingName'])?></strong><small class="subcell"><?=e($m['MeetingId'])?></small></td><td><?=e($m['MeetingDate'])?><small class="subcell"><?=e(substr($m['MeetingTime'],0,5))?></small></td><td><?=e($m['MeetingVenue'])?></td><td><?=e($m['HODName'] ?? $m['HOD_user_id'])?></td></tr><?php endforeach; ?>
                </tbody></table></div><?php endif; ?>
            </section>
            <p class="footnote">Simple project interface · Connected to <?=e($db)?></p>

        <?php elseif ($page === 'meetings'): ?>
            <section class="panel"><div class="panel-head"><div><h3>All Meetings</h3><p>View and add department meetings.</p></div></div>
                <form method="post" class="form-grid">
                    <input type="hidden" name="action" value="add_meeting">
                    <label>Meeting ID<input name="MeetingId" placeholder="M004" required></label>
                    <label>Meeting name<input name="MeetingName" placeholder="Staff Meeting" required></label>
                    <label>Date<input type="date" name="MeetingDate" required></label>
                    <label>Time<input type="time" name="MeetingTime" required></label>
                    <label>Venue<input name="MeetingVenue" placeholder="Hall A" required></label>
                    <label>HOD<select name="HOD_user_id" required><option value="">Select HOD</option><?php foreach($hods as $h): ?><option value="<?=e($h['UserId'])?>"><?=e($h['Name'])?> (<?=e($h['UserId'])?>)</option><?php endforeach; ?></select></label>
                    <label class="full">Recording filename or reference (optional)<input name="Recording" placeholder="meeting-recording.mp4"></label>
                    <div class="full"><button class="button" type="submit">+ Add Meeting</button></div>
                </form>
            </section>
            <section class="panel"><div class="panel-head"><div><h3>Meeting Schedule</h3><p><?=count($meetings)?> records</p></div></div><div class="table-wrap"><table><thead><tr><th>ID</th><th>Meeting</th><th>Date</th><th>Time</th><th>Venue</th><th>HOD</th></tr></thead><tbody>
            <?php foreach($meetings as $m): ?><tr><td><?=e($m['MeetingId'])?></td><td><strong><?=e($m['MeetingName'])?></strong></td><td><?=e($m['MeetingDate'])?></td><td><?=e(substr($m['MeetingTime'],0,5))?></td><td><?=e($m['MeetingVenue'])?></td><td><?=e($m['HODName'] ?? $m['HOD_user_id'])?></td></tr><?php endforeach; ?>
            </tbody></table></div></section>

        <?php elseif ($page === 'users'): ?>
            <section class="panel"><div class="panel-head"><div><h3>Registered Users</h3><p>User details from the User table.</p></div><span class="count-pill"><?=count($users)?> users</span></div>
            <div class="table-wrap"><table><thead><tr><th>USER ID</th><th>NAME</th><th>ROLE</th><th>CONTACT</th><th>EMAIL</th></tr></thead><tbody>
            <?php foreach($users as $u): ?><tr><td><?=e($u['UserId'])?></td><td><strong><?=e($u['Name'])?></strong></td><td><span class="role-pill"><?=e($u['Role'])?></span></td><td><?=e($u['ContactNumber'])?></td><td><?=e($u['Email'] ?? '—')?></td></tr><?php endforeach; ?>
            </tbody></table></div></section>

        <?php elseif ($page === 'agenda'): ?>
            <section class="panel"><div class="panel-head"><div><h3>Add Agenda</h3><p>Link an agenda document to a meeting.</p></div></div>
                <form method="post" class="form-grid"><input type="hidden" name="action" value="add_agenda">
                    <label>Agenda ID<input name="AgendaId" placeholder="A004" required></label>
                    <label>Agenda title<input name="AgendaTitle" placeholder="Staff Development" required></label>
                    <label>Document filename<input name="Document" placeholder="agenda.pdf" required></label>
                    <label>Meeting<select name="MeetingId" required><option value="">Select meeting</option><?php foreach($meetings as $m): ?><option value="<?=e($m['MeetingId'])?>"><?=e($m['MeetingId'].' - '.$m['MeetingName'])?></option><?php endforeach; ?></select></label>
                    <label class="full">HOD<select name="HOD_user_id" required><option value="">Select HOD</option><?php foreach($hods as $h): ?><option value="<?=e($h['UserId'])?>"><?=e($h['Name'])?></option><?php endforeach; ?></select></label>
                    <div class="full"><button class="button" type="submit">+ Add Agenda</button></div>
                </form>
            </section>
            <section class="panel"><div class="panel-head"><div><h3>Agenda Documents</h3><p>Saved agenda records</p></div></div><div class="table-wrap"><table><thead><tr><th>ID</th><th>AGENDA TITLE</th><th>DOCUMENT</th><th>MEETING</th></tr></thead><tbody>
            <?php foreach($agenda as $a): ?><tr><td><?=e($a['AgendaId'])?></td><td><strong><?=e($a['AgendaTitle'])?></strong></td><td><?=e($a['Document'])?></td><td><?=e($a['MeetingName'])?></td></tr><?php endforeach; ?>
            </tbody></table></div></section>

        <?php elseif ($page === 'decisions'): ?>
            <section class="panel"><div class="panel-head"><div><h3>Record a Decision</h3><p>Store a decision under the relevant meeting.</p></div></div>
                <form method="post" class="form-grid"><input type="hidden" name="action" value="add_decision">
                    <label class="full">Meeting<select name="MeetingId" required><option value="">Select meeting</option><?php foreach($meetings as $m): ?><option value="<?=e($m['MeetingId'])?>"><?=e($m['MeetingId'].' - '.$m['MeetingName'])?></option><?php endforeach; ?></select></label>
                    <label class="full">Decision<textarea name="decision" rows="3" placeholder="Enter the decision made..." required></textarea></label>
                    <div class="full"><button class="button" type="submit">Save Decision</button></div>
                </form>
            </section>
            <section class="panel"><div class="panel-head"><div><h3>Meeting Decisions</h3><p>Decisions already recorded in the database.</p></div></div><div class="table-wrap"><table><thead><tr><th>MEETING ID</th><th>MEETING</th><th>DECISION</th></tr></thead><tbody>
            <?php foreach($decisions as $d): ?><tr><td><?=e($d['MeetingId'])?></td><td><?=e($d['MeetingName'])?></td><td><?=e($d['decision'])?></td></tr><?php endforeach; ?>
            </tbody></table></div></section>

        <?php elseif ($page === 'attendance'): ?>
            <section class="panel"><div class="panel-head"><div><h3>Update Confirmation</h3><p>Set a user's attendance confirmation for a meeting.</p></div></div>
                <form method="post" class="form-grid"><input type="hidden" name="action" value="update_confirmation">
                    <label>User<select name="UserId" required><option value="">Select user</option><?php foreach($users as $u): ?><option value="<?=e($u['UserId'])?>"><?=e($u['Name'].' ('.$u['UserId'].')')?></option><?php endforeach; ?></select></label>
                    <label>Meeting<select name="MeetingId" required><option value="">Select meeting</option><?php foreach($meetings as $m): ?><option value="<?=e($m['MeetingId'])?>"><?=e($m['MeetingName'].' ('.$m['MeetingId'].')')?></option><?php endforeach; ?></select></label>
                    <label>Status<select name="Status" required><option>Pending</option><option>Confirmed</option></select></label>
                    <div class="full"><button class="button" type="submit">Update Confirmation</button></div>
                </form>
            </section>
            <section class="panel"><div class="panel-head"><div><h3>Attendance Confirmations</h3><p>Current confirmation status</p></div></div><div class="table-wrap"><table><thead><tr><th>USER</th><th>MEETING</th><th>STATUS</th></tr></thead><tbody>
            <?php foreach($attendance as $a): ?><tr><td><strong><?=e($a['Name'])?></strong><small class="subcell"><?=e($a['UserId'])?></small></td><td><?=e($a['MeetingName'])?><small class="subcell"><?=e($a['MeetingId'])?></small></td><td><span class="status <?=strtolower(e($a['Status']))?>"><?=e($a['Status'])?></span></td></tr><?php endforeach; ?>
            </tbody></table></div></section>
            <section class="panel"><div class="panel-head"><div><h3>Record Participation</h3><p>Mark a user as having participated in a meeting.</p></div></div>
                <form method="post" class="form-grid"><input type="hidden" name="action" value="add_participation">
                    <label>User<select name="UserId" required><option value="">Select user</option><?php foreach($users as $u): ?><option value="<?=e($u['UserId'])?>"><?=e($u['Name'].' ('.$u['UserId'].')')?></option><?php endforeach; ?></select></label>
                    <label>Meeting<select name="MeetingId" required><option value="">Select meeting</option><?php foreach($meetings as $m): ?><option value="<?=e($m['MeetingId'])?>"><?=e($m['MeetingName'].' ('.$m['MeetingId'].')')?></option><?php endforeach; ?></select></label>
                    <div class="full"><button class="button" type="submit">Save Participation</button></div>
                </form>
            </section>
            <section class="panel"><div class="panel-head"><div><h3>Participation Records</h3><p>Users recorded as participants</p></div></div><div class="table-wrap"><table><thead><tr><th>USER</th><th>MEETING</th></tr></thead><tbody>
            <?php foreach($participation as $p): ?><tr><td><?=e($p['Name'])?> <small class="subcell"><?=e($p['UserId'])?></small></td><td><?=e($p['MeetingName'])?> <small class="subcell"><?=e($p['MeetingId'])?></small></td></tr><?php endforeach; ?>
            </tbody></table></div></section>
        <?php else: ?>
            <section class="panel"><h3>Page not found</h3><p>Please choose a page from the sidebar.</p></section>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
