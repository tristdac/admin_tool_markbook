define(['jquery'], function($) {
  return {
    init: function($course, $assessments) {
    	$('.card-body .mr-2').append('<a href="/admin/tool/markbook/index.php?cid='+$course['id']+'" id="markbook" title="Promonitor Markbook" style="margin-left: 10px;" class="btn btn-secondary"><i class="fa fa-clipboard"></i> My Assessments</a>');
    }
  };
});
