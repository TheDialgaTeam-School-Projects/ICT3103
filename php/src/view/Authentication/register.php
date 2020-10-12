<div class="container d-flex justify-content-center align-items-center register-container">
    <div class="my-3 p-3 bg-white border border-dark rounded">
        <?php if (!empty($this->errorMessage)): ?>
            <div class="alert alert-danger" role="alert"><?= $this->errorMessage ?></div>
        <?php endif; ?>
        <p>Register for iBanking account:</p>
        <form method="post" action="<?= $this->getRouteUri('Register') ?>">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="firstname">First Name</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" maxlength="255" required/>
                </div>
                <div class="form-group col-md-6">
                    <label for="lastname">Last Name</label>
                    <input type="text" class="form-control" id="lastname" name="lastname" maxlength="255" required/>
                </div>
            </div>
            <div class="form-group">
                <label for="mobile">Mobile Number</label>
                <input type="tel" class="form-control" id="mobile" name="mobile" required/>
            </div>
            <div class="form-group">
                <label for="dob">Date Of Birth</label>
                <input type="date" class="form-control" id="dob" name="dob" required/>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username"
                       aria-describedby="usernameHelpBlock" minlength="3" maxlength="255" required/>
                <small id="usernameHelpBlock" class="form-text text-muted">
                    Your username must be 3-255 characters long, and must not contain spaces, special characters, or
                    emoji.
                </small>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password"
                       aria-describedby="passwordHelpBlock" minlength="8" required>
                <small id="passwordHelpBlock" class="form-text text-muted">
                    Your password must be at least 8 characters long, contains at least one capital letter, one number,
                    and one special characters.
                </small>
            </div>
            <a class="btn btn-primary" href="<?= $this->getRouteUri('Login') ?>" role="button"><i
                        class="fas fa-arrow-left"></i> Back</a>
            <button type="submit" class="btn btn-primary">Register</button>
            <input type="hidden" id="csrfToken" name="csrfToken" value="<?= $this->csrfToken ?>"/>
        </form>
    </div>
</div>
