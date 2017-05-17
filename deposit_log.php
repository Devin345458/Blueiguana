<?
require("global_header.php");
?>
      <link rel="stylesheet" type="text/css" href="css/deposit_log.css">
      <script>


function format_money(num3) {
  var numb = parseFloat(Math.round(num3 * 100) / 100).toFixed(0);
  numb = numb.toString();
  return "$"+numb;
}

function get_val(el_id) {
  var v = $(el_id).val();
  if (v) {
    return parseFloat(v);
  }
  else {
    return 0;
  }
}

function get_htmlval(el_id) {
  var v = $(el_id).html();
  return Number(v.replace(/[^0-9\.]+/g,""));
}

function get_attrval(el_id) {
  var v = $(el_id).attr('data-amount');
  return Number(v.replace(/[^0-9\.]+/g,""));
}

function gather_data() {
  var input_data = {};
    jQuery('input').each(function(){
      if (jQuery(this).attr('id')) {
        input_data[jQuery(this).attr('id')] = jQuery(this).val();
      }
    });
    return input_data;
}

function get_remote_data() {

  var post_data = {};
  post_data['year'] = $('#dl_year').val();
  post_data['month'] = $('#dl_month').val();
  $.ajax({
      type: 'POST',
      url: 'api.php?action=get_deposit_log',
      data: post_data,
      dataType: 'json',
      success: function (existing_data) {
        jQuery("#deposit_log_table").find('input').val('');
        for (var key in existing_data) {
          jQuery("#"+key).val(existing_data[key]);
        }
        update_calculations();
        console.log("get_deposit_log", existing_data);
      },
      error: function(e, type, message) {
        console.log("ERROR", e, type, message);
      }
  });
}

window.onbeforeunload = function(e) {
    if (save_data_dirty_bit || save_data_in_progress || unsaved_data) {
        return "There is still some data saving in the background that may be lost - Please stay on the page for a few more seconds, then you can leave.";
    }
};

var save_data_setimeout;
var save_data_in_progress=0;
var save_data_dirty_bit=0;
var unsaved_data=0;
function save_data() {
  save_data_in_progress = 1;
  save_data_dirty_bit = 0;
  var input_data = gather_data();

  var post_data = {};
  post_data['year'] = $('#dl_year').val();
  post_data['month'] = $('#dl_month').val();
  post_data['data'] = JSON.stringify(input_data);
  $.ajax({
      type: 'POST',
      url: 'api.php?action=save_deposit_log',
      data: post_data,
      dataType: 'json',
      success: function (data) {
        console.log("sent", data);
          save_data_in_progress = 0;
          unsaved_data = 0;
        if (save_data_dirty_bit) {
            save_data();
        }
      },
      error: function(e, type, message) {
        console.log("ERROR", e, type, message);
          save_data_in_progress = 0;
          unsaved_data = 0;
        if (save_data_dirty_bit) {
            save_data();
        }
      }
  });
}

function update_calculations(save_me) {

  var overall = 0;
  for(var i=1; i<=31; i++) {
    if ($("#short_long_"+i).length) {
      overall += get_val("#short_long_"+i);
    }
  }

  var total_str = "";
  if (overall < 0) {
    total_str = "<span style='color:red;font-weight:bold;'>Short "+ format_money(overall*-1)+"</span>";
  }
  else if (overall > 0) {
    total_str = "<span style='color:blue;font-weight:bold;'>Long "+ format_money(overall)+"</span>";
  }
  else {
    total_str = "<span style='color:blue;font-weight:bold;'>Even</span>";
  }
  $('#total_str').html(total_str);

  if (save_me) {
    unsaved_data = 1;
    clearTimeout(save_data_setimeout);

    if (save_data_in_progress) {
      save_data_dirty_bit=1;
    }
    else {
      save_data_setimeout = setTimeout(function(){
        save_data();
      }, 1000);
    }
  }
}

  $( function() {
    $( "#mp_date" ).datepicker();

    $("#deposit_log_table").find("input").keyup(function () {
     update_calculations(true);
    });

<?
    if($_GET['month']) {
?>
    var the_date = new Date();
    $( "#dl_month" ).val(<?=$_GET['month']?>);
    $( "#dl_year" ).val(<?=$_GET['year']?>);
<?
    }
    else {
?>
    var the_date = new Date();
    $( "#dl_month" ).val(the_date.getMonth()+1);
    $( "#dl_year" ).val(the_date.getFullYear());
<?
    }
?>

    get_remote_data();

  } );

  function load_page() {
    if ($('#dl_month').val() && $('#dl_year').val()) {
      window.location.href="?month="+$('#dl_month').val()+"&year="+$('#dl_year').val();
    }
  }

      </script>

      <div class="jumbotron" style="background-color: rgb(243, 243, 243) !important; padding-top: 10px !important; padding-right: 15px !important; padding-left: 15px !important;">



      <table style="width: 100%;">
        <tr>
          <td style="text-align: left;vertical-align: top;width:25%;">
            <h3>Blue Iguana Car Wash</h3>
            <h2><?=$_SESSION['location_name']?> <?=$_SESSION['location']?></h2>
          </td>
          <td style="text-align: center;vertical-align: top;width:50%;">
            <h1>&nbsp;</h1>
          </td>
          <td style="text-align: right;vertical-align: top;width:25%;">
            <div class="input-group">
              <span class="input-group-addon" id="date-auto-sentry">Month</span>
              <select class="form-control" onchange="load_page()" id='dl_month'>
                <option value='1'>January</option>
                <option value='2'>February</option>
                <option value='3'>March</option>
                <option value='4'>April</option>
                <option value='5'>May</option>
                <option value='6'>June</option>
                <option value='7'>July</option>
                <option value='8'>August</option>
                <option value='9'>September</option>
                <option value='10'>October</option>
                <option value='11'>November</option>
                <option value='12'>December</option>
              </select>
            </div>
<br>
            <div class="input-group">
              <span class="input-group-addon" id="opener-auto-sentry">Year</span>
              <input class="form-control" aria-describedby="opener-auto-sentry" id='dl_year' onblur="load_page()">


            </div>
          </td>
        </tr>
      </table>


      <table border="1" id='deposit_log_table' class='ExcelTable2003' width="100%">



        <tr>
          <td colspan=8 class='xcl_header_top'>Deposit Slip Log</td>
        </tr>

        <tr>
          <td>Date</td>
          <td class='green_bg'>Vending Deposit</td>
          <td>Amount of Deposit</td>
          <td>Bag Number</td>
          <td>Name</td>
          <td>Short / Long</td>
          <td>Reason</td>
          <td>Deposited On</td>
        </tr>



<?
if($_GET['month']) {
  $month = $_GET['month'];
  $year = $_GET['year'];
}
else {
  $month = date('m');
  $year = date('Y');
}

$total_days = date('t', mktime(0, 0, 0, $month, 1, $year));

for ($date = 1; $date <= $total_days; $date++) {
?>
        <tr>
          <td><?=$month?>/<?=$date?>/<?=$year?></td>
          <td class='green_bg'><input class="form-control" id='vending_deposit_<?=$date?>'></td>
          <td><input class="form-control" id='amount_of_deposit_<?=$date?>'></td>
          <td><input class="form-control" id='bag_number_<?=$date?>'></td>
          <td><input class="form-control" id='name_<?=$date?>'></td>
          <td><input class="form-control" id='short_long_<?=$date?>'></td>
          <td><input class="form-control" id='reason_<?=$date?>'></td>
          <td><input class="form-control" id='deposited_on_<?=$date?>'></td>
        </tr>
<?
}
?>




        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td id='total_str'></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>


      </table>



      </div>

    </div>
<?
require("global_footer.php");
?>
