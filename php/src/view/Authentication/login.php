<div class="login-container">
    <form class="login-form p-3 border border-dark rounded" method="post" action="<?= $this->getRouteUri('Login') ?>">
        <i class="fas fa-piggy-bank mb-2" style="width: 72px; height: 72px"></i>
        <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
        <?php if (!empty($this->errorMessage)): ?>
            <div class="alert alert-danger" role="alert"><?= $this->errorMessage ?></div>
        <?php endif; ?>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Password"
                   required>
        </div>
        <input type="hidden" id="csrfToken" name="csrfToken" value="<?= $this->csrfToken ?>"/>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        <a class="btn btn-lg btn-primary btn-block" href="<?= $this->getRouteUri('Register') ?>" role="button">Register</a>
    </form>
</div>
