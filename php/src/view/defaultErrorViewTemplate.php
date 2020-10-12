<div class="container pt-3 vh-100">
    <div class="alert alert-danger" role="alert">
        <h4 class="alert-heading"><?= $this->pageTitle ?></h4>
        <p><?= $this->errorMessage ?></p>
        <?php if (!empty($this->errorStackTrace)): ?>
            <hr/>
            <?php array_map(function ($value) {
                echo "<p>$value</p>";
            }, preg_split('/\n/', $this->errorStackTrace))
            ?>
        <?php endif; ?>
    </div>
</div>
