function checkSubmit(submit_id) {
	document.getElementById(submit_id).value = "Submitting...";
	document.getElementById(submit_id).disabled = true;
	return true;
}

$(document).ready(function() {    
	$("#message").click(function () {
        $("#message").fadeOut("slow");
    });
    
    setTimeout(function(){
    	$("#message").fadeOut("slow");
    },3000)

});

/********************************************************************************************************/
/******************** Datetime picker for new appointment ***********************************************/
/********************************************************************************************************/
$(function(){
	//StartDate
    $("#fsb_appointmentbundle_appointment_startDate input").each(function(){
        $(this).attr("readonly","readonly");
    });
    $('#fsb_appointmentbundle_appointment_startDate_date').datetimepicker({
        format: "Y-m-d",
        timepicker: false,
        datepicker: true,
        closeOnDateSelect:true,
        dayOfWeekStart: 1,
        onGenerate:function( ct ){
            jQuery(this).find('.xdsoft_date.xdsoft_weekend')
            .addClass('xdsoft_disabled');
        },
        
    });
    $('#fsb_appointmentbundle_appointment_startDate_time').datetimepicker({
        format: "H:i",
        timepicker: true,
        datepicker: false,
        step:30,
        minTime:'08:00',
        maxTime:'21:00',
    });
    
  //EndDate
    $("#fsb_appointmentbundle_appointment_endDate input").each(function(){
        $(this).attr("readonly","readonly");
    });
    $('#fsb_appointmentbundle_appointment_endDate_date').datetimepicker({
    	format: "Y-m-d",
        timepicker: false,
        datepicker: true,
        closeOnDateSelect:true,
        dayOfWeekStart: 1,
        onGenerate:function( ct ){
            jQuery(this).find('.xdsoft_date.xdsoft_weekend')
            .addClass('xdsoft_disabled');
        },
    });
    $('#fsb_appointmentbundle_appointment_endDate_time').datetimepicker({
        format: "H:i",
        timepicker: true,
        datepicker: false,
        step:30,
        minTime:'08:00',
        maxTime:'21:00',
    });
});

/********************************************************************************************************/
/******************** Datetime picker for edit appointment ***********************************************/
/********************************************************************************************************/
$(function(){
	//StartDate
    $("#fsb_appointmentbundle_appointmentedit_startDate input").each(function(){
        $(this).attr("readonly","readonly");
    });
    $('#fsb_appointmentbundle_appointmentedit_startDate_date').datetimepicker({
    	format: "Y-m-d",
        timepicker: false,
        datepicker: true,
        closeOnDateSelect:true,
        dayOfWeekStart: 1,
        onGenerate:function( ct ){
            jQuery(this).find('.xdsoft_date.xdsoft_weekend')
            .addClass('xdsoft_disabled');
        },
    });
    $('#fsb_appointmentbundle_appointmentedit_startDate_time').datetimepicker({
        format: "H:i",
        timepicker: true,
        datepicker: false,
        step:30,
        minTime:'08:00',
        maxTime:'21:00',
    });
    
  //EndDate
    $("#fsb_appointmentbundle_appointmentedit_endDate input").each(function(){
        $(this).attr("readonly","readonly");
    });
    $('#fsb_appointmentbundle_appointmentedit_endDate_date').datetimepicker({
    	format: "Y-m-d",
        timepicker: false,
        datepicker: true,
        closeOnDateSelect:true,
        dayOfWeekStart: 1,
        onGenerate:function( ct ){
            jQuery(this).find('.xdsoft_date.xdsoft_weekend')
            .addClass('xdsoft_disabled');
        },
    });
    $('#fsb_appointmentbundle_appointmentedit_endDate_time').datetimepicker({
        format: "H:i",
        timepicker: true,
        datepicker: false,
        step:30,
        minTime:'08:00',
        maxTime:'21:00',
    });
});

///********************************************************************************************************/
///******************** Datetime picker for new unavailable date ******************************************/
///********************************************************************************************************/
$(function(){
	//UnavaliableDate
    $("#fsb_rulebundle_unavailabledate_unavailableDate").each(function(){
        $(this).attr("readonly","readonly");
    });
    $('#fsb_rulebundle_unavailabledate_unavailableDate').datetimepicker({
        format: "Y-m-d",
        timepicker: false,
        datepicker: true,
        closeOnDateSelect:true,
        dayOfWeekStart: 1,
        onGenerate:function( ct ){
            jQuery(this).find('.xdsoft_date.xdsoft_weekend')
            .addClass('xdsoft_disabled');
        },
        
    });
    
    //StartTime
    $("#fsb_rulebundle_unavailabledate_startTime").each(function(){
        $(this).attr("readonly","readonly");
    });
    $('#fsb_rulebundle_unavailabledate_startTime').datetimepicker({
        format: "H:i",
        timepicker: true,
        datepicker: false,
        step:30,
        minTime:'08:00',
        maxTime:'21:00',
    });
  
    //EndTime
    $("#fsb_rulebundle_unavailabledate_endTime").each(function(){
        $(this).attr("readonly","readonly");
    });
    $('#fsb_rulebundle_unavailabledate_endTime').datetimepicker({
        format: "H:i",
        timepicker: true,
        datepicker: false,
        step:30,
        minTime:'08:00',
        maxTime:'21:00',
    });
});


/********************************************************************************************************/
/******************** Checkbox all the day to show or hidden the times of a unavailable date ************/
/********************************************************************************************************/
$(function(){
	$('#fsb_rulebundle_unavailabledate_allDay').change(function(){
	  if($(this).prop("checked")) {
		$('#fsb_rulebundle_unavailabledate_startTime').hide();
	    $('#fsb_rulebundle_unavailabledate_endTime').hide();
	  } else {
		$('#fsb_rulebundle_unavailabledate_startTime').show();
	    $('#fsb_rulebundle_unavailabledate_endTime').show();
	  }
	});
});


///********************************************************************************************************/
///******************** Datetime picker for search availability filter ******************************************/
///********************************************************************************************************/
$(function(){
  
  //StartTime
  $("#fsb_rulebundle_availabilityfilter_startTime").each(function(){
      $(this).attr("readonly","readonly");
  });
  $('#fsb_rulebundle_availabilityfilter_startTime').datetimepicker({
      format: "H:i",
      timepicker: true,
      datepicker: false,
      step:30,
      minTime:'08:00',
      maxTime:'21:00',
  });

  //EndTime
  $("#fsb_rulebundle_availabilityfilter_endTime").each(function(){
      $(this).attr("readonly","readonly");
  });
  $('#fsb_rulebundle_availabilityfilter_endTime').datetimepicker({
      format: "H:i",
      timepicker: true,
      datepicker: false,
      step:30,
      minTime:'08:00',
      maxTime:'21:00',
  });
});


/********************************************************************************************************/
/******************** Datetime picker for appointment outcome filter by Recruiter ***********************************************/
/********************************************************************************************************/
$(function(){
	//StartDate
    $("#fsb_reportingbyrecruiterbundle_filter_startDate_date input").each(function(){
        $(this).attr("readonly","readonly");
    });
    $('#fsb_reportingbyrecruiterbundle_filter_startDate_date').datetimepicker({
    	format: "Y-m-d",
        timepicker: false,
        datepicker: true,
        closeOnDateSelect:true,
        dayOfWeekStart: 1,
        onGenerate:function( ct ){
            jQuery(this).find('.xdsoft_date.xdsoft_weekend')
            .addClass('xdsoft_disabled');
        },
    });
    $('#fsb_reportingbyrecruiterbundle_filter_startDate_time').datetimepicker({
        format: "H:i",
        timepicker: true,
        datepicker: false,
        step:30,
        minTime:'08:00',
        maxTime:'21:00',
    });
    
  //EndDate
    $("#fsb_reportingbyrecruiterbundle_filter_endDate_date input").each(function(){
        $(this).attr("readonly","readonly");
    });
    $('#fsb_reportingbyrecruiterbundle_filter_endDate_date').datetimepicker({
    	format: "Y-m-d",
        timepicker: false,
        datepicker: true,
        closeOnDateSelect:true,
        dayOfWeekStart: 1,
        onGenerate:function( ct ){
            jQuery(this).find('.xdsoft_date.xdsoft_weekend')
            .addClass('xdsoft_disabled');
        },
    });
    $('#fsb_reportingbyrecruiterbundle_filter_endDate_time').datetimepicker({
        format: "H:i",
        timepicker: true,
        datepicker: false,
        step:30,
        minTime:'08:00',
        maxTime:'21:00',
    });
});