<?php
include "includes/header.php";

if (!isset($_GET['email']) || !isset($_GET['token'])) {
    redirect('index');
    exit;
}

$reset = new PasswordReset($_GET['email'], $_GET['token']);

if (!$reset->validateToken()) {
    redirect('index');
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$success, $message] = $reset->resetPassword($_POST['password'] ?? '', $_POST['confirmPassword'] ?? '');
    if ($success) {
        redirect('./login.php');
        exit;
    }
}
?>

<?php include "includes/navigation.php"; ?>

<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-body text-center">
                    <h3><i class="fa fa-lock fa-4x"></i></h3>
                    <h2 class="text-center">Reset Password</h2>
                    <p>You can reset your password here.</p>

                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?= strpos($message, 'successful') !== false ? 'success' : 'danger' ?>">
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" autocomplete="off">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-user color-blue"></i></span>
                                <input name="password" placeholder="Enter password" class="form-control" type="password">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-ok color-blue"></i></span>
                                <input name="confirmPassword" placeholder="Confirm password" class="form-control" type="password">
                            </div>
                        </div>

                        <div class="form-group">
                            <input class="btn btn-lg btn-primary btn-block" value="Reset Password" type="submit">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<hr>
<?php include "includes/footer.php"; ?>