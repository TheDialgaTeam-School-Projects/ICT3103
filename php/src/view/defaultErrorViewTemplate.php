<div class="container pt-3 vh-100">
    <div class="alert alert-danger" role="alert">
        <h4 class="alert-heading"><?= $this->pageTitle ?></h4>
        <p><?= $this->errorMessage ?></p>
        <?php if (!empty($this->errorStackTrace)): ?>
            <hr/>
            <p><?= $this->errorStackTrace ?></p>
        <?php endif; ?>
    </div>
</div>
