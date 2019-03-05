<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}
?>
<a href="javascript:void(0)"data-toggle="modal" data-target="#fpusa_product_feedback"><i class="far fa-flag pr-1"></i> Report incorrect product information.</a>
<div class="modal fade" id="fpusa_product_feedback" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="exampleModalLabel">Report an issue</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div id="report-problem-form" class="modal-body">
        <div class="form-group">
          <label>Please tell us about an issue.</label>
          <select id="report_where" class="form-control report-problem-option mb-2" name="report_where">
            <option value="0">Which part of the page?</option>
            <option value="images">Images</option>
            <option value="name">Product Name</option>
            <option value="bullet_points">Bullet Points</option>
            <option value="other">Other Product Details</option>
          </select>
          <select id="report_issue" class="form-control report-problem-option" name="report_issue" disabled>
            <option value="0">What is the issue?</option>
          </select>
        </div>

        <div class="form-group">
          <label>Comments (optional)</label>
          <textarea id="report-comments" name="comments"></textarea>
        </div>
        <small class="text-muted d-flex align-items-center">
          <i class="fas fa-exclamation pr-3"></i>
          <span>
            Please do not enter personal information. For questions about an order, go to <a href="/my-account">Your Account</a>.
          </span>
        </small>
    </div>
    <div class="modal-footer">
      <button id="report-submit" type="button" class="btn btn-primary" disabled>Submit</button>
      <button id="report-done" type="button" class="btn btn-primary" data-dismiss="modal" style="display: none;">Done</button>
    </div>
  </div>
</div>
</div>
