<?
require("global_header.php");
$evening_pull="active";
require("middle_header.php");
?>
      <link rel="stylesheet" type="text/css" href="css/evening_pull.css">
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
    input_data['comments_notes'] = jQuery('#comments_notes').val();
    return input_data;
}

function get_remote_data() {
  var the_date = new Date($('#mp_date').val());

  var post_data = {};
  post_data['year'] = the_date.getFullYear();
  post_data['month'] = the_date.getMonth()+1;
  post_data['day'] = the_date.getDate();
  $.ajax({
      type: 'POST',
      url: 'api.php?action=get_evening_pull',
      data: post_data,
      dataType: 'json',
      success: function (existing_data) {
        jQuery("#evening_pull_table").find('input').val('');
        for (var key in existing_data) {
          jQuery("#"+key).val(existing_data[key]);
        }
        jQuery('#comments_notes').val(existing_data['comments_notes']);
        update_calculations();
        console.log("get_evening_pull", existing_data);
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
  var the_date = new Date($('#mp_date').val());

  var post_data = {};
  post_data['year'] = the_date.getFullYear();
  post_data['month'] = the_date.getMonth()+1;
  post_data['day'] = the_date.getDate();
  post_data['data'] = JSON.stringify(input_data);
  $.ajax({
      type: 'POST',
      url: 'api.php?action=save_evening_pull',
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

  //Vault
  var vault_total = get_val("#vault_bag_1") + get_val("#vault_bag_5")*5 + get_val("#vault_bag_10")*10 + get_val("#vault_bag_20")*20 + get_val("#vault_bag_50")*50 + get_val("#vault_bag_100")*100;
  var vault_results = vault_total - 300;
  if (vault_results < 0) {
    vault_results = "<span style='color:red;font-weight:bold;'>Short "+ format_money(vault_results*-1)+"</span>";
  }
  else if (vault_results > 0) {
    vault_results = "<span style='color:blue;font-weight:bold;'>Long "+ format_money(vault_results)+"</span>";
  }
  else {
    vault_results = "<span style='color:blue;font-weight:bold;'>Even</span>";
  }
  $('#vault_bag_results').html(vault_results);

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

    $("#evening_pull_table").find("input").keyup(function () {
     update_calculations(true);
    });
    $("#mp_name").keyup(function () {
     update_calculations(true);
    });
    $("#comments_notes").keyup(function () {
     update_calculations(true);
    });

<?
    if($_GET['date']) {
?>
    $( "#mp_date" ).val("<?=$_GET['date']?>");
<?
    }
    else {
?>
    var the_date = new Date();
    var d_string = (the_date.getMonth()+1).toString()+"/"+the_date.getDate().toString()+"/"+the_date.getFullYear().toString();
    $( "#mp_date" ).val(d_string);
<?
    }
?>


    get_remote_data();

  } );

  function load_page() {
    if ($('#mp_date').val()) {
      window.location.href="?date="+$('#mp_date').val();
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
              <span class="input-group-addon" id="date-auto-sentry">Date</span>
              <input class="form-control" aria-describedby="date-auto-sentry" id='mp_date' onchange="load_page()">
            </div>
<br>
            <div class="input-group">
              <span class="input-group-addon" id="opener-auto-sentry">Name</span>
              <input class="form-control" aria-describedby="opener-auto-sentry" id='mp_name'>
            </div>
          </td>
        </tr>
      </table>


      <table border="1" id='evening_pull_table' class='ExcelTable2003' width="100%">


        <tr>
          <td>&nbsp;</td>
          <td colspan=5 class='xcl_header_top' style="text-align: center;">Auto Sentry</td>
          <td>&nbsp;</td>
          <td class='xcl_header' style="text-align: center;">Vault Bag</td>
        </tr>

        <tr>
          <td>&nbsp;</td>
          <td style="text-align: center;" class='xcl_header_bottom'>#1</td>
          <td>&nbsp;</td>
          <td style="text-align: center;" class='xcl_header_bottom'>#2</td>
          <td>&nbsp;</td>
          <td style="text-align: center;" class='xcl_header_bottom'>#3</td>
          <td>&nbsp;</td>
          <td style="text-align:center;" id='vault_bag_results'></td>
        </tr>

        <tr>
          <td style="font-weight: bold; text-align: center;">Bill Accepters:</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>

        <tr>
          <td style="text-align: center;">$1</td>
          <td class="blue_bg"><input class="form-control" id='as1_pull1_1'></td>
          <td>&nbsp;</td>
          <td class="blue_bg"><input class="form-control" id='as2_pull1_1'></td>
          <td>&nbsp;</td>
          <td class="blue_bg"><input class="form-control" id='as3_pull1_1'></td>
          <td>&nbsp;</td>
          <td class="blue_bg"><input class="form-control" id='vault_bag_1'></td>
        </tr>

        <tr>
          <td style="text-align: center;">$5</td>
          <td class="blue_bg"><input class="form-control" id='as1_pull1_5'></td>
          <td>&nbsp;</td>
          <td class="blue_bg"><input class="form-control" id='as2_pull1_5'></td>
          <td>&nbsp;</td>
          <td class="blue_bg"><input class="form-control" id='as3_pull1_5'></td>
          <td>&nbsp;</td>
          <td class="blue_bg"><input class="form-control" id='vault_bag_5'></td>
        </tr>


        <tr>
          <td style="text-align: center;">$10</td>
          <td class="blue_bg"><input class="form-control" id='as1_pull1_10'></td>
          <td>&nbsp;</td>
          <td class="blue_bg"><input class="form-control" id='as2_pull1_10'></td>
          <td>&nbsp;</td>
          <td class="blue_bg"><input class="form-control" id='as3_pull1_10'></td>
          <td>&nbsp;</td>
          <td class="blue_bg"><input class="form-control" id='vault_bag_10'></td>
        </tr>


        <tr>
          <td style="text-align: center;">$20</td>
          <td class="blue_bg"><input class="form-control" id='as1_pull1_20'></td>
          <td>&nbsp;</td>
          <td class="blue_bg"><input class="form-control" id='as2_pull1_20'></td>
          <td>&nbsp;</td>
          <td class="blue_bg"><input class="form-control" id='as3_pull1_20'></td>
          <td>&nbsp;</td>
          <td class="blue_bg"><input class="form-control" id='vault_bag_20'></td>
        </tr>


        <tr>
          <td style="text-align: center;">$50</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td class="blue_bg"><input class="form-control" id='vault_bag_50'></td>
        </tr>


        <tr>
          <td style="text-align: center;">$100</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td class="blue_bg"><input class="form-control" id='vault_bag_100'></td>
        </tr>

        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>

        <tr>
          <td style="border-top: 2px solid black !important;border-right: 1px solid black !important;vertical-align: top;" class='light_blue_bg'>
            <b>Comments / Notes:</b>
          </td>

          <td colspan=7 width="87.5%" style="border-top: 2px solid black !important;" class='light_blue_bg'>
            <textarea rows=8 class="form-control" id='comments_notes'></textarea>
          </td>

        </tr>


      </table>



      </div>

    </div>
<?
require("global_footer.php");
?>
