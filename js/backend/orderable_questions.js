/* global jQuery, TU_QUESTIONS */

jQuery(function($) {
  'use strict';

  /**
   * The metabox for the Orderable Question Type.
   */
  var $orderableBox = $('#orderable');

  /**
   * The element that contains the rows that contain the orderable answers.
   */
  var $orderableRows = $orderableBox.find('tbody');

  /**
   * - Callback fired when the orderable answers are re-ordered
   * - Use the TU_QUESTIONS API function to re-index the rows
   */
  var reIndex = function() {
    TU_QUESTIONS.reIndexMultipleAnswers($orderableBox);
  };

  /**
   * Initialise orderable table rows
   */
  $orderableRows.sortable({
    axis: 'y',
    handle: 'td.tu-index',
    update: reIndex
  });

});