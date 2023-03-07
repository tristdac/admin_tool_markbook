define(['jquery', 'core/ajax', 'core/notification', 'core/modal_factory'], function($, ajax, notification, ModalFactory) {
	return {
		init: function($assessments, $course) {
			// var promises = ajax.call([{
   //              methodname: "tool_markbook_get_available_assessments",
   //              args: {},
   //              fail: notification.exception
   //          }]);
   //          promises[0].done(function(data) {
   //          	alert(data);
   //          }).fail(notification.exception);
			if ($('.path-mod').length > 0) {
				if ($("fieldset#id_modstandardgrade").length > 0) {
					var assType = $('#id_assessmenttype').find(":selected").attr('value');
					var assTypeStr = $('#id_assessmenttype').find(":selected").text();
					$('[data-groupname="gradingduedate"]').addClass('show');
					$('#id_availability .moreless-toggler').addClass('moreless-less').text('Show less...');
					$("select option[value='0']").prop('disabled',true);
					$('#id_assessmentname').parent().append( '<div class="rename"><a id="rename" style="margin:0;cursor:pointer;"><i class="fa fa-refresh"></i> Rename Activity</a></div>');
					if($(".field_assessmentname span[role='option']").attr('data-active-selection') == 'true' ) {
						$(".field_assessmentname").addClass('hidden');
					}
					if(assType == '1') {
						$(".field_assessmentname").addClass('hidden');
					}
					if(assType == '2') {
						$(".field_assessmentname").removeClass('hidden');
					}
					if(assType == '3') {
						$(".field_assessmentname").removeClass('hidden');
					}
					$('.form-autocomplete-selection').change(function() {
						setTimeout(rewrite, 500);
					});
					$('select#id_assessmenttype').change(function() {
						var selections = $('.form-autocomplete-selection').find('span[role="option"]').length;
						if($(this).val()=="1") {
							$(".field_assessmentname").addClass('hidden');
							reset_assessments();
							// $('span.tag.badge span[aria-hidden="true"]').trigger('click');
							enable_save_buttons();
						}
						if(($(this).val()=="2")) {
							$(".field_assessmentname").removeClass('hidden');
							$('#settitle').addClass('hidden');
							$('#appendtitle').addClass('hidden');
							$("select option[value='0']").prop('disabled',true);
							$('.field_assessmentname').addClass('highlight');
							reset_assessments();
							disable_save_buttons();
						}
						// if ($(this).val()=="3") {
						// 	reset_assessments();
						// }
						if(($(this).val()=="3")) {
							$(".field_assessmentname").removeClass('hidden');
							$('#settitle').addClass('hidden');
							$('#appendtitle').addClass('hidden');
							$("select option[value='0']").prop('disabled',true);
							$('.field_assessmentname').addClass('highlight');
							disable_save_buttons();
							reset_assessments();
						}
						
					});
					$('.field_assessmentname').on("click","li",function(){
						// checktext();
						var asstype = $('#id_assessmenttype').find(":selected").attr('value');
						var assid = $(this).attr('data-value');
						var assname = $(this).text();
						var milestones = [];
						var summatives = [];
						$('#asslist').find('.milestone-li').each(function(){ milestones.push(this.id); });
						$('#asslist').find('.summative-li').each(function(){ summatives.push(this.id); });
						if (($.inArray(assid, milestones) !== -1) && asstype == '2') {
							alert(assname + ' is already being used elsewhere in Moodle. Its fine for multiple acitivies to count towards the same Milestone, but is important that you are aware of all Milestone activities. See below for further details.');
						}
						if (($.inArray(assid, summatives) !== -1) && asstype == '2') {
							alert(assname + ' is already being used Summatively elsewhere in Moodle. You cannot assign this as a Milestone assessment. See below for further details.');
							$('#id_markbooksection span.tag.badge[data-value="'+assid+'"] span[aria-hidden="true"]').trigger('click');
						}
						if ((($.inArray(assid, milestones) !== -1) || ($.inArray(assid, summatives) !== -1)) && (asstype == '3')) {
							alert(assid + ' ' + assname + ' is already being used elsewhere in Moodle. You cannot use this summatively until it has been unassigned elsewhere. See below for further details.');
							// setTimeout($('#id_markbooksection .form-autocomplete-selection span[data-value="'+assid+'"] span[aria-hidden="true"]').trigger('click'), 5000);
							setTimeout(function () {
							   $('#id_markbooksection .form-autocomplete-selection span[data-value="'+assid+'"] span[aria-hidden="true"]').trigger('click');
							   // setTimeout(rewrite, 1000);
							}, 500);
						}
						var n = $('.field_assessmentname .form-autocomplete-suggestions li[aria-selected="true"]').length;
						if (n > 0) {  	
							$('.field_assessmentname').removeClass('highlight');
							$('.field_assessmentname #message').remove();
							enable_save_buttons();
						}
						setTimeout(rewrite, 500);
					});
					$('.field_assessmentname').on("click",".form-autocomplete-multiple span",function(){
						var n = $('.field_assessmentname .form-autocomplete-multiple span[aria-selected="true"]').length;
						if (n<=1) {
							$('.field_assessmentname').addClass('highlight');
							disable_save_buttons();
						}
					});
					if(assType == '0') {
						$('.field_assessmentname').addClass('hidden');
						$('.field_assessmenttype').addClass('highlight');
						$('.field_assessmenttype .fselect').append( "<span id='message' style='text-align:center;padding:15px;font-weight:strong;'>Please select Assessment Type</span>" );
						$('fieldset:nth-last-child(2)').removeClass('collapsed');
						disable_save_buttons();
					}
						$('#id_assessmenttype').on('change', function() {
							$('.field_assessmenttype').removeClass('highlight');
						$('.field_assessmenttype #message').remove();
					});
					function rewrite() {
						$('.field_assessmentname .form-autocomplete-multiple span[role="option"]').each(function() {
							var asscode = $(this).attr('data-value');
							// alert($(this).attr('class')+' found');
							// var date = $assessments[$(this).attr('data-value')]['dateset'];
							// alert('bawbag: '+$(this).attr("data-value")+' '+date);
							$( '<div class="rewrite redate"><a id="redate-'+asscode+'"><i class="fa fa-calendar"></i> Write Assessment Dates</a></div>').insertAfter($(this));
							$( '<div class="rewrite append"><a id="append-'+asscode+'"><i class="fa fa-plus"></i> Append Activity Name</a></div>').insertAfter($(this));
							$( '<div class="rewrite rename"><a id="rename-'+asscode+'"><i class="fa fa-refresh"></i> Rename Activity</a></div>').insertAfter($(this));
						});
					}
					
					// $('#rename').click(function() {
						$('.field_assessmentname .form-autocomplete-multiple').find(".badge").each(function() {
							$(this).after( '<div class="settitle"><a id="settitle" style="margin:0;cursor:pointer;"><i class="fa fa-refresh"></i> Set Title</a></div>');
							var title = $(this).text().substring(15);
							$('#id_name').attr('value', assTypeStr + " Assessment: " + title);
						});
					// });
					$('.field_assessmentname').on("click", ".rewrite.rename", function(){
					// $('.rename').live('click', function() {
						var asscode = $(this).find('a').attr('id').substr(7);
						var title = $('.form-autocomplete-suggestions').find("li[data-value='" + asscode + "']").text();
						var title = $assessments[asscode]['AssessmentTitle']+' - '+$assessments[asscode]['AssessmentCode'];
						var assTypeStr = $('#id_assessmenttype').find(":selected").text();
						$('#id_name').attr('value', assTypeStr + " Assessment: " + title);
						alert("Activity title has changed to '"+assTypeStr+" Assessment: "+title+"'");
					});
					$('.field_assessmentname').on("click", ".rewrite.append", function(){
						var asscode = $(this).find('a').attr('id').substr(7);
						var title = $('.form-autocomplete-suggestions').find("li[data-value='" + asscode + "']").text();
						var title = $assessments[asscode]['AssessmentTitle']+' - '+$assessments[asscode]['AssessmentCode'];
						$('#id_name').val($('#id_name').val() + ' (' + title + ')');
						alert("Activity title has been appended with '("+title+")'");
					});
					$('.field_assessmentname').on("click", ".rewrite.redate", function(){
						var asscode = $(this).find('a').attr('id').substr(7);
						var dday = $assessments[asscode]['setD'];
						var dmonth = $assessments[asscode]['setM'];
						var dyear = $assessments[asscode]['setY'];
						var dhour = $assessments[asscode]['setH'];
						var dminute = $assessments[asscode]['setI'];
						var cday = $assessments[asscode]['expD'];
						var cmonth = $assessments[asscode]['expM'];
						var cyear = $assessments[asscode]['expY'];
						var chour = $assessments[asscode]['expH'];
						var cminute = $assessments[asscode]['expI'];

						$('[data-groupname="duedate"]').find('[disabled="disabled"]').removeAttr('disabled');
						$('#id_duedate_enabled').attr('checked','checked');
						$('[data-groupname="gradingduedate"]').find('[disabled="disabled"]').removeAttr('disabled');
						$('#id_gradingduedate_enabled').attr('checked','checked');

						$('#id_duedate_day').find("option[selected]").removeAttr('selected');
						$('#id_duedate_day').find("option[value='" + dday + "']").attr('selected','selected');
						$('#id_duedate_month').find("option[selected]").removeAttr('selected');
						$('#id_duedate_month').find("option[value='" + dmonth + "']").attr('selected','selected');
						$('#id_duedate_year').find("option[selected]").removeAttr('selected');
						$('#id_duedate_year').find("option[value='" + dyear + "']").attr('selected','selected');
						$('#id_duedate_hour').find("option[selected]").removeAttr('selected');
						$('#id_duedate_hour').find("option[value='" + dhour + "']").attr('selected','selected');
						$('#id_duedate_minute').find("option[selected]").removeAttr('selected');
						$('#id_duedate_minute').find("option[value='" + dminute + "']").attr('selected','selected');

						$('#id_gradingduedate_day').find("option[selected]").removeAttr('selected');
						$('#id_gradingduedate_day').find("option[value='" + cday + "']").attr('selected','selected');
						$('#id_gradingduedate_month').find("option[selected]").removeAttr('selected');
						$('#id_gradingduedate_month').find("option[value='" + cmonth + "']").attr('selected','selected');
						$('#id_gradingduedate_year').find("option[selected]").removeAttr('selected');
						$('#id_gradingduedate_year').find("option[value='" + cyear + "']").attr('selected','selected');
						$('#id_gradingduedate_hour').find("option[selected]").removeAttr('selected');
						$('#id_gradingduedate_hour').find("option[value='" + chour + "']").attr('selected','selected');
						$('#id_gradingduedate_minute').find("option[selected]").removeAttr('selected');
						$('#id_gradingduedate_minute').find("option[value='" + cminute + "']").attr('selected','selected');
						
						$('#id_allowsubmissionsfromdate_day').find("option[selected]").removeAttr('selected');
						$('#id_allowsubmissionsfromdate_day').find("option[value='" + cday + "']").attr('selected','selected');
						$('#id_allowsubmissionsfromdate_month').find("option[selected]").removeAttr('selected');
						$('#id_allowsubmissionsfromdate_month').find("option[value='" + cmonth + "']").attr('selected','selected');
						$('#id_allowsubmissionsfromdate_year').find("option[selected]").removeAttr('selected');
						$('#id_allowsubmissionsfromdate_year').find("option[value='" + cyear + "']").attr('selected','selected');
						$('#id_allowsubmissionsfromdate_hour').find("option[selected]").removeAttr('selected');
						$('#id_allowsubmissionsfromdate_hour').find("option[value='" + chour + "']").attr('selected','selected');
						$('#id_allowsubmissionsfromdate_minute').find("option[selected]").removeAttr('selected');
						$('#id_allowsubmissionsfromdate_minute').find("option[value='" + cminute + "']").attr('selected','selected');

						window.location.hash = '#id_availability';
						$(function() {
							var offset = $('.site-navbar').height();
							var scrollTime = 500;
							$("html, body").animate({
					            scrollTop: $('#id_availability').offset().top - offset 
					        }, scrollTime);
					        return false;
				   		 });
						alert("'Due date' and 'grade by' date applied from Markbook");
					});
					$(document).ready(function() {
						setTimeout(rewrite, 2000);
					});
					function disable_save_buttons() {
						$('#id_submitbutton').prop('disabled', true);
						$('#id_submitbutton2').prop('disabled', true);
					}
					function enable_save_buttons() {
						$('#id_submitbutton').prop('disabled', false);
						$('#id_submitbutton2').prop('disabled', false);
					}
					// function checktext() {
					//     $('.form-autocomplete-suggestions').text().replace("No Suggestions", "All available assessments are already being used as Milestones... see list below"); 
					// };​​​​​
					function reset_assessments() {
						$('#id_markbooksection .form-autocomplete-selection span.tag.badge span[aria-hidden="true"]').trigger('click');
					};
					$('body').on('click', 'input[id*="id_submitbutton"][disabled=""]', function(){
						alert('disabled alert');
						$('#id_markbooksection').effect("highlight", {color: "#f4ab45"}, 3000);
					});
				} else {
					$('#id_markbooksection').remove();
				}

			}
			// if ($('.path-course-view').length > 0) {
				// var fullname = $course[fullname];
				// $('#section-0 .content').prepend('<div class="gantt_wrapper"><div class="header"><h2>Assessment Schedule</h2></div><div class="gantt"><div class="gantt__row gantt__row--months"><div class="gantt__row-first"></div> <span>Jan</span><span>Feb</span><span>Mar</span> <span>Apr</span><span>May</span><span>Jun</span> <span>Jul</span><span>Aug</span><span>Sep</span> <span>Oct</span><span>Nov</span><span>Dec</span></div><div class="gantt__row gantt__row--lines" data-month="5"> <span></span><span></span><span></span> <span></span><span></span><span></span> <span></span><span class="marker"></span><span></span> <span></span><span></span><span></span></div><div class="gantt__row"><div class="gantt__row-first"> Barnard Posselt</div><ul class="gantt__row-bars"><li style="grid-column: 4/11; background-color: #2ecaac;">Even longer project</li></ul></div><div class="gantt__row gantt__row--empty"><div class="gantt__row-first"> Ryley Huggons</div><ul class="gantt__row-bars"></ul></div><div class="gantt__row"><div class="gantt__row-first"> Lanie Erwin</div><ul class="gantt__row-bars"><li style="grid-column: 2/5; background-color: #2ecaac;">Start Februar 🙌</li><li style="grid-column: 1/6; background-color: #ff6252;" class="stripes"></li><li style="grid-column: 7/11; background-color: #54c6f9;">Same line</li></ul></div><div class="gantt__row gantt__row--empty"><div class="gantt__row-first"> Krishnah Pauleit</div><ul class="gantt__row-bars"></ul></div><div class="gantt__row gantt__row--empty"><div class="gantt__row-first"> Hobard Lampitt</div><ul class="gantt__row-bars"></ul></div><div class="gantt__row"><div class="gantt__row-first"> Virgilio Jeanes</div><ul class="gantt__row-bars"><li style="grid-column: 2/5; background-color: #2ecaac;"></li></ul></div><div class="gantt__row"><div class="gantt__row-first"> Ky Verick</div><ul class="gantt__row-bars"><li style="grid-column: 3/8; background-color: #54c6f9;">Long project</li></ul></div><div class="gantt__row"><div class="gantt__row-first"> Ketti Waterworth</div><ul class="gantt__row-bars"><li style="grid-column: 4/9; background-color: #ff6252;" class="stripes">A title</li></ul></div></div>');
			// }
		}
	};
});