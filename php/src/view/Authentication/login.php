<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="p-3 bg-white border border-dark rounded">
        <?php if (!empty($this->errorMessage)): ?>
            <div class="alert alert-danger" role="alert"><?= $this->errorMessage ?></div>
        <?php endif; ?>
        <form method="post" action="<?= $this->getRouteUri('Login') ?>">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
            <input type="hidden" id="csrfToken" name="csrfToken" value="<?= $this->getCsrfToken() ?>"/>
            <a class="btn btn-primary" href="<?= $this->getRouteUri('Register') ?>" role="button">Register</a>
        </form>
    </div>
</div>
