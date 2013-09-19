<form class="tu-answers" action="" method="POST">
  <div class="tu-orderable-answers">

    <div class="tu-form-row">
      <div class="tu-form-label"></div>
      <div class="tu-form-inputs">
        <ul class="tu-orderable-answer-list">
          <?php foreach ($orderable_answers as $i => $answer) { ?>
            <li data-name="<?php echo $answer; ?>">
              <span><?php echo $answer; ?></span>
              <input type="hidden" name="tu_answer[]" value="<?php echo $answer; ?>">
            </li>
          <?php } ?>
        </ul>
      </div>
    </div>

    <div class="tu-form-row">
      <div class="tu-form-label"></div>
      <div class="tu-form-inputs">
        <div class="tu-form-input tu-form-button">
          <button type="submit">
            <?php echo apply_filters('tu_form_button', __('Save my answer', 'trainup'), 'save_answer'); ?>
          </button>
        </div>
      </div>
    </div>
    
  </div>
</form>