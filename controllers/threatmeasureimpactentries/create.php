<?php Block::put('breadcrumb') ?>
    <ul>
        <li><a href="<?= Backend::url('pensoft/restcoastmobileapp/threatmeasureimpactentries') ?>">Threat Measure Impact
                Entries</a></li>
        <li><?= e($this->pageTitle) ?></li>
    </ul>
<?php Block::endPut() ?>

<?php if ( ! $this->fatalError): ?>
    <div class="form-group  hint-field span-full    " data-field-name="_hint1"
         id="Form-field-ThreatMeasureImpactEntry-_hint1-group">
        <div class="callout fade in callout-info no-title ">
            <div class="header">
                <i class="icon-info"></i>
                <p></p>
                <p>Connect site threats and measures</p>
                <p></p>
            </div>
        </div>
    </div>
    <?= Form::open(['class' => 'layout']) ?>

    <div class="layout-row">
        <?= $this->formRender() ?>
    </div>

    <div class="form-buttons">
        <div class="loading-indicator-container">
            <button
                type="submit"
                data-request="onSave"
                data-hotkey="ctrl+s, cmd+s"
                data-load-indicator="<?= e(trans('backend::lang.form.saving')) ?>"
                class="btn btn-primary">
                <?= e(trans('backend::lang.form.create')) ?>
            </button>
            <button
                type="button"
                data-request="onSave"
                data-request-data="close:1"
                data-hotkey="ctrl+enter, cmd+enter"
                data-load-indicator="<?= e(trans('backend::lang.form.saving')) ?>"
                class="btn btn-default">
                <?= e(trans('backend::lang.form.create_and_close')) ?>
            </button>
            <span class="btn-text">
                    <?= e(trans('backend::lang.form.or')) ?> <a
                    href="<?= Backend::url('pensoft/restcoastmobileapp/threatmeasureimpactentries') ?>"><?= e(trans('backend::lang.form.cancel')) ?></a>
                </span>
        </div>
    </div>

    <?= Form::close() ?>

<?php else: ?>
    <p class="flash-message static error"><?= e(trans($this->fatalError)) ?></p>
    <p><a href="<?= Backend::url('pensoft/restcoastmobileapp/threatmeasureimpactentries') ?>"
          class="btn btn-default"><?= e(trans('backend::lang.form.return_to_list')) ?></a></p>
<?php endif;
