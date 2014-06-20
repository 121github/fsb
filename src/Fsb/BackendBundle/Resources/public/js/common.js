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
