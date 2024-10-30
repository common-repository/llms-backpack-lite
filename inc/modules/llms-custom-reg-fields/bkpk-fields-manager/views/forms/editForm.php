<?php
namespace BPKPFieldManager;

global $bkpkFM;
// Expected: $formName

$formBuilder = new FormBuilder('form_editor', $formName);
?>

<div class="wrap">
	<div id="bkpk_form_editor">
    
        <?php if( $formName && ! $formBuilder->isFound() ) : ?>
        <?= adminNotice(sprintf(__('Form "%s" is not found. You can create a new form.', $bkpkFM->name), $formName)); ?>
        <?php endif; ?>

        <div class="panel panel-default">
			<div class="panel-body">
				<div class="form-inline" role="form">
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon"><?= __('Form Name*', $bkpkFM->name) ?></div>
							<input type="text" class="form-control" name="form_key"
								value="<?= $formName ?>"
								placeholder="<?= __('Enter unique form name', $bkpkFM->name) ?>">
						</div>
					</div>

					<div class="form-group">
						<ul class="nav nav-pills bkpk_pills">
							<li class="nav active danger"><a href="#bkpk_form_fields_tab"
								data-toggle="tab"><?= __('Form Builder', $bkpkFM->name) ?></a></li>
							<li class="nav"><a href="#bkpk_form_settings_tab" data-toggle="tab"><?= __('Settings', $bkpkFM->name) ?></a></li>
						</ul>
					</div>

					<div class="form-group">
						<span class="bkpk_error_msg"></span>
					</div>

					<div class="form-group pull-right">
						<button type="button" class="btn btn-primary bkpk_save_button"><?= __('Save Changes', $bkpkFM->name) ?></button>
					</div>

				</div>
			</div>
		</div>


		<div class="tab-content">
			<div class="tab-pane fade in active" id="bkpk_form_fields_tab">
				<div class="col-xs-12 col-sm-8">
					<div id="bkpk_fields_container" class="metabox-holder">
                        <?php $formBuilder->displayFormFields();  ?>
                    </div>
				</div>

				<div id="bkpk_steady_sidebar_holder" class="col-xs-12 col-sm-4 ">
					<div id="bkpk_steady_sidebar">
						<div id="bkpk_fields_selectors" class="panel-group">
                            <?php $formBuilder->sharedFieldsSelectorPanel(); ?>
                            <?php $formBuilder->fieldsSelectorPanels(); ?>
                        </div>

						<p class="">
							<button style="float: right" type="button"
								class="bkpk_save_button btn btn-primary"><?= __('Save Changes', $bkpkFM->name) ?></button>
						</p>
						<p class="bkpk_clear"></p>
						<p class="bkpk_error_msg"></p>
					</div>
				</div>

			</div>

			<div class="tab-pane fade" id="bkpk_form_settings_tab">
				<div class="panel panel-default">
					<div class="panel-body">
                    <?php echo $formBuilder->displaySettings(); ?>
                  </div>
				</div>
			</div>
		</div>

		<div id="bkpk_additional_input" class="bkpk_hidden">
            <?php echo $bkpkFM->methodName( 'formEditor', true ); ?>
            <?php echo $formBuilder->maxFieldInput(); ?>
            <?php echo $formBuilder->additional(); ?>            
        </div>

	</div>
</div>