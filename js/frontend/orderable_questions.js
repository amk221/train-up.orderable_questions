/* global jQuery, TU_QUESTIONS */

jQuery(function($) {
  'use strict';

  /**
   * Initialise orderable elements using the default config
   * or a custom config if one is provided.
   */
  $('.tu-orderable-answer-list').sortable(TU_QUESTIONS.orderableConfig || {});

});